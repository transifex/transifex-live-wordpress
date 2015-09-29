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
			'api_key' => null,
			'picker' => 'bottom-right',
			'staging' => 0,
			'enable_frontend_css' => 0,
			'colors' => [
				'accent' => '#006f9f',
				'text' => '#ffffff',
				'background' => '#000000',
				'menu' => '#eaf1f7',
				'languages' => '#666666',
			],
			'color_labels' => [
				'accent' => __( 'Accent', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ),
				'text' => __( 'Text', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ),
				'background' => __( 'Background', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ),
				'menu' => __( 'Menu', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ),
				'languages' => __( 'Languages', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ),
			],
			'urls' => [
				'rate_us' => 'https://wordpress.org/support/view/plugin-reviews/transifex-live-integration?rate=5#postform',
				'api_key_landing_page' => 'https://www.transifex.com/live/?utm_source=liveplugin',
			],
		);
	}

}
