<?php

/**
 * @link              http://docs.transifex.com/developer/integrations/wordpress
 * @package           Transifex_Live_Wordpress
 * @version 		  1.0.2
 *
 * @wordpress-plugin
 * Plugin Name:       Live Translation Plugin
 * Plugin URI:        http://docs.transifex.com/developer/integrations/wordpress
 * Description: 	  The Live Translation Plugin uses Transifex Live, Transifex Live WordPress Plugin a new, innovative way to localize your WordPress website or blog.
 * Version: 		  1.0.2
 * License: 		  GNU General Public License
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       transifex-live-integration
 * Domain Path:       /languages
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-transifex-live-wordpress-activator.php
 */
function activate_transifex_live_wordpress() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-transifex-live-wordpress-activator.php';
    Transifex_Live_Wordpress_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-transifex-live-wordpress-deactivator.php
 */
function deactivate_transifex_live_wordpress() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-transifex-live-wordpress-deactivator.php';
    Transifex_Live_Wordpress_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_transifex_live_wordpress');
register_deactivation_hook(__FILE__, 'deactivate_transifex_live_wordpress');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-transifex-live-wordpress.php';

/**
 * Define constants
 * Check if constant has already been set to allow for overrides coming from wp-config
 */
if (!defined('TRANSIFEX_LIVE_URL')) {
    define('TRANSIFEX_LIVE_URL', plugin_dir_url(__FILE__));
}

if (!defined('TRANSIFEX_LIVE_ADMIN_JS')) {
    define('TRANSIFEX_LIVE_ADMIN_JS', plugin_dir_url(__FILE__) . 'admin/js/');
}

if (!defined('TRANSIFEX_LIVE_PUBLIC_JS')) {
    define('TRANSIFEX_LIVE_PUBLIC_JS', plugin_dir_url(__FILE__) . 'public/js/');
}

if (!defined('TRANSIFEX_LIVE_ADMIN_CSS')) {
    define('TRANSIFEX_LIVE_ADMIN_CSS', plugin_dir_url(__FILE__) . 'admin/css/');
}

if (!defined('TRANSIFEX_LIVE_PUBLIC_CSS')) {
    define('TRANSIFEX_LIVE_PUBLIC_CSS', plugin_dir_url(__FILE__) . 'public/css/');
}

/**
 * Begins execution of the plugin.
 */
function run_transifex_live_wordpress() {

    $plugin = new Transifex_Live_Wordpress();
    $plugin->run();
}

run_transifex_live_wordpress();


