<?php

include_once TRANSIFEX_LIVE_INTEGRATION_DIRECTORY_BASE . '/includes/transifex-live-integration-lib.php';

class Transifex_Live_Integration_Css {

	private $settings;
	private $ok_add_css;

	public function __construct( $settings ) {
		Plugin_Debug::logTrace();
		if ( isset( $settings['enable_frontend_css'] ) && $settings['enable_frontend_css'] ) {
			$this->ok_add_css = true;
		} else {
			$this->skip_css = false;
		}
		$this->settings = $settings;
	}

	function inline_render() {
		Plugin_Debug::logTrace();

		if ( $this->ok_add_css ) {

			$colors = array_map( 'esc_attr', (array) get_option( 'transifex_live_colors', array() ) );
			foreach ($colors as $key => $values) {
				if ( empty( $colors[$key] ) ) {
					$colors[$key] = $values['default'];
				}
			}

			$background_color = implode( ', ', Transifex_Live_Integration_Lib::hex2rgb( $colors['background'] ) );
			$text_color = $colors['text'];
			$accent_color = $colors['accent'];
			$menu_color = $colors['menu'];
			$languages_color = $colors['languages'];

			$css = <<<CSS
        <!-- Transifex Live Custom CSS -->
        <style type="text/css">
            .txlive-langselector {
                background: rgba( $background_color, 0.75 ) !important;
                color: $text_color !important;
            }
            .txlive-langselector .txlive-langselector-toggle {
                border-color: $accent_color !important;
            }
            .txlive-langselector-bottomright .txlive-langselector-marker, .txlive-langselector-bottomleft .txlive-langselector-marker {
                border-bottom-color: $text_color !important;
            }
            .txlive-langselector-topright .txlive-langselector-marker, .txlive-langselector-topleft .txlive-langselector-marker {
                border-top-color: $text_color !important;
            }
            .txlive-langselector-list {
                background-color: $menu_color !important;
                border-color: rgba( 255, 255, 255, 0.5 ) !important;
                color: $languages_color !important;
            }
            .txlive-langselector-list > li:hover {
                background-color: rgba( 0, 0, 0, 0.2 ) !important;
            }
        </style>
CSS;
			Transifex_Live_Integration_Lib::enqueue_inline_styles( 'transifex-live-integration-css', $css );
		}
	}

}
