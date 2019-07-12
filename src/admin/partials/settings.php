<div class="dplr-tab-content">

<?php 
    
    if(!$connected){

        ?>

        <form id="dplrwoo-form-connect" action="options.php" method="post" easy-validate>
            
            <?php
            // output security fields
            settings_fields( 'doppler_for_woocommerce_menu' );
            // output setting sections and their fields
            do_settings_sections( 'doppler_for_woocommerce_menu' );
            // output save settings button - not using, doing it with ajax
            //submit_button( 'Save Settings' );
            ?>

            <button id="dplrwoo-connect" class="dplrwoo-button dplrwoo-button--rounded">
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

            <div class="connected-status">
                <p>
                    <?php _e('You\'re connetcted to Doppler') ?>
                </p>
                <p>
                User Email: <strong><?php echo get_option('dplrwoo_user')?></strong> <br />
                Api Key: <strong><?php echo get_option('dplrwoo_key')?></strong>
                </p>
            </div>

            <button id="dplrwoo-disconnect" class="dplrwoo-button">
                <?php _e('Disconnect', 'doppler-for-woocommerce') ?>
            </button>

        </form>

        <?php
    }

?>

</div>