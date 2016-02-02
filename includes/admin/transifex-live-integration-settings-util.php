<?php

class Transifex_Live_Integration_Settings_Util {

	const EMPTY_TRANSIFEX_LANGUAGES_PATTERN = '/^transifex_languages\(\{\"timestamp\":\".*\"\}\);/';

	/**
	 * Function to retrieve transifex_languages javascript
	 * @param string $api_key API key entered by user.
	 */
	static function get_raw_transifex_languages( $api_key ) {
		Plugin_Debug::logTrace();

		// TODO: move this url to the plugin constants.
		$languages_json_format = "https://cdn.transifex.com/%s/latest/languages.jsonp";
		$request_url = sprintf( $languages_json_format, $api_key );
		$response = wp_remote_get( $request_url ); // TODO: switch to vip_safe_wp_remote_get.
		$response_body = null;
		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 == $response_code ) {
			$response_body = wp_remote_retrieve_body( $response );
			if ( preg_match( self::EMPTY_TRANSIFEX_LANGUAGES_PATTERN, $response_body ) ) {
				Plugin_Debug::logTrace( "empty transifex languages file...skipping" );
				return false;
			}
			return $response_body;
		}
		Plugin_Debug::logTrace( "did not get a 200 getting transifex languages" );
		return false;
	}

	/**
	 * Function to validate transifex_languages javascript
	 * @param string $api_key API key entered by user.
	 * @param string $raw_transifex_languages string to compare.
	 */
	static function check_raw_transifex_languages( $api_key,
			$raw_transifex_languages ) {
		Plugin_Debug::logTrace();
		$s = self::get_raw_transifex_languages( $api_key );
		return strcmp( $s, $raw_transifex_languages ) === 0 ? true : false;
	}

	/**
	 * Function to parse out languages array
	 * @param string $raw_transifex_languages string to parse.
	 */
	static function get_default_languages( $raw_transifex_languages ) {
		Plugin_Debug::logTrace();
		$reg = "/\s*transifex_languages\(\s*(.+?)\s*\);/";
		preg_match( $reg, $raw_transifex_languages, $m );
		$tl_array = json_decode( $m[1], true );
		$tl_t_array = $tl_array['translation'];
		if (!is_array($tl_t_array)) {
			return null;
		}
		$language_array = array_column( $tl_t_array, 'code' );
		if ( isset( $language_array ) ) {
			return $language_array;
		} else {
			return null;
		}
	}

	/**
	 * Function to parse out an assoc array of language mapping
	 * @param string $raw_transifex_languages string to parse.
	 */
	static function get_language_lookup( $raw_transifex_languages ) {
		Plugin_Debug::logTrace();
		$reg = "/\s*transifex_languages\(\s*(.+?)\s*\);/";
		preg_match( $reg, $raw_transifex_languages, $m );
		$tl_array = json_decode( $m[1], true );
		$tl_t_array = $tl_array['translation'];
		$f = function( $x ) {
			return ['code' => $x['code'], 'name' => $x['tx_name'] ];
		};
		if (!is_array($tl_t_array)) {
			return null;
		}
		$language_array = array_map( $f, $tl_t_array );
		if ( isset( $language_array ) ) {
			return $language_array;
		} else {
			return null;
		}
	}

	/**
	 * Function to parse out source language
	 * @param string $raw_transifex_languages string to parse.
	 */
	static function get_source( $raw_transifex_languages ) {
		Plugin_Debug::logTrace();
		$reg = "/\s*transifex_languages\(\s*(.+?)\s*\);/";
		preg_match( $reg, $raw_transifex_languages, $m );
		$tl_array = json_decode( $m[1], true );
		$tl_s_array = $tl_array['source'];
		$source_string = $tl_s_array['code'];
		if ( isset( $source_string ) ) {
			return $source_string;
		} else {
			return null;
		}
	}

	/**
	 * Type checking function for lists
	 * @param array $list array to sanitize.
	 *
	 * TODO This feels like wheel re-invention...look for a library
	 */
	static function sanitize_list( $list ) {
		Plugin_Debug::logTrace();
		$list_arr = explode( ',', $list );

		if ( empty( $list_arr ) ) {
			'';
		}

		$count = count( $list_arr );
		for ($i = 0; $i < $count; $i++) {
			$list_arr[$i] = sanitize_html_class( $list_arr[$i] );
		}

		$list_arr = array_filter( $list_arr );
		return implode( ',', $list_arr );
	}
	
	static function render_source_language( $source_language ) {
		$html = '';
		if (empty($source_langauge)) {
			$html = "No source language published!!!! Please please please publish something!!!!";
		} else {
		$html .= <<<HTML_TEMPLATE
		<input type="hidden" value="<?php echo $source_language ?>" name="transifex_live_settings[source_language]" id="transifex_live_settings_source_language" />
HTML_TEMPLATE;
		}
		}

	static function render_languages( $languages ) {

		$html = '<div id="transifex_live_languages">';
		if (empty($languages)) {
			$html .= '<span id="transifex_live_languages_message">No languages published!!!! Please please please publish something!!!!</span>';
		}
		foreach ($languages as $language) {
			$name = $language['name'];
			$code = $language['code'];
			$value = (isset( $settings['wp_language_' . $language['code']] )) ? $settings['wp_language_' . $language['code']] : $language['code'];
			$html .= <<<HTML_TEMPLATE
			<input disabled="true" type="text" class="regular-text" style="width:200px" name="transifex_live_settings[tx_language_$code]" value="$name" />
            <input type="text" name="transifex_live_settings[wp_language_$code]" id="transifex_live_settings_wp_language_$code" value="$value" class="regular-text">
            <br/>
HTML_TEMPLATE;
		}
		echo $html.'</div>';
	}

	static function render_url_options( $options ) {
		$html = '';

		$i = 0;
		foreach ($options as $option) {
			ob_start();
			checked( $option['checked'], '1' );
			$checked = ob_get_clean();

			$text = $option['text'];
			$id = $option['id'];
			$name = $option['name'];
			$html .= <<<HTML
		<input class="all_selector" type="checkbox" id="$id" name="$name" value="1" $checked>$text
HTML;
			if ( $i % 1 == 0 ) {
				$html .= <<<NEWLINE
				<br/>
NEWLINE;
			}
			$i++;
		}
		echo $html;
	}
}
