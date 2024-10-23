<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks for how to enqueue 
 * the admin-specific stylesheet (CSS) and JavaScript code.
 *
 * @link       https://github.com/mnestorov
 * @since      1.0.0
 *
 * @package    Smarty_Upsell_Bundle_Manager
 * @subpackage Smarty_Upsell_Bundle_Manager/admin
 * @author     Smarty Studio | Martin Nestorov
 */
class Smarty_Ubm_Admin {
    /**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name     The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version         The current version of this plugin.
	 */
	private $version;

	/**
	 * Instance of Smarty_Ubm_Styling.
	 * 
	 * @since    1.0.0
	 * @access   private
	 */
	private $styling;

	/**
	 * Instance of Smarty_Ubm_Activity_Logging.
	 * 
	 * @since    1.0.0
	 * @access   private
	 */
	private $activity_logging;

	/**
	 * Instance of Smarty_Ubm_License.
	 * 
	 * @since    1.0.0
	 * @access   private
	 */
	private $license;
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name     The name of this plugin.
	 * @param    string    $version         The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// Include and instantiate the Styling class
		$this->styling = new Smarty_Ubm_Styling();

		// Include and instantiate the Activity Logging class
		$this->activity_logging = new Smarty_Ubm_Activity_Logging();

		// Include and instantiate the License class
		$this->license = new Smarty_Ubm_License();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/**
		 * This function enqueues custom CSS for the plugin settings in WordPress admin.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smarty_Ubm_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smarty_Ubm_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css', array(), '4.0.13');
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/smarty-ubm-admin.css', array(), $this->version, 'all');
		wp_enqueue_style('dashicons');
		wp_enqueue_style('wp-color-picker');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function enqueues custom JavaScript for the plugin settings in WordPress admin.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smarty_Ubm_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smarty_Ubm_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js', array('jquery'), '4.0.13', true);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/smarty-ubm-admin.js', array('jquery', 'select2'), $this->version, true);
		wp_enqueue_script('wp-color-picker');
	}

	/**
	 * Add settings page to the WordPress admin menu.
	 * 
	 * @since    1.0.0
	 */
	public function ubm_add_settings_page() {
		add_submenu_page(
			'woocommerce',
			__('Upsell Bundle Manager | Settings', 'smarty-upsell-bundle-manager'), // Page title
			__('Upsell Bundle Manager', 'smarty-upsell-bundle-manager'), 			// Menu title                   
			'manage_options',                           							// Capability required to access this page
			'smarty-ubm-settings',           										// Menu slug
			array($this, 'ubm_display_settings_page')  								// Callback function to display the page content
		);
	}

	/**
	 * @since    1.0.0
	 */
	private function ubm_get_settings_tabs() {
		return array(
			'general' 				 => __('General', 'smarty-upsell-bundle-manager'),
			'styling'  	 			 => __('Styling', 'smarty-upsell-bundle-manager'),
			'activity-logging'  	 => __('Activity & Logging', 'smarty-upsell-bundle-manager'),
			'license' 				 => __('License', 'smarty-upsell-bundle-manager')
		);
	}

	/**
	 * Outputs the HTML for the settings page.
	 * 
	 * @since    1.0.0
	 */
	public function ubm_display_settings_page() {
		// Check user capabilities
		if (!current_user_can('manage_options')) {
			return;
		}

		$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
		$tabs = $this->ubm_get_settings_tabs();
	
		// Define the path to the external file
		$partial_file = plugin_dir_path(__FILE__) . 'partials/smarty-ubm-admin-display.php';

		if (file_exists($partial_file) && is_readable($partial_file)) {
			include_once $partial_file;
		} else {
			_ubm_write_logs("Unable to include: '$partial_file'");
		}
	}

