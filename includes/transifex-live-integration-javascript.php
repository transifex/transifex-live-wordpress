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

	/**
	 * Public constructor, sets local settings
	 * @param array $live_settings Associative array of plugin settings.
	 */
	public function __construct( $live_settings ) {
		Plugin_Debug::logTrace();
		$this->live_settings = $live_settings;
	}

	/**
	 * Renders javascript includes in the page
	 */
	function render() {
		Plugin_Debug::logTrace();
		$live_settings_string = json_encode( $this->live_settings );
		Plugin_Debug::logTrace( $live_settings_string );
		$include = <<<LIVE
<script type="text/javascript">window.liveSettings=$live_settings_string;</script>
<script type="text/javascript" src="//cdn.transifex.com/live.js"></script>
LIVE;
		echo $include;
	}

}
