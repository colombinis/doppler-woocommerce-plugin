<form id="dplrwoo-form-disconnect" action="" method="post">

<?php settings_fields( 'doppler_for_woocommerce_menu' ); ?>

<?php

$used_fields = array_filter($maps);

if(is_array($wc_fields)){

    foreach($wc_fields as $fieldtype=>$arr){

        if( $fieldtype!='' && $fieldtype!='order' ):

            ?>
            <table class="tbl tbl--mapping">
                <thead>
                    <tr>
                        <th colspan="2">
                            <?php
                            switch($fieldtype){
                                case 'billing':
                                    _e('Billing fields', 'doppler-for-woocommerce');
                                    break;
                                case 'shipping':
                                    _e('Shipping fields', 'doppler-for-woocommerce');
                                    break;
                                case 'account':
                                    _e('Account fields', 'doppler-for-woocommerce');
                                    break;
                                default:
                                    echo $fieldtype;
                                    break;
                            }
                            ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
            
            <?php

            foreach($arr as $fieldname=>$fieldAtributes){

                ?>

                    <tr>
                        <td><?php echo $fieldAtributes['label']?></td>
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

            ?>
                </tbody>
            </table>

            <?php

        endif;
    }

}

?>
    </tbody>

</table>

<button id="dplrwoo-mapping-btn" class="dplrwoo-button">
    <?php _e('Save', 'doppler-for-woocommerce') ?>
</button>

</form>