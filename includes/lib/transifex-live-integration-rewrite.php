<?php

/**
 * Language rewrites
 * @package TransifexLiveIntegration
 */

/**
 * Static class for subdirectory rewrite functions
 */
class Transifex_Live_Integration_Rewrite {

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
		if ( isset( $rewrite_options['add_rewrites_feed'] ) ) {
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
		if ( !Transifex_Live_Integration_Validators::is_query_ok( $query ) ) {
			return $query;
		}
		$qv = &$query->query_vars;
		if ( $query->is_home && 'page' == get_option( 'show_on_front' ) && get_option( 'page_on_front' ) ) {
			$query->is_page = true;
			$query->is_home = false;
// Dep'd 3/30/2016 Mjj			$qv['page_id'] = get_option( 'page_on_front' );
			// Correct <!--nextpage--> for page_on_front
			if ( !empty( $qv['paged'] ) ) {
				$qv['page'] = $qv['paged'];
				unset( $qv['paged'] );
			}
		}
		return $query;
	}

	/*
	 * This function takes any WP link and associated language configuration and returns a localized url
	 * 
	 * @param string $lang Current language
	 * @param string $link The url to localize
	 * @param array $languages_map A key/value array that maps Transifex locale->plugin code
	 * @param string $source_lang The current source language
	 * @return string Returns modified link
	 */

	static function reverse_hard_link( $lang, $link, $languages_map, $source_lang ) {
		Plugin_Debug::logTrace();
		if ( empty( $lang ) ) {
			return $link;
		}
		if ( empty( $languages_map ) ) {
			return $link;
		}
		$modified_link = $link;
		$reverse_url = true;

		$reverse_url = ($reverse_url) ? (isset( $lang )) : false;

		if ( !empty( $lang ) ) {
			$reverse_url = ($reverse_url) ? (!strpos( $modified_link, $lang )) : false;
		}
		$reverse_url = ($reverse_url) ? (in_array( $lang, $languages_map )) : false;
		$reverse_url = ($reverse_url) ? (!($source_lang == $lang)) : false;

		//TODO  This can be dep'd 
		if ( $reverse_url && (3 <= substr_count( $link, '/' )) ) {
			$array_url = explode( '/', $link );
			$array_url[3] = $lang . '/' . $array_url[3];
			$modified_link = implode( '/', $array_url );
		}
		return $modified_link;
	}

	/*
	 * WP pre_post_link filter, adds lang to permalink 
	 * @param string $permalink The permalink to filter
	 * @param object $post The post object
	 * @param ??? $leavename what this is I dont even know
	 * @return string filtered permalink 
	 */

	function pre_post_link_hook( $permalink, $post, $leavename ) {
		if ( !Transifex_Live_Integration_Validators::is_permalink_ok( $permalink ) ) {
			return $permalink;
		}
		$lang = $this->lang;
		$p = $permalink;
		if ( $lang ) {
			$p = ($this->source_language !== $lang) ? $lang . $permalink : $permalink;
		}
		return $p;
	}

	/*
	 * WP term_link filter, filters term (ie tag and category) link
	 * @param string $termlink The link to filter
	 * @param object $term The term object
	 * @param object $taxonomy The taxonomy object
	 * @return string The filtered link
	 */

	function term_link_hook( $termlink, $term, $taxonomy ) {
		if ( !Transifex_Live_Integration_Validators::is_hard_link_ok( $termlink ) ) {
			return $termlink;
		}
		$retlink = $this->reverse_hard_link( $this->lang, $termlink, $this->languages_map, $this->source_language );
		return $retlink;
	}

	/*
	 * WP post_link filter, filters post link
	 * @param string $permalink The link to filter
	 * @param object $post The term object
	 * @param ??? $leavename What this is I don't even
	 * @return string The filtered link
	 */

	function post_link_hook( $permalink, $post, $leavename ) {
		if ( !Transifex_Live_Integration_Validators::is_hard_link_ok( $permalink ) ) {
			return $permalink;
		}
		$retlink = $this->reverse_hard_link( $this->lang, $permalink, $this->languages_map, $this->source_language );
		return $retlink;
	}

	/*
	 * WP post_type_archive_link filter, filters archive links
	 * @param string $link The link to filter
	 * @param string $post_type The post type
	 * @return string The filtered link
	 */

	function post_type_archive_link_hook( $link, $post_type ) {
		if ( !Transifex_Live_Integration_Validators::is_hard_link_ok( $link ) ) {
			return $link;
		}
		$retlink = $this->reverse_hard_link( $this->lang, $link, $this->languages_map, $this->source_language );
		return $retlink;
	}

	/*
	 * WP day_link filter, filters day link
	 * @param string $daylink The link to filter
	 * @param number $year The year
	 * @param number $month The month
	 * @param number $day The day
	 * @return string The filtered link
	 */

	function day_link_hook( $daylink, $year, $month, $day ) {
		if ( !Transifex_Live_Integration_Validators::is_hard_link_ok( $daylink ) ) {
			return $daylink;
		}
		$retlink = $this->reverse_hard_link( $this->lang, $daylink, $this->languages_map, $this->source_language );
		return $retlink;
	}

	/*
	 * WP month_link filter, filters month link
	 * @param string $monthlink The link to filter
	 * @param number $year The year
	 * @param number $month The month
	 * @return string The filtered link
	 */

	function month_link_hook( $monthlink, $year, $month ) {
		if ( !Transifex_Live_Integration_Validators::is_hard_link_ok( $monthlink ) ) {
			return $monthlink;
		}
		$retlink = $this->reverse_hard_link( $this->lang, $monthlink, $this->languages_map, $this->source_language );
		return $retlink;
	}

	/*
	 * WP year_link filter, filters term link
	 * @param string $yearlink The link to filter
	 * @param number $year The year
	 * @return string The filtered link
	 */

	function year_link_hook( $yearlink, $year ) {
		if ( !Transifex_Live_Integration_Validators::is_hard_link_ok( $yearlink ) ) {
			return $yearlink;
		}
		$retlink = $this->reverse_hard_link( $this->lang, $yearlink, $this->languages_map, $this->source_language );
		return $retlink;
	}

	/*
	 * WP page_link filter, filters page link
	 * @param string $link The link to filter
	 * @param number $id The page id
	 * @param ??? $sample I don't even know
	 * @return string The filtered link
	 */

	function page_link_hook( $link, $id, $sample ) {
		if ( !Transifex_Live_Integration_Validators::is_hard_link_ok( $link ) ) {
			return $link;
		}
		$retlink = $this->reverse_hard_link( $this->lang, $link, $this->languages_map, $this->source_language );
		return $retlink;
	}

	/*
	 * WP home_url hook, filters links using the home_url function
	 * @param string $url The link to filter
	 * @return string The filtered link
	 */

	function home_url_hook( $url ) {
		if ( !Transifex_Live_Integration_Validators::is_hard_link_ok( $url ) ) {
			return $url;
		}
		$retlink = $this->reverse_hard_link( $this->lang, $url, $this->languages_map, $this->source_language );
		return $retlink;
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
		$rr = Transifex_Live_Integration_Generate_Rewrite_Rules::generate_rewrite_rules( $pp, EP_PERMALINK, true, false, false, false );
		$rewrite = array_merge( $rr, $rules );
		return $rewrite;
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
		$pp = $this->generate_page_permastruct();
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
		Plugin_Debug::logTrace( $rr );
		$rewrite = array_merge( $rr, $rules );
		return $rewrite;
	}

}
