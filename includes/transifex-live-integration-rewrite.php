<?php

/**
 * Language rewrites
 * @package TransifexLiveIntegration
 */

/**
 * Static class for settings defaults
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

	/**
	 * Permastruct format for WP page objects
	 * @var string
	 */
	private $page_permastruct;

	/**
	 * Which rewrite option selected in the plugin
	 * @var string
	 */
	public $rewrite_option;
	
	public $rewrite_options;
	
	const REGEX_PATTERN_CHECK_PATTERN = "/\(.*\?|.*\)/";

	private $REWRITE_OPTIONS = [ // not a const for backward compat.
		'0' => 'date',
		'1' => 'page',
		'2' => 'author',
		'3' => 'tag',
		'4' => 'category',
		'5' => 'search',
		'6' => 'feed',
	];

	/**
	 * Private constructor, initializes local vars based on settings
	 * @param array $settings Associative array used to store plugin settings.
	 */
	private function __construct( $settings ) {
		Plugin_Debug::logTrace();
		$this->rewrite_options = [];
		$this->languages_regex = $settings['languages_regex'];
		$this->source_language = $settings['source_language'];
		$this->rewrite_options[] = ($settings['add_rewrites_date'])?'date':'';
		$this->rewrite_options[] = ($settings['add_rewrites_page'])?'page':'';
		$this->rewrite_options[] = ($settings['add_rewrites_author'])?'author':'';
		$this->rewrite_options[] = ($settings['add_rewrites_tag'])?'tag':'';
		$this->rewrite_options[] = ($settings['add_rewrites_category'])?'category':'';
		$this->rewrite_options[] = ($settings['add_rewrites_search'])?'search':'';
		$this->rewrite_options[] = ($settings['add_rewrites_feed'])?'feed':'';
		$b = strpos( ',', $settings['languages'] );
		if ( false === $b ) {
			$this->language_codes = array( $settings['languages'] );
		} else {
			$this->language_codes = explode( ',', $settings['languages'] );
		}
	}

	/**
	 * Factory function to create a rewrite object
	 * @param array $settings Associative array used to store plugin settings.
	 */
	static function create_rewrite( $settings ) {
		Plugin_Debug::logTrace();
		if ( ! isset( $settings['languages'] ) ) {
			Plugin_Debug::logTrace( 'settings[languages] not set' );
			return false;
		}
		if ( ! isset( $settings['languages_regex'] ) ) {
			Plugin_Debug::logTrace( 'settings[languages_regex] not set' );
			return false;
		}
		
		if ( ! preg_match( self::REGEX_PATTERN_CHECK_PATTERN, $settings['languages_regex'] ) ) {
			Plugin_Debug::logTrace( 'settings[languages_regex] failed pattern check' );
			return false;
		}
		return new Transifex_Live_Integration_Rewrite( $settings );
	}

	/**
	 * Callback function to the WP init hook
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
		Plugin_Debug::logTrace();
		$query->query_vars['lang'] = isset( $query->query_vars['lang'] ) ? $query->query_vars['lang'] : $this->source_language;
		return $query;
	}
	
		/**
	 * Function to build page permastructs
	 */
	function generate_date_permastruct() {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		$p = $wp_rewrite->get_date_permastruct();
		$pp = '%lang%/' . $p;
		return $pp;
	}

	/**
	 * Callback function to the WP page_rewrite_rules
	 * @param array $rules Associative array of rewrite rules in WP.
	 */
	function date_rewrite_rules_hook( $rules ) {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		// TODO figure this out $wp_rewrite->add_rewrite_tag( '%pagename%', '(.?.+?)', 'pagename=' );
		$wp_rewrite->add_rewrite_tag( '%lang%', $this->languages_regex, 'lang=' );
		$pp = $this->generate_date_permastruct();
		$this->date_permastruct = $pp;
		$rr = Transifex_Live_Integration_Generate_Rewrite_Rules::generate_rewrite_rules( $pp, EP_PAGES, true, false, false, false );
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
	 */
	function page_rewrite_rules_hook( $rules ) {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		$wp_rewrite->add_rewrite_tag( '%pagename%', '(.?.+?)', 'pagename=' );
		$wp_rewrite->add_rewrite_tag( '%lang%', $this->languages_regex, 'lang=' );
		$pp = $this->generate_page_permastruct();
		$this->page_permastruct = $pp;
		$rr = Transifex_Live_Integration_Generate_Rewrite_Rules::generate_rewrite_rules( $pp, EP_PAGES, true, false, false, false );
		$rewrite = array_merge( $rr, $rules );
		return $rewrite;
	}
	
		/**
	 * Function to build page permastructs
	 */
	function generate_author_permastruct() {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		$p = $wp_rewrite->get_author_permastruct();
		$pp = '%lang%/' . $p;
		return $pp;
	}

	/**
	 * Callback function to the WP page_rewrite_rules
	 * @param array $rules Associative array of rewrite rules in WP.
	 */
	function author_rewrite_rules_hook( $rules ) {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		// TODO figure this out $wp_rewrite->add_rewrite_tag( '%pagename%', '(.?.+?)', 'pagename=' );
		$wp_rewrite->add_rewrite_tag( '%lang%', $this->languages_regex, 'lang=' );
		$pp = $this->generate_author_permastruct();
		$this->author_permastruct = $pp;
		$rr = Transifex_Live_Integration_Generate_Rewrite_Rules::generate_rewrite_rules( $pp, EP_PAGES, true, false, false, false );
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
		$pp = '%lang%/' . $p;
		return $pp;
	}

	/**
	 * Callback function to the WP page_rewrite_rules
	 * @param array $rules Associative array of rewrite rules in WP.
	 */
	function tag_rewrite_rules_hook( $rules ) {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		$wp_rewrite->add_rewrite_tag( '%pagename%', '(.?.+?)', 'pagename=' );
		// TODO figure this out $wp_rewrite->add_rewrite_tag( '%lang%', $this->languages_regex, 'lang=' );
		$pp = $this->generate_tag_permastruct();
		$this->tag_permastruct = $pp;
		$rr = Transifex_Live_Integration_Generate_Rewrite_Rules::generate_rewrite_rules( $pp, EP_PAGES, true, false, false, false );
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
		$pp = '%lang%/' . $p;
		return $pp;
	}

	/**
	 * Callback function to the WP page_rewrite_rules
	 * @param array $rules Associative array of rewrite rules in WP.
	 */
	function category_rewrite_rules_hook( $rules ) {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		//TODO figure this out $wp_rewrite->add_rewrite_tag( '%pagename%', '(.?.+?)', 'pagename=' );
		$wp_rewrite->add_rewrite_tag( '%lang%', $this->languages_regex, 'lang=' );
		$pp = $this->generate_category_permastruct();
		$this->page_permastruct = $pp;
		$rr = Transifex_Live_Integration_Generate_Rewrite_Rules::generate_rewrite_rules( $pp, EP_PAGES, true, false, false, false );
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
		$pp = '%lang%/' . $p;
		return $pp;
	}

	/**
	 * Callback function to the WP page_rewrite_rules
	 * @param array $rules Associative array of rewrite rules in WP.
	 */
	function search_rewrite_rules_hook( $rules ) {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		$wp_rewrite->add_rewrite_tag( '%pagename%', '(.?.+?)', 'pagename=' );
		//TODO figure this out $wp_rewrite->add_rewrite_tag( '%lang%', $this->languages_regex, 'lang=' );
		$pp = $this->generate_page_permastruct();
		$this->search_permastruct = $pp;
		$rr = Transifex_Live_Integration_Generate_Rewrite_Rules::generate_rewrite_rules( $pp, EP_PAGES, true, false, false, false );
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
		$pp = '%lang%/' . $p;
		return $pp;
	}

	/**
	 * Callback function to the WP page_rewrite_rules
	 * @param array $rules Associative array of rewrite rules in WP.
	 */
	function feed_rewrite_rules_hook( $rules ) {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		//TODO figure this out $wp_rewrite->add_rewrite_tag( '%pagename%', '(.?.+?)', 'pagename=' );
		$wp_rewrite->add_rewrite_tag( '%lang%', $this->languages_regex, 'lang=' );
		$pp = $this->generate_feed_permastruct();
		$this->feed_permastruct = $pp;
		$rr = Transifex_Live_Integration_Generate_Rewrite_Rules::generate_rewrite_rules( $pp, EP_PAGES, true, false, false, false );
		$rewrite = array_merge( $rr, $rules );
		return $rewrite;
	}
	
	/**
	 * Function to build 'all' = root permastructs
	 */
	function generate_root_permastruct() {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		return '%lang%/' . $wp_rewrite->root . '/';
	}

	/**
	 * Callback function to the WP root_rewrite_rules
	 * @param array $rules Associative array of rewrite rules in WP.
	 */
	function root_rewrite_rules_hook( $rules ) {
		Plugin_Debug::logTrace();
		global $wp_rewrite;
		$wp_rewrite->add_rewrite_tag( '%lang%', $this->languages_regex, 'lang=' );
		$pp = $this->generate_root_permastruct();
		$rr = Transifex_Live_Integration_Generate_Rewrite_Rules::generate_rewrite_rules( $pp, EP_ROOT );
		Plugin_Debug::logTrace( $rr );
		$rewrite = array_merge( $rr, $rules );
		return $rewrite;
	}

}
