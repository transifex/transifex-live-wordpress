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
	
	public function ok_to_add() {
		$r = ($this->settings['hreflang'])?true:false;
		return $r;
	}
	
	/**
	 * Renders HREFLANG list
	 */
	public function render_hreflang() {
		Plugin_Debug::logTrace();

		$url = get_page_link();
		$source = $this->settings['source_language'];
		$hreflang = <<<SOURCE
		<link rel="alternate" href="$url" hreflang="$source"/>		
SOURCE;
		$a = $this->settings['transifex_languages'];

		$y = json_decode(html_entity_decode($this->settings['languages_map']), true);
		$pp = $this->_get_page_link();
		$xa = explode(",",$a);
		foreach($xa as $i) {
			$u = $y[$i];
			$s = str_replace('%lang%',$u,$pp);
			$hreflang .= <<<HREFLANG
				<link rel="alternate" href="$s" hreflang="$i"/>
HREFLANG;
		}
		echo $hreflang;
		return true;
	}
	
	/**
 * Retrieve the page permalink.
 *
 * Ignores page_on_front. Internal use only.
 *
 * @since 2.1.0
 * @access private
 *
 * @global WP_Rewrite $wp_rewrite
 *
 * @param int|object $post      Optional. Post ID or object.
 * @param bool       $leavename Optional. Leave name.
 * @param bool       $sample    Optional. Sample permalink.
 * @return string The page permalink.
 */
function _get_page_link( $post = false, $leavename = false, $sample = false ) {
	global $wp_rewrite;
	$post = get_post( $post );
	$draft_or_pending = in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) );
	$link = $wp_rewrite->get_page_permastruct();
	$link = '%lang%/'.$link;
	if ( !empty($link) && ( ( isset($post->post_status) && !$draft_or_pending ) || $sample ) ) {
		if ( ! $leavename ) {
			$link = str_replace('%pagename%', get_page_uri( $post ), $link);
		}
		$link = home_url($link);
		$link = user_trailingslashit($link, 'page');
	} else {
		$link = home_url( '?page_id=' . $post->ID );
	}
	return $link;
}
}

?>