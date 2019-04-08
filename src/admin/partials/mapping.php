<form id="dplrwoo-form-disconnect" action="" method="post">

            <?php settings_fields( 'doppler_for_woocommerce_menu' ); ?>

<table class="tbl-mapping">
    
    <tbody>

<?php

if(is_array($wc_fields)){

    foreach($wc_fields as $fieldtype=>$arr){

        foreach($arr as $fieldname=>$fieldAtrributes){

            ?>

            <tr>
                <th><?php echo $fieldname?></th>
                <td>
                    <select name="dplrwoo_mapping[<?php echo $fieldname?>]">
                        <option></option>
                        <?php 
                        foreach ($dplr_fields as $field){
                            ?>
                            <option value="<?php echo $field->name?>">
                                <?php echo $field->name?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            
            <?php

        }

    }

}

?>
    </tbody>

</table>

<button id="dplrwoo-mapping-btn" class="dplrwoo-button">
    <?php _e('Save', 'doppler-for-woocommerce') ?>
</button>

</form>