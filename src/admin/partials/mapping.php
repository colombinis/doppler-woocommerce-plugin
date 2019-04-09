<form id="dplrwoo-form-disconnect" action="" method="post">

            <?php settings_fields( 'doppler_for_woocommerce_menu' ); ?>

<table class="tbl-mapping">
    
    <tbody>

<?php

$used_fields = array_filter($maps);

if(is_array($wc_fields)){

    foreach($wc_fields as $fieldtype=>$arr){

        foreach($arr as $fieldname=>$fieldAtrributes){

            ?>

            <tr>
                <th><?php echo $fieldname?></th>
                <td>
                    <select class="dplrwoo-mapping-fields" name="dplrwoo_mapping[<?php echo $fieldname?>]">
                        <option></option>
                        <?php 
                        foreach ($dplr_fields as $field){
                            
                            if( !in_array($field->name,$used_fields) || $maps[$fieldname] === $field->name ){
                                ?>
                                <option value="<?php echo $field->name?>" <?php if( $maps[$fieldname] === $field->name ) echo 'selected' ?>>
                                    <?php echo $field->name?>
                                </option>
                                <?php
                            }
                        
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