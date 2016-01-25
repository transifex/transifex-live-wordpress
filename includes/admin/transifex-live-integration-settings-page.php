<?php

include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-settings-util.php';

class Transifex_Live_Integration_Settings_Page {

	/**
	 * Function that handles loading setting data and displaying the admin page.
	 */
	static function options_page() {
		Plugin_Debug::logTrace();
		$db_settings = get_option( 'transifex_live_settings', array() );
		if ( !$db_settings ) {
			$db_settings = Transifex_Live_Integration_Defaults::settings();
		}

		$settings = array_merge( Transifex_Live_Integration_Defaults::settings(), $db_settings );

		$is_update_transifex_languages = false;
		if ( isset( $settings['api_key'] ) && // Initialize Live languages after API key is setup.
				( '' === $settings['raw_transifex_languages'] ) ) { // TODO: This seems brittle add more safety.
			Plugin_Debug::logTrace("initial api_key set...updating transifex languages");
			$is_update_transifex_languages = true;
				}

		if (isset($settings['transifex_languages_refresh'])) {
			Plugin_Debug::logTrace("sync button...updating transifex languages");
			$is_update_transifex_languages = true;
			unset($settings['transifex_live_settings']['transifex_languages_refresh']);
		}

		if (strcmp($settings['api_key'],$settings['previous_api_key'] )!==0){
			Plugin_Debug::logTrace("api_key updated...updating transifex languages");
			$is_update_transifex_languages = true;
		}

		if ($is_update_transifex_languages) {
			$raw_api_response_check = Transifex_Live_Integration_Settings_Util::get_raw_transifex_languages( $settings['api_key'] );
			$raw_api_response = $raw_api_response_check ? $raw_api_response_check : null;
			if ( isset( $raw_api_response ) ) {
				// TODO Thesee are used in the template below should be refactored.
				$raw_transifex_languages = $raw_api_response;
				$settings['source_language'] = Transifex_Live_Integration_Settings_Util::get_source( $raw_api_response );
				$languages = Transifex_Live_Integration_Settings_Util::get_default_languages( $raw_api_response );
				$language_lookup = Transifex_Live_Integration_Settings_Util::get_language_lookup( $raw_api_response );
			}
		}
		// TODO Thesee are used in the template below should be refactored.
		if ( !isset( $raw_transifex_languages ) ) {
			$raw_transifex_languages = stripslashes( $settings['raw_transifex_languages'] );
		}
		if ( !isset( $languages ) ) {
			$languages = explode( ",", $settings['transifex_languages'] );
		}

		if ( !isset( $language_lookup ) ) {
			$language_lookup = json_decode( stripslashes( $settings['language_lookup'] ), true );
		}

		ob_start();
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-settings-template.php';
		$content = ob_get_clean();
		echo $content;
	}

	/**
	 * Function that handles saving the setting data and sanitization.
	 */
	public function update_settings() {

		Plugin_Debug::logTrace();
		// TODO: Revisit use of Global POST object...maybe there is a WP API that can be used?
		if ( isset( $_POST['transifex_live_nonce'] ) && wp_verify_nonce( $_POST['transifex_live_nonce'], 'transifex_live_settings' ) ) {
			$settings = Transifex_Live_Integration_Settings_Page::sanitize_settings( $_POST );
			
			
			if (isset($settings['sync'])) {
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

						Plugin_Debug::logTrace('check');
			if (isset($languages_regex)){
				Plugin_Debug::logTrace('regex exists');
			$array_url = explode( "/", site_url() );
			$array_domain = explode( ".", $array_url[2]);
			$array_domain[0] = $languages_regex;
			$array_url[2] =  implode('.',$array_domain);
			$subdomain_pattern = implode( '/', $array_url );
			}
			
			$settings['transifex_live_settings']['subdomain_pattern'] = $subdomain_pattern;
			$settings['transifex_live_settings']['languages_map'] = $languages_map_string;
			$settings['transifex_live_settings']['languages_regex'] = $languages_regex;
			$settings['transifex_live_settings']['languages'] = $languages;
			if ( isset( $settings['transifex_live_settings'] ) ) {
				update_option( 'transifex_live_settings', $settings['transifex_live_settings'] );
			}
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

		$is_api_key_set_notice = (!isset($settings['api_key']))?true:false;

		$is_transifex_languages_set_notice = false;
		$is_transifex_languages_match = false;

		if (isset($settings['transifex_languages_refresh'])) {
			$is_admin_languages_refresh_notice = true;
		}
		
		if ( ! $is_api_key_set_notice ) {
			$is_transifex_languages_set_notice = (!isset($settings['raw_transifex_languages']))?true:false;
			if (isset($settings['raw_transifex_languages'])) {
			$is_transifex_languages_match = Transifex_Live_Integration_Settings_Util::check_raw_transifex_languages( $settings['api_key'], $settings['raw_transifex_languages'] );
			}

			}

		$notice = '';
		if ( isset( $_POST['transifex_live_settings'] ) && !$is_admin_languages_refresh_notice) {
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

		if ( $is_api_key_set_notice ){
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
