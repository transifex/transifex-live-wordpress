<?php

/**
 * Includes Transifex Live Main class
 * @package TransifexLiveIntegration
 */

/**
 * Main Plugin Class
 */
class Transifex_Live_Integration {

	/**
	 * Main Plugin Function
	 * @param boolean $is_admin If the plugin is in admin screens.
	 * @param string  $version  Current version number.
	 */
	static function do_plugin( $is_admin, $version ) {
		$settings = get_option( 'transifex_live_settings', array() );
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-defaults.php';
		if ( !$settings ) {

			$settings = Transifex_Live_Integration_Defaults::settings();
		}
		$live_settings = Transifex_Live_Integration_Defaults::transifex_settings();
		$debug_mode = ($settings['debug']) ? true : false;

		require_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/common/plugin-debug.php';
		new Plugin_Debug( $debug_mode );
		Plugin_Debug::logTrace( 'debug initialized' );

		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-static-factory.php';

		$rewrite_options = get_option( 'transifex_live_options', array() );
		if ( !$rewrite_options ) {

			$rewrite_options = Transifex_Live_Integration_Defaults::options_values();
		}

		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-admin-util.php';
		add_action( 'wp_before_admin_bar_render', [ 'Transifex_Live_Integration_Admin_Util', 'wp_before_admin_bar_render_hook' ] );
		add_action( 'wp_after_admin_bar_render', [ 'Transifex_Live_Integration_Admin_Util', 'wp_after_admin_bar_render_hook' ] );


		if ( $is_admin ) {
			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-admin.php';
			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-admin-util.php';
			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/common/transifex-live-integration-static-files-handler.php';

			add_filter( TRANSIFEX_LIVE_INTEGRATION_ACTION_LINKS, [ 'Transifex_Live_Integration_Admin_Util', 'action_links_hook' ] );
			add_action( 'admin_menu', [ 'Transifex_Live_Integration_Admin_Util', 'admin_menu_hook' ] );
			add_action( 'admin_init', [ 'Transifex_Live_Integration_Admin', 'admin_init_hook' ] );
			add_action( 'admin_notices', [ 'Transifex_Live_Integration_Admin', 'admin_notices_hook' ] );

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

		if ( !($is_admin) ) {
			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-static-factory.php';
			include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-util.php';

			add_filter( 'query_vars', [ 'Transifex_Live_Integration_Util', 'query_vars_hook' ] );

			$live_snippet = Transifex_Live_Integration_Static_Factory::create_live_snippet( $settings, $live_settings );
			if ( $live_snippet ) {
				// We need to wait until wp is setup to retrieve query var
				add_action( 'wp', [ $live_snippet, 'wp_hook' ] );
				add_action( 'wp_head', [ $live_snippet, 'wp_head_hook' ], 1 );
			}


			$prerender = Transifex_Live_Integration_Static_Factory::create_prerender( $settings );
			($prerender) ? Plugin_Debug::logTrace( 'prerender created' ) : Plugin_Debug::logTrace( 'prerender skipped' );
			if ( $prerender ) {
				if ( $prerender->ok_add_vary_header() ) {
					add_filter( 'wp_headers', [$prerender, 'wp_headers_vary_hook' ] );
				}

				if ( $prerender->ok_call_prerender() ) {
					Plugin_Debug::logTrace( 'pased agent check' );
					if ( $prerender->ok_add_response_header() ) {
						add_filter( 'wp_headers', [$prerender, 'wp_headers_response_hook' ] );
					}
					if ( $prerender->ok_add_cookie() ) {
						add_filter( 'init', [$prerender, 'init_hook' ] );
					}
					if ( Transifex_Live_Integration_Util::is_prerender_req( Transifex_Live_Integration_Util::get_user_agent() ) ) {
						Plugin_Debug::logTrace( 'prerender request detected' );
						add_filter( 'wp_headers', [$prerender, 'wp_headers_prerender_hook' ] );
						add_action( 'wp_head', [$prerender, 'wp_head_hook' ], 1 );
					} else {
						Plugin_Debug::logTrace( 'invoke prerender call' );
						add_action( 'after_setup_theme', [ $prerender, 'after_setup_theme_hook' ] );
						add_action( 'shutdown', [ $prerender, 'shutdown_hook' ] );
					}
				}
			}


			$hreflang = Transifex_Live_Integration_Static_Factory::create_hreflang( $settings, $rewrite_options );
			($hreflang) ? Plugin_Debug::logTrace( 'adding hreflang' ) : Plugin_Debug::logTrace( 'skipping hreflang' );
			if ( $hreflang ) {
				add_action( 'wp_head', [ $hreflang, 'render_hreflang' ], 1 );
			}

			$picker = Transifex_Live_Integration_Static_Factory::create_picker( $settings );
			($picker) ? Plugin_Debug::logTrace( 'picker created' ) : Plugin_Debug::logTrace( 'picker skipped' );
			if ( $picker ) {
				add_action( 'wp_head', [ $picker, 'render' ], 1 );
			}

			$subdomain = Transifex_Live_Integration_Static_Factory::create_subdomains( $settings );
			($subdomain) ? Plugin_Debug::logTrace( 'subdomains created' ) : Plugin_Debug::logTrace( 'subdomains skipped' );
			if ( $subdomain ) {
				add_action( 'parse_query', [ $subdomain, 'parse_query_hook' ] );
			}
		}

		$rewrite = Transifex_Live_Integration_Static_Factory::create_rewrite( $settings, $rewrite_options );
		($rewrite) ? Plugin_Debug::logTrace( 'rewrite created' ) : Plugin_Debug::logTrace( 'rewrite skipped' );
		if ( $rewrite ) {
			// check for TDK enabled
			if (isset( $settings['enable_tdk'] )) {
				add_shortcode( 'get_language_url', [$rewrite,'get_language_url'] );
				add_shortcode( 'detect_language', [$rewrite,'detect_language'] );
				add_shortcode( 'is_language', [$rewrite,'is_language'] );
			}
			if ( isset( $rewrite_options['add_rewrites_reverse_template_links'] ) ) {
				Plugin_Debug::logTrace( 'adding reverse template links' );
				add_action( 'wp', [ $rewrite, 'wp_hook' ] );
				add_filter( 'pre_post_link', [$rewrite, 'pre_post_link_hook' ], 10, 3 );
				add_filter( 'term_link', [$rewrite, 'term_link_hook' ], 10, 3 );
				add_filter( 'post_link', [$rewrite, 'term_link_hook' ], 10, 3 );
				add_filter( 'post_type_archive_link', [$rewrite, 'post_type_archive_link_hook' ], 10, 2 );
				add_filter( 'page_link', [$rewrite, 'page_link_hook' ], 10, 3 );
				add_filter( 'day_link', [$rewrite, 'day_link_hook' ], 10, 4 );
				add_filter( 'month_link', [$rewrite, 'month_link_hook' ], 10, 3 );
				add_filter( 'year_link', [$rewrite, 'year_link_hook' ], 10, 2 );
				add_filter( 'home_url', [$rewrite, 'home_url_hook' ] );
				add_filter( 'the_content', [$rewrite, 'the_content_hook' ], 10);
				add_filter( 'widget_text', [$rewrite, 'the_content_hook' ], 10);
				// Add filter for custom content that is not triggered by any other hook
				add_filter('tx_link',  [ $rewrite,'the_content_hook'], 10 ,1);
			}
		}
		$subdirectory = Transifex_Live_Integration_Static_Factory::create_subdirectory( $settings, $rewrite_options );
		($subdirectory) ? Plugin_Debug::logTrace( 'subdirectory created' ) : Plugin_Debug::logTrace( 'subdirectory skipped' );
		if ( $subdirectory ) {

			// Adds 'lang' to query_vars for use in the template.
			add_action( 'parse_query', [ $subdirectory, 'parse_query_hook' ] );
			$static_frontpage_support = (isset( $settings['static_frontpage_support'] )) ? true : false;
			if ( $static_frontpage_support ) {
				add_action( 'parse_query', [ $subdirectory, 'parse_query_root_hook' ] );
			}

			foreach ($subdirectory->rewrite_options as $option) {
				switch ($option) {
					case 'post';
						add_filter( 'post_rewrite_rules', [ $subdirectory, 'post_rewrite_rules_hook' ] );
						break;
					case 'date';
						add_filter( 'date_rewrite_rules', [ $subdirectory, 'date_rewrite_rules_hook' ] );
						break;
					case 'page';
						add_filter( 'page_rewrite_rules', [ $subdirectory, 'page_rewrite_rules_hook' ] );
						break;
					case 'author';
						add_filter( 'author_rewrite_rules', [ $subdirectory, 'author_rewrite_rules_hook' ] );
						break;
					case 'tag';
						add_filter( 'tag_rewrite_rules', [ $subdirectory, 'tag_rewrite_rules_hook' ] );
						break;
					case 'category';
						add_filter( 'category_rewrite_rules', [ $subdirectory, 'category_rewrite_rules_hook' ] );
						break;
					case 'search';
						add_filter( 'search_rewrite_rules', [ $subdirectory, 'search_rewrite_rules_hook' ] );
						break;
					case 'feed';
						add_filter( 'feed_rewrite_rules', [ $subdirectory, 'feed_rewrite_rules_hook' ] );
						break;
					case 'root';
						add_filter( 'root_rewrite_rules', [ $subdirectory, 'root_rewrite_rules_hook' ] );
						break;
					case 'permalink_tag';
						add_action( 'init', [ $subdirectory, 'init_hook' ] );
						break;
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
