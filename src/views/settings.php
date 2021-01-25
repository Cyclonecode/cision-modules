<div class="wrap">
    <?php settings_errors(); ?>
    <h1><?php _e('Cision modules', self::TEXT_DOMAIN); ?></h1>
    <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
    <?php wp_nonce_field('cision-modules-action', 'cision-modules-nonce'); ?>
        <input type="hidden" name="action" value="cision_modules_save_settings" />
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="apiKey"><?php _e('Api Key', self::TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="apiKey" value="<?php echo $this->settings->get('apiKey'); ?>" />
                    <p class="description"><?php echo __('Ticker API Key.', self::TEXT_DOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="apiKey"><?php _e('Service Endpoint', self::TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="serviceEndpoint" value="<?php echo $this->settings->get('serviceEndpoint'); ?>" />
                    <p class="description"><?php echo __('Service endpoint.', self::TEXT_DOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="apiKey"><?php _e('Proxy', self::TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="checkbox" name="useProxyHandler"<?php checked($this->settings->get('useProxyHandler')); ?> />
                    <p class="description"><?php echo __('Use proxy handler.', self::TEXT_DOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="cacheTTL"><?php _e('Cache', self::TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="number" name="cacheTTL" value="<?php echo $this->settings->get('cacheTTL'); ?>" />
                    <p class="description"><?php echo __('Cache duration.', self::TEXT_DOMAIN); ?></p>
                </td>
            </tr>
        </table>
        <?php echo get_submit_button(__('Save settings', self::TEXT_DOMAIN), 'primary', 'cision-modules'); ?>
    </form>
</div>