	/**
	 * Initializes the plugin settings by registering the settings, sections, and fields.
	 *
	 * @since    1.0.0
	 */
	public function ubm_settings_init() {
		// General Settings
		register_setting('smarty_ubm_options_general', 'smarty_ubm_enable_additional_products');
		register_setting('smarty_ubm_options_general', 'smarty_ubm_choose_additional_products');
		register_setting('smarty_ubm_options_general', 'smarty_ubm_choose_where_to_show');
		register_setting('smarty_ubm_options_general', 'smarty_ubm_currency_symbol_position');
        register_setting('smarty_ubm_options_general', 'smarty_ubm_currency_symbol_spacing');
		register_setting('smarty_ubm_options_general', 'smarty_ubm_display_savings');
		register_setting('smarty_ubm_options_general', 'smarty_ubm_debug_mode');
		register_setting('smarty_ubm_options_general', 'smarty_ubm_debug_notices_enabled');

		// Styling
		$this->styling->ubm_s_settings_init();

		// Activity & Logging settings
		$this->activity_logging->ubm_al_settings_init();

		// License settings
		$this->license->ubm_l_settings_init();

		add_settings_section(
			'smarty_ubm_section_general',                                   	// ID of the section
			__('General', 'smarty-upsell-bundle-manager'),                  	// Title of the section
			array($this,'ubm_section_general_cb'),                          	// Callback function that fills the section with the desired content
			'smarty_ubm_options_general'                                		// Page on which to add the section
		);

		add_settings_section(
			'smarty_ubm_section_additional_products',                           // ID of the section
			__('Additional (Bundle) products', 'smarty-upsell-bundle-manager'), // Title of the section
			array($this,'ubm_section_additional_products_cb'),                  // Callback function that fills the section with the desired content
			'smarty_ubm_options_general'                                		// Page on which to add the section
		);

		add_settings_section(
			'smarty_ubm_section_currency_symbol',                           	// ID of the section
			__('Currency Symbol Position', 'smarty-upsell-bundle-manager'), 	// Title of the section
			array($this,'ubm_section_currency_symbol_cb'),                  	// Callback function that fills the section with the desired content
			'smarty_ubm_options_general'                                		// Page on which to add the section
		);

		add_settings_section(
			'smarty_ubm_section_display_options',                           	// ID of the section
			__('Display Options', 'smarty-upsell-bundle-manager'), 				// Title of the section
			array($this,'ubm_section_display_options_cb'),                  	// Callback function that fills the section with the desired content
			'smarty_ubm_options_general'                                		// Page on which to add the section
		);
	
		add_settings_section(
			'smarty_ubm_section_debug_options',                               	// ID of the section
			__('Debug Options', 'smarty-upsell-bundle-manager'),           		// Title of the section
			array($this,'ubm_section_debug_options_cb'),                      	// Callback function that fills the section with the desired content
			'smarty_ubm_options_general'                                		// Page on which to add the section
		);
	
		add_settings_field(
			'smarty_ubm_enable_additional_products',                            		// ID of the field
			__('Enable/Disable Additional Products', 'smarty-upsell-bundle-manager'),   // Title of the field
			array($this,'ubm_checkbox_field_cb'),                        				// Callback function to display the field
			'smarty_ubm_options_general',                               				// Page on which to add the field
			'smarty_ubm_section_additional_products'                             		// Section to which this field belongs
		);
	
		add_settings_field(
			'smarty_ubm_choose_additional_products',                            		// ID of the field
			__('Choose Additional (Bundle) Products', 'smarty-upsell-bundle-manager'),	// Title of the field
			array($this,'ubm_choose_additional_products_cb'),                   		// Callback function to display the field
			'smarty_ubm_options_general',                               				// Page on which to add the field
			'smarty_ubm_section_additional_products'                            		// Section to which this field belongs
		);
	
		add_settings_field(
			'smarty_ubm_choose_where_to_show',                            				// ID of the field
			__('Choose Where to Show', 'smarty-upsell-bundle-manager'),         		// Title of the field
			array($this,'ubm_choose_where_to_show_cb'),                       			// Callback function to display the field
			'smarty_ubm_options_general',                               				// Page on which to add the field
			'smarty_ubm_section_additional_products'                                	// Section to which this field belongs
		);

		add_settings_field(
			'smarty_ubm_currency_symbol_position', 										// ID of the field
			__('Position', 'smarty-upsell-bundle-manager'),         					// Title of the field
			array($this, 'ubm_currency_position_field_cb'), 							// Callback function to display the field
			'smarty_ubm_options_general',                               				// Page on which to add the field
			'smarty_ubm_section_currency_symbol'                                		// Section to which this field belongs
		);

        add_settings_field(
			'smarty_ubm_currency_symbol_spacing', 										// ID of the field
			__('Spacing', 'smarty-upsell-bundle-manager'),         						// Title of the field 
			array($this, 'ubm_currency_spacing_field_cb'), 								// Callback function to display the field
			'smarty_ubm_options_general',                               				// Page on which to add the field
			'smarty_ubm_section_currency_symbol'                                		// Section to which this field belongs
		);

		add_settings_field(
			'smarty_ubm_display_savings_text',                            				// ID of the field
			__('Turn On/Off savings text', 'smarty-upsell-bundle-manager'),         	// Title of the field
			array($this,'ubm_checkbox_field_cb'),                       				// Callback function to display the field
			'smarty_ubm_options_general',                               				// Page on which to add the field
			'smarty_ubm_section_additional_products'                                	// Section to which this field belongs
		);

		add_settings_field(
			'smarty_ubm_debug_mode',                                           			// ID of the field
			__('Debug Mode', 'smarty-upsell-bundle-manager'),              				// Title of the field
			array($this,'ubm_checkbox_field_cb'),                                  		// Callback function to display the field
			'smarty_ubm_options_general',                               				// Page on which to add the field
			'smarty_ubm_section_debug_options'                                   		// Section to which this field belongs
		);
	
		add_settings_field(
			'smarty_ubm_debug_notices',                                        			// ID of the field
			__('Enable Debug Notices', 'smarty-upsell-bundle-manager'),   				// Title of the field
			array($this,'ubm_checkbox_field_cb'),                               		// Callback function to display the field
			'smarty_ubm_options_general',                               				// Page on which to add the field
			'smarty_ubm_section_debug_options'                                   		// Section to which this field belongs
		);
	}
	
