<?php
/**
 * @package Transifex_Live
 * @version 0.9
 */
/*
Plugin Name: Transifex Live
Plugin URI: http://wordpress.org/plugins/transifex-live/
Description: Easily integrate Transifex Live (Beta) into your WordPress site.
Author: ThemeBoy
Version: 0.9
Author URI: http://themeboy.com/
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plugin setup
 *
 * @since 0.9
*/
class Transifex_Live {

	var $defaults = array();
	var $positions = array();
	var $colors = array();

	/**
	 * Transifex Live Constructor.
	 * @access public
	 */
	public function __construct() {
		// Define constants and settings
		$this->define_constants();
		$this->define_settings();

		// Hooks
		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		add_action( 'admin_menu', array( $this, 'options_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'wp_print_scripts', array( $this, 'custom_css' ) );
	}

	/**
	 * Define constants
	 */
	private function define_constants() {
		if ( !defined( 'TRANSIFEX_LIVE_VERSION' ) )
			define( 'TRANSIFEX_LIVE_VERSION', '0.9' );

		if ( !defined( 'TRANSIFEX_LIVE_URL' ) )
			define( 'TRANSIFEX_LIVE_URL', plugin_dir_url( __FILE__ ) );

		if ( !defined( 'TRANSIFEX_LIVE_DIR' ) )
			define( 'TRANSIFEX_LIVE_DIR', plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Define settings
	 */
	private function define_settings() {
		$this->defaults = array(
			'api_key' => null,
			'picker' => 'bottom-right',
			'detectlang' => 1,
			'autocollect' => 1,
			'enable_frontend_css' => 0,
		);

		$this->positions = array(
			'top-left' => __( 'top left', 'transifex-live' ),
			'top-right' => __( 'top right', 'transifex-live' ),
			'bottom-left' => __( 'bottom left', 'transifex-live' ),
			'bottom-right' => __( 'bottom right', 'transifex-live' ),
		);

		$this->colors = array(
			'accent' => array(
				'default' => '#006f9f',
				'label' => __( 'Accent', 'transifex-live' ),
			),
			'text' => array(
				'default' => '#ffffff',
				'label' => __( 'Text', 'transifex-live' ),
			),
			'background' => array(
				'default' => '#000000',
				'label' => __( 'Background', 'transifex-live' ),
			),
			'menu' => array(
				'default' => '#eaf1f7',
				'label' => __( 'Menu', 'transifex-live' ),
			),
			'languages' => array(
				'default' => '#666666',
				'label' => __( 'Languages', 'transifex-live' ),
			),
		);
	}

	/**
	 * Init plugin when WordPress Initialises.
	 */
	public function init() {
		// Set up localisation
		$this->load_plugin_textdomain();
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 */
	public static function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'transifex-live' );

		// Global + Frontend Locale
		load_plugin_textdomain( 'transifex-live', false, plugin_basename( dirname( __FILE__ ) . "/languages" ) );
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @param mixed $links
	 * @return array
	 */
	public function action_links( $links ) {
		return array_merge( array(
			'<a href="' . add_query_arg( array( 'page' => 'transifex-live' ), admin_url( 'options-general.php' ) ) . '">' . __( 'Settings', 'transifex-live' ) . '</a>',
		), $links );
	}

	/**
	 * Add link to settings menu.
	 */
	public function options_menu() {
		add_options_page( __( 'Transifex Live', 'transifex-live' ), __( 'Transifex Live', 'transifex-live' ), 'manage_options', 'transifex-live', array( $this, 'options_page' ) );
	}

	/**
	 * Settings page callback.
	 */
	public function options_page() {
		if ( isset( $_POST['transifex_live_nonce'] ) && wp_verify_nonce( $_POST['transifex_live_nonce'], 'transifex_live_settings' ) ) {
			if ( isset( $_POST['transifex_live_settings'] ) ) update_option( 'transifex_live_settings', $_POST['transifex_live_settings'] );
			if ( isset( $_POST['transifex_live_colors'] ) ) update_option( 'transifex_live_colors', $_POST['transifex_live_colors'] );
		}
		$settings = get_option( 'transifex_live_settings', array() );
		$settings = array_merge( $this->defaults, $settings );
		$colors = array_map( 'esc_attr', (array) get_option( 'transifex_live_colors', array() ) );
		?>
		<div class="wrap transifex-live-settings">
			<h2><?php _e( 'Transifex Live', 'transifex-live' ); ?></h2>
			<form method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'transifex_live_settings', 'transifex_live_nonce' ); ?>
				<p><?php _e( 'Integrate Transifex Live in your webpages and publish translations', 'transifex-live' ); ?></p>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="transifex_live_settings[api_key]"><?php _e( 'API key', 'transifex-live' ); ?></label></th>
							<td>
								<input name="transifex_live_settings[api_key]" type="text" id="transifex_live_settings_api_key" value="<?php echo $settings['api_key']; ?>" class="regular-text" placeholder="<?php _e( 'This field is required.', 'transifex-live' ); ?>">
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="transifex_live_settings_picker"><?php _e( 'Auto position', 'transifex-live' ); ?></label></th>
							<td class="forminp forminp-radio">
								<fieldset>
									<legend class="screen-reader-text"><span><?php _e( 'Auto position', 'transifex-live' ); ?></span></legend>
									<ul>
										<?php foreach ( $this->positions as $key => $label ) { ?>
											<li>
												<label>
													<input name="transifex_live_settings[picker]" value="<?php echo $key; ?>" type="radio" <?php checked( $key, $settings['picker'] ); ?>> <?php echo $label; ?></label>
											</li>
										<?php } ?>
									</ul>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th scope="row"><label><?php _e( 'Options', 'transifex-live' ); ?></label></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php _e( 'Auto-detect the browser locale and translate the page.', 'transifex-live' ); ?></span></legend>
									<label for="transifex_live_settings_detectlang">
										<input name="transifex_live_settings[detectlang]" type="hidden" value="0">
										<input name="transifex_live_settings[detectlang]" type="checkbox" id="transifex_live_settings_detectlang" value="1" <?php checked( $settings['detectlang'] ); ?>>
										<?php _e( 'Auto-detect the browser locale and translate the page.', 'transifex-live' ); ?>
									</label>
								</fieldset>
								<fieldset>
									<legend class="screen-reader-text"><span><?php _e( 'Automatically identify new strings when page content changes.', 'transifex-live' ); ?></span></legend>
									<label for="transifex_live_settings_autocollect">
										<input name="transifex_live_settings[autocollect]" type="hidden" value="0">
										<input name="transifex_live_settings[autocollect]" type="checkbox" id="transifex_live_settings_autocollect" value="1" <?php checked( $settings['autocollect'] ); ?>>
										<?php _e( 'Automatically identify new strings when page content changes.', 'transifex-live' ); ?>
									</label>
								</fieldset>
								<p class="description">
									<?php echo str_replace( '%(url)s', 'http://docs.transifex.com/developer/live/api', __( "For advanced integration instructions read the <a href=\"%(url)s\" target=\"_blank\" title=\"API documentation\">API documentation</a>.", 'transifex-live' ) ); ?>
								</p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<?php _e( 'Frontend Styles', 'transifex-live' ); ?>
							</th>
							<td class="forminp">
								<?php
								foreach ( $this->colors as $key => $values ) {
									if ( empty( $colors[$key] ) ) $colors[$key] = $values['default'];
									$this->color_picker( $values['label'], 'transifex_live_colors[' . $key . ']', $colors[$key] );
								}
								?><br>
								<label for="transifex_live_settings_enable_frontend_css">
									<input name="transifex_live_settings[enable_frontend_css]" type="hidden" value="0">
									<input name="transifex_live_settings[enable_frontend_css]" id="transifex_live_settings_enable_frontend_css" type="checkbox" value="1" <?php checked( $settings['enable_frontend_css'] ); ?>>
									<?php _e( 'Enable', 'transifex-live' ); ?>
								</label>
							</td>
						</tr>
					</tbody>
				</table>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
			</form>
		</div>
		<?php
	}

	/**
	 * Enqueue admin styles
	 */
	public static function admin_scripts() {
		$screen = get_current_screen();

		if ( 'settings_page_transifex-live' == $screen->id ):
	    	wp_enqueue_script( 'transifex-live-admin', TRANSIFEX_LIVE_URL . '/js/admin.js', array( 'jquery', 'wp-color-picker', 'iris' ), TRANFIEX_LIVE_VERSION, true );
			wp_enqueue_style( 'transifex-live-admin', TRANSIFEX_LIVE_URL . '/css/admin.css', array(), TRANFIEX_LIVE_VERSION );
		endif;
	}

	/**
	 * Enqueue scripts
	 */
	public function scripts() {
		$settings = get_option( 'transifex_live_settings', array() );
		$settings = array_filter( $settings, 'strlen' );
		if ( count( array_diff( array_keys( $this->defaults ), array_keys( $settings ) ) ) > 0 ) return;

		wp_enqueue_script( 'transifex-live', '//cdn.transifex.com/live.js', array(), null, false );
		wp_enqueue_script( 'transifex-live-scripts', TRANSIFEX_LIVE_URL . '/js/scripts.js', array( 'transifex-live' ), TRANSIFEX_LIVE_VERSION, false );

		$settings = array_merge( $this->defaults, $settings );
		
		wp_localize_script( 'transifex-live-scripts', 'settings', $settings );
	}

	/**
	 * Custom CSS
	 */
	public function custom_css() {
		$settings = get_option( 'transifex_live_settings', array() );
		if ( ! isset( $settings['enable_frontend_css'] ) || ! $settings['enable_frontend_css'] ) return;
		
		$colors = array_map( 'esc_attr', (array) get_option( 'transifex_live_colors', array() ) );

		foreach ( $this->colors as $key => $values ) {
			if ( empty( $colors[$key] ) ) $colors[$key] = $values['default'];
		}
		?>
		<!-- Transifex Live Custom CSS -->
		<style type="text/css">
			.txlive-langselector {
				background: rgba( <?php echo implode( ', ', $this->hex2rgb( $colors['background'] ) ); ?>, 0.75 ) !important;
				color: <?php echo $colors['text']; ?> !important;
			}
			.txlive-langselector .txlive-langselector-toggle {
				border-color: <?php echo $colors['accent']; ?> !important;
			}
			.txlive-langselector-bottomright .txlive-langselector-marker, .txlive-langselector-bottomleft .txlive-langselector-marker {
				border-bottom-color: <?php echo $colors['text']; ?> !important;
			}
			.txlive-langselector-topright .txlive-langselector-marker, .txlive-langselector-topleft .txlive-langselector-marker {
				border-top-color: <?php echo $colors['text']; ?> !important;
			}
			.txlive-langselector-list {
				background-color: <?php echo $colors['menu']; ?> !important;
				border-color: rgba( 255, 255, 255, 0.5 ) !important;
				color: <?php echo $colors['languages']; ?> !important;
			}
			.txlive-langselector-list > li:hover {
				background-color: rgba( 0, 0, 0, 0.2 ) !important;
			}
		</style>
		<?php
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
	function color_picker( $name, $id, $value ) {
		echo '<div class="color-box"><strong>' . esc_html( $name ) . '</strong>
	   		<input name="' . esc_attr( $id ). '" id="' . esc_attr( str_replace( array( '[', ']' ), array( '_', '' ), $id ) ) . '" type="text" value="' . esc_attr( $value ) . '" class="colorpick" /> <div id="colorPickerDiv_' . esc_attr( $id ) . '" class="colorpickdiv"></div>
	    </div>';
	}

	/**
	 * Hex to RGB
	 *
	 * @access public
	 * @param string $hex
	 * @return array $rgb
	 * Credit: c.bavota (http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/)
	 */
	function hex2rgb( $hex ) {
		$hex = str_replace( '#', '', $hex );
		if ( strlen( $hex ) == 3 ) {
			$r = hexdec( substr( $hex, 0, 1 ).substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ).substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ).substr( $hex, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}
		$rgb = array( $r, $g, $b );
		return $rgb; // returns an array with the rgb values
	}
}

new Transifex_Live();
