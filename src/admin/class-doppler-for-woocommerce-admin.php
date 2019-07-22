<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.fromdoppler.com/
 * @since      1.0.0
 *
 * @package    Doppler_For_Woocommerce
 * @subpackage Doppler_For_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Doppler_For_Woocommerce
 * @subpackage Doppler_For_Woocommerce/admin
 * @author     Doppler LLC <info@fromdoppler.com>
 */
class Doppler_For_Woocommerce_Admin {

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

	private $doppler_service;

	private $connectionStatus;

	private $credentials;

	private $admin_notice;

	private $success_message;

	private $error_message;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $doppler_service ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->doppler_service = $doppler_service;
		$this->connectionStatus = $this->check_connection_status();
		$this->success_message = false;
		$this->error_message = false;

		$this->check_saved_lists();

	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	public function set_error_message($message) {
		$this->error_message = $message;
	}

	public function set_success_message($message) {
		$this->success_message = $message;
	}

	public function get_error_message() {
		return $this->error_message;
	}

	public function get_success_message() {
		return $this->success_message;
	}

	public function display_error_message() {
		?>
		<div id="displayErrorMessage">
			<?php echo $this->get_error_message(); ?>
		</div>
		<?php
	}

	public function display_success_message() {
		?>
		<div id="displaySuccessMessage">
			<?php echo $this->get_success_message(); ?>
		</div>
		<?php
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'css/doppler-for-woocommerce-admin.css', 
			array(), 
			$this->version, 'all' 
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 
				$this->plugin_name, 
				plugin_dir_url( __FILE__ ) . 'js/doppler-for-woocommerce-admin.js', 
				array( 'jquery' ), 
				$this->version, false 
		);
		wp_localize_script( $this->plugin_name, 'ObjWCStr', array( 
			'invalidUser'	=> __( 'Ouch! Enter a valid Email.', 'doppler-for-woocommerce' ),
			'emptyField'	=> __( 'Ouch! The Field is empty.', 'doppler-for-woocommerce'),
			'wrongData'		=> __( 'Ouch! There\'s something wrong with your Username or API Key. Please, try again.'),
			'listSavedOk'   => __( 'The List has been created successfuly')						 				
		) );
	}

	/**
	 * Registers the admin menu
	 */
	public function dplrwoo_init_menu() {
		add_menu_page(
			__('Doppler for WooCommerce', 'doppler-for-woocommerce'),
			__('Doppler for WooCommerce', 'doppler-for-woocommerce'),
			'manage_options',
			'doppler_for_woocommerce_menu',
			array($this, "dplrwoo_admin_page"),
			plugin_dir_url( __FILE__ ) . 'img/icon-doppler-menu.png'
		);
	}

	/**
	 * Display the admin settings screen
	 */
	public function dplrwoo_admin_page() {
		include('partials/doppler-for-woocommerce-settings.php');
	}

	/**
	 * Display the Fields Mapping screen
	 */
	public function dplrwoo_mapping_page() {
		$fields = $this->get_checkout_fields();
	}

	/**
	 * Register the plugin settings and fields for doppler_for_woocommerce_menu.
	 */
	public function dplrwoo_settings_init() {

		if( !isset($_GET['tab']) || $_GET['tab']=='settings' ){

			add_settings_section(
				'dplrwoo_setting_section',
				'Example settings section in reading',
				array($this,'eg_setting_section_callback_function'),
				'doppler_for_woocommerce_menu'
			);

			add_settings_field(
				'dplrwoo_user', 
				__( 'User Email', 'doppler-for-woocommerce' ),
				array($this,'display_user_field'),
				'doppler_for_woocommerce_menu',
				'dplrwoo_setting_section',
				[
				'label_for' => 'dplrwoo_user',
				'class' => 'dplrwoo_user_row',
				]
			);

			add_settings_field(
				'dplrwoo_key',
				__( 'API Key', 'doppler-for-woocommerce' ),
				array($this,'display_key_field'),
				'doppler_for_woocommerce_menu',
				'dplrwoo_setting_section',
				[
				'label_for' => 'dplrwoo_key',
				'class' => 'dplrwoo_key_row',
				]
			);

			register_setting( 'doppler_for_woocommerce_menu', 'dplrwoo_user' );
			register_setting( 'doppler_for_woocommerce_menu', 'dplrwoo_key' );

		}

	}

	/**
	 * Shows user field.
	 */
	function display_user_field( $args ) {
		$option = get_option( 'dplrwoo_user' );
		?>
			<input type="email" value="<?php echo $option ?>" name="dplrwoo_user" />
		<?php
	}

	/**
	 * Shows API Key field
	 */
	function display_key_field( $args ) {
		$option = get_option( 'dplrwoo_key' );
		?>
			<input type="text" value="<?php echo $option ?>" name="dplrwoo_key" maxlength="32" required/>
		<?php
	}

	/**
	 * Example for section text.
	 */
	function eg_setting_section_callback_function( $args ) {
		?>
			<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Example text', 'doppler-for-woocommerce' ); ?></p>
		<?php
	}

	/**
	 * Handles ajax connection with API
	 * used by "connect" button in dopper-for-woocommerce-settings.php
	 */
	public function dplrwoo_api_connect() {
		$connected = $this->doppler_service->setCredentials(['api_key' => $_POST['key'], 'user_account' => $_POST['user']]);
		echo ($connected)? 1:0;
		exit();
	}

	public function dplrwoo_get_lists() {
		echo json_encode($this->get_lists_by_page($_POST['page']));
		exit();
	}

	public function dplrwoo_save_list() {
		if(!empty($_POST['listName'])){
			echo $this->create_list($_POST['listName']);
		}
		exit();
	}

	private function create_list($list_name) {
		$subscriber_resource = $this->doppler_service->getResource('lists');
		return $subscriber_resource->saveList( $list_name )['body'];
	}

	public function dplrwoo_delete_list() {
		if(empty($_POST['listId'])) return false;
		$subscribers_lists = get_option('dplr_subscribers_list');
		$subscriber_resource = $this->doppler_service->getResource('lists');
		echo json_encode($subscriber_resource->deleteList( $_POST['listId'] ));
		exit();
	}

	/**
	 * Create default lists
	 */
	public function dplrwoo_create_default_lists(){
		$resp = array();
		$default_buyers_list = __('WooCommerce Buyers', 'doppler-for-woocommerce');
		$default_contact_list = __('WooCommerce Contacts', 'doppler-for-woocommerce');
		$respBuyer = json_decode($this->create_list($default_buyers_list));
		$respContact = json_decode($this->create_list($default_contact_list));
		$resp['buyers']['response'] = $respBuyer;
		$resp['contacts']['response'] = $respContact;
		if( !empty($respBuyer->createdResourceId) && !empty($respContact->createdResourceId) ){
			update_option( 'dplr_subscribers_list', 
				array( 
					'buyers' => $respBuyer->createdResourceId, 
					'contacts' => $respContact->createdResourceId
				) 
			) ;
		}
		echo json_encode($resp);
		exit();
	}

	/**
	 * Check connection status.
	 */
	public function check_connection_status() {

		$user = get_option('dplrwoo_user');
		$key = get_option('dplrwoo_key');

		if( empty($user) || empty($key) ){
			return false;
		}

		if( !empty($user) && !empty($key) ){
			if(empty($this->doppler_service->config['crendentials'])){
				$this->doppler_service->setCredentials(array('api_key' => $key, 'user_account' => $user));
			}
			if( is_admin() ){ //... if we are at the backend.
				$response =  $this->doppler_service->connectionStatus();
				if($response['response']['code']>=400){
					 $this->admin_notice = array('error', '<strong>Doppler of WooCommerce ERROR</strong> ' . $response['response']['message']);
					 return false;
				}
			}
			return true;
		}

		return false;

	}

	/**
	 * Get the customer's fields.
	 */
	public function get_checkout_fields() {
		if ( ! class_exists( 'WC_Session' ) ) {
			include_once( WP_PLUGIN_DIR . '/woocommerce/includes/abstracts/abstract-wc-session.php' );
		}
		WC()->session = new WC_Session_Handler;
		WC()->customer = new WC_Customer;
		return WC()->checkout->checkout_fields;
	}

	/**
	 * Compares field types between WC and Doppler
	 */
	function check_field_type( $wc_field_type, $dplr_field_type ) {
		
		empty($wc_field_type)? $wc_field_type = 'string' : '';
		
		switch($wc_field_type){
			case 'string':
					if($dplr_field_type === 'string'){
						return true;
					}
					return false;
				break;
			case 'state':
					if($dplr_field_type === 'string'){
						return true;
					}
					return false;
				break;
			case 'radio':
					if($dplr_field_type === 'gender'){
						return true;
					}
					return false;
				break;
			case 'email':
					if($dplr_field_type === 'email'){
						return true;
					}
					return false;
				break;
			case 'country':
					if($dplr_field_type === 'country'){
						return true;
					}
					return false;
				break;
			case 'tel':
					if($dplr_field_type === 'phone'){
						return true;
					}
					return false;
				break;
			case 'date':
					if($dplr_field_type === 'date'){
						return true;
					}
					return false;
				break;
			case 'datetime':
					if($dplr_field_type === 'date'){
						return true;
					}
					return false;
				break;
			case 'datetime-local':
					if($dplr_field_type === 'date'){
						return true;
					}
					return false;
				break;
			case 'number':
				if($dplr_field_type === 'number'){
						return true;
					}
					return false;
				break;
			case 'checkbox':
				if($dplr_field_type === 'boolean'){
						return true;
					}
					return false;
				break;
			default:
					return true;
				break;
		}

	}

	/**
	 * Get lists
	 */
	public function get_alpha_lists() {
		$list_resource = $this->doppler_service->getResource('lists');
		$dplr_lists = $list_resource->getAllLists();
		if(is_array($dplr_lists)){
			foreach($dplr_lists as $k=>$v){
			  if(is_array($v)):
				foreach($v as $i=>$j){
				  $dplr_lists_aux[$j->listId] = array('name'=>trim($j->name), 'subscribersCount'=>$j->subscribersCount);
				}
			  endif;
			}
			$dplr_lists_arr = $dplr_lists_aux;
		}
		return $dplr_lists_arr;	
	}

	public function get_lists_by_page( $page = 1 ) {
		$list_resource = $this->doppler_service->getResource( 'lists' );
		return $list_resource->getListsByPage( $page );
	}


	/**
	 * Al registrarse se guarda el usuario
	 * en la lista de contactos.
	 */
	public function dplrwoo_created_customer( $customer_id, $customer_data, $customer_password ) {
		if( isset($_POST['register']) ){
			$fields_map = get_option('dplrwoo_mapping');
			$list_id = get_option('dplr_subscribers_list')['contacts'];
			if( !empty($fields_map) && !empty($list_id) ){
				$fields = array();
				foreach($fields_map as $k=>$v){
					if($v!=''){
						$fields[] = array('name'=>$v, 'value'=>$_POST[$k]);
					}
				}
				$this->subscribe_customer($list_id, $customer_data['user_email'], $fields);
			}
		}
	}
	
	/**
	 * Envía subscriptor a la lista de compradores
	 * cuando una orden pasa a estado "completo".
	 */
	public function dplrwoo_order_completed( $order_id, $old_status, $new_status, $instance ) {
		if( $new_status == "completed" ) {
			$list_id = get_option('dplr_subscribers_list')['buyers'];
			$order = wc_get_order( $order_id );
			$order_data = $order->get_data();
			$fields = $this->get_mapped_fields($order);
			$this->subscribe_customer($list_id, $order_data['billing']['email'], $fields);
		}
	}

	/**
	 * Envía a la lista de contactos los datos del cliente
	 * en el checkout.
	 * Sólo para WC > 3.0
	 */
	public function dplrwoo_customer_checkout_success( $order_id ) {
		$list_id = get_option('dplr_subscribers_list')['contacts'];
		$order = wc_get_order( $order_id );
		$order_data = $order->get_data();
		$fields = $this->get_mapped_fields($order);
		$this->subscribe_customer($list_id, $order_data['billing']['email'], $fields);
	}

	/**
	 * Get "Registered" users
	 *
	 * Registered users are also saved
	 * to Contact List. 
	 * 
	 */
	private function get_registered_users() {
		$users = get_users( array('role'=>'Customer') );
		$fields_map = get_option('dplrwoo_mapping');
		$registered_users = array();
		if(!empty($users)){
			foreach($users as $k=>$user){
				$meta_fields = get_user_meta($user->ID);
				$fields = array();
				foreach($fields_map as $k=>$v){
					if($v!=''){
						$value = array_shift(array_values($meta_fields[$k]));
						$fields[] = array('name'=>$v, 'value'=>$value);
					}
				}
				$email = array_shift(array_values($meta_fields['billing_email']));
				$registered_users[$email] = $fields;
			}
		}
		return $registered_users;
	}

	/**
	 * Syncrhonizes "Contacts" or "Buyers"
	 *
	 * Contacts are customers who completed 
	 * a checkout form or are registered users.
	 * Buyers are users who has orders that
	 * have been completed.
	 * 
	 */
	public function dplrwoo_synch() {

		$orders_by_email = array();

		$args = array(
			'limit'		=> -1,
			'orderby'	=> 'date',
			'order'		=> 'DESC'
		);
		
		if($_POST['list_type'] == 'contacts'){
			$list_id = get_option('dplr_subscribers_list')['contacts'];
			$registered_users = $this->get_registered_users();
		}else if($_POST['list_type'] == 'buyers'){
			$list_id = get_option('dplr_subscribers_list')['buyers'];
			$args['status'] = 'completed';
		}

		$orders = wc_get_orders($args);

		if(!empty($orders)){
			foreach($orders as $k=>$order){
				$orders_by_email[$order->data['billing']['email']] = $this->get_mapped_fields($order);
			}
		}

		if($_POST['list_type'] == 'contacts'){
			$users = array_merge($registered_users,$orders_by_email);
		}else{
			$users = $orders_by_email;
		}
		
		$subscribers['items'] =  array();
		$subscribers['fields'] =  array();

		if(empty($users)) return false;

		foreach($users as $email=>$fields){
			$subscribers['items'][] = array('email'=>$email, 'fields'=>$fields);
		}

		$subscriber_resource = $this->doppler_service->getResource( 'subscribers' );
		echo $subscriber_resource->importSubscribers($list_id, $subscribers)['body'];
		exit();

	}

	/**
		* Update Subscribers count
		*
		* After synchronizing update 
		* the subscribers counter
		* next to the lists selector.
		*
		*/
	public function update_subscribers_count() {
			$c_count = 0;
			$b_count = 0;
			$list_resource = $this->doppler_service->getResource( 'lists' );
			$c_list_id = get_option('dplr_subscribers_list')['contacts'];
			if(!empty($c_list_id)){
				$c_count = $list_resource->getList($c_list_id)->subscribersCount;
			}
			$b_list_id = get_option('dplr_subscribers_list')['buyers'];
			if(!empty($b_list_id)){
				$b_count = $list_resource->getList($b_list_id)->subscribersCount;
			}
			echo json_encode(array('contacts'=>$c_count, 'buyers'=>$b_count));
			exit();
	}

	public function validate_tracking_code($code){
		return preg_match("/(<|%3C)script[\s\S]*?(>|%3E)[\s\S]*?(<|%3C)(\/|%2F)script[\s\S]*?(>|%3E)/",$code);
	}

	/**
	 * If want to show an admin message, 
	 * set $this->admin_notice = array( $class, $text), 
	 * where class is success, warning, etc.
	 */
	public function show_admin_notice() {
		$class = $this->admin_notice[0];
		$text = $this->admin_notice[1];
		if( !empty($class) && !empty($class) ){
			?>
				<div class="notice notice-<?php echo $class?> is-dismissible">
					<p><?php echo $text ?></p>
				</div>
			<?php
		}
	}

	/**
	 * Get the mapped fields of a given order.
	 */
	private function get_mapped_fields( $order ) {
		$order_data = $order->get_data();
		$fields = array();
		if(!empty($order_data)){
			$fields_map = get_option('dplrwoo_mapping');
			//Map default fields.
			foreach($order_data as $key=>$fieldgroup){
				if( $key === 'shipping' || $key === 'billing' ){	
					foreach($fieldgroup as $fieldname=>$v){
						$f = $key.'_'.$fieldname;
						if( isset($fields_map[$f]) && $fields_map[$f] != '' ){
							$fields[] = array('name'=>$fields_map[$f], 'value'=>$v);
						}
					}
				}
			}
			//Map custom fields
			if(!empty($fields_map)){
				foreach($fields_map as $wc_field=>$dplr_field){
					if( !empty($order->get_meta('_'.$wc_field)) && !empty($dplr_field) ){
						$fields[] = array('name'=>$dplr_field, 'value'=>$order->get_meta('_'.$wc_field));
					}
				}
			}
			return $fields;
		}
	}

	/**
	 * Send email and fields to a Doppler List
	 */
	private function subscribe_customer( $list_id, $email, $fields ){
		if( !empty($list_id) && !empty($email) ){
			$subscriber['email'] = $email;
			$subscriber['fields'] = $fields; 
			$subscriber_resource = $this->doppler_service->getResource('subscribers');
			$result = $subscriber_resource->addSubscriber($list_id, $subscriber);
		}
	}

}