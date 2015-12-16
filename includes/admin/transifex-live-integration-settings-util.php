<?php

class Transifex_Live_Integration_Settings_Util {
	
	static function get_live_languages_list( $api_key ){
		Plugin_Debug::logTrace();
		$languages_json_format = "https://cdn.transifex.com/%s/latest/languages.jsonp";
		$request_url = sprintf($languages_json_format, $api_key);
		Plugin_Debug::logTrace($request_url);
		$response = wp_remote_get($request_url);
		$response_code = wp_remote_retrieve_response_code( $response );
		Plugin_Debug::logTrace($response_code);
		$api_response = wp_remote_retrieve_body( $response );
		Plugin_Debug::logTrace($api_response);
		$languages_arr = "{en}";
		// convert JSON to string array
		return $languages_arr;
	}

	static function sanitize_list( $list ) {
		Plugin_Debug::logTrace();
		$list_arr = explode( ',', $list );

		if ( empty( $list_arr ) ) {
			'';
		}

		for ($i = 0; $i < count( $list_arr ); $i++) {
			$list_arr[$i] = sanitize_html_class( $list_arr[$i] );
		}

		$list_arr = array_filter( $list_arr );
		return implode( ',', $list_arr );
	}

	static function sanitize_hex_color( $color ) {
		Plugin_Debug::logTrace();

		if ( '' === $color )
			return '';

		// 3 or 6 hex digits, or the empty string.
		if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) )
			return $color;

		return null;
	}

	static function color_picker( $name, $id, $value ) {
		Plugin_Debug::logTrace();
		$header_name = esc_html( $name );
		$input_name = esc_attr( $id );
		$input_id = esc_attr( str_replace( array( '[', ']' ), array( '_', '' ), $id ) );
		$input_value = esc_attr( $value );
		$div_id = 'colorPickerDiv_' . esc_attr( $id );
		$picker = <<<PICKER
                <div class="color-box"><strong>$header_name</strong>
			<input name="$input_name" id="$input_id" type="text" value="$input_value" class="colorpick" />
                        <div id="$div_id" class="colorpickdiv"></div>
		</div>
PICKER;
		echo $picker;
	}

}