	/**
     * Function to sanitize the plugin settings on save.
     * 
     * @since    1.0.0
     */
	public function gfg_sanitize_checkbox($input) {
		return $input == 1 ? 1 : 0;
	}

	/**
     * Function to sanitize number fields on save.
     * 
     * @since    1.0.0
     */
	public function gfg_sanitize_number_field($input) {
		return absint($input);
	}

	/**
     * Callback function for the General section.
     * 
     * @since    1.0.0
     */
	public function ubm_section_general_cb() {
		echo '<p>' . __('General settings for the Upsell Bundle Manager.', 'smarty-upsell-bundle-manager') . '</p>';
	}

	/**
     * Callback function for the Convert Images section.
     * 
     * @since    1.0.0
     */
	public function ubm_section_additional_products_cb() {
		echo '<p>' . __('Enable or disable additional (bundle) products feature for single or variable products or/and choose your additional (bundle) products.', 'smarty-upsell-bundle-manager') . '</p>';
	}

	/**
     * Callback function for the Currency section.
     * 
     * @since    1.0.0
     */
	public function ubm_section_currency_symbol_cb() {
        echo '<p>' . __('Customize the currency symbol position and spacing for your WooCommerce upsell products.', 'smarty-upsell-bundle-manager') . '</p>';
    }

	/**
     * Callback function for the Display Options section.
     * 
     * @since    1.0.0
     */
	public function ubm_section_display_options_cb() {
		echo '<p>' . __('Display options for the plugin.', 'smarty-upsell-bundle-manager') . '</p>';
	}
	
