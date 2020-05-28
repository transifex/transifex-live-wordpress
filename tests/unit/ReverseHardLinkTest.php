<?php

include_once __DIR__ .'/BaseTestCase.php';

class ReverseHardLinkTest extends BaseTestCase {

	private $data;

	function setUp(): void {
		include_once './includes/common/plugin-debug.php';
		include_once './includes/lib/transifex-live-integration-rewrite.php';
		include_once './includes/lib/transifex-live-integration-wp-services.php';

		$this->data = [[ //1
				'host' => 'http://www.mydomain.com',
				'lang' => 'zh_CN',
				'link' => 'http://www.mydomain.com/page-markup-and-formatting',
				'languages_map' => ["zh_CN" => "zh_CN", "de_DE" => "de_DE" ],
				'souce_lang' => 'en',
				'pattern' => '/http:\/\/www.mydomain.com\/(zh_CN|de_DE)\//',
				'result' => 'http://www.mydomain.com/zh_CN/page-markup-and-formatting'
			],
			[ //2
				'host' => 'http://www.mydomain.com',
				'lang' => 'zh_CN',
				'link' => 'http://www.mydomain.com/page-markup-and-formatting',
				'languages_map' => ["zh_CN" => "zh_CN", "de_DE" => "de_DE" ],
				'souce_lang' => 'zh_CN',
				'pattern' => '/http:\/\/www.mydomain.com\/(zh_CN|de_DE)\//',
				'result' => 'http://www.mydomain.com/page-markup-and-formatting'
			],
			[ //3
				'host' => 'http://www.mydomain.com',
				'lang' => 'zh_HK',
				'link' => 'http://www.mydomain.com/page-markup-and-formatting',
				'languages_map' => ["zh_CN" => "zh_CN", "de_DE" => "de_DE" ],
				'souce_lang' => 'en',
				'pattern' => '/http:\/\/www.mydomain.com\/(zh_CN|de_DE)\//',
				'result' => 'http://www.mydomain.com/page-markup-and-formatting'
			],
			[ //4
				'host' => 'http://www.mydomain.com',
				'lang' => 'cn',
				'link' => 'http://www.mydomain.com/page-markup-and-formatting',
				'languages_map' => ["zh_CN" => "cn", "de_DE" => "de_DE" ],
				'souce_lang' => 'en',
				'pattern' => '/http:\/\/www.mydomain.com\/(zh_CN|de_DE)\//',
				'result' => 'http://www.mydomain.com/cn/page-markup-and-formatting'
			],
			[ //5
				'host' => 'http://www.mydomain.com',
				'lang' => 'zh_CN',
				'link' => 'http://www.mydomain.com/page-markup-and-formatting',
				'languages_map' => [ ],
				'souce_lang' => 'en',
				'pattern' => '/http:\/\/www.mydomain.com\/(zh_CN|de_DE)\//',
				'result' => 'http://www.mydomain.com/page-markup-and-formatting'
			],
			[ //6
				'host' => 'http://www.mydomain.com',
				'lang' => null,
				'link' => 'http://www.mydomain.com/',
				'languages_map' => null,
				'souce_lang' => 'en',
				'pattern' => '/http:\/\/www.mydomain.com\/(zh_CN|de_DE)\//',
				'result' => 'http://www.mydomain.com/'
			],
			[ //7
				'host' => 'http://www.mydomain.com',
				'lang' => 'zh_CN',
				'link' => 'http://www.mydomain.com/page-markup-and-formatting',
				'languages_map' => ["zh_CN" => "cn", "de_DE" => "de_DE" ],
				'souce_lang' => 'en',
				'pattern' => '/http:\/\/www.mydomain.com\/(cn|de_DE)\//',
				'result' => 'http://www.mydomain.com/page-markup-and-formatting'
			],
			[ //8
				'host' => 'http://www.mydomain.com',
				'lang' => 'zh_CN',
				'link' => 'http://www.mydomain.com/page-markup-and-formatting',
				'languages_map' => ["zh_CN" => "zh_CN", "de_DE" => "de_DE" ],
				'souce_lang' => 'en',
				'pattern' => '/http:\/\/(zh_CN|de_DE|www).mydomain.com\/.*/',
				'result' => 'http://zh_CN.mydomain.com/page-markup-and-formatting'
			],
			[ //9
				'host' => 'http://www.mydomain.com',
				'lang' => 'cn',
				'link' => 'http://www.mydomain.com/page-markup-and-formatting',
				'languages_map' => ["zh_CN" => "cn", "de_DE" => "de_DE" ],
				'souce_lang' => 'en',
				'pattern' => '/http:\/\/(cn|de_DE|www).mydomain.com\/.*/',
				'result' => 'http://cn.mydomain.com/page-markup-and-formatting'
			],
			[ //10 plex case
				'host' => 'http://www.mydomain.com',
				'lang' => 'de',
				'link' => 'http://www.mydomain.com/page-markup-and-formatting-de',
				'languages_map' => ["zh_CN" => "cn", "de_DE" => "de" ],
				'souce_lang' => 'en',
				'pattern' => '/http:\/\/www.mydomain.com\/(cn|de)\//',
				'result' => 'http://www.mydomain.com/de/page-markup-and-formatting-de'
			],
			[ //11
				'host' => 'http://www.mydomain.com',
				'lang' => 'de',
				'link' => 'http://www.mydomain.com/page-markup-and-formatting-de',
				'languages_map' => ["zh_CN" => "cn", "de_DE" => "de" ],
				'souce_lang' => 'en',
				'pattern' => '/http:\/\/(cn|de|www).mydomain.com\/.*/',
				'result' => 'http://de.mydomain.com/page-markup-and-formatting-de'
			],
			[ //12 external link, leave intact
				'host' => 'http://www.mydomain.com',
				'lang' => 'de',
				'link' => 'http://www.another.com/page-markup-and-formatting-de',
				'languages_map' => ["zh_CN" => "cn", "de_DE" => "de" ],
				'souce_lang' => 'en',
				'pattern' => '/http:\/\/www.mydomain.com\/(cn|de)\//',
				'result' => 'http://www.another.com/page-markup-and-formatting-de'
			]
		];
	}

	public function testMe() {
		$counter = 0;
		foreach ($this->data as $i) {
			$rewrite = \Codeception\Stub::makeEmptyExcept(
				Transifex_Live_Integration_Rewrite::class,
				'reverse_hard_link', [
						'wp_services' => \Codeception\Stub::make(
							Transifex_Live_Integration_WP_Services::class, [
								'get_site_url' => $i['host']
								]
							)
					],
					$this
				);

			$counter = $counter + 1;
			$result = $rewrite->reverse_hard_link(
				$i['lang'], $i['link'], $i['languages_map'],
				$i['souce_lang'], $i['pattern']
			);

			$this->assertEquals( $i['result'], $result, 'Test Number:' . $counter );
		}
	}

}
