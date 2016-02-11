<?php

class Transifex_Live_Integration_Settings_Util {
	
	static function render_url_options( $options ) {
		$html = '<table>';
		$row = '';
		$i = 1;
		foreach ($options as $option) {
			ob_start();
			checked( $option['checked'], '1' );
			$checked = ob_get_clean();

			$text = $option['text'];
			$id = $option['id'];
			$name = $option['name'];
			$row .= <<<ROW
		<td style="padding:0px"><input class="all_selector" type="checkbox" id="$id" name="$name" value="1" $checked>$text</td>
ROW;
			if ( $i % 3 == 0 ) {
				$html .= '<tr>'.$row.'</tr>';
				$row = '';
			}
			$i++;
		}
		echo $html.'</table>';
	}
}
