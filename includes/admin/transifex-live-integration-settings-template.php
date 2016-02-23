<div class="wrap transifex-live-settings">
    <h2><?php _e( 'Transifex Live Translation Plugin Settings', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></h2>

	<form id="settings_form" method="post" enctype="multipart/form-data">
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
						<p id="transifex_live_settings_api_key_message">&nbsp;</p>
					</td>
                </tr>
				<tr valign="top">
				</tr></table>
		<h2>Advanced SEO Settings</h2>
		<p>This plugin lets you set unique, language/region-specific URLs for your site and tells search engines what language a page is in. This is done by creating new language subdirectories through the plugin, or by pointing to existing language subdomains. In all cases, the plugin will add the Transifex Live JavaScript snippet to your site.</p>
		<table class="form-table">
			<tr>
				<td>
					<label for="transifex_live_settings_url_options">
						<p><input type="radio" disabled="true" id="transifex_live_settings_url_options_none" name="transifex_live_settings[url_options_none]" value="1" <?php echo $url_options_none ?>> Disabled – Just add the Transifex Live JavaScript snippet to my site. <a target="_blank" href="http://docs.transifex.com/integrations/wordpress/#disabled"><b>Learn more</b></a>.</p>
						<p><input type="radio" disabled="true" id="transifex_live_settings_url_options_subdirectory" name="transifex_live_settings[url_options_subdirectory]" value="1" <?php echo $url_options_subdirectory ?>> Subdirectory – Create new language subdirectories through the plugin, e.g. <code>http://www.example.com/fr/</code>. <a target="_blank" href="http://docs.transifex.com/integrations/wordpress/#subdirectories"><b>Learn more</b></a>.</p>
						<p><input type="radio" disabled="true" id="transifex_live_settings_url_options_subdomain" name="transifex_live_settings[url_options_subdomain]" value="1" <?php echo $url_options_subdomain ?>> Subdomain – Point the plugin to existing language subdomains, e.g. <code>http://fr.example.com</code>. <a target="_blank" href="http://docs.transifex.com/integrations/wordpress/#subdomains"><b>Learn more</b></a>.</p>
						<input type="hidden" id="transifex_live_settings_url_options" name="transifex_live_settings[url_options]" value="<?php echo $url_options ?>" >
					</label>
					<p><b>Note:</b> When you choose the Subdirectory or Subdomain options, the plugin will automatically <a target="_blank" href="http://docs.transifex.com/integrations/wordpress/#hreflang-tag"><b>add hreflang tags</b></a> to the header of your site.</p>
				</td></tr></table>
		<table class="form-table">
			<tr class="custom-urls-settings">
				<th scope="row" class="titledesc adds-rewrites">Published Languages</th>
				<td>
					<p class="url-structure-subdirectory">Below is a list of languages published from Transifex Live. For each language, you can set the name of the subdirectory. Your URLs will follow the pattern of <code>www.example.com/%lang%/</code>, with the language code always appearing immediately after your domain.</p>
					<p class="url-structure-subdomain">Below is a list of languages published from Transifex Live. If you’ve set up language subdomains for your site, enter the language subdomain names below. So if <code>fr.example.com</code> is the subdomain for your French site, put in <code>fr</code>. If you don’t have language subdomains set up yet, be sure they match what’s below when you set them up.</p>
					<br/>
					<input type="hidden" value="<?php echo $source_language ?>" name="transifex_live_settings[source_language]" id="transifex_live_settings_source_language" />
					<p id="transifex_live_languages">
					<table id="language_map_table"><tr><th scope="row">Language</th><th scope="row">Code</th></tr>
						<tr id="language_map_template" style="display:none">
							<td style="padding:0px"><span class="tx-language">Dummy</span></td>
							<td style="padding:0px"><input class="tx-code" type="text" style="width:100px" name="dummy-name" id="dummy-id" value=""></td>
						</tr>
					</table>
					<span id="transifex_live_languages_message">Your languages can't be loaded. Please re-check your API key.</span>
					</p>
					<p class="submit"><input type="button" name="sync" id="sync" class="button button-primary" value="Refresh Languages List"></p>
					<p class="description" id="tagline-description">Tweak your localized urls.</p>
				</td>
			</tr>
			<tr class="url-structure-subdirectory">
				<th>Subdirectory Options</th>
				<td>
					<p>Choose which WordPress content types you want to enable language subdirectories for.</p>
					<p>
					<table id="transifex_live_settings_rewrite-options">
						<tr>
							<td style="padding:0px"><input id="transifex_live_settings_rewrite_option_all" name="transifex_live_settings[rewrite_option_all]" value="1" type="checkbox" <?php echo $checked_rewrite_option_all ?>>All</td>
						</tr>
						<?php Transifex_Live_Integration_Settings_Util::render_url_options( $rewrite_options_array ); ?>
					</table>
					</p>
					<p class="url-structure-subdirectory">Having trouble getting language/region-specific URLs working? <a target="_blank" href="http://docs.transifex.com/integrations/wordpress/#troubleshooting-tips">Check out our additional troubleshooting tips!</a></p>
				</td></tr>
			</tbody>
        </table>
		<input name="transifex_live_settings[enable_custom_urls]" id="transifex_live_settings_custom_urls" type="hidden" value="<?php echo $checked_custom_urls ?>" >
		<input type="hidden" value="<?php echo htmlentities( $settings['subdomain_pattern'] ) ?>" name="transifex_live_settings[subdomain_pattern]" id="transifex_live_settings_subdomain_pattern" />
		<input type="hidden" value="<?php echo htmlentities( stripslashes( $languages_regex ) ) ?>" name="transifex_live_settings[languages_regex]" id="transifex_live_settings_languages_regex" />
		<input type="hidden" value="<?php echo htmlentities( stripslashes( $languages ) ) ?>" name="transifex_live_settings[transifex_languages]" id="transifex_live_settings_transifex_languages" />
		<input type="hidden" value="<?php echo htmlentities( stripslashes( $language_lookup ) ) ?>" name="transifex_live_settings[language_lookup]" id="transifex_live_settings_language_lookup" />
        <input type="hidden" value="<?php echo htmlentities( stripslashes( $language_map ) ) ?>" name="transifex_live_settings[language_map]" id="transifex_live_settings_language_map" />
		<p class="submit"><input disabled="true" type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>"></p>
	</form>
    <p>
			<a href="http://docs.transifex.com/integrations/wordpress/" target="_blank" ><?php _e( 'Plugin documentation', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></a> | <?php _e( 'Thank you for using Transifex!', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>
        </a>
    </p>
</div>
