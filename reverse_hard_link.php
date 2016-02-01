<?php
require('psysh');

class StackTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {

        $source = 'en';
        $lang = 'en';
        $languages = ['ko','de_DE','zh_CN'];
        $link = 'https://192.168.99.100/wp-json';
        $languages_map = ["zh_CN" => "zh_CN","de_DE" => "de_DE","ko" => "ko"];

        $modified_link = $link;
        $reverse_url = true;
        $condition = "";
        $reverse_url = ($reverse_url)?(isset($lang)):false;
        $condition .= ($reverse_url)?"A OK":"A Failed";
        $reverse_url = ($reverse_url)?(!strpos($modified_link,$lang)):false;
        $condition .= ($reverse_url)?"B OK":"B Failed";
        $reverse_url = ($reverse_url)?(array_key_exists($lang,$languages_map)):false;
        $condition .= ($reverse_url)?"C OK":"C Failed";
        $reverse_url = ($reverse_url)?(!($source == $lang)):false; 
        $condition .= ($reverse_url)?"D OK":"D Failed";   
        
        if ($reverse_url) {
            $array_url = explode( '/', $link );
            $array_url[3] = $languages_map[$lang]. '/' . $array_url[3];
            $modified_link = implode( '/', $array_url );
        }


    		eval(\Psy\sh());
    	//	$this->assertEquals($i['expected_source'],$source_string);
    	//	$this->assertEquals(ksort($i['expected_languages']),ksort($language_array));
}

}

?>