<?php

include_once __DIR__ .'/BaseTestCase.php';

// Stands in for WP's taxonomy registry, filled per test.
if ( !function_exists( 'get_taxonomies' ) ) {
	function get_taxonomies( ...$args ) {
		$GLOBALS['tx_test_taxonomy_args'] = $args;
		return $GLOBALS['tx_test_taxonomies'] ?? array();
	}
}

class CustomTaxonomyRewriteRulesTest extends BaseTestCase
{

	protected function setUp(): void
	{
		include_once './includes/common/plugin-debug.php';
		include_once './includes/lib/transifex-live-integration-subdirectory.php';
	}

	protected function tearDown(): void
	{
		unset( $GLOBALS['tx_test_taxonomies'], $GLOBALS['tx_test_taxonomy_args'], $GLOBALS['wp_rewrite'] );
	}

	private function taxonomy( $name, $rewrite, $query_var )
	{
		$taxonomy = new stdClass();
		$taxonomy->name = $name;
		$taxonomy->rewrite = $rewrite;
		$taxonomy->query_var = $query_var;
		return $taxonomy;
	}

	private function rulesFor( $taxonomies, $permastructs )
	{
		$GLOBALS['tx_test_taxonomies'] = $taxonomies;
		$GLOBALS['wp_rewrite'] = new class( $permastructs ) {
			private $permastructs;
			function __construct( $permastructs ) {
				$this->permastructs = $permastructs;
			}
			function get_extra_permastruct( $name ) {
				return $this->permastructs[$name] ?? false;
			}
		};
		$subdirectory = \Codeception\Stub::make( Transifex_Live_Integration_Subdirectory::class, [], $this );
		return $subdirectory->custom_taxonomy_rewrite_rules_hook();
	}

	public function testTermArchivesGetLocalizedRules()
	{
		$rules = $this->rulesFor(
			[ $this->taxonomy( 'resource-category', [ 'slug' => 'resource-category', 'hierarchical' => false ], 'resource-category' ) ],
			[ 'resource-category' => '/resource-category/%resource-category%' ]
		);
		$this->assertSame( [
			'%lang%/resource-category/([^/]+)/page/?([0-9]{1,})/?$' => 'index.php?resource-category=$matches[2]&paged=$matches[3]&lang=$matches[1]',
			'%lang%/resource-category/([^/]+)/?$' => 'index.php?resource-category=$matches[2]&lang=$matches[1]',
		], $rules );
	}

	public function testPubliclyQueryableTaxonomiesAreIncluded()
	{
		// WP serves term archives of taxonomies that are publicly queryable
		// but not public, so they need localized rules too
		$this->rulesFor( [], [] );
		$this->assertSame( [ [ '_builtin' => false, 'publicly_queryable' => true ], 'objects' ], $GLOBALS['tx_test_taxonomy_args'] );
	}

	public function testHierarchicalTermPathsAndFrontBase()
	{
		$rules = $this->rulesFor(
			[ $this->taxonomy( 'topic', [ 'slug' => 'topics', 'hierarchical' => true ], 'topic' ) ],
			[ 'topic' => '/blog/topics/%topic%' ]
		);
		$this->assertArrayHasKey( '%lang%/blog/topics/(.+?)/page/?([0-9]{1,})/?$', $rules );
		$this->assertSame( 'index.php?topic=$matches[2]&lang=$matches[1]', $rules['%lang%/blog/topics/(.+?)/?$'] );
		// the paged rule has to be matched before the greedy term path
		$this->assertSame( 0, array_search( '%lang%/blog/topics/(.+?)/page/?([0-9]{1,})/?$', array_keys( $rules ) ) );
	}

	public function testTaxonomyWithoutQueryVarIsQueriedByName()
	{
		$rules = $this->rulesFor(
			[ $this->taxonomy( 'grade', [ 'slug' => 'grade' ], false ) ],
			[ 'grade' => '/grade/%grade%' ]
		);
		$this->assertSame( 'index.php?taxonomy=grade&term=$matches[2]&lang=$matches[1]', $rules['%lang%/grade/([^/]+)/?$'] );
	}

	public function testTaxonomiesWithoutPrettyUrlsAreSkipped()
	{
		$rules = $this->rulesFor(
			[
				// registered with rewrite => false
				$this->taxonomy( 'internal', false, 'internal' ),
				// plain permalinks leave no permastruct to build from
				$this->taxonomy( 'plain', [ 'slug' => 'plain' ], 'plain' ),
			],
			[ 'internal' => '/internal/%internal%' ]
		);
		$this->assertSame( [], $rules );
	}

}
