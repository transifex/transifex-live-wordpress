<?php
/**
 * Includes hreflang tag attribute on each page containing url rewrites
 * @package TransifexLiveIntegration
 */

/**
 * Class that renders hreflang
 */
class Transifex_Live_Integration_Hreflang {
	/**
	 * Copy of current plugin settings
	 * @var settings array
	 */
	private $settings;
	
	/**
	 * Public constructor, sets the settings
	 * @param array $settings Associative array used to store plugin settings.
	 */
	public function __construct( $settings ) {
		Plugin_Debug::logTrace();
		$this->settings = $settings;		
	}
	
	/**
	 * Renders HREFLANG list
	 */
	public function render_hreflang($url_pattern) {
		$a = $this->setting[language_lookup];
		$hreflang = "";
		foreach($a as $i) {
			$s = replace($url_pattern,$i);
			$hreflang .= <<<HREFLANG
				<link rel="alternate" href="$s" hreflang="$i"/>
HREFLANG;
		}
		return $hreflang;
	}
}

?>