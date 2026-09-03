<?php

include_once __DIR__ .'/BaseTestCase.php';

class IsRestRouteTest extends BaseTestCase
{

    protected function setUp(): void
    {
        include_once './includes/lib/transifex-live-integration-rewrite.php';
    }

    public function testRestPathsAreDetected()
    {
        // rest_get_url_prefix() belongs to WP, so out here the check falls back
        // to the prefix WP uses by default.
        $rest_paths = [
        '/wp-json',
        '/wp-json/',
        '/wp-json/wp/v2/posts',
        '/wp-json/contact-form-7/v1/contact-forms/12/feedback',
        // Permalinks that carry index.php in the path
        '/index.php/wp-json',
        '/index.php/wp-json/wp/v2/posts',
        ];

        foreach ($rest_paths as $path) {
            $this->assertTrue(
                Transifex_Live_Integration_Rewrite::is_rest_route($path),
                $path .' should be recognised as a REST route'
            );
        }
    }

    public function testContentPathsAreLeftAlone()
    {
        // A slug that opens with the prefix belongs to a page like any other, so
        // the prefix has to match the start of the path rather than appear
        // anywhere in it.
        $content_paths = [
        '',
        '/',
        '/about/',
        '/blog/wp-json-guide/',
        '/wp-jsonp/',
        '/de/wp-json-explained/',
        ];

        foreach ($content_paths as $path) {
            $this->assertFalse(
                Transifex_Live_Integration_Rewrite::is_rest_route($path),
                $path .' should be localized like any other path'
            );
        }
    }

}
