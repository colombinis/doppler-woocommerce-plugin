<div class="dplr-tab-content">

    <?php $this->display_success_message() ?>

    <?php $this->display_error_message() ?>

    <div id="showSuccessResponse" class="messages-container info">
    </div>

    <div id="showErrorResponse" class="messages-container blocker">
    </div>

    <?php
    
    if( empty($subscribers_lists['contact']) && empty($subscribers_lists['buyers']) ):
    
        ?>
        <p id="dplrwoo-createlist-div">
            <?php
            _e('You currently don\'t have Doppler Lists selected. Do you want to create a List to send your Contacts to and another to send to your buyers?', 'doppler-for-woocommerce');
            ?>
        </p>
        
        <button id="dplrwoo-create-lists" class="dp-button button-small primary-green"><?php _e('Create Doppler Lists', 'doppler-for-woocommerce')?></button>
        
        <?php

    endif;
    
    ?>

    <form id="dplrwoo-form-list" action="" method="post">

        <?php wp_nonce_field( 'map-lists' );?>

        <table class="grid panel w-100" cellspacing="0">
            <thead>
                <tr class="panel-header">
                    <th class="text-white semi-bold"><?php _e('Type', 'doppler-for-woocommerce') ?></th>
                    <th class="text-white semi-bold"><?php _e('List Name', 'doppler-for-woocommerce') ?></th>
                    <th class="text-white semi-bold"><?php _e('Subscriptors', 'doppler-for-woocommerce')?></th>
                </tr>
            </thead>
            <tbody class="panel-body">
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
                    <td class="text-center td-sm">
                        <span id="buyers-count"><?php echo $scount?></span>
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
                    <td class="text-center td-sm">
                        <span id="contacts-count"><?php echo $scount?></span>
                    </td>
                </tr>
            </tbody>
        </table>

        <button id="dplrwoo-lists-btn" class="dp-button button-medium primary-green">
            <?php _e('Save', 'doppler-for-woocommerce') ?>
        </button>

    </form>

    <hr/>
    <a id="dplrwoo-new-list" class="small-text pointer green-link"><?php _e( 'Create List' , 'doppler-for-woocommerce') ?></a>
    <?php 
        $args = array(
			'limit'		=> -1,
			'orderby'	=> 'date',
			'order'		=> 'DESC'
		);
        $orders = wc_get_orders($args);
        $contacts = $this->get_registered_users();
    ?>
    <?php if( (!empty($orders) ||  !empty($contacts)) && (!empty($subscribers_lists['contacts']) || !empty($subscribers_lists['buyers'])) ): ?> 
        <span> | </span>
        <a id="dplrwoo-btn-synch" class="small-text pointer green-link"><?php _e('Synchronize lists', 'doppler-for-woocommerce')?></a>
        <img class="doing-synch d-none" src="<?php echo DOPPLER_FOR_WOOCOMMERCE_URL . 'admin/img/ajax-synch.gif' ?>" alt="<?php _e('Synchronizing', 'doppler-for-woocommerce')?>"/>
        <span class="synch-ok dashicons dashicons-yes text-dark-green opacity-0"></span>        
    <?php endif;?>                 
</div>

<div id="dplr-dialog-confirm" class="dplr_settings" title="<?php _e('Create a Doppler List', 'doppler-form'); ?>">
    <form>
        <p>
            <input type="text" maxlength="50" value="" placeholder="<?php _e('Write the List name','doppler-for-woocommerce')?>" class="w-100"/>
            <img src="<?php echo DOPPLER_FOR_WOOCOMMERCE_URL?>admin/img/loading.gif" class="d-none" alt="<?php _e('Saving','doppler-for-woocommerce')?>"/>
        </p>
    </form>
</div>