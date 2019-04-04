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

// check user capabilities
 if ( ! current_user_can( 'manage_options' ) ) {
 return;
 }

 $connected = $this->connectionStatus;

 ?>

<div class="doppler-woo-settings">

    <h1> <?php _e('Settings', 'doppler-for-woocommerce') ?> </h1>

    <?php 
    
    if(!$connected){

        ?>

        <form id="dplrwoo-form-connect" action="options.php" method="post">
            
            <?php
            // output security fields
            settings_fields( 'doppler_for_woocommerce_menu' );
            // output setting sections and their fields
            do_settings_sections( 'doppler_for_woocommerce_menu' );
            // output save settings button - not using, doing it with ajax
            //submit_button( 'Save Settings' );
            ?>

            <button id="dplrwoo-connect" class="dplrwoo-button">
                <div class="loading"></div>
                <?php _e('Connect', 'doppler-for-woocommerce') ?>
            </button>

            <div id="dplrwoo-messages">
            </div>

        </form>

        <?php

    }else if($connected){
        
        ?>
        <form id="dplrwoo-form-disconnect" action="options.php" method="post">

            <?php settings_fields( 'doppler_for_woocommerce_menu' ); ?>

            <input type="hidden" name="dplrwoo_user" value="" />
            <input type="hidden" name="dplrwoo_key" value="" />

            <div class="connected_status">
                <?php _e('You\'re connetcted to Doppler') ?> <br />
                User Email: <strong><?php echo get_option('dplrwoo_user')?></strong> <br />
                Api Key: <strong><?php echo get_option('dplrwoo_key')?></strong>
            </div>

            <button id="dplrwoo-disconnect" class="dplrwoo-button">
                <?php _e('Disconnect', 'doppler-for-woocommerce') ?>
            </button>

        </form>

        <?php
    }

    ?>

</div>