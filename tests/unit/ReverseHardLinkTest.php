<?php

class ReverseHardLinkTest extends \PHPUnit_Framework_TestCase
{

    private $data;
    protected function setUp()
    {
        include_once './includes/common/plugin-debug.php';
        include_once './includes/lib/transifex-live-integration-rewrite.php';
        $this->data = [[
            'lang' => 'zh_CN',
            'link' => 'http://192.168.99.100:32777/page-markup-and-formatting',
            'languages_map' => ["zh_CN" => "zh_CN", "de_DE" => "de_DE"],
            'souce_lang' => 'en',
            'result'=> 'http://192.168.99.100:32777/zh_CN/page-markup-and-formatting'
            ],
            [
            'lang' => 'zh_CN',
            'link' => 'http://192.168.99.100:32777/page-markup-and-formatting',
            'languages_map' => ["zh_CN" => "zh_CN", "de_DE" => "de_DE"],
            'souce_lang' => 'zh_CN',
            'result'=> 'http://192.168.99.100:32777/page-markup-and-formatting'
            ],
            [
            'lang' => 'zh_HK',
            'link' => 'http://192.168.99.100:32777/page-markup-and-formatting',
            'languages_map' => ["zh_CN" => "zh_CN", "de_DE" => "de_DE"],
            'souce_lang' => 'en',
            'result'=> 'http://192.168.99.100:32777/page-markup-and-formatting'
            ],
            [
            'lang' => 'cn',
            'link' => 'http://192.168.99.100:32777/page-markup-and-formatting',
            'languages_map' => ["zh_CN" => "cn", "de_DE" => "de_DE"],
            'souce_lang' => 'en',
            'result'=> 'http://192.168.99.100:32777/cn/page-markup-and-formatting'
            ],
            [
            'lang' => 'zh_CN',
            'link' => 'http://192.168.99.100:32777/page-markup-and-formatting',
            'languages_map' => [],
            'souce_lang' => 'en',
            'result'=> 'http://192.168.99.100:32777/page-markup-and-formatting'
            ],
            [
            'lang' => null,
            'link' => 'http://192.168.99.100:32777/',
            'languages_map' => null,
            'souce_lang' => 'en',
            'result'=> 'http://192.168.99.100:32777/'
            ]
            ];
    }

    public function testMe()
    {
        foreach ($this->data as $i) {
            $result = Transifex_Live_Integration_Rewrite::reverse_hard_link(
                $i['lang'], $i['link'], 
                $i['languages_map'], $i['souce_lang']
            );

            //eval(\Psy\sh());
            $this->assertEquals($i['result'], $result);
        }

    }
}
