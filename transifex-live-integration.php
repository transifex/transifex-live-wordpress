<?php

/**
 * Translate your WordPress powered website using Transifex.
 *
 * @link    http://docs.transifex.com/developer/integrations/wordpress
 * @package TransifexLiveIntegration
 * @version 1.3.21
 *
 * @wordpress-plugin
 * Plugin Name:       International SEO by Transifex
 * Plugin URI:        http://docs.transifex.com/developer/integrations/wordpress
 * Description:       Translate your WordPress powered website using Transifex.
 * Version:           1.3.21
 * License:           GNU General Public License
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       transifex-live-integration
 * Domain Path:       /languages
 */
if ( !defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Plugin activation hooks
 * These are stubbed functions, as we avoid changing system state on activation/deactivation
 */
register_activation_hook( __FILE__, [ 'Transifex_Live_Integration', 'activation_hook' ] );
register_deactivation_hook( __FILE__, [ 'Transifex_Live_Integration', 'deactivation_hook' ] );

/**
 * Define constants
 * Check if constant has already been set to allow for overrides coming from wp-config
 */
if ( !defined( 'TRANSIFEX_LIVE_INTEGRATION_NAME' ) ) {
	define( 'TRANSIFEX_LIVE_INTEGRATION_NAME', 'transifex-live-integration' );
}

if ( !defined( 'TRANSIFEX_LIVE_INTEGRATION_BASENAME' ) ) {
	define( 'TRANSIFEX_LIVE_INTEGRATION_BASENAME', plugin_basename( __FILE__ ) );
}

if ( !defined( 'TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE' ) ) {
	define( 'TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE', dirname( __FILE__ ) );
}

if ( !defined( 'TRANSIFEX_LIVE_INTEGRATION_URL' ) ) {
	define( 'TRANSIFEX_LIVE_INTEGRATION_URL', plugin_dir_url( __FILE__ ) );
}

if ( !defined( 'TRANSIFEX_LIVE_INTEGRATION_ACTION_LINKS' ) ) {
	define( 'TRANSIFEX_LIVE_INTEGRATION_ACTION_LINKS', 'plugin_action_links_' . TRANSIFEX_LIVE_INTEGRATION_BASENAME );
}

if ( !defined( 'TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN' ) ) {
	define( 'TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN', 'transifex-live-integration' );
}

if ( !defined( 'TRANSIFEX_LIVE_INTEGRATION_LANGUAGES_PATH' ) ) {
	define( 'TRANSIFEX_LIVE_INTEGRATION_LANGUAGES_PATH', TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/languages/' );
}

if ( !defined( 'TRANSIFEX_LIVE_INTEGRATION_STYLESHEETS' ) ) {
	define( 'TRANSIFEX_LIVE_INTEGRATION_STYLESHEETS', TRANSIFEX_LIVE_INTEGRATION_URL . 'stylesheets' );
}

if ( !defined( 'TRANSIFEX_LIVE_INTEGRATION_JAVASCRIPT' ) ) {
	define( 'TRANSIFEX_LIVE_INTEGRATION_JAVASCRIPT', TRANSIFEX_LIVE_INTEGRATION_URL . 'javascript' );
}

if ( !defined( 'TRANSIFEX_LIVE_INTEGRATION_REGEX_PATTERN_CHECK_PATTERN' ) ) {
	define( 'TRANSIFEX_LIVE_INTEGRATION_REGEX_PATTERN_CHECK_PATTERN', "/\(.*\?|.*\)/" );
}

define( 'LANG_PARAM', 'lang' );
$version = '1.3.21';

require_once( dirname( __FILE__ ) . '/transifex-live-integration-main.php' );
Transifex_Live_Integration::do_plugin( is_admin(), $version );
