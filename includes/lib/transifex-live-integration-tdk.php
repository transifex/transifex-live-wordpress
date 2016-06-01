<?php

/**
 * TDK Template Designer Kit
 * @package TransifexLiveIntegration
 */

/**
 * Interfaces for TDK Interfaces functions
 */
interface Transifex_Live_Integration_Rewrite_Interface {	
	public function get_language_url( $atts );
	public function detect_language();
	public function is_language($atts);
}