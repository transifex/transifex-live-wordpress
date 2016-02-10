<?php

class Transifex_Live_Integration_Settings_Util {
	
	static function render_source_language( $source_language ) {
		$html = '';
		if (empty($source_langauge)) {
			$html = 'Could not fetch published languages. Check your Transifex Live settings.  <a href="">Learn more</a>';
		} else {
		$html .= <<<HTML_TEMPLATE
		<input type="hidden" value="<?php echo $source_language ?>" name="transifex_live_settings[source_language]" id="transifex_live_settings_source_language" />
HTML_TEMPLATE;
		}
		}

	static function render_url_options( $options ) {
		$html = '';

		$i = 0;
		foreach ($options as $option) {
			ob_start();
			checked( $option['checked'], '1' );
			$checked = ob_get_clean();

			$text = $option['text'];
			$id = $option['id'];
			$name = $option['name'];
			$html .= <<<HTML
		<input class="all_selector" type="checkbox" id="$id" name="$name" value="1" $checked>$text
HTML;
			if ( $i % 3 == 0 ) {
				$html .= <<<NEWLINE
				<br/>
NEWLINE;
			}
			$i++;
		}
		echo $html;
	}
}
