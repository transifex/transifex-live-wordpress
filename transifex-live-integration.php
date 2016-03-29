<?php

/**
 * Translates your website using Transifex Live
 *
 * @link    http://docs.transifex.com/developer/integrations/wordpress
 * @package TransifexLiveIntegration
 * @version 1.3.0
 *
 * @wordpress-plugin
 * Plugin Name:       Transifex Live Translation Plugin
 * Plugin URI:        http://docs.transifex.com/developer/integrations/wordpress
 * Description:       Translate your WordPress website or blog without the usual complex setups.
 * Version:           1.3.0
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
	define( 'TRANSIFEX_LIVE_INTEGRATION_ACTION_LINKS', 'plugin_action_links_'.TRANSIFEX_LIVE_INTEGRATION_BASENAME );
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
$version = '1.3.0';

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
		// Plugin 'global' functions
		require_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/common/plugin-debug.php';
		new Plugin_Debug(false);
		Plugin_Debug::logTrace( 'debug initialized' );
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-defaults.php';
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-static-factory.php';

// Load general settings
		$settings = get_option( 'transifex_live_settings', array() );
		if ( !$settings ) {

			$settings = Transifex_Live_Integration_Defaults::settings();
		}

// Load rewrite settings
		$rewrite_options = get_option( 'transifex_live_options', array() );
		if ( !$rewrite_options ) {

			$rewrite_options = Transifex_Live_Integration_Defaults::options_values();
		}

// Add notranslate to admin bar
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-admin-util.php';
		add_action( 'wp_before_admin_bar_render', [ 'Transifex_Live_Integration_Admin_Util', 'wp_before_admin_bar_render_hook' ] );
		add_action( 'wp_after_admin_bar_render', [ 'Transifex_Live_Integration_Admin_Util', 'wp_after_admin_bar_render_hook' ] );
		

		if ( $is_admin ) { // If user is on admin pages
			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-admin.php';
			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-admin-util.php';
			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/common/transifex-live-integration-static-files-handler.php';

// Setup admin dashboard backend
			add_filter( TRANSIFEX_LIVE_INTEGRATION_ACTION_LINKS, [ 'Transifex_Live_Integration_Admin_Util', 'action_links' ] );
			add_action( 'admin_menu', [ 'Transifex_Live_Integration_Admin_Util', 'admin_menu_hook' ] );
			add_action( 'admin_init', [ 'Transifex_Live_Integration_Admin', 'admin_init_hook' ] );
			add_action( 'admin_notices', [ 'Transifex_Live_Integration_Admin', 'admin_notices_hook' ] );

// Setup admin dashboard frontend
			$handler = new Transifex_Live_Integration_Static_Files_Handler();
			$handler->add_css_file( $version, TRANSIFEX_LIVE_INTEGRATION_STYLESHEETS . '/transifex-live-integration-settings-page.css' );

			$handler->add_js_file( $version, TRANSIFEX_LIVE_INTEGRATION_JAVASCRIPT . '/jquery.jloggins.1.0.1.js', 'jloggins' );
			$handler->add_js_file( $version, TRANSIFEX_LIVE_INTEGRATION_JAVASCRIPT . '/jquery-machine.1.0.1.min.js', 'jquery-machine' );
			$handler->add_js_file( $version, TRANSIFEX_LIVE_INTEGRATION_JAVASCRIPT . '/transifex-live-integration-transifex-settings.js' );
			$handler->add_js_file( $version, TRANSIFEX_LIVE_INTEGRATION_JAVASCRIPT . '/transifex-live-integration-settings-page.js' );

			add_action( 'admin_enqueue_scripts', [ $handler, 'render_css' ] );
			add_action( 'admin_enqueue_scripts', [ $handler, 'render_js' ] );

			load_plugin_textdomain( TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN, false, TRANSIFEX_LIVE_INTEGRATION_LANGUAGES_PATH );
		}

		if ( !($is_admin) ) { // If user is on regular page
			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-static-factory.php';
			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-util.php';
// Set lang parameter in query var
			add_filter( 'query_vars', [ 'Transifex_Live_Integration_Util', 'query_vars_hook' ] );
			
// Load snippet
			$live_snippet = Transifex_Live_Integration_Static_Factory::create_live_snippet( $settings );
			if ( $live_snippet ) {
				// We need to wait until wp is setup to retrieve query var
				add_action( 'wp', [ $live_snippet, 'wp_hook' ]);
				add_action( 'wp_head', [ $live_snippet, 'wp_head_hook' ], 1 );
			}
			

// Load prerender feature
			$prerender = Transifex_Live_Integration_Static_Factory::create_prerender( $settings );
			($prerender) ? Plugin_Debug::logTrace( 'prerender created' ) : Plugin_Debug::logTrace( 'prerender skipped' );
			if ( $prerender ) {
				if ( Transifex_Live_Integration_Util::is_prerender_req(Transifex_Live_Integration_Util::get_user_agent()) ) {
					Plugin_Debug::logTrace( 'prerender request detected' );
					add_filter( 'wp_headers', [$prerender, 'wp_headers_hook' ] );
					add_action( 'wp_head', [$prerender, 'wp_head_hook' ], 1 );
				} else {
					Plugin_Debug::logTrace( 'invoke prerender call' );
					add_action( 'after_setup_theme', [ $prerender, 'after_setup_theme_hook' ] );
					add_action( 'shutdown', [ $prerender, 'shutdown_hook' ] );
				}
			}


// Load hreflang feature
			$hreflang = Transifex_Live_Integration_Static_Factory::create_hreflang( $settings );
			($hreflang) ? Plugin_Debug::logTrace( 'adding hreflang' ) : Plugin_Debug::logTrace( 'skipping hreflang' );
			if ( $hreflang ) {
				add_action( 'wp_head', [ $hreflang, 'render_hreflang' ], 1 );
			}

// Load language picker feature
			$picker = Transifex_Live_Integration_Static_Factory::create_picker( $settings );
			($picker) ? Plugin_Debug::logTrace( 'picker created' ) : Plugin_Debug::logTrace( 'picker skipped' );
			if ( $picker ) {
				add_action( 'wp_head', [ $picker, 'render' ], 1 );
			}

// Load subdomain feature		
			$subdomain = Transifex_Live_Integration_Static_Factory::create_subdomains( $settings );
			($subdomain) ? Plugin_Debug::logTrace( 'subdomains created' ) : Plugin_Debug::logTrace( 'subdomains skipped' );
			if ( $subdomain ) {
				add_action( 'parse_query', [ $subdomain, 'parse_query_hook' ] );
			}

// Load subdirectory feature
			$rewrite = Transifex_Live_Integration_Static_Factory::create_rewrite( $settings, $rewrite_options );
			($rewrite) ? Plugin_Debug::logTrace( 'rewrite created' ) : Plugin_Debug::logTrace( 'rewrite skipped' );
			if ( $rewrite ) {
				if ( isset( $rewrite_options['add_rewrites_reverse_template_links'] ) ) {
					Plugin_Debug::logTrace( 'adding reverse template links' );
					add_action( 'wp', [ $rewrite, 'wp_hook' ]);
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
				add_action( 'parse_query', [ $rewrite, 'parse_query_hook' ] );

				foreach ($rewrite->rewrite_options as $option) {
					switch ($option) {
						case 'date';
							add_filter( 'date_rewrite_rules', [ $rewrite, 'date_rewrite_rules_hook' ] );
							break;
						case 'page';
							add_filter( 'page_rewrite_rules', [ $rewrite, 'page_rewrite_rules_hook' ] );
							break;
						case 'author';
							add_filter( 'author_rewrite_rules', [ $rewrite, 'author_rewrite_rules_hook' ] );
							break;
						case 'tag';
							add_filter( 'tag_rewrite_rules', [ $rewrite, 'tag_rewrite_rules_hook' ] );
							break;
						case 'category';
							add_filter( 'category_rewrite_rules', [ $rewrite, 'category_rewrite_rules_hook' ] );
							break;
						case 'search';
							add_filter( 'search_rewrite_rules', [ $rewrite, 'search_rewrite_rules_hook' ] );
							break;
						case 'feed';
							add_filter( 'feed_rewrite_rules', [ $rewrite, 'feed_rewrite_rules_hook' ] );
							break;
						case 'post';
							add_filter( 'post_rewrite_rules', [ $rewrite, 'post_rewrite_rules_hook' ] );
							break;
						case 'root';
							add_filter( 'root_rewrite_rules', [ $rewrite, 'root_rewrite_rules_hook' ] );
							break;
						case 'permalink_tag';
							add_action( 'init', [ $rewrite, 'init_hook' ] );
							break;
					}
				}
			}
		}
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