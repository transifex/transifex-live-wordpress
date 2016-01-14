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

	const REGEX_PATTERN_CHECK_PATTERN = '/\(.*\?|.*\)/';

	private $REWRITE_OPTIONS = [ // not a const for backward compat.
		'0' => 'none',
		'1' => 'pages',
		'2' => 'all',
		'3' => 'set tag only',
	];

	/**
	 * Private constructor, initializes local vars based on settings
	 * @param array $settings Associative array used to store plugin settings.
	 */
	private function __construct( $settings ) {
		Plugin_Debug::logTrace();
		$this->languages_regex = $settings['languages_regex'];
		$this->source_language = $settings['source_language'];
		$this->rewrite_option = $this->REWRITE_OPTIONS[$settings['add_language_rewrites']];
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
		if ( ! isset( $settings['add_language_rewrites'] ) ) {
			Plugin_Debug::logTrace( 'settings[add_language_rewrites] not set' );
			return false;
		}
		if ( 'none' === $settings['add_language_rewrites'] ) {
			Plugin_Debug::logTrace( 'settings[add_language_rewrites] is none' );
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
	static function parse_query_hook( $query ) {
		Plugin_Debug::logTrace();
		$query->query_vars['lang'] = isset( $query->query_vars['lang'] ) ? $query->query_vars['lang'] : $this->source_language;
		return $query;
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
