<?php

/**
 * Includes Admin Generalized Functions
 * @package TransifexLiveIntegration
 */

/**
 * Admin Utils Class
 * All functions should be called statically
 */
class Transifex_Live_Integration_Admin_Util {

	/**
	 * Function to add a 'notranslate' div to the WP admin bar
	 */
	static function wp_before_admin_bar_render_hook() {
		Plugin_Debug::logTrace();
		echo ('<div class="notranslate">');
	}

	/**
	 * Function to add end tag for 'notranslate' div to the WP admin bar
	 */
	static function wp_after_admin_bar_render_hook() {
		Plugin_Debug::logTrace();
		echo ('</div>');
	}

	/**
	 * Returns the site_url in a tokenized form for use by other libraries
	 * @param string $site_url Generally should be site_url()
	 * @param string $url_option_setting The plugin option setting for special urls 
	 * @return string/false Returns the tokenized string or false
	 * [ 3 = Subdirectory, 2 = Subdomain, * = Skip ] 
	 */
	static function generate_tokenized_url( $site_url, $url_option_setting ) {
		Plugin_Debug::logTrace();

		if ( $url_option_setting !== '2' && $url_option_setting != '3' ) {
			Plugin_Debug::logTrace( 'No URL option, skipping tokenization' );
			return false;
		}

		if ( !($site_url) ) {
			Plugin_Debug::logTrace( 'Failed site URL truthiness, skipping tokenization' );
			return false;
		}

		$site_url = rtrim($site_url, '/');
		$slashes = explode( "/", $site_url );
		if ( $url_option_setting === '3' ) { // Subdirectory option
			array_push( $slashes, '%lang%' );
			array_push( $slashes, '' );
		}
		if ( $url_option_setting === '2' ) { // Subdomain option
			$dots = explode( ".", $slashes[2] );
			$dots[0] = '%lang%';
			$slashes[2] = implode( '.', $dots );
			array_push( $slashes, '' );
		}
		$tokenized_url = implode( '/', $slashes );
		return ($tokenized_url) ? $tokenized_url : false;
	}

	/**
	 * Renders subdirectory rewrite options
	 * @param array $options Array of options...usually these will be loaded from defaults
	 */
	static function render_url_options( $options ) {
		$html = '';
		$row = '';
		$i = 1;
		foreach ($options as $option) {
			ob_start();
			checked( $option['checked'], 1 );
			$checked = ob_get_clean();
			$text = $option['text'];
			$id = $option['id'];
			$name = $option['name'];
			$row .= <<<ROW
		<td class="option-checkbox" style="padding:0px"><input class="all_selector" type="checkbox" id="$id" name="$name" value="1" $checked />$text</td>
ROW;
			if ( $i % 3 == 0 ) {
				$html .= '<tr>' . $row . '</tr>';
				$row = '';
			}
			$i++;
		}
		echo $html;
	}

	/**
	 * Renders Transifex Live settings for the admin form
	 * @param array $settings Usually these will be loaded from the defaults
	 */
	static function render_transifex_settings( $settings ) {
		$html = '';
		foreach ($settings as $setting) {
			$value = $setting['value'];
			$id = $setting['id'];
			$name = $setting['name'];
			$html .= <<<HTML
<input type="hidden" value="$value" name="$name" id="$id" />
HTML;
		}
		echo $html;
	}

	/**
	 * Builds plugin links displayed on the WP Plugin section, WP filter
	 * @param array $links Existing list of WP plugin links
	 */
	static function action_links_hook( $links ) {
		Plugin_Debug::logTrace();
		$settings_href = add_query_arg( [ 'page' => TRANSIFEX_LIVE_INTEGRATION_NAME ], admin_url( 'options-general.php' ) );
		$settings_text = __( 'Settings', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN );
		$settings_link = <<<SETTINGS
<a href="$settings_href">$settings_text</a>
SETTINGS;
		return array_merge( [ $settings_link ], $links );
	}

	/**
	 * Adds admin page to WP menu, WP action
	 */
	static function admin_menu_hook() {
		Plugin_Debug::logTrace();
		add_options_page( 'Transifex Live', 'Transifex Live', 'manage_options', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN, [ 'Transifex_Live_Integration_Admin', 'options_page' ] );
	}

}
