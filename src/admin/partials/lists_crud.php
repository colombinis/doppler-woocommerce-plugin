<div id="dplrwoo-crud">

    <form id="dplrwoo-form-list-crud" action="" method="post">

        <label><?php _e('Agregar nueva lista')?></label>
        <input type="text" value="" maxlength="20" disabled="disabled" />

        <button id="dplrwoo-save-list" class="dplrwoo-button" disabled="disabled">
            <?php _e('Create list', 'doppler-for-woocommerce') ?>
        </button>

    </form>

    <div class="dplrwoo-loading"></div>

    <table id="dprwoo-tbl-lists" class="tbl widefat mt-30 d-none">
        <thead>
            <tr>
                <th><?php _e('List ID', 'doppler-form')?></th>
                <th><?php _e('Name', 'doppler-form')?></th>
                <th><?php _e('Subscribers', 'doppler-form')?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

</div>