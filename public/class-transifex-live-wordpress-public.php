<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://docs.transifex.com/developer/integrations/wordpress
 * @since      1.0.0
 *
 * @package    Transifex_Live_Wordpress
 * @subpackage Transifex_Live_Wordpress/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Transifex_Live_Wordpress
 * @subpackage Transifex_Live_Wordpress/public
 */
class Transifex_Live_Wordpress_Public {

    private $plugin_name;
    private $version;
    private $settings;

    /**
     * Initialize the class and set its properties.
     *
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    		The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->set_settings();
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     */
    public function enqueue_styles() {

        $settings = get_option('transifex_live_settings', array());
        if (!isset($settings['enable_frontend_css']) || !$settings['enable_frontend_css']) {
            return;
        }

        $colors = array_map('esc_attr', (array) get_option('transifex_live_colors', array()));
        foreach ($colors as $key => $values) {
            if (empty($colors[$key])) {
                $colors[$key] = $values['default'];
            }
        }
        // TODO move this to a external css file which can be enqueued - @Mjjacko 4/24/2015
        ?>
        <!-- Transifex Live Custom CSS -->
        <style type="text/css">
            .txlive-langselector {
                background: rgba( <?php echo implode(', ', Transifex_Live_Wordpress_Util::hex2rgb($colors['background'])); ?>, 0.75 ) !important;
                color: <?php echo $colors['text']; ?> !important;
            }
            .txlive-langselector .txlive-langselector-toggle {
                border-color: <?php echo $colors['accent']; ?> !important;
            }
            .txlive-langselector-bottomright .txlive-langselector-marker, .txlive-langselector-bottomleft .txlive-langselector-marker {
                border-bottom-color: <?php echo $colors['text']; ?> !important;
            }
            .txlive-langselector-topright .txlive-langselector-marker, .txlive-langselector-topleft .txlive-langselector-marker {
                border-top-color: <?php echo $colors['text']; ?> !important;
            }
            .txlive-langselector-list {
                background-color: <?php echo $colors['menu']; ?> !important;
                border-color: rgba( 255, 255, 255, 0.5 ) !important;
                color: <?php echo $colors['languages']; ?> !important;
            }
            .txlive-langselector-list > li:hover {
                background-color: rgba( 0, 0, 0, 0.2 ) !important;
            }
        </style>
        <?php
    }

    /**
     * Register javascript for the public-facing side of the site.
     */
    public function enqueue_scripts() {


        wp_enqueue_script($this->plugin_name, '//cdn.transifex.com/live.js', array(), null, false);
        wp_enqueue_script($this->plugin_name . '-scripts', TRANSIFEX_LIVE_PUBLIC_JS . 'transifex-live-wordpress-public.js', array($this->plugin_name), $this->plugin_version, false);
        wp_localize_script($this->plugin_name . '-scripts', 'settings', $this->settings);
    }

    function get_settings() {
        return $this->settings;
    }

    function set_settings() {
        $from_option_settings = get_option('transifex_live_settings', array());
        $my_settings = array_filter($from_option_settings, 'strlen');

        if (array_key_exists('picker', $my_settings)) {
            if ($my_settings['picker'] == 'custom id') {
                $my_settings['picker'] = "#" . $my_settings['custom_picker_id'];
            }
        }

        unset($my_settings['custom_picker_id']);
        if (array_key_exists('parse_attr', $my_settings)) {
            $my_settings['parse_attr'] = (explode(', ', $my_settings['parse_attr']));
        }
        if (array_key_exists('ignore_tags', $my_settings)) {
            $my_settings['ignore_tags'] = (explode(', ', $my_settings['ignore_tags']));
        }
        if (array_key_exists('ignore_class', $my_settings)) {
            $my_settings['ignore_class'] = (explode(', ', $my_settings['ignore_class']));
        }
        $this->settings = $my_settings;
    }

}