	/**
     * Callback function for the Generate Feeds section.
     * 
     * @since    1.0.0
     */
	public function ubm_section_debug_options_cb() {
		echo '<p>' . __('Adjust debug settings for the plugin.', 'smarty-upsell-bundle-manager') . '</p>';
	}

	/**
     * Callback function for the additional products select2 field.
     * 
     * @since    1.0.0
     */
	public function ubm_choose_additional_products_cb() {
		$upsell_products = get_option('smarty_ubm_choose_additional_products', []);
		
		// Ensure $additional_products is always an array
		$additional_products = is_array($upsell_products) ? $upsell_products : [];
		$products = wc_get_products(array('limit' => -1)); // Get all products
	
		echo '<select name="smarty_ubm_choose_additional_products[]" multiple="multiple" id="smarty_ubm_choose_additional_products" style="width: 100%;">';
		foreach ($products as $product) {
			$selected = in_array($product->get_id(), $additional_products) ? 'selected' : '';
			echo '<option value="' . esc_attr($product->get_id()) . '" ' . esc_attr($selected) . '>' . esc_html($product->get_name()) . '</option>';
		}
		echo '</select>'; ?>
	
		<script>
			jQuery(document).ready(function($) {
				$('#smarty_ubm_choose_additional_products').select2({
					placeholder: "Select additional products",
					allowClear: true
				});
			});
		</script><?php
	}
	
	/**
     * Callback function for the where to show additional products select2 field.
     * 
     * @since    1.0.0
     */
	public function ubm_choose_where_to_show_cb() {
		$bundle_products = get_option('ubm_choose_where_to_show', []);
		$products_to_show = is_array($bundle_products) ? $bundle_products : [];
		$products = wc_get_products(array('limit' => -1)); // Get all products
	
		echo '<select name="smarty_ubm_choose_where_to_show[]" multiple="multiple" id="smarty_ubm_choose_where_to_show" style="width: 100%;">';
		foreach ($products as $product) {
			$selected = in_array($product->get_id(), $products_to_show) ? 'selected' : '';
			echo '<option value="' . esc_attr($product->get_id()) . '" ' . esc_attr($selected) . '>' . esc_html($product->get_name()) . '</option>';
		}
		echo '</select>'; ?>
	
		<script>
			jQuery(document).ready(function($) {
				$('#smarty_ubm_choose_where_to_show').select2({
					placeholder: "Select products where the bundleds will be displayed",
					allowClear: true
				});
			});
		</script><?php
	}

	/**
     * Callback function for the checkboxes.
     * 
     * @since    1.0.0
     */
	public function ubm_checkbox_field_cb($args) {
		$option = get_option($args['id'], '0'); // Default to unchecked
		$checked = checked(1, $option, false);
		echo "<label class='smarty-toggle-switch'>";
		echo "<input type='checkbox' id='{$args['id']}' name='{$args['id']}' value='1' {$checked} />";
		echo "<span class='smarty-slider round'></span>";
		echo "</label>";
		
		// Display the description only for the debug mode checkbox
		if ($args['id'] == 'smarty_ubm_debug_mode') {
			echo '<p class="description">' . __('Copies specific template files from a plugin directory to a child theme directory in WordPress. <br><em><b>Important:</b> <span class="smarty-text-danger">Turn this to Off in production.</span></em>', 'smarty-custom-upsell-products-design') . '</p>';
		}
	}

	/**
     * Callback function for the currency position field.
     * 
     * @since    1.0.0
     */
	public function ubm_currency_position_field_cb($args) {
		$option = get_option($args['id'], 'left');
		echo '<select name="' . $args['id'] . '">';
		echo '<option value="left"' . selected($option, 'left', false) . '>' . __('Left', 'smarty-upsell-bundle-manager') . '</option>';
		echo '<option value="right"' . selected($option, 'right', false) . '>' . __('Right', 'smarty-upsell-bundle-manager') . '</option>';
		echo '</select>';
	}
	
