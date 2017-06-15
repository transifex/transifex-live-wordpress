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

	/*
	 * Specifies regex language pattern for subdomain
	 * @var string
	 */
	private $subdomain_pattern;

	/**
	 * Private constructor, initializes local vars based on settings
	 * @param array $settings Associative array used to store plugin settings.
	 */
	public function __construct( $settings ) {
		Plugin_Debug::logTrace();
		$this->subdomain_pattern = '/' . html_entity_decode( str_replace( '/', '\/', $settings['subdomain_pattern'] ) ) . '/';
		Plugin_Debug::logTrace( $this->subdomain_pattern );
		$this->source_language = $settings['source_language'];
		if ( !empty( $settings['languages'] ) ) {
			$b = strpos( ',', $settings['languages'] );
			if ( false === $b ) {
				$this->language_codes = array( $settings['languages'] );
			} else {
				$this->language_codes = explode( ',', $settings['languages'] );
			}
		}
	}

	/**
	 * Callback function to the WP parse_query hook
	 * @param array $query WP query object.
	 * @return object filtered query
	 */
	function parse_query_hook( $query ) {
		$m = array();
		Plugin_Debug::logTrace('checking subdomain:'.$this->subdomain_pattern);
        Plugin_Debug::logTrace(site_url());
		if ( preg_match( $this->subdomain_pattern, site_url(), $m ) ) {
			Plugin_Debug::logTrace($m);
			$query->query_vars['lang'] = $m[1];
		} else {
            if ( isset($query->query['lang']) ) {
                $query->query_vars['lang'] = $query->query['lang'];
            } else {
                $query->query_vars['lang'] = $this->source_language;
            }
		}
		return $query;
	}

	/*
	 * WP parse_query filter,additional logic to support localized static frontpages
	 * @param array $query WP query object.
	 * @return array Returns the filtered query object
	 */
	function parse_query_root_hook( $query ) {
		global $wp_query;
		$check_for_lang = ($query->get( 'lang' ) !== $this->source_language) ? true : false;
		$check_page = (null !== $query->get( 'page' ) ) ? true : false;
		$check_pagename = ($query->get( 'pagename' )) ? true : false;
		$check_page_on_front = (get_option( 'page_on_front' )) ? true : false;
		if ( $check_for_lang && $check_page_on_front && $wp_query->is_home ) {
			if ( $check_page && $check_pagename ) {
                Plugin_Debug::logTrace('check page and check pagename');
				$wp_query->is_page = false;
				$wp_query->is_home = true;
				$wp_query->is_posts_page = true;
			} else {
                Plugin_Debug::logTrace('else check page and check pagename');
				$wp_query->is_page = true;
				$wp_query->is_home = false;
				$wp_query->is_singular = true;
				$query->set( 'page_id', get_option( 'page_on_front' ) );
			}
		}
	}

}
