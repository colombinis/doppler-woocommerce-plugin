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
 * @link       https://www.fromdoppler.com/
 * @since      1.0.0
 *
 * @package    Doppler_For_Woocommerce
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove all plugin options and plugin tables from database.
if( $_REQUEST['slug']==='doppler-for-woocommerce' ){

	global $wpdb;
	
	$option_name = 'dplr_subscribers_list';
	delete_option($option_name);
	delete_site_option($option_name);

	$option_name = 'dplrwoo_mapping';
	delete_option($option_name);
	delete_site_option($option_name);

	$option_name = 'dplr_use_hub';
	delete_option($option_name);
	delete_site_option($option_name);

	$option_name = 'dplrwoo_user';
	delete_option($option_name);
	delete_site_option($option_name);

	$option_name = 'dplrwoo_key';
	delete_option($option_name);
	delete_site_option($option_name);

	$option_name = 'dplrwoo_version';
	delete_option($option_name);
	delete_site_option($option_name);

}
