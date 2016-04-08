<?php

include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/common/transifex-live-integration-common.php';

/**
 * Includes language picker javascript snippet
 * @package TransifexLiveIntegration
 */

/**
 * Class that creates javascript snippet for a custom picker
 */
class Transifex_Live_Integration_Picker {

	/**
	 * A key/value array that maps Transifex locale->plugin code
	 * @var array
	 */
	private $language_map;

	/**
	 * Site_url with language pattern for substitution
	 * @var string
	 */
	private $tokenized_url;

	/**
	 * Should picker be enabled?
	 * @var bool
	 */
	private $enable_picker;

	/**
	 * Current source language
	 * @var string
	 */
	private $source_language;

	/**
	 * Constructor
	 * 
	 * @param array $language_map A key/value array that maps Transifex locale->plugin code
	 * @param string $tokenized_url Site_url with language pattern for substitution
	 * @param bool $enable_picker Should picker be enabled?
	 * @param string $source_language Current source language
	 */
	public function __construct( $language_map, $tokenized_url, $enable_picker,
			$source_language
	) {
		Plugin_Debug::logTrace();
		$this->language_map = json_decode( $language_map, true )[0];
		$this->tokenized_url = $tokenized_url;
		$this->enable_picker = $enable_picker;
		$this->source_language = $source_language;
	}

	/*
	 * Render picker Javascript in the template
	 */

	function render() {
		Plugin_Debug::logTrace();
		global $wp;
		$lang = get_query_var( 'lang' );
		$home_url = home_url( $wp->request );
		$url_path = add_query_arg( array(), $wp->request );
		$source_url_path = (substr( $url_path, 0, strlen( $lang ) ) === $lang) ? substr( $url_path, strlen( $lang ), strlen( $url_path ) ) : $url_path;
		$url_map = Transifex_Live_Integration_Common::generate_language_url_map( $source_url_path, $this->tokenized_url, $this->language_map );
		$unslashed_source_url = site_url() . $source_url_path;
		$url_map[$this->source_language] = rtrim( $unslashed_source_url, '/' ) . '/';
		$string_url_map = json_encode( $url_map, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

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
