<?php
include_once __DIR__ .'/BaseTestCase.php';

class CalculateDefaultsTest extends BaseTestCase
{
    private $data_subdomain;
	private $data_subdirectory;
    protected function setUp()
    {
        include_once './includes/common/plugin-debug.php';
        include_once './includes/transifex-live-integration-defaults.php';
        $this->data_subdomain = [[ //1
            'source_alias' => 'www',
            'result'=> 'http://%LANG%.mydomain.com'
            ],
            [ //2
			'source_alias' => 'com',
            'result'=> 'http://www.mydomain.%LANG%'
            ],
            [ //3
			'source_alias' => 'mydomain',
            'result'=> 'http://www.%LANG%.com'
            ]];
		$this->data_subdirectory = [[ //1
            'result'=> 'http://www.mydomain.com/%LANG%'
            ]];
    }

    public function testMe()
    {
		$counter = 0;
        foreach ($this->data_subdomain as $i) {
			$counter = $counter + 1;
            $result = Transifex_Live_Integration_Defaults::calc_default_subdomain(
                $i['source_alias']
            );
//            eval(\Psy\sh());
            $this->assertEquals($i['result'], $result,'Test Number:'.$counter);
        }
		$counter = 0;
        foreach ($this->data_subdirectory as $i) {
			$counter = $counter + 1;
            $result = Transifex_Live_Integration_Defaults::calc_default_subdirectory();
//            eval(\Psy\sh());
            $this->assertEquals($i['result'], $result,'Test Number:'.$counter);
        }

    }
}
