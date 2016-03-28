<?php

class Transifex_Live_Integration_Prerender {

	public function __construct() {
		Plugin_Debug::logTrace();
	}

	static public function is_whitelist_name( $agent, $whitelist_names ) {
		Plugin_Debug::logTrace();
		return !empty( $agent ) ? (preg_match( "/{$whitelist_names}/i", $agent ) > 0) : false;
	}

	static public function is_bot_type( $agent, $bot_types ) {
		Plugin_Debug::logTrace();
		return !empty( $agent ) ? (preg_match( "/{$bot_types}/i", $agent ) > 0) : false;
	}

	static public function is_prerender_req() {
		$req_user_agent = (isset( $_SERVER['HTTP_USER_AGENT'] )) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : false;
		$ret = (strpos( $req_user_agent, 'prerender' )) ? true : false;
		Plugin_Debug::logTrace( $req_user_agent );
		Plugin_Debug::logTrace( $ret );
		return $ret;
	}

	function wp_head_hook() {
		Plugin_Debug::logTrace();
		$status = '';
		if ( is_404() ) {
			$status .= <<< STATUS
<meta name="prerender-status-code" content="404">\n
STATUS;
		}
		echo $status;
	}

	function wp_headers_hook( $headers ) {
		Plugin_Debug::logTrace();
		$headers['X-PreRender-Req'] = 'TRUE';
		return $headers;
	}

	static public function prerender_check( $req_user_agent, $req_escaped_fragment,
			$bot_types, $whitelist_names ) {
		Plugin_Debug::logTrace();

		$bot_types_escaped = addcslashes( $bot_types, '/' );
		$whitelist_names_escaped = addcslashes( $whitelist_names, '/' );

		$is_bot = self::is_bot_type( $req_user_agent, $bot_types_escaped );
		$is_whitelisted = ($is_bot) ? true : self::is_whitelist_name( $req_user_agent, $whitelist_names_escaped );
		$has_escaped_fragment = ($is_whitelisted) ? true : ($req_escaped_fragment) ? true : false;
		$prerender_ok = ($has_escaped_fragment) ? true : self::is_prerender_req();

		return $prerender_ok;
	}

	static function create_prerender( $settings ) {
		Plugin_Debug::logTrace();
		$req_user_agent = (isset( $_SERVER['HTTP_USER_AGENT'] )) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : false;
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
		curl_setopt( $ch, CURLOPT_URL, 'http://secure-refuge-63401.herokuapp.com/' . $page_url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
		curl_setopt( $ch, CURLOPT_HEADER, 1 );
		$response = curl_exec( $ch );
		$header_size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
		$header = substr( $response, 0, $header_size );
		$body = substr( $response, $header_size );
		if ( $response === false ) {
			$error = curl_error( $ch );
			// write to db??
		} else {
			if ( strpos( $header, 'X-PreRender-Req: TRUE' ) ) {
				$output = $body;
			}
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
