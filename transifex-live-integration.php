<?php

/**
 * @link              http://docs.transifex.com/developer/integrations/wordpress
 * @package           Transifex_Live_Integration
 * @version           1.0.4
 *
 * @wordpress-plugin
 * Plugin Name:       Live Translation Plugin
 * Plugin URI:        http://docs.transifex.com/developer/integrations/wordpress
 * Description:       The Live Translation Plugin uses Transifex Live, Transifex Live WordPress Plugin a new, innovative way to localize your WordPress website or blog.
 * Version:           1.0.4
 * License:           GNU General Public License
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       transifex-live-integration
 * Domain Path:       /languages
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define constants
 * Check if constant has already been set to allow for overrides coming from wp-config
 */
if (!defined('TRANSIFEX_LIVE_INTEGRATION_NAME')) {
    define('TRANSIFEX_LIVE_INTEGRATION_NAME', 'transifex-live-integration');
}

if (!defined('TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE')) {
    define('TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE', dirname(__FILE__));
}

if (!defined('TRANSIFEX_LIVE_INTEGRATION_URL')) {
    define('TRANSIFEX_LIVE_INTEGRATION_URL', plugin_dir_url(__FILE__));
}

if (!defined('TRANSIFEX_LIVE_INTEGRATION_ACTION_LINKS')) {
    define('TRANSIFEX_LIVE_INTEGRATION_ACTION_LINKS', 'plugin_action_links_transifex_live_integration');
}

if (!defined('TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN')) {
    define('TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN', 'transifex-live-integration');
}

if (!defined('TRANSIFEX_LIVE_INTEGRATION_LANGUAGES_PATH')) {
    define('TRANSIFEX_LIVE_INTEGRATION_LANGUAGES_PATH', TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/languages/');
}

if (!defined('TRANSIFEX_LIVE_INTEGRATION_STYLESHEETS')) {
    define('TRANSIFEX_LIVE_INTEGRATION_STYLESHEETS', plugins_url().'/'.TRANSIFEX_LIVE_INTEGRATION_NAME.'/stylesheets');
}

if (!defined('TRANSIFEX_LIVE_INTEGRATION_JAVASCRIPT')) {
    define('TRANSIFEX_LIVE_INTEGRATION_JAVASCRIPT', plugins_url().'/'.TRANSIFEX_LIVE_INTEGRATION_NAME.'/javascript');
}

include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/plugin-debug.php';

register_activation_hook(__FILE__, ['Transifex_Live_Integration', 'activation_hook']);
register_deactivation_hook(__FILE__, ['Transifex_Live_Integration', 'deactivation_hook']);
$debug = new Plugin_Debug();

class Transifex_Live_Integration {

    static function do_plugin($is_admin) {
        Plugin_Debug::logTrace();

        if ($is_admin) {
            include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-action-links.php';
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array('Transifex_Live_Integration_Action_Links', 'action_links'));
            
            include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-settings-page.php';
            add_action('admin_menu', array('Transifex_Live_Integration', 'admin_menu_hook'));
            add_filter('admin_init', array('Transifex_Live_Integration_Settings_Page', 'update_settings'));
            
            include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-static-files-handler.php';
            $handler = new Transifex_Live_Integration_Static_Files_Handler();
            $handler->add_css_file(TRANSIFEX_LIVE_INTEGRATION_VERSION,TRANSIFEX_LIVE_INTEGRATION_STYLESHEETS.'/transifex-live-integration-settings-page.css');
            
            $handler->add_js_file(TRANSIFEX_LIVE_INTEGRATION_VERSION,TRANSIFEX_LIVE_INTEGRATION_JAVASCRIPT.'/transifex-live-integration-settings-page.js' );
            $handler->add_js_file(TRANSIFEX_LIVE_INTEGRATION_VERSION,TRANSIFEX_LIVE_INTEGRATION_JAVASCRIPT.'/jquery.validate.min.js','jquery-validate' );

            add_action( 'admin_enqueue_scripts', [$handler, 'render_css']);
            add_action( 'admin_enqueue_scripts', [$handler, 'render_js']);
            add_action( 'admin_enqueue_scripts', [$handler, 'render_iris_color_picker']);

            load_plugin_textdomain(TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN, false, TRANSIFEX_LIVE_INTEGRATION_LANGUAGES_PATH);
            
        } else {
            $settings = get_option('transifex_live_settings', array());
            Plugin_Debug::logTrace($settings);
            
            include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-javascript.php';
            $javascript = new Transifex_Live_Integration_Javascript($settings);
            add_action('wp_head', [$javascript, 'render'], 1);
            include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-css.php';
            $css = new Transifex_Live_Integration_Css($settings);
            $css->inline_render();
        }
    }
        static function admin_menu_hook(){
            Plugin_Debug::logTrace();
            add_options_page('Transifex Live', 'Transifex Live', 'manage_options', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN, array('Transifex_Live_Integration_Settings_Page', 'options_page'));

        }
    


    static function deactivation_hook() {
        Plugin_Debug::logTrace();
    }

    static function activation_hook() {
        Plugin_Debug::logTrace();
    }

}

Transifex_Live_Integration::do_plugin(is_admin());
