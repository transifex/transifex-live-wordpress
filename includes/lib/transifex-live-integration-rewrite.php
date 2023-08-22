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
	public $rewrite_options;

	private $subdirectory_prefix;

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
		$this->subdirectory_prefix = strlen(trim($settings['subdirectory_path'])) > 0 ? $settings['subdirectory_path'] : '';
 		$this->languages_map = json_decode( $settings['language_map'], true )[0];
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
		$this->wp_services = new Transifex_Live_Integration_WP_Services();

		// Plugin_Debug::logTrace('->language_codes: ' . print_r($this->language_codes, true));
	}

	public function get_language_url( $atts ) {
		Plugin_Debug::logTrace('');
		$a = shortcode_atts( array(
			'url' => home_url(),
				), $atts );
		return $this->reverse_hard_link( $this->lang, $a['url'], $this->languages_map, $this->source_language, $this->rewrite_pattern, null, 'get_language_url' );
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
		Plugin_Debug::logTrace('rewrite->lang: ' . $this->lang);
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

	function reverse_hard_link( $lang, $link, $languages_map, $source_lang,
			$pattern, $subdirectory_prefix = '', $caller = null ) {
		$subdirectory_prefix = strlen( $subdirectory_prefix ) > 0 ? '/'. $subdirectory_prefix : $subdirectory_prefix;
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

		preg_match( $pattern, $link, $m );
		if ( count( $m ) > 1 ) {
			$link = str_replace( $m[1], $lang, $m[0] );
		} else {
			$site_host = parse_url($this->wp_services->get_site_url())['host'];
			$parsed_url = parse_url($link);
			$link_host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
			// change only wordpress links - not links reffering to other domains
			if ( $link_host === $site_host ) {
				/* Check if the path starts with the language code,
				* otherwise prepend it. */
				$parsed = parse_url( $link );
				Plugin_Debug::logTrace('sudir prfx ========'. print_r($subdirectory_prefix, true));
				Plugin_Debug::logTrace('parsed path ========'. print_r($parsed['path'], true));
				Plugin_Debug::logTrace('substr ========'. print_r(substr($parsed['path'], 0, strlen($subdirectory_prefix)), true));
				Plugin_Debug::logTrace('bool ========'. print_r((bool)$subdirectory_prefix, true));


				if ( (bool)$subdirectory_prefix ) {
					$check_subdirs = array($subdirectory_prefix . '/', $subdirectory_prefix);
          foreach ($check_subdirs as $check_subdir) {
						if (strpos($parsed['path'], $check_subdir) === 0) {
							$stripped = substr($parsed['path'], strlen($check_subdir));
							$stripped = strlen($stripped) > 0 && substr($stripped, 0, 1) === '/' ? $stripped : '/' . $stripped;
							$parsed['path'] = $stripped;
							Plugin_Debug::logTrace('parsed ***========'. print_r($parsed['path'], true));
							break;
						}
					}
				}
				// $parsed['path'] doesnt' start with '/' . $lang . '/'
				if (strpos($parsed['path'], '/'. $lang .'/') !== 0 ) {
					// $parsed['path'] === '/' . $lang
					if (strpos($parsed['path'], '/'. $lang) === 0 && strlen($parsed['path']) === 3) {
						Plugin_Debug::logTrace('11111 '. strpos($parsed['path'], '/'. $lang));
						if ( (bool)$subdirectory_prefix ) {
							$parsed['path'] = $subdirectory_prefix . $parsed['path'];
						} // else do not modify $parsed['path']
					} else {
						if ( (bool)$subdirectory_prefix ) {
							$parsed['path'] = $subdirectory_prefix . '/' . $lang . $parsed['path'];
						} else {
							$parsed['path'] = '/' . $lang . $parsed['path'];
						}
					}
				} else {
					if ( (bool)$subdirectory_prefix ) {
						$parsed['path'] = $subdirectory_prefix . $parsed['path'];
					} // else do not modify $parsed['path']
				}
				$link = Transifex_Live_Integration_Util::unparse_url( $parsed );
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
		Plugin_Debug::logTrace();
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
		Plugin_Debug::logTrace();
		if ( !Transifex_Live_Integration_Validators::is_hard_link_ok( $termlink ) ) {
			return $termlink;
		}
		$retlink = $this->reverse_hard_link( $this->lang, $termlink, $this->languages_map, $this->source_language, $this->rewrite_pattern, $this->subdirectory_prefix, 'term_link_hook' );
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
		Plugin_Debug::logTrace();
		if ( !Transifex_Live_Integration_Validators::is_hard_link_ok( $permalink ) ) {
			return $permalink;
		}
		$retlink = $this->reverse_hard_link( $this->lang, $permalink, $this->languages_map, $this->source_language, $this->rewrite_pattern, $this->subdirectory_prefix, 'post_link_hook' );
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
		$retlink = $this->reverse_hard_link( $this->lang, $link, $this->languages_map, $this->source_language, $this->rewrite_pattern, $this->subdirectory_prefix, 'post_type_archive_link_hook' );
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
		Plugin_Debug::logTrace();
		if ( !Transifex_Live_Integration_Validators::is_hard_link_ok( $daylink ) ) {
			return $daylink;
		}
		$retlink = $this->reverse_hard_link( $this->lang, $daylink, $this->languages_map, $this->source_language, $this->rewrite_pattern, $this->subdirectory_prefix, 'day_link_hook' );
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
		Plugin_Debug::logTrace();
		if ( !Transifex_Live_Integration_Validators::is_hard_link_ok( $monthlink ) ) {
			return $monthlink;
		}
		$retlink = $this->reverse_hard_link( $this->lang, $monthlink, $this->languages_map, $this->source_language, $this->rewrite_pattern, $this->subdirectory_prefix, 'month_link_hook' );
		return $retlink;
	}

	/*
	 * WP year_link filter, filters term link
	 * @param string $yearlink The link to filter
	 * @param number $year The year
	 * @return string The filtered link
	 */

	function year_link_hook( $yearlink, $year ) {
		Plugin_Debug::logTrace();
		if ( !Transifex_Live_Integration_Validators::is_hard_link_ok( $yearlink ) ) {
			return $yearlink;
		}
		$retlink = $this->reverse_hard_link( $this->lang, $yearlink, $this->languages_map, $this->source_language, $this->rewrite_pattern, $this->subdirectory_prefix, 'year_link_hook' );
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
		Plugin_Debug::logTrace();
		if ( !Transifex_Live_Integration_Validators::is_hard_link_ok( $link ) ) {
			return $link;
		}
		$retlink = $this->reverse_hard_link( $this->lang, $link, $this->languages_map, $this->source_language, $this->rewrite_pattern, $this->subdirectory_prefix, 'page_link_hook' );
		return $retlink;
	}

	/*
	 * WP home_url hook, filters links using the home_url function
	 * @param string $url The link to filter
	 * @return string The filtered link
	 */

	function home_url_hook( $url ) {
		Plugin_Debug::logTrace();
		if ( !Transifex_Live_Integration_Validators::is_hard_link_ok( $url ) ) {
			Plugin_Debug::logTrace('NOT OK LINK -> ' . print_r($url, true));
			return $url;
		}
		$retlink = $this->reverse_hard_link( $this->lang, $url, $this->languages_map, $this->source_language, $this->rewrite_pattern, $this->subdirectory_prefix, 'home_url_hook');
		Plugin_Debug::logTrace('home_url_hook return -> ' . print_r($retlink, true));
		return $retlink;
	}

	/*
	* WP the_content_hook hook, filters links using the the_content function
	* @param string $string The string to filter
	* @return string The filtered string
	*/
	function the_content_hook( $string) {
		Plugin_Debug::logTrace();
		// Regular expression that extracts all urls from a string
		$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
		preg_match_all("/$regexp/siU", $string, $matchArray);
		// Iterate through all links, rewrite when needed
		foreach($matchArray[2] as $match){
			if ( !Transifex_Live_Integration_Validators::is_hard_link_ok( $match ) ) {
				continue;
			}
			$retlink = $this->reverse_hard_link( $this->lang, $match, $this->languages_map, $this->source_language, $this->rewrite_pattern, $this->subdirectory_prefix, 'the_content_hook' );
			$string = str_replace($match, $retlink, $string);
		}
		return $string;
	}

}
