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
	private $override_prerender_check;

	/*
	 * Constructor
	 * @param string $prerender_url Url to prerender service
	 * @param bool $override_prerender_check Overrides check for prerender header
	 */

	public function __construct( $prerender_url, $override_prerender_check ) {
		Plugin_Debug::logTrace();
		$this->prerender_url = $prerender_url;
		$this->override_prerender_check = ($override_prerender_check) ? true : false;
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

	/*
	 * WP wp_headers filter, adds a prerender header
	 */

	function wp_headers_hook( $headers ) {
		Plugin_Debug::logTrace();
		$headers['X-PreRender-Req'] = 'TRUE';
		return $headers;
	}


	function call_curl($url) {
		$arr = [];
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
		curl_setopt( $ch, CURLOPT_HEADER, 1 );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT ,5); 
		curl_setopt( $ch, CURLOPT_TIMEOUT, 7);
		$arr['response'] = curl_exec( $ch );
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
		$debug_html = '<!--'."\n";
		$page_url = home_url( $wp->request );
		$page_url = rtrim( $page_url, '/' ) . '/';
		if (function_exists('curl_version')) {
			$curl_response = $this->call_curl($this->prerender_url . $page_url);
			$header = substr( $curl_response['response'], 0, $curl_response['header_size'] );
			$body = substr( $curl_response['response'], $curl_response['header_size'] );
			$header_lowercase = strtolower($header);
			if ( strpos( $header_lowercase, 'x-prerender-req: true' ) || $this->override_prerender_check ) {
				$output = ($curl_response['response'])?$body:$output;
			}
			$debug_html .= $header.'\n';
			$debug_html .= $curl_response['response']."\n";
			$debug_html .= $curl_response['error']."\n";
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
