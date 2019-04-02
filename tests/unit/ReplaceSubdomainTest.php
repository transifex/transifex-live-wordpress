<?php
include_once __DIR__ .'/BaseTestCase.php';

class ReplaceSubdomainTest extends BaseTestCase
{

    private $data;

    protected function setUp() 
    {
        include_once './includes/common/plugin-debug.php';
        include_once './includes/common/transifex-live-integration-common.php';
        include_once './includes/transifex-live-integration-util.php';
        $this->data = [
        [
        'page_url' => 'https://www.foo.bar/about/',
        'lang' => 'fr',
        'result' => 'https://fr.foo.bar/about/',
        ], [
        'page_url' => 'https://foo.bar/about/',
        'lang' => 'fr',
        'result' => 'https://fr.foo.bar/about/',
        ], [
        'page_url' => 'http://www.foo.bar/www-is-awesome/',
        'lang' => 'fr',
        'result' => 'http://fr.foo.bar/www-is-awesome/',
        ], [
        'page_url' => 'http://www.foo.bar/about/',
        'lang' => 'fr_FR',
        'result' => 'http://fr_FR.foo.bar/about/',
        ]];
    }

    public function testMe() 
    {
        foreach ($this->data as $d) {
            $result = Transifex_Live_Integration_Util::replace_lang_subdomain(
                $d['page_url'], $d['lang']
            );
            $this->assertEquals($d['result'], $result);
        }
    }

}
