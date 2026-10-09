<?php

include_once __DIR__ .'/BaseTestCase.php';

// Stand in for WP's post type registry, filled per test.
if ( !function_exists( 'get_post_types' ) ) {
	function get_post_types( ...$args ) {
		return array_keys( $GLOBALS['tx_test_post_types'] ?? array() );
	}
}
if ( !function_exists( 'get_post_type_object' ) ) {
	function get_post_type_object( $post_type ) {
		return $GLOBALS['tx_test_post_types'][$post_type] ?? null;
	}
}

class PostTypeArchiveRewriteRulesTest extends BaseTestCase
{

	protected function setUp(): void
	{
		include_once './includes/common/plugin-debug.php';
		include_once './includes/common/transifex-live-integration-validators.php';
		include_once './includes/lib/transifex-live-integration-subdirectory.php';
	}

	protected function tearDown(): void
	{
		unset( $GLOBALS['tx_test_post_types'] );
	}

	private function postType( $has_archive, $rewrite = false )
	{
		$post_type = new stdClass();
		$post_type->has_archive = $has_archive;
		$post_type->rewrite = $rewrite;
		return $post_type;
	}

	private function subdirectory( $post_types )
	{
		$GLOBALS['tx_test_post_types'] = $post_types;
		return \Codeception\Stub::make( Transifex_Live_Integration_Subdirectory::class, [ 'languages_regex' => '(de|fr)' ], $this );
	}

	public function testArchiveRulesIncludePagination()
	{
		$rules = $this->subdirectory( [ 'newsroom' => $this->postType( true ) ] )->post_type_archive_rewrite_rules();
		$this->assertSame( [
			'%lang%/newsroom/page/?([0-9]{1,})/?$' => 'index.php?post_type=newsroom&paged=$matches[2]&lang=$matches[1]',
			'%lang%/newsroom/?$' => 'index.php?post_type=newsroom&lang=$matches[1]',
		], $rules );
	}

	public function testArchiveSlugPrecedence()
	{
		$rules = $this->subdirectory( [
			'event' => $this->postType( 'whats-on' , [ 'slug' => 'events' ] ),
			'book' => $this->postType( true, [ 'slug' => 'library/books' ] ),
			'internal' => $this->postType( false, [ 'slug' => 'internal' ] ),
		] )->post_type_archive_rewrite_rules();
		$this->assertSame( 'index.php?post_type=event&lang=$matches[1]', $rules['%lang%/whats-on/?$'] );
		$this->assertSame( 'index.php?post_type=book&lang=$matches[1]', $rules['%lang%/library/books/?$'] );
		$this->assertCount( 4, $rules );
	}

	public function testArchiveRulesGoAboveThePageRules()
	{
		$wp_rules = [
			'(de|fr)/(.?.+?)/?$' => 'index.php?pagename=$matches[2]&lang=$matches[1]',
			'newsroom/?$' => 'index.php?post_type=newsroom',
		];
		$rules = $this->subdirectory( [ 'newsroom' => $this->postType( true ) ] )->rewrite_rules_array_hook( $wp_rules );
		$this->assertSame( [
			'(de|fr)/newsroom/page/?([0-9]{1,})/?$',
			'(de|fr)/newsroom/?$',
			'(de|fr)/(.?.+?)/?$',
			'newsroom/?$',
		], array_keys( $rules ) );
		// The first rule WP would match for /de/newsroom/ is the archive
		foreach ( $rules as $regex => $query ) {
			if ( preg_match( "#^$regex#", 'de/newsroom' ) ) {
				break;
			}
		}
		$this->assertSame( 'index.php?post_type=newsroom&lang=$matches[1]', $query );
	}

}
