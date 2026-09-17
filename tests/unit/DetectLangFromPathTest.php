<?php

include_once __DIR__ .'/BaseTestCase.php';

class DetectLangFromPathTest extends BaseTestCase
{

	protected function setUp(): void
	{
		include_once './includes/lib/transifex-live-integration-rewrite.php';
	}

	public function testLanguageSegmentIsReadOffThePath()
	{
		$this->assertEquals(
			'de',
			Transifex_Live_Integration_Rewrite::detect_lang_from_path(
				'/de/about/?foo=1',
				array( 'de', 'fr' )
			)
		);
	}

	public function testSourcePathHasNoLanguage()
	{
		$this->assertEquals(
			'',
			Transifex_Live_Integration_Rewrite::detect_lang_from_path(
				'/about/',
				array( 'de', 'fr' )
			)
		);
	}

	public function testSiteSubdirectoryIsSkipped()
	{
		$this->assertEquals(
			'de',
			Transifex_Live_Integration_Rewrite::detect_lang_from_path(
				'/cms/de/about',
				array( 'de' ),
				'/cms'
			)
		);
	}

	public function testSlugThatOpensWithALanguageCodeIsNotALocale()
	{
		$this->assertEquals(
			'',
			Transifex_Live_Integration_Rewrite::detect_lang_from_path(
				'/design',
				array( 'de' )
			)
		);
	}

	public function testHostsMatchIgnoresWww()
	{
		$this->assertTrue(
			Transifex_Live_Integration_Rewrite::hosts_match(
				'www.mydomain.com',
				'mydomain.com'
			)
		);
		$this->assertFalse(
			Transifex_Live_Integration_Rewrite::hosts_match(
				'mydomain.com',
				'another.com'
			)
		);
	}

	public function testPrependLangToPathIsIdempotentAndSegmentBased()
	{
		$this->assertEquals(
			'/de/design',
			Transifex_Live_Integration_Rewrite::prepend_lang_to_path( '/design', 'de' )
		);
		$this->assertEquals(
			'/de/about',
			Transifex_Live_Integration_Rewrite::prepend_lang_to_path( '/de/about', 'de' )
		);
		$this->assertEquals(
			'/cms/de/about',
			Transifex_Live_Integration_Rewrite::prepend_lang_to_path( '/cms/about', 'de', '/cms' )
		);
	}

}
