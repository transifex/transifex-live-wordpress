<?php

class Transifex_Live_Integration_Prerender {

	public function __construct() {
		Plugin_Debug::logTrace();
	}

	static public function is_whitelist_name( $agent, $whitelist_names ) {
		return !empty( $agent ) ? (preg_match( "/{$whitelist_names}/", $agent ) > 0) : false;
	}

	static public function is_bot_type( $agent, $bot_types ) {
		return !empty( $agent ) ? (preg_match( "/{$bot_types}/", $agent ) > 0) : false;
	}

	static public function prerender_check( $req_user_agent, $req_escaped_fragment,
			$bot_types, $whitelist_names ) {
		$bool = self::is_bot_type( $req_user_agent, $bot_types );

		$bool = ($bool) ? true : self::is_whitelist_name( $req_user_agent, $whitelist_names );

		$bool = ($bool) ? true : ($req_escaped_fragment) ? true : false;

		$bool = (strpos( strtolower( $req_user_agent ), 'prerender' )) ? false : $bool;
		return $bool;
	}

	static function create_prerender( $settings ) {
		Plugin_Debug::logTrace();
		$req_user_agent = $_SERVER["HTTP_USER_AGENT"];
		$req_escaped_fragment = (isset( $_GET['_escaped_fragment_'] )) ? $_GET['_escaped_fragment_'] : false;

		$check = self::prerender_check( $req_user_agent, $req_escaped_fragment, $settings['generic_bot_types'], $settings['whitelist_crawlers'] );
		return ($check) ? new Transifex_Live_Integration_Prerender( ) : false;
	}

	function callback( $buffer ) {

		global $wp;
		$output = $buffer;
		$page_url = home_url( $wp->request );
		$page_url = rtrim( $page_url, '/' ) . '/';

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, 'http://192.168.99.100:32769/' . $page_url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$result = curl_exec( $ch );
		if ( $result === false ) {
			$error = curl_error( $ch );
			// write to db??
		} else {
			$output = $result;
		}
		curl_close( $ch );
		return $output;
	}

	function after_setup_theme_hook() {
		ob_start( [$this, 'callback' ] );
	}

	function shutdown_hook() {
		ob_end_flush();
	}

}
