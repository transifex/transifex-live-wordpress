<?php

/**
 * Includes Transifex Live javascript snippet
 * @package TransifexLiveIntegration
 */

/**
 * Class that creates javascript snippet based on settings
 */
class Transifex_Live_Integration_Javascript {

	/**
	 * Stores current plugin settings.
	 * @var array
	 */
	private $live_settings = array();
	private $is_detectlang;

	/**
	 * Public constructor, sets local settings
	 * @param array $live_settings Associative array of plugin settings.
	 */
	public function __construct( $live_settings, $is_detectlang ) {
		Plugin_Debug::logTrace();
		$this->live_settings = $live_settings;
		$this->is_detectlang = $is_detectlang;
	}

	/**
	 * Renders javascript includes in the page
	 */
	function render() {
		Plugin_Debug::logTrace();
		
		if ( $this->is_detectlang ) {
			$lang = get_query_var( 'lang' );
			$detectlang = <<<DETECTLANG
function() { return "$lang";}
DETECTLANG;
			$this->live_settings = array_merge( $this->live_settings, array( 'detectlang' => '%function%' ) );
			
		}
		$live_settings_string = json_encode( $this->live_settings );
		$live_settings_string = str_replace('"%function%"', $detectlang, $live_settings_string);
		$include = <<<LIVE
<script type="text/javascript">window.liveSettings={"api_key":"d2581d15aa3844d9858dc1e8913b63bd","staging":0,"enable_frontend_css":0,"custom_picker_id":"","detectlang":function() { return "fr";}};</script>
<script type="text/javascript" src="//cdn.transifex.com/live.js"></script>
LIVE;
		echo $include;
	}

}
