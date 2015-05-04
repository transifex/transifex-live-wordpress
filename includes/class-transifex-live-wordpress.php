<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://docs.transifex.com/developer/integrations/wordpress
 *
 * @package    Transifex_Live_Wordpress
 * @subpackage Transifex_Live_Wordpress/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @package    Transifex_Live_Wordpress
 * @subpackage Transifex_Live_Wordpress/includes
 */
class Transifex_Live_Wordpress {

    protected $loader;
    protected $plugin_name;
    protected $version;

    /**
     * Define the core functionality of the plugin.
     */
    public function __construct() {

        $this->plugin_name = 'transifex-live-wordpress';
        $this->version = '1.0.0';
        $this->plugin_text_domain = 'transifex-live';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

        // Hooks
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'action_links'));
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Transifex_Live_Wordpress_Loader. Orchestrates the hooks of the plugin.
     * - Transifex_Live_Wordpress_i18n. Defines internationalization functionality.
     * - Transifex_Live_Wordpress_Admin. Defines all hooks for the admin area.
     * - Transifex_Live_Wordpress_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @access   private
     */
    private function load_dependencies() {

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-transifex-live-wordpress-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-transifex-live-wordpress-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-transifex-live-wordpress-util.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-transifex-live-wordpress-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-transifex-live-wordpress-public.php';
        
        $this->loader = new Transifex_Live_Wordpress_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Transifex_Live_Wordpress_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Transifex_Live_Wordpress_i18n();
        $plugin_i18n->set_domain($this->get_plugin_text_domain());
        $plugin_i18n->set_directory();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Transifex_Live_Wordpress_Admin($this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Transifex_Live_Wordpress_Public($this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * Default getter functions
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }

    public function get_plugin_text_domain() {
        return $this->plugin_text_domain;
    }

}
