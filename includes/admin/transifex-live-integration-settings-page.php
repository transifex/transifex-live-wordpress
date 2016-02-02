<?php

include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-settings-util.php';

class Transifex_Live_Integration_Settings_Page {


	static function load_settings() {
		Plugin_Debug::logTrace();
		$db_settings = get_option( 'transifex_live_settings', array() );
		if ( !$db_settings ) {
			$db_settings = Transifex_Live_Integration_Defaults::settings();
		}

		return array_merge( Transifex_Live_Integration_Defaults::settings(), $db_settings );
		
	}
	
	static function load_rewrite_options() {
		
		$db_opt_settings = get_option( 'transifex_live_options', array() );
		if ( !$db_opt_settings  ) {
			
			$opt_settings  = Transifex_Live_Integration_Defaults::options_values();
		}
		
		return array_merge( Transifex_Live_Integration_Defaults::options_values(), $db_opt_settings );
	}
	
	static function options_page() {
		
		$settings = self::load_settings();
		$rewrite_options = self::load_rewrite_options();

		$rewrite_options_array = [];
		foreach ($rewrite_options as $key => $value) {
			$arr = [ ];
			$arr['checked'] = $value;
			$arr['text'] = Transifex_Live_Integration_Defaults::get_options_text( $key );
			$arr['id'] = 'transifex_live_options_' . $key;
			$arr['name'] = 'transifex_live_options[' . $key . ']';
			array_push( $rewrite_options_array, $arr );
		}

		$settings['source_language'] = 'en';
		$source_language = $settings['source_language'];
		$settings['transifex_languages']  = "ko,de_DE";
		$languages = explode( ",", $settings['transifex_languages']);
		$settings['language_lookup'] = '[{\"code\":\"zh_CN\",\"name\":\"Chinese (China)\"},{\"code\":\"de\",\"name\":\"German\"},{\"code\":\"de_DE\",\"name\":\"German (Germany)\"},{\"code\":\"ko\",\"name\":\"Korean\"}]';
		$language_lookup = json_decode( stripslashes( $settings['language_lookup'] ), true );
		$language_lookup = [];

		ob_start();
		checked( $settings['enable_custom_urls'] );
		$checked_custom_urls = ob_get_clean();
		$hide_custom_urls_css = ($settings['enable_custom_urls']) ? '' : ' hide-if-js';
		switch ($settings['url_options']) {
			case "1":
				$hide_add_rewrites = ' hide-if-js';
				break;
			case "2":
				$hide_add_rewrites = ' hide-if-js';
				break;
			case "3":
				$hide_add_rewrites = '';
				break;
		}

		ob_start();
		selected( $settings['url_options'], 2 );
		$url_options_subdomain = ob_get_clean();
		ob_start();
		selected( $settings['url_options'], 3 );
		$url_options_subdirectory = ob_get_clean();
		$site_url = site_url();
		$site_url_subdirectory_example = $site_url . '/fr';
		$site_url_array = explode( '/', $site_url );
		$site_url_array[2] = 'fr.' . $site_url_array[2];
		$site_url_subdomain_example = implode( '/', $site_url_array );
		

		ob_start();
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-settings-template.php';
		$content = ob_get_clean();
		echo $content;
	}

	public function admin_init_hook() {
		Plugin_Debug::logTrace();
		if ( isset( $_POST['transifex_live_nonce'] ) && wp_verify_nonce( $_POST['transifex_live_nonce'], 'transifex_live_settings' ) ) {
			self::update_settings(self::sanitize_settings( $_POST ));
		}
	}
	
