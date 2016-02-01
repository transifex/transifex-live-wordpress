<?php

/**
 * Defaults for plugin settings
 * @package TransifexLiveIntegration
 */

/**
 * Static class for settings defaults
 */
class Transifex_Live_Integration_Defaults {

	
	static function options_values() {
		Plugin_Debug::logTrace();
		return [
				'add_rewrites_post' => 0,
				'add_rewrites_page' => 0,
				'add_rewrites_author' => 0,
				'add_rewrites_date' => 0,
				'add_rewrites_tag' => 0,
				'add_rewrites_category' => 0,
				'add_rewrites_search' => 0,
				'add_rewrites_root' => 0,
				'add_rewrites_reverse_template_links' => 0
			];
	}
	
	static function get_options_text($key) {
		Plugin_Debug::logTrace();
		$arr = [
				'add_rewrites_post' => 'Posts',
				'add_rewrites_page' => 'Pages',
				'add_rewrites_author' => 'Authors',
				'add_rewrites_date' => 'Date',
				'add_rewrites_tag' => 'Tags',
				'add_rewrites_category' => 'Categories',
				'add_rewrites_search' => 'Search',
				'add_rewrites_root' => 'Root',
				'add_rewrites_reverse_template_links' => 'Reverse Template Links'
			];
		return $arr[$key]; 
		
	}
	/**
	 * Static function for settings defaults
	 * @return array Returns the settings array
	 */
	static function settings() {
		Plugin_Debug::logTrace();
		return array(
			'api_key' => null, // This is the only required field and needs to be copied from Live
			'previous_api_key' => null,
			'raw_transifex_languages' => null,
			'transifex_languages' => null,
			'language_lookup' => null,
			
			'enable_custom_urls' => 0,
			'urls' => [
				'rate_us' => 'https://wordpress.org/support/view/plugin-reviews/transifex-live-integration?rate=5#postform',
				'api_key_landing_page' => 'https://www.transifex.com/signup/?utm_source=liveplugin',
			],
			'enable_language_urls' => false,
			'add_language_rewrites' => "none",
			'source_language' => null,
			'languages' => null,
			'hreflang' => false,
			'url_options' => 3,
			'subdomain_pattern' => ''
			
		);
	}

}
