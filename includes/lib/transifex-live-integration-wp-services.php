<?php

/*
 * @package TransifexLiveIntegration
 */

/**
 * Provides WP services to other classes.
 */
class Transifex_Live_Integration_WP_Services {

    /*
	 * Wraps WP site_url().
	 */
	function get_site_url() {
		return site_url();
	}
}