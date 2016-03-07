<?php

class Transifex_Live_Integration_Language_Map {

	private $language_map;
	
	public function __construct( $language_map ) {
		Plugin_Debug::logTrace();
		$this->language_map = $language_map;
	}
	
	static function create_language_maps ( $settings ) {
		Plugin_Debug::logTrace();
		if ( !isset( $settings['enabled_language_picker'] ) ) {
			Plugin_Debug::logTrace( 'settings[enabled_language_picker] not set' );
			return false;
		}
		if (  !($settings['enabled_language_picker'] )) {
			Plugin_Debug::logTrace( 'settings[enabled_language_picker] not truthy' );
			return false;
		}
		return new Transifex_Live_Integration_Language_Map( $settings['language_map'] );
	}
	
		static function generate_language_url_map( $raw_url, $languages, $lang,
			$language_map ) {
		Plugin_Debug::logTrace();
		$ret = [ ];
		$tokenized_url = str_replace( $lang, "%lang%", $raw_url, $count );
		if ( $count !== 0 ) {
			foreach ($languages as $language) {
				$arr = [ ];
				$hreflang_code = $language_map[$language];
				$language_url = str_replace( '%lang%', $hreflang_code, $tokenized_url );
				$arr['href'] = $language_url;
				$arr['hreflang'] = $hreflang_code;
				array_push( $ret, $arr );
			}
		}
		return $ret;
	}

	function render() {
		Plugin_Debug::logTrace();
		$language_map = $this->language_map;
		$include = <<<JSONP
<script type="text/javascript">function transifex_language_map() { return $language_map;};</script>
JSONP;
		echo $include;
	}

}
