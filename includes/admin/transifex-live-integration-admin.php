<?php

/**
 * Includes for Admin Page
 * @package TransifexLiveIntegration
 */

/**
 * Main Admin Class
 * All functions to render and update admin page
 */
class Transifex_Live_Integration_Admin {

	/**
	 * Loads plugin settings from db, merges with defaults if any are missing
	 * @return array List of all key->value settings
	 */
	static function load_settings() {
		Plugin_Debug::logTrace();
		$db_settings = get_option( 'transifex_live_settings', array() );
		if ( !$db_settings ) {
			$db_settings = Transifex_Live_Integration_Defaults::settings();
		}

		return array_merge( Transifex_Live_Integration_Defaults::settings(), $db_settings );
	}

	/**
	 * Loads subdirectory options from db, merges with default if any are missing
	 * @return array List of all key->value settings
	 */
	static function load_rewrite_options() {
		Plugin_Debug::logTrace();
		$db_opt_settings = get_option( 'transifex_live_options', array() );
		if ( !$db_opt_settings ) {

			$opt_settings = Transifex_Live_Integration_Defaults::options_values();
		}

		return array_merge( Transifex_Live_Integration_Defaults::options_values(), $db_opt_settings );
	}

	/**
	 * Loads Transifex Live Javascript settings from the db, merges with default if any are missing
	 * @return array List of all key->value settings
	 */
	static function load_transifex_settings() {
		Plugin_Debug::logTrace();
		$db_settings = get_option( 'transifex_live_transifex_settings', array() );
		if ( !$db_settings ) {

			$db_settings = Transifex_Live_Integration_Defaults::transifex_settings();
		}

		return array_merge( Transifex_Live_Integration_Defaults::transifex_settings(), $db_settings );
	}

	/**
	 * Renders admin page
	 */
	static function options_page() {
		Plugin_Debug::logTrace();
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-defaults.php';
		$settings = self::load_settings();
		$rewrite_options = self::load_rewrite_options();

		$rewrite_options_array = [ ];
		foreach ($rewrite_options as $key => $value) {
			$arr = [ ];
			$arr['checked'] = $value;
			$arr['text'] = Transifex_Live_Integration_Defaults::get_options_text( $key );
			$arr['id'] = 'transifex_live_options_' . $key;
			$arr['name'] = 'transifex_live_options[' . $key . ']';
			array_push( $rewrite_options_array, $arr );
		}

		$transifex_settings = self::load_transifex_settings();
		$transifex_settings_settings = $transifex_settings['settings'];

		ob_start();
		checked( $settings['rewrite_option_all'], 1 );
		$checked_rewrite_option_all = ob_get_clean();

		ob_start();
		checked( $settings['enable_prerender'], 1 );
		$checked_enable_prerender = ob_get_clean();
		
		ob_start();
		checked( $settings['enable_prerender_check'], 1 );
		$checked_enable_prerender_check = ob_get_clean();
		
		ob_start();
		checked( $settings['prerender_enable_response_header'], 1 );
		$checked_prerender_enable_response_header = ob_get_clean();

		ob_start();
		checked( $settings['prerender_enable_vary_header'], 1 );
		$checked_prerender_enable_vary_header = ob_get_clean();

		ob_start();
		checked( $settings['prerender_enable_cookie'], 1 );
		$checked_prerender_enable_cookie = ob_get_clean();

		ob_start();
		checked( $settings['static_frontpage_support'], 1 );
		$checked_static_frontpage_support = ob_get_clean();

		// These are used by the template: DO NOT REMOVE - Mjj 2/22/2016
		$languages = [ ];
		if ( $settings['transifex_languages'] !== '' ) {
			$languages = $settings['transifex_languages'];
		}

		$languages_regex = [ ];
		if ( $settings['languages_regex'] !== '' ) {
			$languages_regex = $settings['languages_regex'];
		}

		$source_language = '';
		if ( $settings['source_language'] !== '' ) {
			$source_language = $settings['source_language'];
		}

		$language_lookup = [ ];
		if ( $settings['language_lookup'] !== '' ) {
			$language_lookup = $settings['language_lookup'];
		}

		$language_map = [ ];
		if ( $settings['language_map'] !== '' ) {
			$language_map = $settings['language_map'];
		}
		
		$prerender_response_headers = '';
		if ( $settings['prerender_response_headers'] !== '' ) {
			$prerender_response_headers = $settings['prerender_response_headers'];
		}
		
		$prerender_cookie = '';
		if ( $settings['prerender_cookie'] !== '' ) {
			$prerender_cookie = $settings['prerender_cookie'];
		}		
		
		$checked_custom_urls = ($settings['enable_custom_urls'] === "1") ? "1" : "0";

		$url_options = $settings['url_options'];
		ob_start();
		checked( $settings['url_options'], '1' );
		$url_options_none = ob_get_clean();
		ob_start();
		checked( $settings['url_options'], '2' );
		$url_options_subdomain = ob_get_clean();
		ob_start();
		checked( $settings['url_options'], '3' );
		$url_options_subdirectory = ob_get_clean();
		
		
		$site_url = site_url();
		$site_url_subdirectory_example = $site_url . '/%lang%';
		$site_url_array = explode( '/', $site_url );
		$site_url_array[2] = '%lang%.' . $site_url_array[2];
		$site_url_subdomain_example = implode( '/', $site_url_array );


		ob_start();
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-admin-template.php';
		$content = ob_get_clean();
		echo $content;
	}

