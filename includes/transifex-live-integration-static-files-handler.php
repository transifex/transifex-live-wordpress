<?php
/**
 * Handler to enqueue static css or javascript files
 * @package TransifexLiveIntegration
 */

/**
 * Handles enqueue of static files
 */
class Transifex_Live_Integration_Static_Files_Handler {

	/**
	 * List of css files to enqueue
	 * @var array
	 */
	private $css_files;

	/**
	 * List of javascript files to enqueue
	 * @var array
	 */
	private $js_files;

	/**
	 * Public constructor, Initializes file lists
	 */
	public function __construct() {
		Plugin_Debug::logTrace();
		$this->css_files = array();
		$this->js_files = array();
	}

	/**
	 * Adds a single css file to plugin
	 * @param string $version Current plugin version.
	 * @param string $url Url to static file (can be external).
	 * @param string $handle Identifier if it might be used previously, otherwise generate a random one.
	 * @return array Associatative array in a format that can be enqueued.
	 */
	public function add_css_file( $version, $url, $handle = null ) {
		if ( null === $handle ) {
			$length = 4;
			$randomString = substr( str_shuffle( '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, $length );
			$handle = TRANSIFEX_LIVE_INTEGRATION_NAME . $randomString;
		}
		$arr = [ 'version' => $version, 'url' => $url, 'handle' => $handle ];
		return array_push( $this->css_files, $arr );
	}

	/**
	 * Adds a single js file to plugin
	 * @param string $version Current plugin version.
	 * @param string $url Url to static file (can be external).
	 * @param string $handle Identifier if it might be used previously, otherwise generate a random one.
	 * @return array Associatative array in a format that can be enqueued.
	 */
	public function add_js_file( $version, $url, $handle = null ) {
		if ( null === $handle ) {
			$length = 4;
			$randomString = substr( str_shuffle( '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, $length );
			$handle = TRANSIFEX_LIVE_INTEGRATION_NAME . $randomString;
		}
		$arr = [ 'version' => $version, 'url' => $url, 'handle' => $handle ];
		return array_push( $this->js_files, $arr );
	}

	/**
	 * Renders css through enqueue
	 */
	public function render_css() {
		Plugin_Debug::logTrace();
		foreach ( $this->css_files as $file ) {
			wp_enqueue_style( $file['handle'], $file['url'], false, $file['version'], 'all' );
		}
	}

	/**
	 * Renders js through enqueue
	 */
	public function render_js() {
		Plugin_Debug::logTrace();
		foreach ( $this->js_files as $file ) {
			wp_enqueue_script( $file['handle'], $file['url'], false, $file['version'], 'all' );
		}
	}

	/**
	 * Tries to use existing Wordpress color picker js, otherwise load from admin
	 * @return none
	 */
	public function render_iris_color_picker() {
		Plugin_Debug::logTrace();
		if ( isset( $wp_scripts->registered['iris'] ) ) {
			return;
		}

		// Thanks to http://wordpress.stackexchange.com/questions/82718/how-do-i-implement-the-wordpress-iris-picker-into-my-plugin-on-the-front-end.
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script(
			'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), array( 'iris' ), false, 1
		);
	}

}
