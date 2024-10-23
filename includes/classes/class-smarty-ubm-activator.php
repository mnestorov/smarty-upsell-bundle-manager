<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link       https://github.com/mnestorov
 * @since      1.0.0
 *
 * @package    Smarty_Upsell_Bundle_Manager
 * @subpackage Smarty_Upsell_Bundle_Manager/includes/classes
 * @author     Smarty Studio | Martin Nestorov
 */
class Smarty_Ubm_Activator {

	/**
	 * This function will be executed when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        if (!class_exists('WooCommerce')) {
            wp_die('This plugin requires WooCommerce to be installed and active.');
        }
    }
}