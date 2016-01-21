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
			'add_rewrites_date' => 0,
			'add_rewrites_page' => 0,
			'add_rewrites_author' => 0,
			'add_rewrites_tag' => 0,
			'add_rewrites_category' => 0,
			'add_rewrites_search' => 0,
			'add_rewrites_feed' => 0,
			'add_rewrites_post' => 0,
			'add_rewrites_root' => 0,
			'add_rewrites_all' => 0,
			'url_options' => 1,
			'subdomain_pattern' => '',
			'add_rewrites_reverse_template_links' => 0
		);
	}

}
