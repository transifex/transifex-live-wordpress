<?php

class Transifex_Live_Integration_Settings_Util {

	const EMPTY_TRANSIFEX_LANGUAGES_PATTERN = '/^transifex_languages\(\{\"timestamp\":\".*\"\}\);/';

	/**
	 * Function to retrieve transifex_languages javascript
	 * @param string $api_key API key entered by user.
	 */
	static function get_raw_transifex_languages( $api_key ) {
		Plugin_Debug::logTrace();

		// TODO: move this url to the plugin constants.
		$languages_json_format = "https://cdn.transifex.com/%s/latest/languages.jsonp";
		$request_url = sprintf( $languages_json_format, $api_key );
		$response = wp_remote_get( $request_url ); // TODO: switch to vip_safe_wp_remote_get.
		$response_body = null;
		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 == $response_code ) {
			$response_body = wp_remote_retrieve_body( $response );
			if ( preg_match( self::EMPTY_TRANSIFEX_LANGUAGES_PATTERN, $response_body ) ) {
				Plugin_Debug::logTrace( "empty transifex languages file...skipping" );
				return false;
			}
			return $response_body;
		}
		Plugin_Debug::logTrace( "did not get a 200 getting transifex languages" );
		return false;
	}

	/**
	 * Function to validate transifex_languages javascript
	 * @param string $api_key API key entered by user.
	 * @param string $raw_transifex_languages string to compare.
	 */
	static function check_raw_transifex_languages( $api_key,
			$raw_transifex_languages ) {
		Plugin_Debug::logTrace();
		$s = self::get_raw_transifex_languages( $api_key );
		return strcmp( $s, $raw_transifex_languages ) === 0 ? true : false;
	}

	/**
	 * Function to parse out languages array
	 * @param string $raw_transifex_languages string to parse.
	 */
	static function get_default_languages( $raw_transifex_languages ) {
		Plugin_Debug::logTrace();
		$reg = "/\s*transifex_languages\(\s*(.+?)\s*\);/";
		preg_match( $reg, $raw_transifex_languages, $m );
		$tl_array = json_decode( $m[1], true );
		$tl_t_array = $tl_array['translation'];
		$language_array = array_column( $tl_t_array, 'code' );
		if ( isset( $language_array ) ) {
			return $language_array;
		} else {
			return null;
		}
	}

	/**
	 * Function to parse out an assoc array of language mapping
	 * @param string $raw_transifex_languages string to parse.
	 */
	static function get_language_lookup( $raw_transifex_languages ) {
		Plugin_Debug::logTrace();
		$reg = "/\s*transifex_languages\(\s*(.+?)\s*\);/";
		preg_match( $reg, $raw_transifex_languages, $m );
		$tl_array = json_decode( $m[1], true );
		$tl_t_array = $tl_array['translation'];
		$f = function( $x ) {
			return ['code' => $x['code'], 'name' => $x['tx_name'] ];
		};
		$language_array = array_map( $f, $tl_t_array );
		if ( isset( $language_array ) ) {
			return $language_array;
		} else {
			return null;
		}
	}

	/**
	 * Function to parse out source language
	 * @param string $raw_transifex_languages string to parse.
	 */
	static function get_source( $raw_transifex_languages ) {
		Plugin_Debug::logTrace();
		$reg = "/\s*transifex_languages\(\s*(.+?)\s*\);/";
		preg_match( $reg, $raw_transifex_languages, $m );
		$tl_array = json_decode( $m[1], true );
		$tl_s_array = $tl_array['source'];
		$source_string = $tl_s_array['code'];
		if ( isset( $source_string ) ) {
			return $source_string;
		} else {
			return null;
		}
	}

	/**
	 * Type checking function for lists
	 * @param array $list array to sanitize.
	 *
	 * TODO This feels like wheel re-invention...look for a library
	 */
	static function sanitize_list( $list ) {
		Plugin_Debug::logTrace();
		$list_arr = explode( ',', $list );

		if ( empty( $list_arr ) ) {
			'';
		}

		$count = count( $list_arr );
		for ($i = 0; $i < $count; $i++) {
			$list_arr[$i] = sanitize_html_class( $list_arr[$i] );
		}

		$list_arr = array_filter( $list_arr );
		return implode( ',', $list_arr );
	}

	/**
	 * Type checking function for colors
	 * @param string $color value to check.
	 *
	 * TODO This feels like wheel re-invention...look for a library
	 */
	static function sanitize_hex_color( $color ) {
		Plugin_Debug::logTrace();

		if ( '' === $color ) {
			return '';
		}

		// 3 or 6 hex digits, or the empty string.
		if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
			return $color;
		}

		return null;
	}

	static function render_languages( $languages ) {

		$html = '';
		
		foreach ($languages as $language) {
			$name = $language['name'];
			$code = $language['code'];
			$value = (isset( $settings['wp_language_' . $language['code']] )) ? $settings['wp_language_' . $language['code']] : $language['code'];
			$html .= <<<HTML_TEMPLATE
			<input disabled="true" type="text" class="regular-text" style="width:200px" name="transifex_live_settings[tx_language_$code]" value="$name" />
            <input type="text" name="transifex_live_settings[wp_language_$code]" id="transifex_live_settings_wp_language_$code" value="$value" class="regular-text">
            <br/>
HTML_TEMPLATE;
		}
		echo $html;
	}

	static function render_url_options( $options ) {
		$html = '';

		$i = 0;
		foreach ($options as $option) {
			ob_start();
			checked( $option['checked'] );
			$checked = ob_get_clean();

			$text = $option['text'];
			$id = $option['id'];
			$name = $option['name'];
			$html .= <<<HTML
		<input type="checkbox" id="$id" name="$name" value="1" $checked>$text
HTML;
			if ( $i % 1 == 0 ) {
				$html .= <<<NEWLINE
				<br/>
NEWLINE;
			}
			$i++;
		}
		echo $html;
	}

	/* depd
	  static function render_language_mapper( $language_array, $settings ) {
	  Plugin_Debug::logTrace();

	  if ( !isset( $language_array ) || !count( $language_array ) > 0 ) {
	  Plugin_Debug::logTrace( "$language_array not valid" );
	  return false;
	  }
	  $header_label = __( 'Language/region-specific URLs', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN );
	  $source_language = $settings['source_language'];
	  $source_label = __( 'Source Language', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN );
	  ob_start();
	  checked( $settings['add_rewrites_date'] );
	  $checked_add_rewrites_date = ob_get_clean();

	  ob_start();
	  checked( $settings['add_rewrites_page'] );
	  $checked_add_rewrites_page = ob_get_clean();

	  ob_start();
	  checked( $settings['add_rewrites_author'] );
	  $checked_add_rewrites_author = ob_get_clean();

	  ob_start();
	  checked( $settings['add_rewrites_tag'] );
	  $checked_add_rewrites_tag = ob_get_clean();

	  ob_start();
	  checked( $settings['add_rewrites_category'] );
	  $checked_add_rewrites_category = ob_get_clean();

	  ob_start();
	  checked( $settings['add_rewrites_search'] );
	  $checked_add_rewrites_search = ob_get_clean();

	  ob_start();
	  checked( $settings['add_rewrites_feed'] );
	  $checked_add_rewrites_feed = ob_get_clean();

	  ob_start();
	  checked( $settings['add_rewrites_post'] );
	  $checked_add_rewrites_post = ob_get_clean();

	  ob_start();
	  checked( $settings['add_rewrites_root'] );
	  $checked_add_rewrites_root = ob_get_clean();

	  ob_start();
	  checked( $settings['add_rewrites_reverse_template_links'] );
	  $checked_add_rewrites_reverse_template_links = ob_get_clean();

	  ob_start();
	  checked( $settings['add_rewrites_all'] );
	  $checked_add_rewrites_all = ob_get_clean();

	  $enable_custom_urls_label = __( 'Enable language/region-specific URLs', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN );
	  ob_start();
	  checked( $settings['enable_custom_urls'] );
	  $checked_custom_urls = ob_get_clean();
	  $hide_custom_urls_css = ($settings['enable_custom_urls']) ? '' : ' hide-if-js';
	  switch ($settings['url_options']) {
	  case "1":
	  $hide_add_rewrites = ' hide-if-js';
	  break;
	  case "2":
	  $hide_add_rewrites = ' hide-if-js';
	  break;
	  case "3":
	  $hide_add_rewrites = '';
	  break;
	  }

	  ob_start();
	  selected( $settings['url_options'], 2 );
	  $url_options_subdomain = ob_get_clean();

	  ob_start();
	  selected( $settings['url_options'], 3 );
	  $url_options_subdirectory = ob_get_clean();

	  $site_url = site_url();
	  $site_url_subdirectory_example = $site_url . '/fr';
	  $site_url_array = explode( '/', $site_url );
	  $site_url_array[2] = 'fr.' . $site_url_array[2];
	  $site_url_subdomain_example = implode( '/', $site_url_array );
	  $mapper = <<<SOURCE
	  </tr></table>
	  <h2 class="title">Advanced SEO Settings</h2>
	  <p>The Transifex Live WordPress Plugin lets you set unique, language/region-specific URLs for your site. For example, if the home page of your English site was <code>$site_url</code>, you can set <code>$site_url_subdirectory_example</code> as the home page URL for the French version of your site. New URLs will be generated when you enable this option, so please proceed with caution.</p>
	  <table class="form-table"><tr>
	  <th scope="row">$header_label</th>
	  <td class="forminp">
	  <label class="enable_checkbox" for="transifex_live_settings_enable_custom_urls">
	  <input name="transifex_live_settings[enable_custom_urls]" type="hidden" value="0">
	  <input name="transifex_live_settings[enable_custom_urls]" id="transifex_live_settings_custom_urls" type="checkbox" value="1" $checked_custom_urls>
	  $enable_custom_urls_label
	  </label>
	  </td></tr>
	  <tr class="custom-urls-settings$hide_custom_urls_css">
	  <th scope="row">Use Language/region-specific URLs For</th>
	  <td>
	  <select id="transifex_live_settings_url_options" name="transifex_live_settings[url_options]">
	  <option value="2" $url_options_subdomain>Subdomain ($site_url_subdomain_example)</option>
	  <option value="3" $url_options_subdirectory>Subdirectory ($site_url_subdirectory_example)</option>
	  </select>
	  <br/>
	  <br/>
	  <div class="adds-rewrites$hide_add_rewrites">
	  <input type="checkbox" id="transifex_live_settings_add_rewrites_all" name="transifex_live_settings[add_rewrites_all]" value="1" $checked_add_rewrites_all>All
	  <br/>
	  <input type="checkbox" id="transifex_live_settings_add_rewrites_page" class="all_selector" name="transifex_live_settings[add_rewrites_page]" value="1" $checked_add_rewrites_page>Pages
	  <br/>
	  <input type="checkbox" id="transifex_live_settings_add_rewrites_post" class="all_selector" name="transifex_live_settings[add_rewrites_post]" value="1" $checked_add_rewrites_post >Posts
	  <br/>
	  <input type="checkbox" id="transifex_live_settings_add_rewrites_tag" class="all_selector" name="transifex_live_settings[add_rewrites_tag]" value="1" $checked_add_rewrites_tag>Tags
	  <br/>
	  <input type="checkbox" id="transifex_live_settings_add_rewrites_category" class="all_selector" name="transifex_live_settings[add_rewrites_category]" value="1" $checked_add_rewrites_category>Categories
	  <br/>
	  <input type="checkbox" id="transifex_live_settings_add_rewrites_author" class="all_selector" name="transifex_live_settings[add_rewrites_author]" value="1" $checked_add_rewrites_author>Authors
	  <br/>
	  <input type="checkbox" id="transifex_live_settings_add_rewrites_search" class="all_selector" name="transifex_live_settings[add_rewrites_search]" value="1" $checked_add_rewrites_search>Search
	  <br/>
	  <input type="checkbox" id="transifex_live_settings_add_rewrites_feed" class="all_selector" name="transifex_live_settings[add_rewrites_feed]" value="1" $checked_add_rewrites_feed>Feeds
	  <br/>
	  <input type="checkbox" id="transifex_live_settings_add_rewrites_date" class="all_selector" name="transifex_live_settings[add_rewrites_date]" value="1" $checked_add_rewrites_date >Date
	  <br/>
	  <input type="checkbox" id="transifex_live_settings_add_rewrites_root" class="all_selector" name="transifex_live_settings[add_rewrites_root]" value="1" $checked_add_rewrites_root >Root
	  <br/>
	  <input type="checkbox" id="transifex_live_settings_add_rewrites_reverse_template_links" class="all_selector" name="transifex_live_settings[add_rewrites_reverse_template_links]" value="1" $checked_add_rewrites_reverse_template_links >Reverse Template Links
	  </div>
	  </td></tr>
	  <tr class="custom-urls-settings$hide_custom_urls_css">
	  <th scope="row" class="titledesc">Language/region Codes</th>
	  <td>
	  <p>You can customize the language or region code used in your language/region-specific URLs. The code you choose will always appear immediately after your domain. So if you use <code>fr</code> for your French pages, the URL for your Product page will look something like <code>$site_url_subdirectory_example/product/</code>.</p>
	  <br/>
	  SOURCE;

	  foreach ($language_array as $item) {
	  $name = $item['name'];
	  $code = $item['code'];
	  $value = (isset( $settings['wp_language_' . $item['code']] )) ? $settings['wp_language_' . $item['code']] : $item['code'];
	  $mapper .= <<<MAPPER
	  <input disabled="true" type="text" class="regular-text" style="width:200px" name="transifex_live_settings[tx_language_$code]" value="$name" />
	  <input type="text" name="transifex_live_settings[wp_language_$code]" id="transifex_live_settings_wp_language_$code" value="$value" class="regular-text">
	  <br/>
	  MAPPER;
	  }
	  $mapper .= <<<FOOTER
	  <input type="hidden" value="$source_language" name="transifex_live_settings[source_language]" id="transifex_live_settings_source_language" />
	  <p class="submit"><input type="submit" name="sync" id="sync" class="button button-primary" value="Refresh Languages List"></p>
	  <p>Having trouble getting language/region-specific URLs working? Visit the <a href="/wp-admin/options-permalink.php">Permalinks Settings</a> then return here to clear the WordPress cache, or <a href="https://www.transifex.com/contact/">contact us</a>.</p>
	  </td>
	  FOOTER;
	  echo $mapper;
	  return true;
	  }
	 */
}
