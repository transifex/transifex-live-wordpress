<?php
require('psysh');

class StackTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
    	$arr = [];
    	$test_data['input'] = 'transifex_languages({"timestamp": "1444786177.23", "translation": [{"url": "//cdnl.transifex.com/2699bc66df6546008d0a14acf26732a1/latest/fr.bf888fc164074fd98bf827843db212b4.jsonp", "tx_name": "French", "code": "fr", "name": "Fran\u00e7ais", "rtl": false}], "source": {"tx_name": "English", "code": "en", "name": "English", "rtl": false}});';
    	$test_data['expected_languages'] = [["code" => "fr","name" => "French"]];
    	$test_data['expected_source'] = 'en';
    	array_push($arr,$test_data);

		$test_data['input'] = 'transifex_languages({"timestamp": "1450373800.92", "translation": [{"url": "//cdn.transifex.com/2699bc66df6546008d0a14acf26732a1/latest/zh_CN.f5a69719038448b9926a8950554ab9f4.jsonp", "tx_name": "Chinese (China)", "code": "zh_CN", "name": "\u4e2d\u6587 (\u4e2d\u56fd)", "rtl": false}, {"url": "//cdn.transifex.com/2699bc66df6546008d0a14acf26732a1/latest/fr.f5a69719038448b9926a8950554ab9f4.jsonp", "tx_name": "French", "code": "fr", "name": "Fran\u00e7ais", "rtl": false}, {"url": "//cdn.transifex.com/2699bc66df6546008d0a14acf26732a1/latest/de_DE.f5a69719038448b9926a8950554ab9f4.jsonp", "tx_name": "German (Germany)", "code": "de_DE", "name": "Deutsch", "rtl": false}, {"url": "//cdn.transifex.com/2699bc66df6546008d0a14acf26732a1/latest/ko.f5a69719038448b9926a8950554ab9f4.jsonp", "tx_name": "Korean", "code": "ko", "name": "\ud55c\uad6d\uc5b4", "rtl": false}, {"url": "//cdn.transifex.com/2699bc66df6546008d0a14acf26732a1/latest/es.f5a69719038448b9926a8950554ab9f4.jsonp", "tx_name": "Spanish", "code": "es", "name": "Espa\u00f1ol", "rtl": false}], "source": {"tx_name": "English", "code": "en", "name": "English", "rtl": false}});';
		$test_data['expected_languages'] = [["code" => "fr","name" => "French"],["tx_name"=> "Chinese (China)", "code"=> "zh_CN"],["tx_name"=> "German (Germany)", "code"=> "de_DE"],["tx_name"=> "Spanish", "code"=> "es"],["tx_name"=> "Korean", "code"=> "ko"]];
		$test_data['expected_source'] = 'en';
    	array_push($arr,$test_data);

    	$reg = '/\s*transifex_languages\(\s*(.+?)\s*\);/';

    	foreach ($arr as $i) {
    		preg_match($reg,$i['input'],$m);
    		$tl_array = json_decode($m[1],true);
    		
    		$tl_s_array = $tl_array['source'];
    		$source_string = $tl_s_array['code'];

    		$tl_t_array = $tl_array['translation'];

            $language_array = array_map(
                function($x) { 
                    $arr = ["code" => $x['code'],"name" => $x['tx_name']];
                    return $arr; }, 
                    $tl_t_array
                );
    		eval(\Psy\sh());
    		$this->assertEquals($i['expected_source'],$source_string);
    		$this->assertEquals(ksort($i['expected_languages']),ksort($language_array));
}
    		eval(\Psy\sh());
}
}



?>