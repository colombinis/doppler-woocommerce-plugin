<div class="dplr-tab-content">

    <?php $this->display_success_message() ?>

    <?php $this->display_error_message() ?>

    <div id="showSuccessResponse">
    </div>

    <div id="showErrorResponse">
    </div>

    <?php
    
    if( empty($subscribers_lists['contact']) && empty($subscribers_lists['buyers']) ):
    
        ?>
        <p id="dplrwoo-createlist-div">
            <?php
            _e('You currently don\'t have Doppler Lists selected. Do you want to create a List to send your Contacts to and another to send to your buyers?', 'doppler-for-woocommerce');
            ?>
            <a id="dplrwoo-create-lists"><?php _e('Create Doppler Lists', 'doppler-for-woocommerce')?></a>
            <img src="<?php echo DOPPLER_FOR_WOOCOMMERCE_URL?>admin/img/loading.gif" class="d-none"/>
        </p>
        <?php

    endif;
    
    ?>

    <form id="dplrwoo-form-list" action="" method="post">

        <?php wp_nonce_field( 'map-lists' );?>

        <table class="grid">
            <tbody>
                <tr>
                    <th colspan="2"></th>
                    <th class="text-right td-sm"><?php _e('Subscriptors', 'doppler-for-woocommerce')?></th>
                    <th></th>
                </tr>
                <tr>
                    <th>
                        <?php _e('Buyers', 'doppler-for-woocommerce')?>
                    </th>
                    <td>
                        <select name="dplr_subscribers_list[buyers]" class="dplr-lists-sel">
                            <option value=""></option>
                            <?php 
                            if(!empty($lists)){
                                foreach($lists as $k=>$v){
                                    if( $subscribers_lists['contacts'] != $k ):
                                    ?>
                                    <option value="<?php echo $k?>" 
                                        <?php if($subscribers_lists['buyers']==$k){ echo 'selected'; $scount = $v['subscribersCount']; } ?>
                                        data-subscriptors="<?php echo $v['subscribersCount']?>">
                                        <?php echo $v['name']?>
                                    </option>
                                    <?php
                                    endif;
                                }
                            }   
                            ?>
                        </select>
                    </td>
                    <td class="text-right td-sm">
                        <span id="buyers-count"><?php echo $scount?></span>
                    </td>
                    <td>
                    </td>
                </tr>
                <?php $scount='' ?>
                <tr>
                    <th>
                        <?php _e('Contacts', 'doppler-for-woocommerce')?>
                    </th>
                    <td>
                        <select name="dplr_subscribers_list[contacts]" class="dplr-lists-sel">
                            <option value=""></option>
                            <?php 
                                if(!empty($lists)){
                                    foreach($lists as $k=>$v){
                                        if( $subscribers_lists['buyers'] != $k ):
                                        ?>
                                        <option value="<?php echo $k?>" 
                                            <?php if($subscribers_lists['contacts']==$k){ echo 'selected'; $scount = $v['subscribersCount']; }?>
                                            data-subscriptors="<?php echo $v['subscribersCount']?>">
                                            <?php echo $v['name']?>
                                        </option>
                                        <?php
                                        endif;
                                    }
                                }
                            ?>
                        </select>
                    </td>
                    <td class="text-right td-sm">
                        <span id="contacts-count"><?php echo $scount?></span>
                    </td>
                    <td>
                    </td>
                </tr>
            </tbody>
        </table>

        <button id="dplrwoo-lists-btn" class="dplrwoo-button">
            <?php _e('Save', 'doppler-for-woocommerce') ?>
        </button>

    </form>

    <hr/>
    <a id="dplrwoo-new-list" class="small-text pointer"><?php _e( 'Create List' , 'doppler-for-woocommerce') ?></a>
    <?php if(!empty($subscribers_lists['contacts']) || !empty($subscribers_lists['buyers'])): ?> 
        <span> | </span>
        <a id="dplrwoo-btn-synch" class="small-text pointer"><?php _e('Synchronize lists', 'doppler-for-woocommerce')?></a>
        <img class="doing-synch" src="<?php echo DOPPLER_FOR_WOOCOMMERCE_URL . 'admin/img/ajax-synch.gif' ?>" alt="<?php _e('Synchronizing', 'doppler-for-woocommerce')?>"/>
        <span class="synch-ok dashicons dashicons-yes text-dark-green"></span>        
    <?php endif;?>                 
</div>

<div id="dplr-dialog-confirm" class="doppler-woo-settings w-100" title="<?php _e('Create a Doppler List', 'doppler-form'); ?>">
    <form>
        <p>
            <input type="text" maxlength="50" value="" placeholder="<?php _e('Write the List name','doppler-for-woocommerce')?>" class="w-100"/>
            <img src="<?php echo DOPPLER_FOR_WOOCOMMERCE_URL?>admin/img/loading.gif" class="d-none" alt="<?php _e('Saving','doppler-for-woocommerce')?>"/>
        </p>
    </form>
</div>