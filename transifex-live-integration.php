<?php

/**
 * Translates your website using Transifex Live
 *
 * @link    http://docs.transifex.com/developer/integrations/wordpress
 * @package TransifexLiveIntegration
 * @version 1.2.1
 *
 * @wordpress-plugin
 * Plugin Name:       Transifex Live Translation Plugin
 * Plugin URI:        http://docs.transifex.com/developer/integrations/wordpress
 * Description:       Translate your WordPress website or blog without the usual complex setups.
 * Version:           1.2.1
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

require_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/plugin-debug.php';
$version = '1.2.1';
$debug = new Plugin_Debug();

/**
 * Main Plugin Class
 */
class Transifex_Live_Integration {

	/**
	 * Main Plugin Function
	 * @param boolean $is_admin Stores if the plugin is in admin screens.
	 * @param string  $version  Stores current version number.
	 */
	static function do_plugin( $is_admin, $version ) {
		Plugin_Debug::logTrace();
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-defaults.php';
		$settings = get_option( 'transifex_live_settings', array() );
		if ( !$settings ) {

			$settings = Transifex_Live_Integration_Defaults::settings();
		}

		$rewrite_options = get_option( 'transifex_live_options', array() );
		if ( !$rewrite_options ) {

			$rewrite_options = Transifex_Live_Integration_Defaults::options_values();
		}

		add_filter( 'query_vars', array( 'Transifex_Live_Integration', 'query_vars_hook' ) );
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-subdomain.php';
		$subdomain = Transifex_Live_Integration_Subdomain::create_subdomains( $settings );
		($subdomain) ? Plugin_Debug::logTrace( 'subdomains created' ) : Plugin_Debug::logTrace( 'subdomains skipped' );
		if ( $subdomain ) {
			add_action( 'parse_query', [ $subdomain, 'parse_query_hook' ] );
		}

		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-rewrite.php';
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-generate-rewrite-rules.php';
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-validators.php';
		$rewrite = Transifex_Live_Integration_Rewrite::create_rewrite( $settings, $rewrite_options );
		($rewrite) ? Plugin_Debug::logTrace( 'rewrite created' ) : Plugin_Debug::logTrace( 'rewrite skipped' );
		if ( $rewrite ) {
			if ( isset( $rewrite_options['add_rewrites_reverse_template_links'] ) ) {
				Plugin_Debug::logTrace( 'adding reverse template links' );
				add_filter( 'pre_post_link', [$rewrite, 'pre_post_link_hook' ], 10, 3 );
				add_filter( 'term_link', [$rewrite, 'term_link_hook' ], 10, 3 );
				add_filter( 'post_link', [$rewrite, 'term_link_hook' ], 10, 3 );
				add_filter( 'post_type_archive_link', [$rewrite, 'post_type_archive_link_hook' ], 10, 2 );
				add_filter( 'page_link', [$rewrite, 'page_link_hook' ], 10, 3 );
				add_filter( 'day_link', [$rewrite, 'day_link_hook' ], 10, 4 );
				add_filter( 'month_link', [$rewrite, 'month_link_hook' ], 10, 3 );
				add_filter( 'year_link', [$rewrite, 'year_link_hook' ], 10, 2 );
				add_filter( 'home_url', [$rewrite, 'home_url_hook' ] );
			}
			foreach ($rewrite->rewrite_options as $option) {
				Plugin_Debug::logTrace( $option );
				switch ($option) {
					case 'date';
						add_filter( 'date_rewrite_rules', [ $rewrite, 'date_rewrite_rules_hook' ] );
						add_action( 'parse_query', [ $rewrite, 'parse_query_hook' ] );
						break;
					case 'page';
						add_filter( 'page_rewrite_rules', [ $rewrite, 'page_rewrite_rules_hook' ] );
						add_action( 'parse_query', [ $rewrite, 'parse_query_hook' ] );
						break;
					case 'author';
						add_filter( 'author_rewrite_rules', [ $rewrite, 'author_rewrite_rules_hook' ] );
						add_action( 'parse_query', [ $rewrite, 'parse_query_hook' ] );
						break;
					case 'tag';
						add_filter( 'tag_rewrite_rules', [ $rewrite, 'tag_rewrite_rules_hook' ] );
						add_action( 'parse_query', [ $rewrite, 'parse_query_hook' ] );
						break;
					case 'category';
						add_filter( 'category_rewrite_rules', [ $rewrite, 'category_rewrite_rules_hook' ] );
						add_action( 'parse_query', [ $rewrite, 'parse_query_hook' ] );
						break;
					case 'search';
						add_filter( 'search_rewrite_rules', [ $rewrite, 'search_rewrite_rules_hook' ] );
						add_action( 'parse_query', [ $rewrite, 'parse_query_hook' ] );
						break;
					case 'feed';
						add_filter( 'feed_rewrite_rules', [ $rewrite, 'feed_rewrite_rules_hook' ] );
						add_action( 'parse_query', [ $rewrite, 'parse_query_hook' ] );
						break;
					case 'post';
						add_filter( 'post_rewrite_rules', [ $rewrite, 'post_rewrite_rules_hook' ] );
						add_action( 'parse_query', [ $rewrite, 'parse_query_hook' ] );
						break;
					case 'root';
						add_filter( 'root_rewrite_rules', [ $rewrite, 'root_rewrite_rules_hook' ] );
						add_action( 'parse_query', [ $rewrite, 'parse_query_hook' ] );
						break;
					default;
						Plugin_Debug::logTrace( 'default' );
						add_action( 'init', [ $rewrite, 'init_hook' ] );
						add_action( 'parse_query', [ $rewrite, 'parse_query_hook' ] );
						break;
				}
			}
		}

		if ( $is_admin ) {
			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-action-links.php';
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( 'Transifex_Live_Integration_Action_Links', 'action_links' ) );

			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-settings-page.php';
			add_action( 'admin_menu', [ 'Transifex_Live_Integration', 'admin_menu_hook' ] );
			add_action( 'admin_init', [ 'Transifex_Live_Integration_Settings_Page', 'admin_init_hook' ] );

			add_action( 'admin_notices', [ 'Transifex_Live_Integration_Settings_Page', 'admin_notices_hook' ] );

			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-static-files-handler.php';
			$handler = new Transifex_Live_Integration_Static_Files_Handler();
			$handler->add_css_file( $version, TRANSIFEX_LIVE_INTEGRATION_STYLESHEETS . '/transifex-live-integration-settings-page.css' );

			$handler->add_js_file( $version, TRANSIFEX_LIVE_INTEGRATION_JAVASCRIPT . '/transifex-live-integration-settings-page.js' );
			$handler->add_js_file( $version, TRANSIFEX_LIVE_INTEGRATION_JAVASCRIPT . '/jquery.validate.min.js', 'jquery-validate' );

			add_action( 'admin_enqueue_scripts', [ $handler, 'render_css' ] );
			add_action( 'admin_enqueue_scripts', [ $handler, 'render_js' ] );

			load_plugin_textdomain( TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN, false, TRANSIFEX_LIVE_INTEGRATION_LANGUAGES_PATH );
		} else {


			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-hreflang.php';
			$hreflang = new Transifex_Live_Integration_Hreflang( $settings, true );
			($hreflang->ok_to_add()) ? Plugin_Debug::logTrace( 'adding hreflang' ) : Plugin_Debug::logTrace( 'skipping hreflang' );
			if ( $hreflang->ok_to_add() ) {
				add_action( 'wp_head', [ $hreflang, 'render_hreflang' ], 1 );
			}

			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-javascript.php';
			$javascript = new Transifex_Live_Integration_Javascript( $settings, $rewrite ? true : false  );
			add_action( 'wp_head', [ $javascript, 'render' ], 1 );
		}
	}

	/**
	 * Callback function for query_vars action
	 * @param array $vars list of vars passed from WP.
	 */
	static function query_vars_hook( $vars ) {
		Plugin_Debug::logTrace();
		$vars[] = 'lang';
		return $vars;
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
		// Placeholder function.
	}

	/**
	 * Plugin activation stub
	 */
	static function activation_hook() {
		// Placeholder function.
	}

}

Transifex_Live_Integration::do_plugin( is_admin(), $version );
