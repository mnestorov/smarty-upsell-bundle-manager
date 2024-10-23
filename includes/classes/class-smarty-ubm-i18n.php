<?php
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/mnestorov
 * @since      1.0.0
 *
 * @package    Smarty_Upsell_Bundle_Manager
 * @subpackage Smarty_Upsell_Bundle_Manager/includes/classes
 * @author     Smarty Studio | Martin Nestorov
 */
class Smarty_Gfg_I18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.7.0
	 */
	public function load_plugin_textdomain() {
        load_plugin_textdomain('smarty-upsell-bundle-manager', false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/');
    }
}