
<form id="dplrwoo-form-list" action="" method="post">

    <?php wp_nonce_field( 'map-lists' );?>

    <table class="tbl">
        
        <tbody>

            <tr>
                <td>
                    <?php _e('Compradores', 'doppler-for-woocommerce')?>
                </td>
                <td>
                    <select name="dplr_subsribers_list[buyers]">
                        <option value=""></option>
                        <?php 
                        if(!empty($lists)){
                            foreach($lists as $k=>$v){
                                ?>
                                <option value="<?php echo $k?>" <?php if($subscribers_lists['buyers']==$k) echo 'selected' ?>><?php echo $v?></option>
                                <?php
                            }
                        }   
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td>
                    <?php _e('Usuarios registrados', 'doppler-for-woocommerce')?>
                </td>
                <td>
                    <select name="dplr_subsribers_list[registered]">
                        <option value=""></option>
                        <?php 
                            if(!empty($lists)){
                                foreach($lists as $k=>$v){
                                    ?>
                                    <option value="<?php echo $k?>" <?php if($subscribers_lists['registered']==$k) echo 'selected' ?>><?php echo $v?></option>
                                    <?php
                                }
                            }
                        ?>
                    </select>
                </td>
            </tr>
        </tbody>

    </table>

    <button id="dplrwoo-lists-btn" class="dplrwoo-button">
        <?php _e('Save', 'doppler-for-woocommerce') ?>
    </button>

</form>