```<div class="wrap transifex-live-settings">
    <h2><?php _e( 'Transifex Live Translation Plugin Settings', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></h2>

	<form id="transifex_live_settings_form" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field( 'transifex_live_settings', 'transifex_live_nonce' ); ?>
		<p><?php _e( 'Translate your WordPress site without complexities.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></p>
		<p><?php _e( 'Before using this plugin, be sure you have a Transifex Live API key. ' ); ?>&nbsp;<a target="_blank" href="<?php echo $settings['urls']['api_key_landing_page']; ?>"><?php _e( 'Click here to sign up and get an API key.' ) ?></a>
		<table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="transifex_live_settings[api_key]"><?php _e( 'Transifex Live API Key', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></label></th>
                    <td>
                        <p>
							<input required name="transifex_live_settings[api_key]" type="text" id="transifex_live_settings_api_key" value="<?php echo $settings['api_key']; ?>" class="regular-text" placeholder="<?php _e( 'This field is required.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>">
							<input type="hidden" value="<?php echo $settings['api_key']; ?>" name="transifex_live_settings[previous_api_key]" id="transifex_live_settings_raw_transifex_previous_api_key" />
							<input type="button" disabled="true" name="check" id="transifex_live_settings_api_key_button" class="button button-primary" value="Check">
						</p>
						<p id="transifex_live_settings_api_key_message">
							<span id="transifex_live_settings_api_key_message_validating" class="hide-if-js"><?php _e('Validating your key!', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></span>							
							<span id="transifex_live_settings_api_key_message_valid" class="hide-if-js"><?php _e('Success! Advanced SEO settings enabled.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></span>
							<span id="transifex_live_settings_api_key_message_error" class="hide-if-js"><?php _e("Oops! Please make sure you've entered a valid API key.", TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></span>
							<span id="transifex_live_settings_api_key_message_missing" class="hide-if-js"><?php _e("D'oh! No languages have been published from Transifex Live yet.", TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></span>
						</p>
					</td>
                </tr>
				<tr valign="top">
				</tr></table>
		<h2><?php _e('Advanced SEO Settings', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></h2>
		<p><?php _e('This plugin lets you set unique, language/region-specific URLs for your site and tells search engines what language a page is in. This is done by creating new language subdirectories through the plugin, or by pointing to existing language subdomains. In all cases, the plugin will add the Transifex Live JavaScript snippet to your site.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></p>
		<table class="form-table">
			<tr>
				<td>
					<label for="transifex_live_settings_url_options">
						<p><input type="radio" disabled="true" id="transifex_live_settings_url_options_none" name="transifex_live_settings[url_options_none]" value="1" <?php echo $url_options_none ?>><?php _e('Disabled – Just add the Transifex Live JavaScript snippet to my site. <a target="_blank" href="http://docs.transifex.com/integrations/wordpress/#disabled"><b>Learn more</b></a>.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></p>
						<p><input type="radio" disabled="true" id="transifex_live_settings_url_options_subdirectory" name="transifex_live_settings[url_options_subdirectory]" value="1" <?php echo $url_options_subdirectory ?>><?php _e('Subdirectory – Create new language subdirectories through the plugin, e.g. <code>http://www.example.com/fr/</code>. <a target="_blank" href="http://docs.transifex.com/integrations/wordpress/#subdirectories"><b>Learn more</b></a>.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></p>
						<p><input type="radio" disabled="true" id="transifex_live_settings_url_options_subdomain" name="transifex_live_settings[url_options_subdomain]" value="1" <?php echo $url_options_subdomain ?>><?php _e('Subdomain – Point the plugin to existing language subdomains, e.g. <code>http://fr.example.com</code>. <a target="_blank" href="http://docs.transifex.com/integrations/wordpress/#subdomains"><b>Learn more</b></a>.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></p>
						<input type="hidden" id="transifex_live_settings_url_options" name="transifex_live_settings[url_options]" value="<?php echo $url_options ?>" >
					</label>
					<p class="description"><?php _e('<b>Note:</b> When you choose the Subdirectory or Subdomain options, the plugin will automatically <a target="_blank" href="http://docs.transifex.com/integrations/wordpress/#hreflang-tag"><b>add hreflang tags</b></a> to the header of your site.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></p>
				</td></tr></table>
		<table class="form-table">
			<tr class="custom-urls-settings">
				<th scope="row" class="titledesc adds-rewrites"><?php _e( 'Published Languages', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></th>
				<td>
					<p class="url-structure-subdirectory"><?php _e( 'Below is a list of languages published from Transifex Live. For each language, you can set the name of the subdirectory. Your URLs will follow the pattern of <code>www.example.com/%lang%/</code>, with the language code always appearing immediately after your domain.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>
					</p>
					<p class="url-structure-subdomain"><?php _e( 'Below is a list of languages published from Transifex Live. If you’ve set up language subdomains for your site, enter the language subdomain names below. So if <code>fr.example.com</code> is the subdomain for your French site, put in <code>fr</code>. If you don’t have language subdomains set up yet, be sure they match what’s below when you set them up.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>
					</p>
					<br/>
					<input type="hidden" value="<?php echo $source_language ?>" name="transifex_live_settings[source_language]" id="transifex_live_settings_source_language" />
					<p id="transifex_live_languages">
					<table id="transifex_live_language_map_table"><tr><th scope="row"><?php _e('Language', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></th><th scope="row"><?php _e('Code', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></th></tr>
						<tr id="transifex_live_language_map_template" style="display:none">
							<td style="padding:0px"><span class="tx-language"></span></td>
							<td style="padding:0px"><input class="tx-code" type="text" style="width:100px"></td>
						</tr>
					</table>
					<span id="transifex_live_languages_message" class="hide-if-js"><?php _e( "Your languages can't be loaded. Please re-check your API key.", TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></span>
					<span id="transifex_live_sync_message" class="hide-if-js"><?php _e('Refreshing languages will replace your current codes with those from Transifex Live. Continue?', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></span>
					</p>

					<p class="submit"><input type="button" name="sync" id="transifex_live_sync" class="button button-primary" value="<?php _e('Refresh Languages List', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>"></p>
					<p class="description" id="transifex_live_tagline-description"><?php _e('Tweak your localized urls changing the language codes above.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></p>
				</td>
			</tr>
			<tr class="url-structure-subdirectory">
				<th><?php _e('Subdirectory Options', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></th>
				<td>
					<p><?php _e('Choose which WordPress content types you want to enable language subdirectories for.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></p>
					<p>
					<table id="transifex_live_settings_rewrite-options">
						<tr>
							<td style="padding:0px"><input id="transifex_live_settings_rewrite_option_all" name="transifex_live_settings[rewrite_option_all]" value="1" type="checkbox" <?php echo $checked_rewrite_option_all ?>><?php _e('All', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></td>
						</tr>
						<tr>
							<td style="padding:0px">
							<input class="all_selector" id="transifex_live_settings_static_frontpage_support" name="transifex_live_settings[static_frontpage_support]" value="1" type="checkbox" <?php echo $checked_static_frontpage_support ?>><?php _e('Static Frontpage Support', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>
							</td>
						</tr>
						<?php Transifex_Live_Integration_Admin_Util::render_url_options( $rewrite_options_array ); ?>
					</table>
					</p>
					<p class="url-structure-subdirectory description"><?php _e('Having trouble getting language/region-specific URLs working? <a target="_blank" href="http://docs.transifex.com/integrations/wordpress/#troubleshooting-tips">Check out our additional troubleshooting tips!', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></a></p>
				</td></tr>
			<tr class="prerender-options hide-if-js">
				<th><?php _e( 'Prerender for Crawlers', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></th>
				<td>
					<p><input type="checkbox" id="transifex_live_settings_enable_prerender" name="transifex_live_settings[enable_prerender]" value="1" <?php echo $checked_enable_prerender ?> /><?php _e('Enable Prerender', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>
					</p>
                    <div class="prerender-enable-options hide-if-js">
						<label for="transifex_live_settings[prerender_url]"><?php _e( 'Prerender URL', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></label>
						<input name="transifex_live_settings[prerender_url]" type="text" id="transifex_live_settings_prerender_url" value="<?php echo $settings['prerender_url']; ?>" class="regular-text" placeholder="<?php _e( 'Put your prerender url here.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>">
					<p>
						<input type="checkbox" id="transifex_live_settings_enable_prerender_check" name="transifex_live_settings[enable_prerender_check]" value="1" <?php echo $checked_enable_prerender_check ?> /><?php _e( 'Enable Prerender Check', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>
						<input name="transifex_live_settings[prerender_header_check_key]" type="text" id="transifex_live_settings_prerender_header_check_key" value="<?php echo $settings['prerender_header_check_key']; ?>" class="regular-text" placeholder="<?php _e( 'Prerender Check Key', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>">
						<input name="transifex_live_settings[prerender_header_check_value]" type="text" id="transifex_live_settings_prerender_header_check_value" value="<?php echo $settings['prerender_header_check_value']; ?>" class="regular-text" placeholder="<?php _e( 'Prerender Check Header Value', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>">
					</p>
					<p>
						<input type="checkbox" id="transifex_live_settings_prerender_enable_vary_header" name="transifex_live_settings[prerender_enable_vary_header]" value="1" <?php echo $checked_prerender_enable_vary_header ?> /><?php _e( 'Enable Vary Header', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>
						<input name="transifex_live_settings[prerender_vary_header_value]" type="text" id="transifex_live_settings_prerender_vary_header_value" value="<?php echo $settings['prerender_vary_header_value']; ?>" class="regular-text" placeholder="<?php _e( 'Value for Vary Header', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>">
					</p>
					<p>
						<input type="checkbox" id="transifex_live_settings_prerender_enable_response_header" name="transifex_live_settings[prerender_enable_response_header]" value="1" <?php echo $checked_prerender_enable_response_header ?> /><?php _e( 'Enable Custom Prerender Headers', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>
						<input name="transifex_live_settings[prerender_response_headers]" type="text" id="transifex_live_settings_prerender_response_headers" value="<?php echo htmlentities( stripslashes($prerender_response_headers)); ?>" class="regular-text" placeholder="<?php _e( 'Custom Headers for Prerender Responses', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>">
					</p>
					<p>
						<input type="checkbox" id="transifex_live_settings_prerender_enable_cookie" name="transifex_live_settings[prerender_enable_cookie]" value="1" <?php echo $checked_prerender_enable_cookie ?> /><?php _e( 'Enable Custom Cookie Prerender Responses', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>
						<input name="transifex_live_settings[prerender_cookie]" type="text" id="transifex_live_settings_prerender_cookie" value="<?php echo htmlentities( stripslashes($prerender_cookie)); ?>" class="regular-text" placeholder="<?php _e( 'Custom Cookie for Prerender Responses', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>">
					</p>
					<p>
						<input name="transifex_live_settings[whitelist_crawlers]" type="text" id="transifex_live_settings_whitelist_crawlers" value="<?php echo $settings['whitelist_crawlers']; ?>" class="regular-text" placeholder="<?php _e( 'Regex whitelist of allowed crawlers.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>">
						<input name="transifex_live_settings[generic_bot_types]" type="text" id="transifex_live_settings_generic_bot_types" value="<?php echo $settings['generic_bot_types']; ?>" class="regular-text" placeholder="<?php _e( 'Regex list of crawler type keywords.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>">
					</p>
					<p class="description"><?php _e( 'Important so crawler and bots can see your translated content <a target="_blank" href="#">Check out our docs for details.</a>', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></p>
					</div>
				</td></tr>
			</tbody>
        </table>
		<input name="transifex_live_settings[enable_custom_urls]" id="transifex_live_settings_custom_urls" type="hidden" value="<?php echo $checked_custom_urls ?>" >
		<input type="hidden" value="<?php echo htmlentities( stripslashes( $language_map ) ) ?>" name="transifex_live_settings[language_map]" id="transifex_live_settings_language_map" />
		<input type="hidden" value="<?php echo htmlentities( stripslashes( $transifex_settings_settings ) ) ?>" name="transifex_live_transifex_settings[settings]" id="transifex_live_transifex_settings_settings" />
		<input type="hidden" value="<?php echo htmlentities( $settings['subdomain_pattern'] ) ?>" name="transifex_live_settings[subdomain_pattern]" id="transifex_live_settings_subdomain_pattern" />
		<input type="hidden" value="<?php echo htmlentities( stripslashes( $languages_regex ) ) ?>" name="transifex_live_settings[languages_regex]" id="transifex_live_settings_languages_regex" />
		<input type="hidden" value="<?php echo htmlentities( stripslashes( $languages ) ) ?>" name="transifex_live_settings[transifex_languages]" id="transifex_live_settings_transifex_languages" />
		<input type="hidden" value="<?php echo htmlentities( stripslashes( $language_lookup ) ) ?>" name="transifex_live_settings[language_lookup]" id="transifex_live_settings_language_lookup" />
		<input type="hidden" value="<?php echo htmlentities( stripslashes( $settings['generic_bot_types'] ) ) ?>" name="transifex_live_settings[generic_bot_types]" id="transifex_live_settings_generic_bot_types" />
		<input type="hidden" value="<?php echo htmlentities( stripslashes( $settings['whitelist_crawlers'] ) ) ?>" name="transifex_live_settings[whitelist_crawlers]" id="transifex_live_settings_whitelist_crawlers" />
		<input type="hidden" value="0" name="transifex_live_settings[debug]" id="transifex_live_settings_debug" />
		<p class="submit"><input disabled="true" type="submit" name="submit" id="transifex_live_submit" class="button button-primary" value="<?php _e( 'Save Changes', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>"></p>
	</form>
    <p>
		<a href="http://docs.transifex.com/integrations/wordpress/" target="_blank" ><?php _e( 'Plugin documentation', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></a> | <?php _e( 'Thank you for using Transifex!', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>
        </a>
    </p>
</div>
