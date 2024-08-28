<?php
/**
 * The plugin bootstrap file.
 * 
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 * 
 * @link                    https://smartystudio.net
 * @since                   1.0.0
 * @package                 Smarty_Upsell_Bundle_Manager
 * 
 * @wordpress-plugin
 * Plugin Name:             SM - Upsell Bundle Manager for WooCommerce
 * Plugin URI:              https://github.dev/smartystudio/smarty-upsell-bundle-manager
 * Description:             Designed to change the product variation design and add additional (bundle) products for single products in WooCommerce.
 * Version:                 1.0.0
 * Author:                  Smarty Studio | Martin Nestorov
 * Author URI:              https://smartystudio.net
 * License:                 GPL-2.0+
 * License URI:             http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:             smarty-upsell-bundle-manager
 * Domain Path:             /languages
 * WC requires at least:    3.5.0
 * WC tested up to:         9.0.2
 * Requires Plugins:		woocommerce
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

// Check if DCF_VERSION is not already defined
if (!defined('UBM_VERSION')) {
	/**
	 * Current plugin version.
	 * For the versioning of the plugin is used SemVer - https://semver.org
	 */
	define('UBM_VERSION', '1.0.0');
}

// Check if GFG_BASE_DIR is not already defined
if (!defined('UBM_BASE_DIR')) {
	/**
	 * This constant is used as a base path for including other files or referencing directories within the plugin.
	 */
    define('UBM_BASE_DIR', dirname(__FILE__));
}

if (!defined('CK_KEY')) {
    define('CK_KEY', '');
}

if (!defined('CS_KEY')) {
    define('CS_KEY', '');
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/classes/class-smarty-ubm-activator.php
 * 
 * @since    1.0.0
 */
function activate_ubm() {
	require_once plugin_dir_path(__FILE__) . 'includes/classes/class-smarty-ubm-activator.php';
	Smarty_Ubm_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/classes/class-smarty-ubm-deactivator.php
 * 
 * @since    1.0.0
 */
function deactivate_ubm() {
	require_once plugin_dir_path(__FILE__) . 'includes/classes/class-smarty-ubm-deactivator.php';
	Smarty_Ubm_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_ubm');
register_deactivation_hook(__FILE__, 'deactivate_ubm');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/classes/class-smarty-ubm-locator.php';

/**
 * The plugin functions file that is used to define general functions, shortcodes etc.
 */
require plugin_dir_path(__FILE__) . 'includes/functions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ubm_locator() {
	$plugin = new Smarty_Ubm_Locator();
	$plugin->run();
}

run_ubm_locator();