<?php

/**
 * Defaults for plugin settings
 * @package TransifexLiveIntegration
 */

/**
 * Static class for settings defaults
 */
class Transifex_Live_Integration_Defaults {

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
			'url_options' => 2,
			'subdomain_pattern' => '',
			'rewrite_options' => [
				'add_rewrites_post' => [
					'text' => 'Post',
					'value' => 0
				],
				'add_rewrites_page' => [
					'text' => 'Pages',
					'value' => 0
				],
				'add_rewrites_author' => [
					'text' => 'Authors',
					'value' => 0
				],
				'add_rewrites_date' => [
					'text' => 'Date',
					'value' => 0
				],
				'add_rewrites_tag' => [
					'text' => 'Tags',
					'value' => 0
				],
				'add_rewrites_category' => [
					'text' => 'Categories',
					'value' => 0
				],
				'add_rewrites_search' => [
					'text' => 'Search',
					'value' => 0
				],
				'add_rewrites_feed' => [
					'text' => 'Feed',
					'value' => 0
				],
				'add_rewrites_root' => [
					'text' => 'Root',
					'value' => 0
				],
				'add_rewrites_reverse_template_links' => [
					'text' => 'Reverse Template Links',
					'value' => 0
				]
			]
			
		);
	}

}
