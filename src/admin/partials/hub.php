<div class="dplr-tab-content">

    <form id="dplrwoo-form-hub" action="" method="post">

        <?php wp_nonce_field( 'use-hub' );?>

        <p>
            <?php _e('Some text explaining what Datahub script does','doppler-for-woocommerce') ?>
        </p>
        <p>
            <input type="checkbox" value="1" <?php if($use_hub == '1') echo 'checked' ?> name="dplr_use_hub"/> <?php _e('Use Datahub','doppler-for-woocommerce') ?>
        </p>
        <button id="dplrwoo-hub-btn" class="dplrwoo-button">
            <?php _e('Save', 'doppler-for-woocommerce') ?>
        </button>
    </form>

</div>