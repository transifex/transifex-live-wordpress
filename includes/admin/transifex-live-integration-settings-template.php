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
					<?php Transifex_Live_Integration_Settings_Util::render_language_mapper( $language_lookup, $settings ); ?>
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
