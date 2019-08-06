<div class="dplr-tab-content">

    <?php $this->display_success_message() ?>
    <?php $this->display_error_message() ?>

    <form id="dplrwoo-form-hub" action="" method="post" class="w-100 mw-7">

        <?php wp_nonce_field( 'use-hub' );?>

        <p>
            <?php _e('Some text explaining what Datahub script does','doppler-for-woocommerce') ?>
        </p>
        <p>
            <textarea name="dplr_hub_script" class="w-100" rows="3" placeholder="<?php _e('Paste tracking code here.','doppler-for-woocommerce')?>"><?php echo stripslashes($dplr_hub_script) ?></textarea>
        </p>
        <button id="dplrwoo-hub-btn" class="dp-button button-medium primary-green">
            <?php _e('Save', 'doppler-for-woocommerce') ?>
        </button>

    </form>

</div>