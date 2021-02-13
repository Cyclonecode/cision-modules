<?php
$tickers = $this->getTickers();
$labels = $this->settings->get('label');
if (!is_array($labels) || ($tickers && count($labels) !== count($tickers->Instruments))) {
    foreach ($tickers->Instruments as $key => $ticker) {
        $labels[] = $ticker->TickerSymbol;
    }
}
?>
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
                    <label for="serviceEndpoint"><?php _e('Service Endpoint', self::TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="serviceEndpoint" value="<?php echo $this->settings->get('serviceEndpoint'); ?>" />
                    <p class="description"><?php echo __('Service endpoint.', self::TEXT_DOMAIN); ?></p>
                </td>
            </tr>
            <!--
            <tr>
                <th scope="row">
                    <label for="useProxyHandler"><?php _e('Proxy', self::TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="checkbox" name="useProxyHandler"<?php checked($this->settings->get('useProxyHandler')); ?> />
                    <p class="description"><?php echo __('Use proxy handler.', self::TEXT_DOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="dateFormatOptions"><?php _e('Date and time', self::TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="dateFormatOptions" value="<?php echo $this->settings->get('dateFormatOptions'); ?>" />
                    <p class="description"><?php echo __('Date and time options.', self::TEXT_DOMAIN); ?></p>
                </td>
            </tr>
            -->
            <tr>
                <th scope="row">
                    <label for="thousandSeparator"><?php _e('Thousand separator', self::TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="thousandSeparator" value="<?php echo $this->settings->get('thousandSeparator'); ?>" />
                    <p class="description"><?php echo __('Separator for thousands.', self::TEXT_DOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="decimalSeparator"><?php _e('Decimal separator', self::TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="decimalSeparator" value="<?php echo $this->settings->get('decimalSeparator'); ?>" />
                    <p class="description"><?php echo __('Separator for decimals.', self::TEXT_DOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="decimalPrecision"><?php _e('Decimal precision', self::TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="number" class="regular-text" name="decimalPrecision" value="<?php echo $this->settings->get('decimalPrecision'); ?>" />
                    <p class="description"><?php echo __('Precision for decimals.', self::TEXT_DOMAIN); ?></p>
                </td>
            </tr>
            <!--
            <tr>
                <th scope="row">
                    <label for="displayVolume"><?php _e('Volume', self::TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="checkbox" name="displayVolume"<?php checked($this->settings->get('displayVolume')); ?> />
                    <p class="description"><?php echo __('Display volume.', self::TEXT_DOMAIN); ?></p>
                </td>
            </tr>
            -->
            <tr>
                <th scope="row">
                    <label for="noBackground"><?php _e('Transparent', self::TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="checkbox" name="noBackground"<?php checked($this->settings->get('noBackground')); ?> />
                    <p class="description"><?php echo __('Do not use any background color.', self::TEXT_DOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="backgroundColor"><?php _e('Background color', self::TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="color" name="backgroundColor" value="<?php echo($this->settings->get('backgroundColor')); ?>" />
                    <p class="description"><?php echo __('Background color for ticker.', self::TEXT_DOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="excludeCss"><?php _e('Exclude CSS', self::TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="checkbox" name="excludeCss"<?php checked($this->settings->get('excludeCss')); ?> />
                    <p class="description"><?php echo __('Do not load stylesheet.', self::TEXT_DOMAIN); ?></p>
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
        <?php if ($tickers) : ?>
            <table class="form-table">
                <tr>
                    <th scope="col"><?php echo __('Name of ticker', self::TEXT_DOMAIN); ?></th>
                    <th scope="col"><?php echo __('Enabled', self::TEXT_DOMAIN); ?></th>
                    <th scope="col"><?php echo __('Label', self::TEXT_DOMAIN); ?></th>
                </tr>
                <?php foreach ($tickers->Instruments as $key => $ticker) : ?>
                    <tr>
                        <th width="35%" scope="row"><?php echo $ticker->TickerName; ?></th>
                        <td width="10%" style="padding:0"><input type="hidden" name="enable[<?php echo $key; ?>]" id="enable_hidden_<?php echo $key; ?>" value="0" /><input type="checkbox" name="enable[<?php echo $key; ?>]"<?php checked($this->settings->getFromArray('enable', $key)); ?>/></td>
                        <td width="55%" style="padding:0"><input type="text" class="regular-text" name="label[]" value="<?php echo $labels[$key] ?>" /></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
        <?php echo get_submit_button(__('Save settings', self::TEXT_DOMAIN), 'primary', 'cision-modules'); ?>
    </form>
</div>
