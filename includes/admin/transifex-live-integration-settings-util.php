<?php

class Transifex_Live_Integration_Settings_Util {

	static function wp_before_admin_bar_render_hook() {
		Plugin_Debug::logTrace();
		echo ('<div class="notranslate">');
	}

	static function wp_after_admin_bar_render_hook() {
		Plugin_Debug::logTrace();
		echo ('</div>');
	}

	static function generate_tokenized_url( $site_url, $url_option_setting ) {
		Plugin_Debug::logTrace();
		$tokenized_url = false;

		if ( $url_option_setting !== '2' && $url_option_setting != '3' ) {
			Plugin_Debug::logTrace( 'No URL option, skipping tokenization' );
			return false;
		}

		if ( !($site_url) ) {
			Plugin_Debug::logTrace( 'Failed site URL truthiness, skipping tokenization' );
			return false;
		}

		$slashes = [ ];
		$slashes = explode( "/", $site_url );
		if ( $url_option_setting === '3' ) { // Subdirectory option
			array_push( $slashes, '%lang%' );
			array_push( $slashes, '' );
		}
		if ( $url_option_setting === '2' ) { // Subdomain option
			$dots = explode( ".", $slashes[2] );
			$dots[0] = '%lang%';
			$slashes[2] = implode( '.', $dots );
			array_push( $slashes, '' );
		}
		$tokenized_url = implode( '/', $slashes );

		return $tokenized_url;
	}

	static function render_url_options( $options ) {
		$html = '';
		$row = '';
		$i = 1;
		foreach ($options as $option) {
			ob_start();
			checked( $option['checked'], 1 );
			$checked = ob_get_clean();
			$text = $option['text'];
			$id = $option['id'];
			$name = $option['name'];
			$row .= <<<ROW
		<td class="option-checkbox" style="padding:0px"><input class="all_selector" type="checkbox" id="$id" name="$name" value="1" $checked />$text</td>
ROW;
			if ( $i % 3 == 0 ) {
				$html .= '<tr>' . $row . '</tr>';
				$row = '';
			}
			$i++;
		}
		echo $html;
	}

	static function render_transifex_settings( $settings ) {
		$html = '';
		foreach ($settings as $setting) {
			$text = $setting['value'];
			$id = $setting['id'];
			$name = $setting['name'];
			$html .= <<<HTML
<input type="hidden" value="$value" name="$name" id="$id" />
HTML;
		}
		echo $html;
	}

}
