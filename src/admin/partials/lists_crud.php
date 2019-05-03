<div id="dplrwoo-crud" class="dplr-tab-content">

    <form id="dplrwoo-form-list-crud" action="" method="post">

        <label><?php _e('Create new List')?></label>
        <input type="text" value="" maxlength="20" disabled="disabled" maxlength="100" placeholder="<?php _e('Enter List name', 'doppler-for-woocommerce')?>"/>

        <button id="dplrwoo-save-list" class="dplrwoo-button" disabled="disabled">
            <?php _e('Create list', 'doppler-for-woocommerce') ?>
        </button>

    </form>

    <div class="dplrwoo-loading"></div>

    <table id="dprwoo-tbl-lists" class="grid widefat mt-30">
        <thead>
            <tr>
                <th><?php _e('List ID', 'doppler-form')?></th>
                <th><?php _e('Name', 'doppler-form')?></th>
                <th><?php _e('Subscribers', 'doppler-form')?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>2</td>
                <td>3</td>
                <td>4</td>
            </tr>
        </tbody>
    </table>

</div>

<div id="dplr-dialog-confirm" title="<?php _e('Are you sure you want to delete the List? ', 'doppler-form'); ?>">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span> <?php _e('It\'ll be deleted and can\'t be recovered.', 'doppler-form')?></p>
</div>