<?php

include_once __DIR__ .'/BaseTestCase.php';

// home_url_hook() takes itself off and back on the home_url filter while it
// runs; out here there is no filter API to do that against.
if ( !function_exists( 'remove_filter' ) ) {
	function remove_filter( ...$args ) {
		return true;
	}
}
if ( !function_exists( 'add_filter' ) ) {
	function add_filter( ...$args ) {
		return true;
	}
}

// Stands in for Divi's Visual Builder check, switched on per test.
if ( !function_exists( 'et_core_is_fb_enabled' ) ) {
	function et_core_is_fb_enabled() {
		return !empty( $GLOBALS['tx_test_fb_enabled'] );
	}
}

class LinkRewriteTest extends BaseTestCase
{

	protected function setUp(): void
	{
		include_once './includes/common/plugin-debug.php';
		include_once './includes/common/transifex-live-integration-validators.php';
		include_once './includes/transifex-live-integration-util.php';
		include_once './includes/lib/transifex-live-integration-rewrite.php';
		include_once './includes/lib/transifex-live-integration-wp-services.php';
	}

	private function makeRewrite( $lang, $host = 'https://example.com' )
	{
		return \Codeception\Stub::make(
			Transifex_Live_Integration_Rewrite::class, [
				'lang' => $lang,
				'source_language' => 'en',
				'languages_map' => [ 'de_DE' => 'de', 'fr_FR' => 'fr' ],
				'rewrite_pattern' => '#' . $host . '/%LANG%/.*#',
				'wp_services' => \Codeception\Stub::make(
					Transifex_Live_Integration_WP_Services::class, [
						'get_site_url' => $host
					]
				)
			],
			$this
		);
	}

	private function reverse( $rewrite, $link )
	{
		return $rewrite->reverse_hard_link(
			'de', $link, [ 'de_DE' => 'de' ], 'en', '#https://example.com/%LANG%/.*#'
		);
	}

	public function testCorePathsKeepTheirSourceForm()
	{
		$rewrite = $this->makeRewrite( 'de' );
		foreach ([
			'/wp-login.php',
			'https://example.com/wp-login.php?action=logout',
			'/wp-content/themes/theme/brochure.pdf',
			'https://example.com/wp-includes/js/jquery.js',
			'https://example.com/wp-admin/',
		] as $link) {
			$this->assertEquals( $link, $this->reverse( $rewrite, $link ), $link );
		}
	}

	public function testProtocolRelativeLinkIsLeftAlone()
	{
		$rewrite = $this->makeRewrite( 'de' );
		$this->assertEquals(
			'//example.com/about',
			$this->reverse( $rewrite, '//example.com/about' )
		);
	}

	public function testContentLinksAreRewrittenInPlace()
	{
		$rewrite = $this->makeRewrite( 'de' );
		$cases = [
			// "/" must not be replaced everywhere it occurs in the markup
			'<div class="wp-block-group"><a href="/">Home</a></div>'
				=> '<div class="wp-block-group"><a href="/de/">Home</a></div>',
			// a slug that another slug opens with
			'<a href="/about">A</a> <a href="/about-us">B</a>'
				=> '<a href="/de/about">A</a> <a href="/de/about-us">B</a>',
			// an external url and an image sharing the path stay untouched
			'<a href="/about">A</a> <a href="https://other.org/about">ext</a> <img src="/about.png">'
				=> '<a href="/de/about">A</a> <a href="https://other.org/about">ext</a> <img src="/about.png">',
			// quoting styles and other attributes
			"<a class='btn' href='/support'>S</a> <a href=/contact>C</a> <a data-href=\"/x\" href=\"https://example.com/y?a=1&amp;b=2#top\">Y</a>"
				=> "<a class='btn' href='/de/support'>S</a> <a href=/de/contact>C</a> <a data-href=\"/x\" href=\"https://example.com/de/y?a=1&amp;b=2#top\">Y</a>",
			// links that are not same-site pages
			'<a href="#top">T</a> <a href="mailto:hi@example.com">M</a> <a href="">E</a> <a href="/wp-login.php">L</a>'
				=> '<a href="#top">T</a> <a href="mailto:hi@example.com">M</a> <a href="">E</a> <a href="/wp-login.php">L</a>',
		];
		foreach ($cases as $in => $expected) {
			$this->assertEquals( $expected, $rewrite->the_content_hook( $in ), $in );
		}
	}

