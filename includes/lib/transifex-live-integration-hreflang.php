<?php

include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/common/transifex-live-integration-common.php';
include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE .'/includes/lib/transifex-live-integration-wp-services.php';
/**
 * Includes hreflang tag attribute on each page
 * @package TransifexLiveIntegration
 */

/**
 * Class that renders hreflang
 */
class Transifex_Live_Integration_Hreflang {

	/**
	 * Copy of current plugin settings
	 * @var settings array
	 */
	private $settings;
	private $hreflang_map;

	/*
	 * A key/value array that maps Transifex locale->plugin code
	 * @var language_map array
	 */
	private $language_map;

	/*
	 * A list of Transifex locales, for enabled languages
	 * @var languages array
	 */
	private $languages;

	/*
	 * The site_url with a placeholder for language
	 * @var tokenized_url string
	 */
	private $tokenized_url;
	private $rewrite_options;

	/*
	 * URL option human readable name. Takes 3 possible values:
	 * - 'subdirectory'
	 * - 'subdomain'
	 * - 'none'
	 *
	 * @var url_option_name string
	 */
	private $url_option_name;

	/**
	 * Public constructor, sets the settings
	 * @param array $settings Associative array used to store plugin settings.
	 */
	public function __construct( $settings, $rewrite_options ) {
		Plugin_Debug::logTrace();
		$this->settings = $settings;
		$this->language_map = json_decode( $settings['language_map'], true )[0] ?? array();
		$this->languages = json_decode( $settings['transifex_languages'], true );
		$this->tokenized_url = $settings['tokenized_url'];
		$this->rewrite_options = $rewrite_options;
		$this->hreflang_map = json_decode( $settings['hreflang_map'], true )[0] ?? array();
		if ( $settings['url_options'] == '2' ) {
			$this->url_option_name = 'subdomain';
		} else if ( $settings['url_options'] == '3' ) {
			$this->url_option_name = 'subdirectory';
		} else {  // Transifex_Live_Integration_Hreflang should not be used if url_options is set to none
			$this->url_option_name = 'none';
		}
	}

	public function check_rewrite_options() {
		Plugin_Debug::logTrace();
		// These test the value rather than the presence of the key: options are
		// now stored for every rewrite type, so a disabled one is present with
		// a falsy value instead of being missing.
		if ( !empty( $this->rewrite_options['add_rewrites_post'] ) && is_single() ) {
			return true;
		}
		if ( !empty( $this->rewrite_options['add_rewrites_root'] ) && is_home() ) {
			return true;
		}
		if ( !empty( $this->rewrite_options['add_rewrites_date'] ) && is_archive() ) {
			return true;
		}
		if ( !empty( $this->rewrite_options['add_rewrites_page'] ) && is_page() ) {
			return true;
		}
		if ( !empty( $this->rewrite_options['add_rewrites_author'] ) && is_author() ) {
			return true;
		}
		if ( !empty( $this->rewrite_options['add_rewrites_tag'] ) && is_tag() ) {
			return true;
		}
		if ( !empty( $this->rewrite_options['add_rewrites_category'] ) && is_category() ) {
			return true;
		}
		if ( !empty( $this->rewrite_options['add_rewrites_search'] ) && is_search() ) {
			return true;
		}
		if ( !empty( $this->rewrite_options['add_rewrites_feed'] ) && is_feed() ) {
			return true;
		}
		return false;
	}

	/*
	 * Builds array with hreflang attributes as keys
	 * @param string $raw_url The current url
	 * @param array $languages The list of enabled languages
	 * @param array $language_map The key/value list of Transifex locale->plugin code
	 * @return array A list of attributes for HREFLANG tags
	 */

	private function generate_languages_hreflang( $raw_url, $languages,
			$language_map, $hreflang_map
	) {
		Plugin_Debug::logTrace();
		$source = $this->settings['source_language'];
		$url_map = Transifex_Live_Integration_Common::generate_language_url_map( $raw_url, $this->tokenized_url, $language_map );
		$ret = [ ];
		foreach ($languages as $language) {
			$arr = [ ];
			$href_link = $url_map[$language];
			$href_link_parts = explode(':', $href_link);
			if (count($href_link_parts) && ($href_link_parts[0] === 'http' || $href_link_parts[0] === 'https')) {
				$protocol = Transifex_Live_Integration_Util::get_http_requested_protocol();
				$href_link_parts[0] = $protocol;
				$arr['href'] = implode(':', $href_link_parts);
      		} else {
        		$arr['href'] = $url_map[$language];
			}
			$arr['hreflang'] = $hreflang_map[$language];
			// the code the lang query var holds for this language
			$arr['code'] = $language_map[$language] ?? $language;
      		$arr['is_source'] = ($language === $source);
			array_push( $ret, $arr );
		}
		return $ret;
	}

