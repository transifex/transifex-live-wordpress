<?php

/**
 * The plugin unit tests
 *
 * @link       http://docs.transifex.com/developer/integrations/wordpress
 * @since      1.0.0
 *
 * @package    Transifex_Live_Wordpress
 * @subpackage Transifex_Live_Wordpress/test
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Transifex_Live_Wordpress
 * @subpackage Transifex_Live_Wordpress/test
 */
class Test_Transifex_Live_Wordpress extends WP_UnitTestCase {

    private $plugin;
    private $plugin_i18n;
    private $plugin_admin;
    private $plugin_public;

    function setUp() {
        $this->plugin = new Transifex_Live_Wordpress();
        $this->plugin_i18n = new Transifex_Live_Wordpress_i18n();
    }

    function testTrue() {
        // replace this with some actual testing code
        $this->assertTrue(true);
    }

    function testPlugin() {
        $this->assertFalse(null == $this->plugin);
    }

    function testAdminPlugin() {
        $this->plugin_admin = new Transifex_Live_Wordpress_Admin(
                $this->plugin->get_plugin_name(), $this->plugin->get_version(), $this->plugin->get_plugin_text_domain());

        $this->assertFalse(null == $this->plugin_admin);
        $this->assertFalse(null == $this->plugin_admin->get_defaults());
        $this->assertFalse(null == $this->plugin_admin->get_positions());
        $this->assertFalse(null == $this->plugin_admin->get_colors());
        $this->assertFalse(null == $this->plugin_admin->get_custom_picker());
        $this->assertFalse(null == $this->plugin_admin->get_urls());
    }

    function testPublicPlugin() {
        // Note public class is dependent on option admin class to load
        if ($this->plugin_admin != null) {
            $this->plugin_admin = new Transifex_Live_Wordpress_Admin(
                    $this->plugin->get_plugin_name(), $this->plugin->get_version());
        }
        $this->plugin_public = new Transifex_Live_Wordpress_Public(
                $this->plugin->get_plugin_name(), $this->plugin->get_version());

//TODO need to fix this test by defining settings in admin first - @mjjacko 4/25/15
//        $this->assertFalse(null == $this->plugin_public->get_settings());
        $this->assertFalse(null == $this->plugin_public);
    }

    function testConstants() {
        echo "TRANSIFEX_LIVE_URL:" . constant('TRANSIFEX_LIVE_URL') . PHP_EOL;
        $this->assertTrue(defined('TRANSIFEX_LIVE_URL'));

        echo "TRANSIFEX_LIVE_PUBLIC_CSS:" . constant('TRANSIFEX_LIVE_PUBLIC_CSS') . PHP_EOL;
        $this->assertTrue(defined('TRANSIFEX_LIVE_PUBLIC_CSS'));

        echo "TRANSIFEX_LIVE_PUBLIC_JS:" . constant('TRANSIFEX_LIVE_PUBLIC_JS') . PHP_EOL;
        $this->assertTrue(defined('TRANSIFEX_LIVE_PUBLIC_JS'));

        echo "TRANSIFEX_LIVE_ADMIN_CSS:" . constant('TRANSIFEX_LIVE_ADMIN_CSS') . PHP_EOL;
        $this->assertTrue(defined('TRANSIFEX_LIVE_PUBLIC_CSS'));

        echo "TRANSIFEX_LIVE_ADMIN_JS:" . constant('TRANSIFEX_LIVE_ADMIN_JS') . PHP_EOL;
        $this->assertTrue(defined('TRANSIFEX_LIVE_ADMIN_JS'));
    }

    function testLocale() {
        $this->plugin_i18n->set_domain($this->plugin->get_plugin_name());
        $this->plugin_i18n->set_directory();
        echo "Locale Directory:" . $this->plugin_i18n->get_directory() . PHP_EOL;
        $this->assertFalse(null == $this->plugin_i18n);
    }

    function testLoaderCounts() {
        $this->assertTrue(5 == count($this->plugin->get_loader()->get_actions()));
        $this->assertTrue(0 == count($this->plugin->get_loader()->get_filters()));
    }

}
