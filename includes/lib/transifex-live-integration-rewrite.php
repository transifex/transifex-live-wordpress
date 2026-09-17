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
	private $rewrite_pattern;
	private $wp_services;
	public $rewrite_options;

	/**
	 * Private constructor, initializes local vars based on settings
	 * @param array $settings Associative array used to store plugin settings.
	 */
	public function __construct( $settings, $rewrite_options ) {
		Plugin_Debug::logTrace();
		if ( !defined( 'TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE' ) ) {
			define( 'TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE', dirname( __FILE__, 3));
		}

		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/common/transifex-live-integration-validators.php';
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/override/transifex-live-integration-generate-rewrite-rules.php';
		include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE .'/includes/lib/transifex-live-integration-wp-services.php';

		$this->rewrite_options = [ ];
		$this->languages_regex = $settings['languages_regex'];
		$this->source_language = $settings['source_language'];
		$this->languages_map = json_decode( $settings['language_map'], true )[0] ?? array();
		$this->lang = false; // need to wait before initting
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
		if ( $settings['url_options'] == '2' ) {
			$this->rewrite_pattern = $settings['subdomain_pattern'];
		} else {
			$this->rewrite_pattern = $settings['subdirectory_pattern'];
		}
		if ( $this->rewrite_pattern ) {
			$pattern = $this->rewrite_pattern;
			//Check for delimiters and add them if missing
			if ( !(substr( $pattern, 0, 1 ) == '#' && substr( $pattern, -1 ) == '#') ) {
				$pattern = trim( $pattern, '#' );
				$pattern = '#' . $pattern . '#';
				$this->rewrite_pattern = $pattern;
			}
		}
		$this->wp_services = new Transifex_Live_Integration_WP_Services($settings);
	}

	public function get_language_url( $atts ) {
		$a = shortcode_atts( array(
			'url' => home_url(),
				), $atts );
		return $this->reverse_hard_link( $this->lang, $a['url'], $this->languages_map, $this->source_language, $this->rewrite_pattern );
	}

	public function detect_language() {
		return $this->lang;
	}

	public function is_language( $atts ) {
		$a = shortcode_atts( array(
			'language' => $this->lang,
				), $atts );
		return ($a['language'] == $this->lang) ? true : false;
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

	function wp_hook() {
		Plugin_Debug::logTrace();
		$this->lang = get_query_var( 'lang' );
	}

	/*
	 * WP parse_request action, takes the language as soon as the request is routed.
	 *
	 * This is the same value the Live snippet, picker and hreflang read later
	 * through the lang query var, so links cannot disagree with the language
	 * the page is rendered in. Until it runs the language stays unknown, which
	 * keeps home_url() unlocalized while WP strips the install path off the
	 * request: a localized home_url() there swallows the language segment.
	 * @param object $wp The WP object
	 */
	function parse_request_hook( $wp ) {
		Plugin_Debug::logTrace();
		$this->lang = ( !empty( $wp->query_vars['lang'] ) ) ? $wp->query_vars['lang'] : $this->source_language;
	}

	/*
	 * Whether two hosts refer to the same site.
	 *
	 * @param string $host_a A hostname
	 * @param string $host_b Another hostname
	 * @return bool Returns true when the hosts should be treated as the same site
	 */
	static function hosts_match( $host_a, $host_b ) {
		if ( $host_a === '' || $host_b === '' ) {
			return false;
		}
		return self::normalize_host( $host_a ) === self::normalize_host( $host_b );
	}

	/*
	 * Strips a leading www. so host comparisons are not sensitive to it.
	 * 
	 * @param string $host A hostname
	 * @return string The normalized hostname
	 */
	static function normalize_host( $host ) {
		$host = strtolower( (string) $host );
		if ( strpos( $host, 'www.' ) === 0 ) {
			$host = substr( $host, 4 );
		}
		return $host;
	}

	/*
	 * Inserts a language code as a path segment, after any site subdirectory.
	 *
	 * @param string $path The URL path
	 * @param string $lang The URL language code
	 * @param string $site_path Path prefix of the site url, if any
	 * @return string The path with the language segment inserted
	 */
	static function prepend_lang_to_path( $path, $lang, $site_path = '' ) {
		if ( $path === '' || $path === null ) {
			$path = '/';
		}
		$site_path = rtrim( (string) $site_path, '/' );
		$prefix = '';
		$rest = $path;
		if ( $site_path !== '' && ( $path === $site_path || strpos( $path, $site_path . '/' ) === 0 ) ) {
			$prefix = $site_path;
			$rest = substr( $path, strlen( $site_path ) );
			if ( $rest === '' ) {
				$rest = '/';
			}
		}
		if ( $rest === '/' . $lang || strpos( $rest, '/' . $lang . '/' ) === 0 ) {
			return $path;
		}
		if ( isset( $rest[0] ) && $rest[0] !== '/' ) {
			$rest = '/' . $rest;
		}
		return $prefix . '/' . $lang . $rest;
	}

	/*
	 * Checks whether a path addresses the WP REST API.
	 *
	 * WP builds REST urls out of home_url(), so they arrive at the link filters
	 * like any other url, but no rewrite rule serves them under a language
	 * prefix: a prefixed one misses the REST API and answers with a 404 page,
	 * which breaks anything on the page that expects JSON.
	 *
	 * The prefix is looked for as a whole path segment, wherever it sits. It is
	 * not always the first one: an install served from a subdirectory carries
	 * the site path ahead of it, and permalinks holding index.php carry that.
	 * Matching a whole segment rather than a bare substring still leaves a page
	 * whose slug merely opens with the prefix to be localized as usual.
	 * @param string $path The path component of the url
	 * @return bool Returns true when the path addresses the REST API
	 */
	static function is_rest_route( $path ) {
		$prefix = ( function_exists( 'rest_get_url_prefix' ) ) ? rest_get_url_prefix() : 'wp-json';
		$segment = '/' . $prefix;
		if ( false !== strpos( $path, $segment . '/' ) ) {
			return true;
		}
		return ( substr( $path, -strlen( $segment ) ) === $segment );
	}

	/*
	 * Checks whether a path addresses WP itself rather than site content.
	 *
	 * Admin screens, core and theme/plugin files, and scripts like
	 * wp-login.php or xmlrpc.php are served from one place only, so a
	 * language prefix turns them into 404s. The core directories are matched
	 * as whole segments wherever they sit, since an install served from a
	 * subdirectory carries its own path ahead of them.
	 * @param string $path The path component of the url
	 * @return bool Returns true when the path must keep its source form
	 */
	static function is_excluded_path( $path ) {
		if ( self::is_rest_route( $path ) ) {
			return true;
		}
		$segments = explode( '/', trim( (string) $path, '/' ) );
		if ( array_intersect( $segments, array( 'wp-admin', 'wp-content', 'wp-includes' ) ) ) {
			return true;
		}
		return ( strtolower( substr( (string) $path, -4 ) ) === '.php' );
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

	function reverse_hard_link( $lang, $link, $languages_map, $source_lang, $pattern ) {
		Plugin_Debug::logTrace();
		if ( !(isset( $pattern )) ) {
			return $link;
		}
		if ( !(substr( $pattern, 0, 1 ) == '#' && substr( $pattern, -1 ) == '#') ) {
			if ( !(substr( $pattern, 0, 1 ) == '/' && substr( $pattern, -1 ) == '/') ) {
				Plugin_Debug::logTrace( 'Pattern:' . $pattern . '||Missing delimiters.' );
				return $link;
			}
		}

		if ( empty( $lang ) || empty( $languages_map ) ) {
			return $link;
		}
		elseif ( !in_array( $lang, array_values( $languages_map ) ) || $source_lang == $lang ) {
			return $link;
		}

		// Protocol-relative links carry no scheme for unparse_url to rebuild
		// them with, so they would come back without their leading slashes.
		if ( substr( $link, 0, 2 ) === '//' ) {
			return $link;
		}

		preg_match( $pattern, $link, $m );
		if ( count( $m ) > 1 ) {
			$link = str_replace( $m[1], $lang, $m[0] );
		} else {
			$site_url = $this->wp_services->get_site_url();
			$site_host = parse_url($site_url)['host'] ?? '';
			$site_path = parse_url($site_url, PHP_URL_PATH) ?? '';
			$parsed_url = parse_url($link);
			$link_host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
			$current_path = $parsed_url['path'] ?? '';
			$scheme = isset($parsed_url['scheme']) ? strtolower($parsed_url['scheme']) : '';
			if ( $scheme !== '' && $scheme !== 'http' && $scheme !== 'https' ) {
				return $link;
			}
			// Same-site absolute links, and root-relative ones with no host.
			$is_same_site = ( $link_host === '' && $current_path !== '' )
				|| self::hosts_match( $link_host, $site_host );
			// change only wordpress content links - not links reffering to other domains
			if ( $is_same_site && !self::is_excluded_path( $current_path ) ) {
				$parsed_url['path'] = self::prepend_lang_to_path( $current_path, $lang, $site_path );
				$link = Transifex_Live_Integration_Util::unparse_url( $parsed_url );
			}
		}
		return $link;
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
		$retlink = $this->reverse_hard_link( $this->lang, $termlink, $this->languages_map, $this->source_language, $this->rewrite_pattern );
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
		$retlink = $this->reverse_hard_link( $this->lang, $permalink, $this->languages_map, $this->source_language, $this->rewrite_pattern );
		return $retlink;
	}

	/**
	 * Filters and processes the given field value to handle URLs and localize content.
	 *
	 * This function determines whether the provided field value is a valid URL. If it is a URL,
	 * it localizes the URL by calling the `reverse_hard_link` method. If the field value is not a URL,
	 * the method `the_content_hook` is invoked to localize all anchor (`<a>`) href links within the text content.
	 *
	 * This function can be triggered as following:
	 * e.g add_filter('acf/format_value', [$rewrite, 'custom_field_link_hook'], 10, 3 );
	 *
	 * @param string $field_value The field value to be processed, which can be a URL or custom content.
	 * @return string The processed field value after handling URLs or applying content hooks.
	 */
	function custom_field_link_hook($field_value) {
		if (filter_var($field_value, FILTER_VALIDATE_URL)) {
			if ( !Transifex_Live_Integration_Validators::is_hard_link_ok( $field_value ) ) {
				return $field_value;
			}
			$field_value = $this->reverse_hard_link( $this->lang, $field_value, $this->languages_map, $this->source_language, $this->rewrite_pattern );
		} else {
			$field_value = $this->the_content_hook($field_value);
		}

		return $field_value;
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
		$retlink = $this->reverse_hard_link( $this->lang, $link, $this->languages_map, $this->source_language, $this->rewrite_pattern );
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
		$retlink = $this->reverse_hard_link( $this->lang, $daylink, $this->languages_map, $this->source_language, $this->rewrite_pattern );
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
		$retlink = $this->reverse_hard_link( $this->lang, $monthlink, $this->languages_map, $this->source_language, $this->rewrite_pattern );
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
		$retlink = $this->reverse_hard_link( $this->lang, $yearlink, $this->languages_map, $this->source_language, $this->rewrite_pattern );
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
		$retlink = $this->reverse_hard_link( $this->lang, $link, $this->languages_map, $this->source_language, $this->rewrite_pattern );
		return $retlink;
	}

	/*
	 * WP home_url hook, filters links using the home_url function
	 * @param string $url The link to filter
	 * @return string The filtered link
	 */

	function home_url_hook( $url ) {
		Plugin_Debug::logTrace();
		// remove early and add late this filter to avoid recursion when calculating home urls
		remove_filter('home_url', array($this, 'home_url_hook'));
		$link = $url;

		// A bare home url has no path, so it fails the slash count in
		// is_hard_link_ok(). Only that case gets a `/`: padding any other url
		// breaks the ones WP extends afterwards (rest_url() appends the route
		// to home_url('wp-json'), giving wp-json//) or that end in a query.
		if ($this->lang && $this->lang !== $this->source_language) {
			$parsed = parse_url( $link );
			if ( $parsed !== false && empty( $parsed['path'] ) ) {
				$parsed['path'] = '/';
				$link = Transifex_Live_Integration_Util::unparse_url( $parsed );
			}
		}
		$retlink = $url;
		if ( Transifex_Live_Integration_Validators::is_hard_link_ok( $link ) ) {
			$localized = $this->reverse_hard_link( $this->lang, $link, $this->languages_map, $this->source_language, $this->rewrite_pattern );
			// Hand back the untouched url unless it was actually localized
			$retlink = ( $localized !== $link ) ? $localized : $url;
		}
		add_filter('home_url', array($this, 'home_url_hook'), 11, 1);
		return $retlink;
	}

	/*
	* WP the_content_hook hook, filters links using the the_content function
	* @param string $string The string to filter
	* @return string The filtered string
	*/
	function the_content_hook( $string) {
		if ( !is_string( $string ) || empty( $this->lang ) || $this->lang === $this->source_language ) {
			return $string;
		}
		// Rewrites the href value of each anchor in place. Replacing the url
		// as a bare substring would also hit every other place it occurs:
		// "/" is in every closing tag, and a relative path is part of any
		// longer url or src that shares it. The rest of the tag is captured
		// only to see whether the anchor names the language it links to.
		$regexp = '/(<a\s(?:[^>]*?\s)?href\s*=\s*)(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'>]+))([^>]*)/i';
		return preg_replace_callback( $regexp, function ( $m ) {
			// A link with hreflang points at one language version on purpose,
			// like a language switcher's, so it keeps its own language.
			if ( preg_match( '/\shreflang\s*=/i', $m[1] . ' ' . $m[5] ) ) {
				return $m[0];
			}
			if ( isset( $m[4] ) && $m[4] !== '' ) {
				$quote = '';
				$url = $m[4];
			} elseif ( isset( $m[3] ) && $m[3] !== '' ) {
				$quote = "'";
				$url = $m[3];
			} else {
				$quote = '"';
				$url = $m[2];
			}
			if ( !Transifex_Live_Integration_Validators::is_hard_link_ok( $url ) ) {
				return $m[0];
			}
			$retlink = $this->reverse_hard_link( $this->lang, $url, $this->languages_map, $this->source_language, $this->rewrite_pattern );
			return $m[1] . $quote . $retlink . $quote . $m[5];
		}, $string );
	}

	/*
	 * WP do_shortcode_tag filter, localizes links in shortcode output.
	 *
	 * Page builders such as Divi render theme headers, footers and popup
	 * layouts as shortcodes outside the_content, so no other link filter
	 * sees them. Shortcodes in the_content expand after the_content_hook
	 * has run, so their links are only seen here as well. Layout containers
	 * hold their children's already rewritten output and are skipped; the
	 * rewrite is idempotent, so any other container is just redone.
	 * Shortcodes whose links must keep their own form can be added through
	 * the tx_shortcode_skip_names filter.
	 * @param string $output The shortcode output
	 * @param string $tag The shortcode name
	 * @return string The filtered output
	 */
	function do_shortcode_tag_hook( $output, $tag ) {
		if ( !is_string( $output ) || empty( $this->lang ) || $this->lang === $this->source_language ) {
			return $output;
		}
		if ( self::is_editor_request() ) {
			return $output;
		}
		$skip = array(
			// this plugin's own shortcodes
			'get_language_url', 'detect_language', 'is_language',
			// Divi layout containers
			'et_pb_section', 'et_pb_row', 'et_pb_row_inner', 'et_pb_column', 'et_pb_column_inner',
		);
		if ( function_exists( 'apply_filters' ) ) {
			$skip = (array) apply_filters( 'tx_shortcode_skip_names', $skip );
		}
		if ( in_array( $tag, $skip, true ) ) {
			return $output;
		}
		return $this->the_content_hook( $output );
	}

	/*
	 * Checks whether the request is an admin, AJAX or page builder one.
	 *
	 * These render shortcodes for editing, not for visitors: the Divi
	 * Visual Builder opened on a localized url would otherwise show
	 * rewritten links in its preview.
	 * @return bool Returns true when shortcode output must not be rewritten
	 */
	static function is_editor_request() {
		if ( function_exists( 'is_admin' ) && is_admin() ) {
			return true;
		}
		if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) {
			return true;
		}
		return ( function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled() );
	}

	/*
	 * WP render_block filter, localizes links in block markup.
	 *
	 * Blocks rendered outside the_content (block theme headers, footers and
	 * navigation) are not seen by any other link filter, and a navigation
	 * link block stores its url as a static attribute. Only leaf blocks are
	 * rewritten: a parent's markup holds its already rendered children, so
	 * rewriting it again would redo their work on every level of nesting.
	 * Blocks that print a link of their own around their children can be
	 * added through the tx_render_block_names filter.
	 * @param string $block_content The rendered block markup
	 * @param array $block The parsed block
	 * @return string The filtered markup
	 */
	function render_block_hook( $block_content, $block ) {
		if ( empty( $this->lang ) || $this->lang === $this->source_language ) {
			return $block_content;
		}
		$with_own_link = array( 'core/navigation-submenu' );
		if ( function_exists( 'apply_filters' ) ) {
			$with_own_link = (array) apply_filters( 'tx_render_block_names', $with_own_link );
		}
		$block_name = $block['blockName'] ?? '';
		if ( !empty( $block['innerBlocks'] ) && !in_array( $block_name, $with_own_link, true ) ) {
			return $block_content;
		}
		return $this->the_content_hook( $block_content );
	}

	/*
	 * WP wp_setup_nav_menu_item filter, localizes menu items that carry a URL
	 * of their own.
	 *
	 * Menu items pointing at a post, page, term or archive take their URL from
	 * the matching WP link function, so the filters above already localize
	 * them. A custom link instead carries the URL typed into the menu editor,
	 * which no link filter ever sees, leaving it in the source language.
	 * @param object $menu_item The menu item object
	 * @return object Returns the filtered menu item
	 */
	function nav_menu_item_hook( $menu_item ) {
		if ( !isset( $menu_item->type ) || 'custom' !== $menu_item->type ) {
			return $menu_item;
		}
		if ( !isset( $menu_item->url ) || !Transifex_Live_Integration_Validators::is_hard_link_ok( $menu_item->url ) ) {
			return $menu_item;
		}
		$menu_item->url = $this->reverse_hard_link( $this->lang, $menu_item->url, $this->languages_map, $this->source_language, $this->rewrite_pattern );
		return $menu_item;
	}

	/*
	 * WP nav_menu_link_attributes filter, localizes the href at render time.
	 *
	 * @param array $atts The HTML attributes of the menu link
	 * @return array Returns the filtered attributes
	 */
	function nav_menu_link_attributes_hook( $atts ) {
		if ( empty( $atts['href'] ) || !Transifex_Live_Integration_Validators::is_hard_link_ok( $atts['href'] ) ) {
			return $atts;
		}
		$atts['href'] = $this->reverse_hard_link( $this->lang, $atts['href'], $this->languages_map, $this->source_language, $this->rewrite_pattern );
		return $atts;
	}

	/*
	 * WP comment_form_field_comment filter, to add a hidden field to the comment form
	 * that will be used to redirect the user to the same page after submitting the comment.
	 * We append the the comment field HTML with the hidden field.
	 * @param string $comment_form_field_comment The comment field HTML
	 * @return string The filtered comment field HTML
	 */
	function add_redirect_to_comments_form_hook( $comment_form_field_comment) {
		Plugin_Debug::logTrace();
		global $wp;
		$current_url =  add_query_arg( $wp->query_vars, home_url( $wp->request ) );
		return $comment_form_field_comment. '<input type="hidden" name="redirect_to" value="' . $current_url . '" />';
	}
}
