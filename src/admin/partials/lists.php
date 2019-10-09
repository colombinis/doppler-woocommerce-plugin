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

        <div class="flex-grow-1">
            <p class="size-medium" id="dplr-settings-text">
            <?php
            if( empty($subscribers_lists['contacts']) && empty($subscribers_lists['buyers']) ):
            
                _e('You currently don\'t have Doppler Lists selected. Do you want to create a List to send your Contacts to and another to send to your buyers?', 'doppler-for-woocommerce');
               
               ?>
                
                <button id="dplrwoo-create-lists" class="dp-button button-small primary-green"><?php _e('Create Doppler Lists', 'doppler-for-woocommerce')?></button>
                
                <?php
            else :
                _e('Your Customers will be sent automatically to the selected Doppler List after checkout.', 'doppler-for-woocommerce');
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

        <?php wp_nonce_field( 'map-lists' );?>
        
        <p>
            <label><?php _e('Doppler List to send Buyers', 'doppler-for-woocommerce')?></label>
            <select name="dplr_subscribers_list[buyers]" class="dplrwoo-lists-sel" id="buyers-list">
                <option value=""></option>
                <?php 
                if(!empty($lists)){
                    foreach($lists as $k=>$v){
                        if( !empty( $subscribers_lists['contacts']) && $subscribers_lists['contacts'] != $k ):
                        ?>
                        <option value="<?php echo esc_attr($k)?>" 
                            <?php if(!empty($subscribers_lists['buyers']) && $subscribers_lists['buyers']==$k){ echo 'selected'; $scount = $v['subscribersCount']; } ?>
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
                <option value=""></option>
                <?php 
                    if(!empty($lists)){
                        foreach($lists as $k=>$v){
                            if( !empty($subscribers_lists['buyers']) && $subscribers_lists['buyers'] != $k ):
                            ?>
                            <option value="<?php echo $k?>" 
                                <?php if(!empty($subscribers_lists['contacts']) && $subscribers_lists['contacts']==$k){ echo 'selected'; $scount = $v['subscribersCount']; }?>
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
                $btn_disable = empty($subscribers_lists['buyers']) && empty($subscribers_lists['contacts']) ? 'disabled' : '';
            ?>

            <button id="dplrwoo-clear" class="dp-button button-medium primary-grey" <?php echo $btn_disable?>>
                <?php _e('Clear selection', 'doppler-for-learnpress') ?>
            </button>
        
            <button id="dplrwoo-lists-btn" class="dp-button button-medium primary-green ml-1" <?php echo $btn_disable?>>
                <?php _e('Synchronize', 'doppler-for-woocommerce') ?>
            </button>

        </p>

    </form>
               
</div>