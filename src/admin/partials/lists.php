<div class="dplr-tab-content">

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
                        <select name="dplr_subsribers_list[buyers]">
                            <option value=""></option>
                            <?php 
                            if(!empty($lists)){
                                foreach($lists as $k=>$v){
                                    ?>
                                    <option value="<?php echo $k?>" 
                                        <?php if($subscribers_lists['buyers']==$k){ echo 'selected'; $scount = $v['subscribersCount']; } ?>
                                        data-subscriptors="<?php echo $v['subscribersCount']?>">
                                        <?php echo $v['name']?>
                                    </option>
                                    <?php
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
                        <select name="dplr_subsribers_list[contacts]">
                            <option value=""></option>
                            <?php 
                                if(!empty($lists)){
                                    foreach($lists as $k=>$v){
                                        ?>
                                        <option value="<?php echo $k?>" 
                                            <?php if($subscribers_lists['contacts']==$k){ echo 'selected'; $scount = $v['subscribersCount']; }?>
                                            data-subscriptors="<?php echo $v['subscribersCount']?>">
                                            <?php echo $v['name']?>
                                        </option>
                                        <?php
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
    <?php if(!empty($subscribers_lists['contacts']) || !empty($subscribers_lists['buyers'])): ?>                     
        <a id="btn-synch" class="small-text pointer"><?php _e('Synchronize lists', 'doppler-for-woocommerce')?></a>
        <img class="doing-synch" src="<?php echo DOPPLER_FOR_WOOCOMMERCE_URL . 'admin/img/ajax-synch.gif' ?>" alt="<?php _e('Synchronizing', 'doppler-for-woocommerce')?>"/>
        <span class="synch-ok dashicons dashicons-yes text-dark-green"></span>        
    <?php endif;?>                 
</div>