	public function testContentRewriteIsIdempotent()
	{
		$rewrite = $this->makeRewrite( 'de' );
		$html = '<a href="/about">A</a> <a href="https://example.com/about-us">B</a>';
		$once = $rewrite->the_content_hook( $html );
		$this->assertEquals( $once, $rewrite->the_content_hook( $once ) );
	}

	public function testContentIsLeftAloneInTheSourceLanguage()
	{
		$html = '<a href="/about">A</a>';
		$this->assertEquals( $html, $this->makeRewrite( 'en' )->the_content_hook( $html ) );
		$this->assertEquals( $html, $this->makeRewrite( false )->the_content_hook( $html ) );
	}

	public function testLinksWithHreflangKeepTheirLanguage()
	{
		$rewrite = $this->makeRewrite( 'de' );
		$html = '<a href="https://example.com/" hreflang="en">English</a> <a hreflang=fr href="/fr/about">FR</a>';
		$this->assertEquals( $html, $rewrite->the_content_hook( $html ) );
	}

	public function testShortcodeOutputIsRewritten()
	{
		$rewrite = $this->makeRewrite( 'de' );
		// Divi Theme Builder text and code modules, and a third-party module
		foreach ( [ 'et_pb_text', 'et_pb_code', 'et_pb_dp_dmb_module' ] as $tag ) {
			$this->assertEquals(
				'<div class="et_pb_text_inner"><p><a href="https://example.com/de/support/">Support</a></p></div>',
				$rewrite->do_shortcode_tag_hook( '<div class="et_pb_text_inner"><p><a href="https://example.com/support/">Support</a></p></div>', $tag ),
				$tag
			);
		}
	}

	public function testShortcodeContainersAreSkipped()
	{
		$rewrite = $this->makeRewrite( 'de' );
		$html = '<div class="et_pb_section"><a href="/about">A</a></div>';
		foreach ( [ 'et_pb_section', 'et_pb_row', 'et_pb_column', 'get_language_url' ] as $tag ) {
			$this->assertEquals( $html, $rewrite->do_shortcode_tag_hook( $html, $tag ), $tag );
		}
	}

	public function testShortcodeOutputIsLeftAloneInTheSourceLanguage()
	{
		$html = '<a href="/about">A</a>';
		$this->assertEquals( $html, $this->makeRewrite( 'en' )->do_shortcode_tag_hook( $html, 'et_pb_text' ) );
		$this->assertEquals( $html, $this->makeRewrite( false )->do_shortcode_tag_hook( $html, 'et_pb_text' ) );
		// non-string output some shortcodes return
		$this->assertSame( true, $this->makeRewrite( 'de' )->do_shortcode_tag_hook( true, 'is_language' ) );
	}

	public function testShortcodeRewriteIsIdempotent()
	{
		$rewrite = $this->makeRewrite( 'de' );
		$once = $rewrite->do_shortcode_tag_hook( '<a href="/about">A</a>', 'et_pb_text' );
		// the parent module rewrites its already rewritten child again
		$this->assertEquals( '<a href="/de/about">A</a>', $once );
		$this->assertEquals( $once, $rewrite->do_shortcode_tag_hook( $once, 'et_pb_tabs' ) );
	}

	public function testShortcodeOutputIsLeftAloneInTheVisualBuilder()
	{
		$GLOBALS['tx_test_fb_enabled'] = true;
		$html = '<a href="/about">A</a>';
		$result = $this->makeRewrite( 'de' )->do_shortcode_tag_hook( $html, 'et_pb_text' );
		$GLOBALS['tx_test_fb_enabled'] = false;
		$this->assertEquals( $html, $result );
	}

