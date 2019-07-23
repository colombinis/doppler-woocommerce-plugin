<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.fromdoppler.com/
 * @since      1.0.0
 *
 * @package    Doppler_For_Woocommerce
 * @subpackage Doppler_For_Woocommerce/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<?php

 if ( ! current_user_can( 'manage_options' ) ) {
 return;
 }

 if( isset($_GET['tab']) ) {
    $active_tab = $_GET['tab'];
 }else{
    $active_tab = 'settings';
 } 

 $connected = $this->connectionStatus;

 ?>

<div class="wrap doppler-woo-settings">

    <h2 class="main-title"><?php _e('Doppler for WooCommerce', 'doppler-for-woocommerce')?> <?php echo $this->get_version()?></h2> 

    <h2 class="nav-tab-wrapper">
        <a href="?page=doppler_for_woocommerce_menu&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php _e('Settings', 'doppler-for-woocommerce')?></a>
        <?php if ($connected) :?>
            <a href="?page=doppler_for_woocommerce_menu&tab=fields" class="nav-tab <?php echo $active_tab == 'fields' ? 'nav-tab-active' : ''; ?>"><?php _e('Fields Mapping', 'doppler-for-woocommerce')?></a>
            <a href="?page=doppler_for_woocommerce_menu&tab=lists" class="nav-tab <?php echo $active_tab == 'lists' ? 'nav-tab-active' : ''; ?>"><?php _e('Lists to synchronize', 'doppler-for-woocommerce')?></a>
            <a href="?page=doppler_for_woocommerce_menu&tab=lists_crud" class="nav-tab <?php echo $active_tab == 'lists_crud' ? 'nav-tab-active' : ''; ?>"><?php _e('Lists Managment', 'doppler-for-woocommerce')?></a>
            <a href="?page=doppler_for_woocommerce_menu&tab=hub" class="nav-tab <?php echo $active_tab == 'hub' ? 'nav-tab-active' : ''; ?>"><?php _e('On-Site Tracking', 'doppler-for-woocommerce')?></a>
        <?php endif; ?>
    </h2>

    <h1 class="screen-reader-text"></h1>

    <?php

    if($connected):

    switch($active_tab){

        case 'lists':
            if( isset($_POST['dplr_subscribers_list']) && current_user_can('manage_options') && check_admin_referer('map-lists') ){
                update_option( 'dplr_subscribers_list', $_POST['dplr_subscribers_list'] );
                $this->set_success_message(__('Subscribers lists saved succesfully', 'doppler-for-woocommerce'));
            }
            $lists = $this->get_alpha_lists();
            $subscribers_lists = get_option('dplr_subscribers_list');
            require_once('lists.php');
        break;

        case 'lists_crud':
            require_once('lists_crud.php');
        break;

        case 'fields':
            if( isset($_POST['dplrwoo_mapping']) && current_user_can('manage_options') && check_admin_referer('map-fields') ){
                update_option( 'dplrwoo_mapping', $_POST['dplrwoo_mapping'] );
                $this->set_success_message(__('Fields mapped succesfully', 'doppler-for-woocommerce'));
            }
            $wc_fields = $this->get_checkout_fields();
            $fields_resource = $this->doppler_service->getResource('fields');
            $dplr_fields = $fields_resource->getAllFields();
            $dplr_fields = isset($dplr_fields->items) ? $dplr_fields->items : [];
            $maps = get_option('dplrwoo_mapping');
            require_once('mapping.php');
        break;

        case 'hub':
            if( $_POST['_wpnonce'] && current_user_can('manage_options') && check_admin_referer('use-hub') ){
				if($this->validate_tracking_code($_POST['dplr_hub_script'])):
					update_option( 'dplr_hub_script', $_POST['dplr_hub_script']);
					$this->set_success_message(__('On Site Tracking code saved successfully', 'doppler-for-woocommerce'));
				else:
                    $this->set_error_message(__('Tracking code is invalid', 'doppler-for-woocommerce'));
				endif;
            }
            $dplr_hub_script = get_option('dplr_hub_script');
            require_once('hub.php');
        break;

        default:
            require_once('settings.php');
        break;
    }

    else:

        ?>
        <div><?php echo $this->admin_notice[1]?></div>
        <?php

    endif;

    ?>
    
</div>