	/*
	 * Checks whether the canonical URLs setting has been switched off
	 * @return bool Returns true when no canonical must be rendered or changed
	 */
	public function canonical_urls_disabled() {
		return !empty( $this->settings['canonical_urls'] );
	}

	/*
	 * Checks whether an SEO plugin prints the canonical tag itself.
	 *
	 * Two canonical tags that disagree are ignored or misread by search
	 * engines, so when one of these is active its canonical is localized
	 * through seo_canonical_hook() instead of printing a second one.
	 * @return bool Returns true when Yoast SEO or Rank Math is active
	 */
	static function seo_plugin_prints_canonical() {
		return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' );
	}

	/*
	 * Finds the url of the translation for a language.
	 *
	 * @param string $lang The current language code
	 * @param array $hreflangs The entries built by generate_languages_hreflang()
	 * @return string|null The translated url, or null for the source language
	 */
	static function translated_url( $lang, $hreflangs ) {
		if ( empty( $lang ) ) {
			return null;
		}
		foreach ($hreflangs as $hreflang) {
			if ( empty( $hreflang['is_source'] ) && isset( $hreflang['code'] ) && $hreflang['code'] === $lang ) {
				return $hreflang['href'];
			}
		}
		return null;
	}

	/*
	 * The canonical url of the current page: its own url in the current language.
	 *
	 * @param string $lang The current language code
	 * @param string $source_url The url of the page in the source language
	 * @param array $hreflangs The entries built by generate_languages_hreflang()
	 * @return string The canonical url
	 */
	static function canonical_url( $lang, $source_url, $hreflangs ) {
		$translated = self::translated_url( $lang, $hreflangs );
		return ( $translated !== null ) ? $translated : $source_url;
	}

	/*
	 * Points a canonical printed by another plugin at the current translation.
	 *
	 * SEO plugins build the canonical from their own stored permalink, which
	 * is the source language url, so on a translated page it declares the
	 * source page canonical. Only a canonical that addresses the current
	 * page is changed: one set by hand to another page is left as it is.
	 * @param string $canonical The canonical url printed by the other plugin
	 * @param string $lang The current language code
	 * @param string $source_url The url of the page in the source language
	 * @param array $hreflangs The entries built by generate_languages_hreflang()
	 * @return string The canonical url to print
	 */
	static function localize_canonical( $canonical, $lang, $source_url, $hreflangs ) {
		$translated = self::translated_url( $lang, $hreflangs );
		if ( $translated === null ) {
			return $canonical;
		}
		$key = self::url_key( $canonical );
		if ( $key === self::url_key( $source_url ) || $key === self::url_key( $translated ) ) {
			return $translated;
		}
		return $canonical;
	}

	/*
	 * Reduces a url to what identifies the page, for comparing urls
	 * that differ only in scheme, a leading www. or a trailing slash.
	 * @param string $url A url
	 * @return string The comparable form of the url
	 */
	static function url_key( $url ) {
		$parts = parse_url( (string) $url );
		if ( $parts === false ) {
			return (string) $url;
		}
		$host = strtolower( $parts['host'] ?? '' );
		if ( strpos( $host, 'www.' ) === 0 ) {
			$host = substr( $host, 4 );
		}
		$key = $host . rtrim( $parts['path'] ?? '', '/' );
		if ( isset( $parts['query'] ) ) {
			$key .= '?' . $parts['query'];
		}
		return $key;
	}

