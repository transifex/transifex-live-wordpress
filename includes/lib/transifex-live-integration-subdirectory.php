<?php

/**
 * Language rewrites
 * @package TransifexLiveIntegration
 */

/**
 * Static class for subdirectory rewrite functions
 */
class Transifex_Live_Integration_Subdirectory {

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

	/**
	 * Regex used by rewrite for languages
	 * @var string
	 */
	private $languages_regex;
	private $languages_map;
	private $lang;
	public $rewrite_options;

	/**
	 * Private constructor, initializes local vars based on settings
	 * @param array $settings Associative array used to store plugin settings.
	 */
	public function __construct( $settings, $rewrite_options ) {
		Plugin_Debug::logTrace();
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/common/transifex-live-integration-validators.php';
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/override/transifex-live-integration-generate-rewrite-rules.php';
		$this->rewrite_options = [ ];
		$this->languages_regex = $settings['languages_regex'];
		$this->source_language = $settings['source_language'];
		$this->languages_map = json_decode( $settings['language_map'], true )[0];
		$this->lang = false;
		if ( isset( $rewrite_options['add_rewrites_post'] ) ) {
			$this->rewrite_options[] = ($rewrite_options['add_rewrites_post']) ? 'post' : '';
		}
		if ( isset( $rewrite_options['add_rewrites_root'] ) ) {
			$this->rewrite_options[] = ($rewrite_options['add_rewrites_root']) ? 'root' : '';
		}
		if ( isset( $rewrite_options['add_rewrites_date'] ) ) {
			$this->rewrite_options[] = ($rewrite_options['add_rewrites_date']) ? 'date' : '';
		}
		if ( isset( $rewrite_options['add_rewrites_page'] ) ) {
			$this->rewrite_options[] = ($rewrite_options['add_rewrites_page']) ? 'page' : '';
		}
		if ( isset( $rewrite_options['add_rewrites_author'] ) ) {
			$this->rewrite_options[] = ($rewrite_options['add_rewrites_author']) ? 'author' : '';
		}
		if ( isset( $rewrite_options['add_rewrites_tag'] ) ) {
			$this->rewrite_options[] = ($rewrite_options['add_rewrites_tag']) ? 'tag' : '';
		}
		if ( isset( $rewrite_options['add_rewrites_category'] ) ) {
			$this->rewrite_options[] = ($rewrite_options['add_rewrites_category']) ? 'category' : '';
		}
		if ( isset( $rewrite_options['add_rewrites_search'] ) ) {
			$this->rewrite_options[] = ($rewrite_options['add_rewrites_search']) ? 'search' : '';
		}
		if ( isset( $rewrite_options['add_rewrites_feed'] ) ) {
			$this->rewrite_options[] = ($rewrite_options['add_rewrites_feed']) ? 'feed' : '';
		}
		if ( isset( $rewrite_options['add_rewrites_permalink_tag'] ) ) {
			$this->rewrite_options[] = ($rewrite_options['add_rewrites_permalink_tag']) ? 'permalink_tag' : '';
		}
		if ( !empty( $settings['languages'] ) ) {
			$b = strpos( ',', $settings['languages'] );
			if ( false === $b ) {
				$this->language_codes = array( $settings['languages'] );
			} else {
				$this->language_codes = explode( ',', $settings['languages'] );
			}
		}
	}

	/*
	 * WP wp action hook, initializes language from query
	 */

	function wp_hook() {
		Plugin_Debug::logTrace();
		$this->lang = get_query_var( 'lang' );
	}

	/**
	 * WP init action hook, adds lang as a query parameter
	 */
	function init_hook() {
		Plugin_Debug::logTrace();
		add_rewrite_tag( '%lang%', $this->languages_regex, 'lang=' );
	}

