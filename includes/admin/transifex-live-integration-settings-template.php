<div class="wrap transifex-live-settings">
    <h2><?php _e( 'Transifex Live Plugin', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></h2>

	<form id="settings_form" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field( 'transifex_live_settings', 'transifex_live_nonce' ); ?>
		<p><?php _e( 'Transifex Live Plugin helps you to easily translate your WordPress site.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></p>
		<p><?php _e( 'This plugin depends on the Transifex Live service.  You will need an API key in order to use this plugin.' ); ?></p>
		<p><a href="<?php echo $settings['urls']['api_key_landing_page']; ?>"><?php _e( "If you don't have a key, click here to sign up and get one" ) ?></a></p>
		<table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="transifex_live_settings[api_key]"><?php _e( 'Transifex Live API Key:', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></label></th>
                    <td>
                        <p>
							<input required name="transifex_live_settings[api_key]" type="text" id="transifex_live_settings_api_key" value="<?php echo $settings['api_key']; ?>" class="regular-text" placeholder="<?php _e( 'This field is required.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>">
							<input type="hidden" value="<?php echo $settings['api_key']; ?>" name="transifex_live_settings[previous_api_key]" id="transifex_live_settings_raw_transifex_previous_api_key" />
							<input type="button" name="check" id="transifex_live_settings_api_key_button" class="button button-primary" value="Check">
						</p>
						<p id="transifex_live_settings_api_key_message">&nbsp;</p>
					</td>
                </tr>
				<tr valign="top">
				</tr></table>
		<h2>Advanced SEO Configuration</h2>
		<table class="form-table">
			<tr>
				<td>
					<label for="transifex_live_settings_url_options">
						<p><input type="radio" disabled="true" id="transifex_live_settings_url_options_none" name="transifex_live_settings[url_options_none]" value="1" <?php echo $url_options_none ?>> Disabled - In this mode the plugin will simply add the Transifex Live Javascript snippet to every page on your site.  <a href="http://docs.transifex.com/integrations/wordpress/#disabled-mode"><b>Learn more</b></a></p>
						<p><input type="radio" disabled="true" id="transifex_live_settings_url_options_subdirectory" name="transifex_live_settings[url_options_subdirectory]" value="2" <?php echo $url_options_subdirectory ?>> Subdirectory - In this mode the plugin will add localized rewrites to your url path based on the languages published in Transifex Live.  <a href="http://docs.transifex.com/integrations/wordpress/#subdirectories"><b>Learn more</b></a></p>
						<p><input type="radio" disabled="true" id="transifex_live_settings_url_options_subdomain" name="transifex_live_settings[url_options_subdomain]" value="3" <?php echo $url_options_subdomain ?>> Subdomain  - In this mode the plugin will integrate with existing localized subdomain urls.  <a href="http://docs.transifex.com/integrations/wordpress/#subdomains"><b>Learn more</b></a></p>
						<input type="hidden" id="transifex_live_settings_url_options" name="transifex_live_settings[url_options]" value="<?php echo $url_options ?>" >
					</label>
					<p><b>Note:</b>  When Advanced SEO modes are enabled, the plugin will automatically add hreflang tags to the header of your site.  <a href="http://docs.transifex.com/integrations/wordpress/#hreflang-tag"><b>Learn more</b></a></p>
				</td></tr></table>
		<table class="form-table">
			<tr class="custom-urls-settings">
				<th scope="row" class="titledesc adds-rewrites">Published Languages</th>
				<td>
					<p class="url-structure-subdirectory">The plugin has loaded your language codes based off your published languages from Transifex Live.  Your urls will follow this pattern: <code><?php echo $site_url_subdirectory_example ?></code>  <a href="http://docs.transifex.com/integrations/wordpress/#published-languages"><b>Learn more</b></a></p>
					<p class="url-structure-subdomain">The plugin has loaded your language codes based off your published languages from Transifex Live.  You will need to setup your subdomains to match the following pattern: <code><?php echo $site_url_subdomain_example ?></code>  <a href="http://docs.transifex.com/integrations/wordpress/#published-languages"><b>Learn more</b></a></p>
					<br/>
					<input type="hidden" value="<?php echo $source_language ?>" name="transifex_live_settings[source_language]" id="transifex_live_settings_source_language" />
					<p id="transifex_live_languages">
						<span id="transifex_live_languages_message">Transifex Live languages are not loaded. Please re-check your API key.</span>
					</p>
					<p class="description" id="tagline-description">Tweak your localized urls.</p>
				</td>
			</tr>
			<tr class="url-structure-subdirectory">
				<th>Subdirectory Options</th>
				<td>
					<p>These options allow for fine-grained control over which WordPress objects will allow language permalinks.  <a href="http://docs.transifex.com/integrations/wordpress/#subdirectory-options"><b>Learn more</b></a></p>
					<p><input id="transifex_live_settings_rewrite_option_all" name="transifex_live_settings[rewrite_option_all]" value="1" type="checkbox" <?php echo $checked_rewrite_option_all ?>>All</p>
					<p><?php Transifex_Live_Integration_Settings_Util::render_url_options( $rewrite_options_array ); ?></p>
					<p class="description" id="tagline-description">Pick which WordPress objects to add rewrites to.</p>
					<p class="url-structure-subdirectory">Having trouble getting your localized URLs working?  <a href="http://docs.transifex.com/integrations/wordpress/#troubleshooting-tips">Check out our additional troubleshooting tips!</a></p>
					</div>
				</td></tr>
			</tbody>
        </table>
		<input name="transifex_live_settings[enable_custom_urls]" id="transifex_live_settings_custom_urls" type="hidden" value="<?php echo $checked_custom_urls ?>" >
		<input type="hidden" value="<?php echo htmlentities( $settings['subdomain_pattern'] ) ?>" name="transifex_live_settings[subdomain_pattern]" id="transifex_live_settings_subdomain_pattern" />
		<input type="hidden" value="<?php echo htmlentities( stripslashes( $languages_regex ) ) ?>" name="transifex_live_settings[languages_regex]" id="transifex_live_settings_languages_regex" />
		<input type="hidden" value="<?php echo htmlentities( stripslashes( $languages ) ) ?>" name="transifex_live_settings[transifex_languages]" id="transifex_live_settings_transifex_languages" />
		<input type="hidden" value="<?php echo htmlentities( stripslashes( $language_lookup ) ) ?>" name="transifex_live_settings[language_lookup]" id="transifex_live_settings_language_lookup" />
        <input type="hidden" value="<?php echo htmlentities( stripslashes( $language_map ) ) ?>" name="transifex_live_settings[language_map]" id="transifex_live_settings_language_map" />
    </form>
    <p>
        <a href="<?php echo $settings['urls']['rate_us']; ?>">
			<?php _e( 'Thank you for using Transifex!', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>
        </a>
    </p>
</div>
