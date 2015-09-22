<?php

class Transifex_Live_Integration_Settings_Util {

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
