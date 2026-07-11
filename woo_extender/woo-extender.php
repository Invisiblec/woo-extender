<?php

/**
 * Plugin Name: Woo Extender
 * Description: Extend WooCommerce functionality.
 * Version: 1.0.0
 * Author: Reza
 * Text Domain: woo-extender
 */

defined('ABSPATH') || exit;

define('WOO_EXTNDR_VERSION', '1.0.0');
define('WOO_EXTNDR_PATH', plugin_dir_path(__FILE__));
define('WOO_EXTNDR_URL', plugin_dir_url(__FILE__));

require_once WOO_EXTNDR_PATH . 'vendor/autoload.php';
require_once WOO_EXTNDR_PATH . 'includes/Helpers/functions.php';

use WooExtender\Core\Plugin;

register_activation_hook(
    __FILE__,
    ['WooExtender\Core\Activator', 'activate']
);

register_deactivation_hook(
    __FILE__,
    ['WooExtender\Core\Deactivator', 'deactivate']
);

$plugin = new Plugin();
$plugin->run();