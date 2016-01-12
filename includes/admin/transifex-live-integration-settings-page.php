<?php

include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-settings-util.php';

class Transifex_Live_Integration_Settings_Page {

	static function options_page() {
		Plugin_Debug::logTrace();
		$db_settings = get_option( 'transifex_live_settings', array() );
		if ( !$db_settings ) {
			$db_settings = Transifex_Live_Integration_Defaults::settings();
		}
		
		$db_colors = array_map( 'esc_attr', (array) get_option( 'transifex_live_colors', array() ) );
		if ( empty($db_colors)) {
			$db_colors = Transifex_Live_Integration_Defaults::settings()['colors'];
		}
		$colors_colors = ['colors' => $db_colors ];
		$raw_settings = array_merge( $db_settings, $colors_colors );
		$settings = array_merge( Transifex_Live_Integration_Defaults::settings(), $raw_settings );
		
		if ( isset( $settings['api_key'] ) &&  // Initialize Live languages after API key is setup
				( $settings['raw_transifex_languages']=="" ) ) {
				$raw_api_response_check = Transifex_Live_Integration_Settings_Util::get_raw_transifex_languages( $settings['api_key'] );
				$raw_api_response = $raw_api_response_check?$raw_api_response_check:null;
				if ( isset( $raw_api_response ) ) {
					// TODO Thesee are used in the template below should be refactored - Mjj
					$raw_transifex_languages = $raw_api_response;
					$settings['source_language'] = Transifex_Live_Integration_Settings_Util::get_source( $raw_api_response );
					$languages = Transifex_Live_Integration_Settings_Util::get_default_languages( $raw_api_response );
					$language_lookup = Transifex_Live_Integration_Settings_Util::get_language_lookup( $raw_api_response );
				}
			}
		// TODO Thesee are used in the template below should be refactored - Mjj
		if (!isset($raw_transifex_languages) ){
			$raw_transifex_languages = stripslashes($settings['raw_transifex_languages']);
		}
		if (!isset($languages)){
			$languages = explode(",",$settings['transifex_languages']);
		}

		if (!isset($language_lookup) ){
			$language_lookup = json_decode(stripslashes($settings['language_lookup']), true);
		}
		
		ob_start();
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/admin/transifex-live-integration-settings-template.php';
		$content = ob_get_clean();
		echo $content;
	}

	public function update_settings() {

		Plugin_Debug::logTrace();
		if ( isset( $_POST['transifex_live_nonce'] ) && wp_verify_nonce( $_POST['transifex_live_nonce'], 'transifex_live_settings' ) ) {
			$settings = Transifex_Live_Integration_Settings_Page::sanitize_settings( $_POST );
			$transifex_languages = explode(",",$settings['transifex_live_settings']['transifex_languages']);
			$languages_regex = '';
			$languages_map = [];
			$languages = '';
			$trim = false;
			foreach ($transifex_languages as $lang) {
				$trim = true;
				$languages .= $settings['transifex_live_settings']['wp_language_'.$lang];
			    $languages .= ",";
				$languages_regex .= $settings['transifex_live_settings']['wp_language_'.$lang];
			    $languages_regex .= "|";
				$languages_map [$lang] = $settings['transifex_live_settings']['wp_language_'.$lang];
			}
			$languages = ($trim)?rtrim($languages, ","):'';
			$languages_regex = ($trim)?rtrim($languages_regex, "|"):'';
			$languages_regex = "(".$languages_regex.")";
			$languages_map_string = htmlentities(json_encode($languages_map));
			
			Plugin_Debug::logTrace($languages_regex);
			Plugin_Debug::logTrace($languages_map_string);
			Plugin_Debug::logTrace($languages);
			
			$settings['transifex_live_settings']['languages_map'] = $languages_map_string;
			$settings['transifex_live_settings']['languages_regex'] = $languages_regex;
			$settings['transifex_live_settings']['languages'] = $languages;
			if ( isset( $settings['transifex_live_settings'] ) ) {
				update_option( 'transifex_live_settings', $settings['transifex_live_settings'] );
			}

			if ( isset( $settings['transifex_live_colors'] ) ) {
				update_option( 'transifex_live_colors', $settings['transifex_live_colors'] );
			}

			add_action( 'admin_notices', array( 'Transifex_Live_Integration_Settings_Page', 'admin_notices' ) );
		}
	}

	public function admin_notices() {
		$notice = '';
		if ( isset( $_POST['transifex_live_settings'] ) ) {
			$notice = '<p>' . __( 'Your changes to the settings have been saved!', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ) . '</p>';
		}

		if ( isset( $_POST['transifex_live_colors'] ) ) {
			$notice .= '<p>' . __( 'Your changes to the colors have been saved!', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ) . '</p>';
		}

		echo '<div class="notice">' . $notice . '</div>';
	}

	static public function sanitize_settings( $settings ) {
		Plugin_Debug::logTrace();
		$settings['transifex_live_settings']['api_key'] = ( isset( $settings['transifex_live_settings']['api_key'] )) ? sanitize_text_field( $settings['transifex_live_settings']['api_key'] ) : '';
		$settings['transifex_live_settings']['staging'] = ( $settings['transifex_live_settings']['staging'] ) ? 1 : 0;
		$settings['transifex_live_settings']['enable_frontend_css'] = ( $settings['transifex_live_settings']['enable_frontend_css'] ) ? 1 : 0;
		$settings['transifex_live_settings']['custom_picker_id'] = ( isset( $settings['transifex_live_settings']['custom_picker_id'] )) ? sanitize_text_field( $settings['transifex_live_settings']['custom_picker_id'] ) : '';
		$settings['transifex_live_settings']['raw_transifex_languages'] = ( isset( $settings['transifex_live_settings']['raw_transifex_languages'] )) ? sanitize_text_field( $settings['transifex_live_settings']['raw_transifex_languages'] ) : '';
		$settings['transifex_live_settings']['languages'] = ( isset( $settings['transifex_live_settings']['languages'] )) ? sanitize_text_field( $settings['transifex_live_settings']['languages'] ) : '';
		$settings['transifex_live_settings']['language_lookup'] = ( isset( $settings['transifex_live_settings']['language_lookup'] )) ? sanitize_text_field( $settings['transifex_live_settings']['language_lookup'] ) : '';
		
		
		$settings['transifex_live_colors']['accent'] = Transifex_Live_Integration_Settings_Util::sanitize_hex_color( $settings['transifex_live_colors']['accent'] );
		$settings['transifex_live_colors']['text'] = Transifex_Live_Integration_Settings_Util::sanitize_hex_color( $settings['transifex_live_colors']['text'] );
		$settings['transifex_live_colors']['background'] = Transifex_Live_Integration_Settings_Util::sanitize_hex_color( $settings['transifex_live_colors']['background'] );
		$settings['transifex_live_colors']['menu'] = Transifex_Live_Integration_Settings_Util::sanitize_hex_color( $settings['transifex_live_colors']['menu'] );
		$settings['transifex_live_colors']['languages'] = Transifex_Live_Integration_Settings_Util::sanitize_hex_color( $settings['transifex_live_colors']['languages'] );
		return $settings;
	}

}
