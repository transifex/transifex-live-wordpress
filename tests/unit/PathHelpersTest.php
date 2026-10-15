<?php

include_once __DIR__ .'/BaseTestCase.php';

class PathHelpersTest extends BaseTestCase
{

	protected function setUp(): void
	{
		include_once './includes/lib/transifex-live-integration-rewrite.php';
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
		$this->assertFalse(
			Transifex_Live_Integration_Rewrite::hosts_match( '', 'mydomain.com' )
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
			'/de/',
			Transifex_Live_Integration_Rewrite::prepend_lang_to_path( '/', 'de' )
		);
		$this->assertEquals(
			'/cms/de/about',
			Transifex_Live_Integration_Rewrite::prepend_lang_to_path( '/cms/about', 'de', '/cms' )
		);
	}

	public function testCorePathsAreExcluded()
	{
		$excluded_paths = [
		'/wp-admin/',
		'/wp-admin/post.php',
		'/wp-content/uploads/2024/01/file.pdf',
		'/wp-content/themes/theme/style.css',
		'/wp-content/plugins/plugin/script.js',
		'/wp-includes/js/jquery.js',
		'/wp-login.php',
		'/xmlrpc.php',
		'/index.php',
		// An install served from a subdirectory puts its path ahead of them
		'/cms/wp-content/themes/theme/style.css',
		'/cms/wp-login.php',
		'/wp-json/wp/v2/posts',
		];

		foreach ($excluded_paths as $path) {
			$this->assertTrue(
				Transifex_Live_Integration_Rewrite::is_excluded_path($path),
				$path .' should keep its source form'
			);
		}
	}

	public function testContentPathsAreNotExcluded()
	{
		$content_paths = [
		'',
		'/',
		'/about/',
		'/support',
		'/blog/wp-content-strategy/',
		'/php-tips/',
		'/index.php/about',
		];

		foreach ($content_paths as $path) {
			$this->assertFalse(
				Transifex_Live_Integration_Rewrite::is_excluded_path($path),
				$path .' should be localized like any other path'
			);
		}
	}

}
