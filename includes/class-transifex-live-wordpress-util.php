<?php

/**
 * Utility class for stand-alone helper functions
 *
 * @link       http://docs.transifex.com/developer/integrations/wordpress
 *
 * @package    Transifex_Live_Wordpress
 * @subpackage Transifex_Live_Wordpress/util
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Transifex_Live_Wordpress
 * @subpackage Transifex_Live_Wordpress/util
 */
class Transifex_Live_Wordpress_Util {

    /**
     * Hex to RGB
     *
     * @access public
     * @param string $hex
     * @return array $rgb
     * Credit: c.bavota (http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/)
     */
    public function hex2rgb($hex) {
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array($r, $g, $b);
        return $rgb; // returns an array with the rgb values
    }

}