	/**
     * Callback function for the currency spacing field.
     * 
     * @since    1.0.0
     */
	public function ubm_currency_spacing_field_cb($args) {
		$option = get_option($args['id'], 'no_space');
		echo '<select name="' . $args['id'] . '">';
		echo '<option value="space"' . selected($option, 'space', false) . '>' . __('With Space', 'smarty-upsell-bundle-manager') . '</option>';
		echo '<option value="no_space"' . selected($option, 'no_space', false) . '>' . __('Without Space', 'smarty-upsell-bundle-manager') . '</option>';
		echo '</select>';
	}

	/**
	 * Handles saving of the custom attribute fields on update.
	 * 
	 * @since    1.0.0
	 * @param int $attribute_id ID of the attribute being updated.
	 * @param array $attribute Array of new attribute data.
	 * @param string $old_attribute_name Old name of the attribute.
	 * @return void Saves the custom field data but does not return a value.
	 */	
	public function ubm_woocommerce_attribute_updated($attribute_id, $attribute, $old_attribute_name) {
		if (isset($_POST['ubm_variation_design']) && $_POST['ubm_variation_design'] == 1) {
			update_option('ubm_variation_design_'. $attribute_id, 1);
		} else {
			update_option('ubm_variation_design_'. $attribute_id, 0);
		}
	}
		
	/**
	 * Retrieves the custom attribute fields.
	 * 
	 * @since    1.0.0
	 * @param int $attr_id ID of the attribute.
	 * @return mixed Value of the 'ubm_variation_design' option for the attribute or false if not set.
	 */
	public function ubm_get_attr_fields($attr_id) {
		$attr_variation = get_option('ubm_variation_design_'. $attr_id, false);
		return $attr_variation;
	}

	/**
     * Adds custom fields to the WooCommerce attribute edit form.
     *
	 * @since    1.0.0
     * @return void Echoes HTML output for the custom field.
     */
    public function ubm_after_edit_attribute_fields() {
        $attr_id = isset($_GET['edit']) && $_GET['page'] === 'product_attributes' ? (int) $_GET['edit'] : false;
        $attr_variation = $this->ubm_get_attr_fields($attr_id);

        // Sanitization is crucial here for security
        $checked = checked('1', $attr_variation, false);

        // Escaping for output
        echo '<tr class="form-field">';
        echo '    <th valign="top" scope="row">';
        echo '        <label for="up_sell_design">' . esc_html__('Custom up-sell design', 'smarty-upsell-bundle-manager') . '</label>';
        echo '    </th>';
        echo '    <td>';
        echo '        <input name="up_sell_design" id="up_sell_design" type="checkbox" value="1" ' . esc_attr($checked) . ' />';
        echo '		  <p class="description">' . esc_html__('Turn the custom up-sell design on or off for attributes.', 'smarty-upsell-bundle-manager') . '</p>';
        echo '    </td>';
        echo '</tr>';
    }

	/**
     * This function adds two custom text input fields to WooCommerce 
     * product variation forms in the admin panel. 
	 * 
	 * @since    1.0.0
     */
    public function ubm_add_custom_fields_to_variations($loop, $variation_data, $variation) {
        // Custom field for Label 1
        woocommerce_wp_text_input(array(
            'id' 			=> 'smarty_ubm_label_1[' . $variation->ID . ']', 
            'label' 		=> __('Label 1', 'smarty-upsell-bundle-manager'), 
            'description' 	=> __('Enter the label for example: `Best Seller`', 'smarty-upsell-bundle-manager'),
            'desc_tip' 		=> true,
            'value' 		=> get_post_meta($variation->ID, '_smarty_ubm_label_1', true),
            'wrapper_class' => 'form-row form-row-first'
        ));

        // Custom field for Label 2
        woocommerce_wp_text_input(array(
            'id' 			=> 'smarty_ubm_label_2[' . $variation->ID . ']', 
            'label' 		=> __('Label 2', 'smarty-upsell-bundle-manager'), 
            'description' 	=> __('Enter the label for example: `Best Value`', 'smarty-upsell-bundle-manager'),
            'desc_tip' 		=> true,
            'value' 		=> get_post_meta($variation->ID, '_smarty_ubm_label_2', true),
            'wrapper_class' => 'form-row form-row-last'
        ));
    }
    
