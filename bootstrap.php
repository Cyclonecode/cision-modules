<?php

/**
 * Plugin Name: Cision Modules
 * Plugin URI: https://wordpress.org/plugins/cision-modules/
 * Description: Cision client modules.
 * Version: 1.0.1
 * Requires at least: 4.0.0
 * Requires PHP: 7.1
 * Author: Cyclonecode
 * Author URI: https://stackoverflow.com/users/1047662/cyclonecode?tab=profile
 * Copyright: Cyclonecode
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cision-modules
 * Domain Path: /languages
 *
 * @package cision-modules
 * @author cision-modules
 */

namespace CisionModules;

require_once __DIR__ . '/vendor/autoload.php';

add_action('plugins_loaded', function () {
    Plugin::getInstance();
});

register_activation_hook(__FILE__, array('CisionModules\Plugin', 'activate'));
register_deactivation_hook(__FILE__, array('CisionModules\Plugin', 'deActivate'));
register_uninstall_hook(__FILE__, array('CisionModules\Plugin', 'delete'));
