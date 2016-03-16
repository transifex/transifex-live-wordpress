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
	private $tokenized_url;

	/**
	 * Public constructor, sets the settings
	 * @param array $settings Associative array used to store plugin settings.
	 */
	public function __construct( $settings ) {
		Plugin_Debug::logTrace();
		$this->settings = $settings;
		$this->language_map = json_decode( $settings['language_map'], true )[0];
		$this->languages = json_decode( $settings['transifex_languages'], true );
		$this->tokenized_url = $settings['tokenized_url'];
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
		if ( !isset( $this->settings['tokenized_url'] ) ) {
			Plugin_Debug::logTrace( 'settings[tokenized_url] not set...skipping hreflang' );
			return false;
		}
		return true;
	}

	private function generate_languages_hreflang( $raw_url, $languages, $language_map ) {
		Plugin_Debug::logTrace();
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-picker.php';
		$url_map = Transifex_Live_Integration_Picker::generate_language_url_map( $raw_url, $this->tokenized_url, $language_map );
		$ret = [ ];
	//	$tokenized_url = str_replace( $lang, "%lang%", $raw_url, $count );
			foreach ($languages as $language) {
				$arr = [ ];
		//		$hreflang_code = $language_map[$language];
		//		$language_url = str_replace( '%lang%', $hreflang_code, $tokenized_url );
				
				$arr['href'] = $url_map[$language];
				$arr['hreflang'] = $language_map[$language];
				array_push( $ret, $arr );
			}
		return $ret;
	}

	/**
	 * Renders HREFLANG list
	 */
	public function render_hreflang() {
		Plugin_Debug::logTrace();
		global $wp;
		$lang = get_query_var( 'lang' );
		$raw_url = home_url( $wp->request );
		$url_path = add_query_arg(array(), $wp->request);
		$source_url_path = (substr( $url_path, 0, count($lang)-1 ) === $lang)?substr( $url_path, count($lang)-1, count($url_path)-1 ):$url_path;
		
		$source = $this->settings['source_language'];
		$source_url = site_url() + $source_url_path;
		$hreflang_out = '';
		$hreflang_out .= <<<SOURCE
<link rel="alternate" href="$source_url" hreflang="$source"/>\n		
SOURCE;
		$hreflangs = $this->generate_languages_hreflang( $source_url_path, $this->languages, $this->language_map );
		foreach ($hreflangs as $hreflang) {
			$href_attr = $hreflang['href'];
			$hreflang_attr = $hreflang['hreflang'];
			$hreflang_out .= <<<HREFLANG
<link rel="alternate" href="$href_attr" hreflang="$hreflang_attr"/>\n
HREFLANG;
		}
		echo $hreflang_out;
		return true;
	}

}

?>