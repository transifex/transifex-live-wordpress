<?php

class Transifex_Live_Integration_Static_Factory {

	static function create_hreflang( $settings ) {
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
		return new Transifex_Live_Integration_Hreflang( $settings );
	}

	static function create_live_snippet( $settings ) {
		Plugin_Debug::logTrace();

		if ( !isset( $settings['api_key'] ) ) {
			Plugin_Debug::logTrace( 'API key not set skipping live snippet' );
			return false;
		}

		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/lib/transifex-live-integration-javascript.php';
		return new Transifex_Live_Integration_Javascript( $settings );
	}

	/**
	 * Factory function to create a rewrite object
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

		if ( $settings['url_options'] != '3' ) {
			Plugin_Debug::logTrace( 'settings[url_options] not subdirectory' );
			return false;
		}

		if ( !preg_match( TRANSIFEX_LIVE_INTEGRATION_REGEX_PATTERN_CHECK_PATTERN, $settings['languages_regex'] ) ) {
			Plugin_Debug::logTrace( 'settings[languages_regex] failed pattern check' );
			return false;
		}
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/lib/transifex-live-integration-rewrite.php';
		return new Transifex_Live_Integration_Rewrite( $settings, $rewrite_options );
	}

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
		return new Transifex_Live_Integration_Picker( $settings['language_map'], $settings['tokenized_url'], $settings['enable_picker'], $settings['source_language'] );
	}

	static function create_prerender( $settings ) {
		Plugin_Debug::logTrace();
		if (!isset($settings['enable_prerender'])) {
			Plugin_Debug::logTrace('prerender not enabled, skipping prerender');
			return false;
		}
		if (!isset($settings['prerender_url'])) {
			Plugin_Debug::logTrace('prerender url not set, skipping prerender');
			return false;
		}
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-util.php';
		$agent = Transifex_Live_Integration_Util::get_user_agent();
		$req_escaped_fragment = (isset( $_GET['_escaped_fragment_'] )) ? $_GET['_escaped_fragment_'] : false;

		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/lib/transifex-live-integration-prerender.php';
		$check = Transifex_Live_Integration_Util::prerender_check( $agent, $req_escaped_fragment, $settings['generic_bot_types'], $settings['whitelist_crawlers'] );
		return ($check) ? new Transifex_Live_Integration_Prerender($settings['prerender_url']) : false;
	}

}
