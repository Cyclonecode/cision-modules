<?php

namespace CisionModules;

use Cyclonecode\Plugin\Singleton;
use Cyclonecode\Plugin\Settings;

class Plugin extends Singleton
{
    const VERSION = '1.0.0';
    const SETTINGS_NAME = 'cision_modules';
    const TEXT_DOMAIN = 'cision-modules';
    const PARENT_MENU_SLUG = 'tools.php';
    const MENU_SLUG = 'cision-modules';

    /**
     *
     * @var Settings
     */
    private $settings;

    /**
     * @var string $capability
     */
    private $capability = 'manage_options';

    /**
     *
     */
    public function init()
    {
        // Allow people to change what capability is required to use this plugin.
        $this->capability = apply_filters('cision_modules_cap', $this->capability);

        $this->settings = new Settings(self::SETTINGS_NAME);

        $this->checkForUpgrade();
        $this->addActions();
        $this->addFilters();
        $this->localize();
    }

    /**
     * Localize plugin.
     */
    protected function localize()
    {
        load_plugin_textdomain(self::TEXT_DOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Add actions.
     */
    public function addActions()
    {
        if (is_admin()) {
            add_action('admin_menu', array($this, 'addMenu'));
            add_action('admin_post_cision_modules_save_settings', array($this, 'saveSettings'));
        } else {
            add_shortcode('cision-ticker', array($this, 'doTicker'));
            add_action('wp_enqueue_scripts', array($this, 'addFrontendScripts'));
        }
    }

    /**
     * Add filters.
     */
    public function addFilters()
    {
        if (is_admin()) {
            add_filter('admin_footer_text', array($this, 'adminFooter'));
            add_filter('plugin_action_links', array($this, 'addActionLinks'), 10, 2);
            add_filter('plugin_row_meta', array($this, 'filterPluginRowMeta'), 10, 4);
        }
    }

    /**
     * Display ticker.
     *
     * @param $args
     */
    public function doTicker($args)
    {
        if (!$this->settings->get('excludeCss')) {
            wp_enqueue_style('frontend');
        }
        $tickers = $this->getTickers();
        ob_start();
        ?>
        <div class="cision-ticker-wrapper">
            <div class="cision-ticker"<?php if(!$this->settings->get('excludeCss') && !$this->settings->get('noBackground')) : ?> style="background-color: <?php echo $this->settings->get('backgroundColor'); ?>"<?php endif; ?>>
                <ul>
                    <?php foreach ($tickers->Instruments as $key => $ticker) : ?>
                    <li>
                        <?php echo number_format(
                                $ticker->Quotes[0]->Price,
                                $this->settings->get('decimalPrecision'),
                                $this->settings->get('decimalSeparator'),
                                $this->settings->get('thousandSeparator')
                        ); ?> <?php echo $ticker->TradeCurrency; ?>
                        <span><?php echo $this->settings->get('label')[$key]; ?></span>
                    </li>
<!--                        --><?php //if ($this->settings->get('displayVolume')) : ?>
<!--                            <li>-->
<!--                                --><?php //echo $ticker->Quotes[0]->Quantity; ?>
<!--                                <span>--><?php //echo __('Volume', self::TEXT_DOMAIN); ?><!--</span>-->
<!--                            </li>-->
<!--                        --><?php //endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php
        ob_end_flush();
    }

    /**
     * Get tickers.
     *
     * @return mixed|null
     */
    protected function getTickers()
    {
        $data = get_transient('cision_modules_ticker');
        if (!$data) {
            $response = wp_safe_remote_request(trailingslashit($this->settings->get('serviceEndpoint')) . 'Ticker/' . $this->settings->get('apiKey'));
            if (!is_wp_error($response) && ($code = wp_remote_retrieve_response_code($response)) && $code === 200 || $code === 201) {
                $data = wp_remote_retrieve_body($response);
                set_transient('cision_modules_ticker', $data, $this->settings->get('cacheTTL'));
            } else {
                $data = null;
            }
        }
        return $data ? json_decode($data) : null;
    }

    /**
     * Add action link on plugins page.
     *
     * @param array $links
     * @param string $file
     *
     * @return mixed
     */
    public function addActionLinks($links, $file)
    {
        $settings_link = '<a href="' . admin_url(self::PARENT_MENU_SLUG . '?page=' . self::MENU_SLUG) . '">' .
            __('Settings', self::TEXT_DOMAIN) .
            '</a>';
        if ($file === 'cision-modules/bootstrap.php') {
            array_unshift($links, $settings_link);
            // array_unshift($links, '<a href="https://">' . __('Support', self::TEXT_DOMAIN) . '</a>');
            // array_unshift($links, '<a href="https://">' . __('Rate', self::TEXT_DOMAIN) . '</a>');
        }

        return $links;
    }
    /**
     * Filters the array of row meta for each plugin in the Plugins list table.
     *
     * @param string[] $plugin_meta An array of the plugin's metadata.
     * @param string   $plugin_file Path to the plugin file relative to the plugins directory.
     * @return string[] An array of the plugin's metadata.
     */
    public function filterPluginRowMeta(array $plugin_meta, $plugin_file)
    {
        if ($plugin_file !== 'cision-modules/bootstrap.php') {
            return $plugin_meta;
        }

//        $plugin_meta[] = sprintf(
//            '<a href="%1$s"><span class="dashicons dashicons-star-filled" aria-hidden="true" style="font-size:14px;line-height:1.3"></span>%2$s</a>',
//            'https://github.com/sponsors/cyclonecode',
//            esc_html_x('Sponsor', 'verb', 'cision-modules')
//        );

        return $plugin_meta;
    }

    public function addFrontendScripts($hook)
    {
        wp_register_style(
            'frontend',
            plugin_dir_url(__FILE__) . 'css/frontend.css',
            array(),
            self::VERSION
        );
    }

    /**
     * Add scripts.
     */
    public function addScripts($hook)
    {
        if ($hook !== 'tools_page_cision-modules') {
            return;
        }
    }

    /**
     * Check if any updates needs to be performed.
     */
    public function checkForUpgrade()
    {
        if (version_compare($this->settings->get('version'), self::VERSION, '<')) {
            $defaults = array(
              'decimalPrecision' => 2,
              'decimalSeparator' => '.',
              'thousandSeparator' => '.',
              // 'dateFormatOptions' => 'YY-MM-DD HH:ii:ss',
              'cacheTTL' => 300,
              'serviceEndpoint' => 'https://publish.ne.cision.com/papi/',
              'excludeCss' => false,
              'backgroundColor' => '#ffffff',
              'noBackground' => fasle,
              'labelBaseStock' => __('stamaktie', self::TEXT_DOMAIN),
              'labelPreferenceStock' => __('preferensaktie', self::TEXT_DOMAIN),
              'label' => array(),
              'displayVolume' => false
            );

            // Set defaults.
            foreach ($defaults as $key => $value) {
                $this->settings->add($key, $value);
            }
            $this->settings->set('version', self::VERSION);
            $this->settings->save();
        }
    }

    /**
     * Triggered when plugin is activated.
     */
    public static function activate()
    {
    }

    /**
     * Triggered when plugin is deactivated.
     */
    public static function deActivate()
    {
    }

    /**
     * Uninstalls the plugin.
     */
    public static function delete()
    {
        delete_option(self::SETTINGS_NAME);
        delete_transient('cision_modules_ticker');
        delete_transient('cision_modules_settings_errors');
    }

    /**
     * Adds customized text to footer in admin dashboard.
     *
     * @param string $footer_text
     *
     * @return string
     */
    public function adminFooter($footer_text)
    {
        $screen = get_current_screen();
        if ($screen->id === 'tools_page_cision-modules') {
            $rate_text = sprintf(
                __('Thank you for using <a href="%1$s" target="_blank">Cision modules</a>! Please <a href="%2$s" target="_blank">rate us on WordPress.org</a>', self::TEXT_DOMAIN),
                'https://wordpress.org/plugins/cision-modules',
                'https://wordpress.org/support/plugin/cision-modules/reviews/?rate=5#new-post'
            );

            return '<span>' . $rate_text . '</span>';
        } else {
            return $footer_text;
        }
    }

    /**
     * Add menu item for plugin.
     */
    public function addMenu()
    {
        add_submenu_page(
            self::PARENT_MENU_SLUG,
            __('Cision modules', self::TEXT_DOMAIN),
            __('Cision modules', self::TEXT_DOMAIN),
            $this->capability,
            self::MENU_SLUG,
            array($this, 'doSettingsPage')
        );
    }

    /**
     * Add message to be displayed in settings form.
     *
     * @param string $message
     * @param string $type
     */
    protected function addSettingsMessage($message, $type = 'error')
    {
        add_settings_error(
            'cision-modules',
            esc_attr('cision-modules-updated'),
            $message,
            $type
        );
    }

    /**
     * Handle form data for configuration page.
     */
    public function saveSettings()
    {
        // Check if settings form is submitted.
        if (filter_input(INPUT_POST, 'cision-modules', FILTER_SANITIZE_STRING)) {
            // Validate so user has correct privileges.
            if (!current_user_can($this->capability)) {
                die(__('You are not allowed to perform this action.', self::TEXT_DOMAIN));
            }
            // Verify nonce and referer.
            if (check_admin_referer('cision-modules-action', 'cision-modules-nonce')) {
                // Filter and sanitize form values.
                $this->settings->apiKey = filter_input(INPUT_POST, 'apiKey', FILTER_SANITIZE_STRING);
                $this->settings->serviceEndpoint = filter_input(INPUT_POST, 'serviceEndpoint', FILTER_SANITIZE_URL);
                $this->settings->cacheTTL = filter_input(
                    INPUT_POST,
                    'cacheTTL',
                    FILTER_VALIDATE_INT,
                    array(
                        'options' => array(
                            'min_range' => 1,
                            'default' => 300,
                        )
                    )
                );
                $this->settings->dateFormatOptions = filter_input(
                    INPUT_POST,
                    'dateFormatOptions',
                    FILTER_VALIDATE_REGEXP,
                    array(
                            'options' => array(
                                'regex' => '//',
                            )
                    )
                );
                $this->settings->decimalSeparator = filter_input(
                    INPUT_POST,
                    'decimalSeparator',
                    FILTER_SANITIZE_STRING
                );
                $this->settings->thousandSeparator = filter_input(
                    INPUT_POST,
                    'thousandSeparator',
                    FILTER_SANITIZE_STRING
                );
                $this->settings->decimalPrecision = filter_input(
                    INPUT_POST,
                    'decimalPrecision',
                    FILTER_VALIDATE_INT,
                    array(
                            'options' => array(
                                    'min_range' => 0,
                                    'default' => 0,
                            ),
                    )
                );
                $this->settings->excludeCss = filter_input(
                    INPUT_POST,
                    'excludeCss',
                    FILTER_VALIDATE_BOOLEAN
                );
                $this->settings->noBackground = filter_input(
                        INPUT_POST,
                    'noBackground',
                    FILTER_VALIDATE_BOOLEAN
                );
                $this->settings->backgroundColor = filter_input(
                    INPUT_POST,
                    'backgroundColor',
                    FILTER_VALIDATE_REGEXP,
                    array(
                            'options' => array(
                                    'regexp' => '/\#[a-fA-F0-9]{6}/',
                            )
                    )
                );
                // var_dump($_POST['label']);exit;
                $this->settings->label = filter_input(
                    INPUT_POST,
                    'label',
                    FILTER_SANITIZE_STRING,
                    FILTER_REQUIRE_ARRAY
                );
                $this->settings->displayVolume = filter_input(
                        INPUT_POST,
                    'displayVolume',
                    FILTER_VALIDATE_BOOLEAN
                );
                delete_transient('cision_modules_ticker');
                $this->settings->save();

                wp_safe_redirect(admin_url(self::PARENT_MENU_SLUG . '?page=' . self::MENU_SLUG));
            }
        }
    }

    /**
     * Display the settings page.
     */
    public function doSettingsPage()
    {
        // Display any settings messages
        $setting_errors = get_transient('cision_modules_settings_errors');
        if ($setting_errors) {
            foreach ($setting_errors as $error) {
                $this->addSettingsMessage($error['message'], $error['type']);
            }
            delete_transient('cision_modules_settings_errors');
        }
        $template = __DIR__ . '/views/settings.php';
        if (file_exists($template)) {
            require_once $template;
        }
    }
}
