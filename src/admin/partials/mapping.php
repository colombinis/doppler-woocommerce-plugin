<table>
    <tbody>
<?php

//var_dump($dplr_fields);

//var_dump($wc_fields);

if(is_array($wc_fields)){

    foreach($wc_fields as $k=>$v){

        foreach($v as $i=>$j){

            ?>

            <tr>
                <th><?php echo $i?></th>
                <td>
                    <select name="dplr_fieldmap[]">
                        <option></option>
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