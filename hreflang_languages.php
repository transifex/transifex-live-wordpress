<?php
require('psysh');

class StackTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {

        $lang = 'ko';
        $languages = ['ko','de_DE','zh_CN'];
        $raw_url = 'http://www.transifex.com/ko/';
        $languages_map = ["zh_CN" => "zh_CN","de_DE" => "de_DE","ko" => "ko"];

        $ret = [];
        $tokenized_url = str_replace( $lang , "%lang%", $raw_url, $count);
        if ($count !== 0) {
        foreach ($languages as $language) {
            $arr = [];
            $hreflang_code = $languages_map[$language];
            $language_url = str_replace( '%lang%', $hreflang_code, $tokenized_url );
            $arr['href'] = $language_url;
            $arr['hreflang'] = $hreflang_code;
            array_push($ret,$arr);
        }
    }
    		eval(\Psy\sh());
    	//	$this->assertEquals($i['expected_source'],$source_string);
    	//	$this->assertEquals(ksort($i['expected_languages']),ksort($language_array));
}

}

?>