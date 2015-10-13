<div class="wrap transifex-live-settings">
    <h2><?php _e( 'Transifex Live Wordpress Plugin Settings', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></h2>
    <form id="settings_form" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field( 'transifex_live_settings', 'transifex_live_nonce' ); ?>
        <p><?php _e( 'Transifex Live is a new, innovative way to localize your website with one snippet of JavaScript.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?><br>
			<?php _e( 'It eliminates the hassle of extracting phrases from your code for translation, dealing with system integrations, or waiting for the next deployment to take translations live.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></p>
        <p><?php _e( 'This plugin requires a Transifex Live API key.' ); ?>&nbsp;&nbsp;<a href="<?php echo $settings['urls']['api_key_landing_page']; ?>"><?php _e( 'Click here to sign up and get a API key for free.' ) ?></a> 
        <table class="form-table">
            <tbody>
                <!-- API Key -->
                <tr>
                    <th scope="row"><label for="transifex_live_settings[api_key]"><?php _e( 'Transifex Live API Key', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></label></th>
                    <td>
                        <input required name="transifex_live_settings[api_key]" type="text" id="transifex_live_settings_api_key" value="<?php echo $settings['api_key']; ?>" class="regular-text" placeholder="<?php _e( 'This field is required.', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e( 'Plugin Options', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></label></th>
                    <td>
                        <!-- Staging -->
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Is this a staging server?', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?></span></legend>
                            <label for="transifex_live_settings_staging">
                                <input name="transifex_live_settings[staging]" type="hidden" value="0">
                                <input name="transifex_live_settings[staging]" type="checkbox" id="transifex_live_settings_staging" value="1" <?php checked( $settings['staging'] ); ?>>
								<?php _e( 'Is this a staging server?', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>
                            </label>
                        </fieldset>
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
                    <th scope="row" class="titledesc">
						<?php _e( 'Language URLs', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>
                    </th>
                    <td class="forminp">
						<label class="enable_checkbox" for="transifex_live_settings_enable_language_urls">
                            <input name="transifex_live_settings[enable_language_urls]" type="hidden" value="0">
                            <input name="transifex_live_settings[enable_language_urls]" id="transifex_live_settings_enable_language_urls" type="checkbox" value="1" <?php checked( $settings['enable_language_urls'] ); ?>>
							<?php _e( 'Enable', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>
                        </label>
						<br/>
						<input name="transifex_live_settings[source_language]" type="text" id="transifex_live_settings_source_language" value="<?php echo $settings['source_language']; ?>" class="regular-text" placeholder="<?php _e( 'Enter source', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>">
						<input name="transifex_live_settings[language_codes]" type="text" id="transifex_live_settings_language_codes" value="<?php echo $settings['language_codes']; ?>" class="regular-text" placeholder="<?php _e( 'Enter locales', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN ); ?>">
                    </td>
                </tr>
            </tbody>
        </table>
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