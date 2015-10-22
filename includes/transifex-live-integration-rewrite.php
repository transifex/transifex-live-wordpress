<?php

/**
 * Language rewrites
 * @package TransifexLiveIntegration
 */

/**
 * Static class for settings defaults
 * Experimental turned off in production
 */
class Transifex_Live_Integration_Rewrite {

	/**
	 * Determines whether to display CSS
	 * @var boolean
	 */
	private $rewrites_ok;
	private $source_language;
	private $language_codes;

	/**
	 * Public constructor, initializes local vars based on settings
	 * @param array $settings Associative array used to store plugin settings.
	 */
	public function __construct( $settings ) {
		Plugin_Debug::logTrace();
		if ( isset( $settings['enable_language_urls'] ) && $settings['enable_language_urls'] ) {
			$this->rewrites_ok = true;
			$this->source_language = $settings['source_language'];
			$b = strpos( ",", $settings['language_codes'] );
			if ( $b === false ) {
				$this->language_codes = array( $settings['language_codes'] );
			} else {
				$this->language_codes = explode( ",", $settings['language_codes'] );
			}
		} else {
			$this->rewrites_ok = false;
		}
	}
	
	function is_enabled() {
		return false;
//TODO future feature turned off
//		return $this->rewrites_ok;
	}

	function add_rewrites( $rules ) {

	//	if ( !$this->rewrites_ok ) {
		if (false){
			return $rules;
		}

		$newRules = array();
		$lang_prefix = "([a-z]{2,2}(\-[a-z]{2,2})?)/";

		$lang_parameter = "&" . LANG_PARAM . '=$matches[1]';


		$newRules[$lang_prefix . "?$"] = "index.php?lang=\$matches[1]";
		foreach ($rules as $key => $value) {
			$original_key = $key;
			$original_value = $value;

			$key = $lang_prefix . $key;


			for ($i = 6; $i > 0; $i--) {
				$value = str_replace( '[' . $i . ']', '[' . ($i + 2) . ']', $value );
			}

			$value .= $lang_parameter;


			$newRules[$key] = $value;
			$newRules[$original_key] = $original_value;
		}


		return $newRules;
	}

}
