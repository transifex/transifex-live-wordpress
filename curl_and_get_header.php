<?php
require('psysh');

class StackTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, 'http://192.168.99.100:32769/http://192.168.99.100:32777/el/blog/hello-world/' );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		$response = curl_exec( $ch );
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		$body = substr($response, $header_size);
		if ( $response === false ) {
			$error = curl_error( $ch );
			// write to db??
		} else {
			
			$output = $body;
		}
		curl_close( $ch );

eval(\Psy\sh());
}

    		
}



?>