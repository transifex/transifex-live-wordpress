<div class="wrap transifex-live-settings">
    <h2><?php _e( 'Transifex Live Wordpress Plugin Settings', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></h2>
    <form id="settings_form" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field( 'transifex_live_settings', 'transifex_live_nonce' ); ?>
        <p><?php _e( 'Transifex Live is a new, innovative way to localize your website with one snippet of JavaScript.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?><br>
			<?php _e( 'It eliminates the hassle of extracting phrases from your code for translation, dealing with system integrations, or waiting for the next deployment to take translations live.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></p>
        <p><?php _e( 'This plugin requires a Transifex Live API key.' ); ?>&nbsp;&nbsp;<a href="<?php echo $settings['urls']['api_key_landing_page']; ?>"><?php _e( 'Click here to sign up and get a API key for free.' ) ?></a> 
        <p><?php _e( 'Having troubles getting localized urls working?' ); ?>&nbsp;&nbsp;<a href="/wp-admin/options-permalink.php"><?php _e( 'Click here to manually refresh them!' ) ?></a> 

		<table class="form-table">
            <tbody>
                <!-- API Key -->
                <tr>
                    <th scope="row"><label for="transifex_live_settings[api_key]"><?php _e( 'Transifex Live API Key', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></label></th>
                    <td>
                        <input required name="transifex_live_settings[api_key]" type="text" id="transifex_live_settings_api_key" value="<?php echo $settings['api_key']; ?>" class="regular-text" placeholder="<?php _e( 'This field is required.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
						<?php _e( 'Language Picker Styling', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>
                    </th>
                    <td class="forminp">
						<label class="enable_checkbox" for="transifex_live_settings_enable_frontend_css">
                            <input name="transifex_live_settings[enable_frontend_css]" type="hidden" value="0">
                            <input name="transifex_live_settings[enable_frontend_css]" id="transifex_live_settings_enable_frontend_css" type="checkbox" value="1" <?php checked( $settings['enable_frontend_css'] ); ?>>
							<?php _e( 'Enable', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>
                        </label>
						<?php
						foreach ($settings['colors'] as $key => $value) {
							if ( empty( $settings['colors'][$key] ) )
								$settings['colors'][$key] = $value;
							Transifex_Live_Integration_Settings_Util::color_picker( $settings['color_labels'][$key], 'transifex_live_colors[' . $key . ']', $settings['colors'][$key] );
						}
						?><br>
                    </td>
                </tr>
			    <tr valign="top">
						<?php Transifex_Live_Integration_Settings_Util::render_language_mapper($language_lookup,$settings); ?>
                </tr>
            </tbody>
        </table>
		<input type="hidden" value="<?php echo htmlentities($raw_transifex_languages)?>" name="transifex_live_settings[raw_transifex_languages]" id="transifex_live_settings_raw_transifex_languages" />
		<input type="hidden" value="<?php echo implode(",",$languages)?>" name="transifex_live_settings[transifex_languages]" id="transifex_live_settings_transifex_languages" />
		<input type="hidden" value="<?php echo htmlentities(json_encode($language_lookup))?>" name="transifex_live_settings[language_lookup]" id="transifex_live_settings_language_lookup" />

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