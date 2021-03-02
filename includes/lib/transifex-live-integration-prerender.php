<?php

/**
 * Includes prerender support
 * @package TransifexLiveIntegration
 */

/**
 * Class integrates prerender service
 */
class Transifex_Live_Integration_Prerender {

	/**
	 * Url to prerender service
	 * @var string
	 */
	private $prerender_url;

	/**
	 * Overrides check for prerender header
	 * @var bool
	 */
	private $enable_prerender_check;
	private $prerender_enable_vary_header;
	private $prerender_vary_header_value;
	private $prerender_header_check_key;
	private $prerender_header_check_value;
	private $prerender_enable_cookie;
	private $prerender_cookie;
	private $prerender_enable_response_header;
	private $prerender_response_headers;
	private $generic_bot_types;
	private $whitelist_crawlers;

	/*
	 * Constructor
	 * @param string $prerender_url Url to prerender service
	 * @param bool $override_prerender_check Overrides check for prerender header
	 */

	public function __construct( $prerender_url, $enable_prerender_check, $settings ) {
		Plugin_Debug::logTrace();
		$this->prerender_url = rtrim( $prerender_url, '/' ) . '/';
		$this->enable_prerender_check = ($enable_prerender_check) ? true : false;
		$this->prerender_enable_vary_header = (isset( $settings['prerender_enable_vary_header'] )) ? true : false;
		$this->prerender_vary_header_value = $settings['prerender_vary_header_value'];
		$this->prerender_header_check_key = $settings['prerender_header_check_key'];
		$this->prerender_header_check_value = $settings['prerender_header_check_value'];
		$this->prerender_enable_response_header = (isset( $settings['prerender_enable_response_header'] )) ? true : false;
		$this->generic_bot_types = $settings['generic_bot_types'];
		$this->whitelist_crawlers = $settings['whitelist_crawlers'];

		$this->prerender_response_headers = [ ];
		if ( isset( $settings['prerender_response_headers'] ) ) {
			$this->prerender_response_headers = json_decode( stripslashes( $settings['prerender_response_headers'] ), true );
		}

		$this->prerender_enable_cookie = (isset( $settings['prerender_enable_cookie'] )) ? true : false;
		$this->prerender_cookie = [ ];
		if ( isset( $settings['prerender_cookie'] ) ) {
			$this->prerender_cookie = json_decode( stripslashes( $settings['prerender_cookie'] ), true );
		}
	}

	/*
	 * WP wp_head action, adds a 404 meta for prerender service
	 */

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

	function wp_headers_response_hook( $headers ) {
		Plugin_Debug::logTrace();
		$a = $this->prerender_response_headers;
		foreach ($a as $k => $v) {
			$headers[$k] = $v;
		}
		return $headers;
	}

	function wp_headers_vary_hook( $headers ) {
		Plugin_Debug::logTrace();
		$headers['Vary'] = $this->prerender_vary_header_value;
		return $headers;
	}

	/*
	 * WP wp_headers filter, adds headers for prerender request
	 */

	function wp_headers_prerender_hook( $headers ) {
		Plugin_Debug::logTrace();
		$headers[$this->prerender_header_check_key] = $this->prerender_header_check_value;
		return $headers;
	}

	function init_hook() {
		$a = $this->prerender_cookie;
		foreach ($a as $k => $v) {
			setcookie( $k, $v, DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		}
	}

	function ok_call_prerender() {
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-util.php';
		$agent = Transifex_Live_Integration_Util::get_user_agent();
		$req_escaped_fragment = (isset( $_GET['_escaped_fragment_'] )) ? true : false;
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/lib/transifex-live-integration-prerender.php';
		$check = Transifex_Live_Integration_Util::prerender_check( $agent, $req_escaped_fragment, $this->generic_bot_types, $this->whitelist_crawlers );
		return $check;
	}

	function ok_add_vary_header() {
		return ($this->prerender_enable_vary_header);
	}

	function ok_add_response_header() {
		return ($this->prerender_enable_response_header);
	}

	function ok_add_cookie() {
		return ($this->prerender_enable_cookie);
	}

	function call_curl( $url ) {
		$arr = [ ];
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
		curl_setopt( $ch, CURLOPT_HEADER, 1 );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 20 );
		$arr['url'] = $url;
		$arr['response'] = curl_exec( $ch );
		$arr['statuscode'] = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		$arr['header_size'] = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
		$arr['error'] = curl_error( $ch );
		curl_close( $ch );
		return $arr;
	}

	/*
	 * This aptly named filter function is used to make the prerender call,
	 * 		ideally it should be executed after the template render is finished but before sending to the browser
	 * 	@param string $buffer This is a very large string containing the entire page out
	 * @return string Returns the page buffer back to the browser
	 */

	function callback( $buffer ) {
		global $wp;
		$output = $buffer;

		# Try to get language code from the Transifex-Lang header.
		# This is used in the instance where subdomains have been configured
		# outside Wordpress so it does not have any knowledge of other
		# subdomains.
		$lang = urlencode($_SERVER['HTTP_X_TRANSIFEX_LANG']);

		$debug_html = '<!--' . "\n";
		$page_url = home_url( $wp->request );
		if ( !empty($lang) ) {
			$page_url = Transifex_Live_Integration_Util::replace_lang_subdomain($page_url, $lang);
		}
		if (preg_match("/^.*\.(js|css|xml|less|png|jpg|jpeg|gif|pdf|doc|txt|ico|rss|zip|mp3|rar|exe|wmv|doc|avi|ppt|mpg|mpeg|tif|wav|mov|psd|ai|xls|mp4|m4a|swf|dat|dmg|iso|flv|m4v|torrent|ttf|woff|svg|eot)$/i", $page_url)) {
			return $output;
		}
		$page_url = rtrim( $page_url, '/' ) . '/';
		if ( function_exists( 'curl_version' ) ) {
			$curl_response = $this->call_curl( $this->prerender_url . $page_url );
			$header = substr( $curl_response['response'], 0, $curl_response['header_size'] );
			$body = substr( $curl_response['response'], $curl_response['header_size'] );
			$header_lowercase = strtolower( $header );
			$header_prerender_check = (strpos( $header_lowercase, strtolower( $this->prerender_header_check_key ) )) ? true : false;
			$debug_html .= 'X-Prerender-Req Header check:' . $header_prerender_check . "\n";
			$debug_html .= 'Check enabled:' . $this->enable_prerender_check . "\n";
			if ( $header_prerender_check && $this->enable_prerender_check ) {
				$output = ($curl_response['response']) ? $body : $output;
				$debug_html .= 'Buffer swapped with prerender response.' . "\n";
			}
			$debug_html .= $curl_response['url'] . "\n";
			$debug_html .= $header . "\n";
			$debug_html .= $curl_response['error'] . "\n";
		} else {
			$debug_html .= 'Curl functions missing, skipping prerender call';
		}
		$output .= "\n$debug_html\n-->";
		return $output;
	}

	/*
	 * WP action hook that triggers the callback function
	 */

	function after_setup_theme_hook() {
		ob_start( [$this, 'callback' ] );
	}

	/*
	 * WP action hook that flushes the buffer and sends the page to the browser
	 */

	function shutdown_hook() {
		ob_end_flush();
	}

}
