<?php

/**
 * Includes Transifex Live javascript snippet
 * @package TransifexLiveIntegration
 */

/**
 * Class that creates javascript snippet based on settings
 */
class Transifex_Live_Integration_Javascript {

	/**
	 * Stores current plugin settings.
	 * @var array
	 */
	private $live_settings;
	private $lang;
	private $source_language;
	private $language_map;

	/**
	 * Public constructor, sets local settings
	 * @param array $settings Associative array of plugin settings.
	 */
	public function __construct( $settings ) {
		Plugin_Debug::logTrace();
		$this->live_settings['api_key'] = $settings['api_key'];
		$this->lang = false;
		$this->source_language = $settings['source_language'];
		$this->language_map = $settings['language_map'];
	}

	function wp_hook() {
		Plugin_Debug::logTrace();
		$this->lang = self::lang_check(
						get_query_var( 'lang' ), $this->source_language, $this->language_map
		);
	}

	static function lang_check( $query_var, $source_language, $language_map ) {
		Plugin_Debug::logTrace();
		if ( !isset( $query_var ) || !isset( $source_language ) || !isset( $language_map ) ) {
			Plugin_Debug::logTrace( 'lang_check params not set, defaulting to native lang detection' );
			return false;
		}

		$lm = json_decode( $language_map, true )[0];
		$lang = false;
		if ( $query_var == $source_language ) {
			$lang = $source_language;
			Plugin_Debug::logTrace( 'lang is source, overriding live with source' );
		} else {
			$lang = array_search( $query_var, $lm );
			if ( $lang ){
				Plugin_Debug::logTrace( 'lang is set, overriding live detection' );
			} else {
				Plugin_Debug::logTrace( 'lang missing, defaulting to native detection' );
				$lang = false;
			}
		}
		return $lang;
	}

	/**
	 * Renders javascript includes in the page
	 */
	function wp_head_hook() {
		Plugin_Debug::logTrace();
		$lang = $this->lang;
		$live_settings = $this->live_settings;
		if ( $lang ) {
			$detectlang = <<<DETECTLANG
function() { return "$lang";}
DETECTLANG;
			$live_settings = array_merge( $live_settings, ['detectlang' => '%function%' ] );
			$live_settings_json = json_encode( $live_settings );
			$live_settings_string = str_replace( '"%function%"', $detectlang, $live_settings_json );
		} else {
			$live_settings_string = json_encode( $live_settings );
		}

		$snippet = <<<SNIPPET
<script type="text/javascript">window.liveSettings=$live_settings_string;</script>
<script type="text/javascript" src="//cdn.transifex.com/live.js"></script>\n
SNIPPET;
		echo $snippet;
	}

}
