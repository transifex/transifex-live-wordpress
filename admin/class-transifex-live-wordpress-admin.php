<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://docs.transifex.com/developer/integrations/wordpress
 *
 * @package    Transifex_Live_Wordpress
 * @subpackage Transifex_Live_Wordpress/admin
 */
class Transifex_Live_Wordpress_Admin {

	private $plugin_name;
	private $version;
	private $text_domain;
	private $defaults;
	private $positions;
	private $colors;
	private $custom_picker;
	private $urls;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param    string    $plugin_name     The name of this plugin.
	 * @param    string    $version    		The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $text_domain ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->text_domain = $text_domain;
		$this->define_settings();
		add_action( 'admin_menu', array( $this, 'options_menu' ) );
		add_filter( 'plugin_action_links_' . $this->plugin_name, array( $this, 'action_links' ) );
		add_filter( 'admin_init', array( $this, 'update_settings' ) );
	}

	/**
	 * Define settings
	 */
	private function define_settings() {
		$this->defaults = array(
			'api_key' => null,
			'picker' => 'bottom-right',
			'detectlang' => 1,
			'dynamic' => 1,
			'autocollect' => 1,
			'staging' => 0,
			'enable_frontend_css' => 0,
			'parse_attr' => null,
			'ignore_tags' => null,
			'ignore_class' => null,
		);

		$this->custom_picker = array(
			'custom_picker_id' => ''
		);

		$this->positions = array(
			'top-left' => array( 'location' => 'top-left', $this->text_domain ),
			'top-right' => array( 'location' => 'top-right', $this->text_domain ),
			'bottom-left' => array( 'location' => 'bottom-left', $this->text_domain ),
			'bottom-right' => array( 'location' => 'bottom-right', $this->text_domain ),
			'id' => array( 'location' => 'custom id', $this->text_domain ),
		);

		$this->colors = array(
			'accent' => array(
				'default' => '#006f9f',
				'label' => __( 'Accent', $this->text_domain ),
			),
			'text' => array(
				'default' => '#ffffff',
				'label' => __( 'Text', $this->text_domain ),
			),
			'background' => array(
				'default' => '#000000',
				'label' => __( 'Background', $this->text_domain ),
			),
			'menu' => array(
				'default' => '#eaf1f7',
				'label' => __( 'Menu', $this->text_domain ),
			),
			'languages' => array(
				'default' => '#666666',
				'label' => __( 'Languages', $this->text_domain ),
			),
		);

		$this->urls = array(
			'rate_us' => 'https://wordpress.org/support/view/plugin-reviews/' . $this->plugin_name . '?rate=5#postform',
			'api_key_landing_page' => 'https://www.transifex.com/live/?utm_source=liveplugin'
		);
	}

	/**
	 * Add link to settings menu.
	 */
	public function options_menu() {
		add_options_page( __( 'Transifex Live', $this->text_domain ), __( 'Transifex Live', $this->text_domain ), 'manage_options', $this->text_domain, array( $this, 'options_page' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name . '-admin', TRANSIFEX_LIVE_ADMIN_CSS . $this->plugin_name . '-admin.css', array( ), $this->plugin_version );
	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name . '-admin', TRANSIFEX_LIVE_ADMIN_JS . $this->plugin_name . '-admin.js', array( 'jquery', 'wp-color-picker', 'iris' ), $this->plugin_version, true );
		wp_enqueue_script( $this->plugin_name . '-admin', TRANSIFEX_LIVE_ADMIN_JS . 'jquery.validate.min.js', array( ), null, false );
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @param mixed $links
	 * @return array
	 */
	public function action_links( $links ) {
		return array_merge( array(
			'<a href="' . add_query_arg( array( 'page' => $this->plugin_name ), admin_url( 'options-general.php' ) ) . '">' . __( 'Settings', $this->text_domain ) . '</a>',
		), $links );
	}

	/**
	 * Getter functions for private variables
	 */
	public function get_defaults() {
		return $this->defaults;
	}

	public function get_positions() {
		return $this->positions;
	}

	public function get_colors() {
		return $this->colors;
	}

	public function get_custom_picker() {
		return $this->custom_picker;
	}

	public function get_urls() {
		return $this->urls;
	}

	public function update_settings() {
		if ( isset( $_POST[ 'transifex_live_nonce' ] ) && wp_verify_nonce( $_POST[ 'transifex_live_nonce' ], 'transifex_live_settings' ) ) {
			$settings = $this->sanitize_settings( $_POST );

			if ( isset( $settings[ 'transifex_live_settings' ] ) ) {
				update_option( 'transifex_live_settings', $settings[ 'transifex_live_settings' ] );
			}

			if ( isset( $settings[ 'transifex_live_colors' ] ) ) {
				update_option( 'transifex_live_colors', $settings[ 'transifex_live_colors' ] );
			}
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}
	}

	public function admin_notices() {
		$notice = '';
		if ( isset( $_POST[ 'transifex_live_settings' ] ) ) {
			$notice = '<p>' . __( 'Your changes to the settings have been saved!', $this->text_domain ) . '</p>';
		}

		if ( isset( $_POST[ 'transifex_live_colors' ] ) ) {
			$notice.= '<p>' . __( 'Your changes to the colors have been saved!', $this->text_domain ) . '</p>';
		}

		echo "<div class='notice'>" . $notice . "</div>";
	}

	public function sanitize_settings( $settings ) {
		$settings[ 'transifex_live_settings' ][ 'api_key' ] = ( isset( $settings[ 'transifex_live_settings' ][ 'api_key' ] )) ? sanitize_text_field( $settings[ 'transifex_live_settings' ][ 'api_key' ] ) : '';
		$settings[ 'transifex_live_settings' ][ 'picker' ] = ( in_array( $settings[ 'transifex_live_settings' ][ 'picker' ], array( 'top-left', 'top-right', 'bottom-left', 'bottom-right', 'custom id' ) )) ? $settings[ 'transifex_live_settings' ][ 'picker' ] : $this->defaults[ 'picker' ];
		$settings[ 'transifex_live_settings' ][ 'detectlang' ] = ( $settings[ 'transifex_live_settings' ][ 'detectlang' ] ) ? 1 : 0;
		$settings[ 'transifex_live_settings' ][ 'dynamic' ] = ( $settings[ 'transifex_live_settings' ][ 'dynamic' ] ) ? 1 : 0;
		$settings[ 'transifex_live_settings' ][ 'autocollect' ] = ( $settings[ 'transifex_live_settings' ][ 'autocollect' ] ) ? 1 : 0;
		$settings[ 'transifex_live_settings' ][ 'staging' ] = ( $settings[ 'transifex_live_settings' ][ 'staging' ] ) ? 1 : 0;
		$settings[ 'transifex_live_settings' ][ 'enable_frontend_css' ] = ( $settings[ 'transifex_live_settings' ][ 'enable_frontend_css' ] ) ? 1 : 0;
		$settings[ 'transifex_live_settings' ][ 'parse_attr' ] = $this->sanitize_list( $settings[ 'transifex_live_settings' ][ 'parse_attr' ] );
		$settings[ 'transifex_live_settings' ][ 'ignore_tags' ] = $this->sanitize_list( $settings[ 'transifex_live_settings' ][ 'ignore_tags' ] );
		$settings[ 'transifex_live_settings' ][ 'ignore_class' ] = $this->sanitize_list( $settings[ 'transifex_live_settings' ][ 'ignore_class' ] );
		$settings[ 'transifex_live_settings' ][ 'custom_picker_id' ] = ( isset( $settings[ 'transifex_live_settings' ][ 'custom_picker_id' ] )) ? sanitize_text_field( $settings[ 'transifex_live_settings' ][ 'custom_picker_id' ] ) : '';
		$settings[ 'transifex_live_colors' ][ 'accent' ] = $this->sanitize_hex_color( $settings[ 'transifex_live_colors' ][ 'accent' ] );
		$settings[ 'transifex_live_colors' ][ 'text' ] = $this->sanitize_hex_color( $settings[ 'transifex_live_colors' ][ 'text' ] );
		$settings[ 'transifex_live_colors' ][ 'background' ] = $this->sanitize_hex_color( $settings[ 'transifex_live_colors' ][ 'background' ] );
		$settings[ 'transifex_live_colors' ][ 'menu' ] = $this->sanitize_hex_color( $settings[ 'transifex_live_colors' ][ 'menu' ] );
		$settings[ 'transifex_live_colors' ][ 'languages' ] = $this->sanitize_hex_color( $settings[ 'transifex_live_colors' ][ 'languages' ] );
		return $settings;
	}

	function sanitize_list( $list ) {
		$list_arr = explode( ',', $list );

		if ( empty( $list_arr ) ) {
			'';
		}

		for ( $i = 0; $i < count( $list_arr ); $i++ ) {
			$list_arr[ $i ] = sanitize_html_class( $list_arr[ $i ] );
		}

		$list_arr = array_filter( $list_arr );
		return implode( ',', $list_arr );
	}

	function sanitize_hex_color( $color ) {

		if ( '' === $color )
			return '';

		// 3 or 6 hex digits, or the empty string.
		if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) )
			return $color;

		return null;
	}

	/**
	 * Output a colour picker input box.
	 *
	 * @access public
	 * @param mixed $name
	 * @param mixed $id
	 * @param mixed $value
	 * @return void
	 */
	// TODO Move this out to a partial to allow reuse - @mjjacko 4/23/15
	function color_picker( $name, $id, $value ) {
		echo '<div class="color-box"><strong>' . esc_html( $name ) . '</strong>
			<input name="' . esc_attr( $id ) . '" id="' . esc_attr( str_replace( array( '[', ']' ), array( '_', '' ), $id ) ) . '" type="text" value="' . esc_attr( $value ) . '" class="colorpick" /> <div id="colorPickerDiv_' . esc_attr( $id ) . '" class="colorpickdiv"></div>
		</div>';
	}

	/**
	 * Settings page callback.
	 */
