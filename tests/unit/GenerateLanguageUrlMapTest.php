<?php

class GenerateLanguageUrlMapTest extends \PHPUnit_Framework_TestCase {

	private $data;

	protected function setUp() {
		require_once './includes/plugin-debug.php';
		include_once './includes/transifex-live-integration-picker.php';
		$this->data = [
		[
		'raw_url' => '/about',
		'tokenized_url' => 'http://192.168.99.100:32777/%lang%/',
		'language_map' => [ 'zh_CN' => 'cn'],
		'result' => ['zh_CN' => 'http://192.168.99.100:32777/cn/about']
			], [
		'raw_url' => '2015/12/17/hello-world/',
		'tokenized_url' => 'http://192.168.99.100:32777/%lang%/',
		'language_map' => [ 'zh_CN' => 'cn'],
		'result' => ['zh_CN' => 'http://192.168.99.100:32777/cn/2015/12/17/hello-world/']
				]
			];
		// negative options go here

	}

	public function testMe() {
		foreach ($this->data as $d) {
			$result = Transifex_Live_Integration_Picker::generate_language_url_map( $d['raw_url'], $d['tokenized_url'], $d['language_map'] );

			//       eval(\Psy\sh());
			$this->assertEquals( $d['result'], $result );
		}
	}

}
