<?php

class Transifex_Live_Integration_Static_Files_Handler {

    private $css_files;
    private $js_files;

    public function __construct() {
        Plugin_Debug::logTrace();
        $this->css_files = array();
        $this->js_files = array();
    }

    public function add_css_file($version, $url, $handle = NULL) {
        if ($handle == NULL) {
            $length = 4;
            $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
            $handle = TRANSIFEX_LIVE_INTEGRATION_NAME . $randomString;
        }
        $arr = ['version' => $version, 'url' => $url, 'handle' => $handle];
        return array_push($this->css_files, $arr);
    }

    public function add_js_file($version, $url, $handle = NULL) {
        if ($handle == NULL) {
            $length = 4;
            $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
            $handle = TRANSIFEX_LIVE_INTEGRATION_NAME . $randomString;
        }
        $arr = ['version' => $version, 'url' => $url, 'handle' => $handle];
        return array_push($this->js_files, $arr);
    }

    public function render_css() {
        Plugin_Debug::logTrace();
        foreach ($this->css_files as $file) {
            wp_enqueue_style($file['handle'], $file['url'], false, $file['version'], 'all');
        }
    }

    public function render_js() {
        Plugin_Debug::logTrace();
        foreach ($this->js_files as $file) {
            wp_enqueue_script($file['handle'], $file['url'], false, $file['version'], 'all');
        }
    }

    public function render_iris_color_picker() {
        Plugin_Debug::logTrace();
        if (isset($wp_scripts->registered['iris'])) {
            return;
        }

            // thanks to http://wordpress.stackexchange.com/questions/82718/how-do-i-implement-the-wordpress-iris-picker-into-my-plugin-on-the-front-end
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script(
                    'iris', admin_url('js/iris.min.js'), array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), false, 'wp-color-picker', admin_url('js/color-picker.min.js'), array('iris'), false, 1
            );
        
    }

}
