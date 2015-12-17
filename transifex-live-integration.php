<?php

/**
 * Translates your website using Transifex Live
 *
 * @link              http://docs.transifex.com/developer/integrations/wordpress
 * @package           TransifexLiveIntegration
 * @version           1.0.5
 *
 * @wordpress-plugin
 * Plugin Name:       Transifex Live Translation Plugin
 * Plugin URI:        http://docs.transifex.com/developer/integrations/wordpress
 * Description:       Translate your WordPress website or blog without the usual complex setups.
 * Version:           1.0.5
 * License:           GNU General Public License
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       transifex-live-integration
 * Domain Path:       /languages
 */
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

register_activation_hook( __FILE__, [ 'Transifex_Live_Integration', 'activation_hook' ] );
register_deactivation_hook( __FILE__, [ 'Transifex_Live_Integration', 'deactivation_hook' ] );

/**
 * Define constants
 * Check if constant has already been set to allow for overrides coming from wp-config
 */
if ( !defined( 'TRANSIFEX_LIVE_INTEGRATION_NAME' ) ) {
	define( 'TRANSIFEX_LIVE_INTEGRATION_NAME', 'transifex-live-integration' );
}

if ( !defined( 'TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE' ) ) {
	define( 'TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE', dirname( __FILE__ ) );
}

if ( !defined( 'TRANSIFEX_LIVE_INTEGRATION_URL' ) ) {
	define( 'TRANSIFEX_LIVE_INTEGRATION_URL', plugin_dir_url( __FILE__ ) );
}

if ( !defined( 'TRANSIFEX_LIVE_INTEGRATION_ACTION_LINKS' ) ) {
	define( 'TRANSIFEX_LIVE_INTEGRATION_ACTION_LINKS', 'plugin_action_links_transifex_live_integration' );
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

define( 'LANG_PARAM', 'lang' );

include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/plugin-debug.php';
$debug = new Plugin_Debug();
$version = '1.0.6';

/**
 * Main Plugin Class
 */
class Transifex_Live_Integration {

	/**
	 * Main Plugin Function
	 * @param boolean $is_admin Stores if the plugin is in admin screens.
	 * @param string  $version Stores current version number.
	 */
	static function do_plugin( $is_admin, $version ) {
		Plugin_Debug::logTrace();
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-defaults.php';
		$settings = get_option( 'transifex_live_settings', array() );
		if ( !$settings ) {

			$settings = Transifex_Live_Integration_Defaults::settings();
		}
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-rewrite.php';
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-generate-rewrite-rules.php';
		$rewrite = Transifex_Live_Integration_Rewrite::create_rewrite( $settings );
		($rewrite)?Plugin_Debug::logTrace("rewrite created"):Plugin_Debug::logTrace("rewrite false");;
		if ( $rewrite ) {

			add_action( 'init', array( 'Transifex_Live_Integration_Rewrite', 'init_hook' ) );
			add_filter( 'query_vars', array( 'Transifex_Live_Integration_Rewrite', 'query_vars_hook' ) );
			add_filter( 'page_rewrite_rules', [ $rewrite, 'page_rewrite_rules_hook' ] );
			
//			add_filter( 'post_link', array( 'Transifex_Live_Integration_Rewrite', 'post_link_hook' ), 10, 2 );
//			add_action( 'parse_query', array( 'Transifex_Live_Integration_Rewrite', 'parse_query_hook' ) );
		}
		if ( $is_admin ) {
			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-action-links.php';
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( 'Transifex_Live_Integration_Action_Links', 'action_links' ) );

			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-settings-page.php';
			add_action( 'admin_menu', array( 'Transifex_Live_Integration', 'admin_menu_hook' ) );
			add_filter( 'admin_init', array( 'Transifex_Live_Integration_Settings_Page', 'update_settings' ) );

			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-static-files-handler.php';
			$handler = new Transifex_Live_Integration_Static_Files_Handler();
			$handler->add_css_file( $version, TRANSIFEX_LIVE_INTEGRATION_STYLESHEETS . '/transifex-live-integration-settings-page.css' );

			$handler->add_js_file( $version, TRANSIFEX_LIVE_INTEGRATION_JAVASCRIPT . '/transifex-live-integration-settings-page.js' );
			$handler->add_js_file( $version, TRANSIFEX_LIVE_INTEGRATION_JAVASCRIPT . '/jquery.validate.min.js', 'jquery-validate' );

			add_action( 'admin_enqueue_scripts', [ $handler, 'render_css' ] );
			add_action( 'admin_enqueue_scripts', [ $handler, 'render_js' ] );
			add_action( 'admin_enqueue_scripts', [ $handler, 'render_iris_color_picker' ] );

			load_plugin_textdomain( TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN, false, TRANSIFEX_LIVE_INTEGRATION_LANGUAGES_PATH );
		} else {

			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-javascript.php';
			$javascript = new Transifex_Live_Integration_Javascript( $settings, true );
			add_action( 'wp_head', [ $javascript, 'render' ], 1 );
			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-css.php';
			$css = new Transifex_Live_Integration_Css( $settings );
			$css->inline_render();
		}
	}

	/**
	 * Callback function for admin_menu action
	 */
	static function admin_menu_hook() {
		Plugin_Debug::logTrace();
		add_options_page( 'Transifex Live', 'Transifex Live', 'manage_options', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN, array( 'Transifex_Live_Integration_Settings_Page', 'options_page' ) );
	}

	/**
	 * Plugin deactivation stub
	 */
	static function deactivation_hook() {

	}

	/**
	 * Plugin activation stub
	 */
	static function activation_hook() {
		/*
		$settings = get_option( 'transifex_live_settings', array() );
		if ( isset( $settings['api_key'] ) ) {
				$raw_api_response = Transifex_Live_Integration_Settings_Util::get_raw_transifex_languages( $settings['api_key'] );
				if ( isset( $raw_api_response ) ) {
					$settings['raw_transifex_languages'] = $raw_api_response;
					$settings['source_language'] = Transifex_Live_Integration_Settings_Util::get_source( $raw_api_response );
					$settings['languages'] = Transifex_Live_Integration_Settings_Util::get_default_languages( $raw_api_response );
					$settings['language_lookup'] = Transifex_Live_Integration_Settings_Util::get_language_lookup( $raw_api_response );
				}
			}
		 * 
		 */

	}

		 

}

Transifex_Live_Integration::do_plugin( is_admin(), $version );
