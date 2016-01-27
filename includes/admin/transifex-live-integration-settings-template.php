<div class="wrap transifex-live-settings">
    <h2><?php _e( 'Transifex Live Translation Plugin Settings', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></h2>
    <form id="settings_form" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field( 'transifex_live_settings', 'transifex_live_nonce' ); ?>
        <p><?php _e( 'Transifex Live makes it easy to translate WordPress sites. There’s no need to create one language per post, insert language tags, or have multiple WordPress instances.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></p>
        <p><?php _e( 'Before using this plugin, be sure you have a Transifex Live API key. ' ); ?>&nbsp;<a href="<?php echo $settings['urls']['api_key_landing_page']; ?>"><?php _e( 'Click here to sign up and get an API key for free.' ) ?></a>
		<table class="form-table">
            <tbody>
                <!-- API Key -->
                <tr>
                    <th scope="row"><label for="transifex_live_settings[api_key]"><?php _e( 'Transifex Live API Key', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></label></th>
                    <td>
                        <input required name="transifex_live_settings[api_key]" type="text" id="transifex_live_settings_api_key" value="<?php echo $settings['api_key']; ?>" class="regular-text" placeholder="<?php _e( 'This field is required.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>">
						<input type="hidden" value="<?php echo $settings['api_key']; ?>" name="transifex_live_settings[previous_api_key]" id="transifex_live_settings_raw_transifex_previous_api_key" />
					</td>
                </tr>
				<tr valign="top">
				</tr></table>
		<h2 class="title">Advanced SEO Settings</h2>
		<p>The Transifex Live Translation Plugin lets you set unique, language/region-specific URLs for your site and tell search engines what language a page is in. This is done by creating new language subdirectories through the plugin, or by pointing to existing language subdomains. When you enable language/region-specific URLs, the plugin will automatically add hreflang tags to the header of your site.</p>
		<table class="form-table"><tr>
				<th scope="row"><?php _e( 'Language/region-specific URLs', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></th>
				<td class="forminp">
					<label class="enable_checkbox" for="transifex_live_settings_enable_custom_urls">
						<input name="transifex_live_settings[enable_custom_urls]" type="hidden" value="0">
						<input name="transifex_live_settings[enable_custom_urls]" id="transifex_live_settings_custom_urls" type="checkbox" value="1" <?php echo $checked_custom_urls ?>>
						<?php _e( 'Use language/region-specific URLs', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>
					</label>
				</td></tr>
			<tr class="custom-urls-settings<?php echo $hide_custom_urls_css ?>">
				<th scope="row">URL Structure</th>
				<td>
					<select id="transifex_live_settings_url_options" name="transifex_live_settings[url_options]">
						<option value="3" <?php echo $url_options_subdirectory ?>>Subdirectory (<?php echo $site_url_subdirectory_example ?>)</option>
            <option value="2" <?php echo $url_options_subdomain ?>>Subdomain (<?php echo $site_url_subdomain_example ?>)</option>
					</select>
					<br/>
					<br/>
					<div class="adds-rewrites<?php echo $hide_add_rewrites ?>">
            <p>Use language/region-specific URLs for:</p>
						<?php Transifex_Live_Integration_Settings_Util::render_url_options( $rewrite_options_array ); ?>
					</div>
				</td></tr>
			<tr class="custom-urls-settings<?php echo $hide_custom_urls_css ?>">
				<th scope="row" class="titledesc">Language/region Codes</th>
        <th scope="row" class="titledesc">Subdomain Names</th>
				<td>
					<p>You can customize the language or region code used in your language/region-specific URLs. The code you choose will always appear immediately after your domain. So if you use <code>fr</code> for your French pages, the URL for your Product page will look something like <code><?php echo $site_url_subdirectory_example?>/product/</code>.</p>
					<p>If you've already set up language subdomains on your site (this has to be done outside of the plugin), enter the language subdomain names below. So if <code>fr.example.com</code> is the subdomain for your French site, put in <code>fr</code>. When the hreflang tags are automatically added to your site’s header, they will point to each of your language subdomains.</p>
					<br/>
					<?php Transifex_Live_Integration_Settings_Util::render_languages( $language_lookup ); ?>
					<input type="hidden" value="$source_language" name="transifex_live_settings[source_language]" id="transifex_live_settings_source_language" />
					<p class="submit"><input type="submit" name="sync" id="sync" class="button button-primary" value="Refresh Languages List"></p>
					<p>Having trouble getting language/region-specific URLs working? Visit the <a href="/wp-admin/options-permalink.php">Permalinks Settings</a> then return here to clear the WordPress cache, or <a href="https://www.transifex.com/contact/">contact us</a>.</p>
				</td>
			</tr>
			</tbody>
        </table>
		<input type="hidden" value="<?php echo htmlentities( $settings['subdomain_pattern'] ) ?>" name="transifex_live_settings[subdomain_pattern]" id="transifex_live_settings_subdomain_pattern" />
		<input type="hidden" value="<?php echo htmlentities( $raw_transifex_languages ) ?>" name="transifex_live_settings[raw_transifex_languages]" id="transifex_live_settings_raw_transifex_languages" />
		<input type="hidden" value="<?php echo implode( ",", $languages ) ?>" name="transifex_live_settings[transifex_languages]" id="transifex_live_settings_transifex_languages" />
		<input type="hidden" value="<?php echo htmlentities( json_encode( $language_lookup ) ) ?>" name="transifex_live_settings[language_lookup]" id="transifex_live_settings_language_lookup" />
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>"></p>
    </form>
    <script>
        jQuery(document).ready(function ($) {
            $("#settings_form").validate;
        });
    </script>
    <p>
        <a href="<?php echo $settings['urls']['rate_us']; ?>">
			<?php _e( 'Thank you for using Transifex!', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>
        </a>
    </p>
</div>
