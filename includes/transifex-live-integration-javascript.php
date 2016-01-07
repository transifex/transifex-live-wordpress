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
		unset($live_settings['staging']); // control for staging deprecated https://github.com/transifex/transifex-live-wordpress/issues/46
		$this->live_settings = $live_settings;
		$this->is_detectlang = $is_detectlang;
	}

	/**
	 * Renders javascript includes in the page
	 */
	function render() {
		Plugin_Debug::logTrace();
/* TODO URL detection logic removed		
		if ( $this->is_detectlang ) {
			$lang = get_query_var( 'lang' );
			$detectlang = <<<DETECTLANG
function() { return "$lang";}
DETECTLANG;
			$this->live_settings = array_merge( $this->live_settings, array( 'detectlang' => '%function%' ) );
			
		}
		$live_settings_string = json_encode( $this->live_settings );
		$live_settings_string = str_replace('"%function%"', $detectlang, $live_settings_string);
 * 
 */
		$live_settings_string = json_encode( $this->live_settings );
		$include = <<<LIVE
<script type="text/javascript">window.liveSettings=$live_settings_string;</script>
<script type="text/javascript" src="//cdn.transifex.com/live.js"></script>
LIVE;
		echo $include;
	}

}
