<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://docs.transifex.com/developer/integrations/wordpress
 *
 * @package    Transifex_Live_Wordpress
 * @subpackage Transifex_Live_Wordpress/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Transifex_Live_Wordpress
 * @subpackage Transifex_Live_Wordpress/includes
 */
class Transifex_Live_Wordpress_i18n {

    private $domain;
    private $directory;

    /**
     * Load the plugin text domain for translation.
     */
    public function load_plugin_textdomain() {

        load_plugin_textdomain(
                $this->domain, false, $this->directory
        );
    }

    /**
     * Sets the location for the languages path
     */
    public function set_directory() {
        $this->directory = dirname(dirname(plugin_basename(__FILE__))) . '/languages/';
    }

    /**
     * Default setter
     */
    public function set_domain($domain) {
        $this->domain = $domain;
    }

    /**
     * Default getters
     */
    public function get_directory() {
        return $this->directory;
    }

    public function get_domain() {
        return $this->domain;
    }

}
