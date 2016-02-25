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
	private $live_settings_keys = array( 'api_key');
	private $live_settings;
	private $is_detectlang;
	private $tx_langs;
	private $language_map;
	private $source_language;
	/**
	 * Public constructor, sets local settings
	 * @param array $live_settings Associative array of plugin settings.
	 */
	public function __construct( $settings, $is_detectlang ) {
		Plugin_Debug::logTrace();
		foreach ($this->live_settings_keys as $k) {
			$this->live_settings[$k] = $settings[$k];
		}
		$this->tx_langs = $settings['transifex_languages'];
		$this->is_detectlang = $is_detectlang;
		$this->language_map = json_decode( $settings['language_map'], true )[0];
		$this->source_language = $settings['source_language'];
	}

	/**
	 * Renders javascript includes in the page
	 */
	function render() {
		Plugin_Debug::logTrace();
		$this->is_detectlang ? Plugin_Debug::logTrace( "overriding detectlang" ) : Plugin_Debug::logTrace( "skipped detectlang override" );
		if ( $this->is_detectlang ) {
			$query_lang = get_query_var( 'lang' );
			if ($query_lang == $this->source_language) {
				$lang = $this->source_language;
			} else {
				$lang = array_search($query_lang,$this->language_map);
				if (!$lang) {
					Plugin_Debug::logTrace('javascript render failed could not find key');
					return false;
				}
			}
			$check_for_standard_lang = in_array( $lang, explode( ",", $this->tx_langs ) );
			Plugin_Debug::logTrace( $check_for_standard_lang ? "standard lang detected, skipping override" : "not standard lang, overriding" );
			if ( !$check_for_standard_lang ) {
				Plugin_Debug::logTrace( "Not a standard lang override" );
				$detectlang = <<<DETECTLANG
function() { return "$lang";}
DETECTLANG;
				$this->live_settings = array_merge( $this->live_settings, array( 'detectlang' => '%function%' ) );
			}
		}
		$live_settings_string = json_encode( $this->live_settings );
		if ( isset( $detectlang ) ) {
			$live_settings_string = str_replace( '"%function%"', $detectlang, $live_settings_string );
		}

		$include = <<<LIVE
<script type="text/javascript">window.liveSettings=$live_settings_string;</script>
<script type="text/javascript" src="//cdn.transifex.com/live.js"></script>\n
LIVE;
		echo $include;
	}

}
