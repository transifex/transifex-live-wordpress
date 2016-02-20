<?php

/**
 * Includes hreflang tag attribute on each page containing url rewrites
 * @package TransifexLiveIntegration
 */

/**
 * Class that renders hreflang
 */
class Transifex_Live_Integration_Hreflang {

	/**
	 * Copy of current plugin settings
	 * @var settings array
	 */
	private $settings;
	
	private $language_map;
	
	private $languages;

	/**
	 * Public constructor, sets the settings
	 * @param array $settings Associative array used to store plugin settings.
	 */
	public function __construct( $settings ) {
		Plugin_Debug::logTrace();
		$this->settings = $settings;
		$this->language_map = json_decode( stripslashes( $settings['language_map'] ), true );
		$this->languages = json_decode( stripslashes( $settings['transifex_languages']), true );
	}

	public function ok_to_add() {
		if ( !isset( $this->settings['api_key'] ) ) {
			Plugin_Debug::logTrace( 'settings[api_key] not set...skipping hreflang' );
			return false;
		}
		if ( !isset( $this->settings['languages'] ) ) {
			Plugin_Debug::logTrace( 'settings[languages] not set...skipping hreflang' );
			return false;
		}
		if ( $this->settings['url_options'] === '1' ) {
			Plugin_Debug::logTrace( 'settings[url_options] set to none...skipping hreflang' );
			return false;
		}
		return true;
	}

	private function generate_languages_hreflang( $raw_url, $languages, $lang,
			$language_map ) {
		Plugin_Debug::logTrace();
		$ret = [ ];
		$tokenized_url = str_replace( $lang, "%lang%", $raw_url, $count );
		if ( $count !== 0 ) {
			foreach ($languages as $language) {
				$arr = [ ];
				$hreflang_code = $language_map[$language];
				$language_url = str_replace( '%lang%', $hreflang_code, $tokenized_url );
				$arr['href'] = $language_url;
				$arr['hreflang'] = $hreflang_code;
				array_push( $ret, $arr );
			}
		}
		return $ret;
	}

	/**
	 * Renders HREFLANG list
	 */
	public function render_hreflang() {
		Plugin_Debug::logTrace();
		global $wp;
		$raw_url = home_url( $wp->request );
		if ( '/' !== substr( $raw_url, -1 ) ) {
			$raw_url = $raw_url . '/';
		}
		$base_url = $raw_url;
		if ( $this->settings['source_language'] === get_query_var( 'lang' ) ) {
			$array_url = explode( "/", $raw_url );
			if ( $this->settings['url_options'] === '3' ) {
				array_pop( $array_url );
				array_push( $array_url, get_query_var( 'lang' ) );
				array_push( $array_url, '' );
			}
			if ( $this->settings['url_options'] === '2' ) {
				$array_domain = explode( ".", $array_url[2] );
				$array_domain[0] = get_query_var( 'lang' );
				$array_url[2] = implode( '.', $array_domain );
			}

			$raw_url = implode( '/', $array_url );
		} else {
			$base_url = str_replace( '/' . get_query_var( 'lang' ), "", $raw_url );
		}
		$source = $this->settings['source_language'];
		$hreflang_out = '';
		$hreflang_out .= <<<SOURCE
		<link rel="alternate" href="$base_url" hreflang="$source"/>		
SOURCE;
		$languages = explode( ",", $this->languages );
		$lang = get_query_var( 'lang' );
		$hreflangs = $this->generate_languages_hreflang( $raw_url, $languages, $lang, $this->language_map );
		foreach ($hreflangs as $hreflang) {
			$href_attr = $hreflang['href'];
			$hreflang_attr = $hreflang['hreflang'];
			$hreflang_out .= <<<HREFLANG
				<link rel="alternate" href="$href_attr" hreflang="$hreflang_attr"/>
HREFLANG;
		}
		echo $hreflang_out;
		return true;
	}

}

?>