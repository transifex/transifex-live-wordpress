<?php

class GenerateTokenizedUrlTest extends \PHPUnit_Framework_TestCase
{

    private $data;
    protected function setUp()
    {
		require_once './includes/plugin-debug.php';
        include_once './includes/admin/transifex-live-integration-settings-util.php';
        $this->data = [[
            'site_url' => 'http://192.168.99.100:32777',
            'url_option_setting' => '2', // Subdomain option
            'result' => 'http://%lang%.168.99.100:32777/'
            ],
            [
            'site_url' => 'http://192.168.99.100:32777',
            'url_option_setting' => '3', // Subdirectory option
            'result' => 'http://192.168.99.100:32777/%lang%/'
            ],
            ];
        // negative options tests
            $neg_options = [1,'1','',' ',null];
            foreach ($neg_options as $o) {
                array_push($this->data,['site_url'=>'http://192.168.99.100:32777','url_option_setting'=>$o,'result'=>false]);
            }

    }

    public function testMe()
    {
        foreach ($this->data as $d) {
        $result = Transifex_Live_Integration_Settings_Util::generate_tokenized_url($d['site_url'], $d['url_option_setting']);

 //       eval(\Psy\sh());
        $this->assertEquals($d['result'], $result);
    }

    }
}
