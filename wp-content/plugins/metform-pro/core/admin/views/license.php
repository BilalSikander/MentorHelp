<?php

defined( 'ABSPATH' ) || exit;

$license_status = \MetForm_Pro\Libs\License::instance()->status();

?><div class="wrap">
    <h2>License Settings</h2>
    <div class="metform-admin-container stuffbox" style="padding:15px">
        <div class="attr-card-body">
            <form action="" method="post" class="form-group attr-input-group mf-admin-input-text mf-admin-input-text--metform-license-key">

                <?php if($license_status == 'invalid') :?>
                <p>Enter your license key here to activate MetForm Pro. It will enable update notice and auto updates.</p>

                <ol>
                    <li>Log in to your Wpmet account to get the license key.</li>
                    <li>If you don't yet buy this product, get <a href="https://wpmet.com/metform-pricing/" target="_blank">MetForm Pro</a> now.</li>
                    <li>Copy the MetForm Pro license key from your account and paste it below.</li>
                </ol>

                <label for="mf-admin-option-text-metform-license-key"><b>Your License Key</b></label><br/><br/>
                    <input type="text" class="attr-form-control" id="mf-admin-option-text-metform-license-key" placeholder="Please insert your license key here" name="metform-pro-settings-page-key" value="">
                    <span class="attr-input-group-btn">
                        <input type="hidden" name="metform-pro-settings-page-action" value="activate">
                        <button class="button button-primary" type="submit"><div class="mf-spinner"></div>Activate</button>
                    </span>

                <div class="metform-license-form-result">
                    <p class="attr-alert attr-alert-info">
                        Still can't find your lisence key? <a target="_blank" href="https://wpmet.com/support-ticket">Knock us here!</a>
                    </p>
                </div>

                <?php else: ?>
                <div id="metform-sites-notice-id-license-status" class="metform-notice notice metform-active-notice notice-success" dismissible-meta="user">
                    <p><?php printf( esc_html__('Congratulations! You\'r product is activated for "%s"', 'metform-pro'), parse_url(home_url(), PHP_URL_HOST)); ?></p>
                </div>

                <div class="attr-revoke-btn-container">
                <input type="hidden" name="metform-pro-settings-page-action" value="deactivate">
                <button type="submit" class="button button-secondary">Remove license from this domain</button> <span style="margin: 8px 0 0 20px; display: inline-block;">See documention <a target="_blank" href="https://help.wpmet.com/docs/how-to-revoke-product-license-key/">here</a>.</span>
                </div>
                <?php endif; ?>





                <?php wp_nonce_field( 'metform-pro-settings-page', 'metform-pro-settings-page' ); ?>
            </form>
        </div>
    </div>
</div>