	public function testRenderBlockRewritesLeafBlocksOnly()
	{
		$rewrite = $this->makeRewrite( 'de' );
		$html = '<a href="/about">A</a>';

		$this->assertEquals(
			'<a href="/de/about">A</a>',
			$rewrite->render_block_hook( $html, [ 'blockName' => 'core/navigation-link', 'innerBlocks' => [] ] )
		);
		// A parent's markup holds children that were rewritten on their own
		$this->assertEquals(
			$html,
			$rewrite->render_block_hook( $html, [ 'blockName' => 'core/group', 'innerBlocks' => [ [ 'blockName' => 'core/paragraph' ] ] ] )
		);
		// except for a block that prints a link of its own around them
		$this->assertEquals(
			'<a href="/de/about">A</a>',
			$rewrite->render_block_hook( $html, [ 'blockName' => 'core/navigation-submenu', 'innerBlocks' => [ [ 'blockName' => 'core/navigation-link' ] ] ] )
		);
	}

	public function testHomeUrlIsLeftAloneUntilTheRequestIsRouted()
	{
		// WP::parse_request() reads home_url() to strip the install path off
		// the request. A localized one there would strip the language too.
		$rewrite = $this->makeRewrite( false );
		$this->assertEquals( 'https://example.com', $rewrite->home_url_hook( 'https://example.com' ) );

		$wp = new stdClass();
		$wp->query_vars = [ 'lang' => 'de', 'pagename' => 'support' ];
		$rewrite->parse_request_hook( $wp );
		$this->assertEquals( 'https://example.com/de/', $rewrite->home_url_hook( 'https://example.com' ) );
		$this->assertEquals( 'de', $rewrite->detect_language() );
	}

	public function testSourceRequestKeepsHomeUrl()
	{
		$rewrite = $this->makeRewrite( false );
		$wp = new stdClass();
		$wp->query_vars = [ 'pagename' => 'support' ];
		$rewrite->parse_request_hook( $wp );
		$this->assertEquals( 'en', $rewrite->detect_language() );
		$this->assertEquals( 'https://example.com', $rewrite->home_url_hook( 'https://example.com' ) );
	}

	public function testHomeUrlIsNotPaddedWhenLeftUnlocalized()
	{
		// rest_url() appends '/' . $route to home_url('wp-json'), so a padded
		// prefix comes out as wp-json//, which the REST API answers with a 404.
		$rewrite = $this->makeRewrite( 'de' );
		$this->assertEquals( 'https://example.com/wp-json', $rewrite->home_url_hook( 'https://example.com/wp-json' ) );
		$this->assertEquals( 'https://example.com/xmlrpc.php', $rewrite->home_url_hook( 'https://example.com/xmlrpc.php' ) );
		$this->assertEquals( 'https://www.google.com/about', $rewrite->home_url_hook( 'https://www.google.com/about' ) );
	}

	public function testHomeUrlKeepsItsOwnForm()
	{
		$rewrite = $this->makeRewrite( 'de' );
		$this->assertEquals( 'https://example.com/de/', $rewrite->home_url_hook( 'https://example.com' ) );
		$this->assertEquals( 'https://example.com/de/', $rewrite->home_url_hook( 'https://example.com/' ) );
		$this->assertEquals( 'https://example.com/de/feed', $rewrite->home_url_hook( 'https://example.com/feed' ) );
		$this->assertEquals( 'https://example.com/de/support/', $rewrite->home_url_hook( 'https://example.com/support/' ) );
		// The query stays last, with no slash tacked onto its value
		$this->assertEquals( 'https://example.com/de/?s=shoes', $rewrite->home_url_hook( 'https://example.com?s=shoes' ) );
		$this->assertEquals( 'https://example.com/de/?s=shoes', $rewrite->home_url_hook( 'https://example.com/?s=shoes' ) );
	}

}
