<?php

/**
 * Plugin Name: Cision Modules
 * Plugin URI: https://wordpress.org/plugins/cision-modules/
 * Description: Cision client modules.
 * Version: 1.0.0
 * Requires at least: 3.1.0
 * Requires PHP: 5.4
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

use CisionModules\Plugin;

add_action('plugins_loaded', function () {
    Plugin::getInstance();
});

register_activation_hook(__FILE__, array('CisionModules\Plugin', 'activate'));
register_deactivation_hook(__FILE__, array('CisionModules\Plugin', 'deActivate'));
register_uninstall_hook(__FILE__, array('CisionModules\Plugin', 'delete'));
