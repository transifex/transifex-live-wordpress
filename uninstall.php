<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @package Transifex_Live_Integration
 */

// If uninstall not called from WordPress, then exit.
if (! defined('WP_UNINSTALL_PLUGIN') ) {
    exit;
}

$settings_option_name = 'transifex_live_settings';

delete_option($settings_option_name);

$colors_option_name = 'transifex_live_colors';

delete_option($colors_option_name);

$options_option_name = 'transifex_live_options';

delete_option($options_option_name);

$transifex_settings_option_name = 'transifex_live_transifex_settings';

delete_option($transifex_settings_option_name);
