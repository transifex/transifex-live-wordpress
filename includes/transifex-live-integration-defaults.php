<?php

include_once __DIR__ .'/lib/transifex-live-integration-wp-services.php';

/**
 * Defaults for plugin settings
 * @package TransifexLiveIntegration
 */

/**
 * Static class for settings defaults
 */
class Transifex_Live_Integration_Defaults {

	/**
	 * Returns default option values for subdirectory rewrites
	 * @return array Returns the options value array
	 */
	static function options_values() {
		return [
			'add_rewrites_post' => 0,
			'add_rewrites_page' => 0,
			'add_rewrites_author' => 0,
			'add_rewrites_date' => 0,
			'add_rewrites_tag' => 0,
			'add_rewrites_category' => 0,
			'add_rewrites_search' => 0,
			'add_rewrites_root' => 0,
			'add_rewrites_reverse_template_links' => 1,
			'add_rewrites_permalink_tag' => 0
		];
	}

	/**
	 * Returns default option text display for subdirectory rewrites
	 * @param string $key The option key value stored to the database
	 * @return string Returns the text string
	 */
	static function get_options_text( $key ) {
		$arr = [
			'add_rewrites_post' => __( 'Posts', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ),
			'add_rewrites_page' => __( 'Pages', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ),
			'add_rewrites_author' => __( 'Authors', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ),
			'add_rewrites_date' => __( 'Date', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ),
			'add_rewrites_tag' => __( 'Tags', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ),
			'add_rewrites_category' => __( 'Categories', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ),
			'add_rewrites_search' => __( 'Search', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ),
			'add_rewrites_root' => __( 'Root', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ),
			'add_rewrites_reverse_template_links' => __( 'Reverse Template Links', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ),
			'add_rewrites_permalink_tag' => __( 'Permalink Tag', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ),
		];
		return $arr[$key];
	}

	/**
	 * Returns default values for Transifex settings Javascript include
	 * @return array Returns the settings array
	 */
	static function transifex_settings() {
		return [
			'settings' => '',
			//'picker' => 'no-picker',
			//'domain' => '',
			//'ignore_tags' => [ ],
			//'autocollect' => false,
			//'parse_attr' => [ ],
			//'dynamic' => false,
			//'detectlang' => false,
			//'ignore_class' => [ ],
			'wp' => true
		];
	}

	static function calc_default_subdomain($source_name, $is_subdirectory_install = false) {
		if ( function_exists( 'site_url' ) ) { // sometimes we might run outside of WP
			// Refer below to calc_default_subdirectory() for explanation of is_subdirectory_install
			$site_url = (new Transifex_Live_Integration_WP_Services())->get_site_url($is_subdirectory_install);
		} else {
			$site_url = 'http://www.mydomain.com';
		}
		return str_replace($source_name,'%LANG%',$site_url);
	}

	static function calc_default_subdirectory($is_subdirectory_install = false) {
		if ( function_exists( 'site_url' ) ) { // sometimes we might run outside of WP
			// is_subdirectory_install == true is the case when the site is installed in a subdirectory like
			// wwww.mydomain.com/cms but the site is accessible from www.mydomain.com (root url). In this case
			// we dont' use the site_url() function because it contains the subdirectory and use the
			// function home_url() instead.
			$site_url = (new Transifex_Live_Integration_WP_Services())->get_site_url($is_subdirectory_install);
		} else {
			$site_url = 'http://www.mydomain.com';
		}
		return $site_url . '/%LANG%';
	}

	/**
	 * Static function for settings defaults
	 * @return array Returns the settings array
	 */
	static function settings() {
		return array(
			'debug' => '0',
			'api_key' => null, // This is the only required field and needs to be copied from Live
			'enable_staging' => 0,
			// Wordpress is installed in a subdirectory like www.mydomain.com/cms but the site is accessible from www.mydomain.com
			'is_subdirectory_install' => 0,
			'translate_urls' => false,
      'canonical_urls' => false,
			'previous_api_key' => null,
			'raw_transifex_languages' => null,
			'transifex_languages' => null,
			'language_lookup' => null,
			'language_map' => '[]',
			'hreflang_map' => '[]',
			'languages_regex' => null,
			'rewrite_option_all' => 0,
			'enable_custom_urls' => 0,
			'enable_tdk' => 0,
			'urls' => [
				'rate_us' => 'https://wordpress.org/support/view/plugin-reviews/transifex-live-integration?rate=5#postform',
				'api_key_landing_page' => 'https://app.transifex.com/signup/?utm_source=liveplugin',
				'translate_urls' => 'https://help.transifex.com/en/articles/6259014-technical-instructions#h_e9a258ad3b',
			],
			'enable_language_urls' => false,
			'enable_picker' => false,
			'add_language_rewrites' => 'none selected',
			'source_language' => '',
			'languages' => '',
			'hreflang' => false,
			'url_options' => 1,
			'source_alias' => 'www',
			// calc_default_subdomain('www') won't work for domain names starting with non-www prefixes
			'subdomain_pattern' => self::calc_default_subdomain('www'),
			'subdirectory_pattern' => self::calc_default_subdirectory(),
			'static_frontpage_support' => false,
			'enable_prerender' => 0,
			'prerender_url' => '',
			'whitelist_crawlers' =>
			'googlebot|yahoo|bingbot|baiduspider|facebookexternalhit|twitterbot|rogerbot|linkedinbot|embedly|quora link preview|showyoubot|outbrain|pinterest/0.|developers.google.com/+/web/snippet|slackbot|vkshare|w3c_validator|redditbot|applebot|whatsapp|flipboard',
			'generic_bot_types' => 'bot|crawl|slurp|spider',
			'enable_prerender_check' => 1,
			'prerender_header_check_key' => 'X-Prerender-Req',
			'prerender_header_check_value' => 'TRUE',
			'prerender_enable_response_header' => 0,
			'prerender_response_headers' => '{"Expires" : "Tue, 03 Jul 2001 06:00:00 GMT", "Last-Modified" : "{now} GMT","Cache-Control":"max-age=0, no-cache, must-revalidate, proxy-revalidate"}',
			'prerender_enable_vary_header' => 1,
			'prerender_vary_header_value' => 'User-Agent,X-Prerender-Req',
			'prerender_enable_cookie' => 0,
			'prerender_cookie' => '{"wordpress_test_cookie" : "WP+Cookie+check"}'
		);
	}

}
