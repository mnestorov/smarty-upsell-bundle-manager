<?php
/**
 * The core plugin class.
 *
 * This is used to define attributes, functions, internationalization used across
 * both the admin-specific hooks, and public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @link       https://github.com/mnestorov
 * @since      1.0.0
 *
 * @package    Smarty_Upsell_Bundle_Manager
 * @subpackage Smarty_Upsell_Bundle_Manager/includes/classes
 * @author     Smarty Studio | Martin Nestorov
 */
class Smarty_Ubm_Locator {

	/**
	 * The loader that's responsible for maintaining and registering all hooks
	 * that power the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Smarty_Ubm_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if (defined('UBM_VERSION')) {
			$this->version = UBM_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		$this->plugin_name = 'smarty_upsell_bundle_manager';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Smarty_Ubm_Loader. Orchestrates the hooks of the plugin.
	 * - Smarty_Ubm_i18n. Defines internationalization functionality.
	 * - Smarty_Ubm_Admin. Defines all hooks for the admin area.
	 * - Smarty_Ubm_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'classes/class-smarty-ubm-loader.php';

		/**
		 * The class responsible for defining internationalization functionality of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'classes/class-smarty-ubm-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . '../admin/class-smarty-ubm-admin.php';

		/**
		 * The class responsible for Activity & Logging functionality in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . '../admin/tabs/class-smarty-ubm-activity-logging.php';

		/**
		 * The class responsible for License functionality in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . '../admin/tabs/class-smarty-ubm-license.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . '../public/class-smarty-ubm-public.php';

		// Run the loader
		$this->loader = new Smarty_Ubm_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Smarty_Ubm_I18n class in order to set the domain and to
	 * register the hook with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Smarty_Ubm_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Smarty_Ubm_Admin($this->get_plugin_name(), $this->get_version());
		
		$plugin_styling = new Smarty_Ubm_Styling();
		$plugin_activity_logging = new Smarty_Ubm_Activity_Logging();
		$plugin_license = new Smarty_Ubm_License();

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_menu', $plugin_admin, 'ubm_add_settings_page');
		$this->loader->add_action('admin_init', $plugin_admin, 'ubm_settings_init');
		$this->loader->add_action('woocommerce_attribute_updated', $plugin_admin, 'ubm_woocommerce_attribute_updated', 10, 3);
		$this->loader->add_action('woocommerce_after_edit_attribute_fields', $plugin_admin, 'ubm_after_edit_attribute_fields', 10, 0);
		$this->loader->add_action('woocommerce_product_after_variable_attributes', $plugin_admin, 'ubm_add_custom_fields_to_variations', 10, 3);
    	$this->loader->add_action('woocommerce_save_product_variation', $plugin_admin, 'ubm_save_custom_fields_variations', 10, 2);
		$this->loader->add_action('woocommerce_order_item_meta_end', $plugin_admin, 'ubm_display_additional_products_order_meta', 10, 3);
    	$this->loader->add_action('woocommerce_after_order_itemmeta', $plugin_admin, 'ubm_display_additional_products_order_meta', 10, 3);
		$this->loader->add_filter('manage_edit-shop_order_columns', $plugin_admin, 'ubm_add_order_list_column');
    	$this->loader->add_action('manage_shop_order_posts_custom_column', $plugin_admin, 'ubm_add_order_list_column_content', 10, 2);

		// Register hooks for Styling
		$this->loader->add_action('admin_init', $plugin_styling, 'ubm_s_settings_init');

		// Register hooks for Activity & Logging
		$this->loader->add_action('admin_init', $plugin_activity_logging, 'ubm_al_settings_init');
        $this->loader->add_action('wp_ajax_smarty_ubm_clear_logs', $plugin_activity_logging, 'ubm_handle_ajax_clear_logs');

		// Register hooks for License management
		$this->loader->add_action('admin_init', $plugin_license, 'ubm_l_settings_init');
		$this->loader->add_action('updated_option', $plugin_license, 'ubm_handle_license_status_check', 10, 3);
		$this->loader->add_action('admin_notices', $plugin_license, 'ubm_license_notice');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Smarty_Ubm_Public($this->get_plugin_name(), $this->get_version());
		
		$this->loader->add_filter('woocommerce_variable_sale_price_html', $plugin_public, 'ubm_variable_price_range', 10, 2);
    	$this->loader->add_filter('woocommerce_variable_price_html', $plugin_public, 'ubm_variable_price_range', 10, 2);
		$this->loader->add_filter('woocommerce_available_variation', $plugin_public, 'ubm_woocommerce_available_variation', 10, 3);
		$this->loader->add_action('woocommerce_before_single_variation', $plugin_public, 'ubm_add_additional_products_checkbox', 5);
		$this->loader->add_action('wp_ajax_smarty_ubm_choose_additional_products', $plugin_public, 'ubm_handle_additional_products_cart');
    	$this->loader->add_action('wp_ajax_nopriv_smarty_ubm_choose_additional_products', $plugin_public, 'ubm_handle_additional_products_cart');
		$this->loader->add_filter('woocommerce_get_cart_item_from_session', $plugin_public, 'ubm_get_cart_item_from_session', 10, 2);
		$this->loader->add_filter('woocommerce_add_cart_item_data', $plugin_public, 'ubm_add_cart_item_data', 10, 3);
		$this->loader->add_filter('woocommerce_get_item_data', $plugin_public, 'ubm_display_additional_products_in_cart', 10, 2);
		$this->loader->add_action('wp_ajax_woocommerce_update_cart_action', $plugin_public, 'ubm_calculate_cart_item_price');
		$this->loader->add_action('wp_ajax_nopriv_woocommerce_update_cart_action', $plugin_public, 'ubm_calculate_cart_item_price');
		$this->loader->add_action('wp_ajax_woocommerce_add_to_cart', $plugin_public, 'ubm_calculate_cart_item_price');
		$this->loader->add_action('wp_ajax_nopriv_woocommerce_add_to_cart', $plugin_public, 'ubm_calculate_cart_item_price');
		$this->loader->add_action('woocommerce_before_calculate_totals', $plugin_public, 'ubm_calculate_cart_item_price', 10, 1);
		$this->loader->add_action('woocommerce_before_calculate_totals', $plugin_public, 'ubm_additional_product_recalculate_price', 10, 1);
		$this->loader->add_action('woocommerce_add_order_item_meta', $plugin_public, 'ubm_add_order_item_meta', 10, 3);
		$this->loader->add_filter('woocommerce_hidden_order_itemmeta', $plugin_public, 'ubm_hide_additional_product_skus');
		$this->loader->add_action('wp_head', $plugin_public, 'ubm_public_custom_css');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Smarty_Ubm_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}