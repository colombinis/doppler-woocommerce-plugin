<?php
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}
?>

<div class="dplr-tab-content">

    <?php $this->display_success_message() ?>

    <?php $this->display_error_message() ?>

    <div id="showSuccessResponse" class="messages-container info d-none">
    </div>

    <div id="showErrorResponse" class="messages-container blocker d-none">
    </div>

    <div class="d-flex flex-row">

        <div class="col-68">
            <p class="size-medium mt10 pr-10" id="dplr-settings-text">
            
            <?php

            $suggest_default_lists = false;
            if( empty($subscribers_lists['contacts']) && empty($subscribers_lists['buyers']) ):
                $suggest_default_lists = true;
                _e('Pick the Doppler Lists you want to import your Users into. You can sync existing Lists or create new ones.', 'doppler-for-woocommerce');
            else :
                _e('As they register to your store or buy a product, your Subscribers will be automatically sent to the selected Doppler Lists.', 'doppler-for-woocommerce');
            endif;
            ?>
            </p>
        </div>
        <div class="flex-grow-1"> 
            <form id="dplrwoo-form-list-new" class="text-right" action="" method="post">
                <input type="text" value="" class="d-inline-block"  maxlength="100" placeholder="<?php _e('Write the List name', 'doppler-for-woocommerce')?>"/>
                <button id="dplrwoo-save-list" class="dp-button dp-button--inline button-medium primary-green" disabled="disabled">
                    <?php _e('Create List', 'doppler-form') ?>
                </button>
            </form>
        </div>

    </div>

    <form id="dplrwoo-form-list" action="" method="post">
        <?php 
            wp_nonce_field( 'map-lists' );
            $selected_contacts_list = !empty( $subscribers_lists['contacts'])? $subscribers_lists['contacts'] : '';
            $selected_buyers_list = !empty( $subscribers_lists['buyers'])? $subscribers_lists['buyers'] : '';
        ?>
        <p>
            <label><?php _e('Doppler List to send Buyers', 'doppler-for-woocommerce')?></label>
            <select name="dplr_subscribers_list[buyers]" class="dplrwoo-lists-sel" id="buyers-list">
                <option value="0"><?php if($suggest_default_lists) _e('WooCommerce Buyers','doppler-for-woocommerce') ?></option>
                <?php 
                if(!empty($lists)){
                    foreach($lists as $k=>$v){
                        if( $selected_contacts_list != $k ):
                        ?>
                        <option value="<?php echo esc_attr($k)?>" 
                            <?php if( $selected_buyers_list ==$k && !$suggest_default_lists ){ echo 'selected'; $scount = $v['subscribersCount']; } ?>
                            data-subscriptors="<?php echo esc_attr($v['subscribersCount'])?>">
                            <?php echo esc_html($v['name'])?>
                        </option>
                        <?php
                        endif;
                    }
                }   
                ?>
            </select>
        </p>

        <p>
            
            <label><?php _e('Doppler List to send Contacts', 'doppler-for-woocommerce')?></label>
                   
            <select name="dplr_subscribers_list[contacts]" class="dplrwoo-lists-sel" id="contacts-list">
                <option value="0"><?php if($suggest_default_lists) _e('WooCommerce Contacts', 'doppler-for-woocommerce') ?></option>
                <?php 
                    if(!empty($lists)){
                        foreach($lists as $k=>$v){
                            if( $selected_buyers_list != $k ):
                            ?>
                            <option value="<?php echo $k?>" 
                                <?php if( $selected_contacts_list ==$k && !$suggest_default_lists ){ echo 'selected'; $scount = $v['subscribersCount']; }?>
                                data-subscriptors="<?php echo esc_attr($v['subscribersCount'])?>">
                                <?php echo esc_html($v['name']) ?>
                            </option>
                            <?php
                            endif;
                        }
                    }
                ?>
            </select>
        </p>  
        
        <p class="d-flex justify-end">

            <?php
               $btn_disable = !$suggest_default_lists && ( empty($subscribers_lists['buyers']) && empty($subscribers_lists['contacts']) ) ? 'disabled' : '';
            ?>

            <!--
            <button id="dplrwoo-clear" class="dp-button button-medium primary-grey" <?php echo $btn_disable?>>
                <?php _e('Clear selection', 'doppler-for-learnpress') ?>
            </button>
            -->
        
            <button id="dplrwoo-lists-btn" class="dp-button button-medium primary-green ml-1" <?php echo $btn_disable?>>
                <?php _e('Synchronize', 'doppler-for-woocommerce') ?>
            </button>

        </p>

    </form>
               
</div>