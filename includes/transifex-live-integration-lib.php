<?php

class Transifex_Live_Integration_Lib {

	/**
	 * Hex to RGB
	 *
	 * @access public
	 * @param string $hex
	 * @return array $rgb
	 * Credit: c.bavota (http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/)
	 */
	static public function hex2rgb( $hex ) {
		Plugin_Debug::logTrace();
		$hex = str_replace( '#', '', $hex );
		if ( strlen( $hex ) == 3 ) {
			$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}
		$rgb = array( $r, $g, $b );
		return $rgb; // returns an array with the rgb values
	}

	/**
	 * Enqueue inline CSS. @see wp_enqueue_style().
	 * 
	 * 
	 * @param string      $handle    Identifying name for script
	 * @param string      $src       The JavaScript codez
	 * 
	 * @return null
	 */
	static function enqueue_inline_styles( $handle, $js ) {
		$cb = function()use( $handle, $js ) {
			if ( wp_script_is( $handle, 'done' ) )
				return;
			echo "\n$js\n";
			global $wp_styles;
			$wp_styles->done[] = $handle;
		};
		$hook = 'wp_print_styles';
		add_action( $hook, $cb );
	}

}