	/**
	 * A WP action hook to get the POSTd page, and call santitation for security and update
	 */
	static public function admin_init_hook() {
		Plugin_Debug::logTrace();
		if ( isset( $_POST['transifex_live_nonce'] ) && wp_verify_nonce( $_POST['transifex_live_nonce'], 'transifex_live_settings' ) ) {
			self::update_settings( self::sanitize_settings( $_POST ) );
		}
	}

	/**
	 * Updates db with settings information
	 * @param array $settings A List of all settings key->value arrays
	 * 'transifex_live_transifex_settings' = Settings for Javascript
	 * 'transifex_live_settings' = General plugin settings
	 * 'transifex_live_options' = Subdirectory options
	 */
	static public function update_settings( $settings ) {
		Plugin_Debug::logTrace();

		if ( isset( $settings['transifex_live_transifex_settings']['settings'] ) ) {
			$p = json_decode( $settings['transifex_live_transifex_settings']['settings'], true )['production']['picker'];
			$settings['transifex_live_settings']['enable_picker'] = ($p !== 'no-picker') ? true : false;
		}

		$transifex_languages = json_decode( stripslashes( $settings['transifex_live_settings']['transifex_languages'] ), true );
		$tokenized_url = Transifex_Live_Integration_Admin_Util::generate_tokenized_url( site_url(), $settings['transifex_live_settings']['url_options'] );
		$settings['transifex_live_settings']['tokenized_url'] = $tokenized_url;

		$languages_map = $settings['transifex_live_settings']['language_map'];
		$languages_map_string = $languages_map; // TODO: Switch to wp_json_encode.


		$languages_map = (array) json_decode( stripslashes( $languages_map ), true );
		$trim = false;

		$languages = '';
		$languages_regex = '';
		foreach ($transifex_languages as $lang) {
			$trim = true;
			$languages .= $languages_map[0][$lang];
			$languages .= ",";
			$languages_regex .= $languages_map[0][$lang];
			$languages_regex .= "|";
		}

		$languages = ($trim) ? rtrim( $languages, ',' ) : '';
		$languages_regex = ($trim) ? rtrim( $languages_regex, '|' ) : '';
		$languages_regex = '(' . $languages_regex . ')';


		if ( isset( $languages_regex ) ) {
			$array_url = explode( "/", site_url() );
			$array_domain = explode( ".", $array_url[2] );
			$array_domain[0] = $languages_regex;
			$array_url[2] = implode( '.', $array_domain );
			$subdomain_pattern = implode( '/', $array_url );
		}


		$settings['transifex_live_settings']['subdomain_pattern'] = $subdomain_pattern;
		$settings['transifex_live_settings']['languages_regex'] = $languages_regex;
		$settings['transifex_live_settings']['languages'] = $languages;
		if ( isset( $settings['transifex_live_settings'] ) ) {
			update_option( 'transifex_live_settings', $settings['transifex_live_settings'] );
		}

		if ( isset( $settings['transifex_live_options'] ) ) {
			update_option( 'transifex_live_options', $settings['transifex_live_options'] );
		}

		if ( isset( $settings['transifex_live_transifex_settings'] ) ) {
			update_option( 'transifex_live_transifex_settings', $settings['transifex_live_transifex_settings'] );
		}
	}

