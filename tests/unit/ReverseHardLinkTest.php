<?php

class ReverseHardLinkTest extends \PHPUnit_Framework_TestCase
{

    private $data;
    protected function setUp()
    {
        include_once './includes/common/plugin-debug.php';
        include_once './includes/lib/transifex-live-integration-rewrite.php';
        $this->data = [[ //1
            'lang' => 'zh_CN',
            'link' => 'http://www.mydomain.com/page-markup-and-formatting',
            'languages_map' => ["zh_CN" => "zh_CN", "de_DE" => "de_DE"],
            'souce_lang' => 'en',
			'pattern' => '/http:\/\/www.mydomain.com\/(zh_CN|de_DE)\//',
            'result'=> 'http://www.mydomain.com/zh_CN/page-markup-and-formatting'
            ],
            [ //2
            'lang' => 'zh_CN',
            'link' => 'http://www.mydomain.com/page-markup-and-formatting',
            'languages_map' => ["zh_CN" => "zh_CN", "de_DE" => "de_DE"],
            'souce_lang' => 'zh_CN',
			'pattern' => '/http:\/\/www.mydomain.com\/(zh_CN|de_DE)\//',
            'result'=> 'http://www.mydomain.com/page-markup-and-formatting'
            ],
            [ //3
            'lang' => 'zh_HK',
            'link' => 'http://www.mydomain.com/page-markup-and-formatting',
            'languages_map' => ["zh_CN" => "zh_CN", "de_DE" => "de_DE"],
            'souce_lang' => 'en',
			'pattern' => '/http:\/\/www.mydomain.com\/(zh_CN|de_DE)\//',
            'result'=> 'http://www.mydomain.com/page-markup-and-formatting'
            ],
            [ //4
            'lang' => 'cn',
            'link' => 'http://www.mydomain.com/page-markup-and-formatting',
            'languages_map' => ["zh_CN" => "cn", "de_DE" => "de_DE"],
            'souce_lang' => 'en',
			'pattern' => '/http:\/\/www.mydomain.com\/(zh_CN|de_DE)\//',
            'result'=> 'http://www.mydomain.com/cn/page-markup-and-formatting'
            ],
            [ //5
            'lang' => 'zh_CN',
            'link' => 'http://www.mydomain.com/page-markup-and-formatting',
            'languages_map' => [],
            'souce_lang' => 'en',
			'pattern' => '/http:\/\/www.mydomain.com\/(zh_CN|de_DE)\//',
            'result'=> 'http://www.mydomain.com/page-markup-and-formatting'
            ],
            [ //6
            'lang' => null,
            'link' => 'http://www.mydomain.com/',
            'languages_map' => null,
            'souce_lang' => 'en',
			'pattern' => '/http:\/\/www.mydomain.com\/(zh_CN|de_DE)\//',
            'result'=> 'http://www.mydomain.com/'
            ],
			[ //7
            'lang' => 'zh_CN',
            'link' => 'http://www.mydomain.com/page-markup-and-formatting',
            'languages_map' => ["zh_CN" => "cn", "de_DE" => "de_DE"],
            'souce_lang' => 'en',
			'pattern' => '/http:\/\/www.mydomain.com\/(cn|de_DE)\//',
            'result'=> 'http://www.mydomain.com/page-markup-and-formatting'
            ],
			[ //8
            'lang' => 'zh_CN',
            'link' => 'http://www.mydomain.com/page-markup-and-formatting',
            'languages_map' => ["zh_CN" => "zh_CN", "de_DE" => "de_DE"],
            'souce_lang' => 'en',
			'pattern' => '/http:\/\/(zh_CN|de_DE|www).mydomain.com\/.*/',
            'result'=> 'http://zh_CN.mydomain.com/page-markup-and-formatting'
            ],
			[ //9
            'lang' => 'cn',
            'link' => 'http://www.mydomain.com/page-markup-and-formatting',
            'languages_map' => ["zh_CN" => "cn", "de_DE" => "de_DE"],
            'souce_lang' => 'en',
			'pattern' => '/http:\/\/(cn|de_DE|www).mydomain.com\/.*/',
            'result'=> 'http://cn.mydomain.com/page-markup-and-formatting'
            ]
            ];
    }

    public function testMe()
    {
		$counter = 0;
        foreach ($this->data as $i) {
			$counter = $counter + 1;
            $result = Transifex_Live_Integration_Rewrite::reverse_hard_link(
                $i['lang'], $i['link'], 
                $i['languages_map'], $i['souce_lang'], $i['pattern']
            );

//            eval(\Psy\sh());
            $this->assertEquals($i['result'], $result,'Test Number:'.$counter);
        }

    }
}
