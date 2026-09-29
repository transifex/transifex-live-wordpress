<?php

include_once __DIR__ .'/BaseTestCase.php';

if ( !defined( 'TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE' ) ) {
	define( 'TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE', '.' );
}

class CanonicalUrlTest extends BaseTestCase
{

	protected function setUp(): void
	{
		include_once './includes/common/plugin-debug.php';
		include_once './includes/lib/transifex-live-integration-hreflang.php';
	}

	// Entries as generate_languages_hreflang() builds them, "de" listed first
	private function hreflangs( $host_for )
	{
		return [
			[ 'href' => $host_for( 'de' ) . 'contact-support/', 'hreflang' => 'de', 'code' => 'de', 'is_source' => false ],
			[ 'href' => $host_for( 'fr' ) . 'contact-support/', 'hreflang' => 'fr', 'code' => 'fr', 'is_source' => false ],
		];
	}

	private function subdirectory()
	{
		return $this->hreflangs( function ( $code ) { return 'https://example.com/' . $code . '/'; } );
	}

	private function subdomain()
	{
		return $this->hreflangs( function ( $code ) { return 'https://' . $code . '.example.com/'; } );
	}

	public function testCanonicalIsThePageInTheCurrentLanguage()
	{
		$source_url = 'https://example.com/contact-support/';
		foreach ( [ 'subdirectory', 'subdomain' ] as $mode ) {
			$hreflangs = $this->$mode();
			// the source page used to get the first translation's url
			$this->assertEquals( $source_url, Transifex_Live_Integration_Hreflang::canonical_url( 'en', $source_url, $hreflangs ), $mode );
			$this->assertEquals( $hreflangs[0]['href'], Transifex_Live_Integration_Hreflang::canonical_url( 'de', $source_url, $hreflangs ), $mode );
			// not the translation listed first
			$this->assertEquals( $hreflangs[1]['href'], Transifex_Live_Integration_Hreflang::canonical_url( 'fr', $source_url, $hreflangs ), $mode );
		}
	}

	public function testCanonicalFallsBackToTheSourceUrl()
	{
		$source_url = 'https://example.com/';
		$hreflangs = [ [ 'href' => 'https://example.com/de/', 'hreflang' => 'de', 'code' => 'de', 'is_source' => false ] ];
		$this->assertEquals( $source_url, Transifex_Live_Integration_Hreflang::canonical_url( '', $source_url, $hreflangs ) );
		$this->assertEquals( $source_url, Transifex_Live_Integration_Hreflang::canonical_url( 'it', $source_url, $hreflangs ) );
		$this->assertEquals( 'https://example.com/de/', Transifex_Live_Integration_Hreflang::canonical_url( 'de', $source_url, $hreflangs ) );
	}

	public function testSeoPluginCanonicalIsLocalized()
	{
		$source_url = 'https://example.com/contact-support/';
		$hreflangs = $this->subdirectory();
		$expected = 'https://example.com/de/contact-support/';
		foreach ([
			'https://example.com/contact-support/',
			// scheme, www. and trailing slash differences
			'http://www.example.com/contact-support',
			// already localized
			'https://example.com/de/contact-support/',
		] as $canonical) {
			$this->assertEquals( $expected, Transifex_Live_Integration_Hreflang::localize_canonical( $canonical, 'de', $source_url, $hreflangs ), $canonical );
		}
	}

	public function testSeoPluginCanonicalToAnotherPageIsKept()
	{
		$source_url = 'https://example.com/contact-support/';
		$hreflangs = $this->subdirectory();
		foreach ([
			'https://example.com/support/',
			'https://example.com/contact-support/?replytocom=5',
			'https://other.org/contact-support/',
		] as $canonical) {
			$this->assertEquals( $canonical, Transifex_Live_Integration_Hreflang::localize_canonical( $canonical, 'de', $source_url, $hreflangs ), $canonical );
		}
	}

	public function testSeoPluginCanonicalIsKeptInTheSourceLanguage()
	{
		$source_url = 'https://example.com/contact-support/';
		$this->assertEquals( $source_url, Transifex_Live_Integration_Hreflang::localize_canonical( $source_url, 'en', $source_url, $this->subdirectory() ) );
	}
}
