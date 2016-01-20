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

	/**
	 * Public constructor, sets the settings
	 * @param array $settings Associative array used to store plugin settings.
	 */
	public function __construct( $settings ) {
		Plugin_Debug::logTrace();
		$this->settings = $settings;
	}

	public function ok_to_add() {
		if ( ! isset( $this->settings['api_key'] ) ){
			Plugin_Debug::logTrace( 'settings[api_key] not set...skipping hreflang' );
			return false;
		}
		if ( ! isset( $this->settings['languages'] ) ) {
			Plugin_Debug::logTrace( 'settings[languages] not set...skipping hreflang' );
			return false;
		}
		if ((! isset ($this->settings['add_rewrites_date'])) && ( ! isset ($this->settings['add_rewrites_page'])) &&
				(! isset ($this->settings['add_rewrites_author'])) && (! isset ($this->settings['add_rewrites_tag']))
				&& (! isset ($this->settings['add_rewrites_category'])) && (! isset ($this->settings['add_rewrites_search']))
				&& (! isset ($this->settings['add_rewrites_feed'])) && (! isset ($this->settings['add_rewrites_root']))
				&& (! isset ($this->settings['add_rewrites_post'])) ){
			Plugin_Debug::logTrace( 'no rewrite option set...skipping hreflang' );
			return false;
		}
		return true;
	}

	/**
	 * Renders HREFLANG list
	 */
	public function render_hreflang() {
		Plugin_Debug::logTrace();
		global $wp;
		$raw_url = home_url( $wp->request );
		if ('/' !== substr($raw_url, -1)) {
			$raw_url = $raw_url.'/';
		}
		if ($this->settings['source_language'] == get_query_var( 'lang')  ) {
			$array_url = explode("/",$raw_url);
			$array_url[2] = get_query_var( 'lang') . '/' . $array_url[2];
			$raw_url = implode('/',$array_url);
		}
		$base_url = str_replace( '/'.get_query_var( 'lang') , "", $raw_url);
		$token_url = str_replace( get_query_var( 'lang') , "%lang%", $raw_url);
		$source = $this->settings['source_language'];
		$hreflang = <<<SOURCE
		<link rel="alternate" href="$base_url" hreflang="$source"/>		
SOURCE;
		$a = $this->settings['transifex_languages'];

		$y = json_decode( html_entity_decode( $this->settings['languages_map'] ), true );
		$pp = $token_url;
		$xa = explode( ",", $a );
		foreach ($xa as $i) {
			$u = $y[$i];
			$s = str_replace( '%lang%', $u, $pp );
			$hreflang .= <<<HREFLANG
				<link rel="alternate" href="$s" hreflang="$i"/>
HREFLANG;
		}
		echo $hreflang;
		return true;
	}

}

?>