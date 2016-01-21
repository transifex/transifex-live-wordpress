<?php

/**
 * Language Subdomain
 * @package TransifexLiveIntegration
 */

/**
 * Module for settings subdomain
 */
class Transifex_Live_Integration_Subdomain {

	/**
	 * Source language used by rewrite
	 * @var string
	 */
	private $source_language;

	/**
	 * List of languages used by rewrite 
	 * @var array
	 */
	private $language_codes;
	
	private $subdomain_pattern;

	/**
	 * Private constructor, initializes local vars based on settings
	 * @param array $settings Associative array used to store plugin settings.
	 */
	private function __construct( $settings ) {
		Plugin_Debug::logTrace();
		$this->subdomain_pattern = '/'.html_entity_decode(str_replace('/','\/',$settings['subdomain_pattern'])) . '/';
		Plugin_Debug::logTrace($this->subdomain_pattern);
		$this->source_language = $settings['source_language'];
		if ( ! empty($settings['languages']  )) {
			$b = strpos( ',', $settings['languages'] );
			if ( false === $b ) {
				$this->language_codes = array( $settings['languages'] );
			} else {
				$this->language_codes = explode( ',', $settings['languages'] );
			}
		}
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
		
		if ( $settings['url_options'] != '2'  ) {
			Plugin_Debug::logTrace( 'settings[url_options] not subdomain' );
			return false;
		}

		return new Transifex_Live_Integration_Subdomain( $settings );
	}

	/**
	 * Callback function to the WP parse_query hook
	 * @param array $query WP query object.
	 */
	function parse_query_hook( $query ) {
		Plugin_Debug::logTrace();
		$m = array();
		if (preg_match($this->subdomain_pattern,site_url(),$m)){
			$query->query_vars['lang'] = $m[1];
		} else {
			$query->query_vars['lang'] = $this->source_language;
		}
		return $query;
	}
	

}
