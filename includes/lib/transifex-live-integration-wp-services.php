<?php

/*
 * @package TransifexLiveIntegration
 */

/**
 * Provides WP services to other classes.
 */
class Transifex_Live_Integration_WP_Services {
	/**
	 * Settings array, corresponds to get_option('transifex_live_settings') from DB.
	 * @var array
	 */
	public $settings;

	public function __construct( $settings = null ) {
		$this->settings = $settings;
	}

  /*
	 * Wraps WP site_url() / home_url().
	 * @param bool $is_subdirectory_install Is the site installed in a subdirectory? (see settings defaults)
	 *           		                        Use this argument if settings are not available when constructing class.
	 */
	function get_site_url($is_subdirectory_install = null) {
		$is_subdirectory = false;
		if (isset($this->settings) && array_key_exists('is_subdirectory_install', $this->settings)) {
			$is_subdirectory = $this->settings['is_subdirectory_install'];
		} else if (isset($is_subdirectory_install)) {
			$is_subdirectory = $is_subdirectory_install;
		}
		if (!$is_subdirectory) { // retrieve from db, `home_url(), site_url()` functions cause recursion :(
			return get_option('siteurl');
		} else {
			return get_option('home');
		}
	}

}
