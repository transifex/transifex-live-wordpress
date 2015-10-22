<?php
/**
 * Common libraries
 * @package TransifexLiveIntegration
 */

/**
 * Common PHP Libraries from other sources
 */
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


}
