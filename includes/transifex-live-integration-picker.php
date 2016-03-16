<?php

class Transifex_Live_Integration_Picker {

	private $language_map;
	private $tokenized_url;
	private $enable_picker;
	private $source_language;
	
	public function __construct( $language_map, $tokenized_url, $enable_picker, $source_language ) {
		Plugin_Debug::logTrace();
		$this->language_map = json_decode( $language_map, true )[0];
		$this->tokenized_url = $tokenized_url;
		$this->enable_picker = $enable_picker;
		$this->source_language = $source_language;
	}
	
	static function create_picker ( $settings ) {
		Plugin_Debug::logTrace();
		if ( !isset( $settings['enable_picker'] ) ) {
			Plugin_Debug::logTrace( 'settings[enable_picker] not set' );
			return false;
		}
		if (  !($settings['enable_picker']) ) {
			Plugin_Debug::logTrace( 'settings[enable_picker] not truthy' );
			return false;
		}
		if ( !isset( $settings['tokenized_url'] ) || !( $settings['tokenized_url'] ) ) {
			Plugin_Debug::logTrace( 'settings[tokenized_url] not set and not truthy' );
			return false;
		}
		return new Transifex_Live_Integration_Picker( $settings['language_map'], $settings['tokenized_url'], $settings['enable_picker'], $settings['source_language'] );
	}
	
	static function generate_language_url_map( $raw_url, $tokenized_url, $language_map ) {
		Plugin_Debug::logTrace();
		$trimmed_tokenized_url = rtrim($tokenized_url,"/");
		$trimmed_url = ltrim($raw_url,"/");
		$ret = [ ];
			foreach ($language_map as $k => $v) {
				
				$ret[$k] = str_replace('%lang%',$v,$trimmed_tokenized_url) . "/". $trimmed_url;
			}
		
		return $ret;
	}

	function render() {
		Plugin_Debug::logTrace();
		global $wp;
		$lang = get_query_var( 'lang' );
		$home_url = home_url( $wp->request );
		Plugin_Debug::logTrace(home_url( $wp->request ));
		$url_path = add_query_arg(array(), $wp->request);
		$source_url_path = (substr( $url_path, 0, strlen($lang) ) === $lang)?substr( $url_path, strlen($lang), strlen($url_path) ):$url_path;
		Plugin_Debug::logTrace(add_query_arg(array(), $wp->request));
		Plugin_Debug::logTrace($source_url_path);
		$url_map = $this->generate_language_url_map($source_url_path, $this->tokenized_url, $this->language_map);
		$url_map[$this->source_language] = site_url() . $source_url_path;
		$string_url_map = json_encode($url_map, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ;
		
		$include = <<<JSONP
<script type="text/javascript">
	Transifex.live.onBeforeTranslatePage(function(params) {
		var locale_urls = $string_url_map;
		if(Transifex.live.ready === true && Transifex.live.getSelectedLanguageCode() !== params.lang_code){
			params.noop = true;
			window.location.href = locale_urls[params.lang_code];
		}
	});
</script>
JSONP;
		echo $include;
	}

}
