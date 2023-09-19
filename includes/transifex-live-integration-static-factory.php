<?php

/**
 * Static factory library functions 
 * @package TransifexLiveIntegration
 */
/*
 * Functions to instantiate plugin libraries
 */
class Transifex_Live_Integration_Static_Factory {
	/*
	 * Creates HREFLANG object
	 * @param array $settings Settings array from the db
	 * @return object/false Returns either new object or false
	 */

	static function create_hreflang( $settings, $rewrite_options ) {
		if ( !isset( $settings['api_key'] ) ) {
			Plugin_Debug::logTrace( 'settings[api_key] not set...skipping hreflang' );
			return false;
		}
		if ( !isset( $settings['languages'] ) ) {
			Plugin_Debug::logTrace( 'settings[languages] not set...skipping hreflang' );
			return false;
		}
		if ( $settings['url_options'] === '1' ) {
			Plugin_Debug::logTrace( 'settings[url_options] set to none...skipping hreflang' );
			return false;
		}
		if ( !isset( $settings['tokenized_url'] ) ) {
			Plugin_Debug::logTrace( 'settings[tokenized_url] not set...skipping hreflang' );
			return false;
		}
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/lib/transifex-live-integration-hreflang.php';
		return new Transifex_Live_Integration_Hreflang( $settings, $rewrite_options );
	}

	/*
	 * Creates live snippet library
	 * @param array $settings
	 * @return object/false Returns new onject or false
	 */

	static function create_live_snippet( $settings, $live_settings ) {
		Plugin_Debug::logTrace();

		if ( !isset( $settings['api_key'] ) ) {
			Plugin_Debug::logTrace( 'API key not set skipping live snippet' );
			return false;
		}

		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/lib/transifex-live-integration-javascript.php';
		return new Transifex_Live_Integration_Javascript( $settings, $live_settings );
	}

	/**
	 * Factory function to create a subdomain object
	 * @param array $settings Associative array used to store plugin settings.
	 */
	static function create_subdomains( $settings ) {
		Plugin_Debug::logTrace();
		if ( !isset( $settings['languages'] ) ) {
			Plugin_Debug::logTrace( 'settings[languages] not set' );
			return false;
		}

		if ( $settings['url_options'] != '2' ) {
			Plugin_Debug::logTrace( 'settings[url_options] not subdomain' );
			return false;
		}
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/lib/transifex-live-integration-subdomain.php';
		return new Transifex_Live_Integration_Subdomain( $settings );
	}
	/**
	 * Factory function to create a rewrite object
	 * @param array $settings Associative array used to store plugin settings.
	 */
	static function create_subdirectory( $settings, $rewrite_options ) {
		Plugin_Debug::logTrace();
		if ( !isset( $settings['languages'] ) ) {
			Plugin_Debug::logTrace( 'settings[languages] not set' );
			return false;
		}
		if ( !isset( $settings['languages_regex'] ) ) {
			Plugin_Debug::logTrace( 'settings[languages_regex] not set' );
			return false;
		}

		if ( $settings['url_options'] != '3' ) {
			Plugin_Debug::logTrace( 'settings[url_options] not subdirectory' );
			return false;
		}

		if ( !preg_match( TRANSIFEX_LIVE_INTEGRATION_REGEX_PATTERN_CHECK_PATTERN, $settings['languages_regex'] ) ) {
			Plugin_Debug::logTrace( 'settings[languages_regex] failed pattern check' );
			return false;
		}
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/lib/transifex-live-integration-subdirectory.php';
		return new Transifex_Live_Integration_Subdirectory( $settings, $rewrite_options );
	}
	
	/**
	 * Factory function to create a rewrite object
	 * @param array $settings Associative array used to store plugin settings.
	 */
	static function create_rewrite( $settings, $rewrite_options ) {
		Plugin_Debug::logTrace();
		if ( !isset( $settings['languages'] ) ) {
			Plugin_Debug::logTrace( 'settings[languages] not set' );
			return false;
		}
		if ( !isset( $settings['languages_regex'] ) ) {
			Plugin_Debug::logTrace( 'settings[languages_regex] not set' );
			return false;
		}

		if ( $settings['url_options'] == '1' ) {
			Plugin_Debug::logTrace( 'settings[url_options] is disabled' );
			return false;
		}

		if ( !preg_match( TRANSIFEX_LIVE_INTEGRATION_REGEX_PATTERN_CHECK_PATTERN, $settings['languages_regex'] ) ) {
			Plugin_Debug::logTrace( 'settings[languages_regex] failed pattern check' );
			return false;
		}
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/lib/transifex-live-integration-rewrite.php';
		return new Transifex_Live_Integration_Rewrite( $settings, $rewrite_options );
	}

	/*
	 * Creates language picker snippet library
	 * @param array $settings
	 * @return object/false Returns new onject or false
	 */

	static function create_picker( $settings ) {
		Plugin_Debug::logTrace();
		if ( !isset( $settings['enable_picker'] ) ) {
			Plugin_Debug::logTrace( 'settings[enable_picker] not set' );
			return false;
		}
		if ( !($settings['enable_picker']) ) {
			Plugin_Debug::logTrace( 'settings[enable_picker] not truthy' );
			return false;
		}
		if ( !isset( $settings['tokenized_url'] ) || !( $settings['tokenized_url'] ) ) {
			Plugin_Debug::logTrace( 'settings[tokenized_url] not set and not truthy' );
			return false;
		}
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/lib/transifex-live-integration-picker.php';
		return new Transifex_Live_Integration_Picker( 
			$settings['language_map'], $settings['tokenized_url'], $settings['enable_picker'], 
			$settings['source_language'], $settings['is_subdirectory_install'] );
	}

	/*
	 * Creates prerender library
	 * @param array $settings
	 * @return object/false Returns new onject or false
	 */

	static function create_prerender( $settings ) {
		Plugin_Debug::logTrace();
		if ( !isset( $settings['enable_prerender'] ) ) {
			Plugin_Debug::logTrace( 'prerender not enabled, skipping prerender' );
			return false;
		}
		if ( !isset( $settings['prerender_url'] ) ) {
			Plugin_Debug::logTrace( 'prerender url not set, skipping prerender' );
			return false;
		}
		if ( !isset( $settings['url_options'] ) ) {
			Plugin_Debug::logTrace( 'No URL option set, skipping prerender' );
			return false;
		}

		if ( $settings['url_options'] !== '2' && $settings['url_options'] != '3' ) {
			Plugin_Debug::logTrace( 'URL option is none, skipping prerender' );
			return false;
		}

		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/lib/transifex-live-integration-prerender.php';
		$enable_prerender_check = (isset( $settings['enable_prerender_check'] )) ? true : false;
		return new Transifex_Live_Integration_Prerender( $settings['prerender_url'], $enable_prerender_check, $settings );
	}

}
