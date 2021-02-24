<?php

/**
 * Common util functions
 * @package TransifexLiveIntegration
 */
/*
 * Common util
 */
class Transifex_Live_Integration_Util {

	/**
	 * Callback function for query_vars action
	 * @param array $vars list of vars passed from WP.
	 * @return array filtered vars
	 */
	static function query_vars_hook( $vars ) {
		Plugin_Debug::logTrace();
		$vars[] = 'lang';
		return $vars;
	}

	static function get_http_requested_protocol() {
		Plugin_Debug::logTrace();
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
	}

	/*
	 * Checks if an agent is on the whitelist
	 * @param string $agent User agent to search against
	 * @param string $whitelist_names A regex list of whitelisted agents
	 * @return bool Returns true if there is a match, false otherwise
	 */

	static public function is_whitelist_name( $agent, $whitelist_names ) {
		Plugin_Debug::logTrace();
		return !empty( $agent ) ? (preg_match( "/{$whitelist_names}/i", $agent ) > 0) : false;
	}

	/*
	 * Gets the user agent from PHP Server object
	 * @return string/false Returns the user agent string or false if not set
	 */

	static function get_user_agent() {
		Plugin_Debug::logTrace();
		return (isset( $_SERVER['HTTP_USER_AGENT'] )) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : false;
	}

	/*
	 * Checks if an agent is on the bot type list
	 * @param string $agent User agent to search against
	 * @param string $bot_types A regex list of bot types
	 * @return bool Returns true if there is a match, false otherwise
	 */

	static function is_bot_type( $agent, $bot_types ) {
		Plugin_Debug::logTrace();
		return !empty( $agent ) ? (preg_match( "/{$bot_types}/i", $agent ) > 0) : false;
	}

	/*
	 * Checks to see if the request is from prerender
	 * @param string $agent user agent string
	 * @return bool If the user agent contains prerender
	 */

	static function is_prerender_req( $agent ) {
		Plugin_Debug::logTrace();
		$ret = (strpos( $agent, 'prerender' )) ? true : false;
		return $ret;
	}

	/*
	 * This function is the core backend check to determine if user agent should be prerender'd
	 *
	 * @param string $req_user_agent User agent string, generally from browser
	 * @param string $req_escaped_fragment Escaped fragment string, generally from browser
	 * @param string $bot_types A regex string list of bot keywords for quick matching
	 * @param string $whitelist_names A regex string list of whitelisted bots
	 * @return bool Checks a given user agent for bot-ability
	 */

	static function prerender_check( $req_user_agent, $req_escaped_fragment,
			$bot_types, $whitelist_names
	) {
		Plugin_Debug::logTrace();

		$bot_types_escaped = addcslashes( $bot_types, '/' );
		$whitelist_names_escaped = addcslashes( $whitelist_names, '/' );

		$is_bot = self::is_bot_type( $req_user_agent, $bot_types_escaped );
		$is_whitelisted = ($is_bot) ? true : self::is_whitelist_name( $req_user_agent, $whitelist_names_escaped );
		$has_escaped_fragment = ($is_whitelisted) ? true : (($req_escaped_fragment) ? true : false);
		$prerender_ok = ($has_escaped_fragment) ? true : self::is_prerender_req( self::get_user_agent() );

		return $prerender_ok;
	}


	/**
	* @param $page_url
	* @param $lang
	*
	* @return mixed
	*/
	static function replace_lang_subdomain($page_url, $lang){
		$url_parts = parse_url($page_url);
		if (isset($url_parts['host'])) {
			$host = $url_parts['host'];
			$dots = explode('.', $host);
			if ($dots[0] === $lang){
				return $page_url;
			}
			if ($dots[0] === "www") {
				$dots[0] = $lang;
			} else {
				array_unshift($dots, $lang);
			}
			$host = implode('.', $dots);
			$url_parts['host'] = $host;
			$page_url = Transifex_Live_Integration_Util::unparse_url($url_parts);
		}
		return $page_url;
	}

	/*
	 * Reconstruct a parsed URL.
	 * @param string $parsed_url The URL parsed by parse_url
	 * @return string The final URL as a string
	 */

	static function unparse_url($parsed_url) {
		$scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
		$host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
		$port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
		$user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
		$pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
		$pass     = ($user || $pass) ? "$pass@" : '';
		$path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
		$query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
		$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
		return "$scheme$user$pass$host$port$path$query$fragment";
	}

}