// TODO Move this out to a template - @mjjacko 4/23/15
	public function options_page() {
		$settings = get_option( 'transifex_live_settings', array( ) );
		$settings = array_merge( $this->defaults, $settings );
		$colors = array_map( 'esc_attr', (array) get_option( 'transifex_live_colors', array( ) ) );
		?>
		<div class="wrap transifex-live-settings">
			<h2><?php _e( 'Transifex Live Wordpress Plugin Settings', $this->text_domain ); ?></h2>
			<form id="settings_form" method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'transifex_live_settings', 'transifex_live_nonce' ); ?>
				<p><?php _e( 'Transifex Live is a new, innovative way to localize your website with one snippet of JavaScript.', $this->text_domain ); ?><br>
					<?php _e( 'It eliminates the hassle of extracting phrases from your code for translation, dealing with system integrations, or waiting for the next deployment to take translations live.', $this->text_domain ); ?></p>
				<p><?php _e( 'This plugin requires a Transifex Live API key.' ); ?>&nbsp;&nbsp;<a href="<?php echo $this->urls[ 'api_key_landing_page' ]; ?>"><?php _e( 'Click here to sign up and get a API key for free.' ) ?></a> 
				<table class="form-table">
					<tbody>
						<!-- API Key -->
						<tr>
							<th scope="row"><label for="transifex_live_settings[api_key]"><?php _e( 'Transifex API Key', $this->text_domain ); ?></label></th>
							<td>
								<input required name="transifex_live_settings[api_key]" type="text" id="transifex_live_settings_api_key" value="<?php echo $settings[ 'api_key' ]; ?>" class="regular-text" placeholder="<?php _e( 'This field is required.', $this->text_domain ); ?>">
							</td>
						</tr>
						<tr>
							<th scope="row"><label><?php _e( 'Plugin Options', $this->text_domain ); ?></label></th>
							<td>
								<!-- Detect lang -->
								<fieldset>
									<legend class="screen-reader-text"><span><?php _e( 'Auto-detect the browser locale and translate the page.', $this->text_domain ); ?></span></legend>
									<label for="transifex_live_settings_detectlang">
										<input name="transifex_live_settings[detectlang]" type="hidden" value="0">
										<input name="transifex_live_settings[detectlang]" type="checkbox" id="transifex_live_settings_detectlang" value="1" <?php checked( $settings[ 'detectlang' ] ); ?>>
										<?php _e( 'Auto-detect the browser locale and translate the page.', $this->text_domain ); ?>
									</label>
								</fieldset>
								<!-- Auto collect -->
								<fieldset>
									<legend class="screen-reader-text"><span><?php _e( 'Automatically identify new strings when page content changes.', $this->text_domain ); ?></span></legend>
									<label for="transifex_live_settings_autocollect">
										<input name="transifex_live_settings[autocollect]" type="hidden" value="0">
										<input name="transifex_live_settings[autocollect]" type="checkbox" id="transifex_live_settings_autocollect" value="1" <?php checked( $settings[ 'autocollect' ] ); ?>>
										<?php _e( 'Automatically identify new strings when page content changes.', $this->text_domain ); ?>
									</label>
								</fieldset>
								<!-- Dynamic -->
								<fieldset>
									<legend class="screen-reader-text"><span><?php _e( 'Enable translation of dynamically injected content.', $this->text_domain ); ?></span></legend>
									<label for="transifex_live_settings_dynamic">
										<input name="transifex_live_settings[dynamic]" type="hidden" value="0">
										<input name="transifex_live_settings[dynamic]" type="checkbox" id="transifex_live_settings_dynamic" value="1" <?php checked( $settings[ 'dynamic' ] ); ?>>
										<?php _e( 'Enable translation of dynamically injected content.', $this->text_domain ); ?>
									</label>
								</fieldset>
								<!-- Staging -->
								<fieldset>
									<legend class="screen-reader-text"><span><?php _e( 'Is this a staging server?', $this->text_domain ); ?></span></legend>
									<label for="transifex_live_settings_staging">
										<input name="transifex_live_settings[staging]" type="hidden" value="0">
										<input name="transifex_live_settings[staging]" type="checkbox" id="transifex_live_settings_staging" value="1" <?php checked( $settings[ 'staging' ] ); ?>>
										<?php _e( 'Is this a staging server?', $this->text_domain ); ?>
									</label>
								</fieldset>
								<!-- Parse attr -->
								<fieldset>
									<legend class="screen-reader-text"><span><?php _e( 'Are there any attributes that need to be parsed?', $this->text_domain ); ?></span></legend>
									<label for="transifex_live_settings_parse_attr" class="text_field_fix">Parse attributes</label>
									<input name="transifex_live_settings[parse_attr]" type="text" id="transifex_live_settings_parse_attr" value="<?php echo $settings[ 'parse_attr' ]; ?>" class="regular-text" placeholder="<?php _e( 'Are there any attributes that need to be parsed?', $this->text_domain ); ?>">
								</fieldset>
								<!-- Ignore tags -->
								<fieldset>
									<legend class="screen-reader-text"><span><?php _e( 'Would you like to ignore any tags?', $this->text_domain ); ?></span></legend>
									<label for="transifex_live_settings_ignore_tags" class="text_field_fix">Ignore tags</label>
									<input name="transifex_live_settings[ignore_tags]" type="text" id="transifex_live_settings_ignore_tags" value="<?php echo $settings[ 'ignore_tags' ]; ?>" class="regular-text" placeholder="<?php _e( 'Would you like to ignore any tags?', $this->text_domain ); ?>">
								</fieldset>
								<!-- Ignore class -->
								<fieldset>
									<legend class="screen-reader-text"><span><?php _e( 'Would you like to ignore any classes?', $this->text_domain ); ?></span></legend>
									<label for="transifex_live_settings_ignore_class" class="text_field_fix">Ignore class</label>
									<input name="transifex_live_settings[ignore_class]" type="text" id="transifex_live_settings_ignore_class" value="<?php echo $settings[ 'ignore_class' ]; ?>" class="regular-text" placeholder="<?php _e( 'Would you like to ignore any classes?', $this->text_domain ); ?>">
								</fieldset>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="transifex_live_settings_picker"><?php _e( 'Language Picker Location', $this->text_domain ); ?></label></th>
							<td class="forminp forminp-radio">
								<fieldset>
									<legend class="screen-reader-text"><span><?php _e( 'Auto position', $this->text_domain ); ?></span></legend>
									<ul>
										<?php foreach ( $this->positions as $row ) { ?>
											<li>
												<label>
													<input name="transifex_live_settings[picker]" value="<?php echo $row[ 'location' ]; ?>" type="radio" <?php checked( $row[ 'location' ], $settings[ 'picker' ] ); ?>><?php echo $row[ 'location' ]; ?>
												</label>
											</li>
										<?php } ?>
									</ul>
								</fieldset>
							</td>
						</tr>
						<tr>
						<tr>
							<th scope="row"><label for="transifex_live_settings[api_key]"><?php _e( 'Language Picker ID', $this->text_domain ); ?></label></th>
							<td>
								<input name="transifex_live_settings[custom_picker_id]" type="text" id="transifex_live_settings_custom_picker_id" value="<?php
								if ( $settings[ 'picker' ] == 'custom id' ) {
									echo $settings[ 'custom_picker_id' ];
								}
										?>" class="regular-text" placeholder="<?php _e( '', $this->text_domain ); ?>">
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<?php _e( 'Language Picker Styling', $this->text_domain ); ?>
							</th>
							<td class="forminp">
								<?php
								foreach ( $this->colors as $key => $values ) {
									if ( empty( $colors[ $key ] ) )
										$colors[ $key ] = $values[ 'default' ];
									$this->color_picker( $values[ 'label' ], 'transifex_live_colors[' . $key . ']', $colors[ $key ] );
								}
								?><br>
								<label for="transifex_live_settings_enable_frontend_css">
									<input name="transifex_live_settings[enable_frontend_css]" type="hidden" value="0">
									<input name="transifex_live_settings[enable_frontend_css]" id="transifex_live_settings_enable_frontend_css" type="checkbox" value="1" <?php checked( $settings[ 'enable_frontend_css' ] ); ?>>
									<?php _e( 'Enable', $this->text_domain ); ?>
								</label>
							</td>
						</tr>
					</tbody>
				</table>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', $this->text_domain ); ?>"></p>
			</form>
			<script>
				jQuery(document).ready(function ($) {
					$("#settings_form").validate;
				});
			</script>
			<p>
				<a href="<?php echo $this->urls[ 'rate_us' ]; ?>">
					<?php _e( 'Thank you for using Transifex!', $this->text_domain ); ?>
				</a>
			</p>
		</div>
		<?php
	}

}
