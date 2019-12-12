<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.fromdoppler.com/
 * @since      1.0.0
 *
 * @package    Doppler_For_Woocommerce
 * @subpackage Doppler_For_Woocommerce/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Doppler_For_Woocommerce
 * @subpackage Doppler_For_Woocommerce/includes
 * @author     Doppler LLC <info@fromdoppler.com>
 */
class Doppler_For_Woocommerce_Deactivator {

	/**
	 * Deactivate plugin. (use period)
	 *
	 * Performs tasks on deactivate plugin.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		
		$options = get_option('dplr_settings');
		$has_consumer_secret = get_option('dplrwoo_consumer_secret');
		
		if( empty($options['dplr_option_useraccount']) || empty($options['dplr_option_apikey']) ||
			empty($has_consumer_secret) ) return false;

		//$url = DOPPLER_WOO_API_URL . 'accounts/'.$options['dplr_option_useraccount'].'/integrations/magento';
		/* DELETE THIS FOR PRODUCTION */
		$url = 'http://newapiqa.fromdoppler.net/accounts/mariofabianblanc@gmail.com/integrations/magento';
		$options['dplr_option_apikey'] = '884E71335D719C8F7A37A84F48D7EE6F';
		/* END DELETE THIS */
		
		$response = wp_remote_request($url, array(
			'method' => 'DELETE',
			'headers'=> array(
				"Accept" => "application/json",
				"Content-Type" => "application/json",
				"X-Doppler-Subscriber-Origin" => 'WooCommerece',
				"Authorization" => "token ". $options['dplr_option_apikey']
			),
			'timeout' => 12,
		));
		var_dump($response); die();
	}

}