	/**
     * This function handles the saving of data entered into the custom fields 
     * ('Label 1' and 'Label 2') for each product variation.
	 * 
	 * @since    1.0.0
     */
    public function ubm_save_custom_fields_variations($variation_id, $i) {
        // Save Best Seller Label
        if (isset($_POST['smarty_ubm_label_1'][$variation_id])) {
            update_post_meta($variation_id, '_smarty_ubm_label_1', sanitize_text_field($_POST['smarty_ubm_label_1'][$variation_id]));
        }

        // Save Best Value Label
        if (isset($_POST['smarty_ubm_label_2'][$variation_id])) {
            update_post_meta($variation_id, '_smarty_ubm_label_2', sanitize_text_field($_POST['smarty_ubm_label_2'][$variation_id]));
        }
    }

	/**
     * @since    1.0.0
     */
	public function ubm_display_additional_products_order_meta($item_id, $item, $order) {
        $additional_products = wc_get_order_item_meta($item_id, '_additional_products', true);
        //error_log('Additional Products: ' . print_r($additional_products, true)); // Debug log
        
        if ($additional_products && is_array($additional_products)) {
            // Use ob_start to capture the output
            ob_start();
            echo '<div class="bundle-items">';
            if (is_page('checkout') || is_admin()) {
                echo '<span class="dashicons dashicons-archive"></span>';
            } 
            echo '<p><strong>' . __('In a bundle with', 'smarty-upsell-bundle-manager') . ':</strong></p>';
            echo '<ul style="list-style-type: none !important; padding: 0 5px;">';
            foreach ($additional_products as $additional_product_id) {
                $product = wc_get_product($additional_product_id);
                if ($product) {
                    //error_log('Product ID: ' . $additional_product_id . ' - SKU: ' . $additional_sku); // Debug log
                    echo '<li>- 1 <small>x</small> ' . '<span>' . esc_html($product->get_name()) . '</span>' . ' (' . wc_price($product->get_price()) . ')</li>';
                    if (!is_page('checkout')) {
                        echo '<ul style="list-style-type: none !important; padding: 0 15px;"><li><span><small>- <strong>' . __('SKU: ', 'smarty-upsell-bundle-manager') . '</strong>' . esc_html($product->get_sku()) . '</span>' . '</small></li></ul>';
                    }
                }
            }
            echo '</ul>';
            echo '</div>';
            // Capture the output and assign it to a variable
            $additional_products_html = ob_get_clean();
            
            // Display the additional products in the order item meta
            echo $additional_products_html;
        }
    }

	/**
     * @since    1.0.0
     */
	public function ubm_add_order_list_column($columns) {
        $new_columns = array();
    
        foreach ($columns as $key => $column) {
            if ('order_number' === $key) {
                $new_columns['is_bundle'] = '';
            }
            $new_columns[$key] = $column;
        }
    
        return $new_columns;
    }

	/**
     * @since    1.0.0
     */
	public function ubm_add_order_list_column_content($column, $post_id) {
        if ('is_bundle' === $column) {
            $order = wc_get_order($post_id);
            $items = $order->get_items();
            $has_bundle = false;
    
            foreach ($items as $item_id => $item) {
                $additional_products = wc_get_order_item_meta($item_id, '_additional_products', true);
                if ($additional_products && is_array($additional_products)) {
                    $has_bundle = true;
                    break;
                }
            }
    
            if ($has_bundle) {
                echo '<span class="dashicons dashicons-archive" title="' . __('This order contains bundled products', 'smarty-upsell-bundle-manager') . '"></span>';
            } else {
                echo '';
            }
        }
    }
}