<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.fromdoppler.com/
 * @since      1.0.0
 *
 * @package    Doppler_For_Woocommerce
 * @subpackage Doppler_For_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Doppler_For_Woocommerce
 * @subpackage Doppler_For_Woocommerce/public
 * @author     Doppler LLC <info@fromdoppler.com>
 */
class Doppler_For_Woocommerce_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Doppler_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Doppler_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/doppler-for-woocommerce-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Doppler_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Doppler_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/doppler-for-woocommerce-public.js', array( 'jquery' ), $this->version, false );
		if(get_option('dplrwoo')){
			wp_enqueue_script( 'hub-fromdoppler', '//hub.fromdoppler.com/public/dhtrack.js', array(), false, false );
		}
	}

	/**
	 * Add async tag to hub script
	 */
	public function dplrwoo_script_loader_tag($tag, $handle) {
		if ($handle === 'hub-fromdoppler') {
			if (false === stripos($tag, 'async')) {
				$tag = str_replace(' src', ' async="async" src', $tag);	
			}
			if (false === stripos($tag, 'defer')) {
				$tag = str_replace('<script ', '<script defer ', $tag);	
			}
		}
		return $tag;
	}

}