	/*
	 * WP wpseo_canonical and rank_math/frontend/canonical filters,
	 * localizes the canonical the SEO plugin prints.
	 * @param string $canonical The canonical url
	 * @return string The filtered canonical url
	 */
	public function seo_canonical_hook( $canonical ) {
		if ( !is_string( $canonical ) || $canonical === '' ) {
			return $canonical;
		}
		$urls = $this->current_page_urls();
		if ( !$urls ) {
			return $canonical;
		}
		return self::localize_canonical( $canonical, $urls['lang'], $urls['source_url'], $urls['hreflangs'] );
	}

	/**
	 * Renders HREFLANG tags into the template
	 */
	public function render_hreflang() {
		Plugin_Debug::logTrace();
		$urls = $this->current_page_urls();
		if ( !$urls ) {
			return false;
		}
		$source_url = $urls['source_url'];
		$source_hreflang = $urls['source_hreflang'];
		$hreflang_out = <<<SOURCE
<link rel="alternate" href="$source_url" hreflang="$source_hreflang"/>\n
SOURCE;
		foreach ($urls['hreflangs'] as $hreflang) {
			if ( $hreflang['is_source'] ) {
				continue;
			}
			$href_attr = $hreflang['href'];
			$hreflang_attr = $hreflang['hreflang'];
			$hreflang_out .= <<<HREFLANG
<link rel="alternate" href="$href_attr" hreflang="$hreflang_attr"/>\n
HREFLANG;
		}
		$hreflang_out .= <<<XDEFAULT
<link rel="alternate" href="$source_url" hreflang="x-default"/>\n
XDEFAULT;
		if ( !$this->canonical_urls_disabled() && !self::seo_plugin_prints_canonical() ) {
			$canonical_url = self::canonical_url( $urls['lang'], $source_url, $urls['hreflangs'] );
			// WP prints its own canonical on single posts and pages later in
			// wp_head; this one replaces it for every page type.
			remove_action( 'wp_head', 'rel_canonical' );
			$hreflang_out .= <<<CANONICAL
<link rel="canonical" href="$canonical_url"/>\n
CANONICAL;
		}
		echo $hreflang_out;
		return true;
	}

	/*
	 * Works out the current page's url in each language.
	 *
	 * @return array|false The current language, the source url and hreflang,
	 *   and the entries for every language, or false when the page type is
	 *   not localized
	 */
	private function current_page_urls() {
		if ( !($this->check_rewrite_options()) ) {
			return false;
		}
		global $wp;
		$lang = get_query_var( 'lang' );
		$url_path = add_query_arg( array(), $wp->request );
		if ( $this->url_option_name == 'subdomain' ) {
			$source_url_path = (substr( $url_path, 0, strlen( $lang ) ) === $lang) ? substr( $url_path, strlen( $lang ), strlen( $url_path ) ) : $url_path;
		} else if ( $this->url_option_name == 'subdirectory' ) {
			if ($url_path == $lang) {
				$source_url_path = (substr( $url_path, 0, strlen( $lang ) ) === $lang) ? substr( $url_path, strlen( $lang ), strlen( $url_path ) ) : $url_path;
			} else {
				$source_url_path = (substr( $url_path, 0, strlen( $lang ) + 1 ) === $lang .'/') ? substr( $url_path, strlen( $lang ) + 1, strlen( $url_path ) ) : $url_path;
			}
		}
		$source = $this->settings['source_language'];
		$site_url_slash_maybe = (new Transifex_Live_Integration_WP_Services($this->settings))->get_site_url();
		$site_url = rtrim( $site_url_slash_maybe, '/' ) . '/';
		$source_url_path = ltrim( $source_url_path, '/' );
		$unslashed_source_url = $site_url . $source_url_path;
		$source_url = rtrim( $unslashed_source_url, '/' ) . '/';
		$hreflangs = $this->generate_languages_hreflang( $source_url_path, $this->languages, $this->language_map, $this->hreflang_map  );
		$source_hreflang = '';
		foreach ($hreflangs as $hreflang) {
			if ( $hreflang['is_source'] ) {
				$source_hreflang = $hreflang['hreflang'];
				break;
			}
		}
		// If source_hreflang is not found,
		// use the source language as default
		if ( empty( $source_hreflang ) ) {
			$source_hreflang = $source;
		}
		return array(
			'lang' => $lang,
			'source_url' => $source_url,
			'source_hreflang' => $source_hreflang,
			'hreflangs' => $hreflangs,
		);
	}
}

?>