	/**
	 * Function that handles saving the setting data and sanitization.
	 */
	static public function update_settings($settings) {
		Plugin_Debug::logTrace();
			if ( isset( $settings['sync'] ) ) {
				$settings['transifex_live_settings']['transifex_languages_refresh'] = true;
			}

			$transifex_languages = explode( ',', $settings['transifex_live_settings']['transifex_languages'] );
			$languages_regex = '';
			$languages_map = [ ];
			$languages = '';
			$trim = false;
			foreach ($transifex_languages as $lang) {
				$trim = true;
				$k = 'wp_language_' . $lang;
				if ( !isset( $settings['transifex_live_settings'][$k] ) ) {
					break;
				}
				$languages .= $settings['transifex_live_settings'][$k];
				$languages .= ',';
				$languages_regex .= $settings['transifex_live_settings'][$k];
				$languages_regex .= '|';
				$languages_map [$lang] = $settings['transifex_live_settings'][$k];
			}
			$languages = ($trim) ? rtrim( $languages, ',' ) : '';
			$languages_regex = ($trim) ? rtrim( $languages_regex, '|' ) : '';
			$languages_regex = '(' . $languages_regex . ')';
			$languages_map_string = htmlentities( json_encode( $languages_map ) ); // TODO: Switch to wp_json_encode.

			if ( isset( $languages_regex ) ) {
				Plugin_Debug::logTrace( 'regex exists' );
				$array_url = explode( "/", site_url() );
				$array_domain = explode( ".", $array_url[2] );
				$array_domain[0] = $languages_regex;
				$array_url[2] = implode( '.', $array_domain );
				$subdomain_pattern = implode( '/', $array_url );
			}

			$settings['transifex_live_settings']['subdomain_pattern'] = $subdomain_pattern;
			$settings['transifex_live_settings']['languages_map'] = $languages_map_string;
			$settings['transifex_live_settings']['languages_regex'] = $languages_regex;
			$settings['transifex_live_settings']['languages'] = $languages;
			if ( isset( $settings['transifex_live_settings'] ) ) {
				update_option( 'transifex_live_settings', $settings['transifex_live_settings'] );
			}

			if ( isset( $settings['transifex_live_options'] ) ) {
				Plugin_Debug::logTrace($settings['transifex_live_options']);
				update_option( 'transifex_live_options', $settings['transifex_live_options'] );
			}
		
	}

	/**
	 * Callback function that sets notifications in WP admin pages
	 */
	public function admin_notices_hook() {
		$is_admin_page_notice = false;

		$is_admin_dashboard_notice = false;

		$is_admin_languages_refresh_notice = false;

		// TODO: refactor this DB call to a better place.
		$settings = get_option( 'transifex_live_settings', array() );
		// TODO: might need to trap the state here when indices api_key or raw_transifex_languages are missing.

		$is_api_key_set_notice = (!isset( $settings['api_key'] )) ? true : false;

		$is_transifex_languages_set_notice = false;
		$is_transifex_languages_match = false;

		if ( isset( $settings['transifex_languages_refresh'] ) ) {
			$is_admin_languages_refresh_notice = true;
		}

		if ( !$is_api_key_set_notice ) {
			$is_transifex_languages_set_notice = (!isset( $settings['raw_transifex_languages'] )) ? true : false;
			if ( isset( $settings['raw_transifex_languages'] ) ) {
				$is_transifex_languages_match = Transifex_Live_Integration_Settings_Util::check_raw_transifex_languages( $settings['api_key'], $settings['raw_transifex_languages'] );
			}
		}

		$notice = '';
		if ( isset( $_POST['transifex_live_settings'] ) && !$is_admin_languages_refresh_notice ) {
			$is_admin_page_notice = true;
			$notice = '<p>' . __( 'Your changes to the settings have been saved!', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ) . '</p>';
		}

		if ( $is_admin_languages_refresh_notice ) {
			$is_admin_page_notice = true;
			$notice .= '<p>Languages list updated!</p>';
		}

		if ( $is_transifex_languages_set_notice ) {
			$is_admin_dashboard_notice = true;
			$notice .= '<p>There was a problem syncing with Transifex Live. Please try again in a bit, or <a href="https://www.transifex.com/contact/" target="_blank">contact us</a> if the issue persists.</p>';
		}

		if ( $is_api_key_set_notice ) {
			$is_admin_dashboard_notice = true;
			$notice .= "<p><strong>Thanks for installing the Transifex Live WordPress plugin!</strong> Add your API key to make translations live for your site.</p>";
		}

		if ( $is_transifex_languages_match ) {
			$is_admin_dashboard_notice = true;
			$notice .= "<p>Looks like there were some changes to your published languages. Click the <strong>Refresh Languages List<strong> button to update list of languages.</p>";
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
		$settings['transifex_live_settings']['languages'] = ( isset( $settings['transifex_live_settings']['languages'] )) ? sanitize_text_field( $settings['transifex_live_settings']['languages'] ) : '';
		$settings['transifex_live_settings']['language_lookup'] = ( isset( $settings['transifex_live_settings']['language_lookup'] )) ? sanitize_text_field( $settings['transifex_live_settings']['language_lookup'] ) : '';

		return $settings;
	}

}
