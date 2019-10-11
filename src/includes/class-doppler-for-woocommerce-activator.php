<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Fired during plugin activation
 *
 * @link       https://www.fromdoppler.com/
 * @since      1.0.0
 *
 * @package    Doppler_For_Woocommerce
 * @subpackage Doppler_For_Woocommerce/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Doppler_For_Woocommerce
 * @subpackage Doppler_For_Woocommerce/includes
 * @author     Doppler LLC <info@fromdoppler.com>
 */
class Doppler_For_Woocommerce_Activator {

	/**
	 * Deactivate plugin (use period)
	 *
	 * Creates all the neccesary options, fields
	 * or database tables. Perhaps check if WooCommerce exists?
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
		global $wpdb;
		$table_name = $wpdb->wp_prefix . DOPPLER_ABANDONED_CART_TABLE;
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $table_name (
			    id BIGINT(20) NOT NULL AUTO_INCREMENT,
			    name VARCHAR(60),
			    lastname VARCHAR(60),
			    email VARCHAR(100),
			    phone VARCHAR(20),
			    location VARCHAR(100),
			    cart_contents LONGTEXT,
			    cart_total DECIMAL(10,2),
			    currency VARCHAR(10),
			    time DATETIME DEFAULT '0000-00-00 00:00:00',
			    session_id VARCHAR(60),
			    other_fields LONGTEXT,
			    PRIMARY KEY  (id)
		) $charset_collate;";
		  
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		$sql ="ALTER TABLE $table_name AUTO_INCREMENT = 1";
		dbDelta( $sql );

		//Saves plugin version.
		update_option('dplrwoo_version', DOPPLER_FOR_WOOCOMMERCE_VERSION);


	}

}