	/**
	 * Callback function that sets notifications in WP admin pages
	 */
	static public function admin_notices_hook() {
		Plugin_Debug::logTrace();
		$is_admin_page_notice = false;

		$is_admin_dashboard_notice = false;

		// TODO: refactor this DB call to a better place.
		$settings = get_option( 'transifex_live_settings', array() );
		// TODO: might need to trap the state here when indices api_key or raw_transifex_languages are missing.

		$is_api_key_set_notice = (!isset( $settings['api_key'] )) ? true : false;

		$notice = '';
		if ( isset( $_POST['transifex_live_settings'] ) ) {
			$is_admin_page_notice = true;
			$notice = '<p>' . __( 'Your changes to the settings have been saved!', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ) . '</p>';
		}

		if ( $is_api_key_set_notice ) {
			$is_admin_dashboard_notice = true;
			$notice .= "<p><strong>Thanks for installing the Transifex Live WordPress plugin!</strong> Add your API key to make translations live for your site.</p>";
		}

		if ( $is_admin_page_notice ) {
			echo '<div class="notice is-dismissable">' . $notice . '</div>';
		}
		if ( $is_admin_dashboard_notice ) {
			echo '<div class="clear"></div>';
			echo '<div class="update-nag is-dismissable">' . $notice . '</div>';
		}
	}

	/**
	 * Function called to ensure settings input is sane before saving to DB
	 * @param array $settings list of all settings saved in the WP DB.
	 */
	static public function sanitize_settings( $settings ) {
		Plugin_Debug::logTrace();
		$settings['transifex_live_settings']['api_key'] = ( isset( $settings['transifex_live_settings']['api_key'] )) ? sanitize_text_field( $settings['transifex_live_settings']['api_key'] ) : '';
		$settings['transifex_live_settings']['raw_transifex_languages'] = ( isset( $settings['transifex_live_settings']['raw_transifex_languages'] )) ? sanitize_text_field( $settings['transifex_live_settings']['raw_transifex_languages'] ) : '';
		$settings['transifex_live_settings']['languages'] = ( isset( $settings['transifex_live_settings']['languages'] )) ? sanitize_text_field( stripslashes( $settings['transifex_live_settings']['languages'] ) ) : '';
		$settings['transifex_live_settings']['language_lookup'] = ( isset( $settings['transifex_live_settings']['language_lookup'] )) ? sanitize_text_field( stripslashes( $settings['transifex_live_settings']['language_lookup'] ) ) : '';
		$settings['transifex_live_settings']['language_map'] = ( isset( $settings['transifex_live_settings']['language_map'] )) ? sanitize_text_field( stripslashes( $settings['transifex_live_settings']['language_map'] ) ) : '';
		$settings['transifex_live_settings']['source_language'] = ( isset( $settings['transifex_live_settings']['source_language'] )) ? sanitize_text_field( $settings['transifex_live_settings']['source_language'] ) : '';
		$settings['transifex_live_settings']['subdomain_pattern'] = ( isset( $settings['transifex_live_settings']['subdomain_pattern'] )) ? sanitize_text_field( $settings['transifex_live_settings']['subdomain_pattern'] ) : '';
		$settings['transifex_live_settings']['languages_regex'] = ( isset( $settings['transifex_live_settings']['languages_regex'] )) ? sanitize_text_field( $settings['transifex_live_settings']['languages_regex'] ) : '';
		$settings['transifex_live_settings']['transifex_languages'] = ( isset( $settings['transifex_live_settings']['transifex_languages'] )) ? sanitize_text_field( stripslashes( $settings['transifex_live_settings']['transifex_languages'] ) ) : '';


		$settings['transifex_live_transifex_settings']['settings'] = ( isset( $settings['transifex_live_transifex_settings']['settings'] )) ? sanitize_text_field( stripslashes( $settings['transifex_live_transifex_settings']['settings'] ) ) : '';
		return $settings;
	}

}