	/**
	 * Callback function to the WP parse_query hook
	 * @param array $query WP query object.
	 */
	function parse_query_hook( $query ) {
		if ( !Transifex_Live_Integration_Validators::is_query_ok( $query ) ) {
			return $query;
		}
		$qv = &$query->query_vars;
		$qv['lang'] = isset( $query->query_vars['lang'] ) ? $query->query_vars['lang'] : $this->source_language;
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
				$wp_query->is_page = false;
				$wp_query->is_home = true;
				$wp_query->is_posts_page = true;
			} else {
				$wp_query->is_page = true;
				$wp_query->is_home = false;
				$wp_query->is_singular = true;
				$query->set( 'page_id', get_option( 'page_on_front' ) );
			}
		}
	}

	/**
	 * Function to build page permastructs
	 * @return string permastruct
	 */
	function generate_post_permastruct() {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		$p = $wp_rewrite->permalink_structure;
		$pp = '%lang%' . $p;
		return $pp;
	}

	/**
	 * Callback function to the WP page_rewrite_rules
	 * @param array $rules Associative array of rewrite rules in WP.
	 * @return array Returns filtered rules array
	 */
	function post_rewrite_rules_hook( $rules ) {
		if ( !Transifex_Live_Integration_Validators::is_rules_ok( $rules ) ) {
			return $rules;
		}
		global $wp_rewrite;
		$wp_rewrite->add_rewrite_tag( '%lang%', $this->languages_regex, 'lang=' );
		$pp = $this->generate_post_permastruct();
		$this->post_permastruct = $pp;
		$rr = Transifex_Live_Integration_Generate_Rewrite_Rules::generate_rewrite_rules( $pp, EP_PERMALINK, true, false, false, true );
		$rewrite = array_merge( $rr, $rules );
		
		// Handle custom post types from custom filter
		$custom_rewrite = $this->custom_type_rules_hook();
		$rewrite = array_merge( $custom_rewrite, $rewrite);
		
		return $rewrite;
	}
	
	/**
	 * Callback function to the WP page_rewrite_rules
	 *
	 * It applies a custom filter, allowing 3rd party modules to add their own
	 * permalink rewrite rules.
	 *
	 * @return array Returns custom rewrite rules
	 */
	 function custom_type_rules_hook(){
		// Get custom rules from 3rd party module, if our own filter is being used
		$custom_types_array = array();
		$custom_types_array = apply_filters('transifex_generate_rewrite_rules', $custom_types_array);
		$rewrite_array = array();
		foreach($custom_types_array as $custom_type_regex => $custom_type_action){
			// Replace the lang placeholder with the language regex for this configuation
			$custom_type_regex = str_replace("%lang%", $this->languages_regex, $custom_type_regex);
			// Add permalink to array
			$rewrite_array[$custom_type_regex] = $custom_type_action;
		}
		return $rewrite_array;
	}

	/**
	 * Function to build page permastructs
	 */
	function generate_date_permastruct() {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		$p = $wp_rewrite->get_date_permastruct();
		$pp = '%lang%' . $p;
		return $pp;
	}

	/**
	 * Callback function to the WP page_rewrite_rules
	 * @param array $rules Associative array of rewrite rules in WP.
	 */
	function date_rewrite_rules_hook( $rules ) {
		if ( !Transifex_Live_Integration_Validators::is_rules_ok( $rules ) ) {
			return $rules;
		}
		global $wp_rewrite;
		$wp_rewrite->add_rewrite_tag( '%lang%', $this->languages_regex, 'lang=' );
		$pp = $this->generate_date_permastruct();
		$rr = Transifex_Live_Integration_Generate_Rewrite_Rules::generate_rewrite_rules( $pp, EP_DATE, true, false, false, false );
		$rewrite = array_merge( $rr, $rules );
		return $rewrite;
	}

	/**
	 * Function to build page permastructs
	 */
	function generate_page_permastruct() {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		$p = $wp_rewrite->get_page_permastruct();
		$pp = '%lang%/' . $p;
		return $pp;
	}

	/**
	 * Callback function to the WP page_rewrite_rules
	 * @param array $rules Associative array of rewrite rules in WP.
	 * @return array Returns filtered rules
	 */
	function page_rewrite_rules_hook( $rules ) {
		if ( !Transifex_Live_Integration_Validators::is_rules_ok( $rules ) ) {
			return $rules;
		}
		global $wp_rewrite;
		$wp_rewrite->add_rewrite_tag( '%pagename%', '(.?.+?)', 'pagename=' );
		$wp_rewrite->add_rewrite_tag( '%lang%', $this->languages_regex, 'lang=' );
		$pp = $this->generate_page_permastruct();
		$rr = Transifex_Live_Integration_Generate_Rewrite_Rules::generate_rewrite_rules( $pp, EP_PAGES, true, false, false, false );
		$rewrite = array_merge( $rr, $rules );
		return $rewrite;
	}

	/**
	 * Function to build author permastructs
	 * @return string Returns updated permastruct
	 */
	function generate_author_permastruct() {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		$p = $wp_rewrite->get_author_permastruct();
		$pp = '%lang%' . $p;
		return $pp;
	}

	/**
	 * Callback function to the WP page_rewrite_rules
	 * @param array $rules Associative array of rewrite rules in WP.
	 */
	function author_rewrite_rules_hook( $rules ) {
		if ( !Transifex_Live_Integration_Validators::is_rules_ok( $rules ) ) {
			return $rules;
		}
		global $wp_rewrite;
		$wp_rewrite->add_rewrite_tag( '%lang%', $this->languages_regex, 'lang=' );
		$pp = $this->generate_author_permastruct();
		$rr = Transifex_Live_Integration_Generate_Rewrite_Rules::generate_rewrite_rules( $pp, EP_AUTHORS, true, false, false, false );
		$rewrite = array_merge( $rr, $rules );
		return $rewrite;
	}

	/**
	 * Function to build page permastructs
	 */
	function generate_tag_permastruct() {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		$p = $wp_rewrite->get_tag_permastruct();
		$pp = '%lang%' . $p;
		return $pp;
	}

	/**
	 * Callback function to the WP page_rewrite_rules
	 * @param array $rules Associative array of rewrite rules in WP.
	 */
	function tag_rewrite_rules_hook( $rules ) {
		if ( !Transifex_Live_Integration_Validators::is_rules_ok( $rules ) ) {
			return $rules;
		}
		global $wp_rewrite;
		$wp_rewrite->add_rewrite_tag( '%lang%', $this->languages_regex, 'lang=' );
		$pp = $this->generate_tag_permastruct();
		$rr = Transifex_Live_Integration_Generate_Rewrite_Rules::generate_rewrite_rules( $pp, EP_TAGS, true, false, false, false );
		$rewrite = array_merge( $rr, $rules );
		return $rewrite;
	}

	/**
	 * Function to build page permastructs
	 */
	function generate_category_permastruct() {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		$p = $wp_rewrite->get_category_permastruct();
		$pp = '%lang%' . $p;
		return $pp;
	}

	/**
	 * Callback function to the WP page_rewrite_rules
	 * @param array $rules Associative array of rewrite rules in WP.
	 */
	function category_rewrite_rules_hook( $rules ) {
		if ( !Transifex_Live_Integration_Validators::is_rules_ok( $rules ) ) {
			return $rules;
		}
		global $wp_rewrite;
		$wp_rewrite->add_rewrite_tag( '%lang%', $this->languages_regex, 'lang=' );
		$pp = $this->generate_category_permastruct();
		$rr = Transifex_Live_Integration_Generate_Rewrite_Rules::generate_rewrite_rules( $pp, EP_CATEGORIES, true, false, false, false );
		$rewrite = array_merge( $rr, $rules );
		return $rewrite;
	}

	/**
	 * Function to build page permastructs
	 */
	function generate_search_permastruct() {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		$p = $wp_rewrite->get_search_permastruct();
		$pp = '%lang%' . $p;
		return $pp;
	}

	/**
	 * Callback function to the WP page_rewrite_rules
	 * @param array $rules Associative array of rewrite rules in WP.
	 */
	function search_rewrite_rules_hook( $rules ) {
		if ( !Transifex_Live_Integration_Validators::is_rules_ok( $rules ) ) {
			return $rules;
		}
		global $wp_rewrite;
		$wp_rewrite->add_rewrite_tag( '%lang%', $this->languages_regex, 'lang=' );
		$pp = $this->generate_search_permastruct();
		$rr = Transifex_Live_Integration_Generate_Rewrite_Rules::generate_rewrite_rules( $pp, EP_SEARCH, true, false, false, false );
		$rewrite = array_merge( $rr, $rules );
		return $rewrite;
	}

	/**
	 * Function to build page permastructs
	 */
	function generate_feed_permastruct() {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		$p = $wp_rewrite->get_feed_permastruct();
		$pp = '%lang%' . $p;
		return $pp;
	}

	/**
	 * Callback function to the WP page_rewrite_rules
	 * @param array $rules Associative array of rewrite rules in WP.
	 */
	function feed_rewrite_rules_hook( $rules ) {
		if ( !Transifex_Live_Integration_Validators::is_rules_ok( $rules ) ) {
			return $rules;
		}
		global $wp_rewrite;
		$wp_rewrite->add_rewrite_tag( '%lang%', $this->languages_regex, 'lang=' );
		$pp = $this->generate_feed_permastruct();
		$rr = Transifex_Live_Integration_Generate_Rewrite_Rules::generate_rewrite_rules( $pp, EP_NONE, true, false, false, false );
		$rewrite = array_merge( $rr, $rules );
		return $rewrite;
	}

	/**
	 * Function to build 'all' = root permastructs
	 */
	function generate_root_permastruct() {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		return '%lang%' . $wp_rewrite->root . '/';
	}

	/**
	 * Callback function to the WP root_rewrite_rules
	 * @param array $rules Associative array of rewrite rules in WP.
	 */
	function root_rewrite_rules_hook( $rules ) {
		if ( !Transifex_Live_Integration_Validators::is_rules_ok( $rules ) ) {
			return $rules;
		}
		global $wp_rewrite;
		$wp_rewrite->add_rewrite_tag( '%lang%', $this->languages_regex, 'lang=' );
		$pp = $this->generate_root_permastruct();
		$rr = Transifex_Live_Integration_Generate_Rewrite_Rules::generate_rewrite_rules( $pp, EP_ROOT );
		$rewrite = array_merge( $rr, $rules );
		return $rewrite;
	}

}
