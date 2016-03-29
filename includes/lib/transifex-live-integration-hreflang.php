<?php
include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/common/transifex-live-integration-common.php';
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

	private function generate_languages_hreflang( $raw_url, $languages,
			$language_map
	) {
		Plugin_Debug::logTrace();
		$url_map = Transifex_Live_Integration_Common::generate_language_url_map( $raw_url, $this->tokenized_url, $language_map );
		$ret = [ ];
		foreach ($languages as $language) {
			$arr = [ ];

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
		$url_path = add_query_arg( array(), $wp->request );
		$source_url_path = (substr( $url_path, 0, strlen( $lang ) ) === $lang) ? substr( $url_path, strlen( $lang ), strlen( $url_path ) ) : $url_path;
		$source = $this->settings['source_language'];
		$unslashed_source_url = site_url() . $source_url_path;
		$source_url = rtrim( $unslashed_source_url, '/' ) . '/';
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