<?php

class Transifex_Live_Integration_Util {

	/**
	 * Callback function for query_vars action
	 * @param array $vars list of vars passed from WP.
	 */
	static function query_vars_hook( $vars ) {
		Plugin_Debug::logTrace();
		$vars[] = 'lang';
		return $vars;
	}

	static public function is_whitelist_name( $agent, $whitelist_names ) {
		Plugin_Debug::logTrace();
		return !empty( $agent ) ? (preg_match( "/{$whitelist_names}/i", $agent ) > 0) : false;
	}

	static function get_user_agent() {
		Plugin_Debug::logTrace();
		return (isset( $_SERVER['HTTP_USER_AGENT'] )) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : false;
	}

	static function is_bot_type( $agent, $bot_types ) {
		Plugin_Debug::logTrace();
		return !empty( $agent ) ? (preg_match( "/{$bot_types}/i", $agent ) > 0) : false;
	}

	static function is_prerender_req( $agent ) {
		Plugin_Debug::logTrace();
		$ret = (strpos( $agent, 'prerender' )) ? true : false;
		return $ret;
	}

	static function prerender_check( $req_user_agent, $req_escaped_fragment,
			$bot_types, $whitelist_names
	) {
		Plugin_Debug::logTrace();

		$bot_types_escaped = addcslashes( $bot_types, '/' );
		$whitelist_names_escaped = addcslashes( $whitelist_names, '/' );

		$is_bot = self::is_bot_type( $req_user_agent, $bot_types_escaped );
		$is_whitelisted = ($is_bot) ? true : self::is_whitelist_name( $req_user_agent, $whitelist_names_escaped );
		$has_escaped_fragment = ($is_whitelisted) ? true : ($req_escaped_fragment) ? true : false;
		$prerender_ok = ($has_escaped_fragment) ? true : self::is_prerender_req( self::get_user_agent() );

		return $prerender_ok;
	}

}
