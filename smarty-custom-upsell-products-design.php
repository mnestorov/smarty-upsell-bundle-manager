<?php
/**
 * Plugin Name: SM - Custom Upsell Products Design for WooCommerce
 * Plugin URI: https://smartystudio.net/smarty-custom-upsell-products-design
 * Description: Designed to change the product variation design for single products in WooCommerce.
 * Version: 1.0.0
 * Author: Smarty Studio | Martin Nestorov
 * Author URI: https://smartystudio.net
 * Text Domain: smarty-custom-upsell-products-design
 * Domain Path: /languages/
 * WC requires at least: 3.0.0
 * WC tested up to: 6.1.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

if (!function_exists('smarty_register_settings_page')) {
    function smarty_register_settings_page() {
        add_submenu_page(
            'woocommerce',
            __('Custom Upsell Products Design | Settings', 'smarty-custom-upsell-products-design'),
            __('Upsell Products Design', 'smarty-custom-upsell-products-design'),
            'manage_options',
            'smarty-custom-upsell-settings',
            'smarty_settings_page_content',
        );
    }
    add_action('admin_menu', 'smarty_register_settings_page');
}

if (!function_exists('smarty_register_settings')) {
    function smarty_register_settings() {
        // Register settings
        register_setting('smarty_settings_group', 'smarty_active_bg_color');
        register_setting('smarty_settings_group', 'smarty_active_border_color');
        register_setting('smarty_settings_group', 'smarty_price_color');
        register_setting('smarty_settings_group', 'smarty_old_price_color');
        register_setting('smarty_settings_group', 'smarty_free_delivery_bg_color');
        register_setting('smarty_settings_group', 'smarty_free_delivery_color');
        register_setting('smarty_settings_group', 'smarty_label_1_bg_color');
        register_setting('smarty_settings_group', 'smarty_label_1_color');
        register_setting('smarty_settings_group', 'smarty_label_2_bg_color');
        register_setting('smarty_settings_group', 'smarty_label_2_color');
        register_setting('smarty_settings_group', 'smarty_price_font_size');
        register_setting('smarty_settings_group', 'smarty_old_price_font_size');
        register_setting('smarty_settings_group', 'smarty_variable_desc_font_size');
        register_setting('smarty_settings_group', 'smarty_free_delivery_font_size');
        register_setting('smarty_settings_group', 'smarty_label_1_font_size');
        register_setting('smarty_settings_group', 'smarty_label_2_font_size');
        register_setting('smarty_settings_group', 'smarty_currency_symbol_position');
        register_setting('smarty_settings_group', 'smarty_currency_symbol_spacing');
        register_setting('smarty_settings_group', 'smarty_savings_text_size');
        register_setting('smarty_settings_group', 'smarty_savings_text_color');

        // Add settings sections
        add_settings_section('smarty_colors_section', 'Colors', 'smarty_colors_section_cb', 'smarty_settings_page');
        add_settings_section('smarty_font_sizes_section', 'Font Sizes', 'smarty_font_sizes_section_cb', 'smarty_settings_page');
        add_settings_section('smarty_currency_section', 'Currency Symbol', 'smarty_currency_section_cb', 'smarty_settings_page');

        // Add settings fields for colors
        add_settings_field('smarty_active_bg_color', 'Upsell (Background)', 'smarty_color_field_cb', 'smarty_settings_page', 'smarty_colors_section', ['id' => 'smarty_active_bg_color']);
        add_settings_field('smarty_active_border_color', 'Upsell (Border)', 'smarty_color_field_cb', 'smarty_settings_page', 'smarty_colors_section', ['id' => 'smarty_active_border_color']);
        add_settings_field('smarty_price_color', 'Price', 'smarty_color_field_cb', 'smarty_settings_page', 'smarty_colors_section', ['id' => 'smarty_price_color']);
        add_settings_field('smarty_old_price_color', 'Old Price', 'smarty_color_field_cb', 'smarty_settings_page', 'smarty_colors_section', ['id' => 'smarty_old_price_color']);
        add_settings_field('smarty_free_delivery_bg_color', 'Free Delivery (Background)', 'smarty_color_field_cb', 'smarty_settings_page', 'smarty_colors_section', ['id' => 'smarty_free_delivery_bg_color']);
        add_settings_field('smarty_free_delivery_color', 'Free Delivery (Text)', 'smarty_color_field_cb', 'smarty_settings_page', 'smarty_colors_section', ['id' => 'smarty_free_delivery_color']);
        add_settings_field('smarty_label_1_bg_color', 'Label 1 (Background)', 'smarty_color_field_cb', 'smarty_settings_page', 'smarty_colors_section', ['id' => 'smarty_label_1_bg_color']);
        add_settings_field('smarty_label_1_color', 'Label 1 (Text)', 'smarty_color_field_cb', 'smarty_settings_page', 'smarty_colors_section', ['id' => 'smarty_label_1_color']);
        add_settings_field('smarty_label_2_bg_color', 'Label 2 (Background)', 'smarty_color_field_cb', 'smarty_settings_page', 'smarty_colors_section', ['id' => 'smarty_label_2_bg_color']);
        add_settings_field('smarty_label_2_color', 'Label 2 (Text)', 'smarty_color_field_cb', 'smarty_settings_page', 'smarty_colors_section', ['id' => 'smarty_label_2_color']);

        // Add settings fields for font sizes
        add_settings_field('smarty_price_font_size', 'Price', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_price_font_size']);
        add_settings_field('smarty_old_price_font_size', 'Old Price', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_old_price_font_size']);
        add_settings_field('smarty_variable_desc_font_size', 'Upsell Description', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_variable_desc_font_size']);
        add_settings_field('smarty_free_delivery_font_size', 'Free Delivery', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_free_delivery_font_size']);
        add_settings_field('smarty_label_1_font_size', 'Label 1', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_label_1_font_size']);
        add_settings_field('smarty_label_2_font_size', 'Label 2', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_label_2_font_size']);
        
        // Add settings fields for savings text
        add_settings_field('smarty_savings_text_size', 'Savings Text', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_savings_text_size']);
        add_settings_field('smarty_savings_text_color', 'Savings Text', 'smarty_color_field_cb', 'smarty_settings_page', 'smarty_colors_section', ['id' => 'smarty_savings_text_color']);

        // Add settings fields for currency
        add_settings_field('smarty_currency_symbol_position', 'Position', 'smarty_currency_position_field_cb', 'smarty_settings_page', 'smarty_currency_section', ['id' => 'smarty_currency_symbol_position']);
        add_settings_field('smarty_currency_symbol_spacing', 'Spacing', 'smarty_currency_spacing_field_cb', 'smarty_settings_page', 'smarty_currency_section', ['id' => 'smarty_currency_symbol_spacing']);
    }
    add_action('admin_init', 'smarty_register_settings');
}

function smarty_colors_section_cb() {
    echo '<p>Customize the colors for various elements in your WooCommerce upsell products.</p>';
}

function smarty_color_field_cb($args) {
    $option = get_option($args['id'], '');
    echo '<input type="text" name="' . $args['id'] . '" value="' . esc_attr($option) . '" class="smarty-color-field" data-default-color="' . esc_attr($option) . '" />';
}

function smarty_font_sizes_section_cb() {
    echo '<p>Customize the font sizes for various elements in your WooCommerce upsell products.</p>';
}

function smarty_font_size_field_cb($args) {
    $option = get_option($args['id'], '14');
    echo '<input type="range" name="' . $args['id'] . '" min="10" max="30" value="' . esc_attr($option) . '" class="smarty-font-size-slider" />';
    echo '<span id="' . $args['id'] . '-value">' . esc_attr($option) . 'px</span>';
}

function smarty_currency_section_cb() {
    echo '<p>Customize the currency symbol position and spacing for your WooCommerce upsell products.</p>';
}

function smarty_currency_position_field_cb($args) {
    $option = get_option($args['id'], 'left');
    echo '<select name="' . $args['id'] . '">';
    echo '<option value="left"' . selected($option, 'left', false) . '>Left</option>';
    echo '<option value="right"' . selected($option, 'right', false) . '>Right</option>';
    echo '</select>';
}

function smarty_currency_spacing_field_cb($args) {
    $option = get_option($args['id'], 'no_space');
    echo '<select name="' . $args['id'] . '">';
    echo '<option value="space"' . selected($option, 'space', false) . '>With Space</option>';
    echo '<option value="no_space"' . selected($option, 'no_space', false) . '>Without Space</option>';
    echo '</select>';
}

if (!function_exists('smarty_settings_page_content')) {
    function smarty_settings_page_content() {
        ?>
       <div class="wrap">
            <h1><?php _e('Custom Upsell Products Design | Settings', 'smarty-custom-upsell-products-design'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('smarty_settings_group');
                do_settings_sections('smarty_settings_page');
                ?>
                <hr style="border-bottom: 2px solid #ccc;">
                <?php submit_button(); ?>
            </form>
        </div>
        <style>
            .wp-color-result { vertical-align: middle; }
        </style>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.smarty-color-field').wpColorPicker();

                // Update the font size value display
                $('.smarty-font-size-slider').on('input', function() {
                    var sliderId = $(this).attr('name');
                    $('#' + sliderId + '-value').text($(this).val() + 'px');
                });
            });
        </script>
        <?php
    }
}

if (!function_exists('smarty_copy_files_to_child_theme')) {
    /**
     * Copies specific files from a plugin directory to a child theme directory in WordPress.
     * 
     * This function is designed to enhance a child theme by programmatically copying 
     * certain files from a specified plugin directory. It is particularly useful for 
     * overriding WooCommerce templates or similar files within a child theme.
     *
     * How it works:
     * - The function defines an array of filenames that need to be copied.
     * - It specifies the source directory (usually within a plugin) and the destination 
     *   directory (within a child theme).
     * - The function then iterates over each file, checking if it exists in the source 
     *   directory.
     * - If the file exists, the function attempts to copy it to the destination directory.
     * - If the destination directory does not exist, it will be created.
     * - The function provides basic feedback through echoes, informing if a file was 
     *   successfully copied or if an error occurred (including file not found or copy failure).
     *
     * Usage:
     * - This function can be triggered as needed, typically within a plugin's setup or 
     *   initialization phase.
     * - Be cautious about file permissions and existing file checks to prevent 
     *   unintended data loss or security issues.
     *
     * @param bool $debug Whether to enable debug mode for detailed output.
     * @return void Outputs messages based on success or failure of file copying when debugging is enabled.
     */
    function smarty_copy_files_to_child_theme($debug = false) {
        // Define an array of file names to copy
        $files_to_copy = [
            'variation.php',
            'variable.php',
            'variable-product-upsell-design.php',
            'variable-product-standard-variations.php',
        ];

        // Define the source and destination directories
        $source_directory = plugin_dir_path( __FILE__ ) . '/templates/woocommerce/single-product/add-to-cart/';
        $destination_directory = get_stylesheet_directory() . '/woocommerce/single-product/add-to-cart/';

        // Check if destination directory exists, if not create it
        if (!file_exists($destination_directory)) {
            mkdir($destination_directory, 0755, true);
        }

        // Loop through each file and copy it
        foreach ($files_to_copy as $file_name) {
            $source_path = $source_directory . $file_name;
            $destination_path = $destination_directory . $file_name;

            // Check if the source file exists
            if (file_exists($source_path)) {
                // Use the built-in PHP copy function to copy the file
                if (copy($source_path, $destination_path)) {
                    echo 'Copied file: ' . $file_name . '<br>';
                } else {
                    echo 'Error: Unable to copy file: ' . $file_name . '<br>';
                }
            } else {
                echo 'Error: Source file not found: ' . $file_name . '<br>';
            }
        }
    }
}

// Use the function with debugging enabled
//smarty_copy_files_to_child_theme(true);

// Use the function without debugging (in production, for example)
//smarty_copy_files_to_child_theme(false);

if (!function_exists('smarty_after_edit_attribute_fields')) {
    /**
     * Adds custom fields to the WooCommerce attribute edit form.
     *
     * @return void Echoes HTML output for the custom field.
     */
    function smarty_after_edit_attribute_fields() {
        $attr_id = isset($_GET['edit']) && $_GET['page'] === 'product_attributes' ? (int) $_GET['edit'] : false;
        $attr_up_sell = smarty_get_attr_fields($attr_id);

        // Sanitization is crucial here for security
        $checked = checked('1', $attr_up_sell, false);

        // Escaping for output
        echo '<tr class="form-field">';
        echo '    <th valign="top" scope="row">';
        echo '        <label for="up_sell_design">' . esc_html__('Custom up-sell design', 'smarty-custom-upsell-products-design') . '</label>';
        echo '    </th>';
        echo '    <td>';
        echo '        <input name="up_sell_design" id="up_sell_design" type="checkbox" value="1" ' . esc_attr($checked) . ' />';
        echo '		  <p class="description">' . esc_html__('Turn the custom up-sell design on or off for attributes.', 'smarty-custom-upsell-products-design') . '</p>';
        echo '    </td>';
        echo '</tr>';
    }
    add_action('woocommerce_after_edit_attribute_fields', 'smarty_after_edit_attribute_fields', 10, 0);
}

if (!function_exists('smarty_woocommerce_attribute_updated')) {
    /**
     * Handles saving of the custom attribute fields on update.
     *
     * @param int $attribute_id ID of the attribute being updated.
     * @param array $attribute Array of new attribute data.
     * @param string $old_attribute_name Old name of the attribute.
     * @return void Saves the custom field data but does not return a value.
     */
    function smarty_woocommerce_attribute_updated($attribute_id, $attribute, $old_attribute_name) {
        if (isset($_POST['up_sell_design']) && $_POST['up_sell_design'] == 1) {
            update_option('up_sell_design_'. $attribute_id, 1);
        } else {
            update_option('up_sell_design_'. $attribute_id, 0);
        }
    }
    add_action('woocommerce_attribute_updated', 'smarty_woocommerce_attribute_updated', 10, 3);
}

if (!function_exists('smarty_get_attr_fields')) {
    /**
     * Retrieves the custom attribute fields.
     *
     * @param int $attr_id ID of the attribute.
     * @return mixed Value of the 'up_sell_design' option for the attribute or false if not set.
     */
    function smarty_get_attr_fields($attr_id) {
        $attr_up_sell = get_option('up_sell_design_'. $attr_id, false);
        return $attr_up_sell;
    }
}

if (!function_exists('smarty_variable_price_range')) {
    /**
     * Modifies the display format of WooCommerce variable product prices.
     * 
     * @param string $wcv_price Current price HTML.
     * @param WC_Product $product WooCommerce product object.
     * @return string Modified price HTML.
     */
    function smarty_variable_price_range($wc_variable_price, $product) {
        $prefix = '';
        $wc_variable_min_sale_price = null;
        $wc_variable_reg_min_price = $product->get_variation_regular_price('min', true);
        $wc_variable__min_sale_price = $product->get_variation_sale_price('min', true);
        $wc_variable__max_price = $product->get_variation_price('max', true);
        $wc_variable__min_price = $product->get_variation_price('min', true);
        $wc_variable__price = ($wc_variable_min_sale_price == $wc_variable_reg_min_price) 
            ? wc_price($wc_variable_reg_min_price) 
            : wc_price($wc_variable_min_sale_price);

        return ($wc_variable_min_price == $wc_variable_max_price) ? $wc_variable_price : sprintf('%s%s', $prefix, $wc_variable_price);
    }
    add_filter('woocommerce_variable_sale_price_html', 'smarty_variable_price_range', 10, 2);
    add_filter('woocommerce_variable_price_html', 'smarty_variable_price_range', 10, 2);
}

if (!function_exists('smarty_additional_product_recalculate_price')) {
    /**
     * Recalculates the price of products in the cart under specific conditions.
     * 
     * @param WC_Cart $cart_object The WooCommerce cart object.
     * @return void Modifies the cart object but does not return a value.
     */
    function smarty_additional_product_recalculate_price($cart_object) {
        $general_products = array();
        $all_pr = array();
        $new_pr_price = array();
        
        foreach ($cart_object->get_cart() as $hash => $value ) {
            $promo_general_product = get_post_meta( $value['product_id'], 'attach_general_products', true );
            
            if (!empty($promo_general_product) && $promo_general_product != 0) {
                $promo_general_product = str_replace(' ', '', $promo_general_product);
                $general_products[$value['product_id']] = explode(',', $promo_general_product);
            }
            
            $all_pr[$value['product_id']] = $value['data']->get_regular_price();
        }

        if (count($general_products) > 0) {
            foreach ($general_products as $promo_pr_id => $g_pr_id) {
                $general_pr = array_intersect_key(array_flip($g_pr_id), $all_pr);
                
                if (count($general_pr) == 0) {
                    $new_pr_price[$promo_pr_id] = $all_pr[$promo_pr_id];
                }
            }
        }

        if (count($new_pr_price) > 0) {
            foreach ($cart_object->get_cart() as $hash => $value ) {
            
                if (array_key_exists($value['product_id'], $new_pr_price)) {
                    $value['data']->set_price( $new_pr_price[$value['product_id']] );
                }
            }
        }
    }
    add_action( 'woocommerce_before_calculate_totals', 'smarty_additional_product_recalculate_price' );
}

if (!function_exists('smarty_woocommerce_available_variation')) {
    /**
     * Filters the variation data array to modify the price HTML output.
     * 
     * This function adjusts the price displayed in the variation templates to show
     * only the sale price if the product is on sale, or the regular price otherwise.
     * It directly affects the JavaScript-based template by modifying the `price_html` key
     * in the variation data array before it is passed to the front end.
     *
     * @param array       $variation_data Array of variation data.
     * @param WC_Product  $product The variable product object.
     * @param WC_Product_Variation $variation The single variation object.
     * @return array Modified variation data including only the active price HTML.
     */
    function smarty_woocommerce_available_variation( $variation_data, $product, $variation ) {
        // Check if the variation is on sale.
        if ( $variation->is_on_sale() ) {
            // If on sale, set the price_html to the sale price wrapped in appropriate HTML.
            $variation_data['price_html'] = wc_price( $variation->get_sale_price() );
        } else {
            // If not on sale, set the price_html to the regular price wrapped in appropriate HTML.
            $variation_data['price_html'] = wc_price( $variation->get_regular_price() );
        }

        // Return the modified variation data array.
        return $variation_data;
    }
    add_filter( 'woocommerce_available_variation', 'smarty_woocommerce_available_variation', 10, 3 );
}

if (!function_exists('smarty_free_delivery_amount')) {
    /**
     * Calculates the amount required for free delivery based on the blog ID.
     *
     * @return float Minimum amount required for free delivery.
     */
    function smarty_free_delivery_amount() {
        $minimum_free_delivery_amount = PHP_INT_MAX;

        // Get all shipping zones
        $shipping_zones = WC_Shipping_Zones::get_zones();

        foreach ($shipping_zones as $zone_id => $zone) {
            // Get shipping methods for the zone
            $shipping_methods = WC_Shipping_Zones::get_zone($zone_id)->get_shipping_methods(true, 'values');
            
            foreach ($shipping_methods as $method) {
                // Check if the method is free shipping and enabled
                if ($method->id == 'free_shipping' && $method->enabled == 'yes') {
                    // Extract the minimum amount for free shipping
                    $min_amount = $method->min_amount;

                    // Compare and store the lowest minimum amount across all zones
                    if (is_numeric($min_amount) && $min_amount < $minimum_free_delivery_amount) {
                        $minimum_free_delivery_amount = $min_amount;
                    }
                }
            }
        }

        // Return the lowest found minimum amount, or a default if none is set
        return ($minimum_free_delivery_amount !== PHP_INT_MAX) ? $minimum_free_delivery_amount : 0;
    }
}

if (!function_exists('smarty_add_custom_fields_to_variations')) {
    /**
     * This function adds two custom text input fields to WooCommerce 
     * product variation forms in the admin panel. 
     */
    function smarty_add_custom_fields_to_variations($loop, $variation_data, $variation) {
        // Custom field for Label 1
        woocommerce_wp_text_input(array(
            'id' => 'smarty_label_1[' . $variation->ID . ']', 
            'label' => __('Label 1', 'smarty-custom-upsell-products-design'), 
            'description' => __('Enter the label for example: `Best Seller`', 'smarty-custom-upsell-products-design'),
            'desc_tip' => true,
            'value' => get_post_meta($variation->ID, '_smarty_label_1', true),
            'wrapper_class' => 'form-row form-row-first'
        ));

        // Custom field for Label 2
        woocommerce_wp_text_input(array(
            'id' => 'smarty_label_2[' . $variation->ID . ']', 
            'label' => __('Label 2', 'smarty-custom-upsell-products-design'), 
            'description' => __('Enter the label for example: `Best Value`', 'smarty-custom-upsell-products-design'),
            'desc_tip' => true,
            'value' => get_post_meta($variation->ID, '_smarty_label_2', true),
            'wrapper_class' => 'form-row form-row-last'
        ));
    }
    add_action('woocommerce_product_after_variable_attributes', 'smarty_add_custom_fields_to_variations', 10, 3);
}

if (!function_exists('smarty_save_custom_fields_variations')) {
    /**
     * This function handles the saving of data entered into the custom fields 
     * ('Label 1' and 'Label 2') for each product variation.
     */
    function smarty_save_custom_fields_variations($variation_id, $i) {
        // Save Best Seller Label
        if (isset($_POST['smarty_label_1'][$variation_id])) {
            update_post_meta($variation_id, '_smarty_label_1', sanitize_text_field($_POST['smarty_label_1'][$variation_id]));
        }

        // Save Best Value Label
        if (isset($_POST['smarty_label_2'][$variation_id])) {
            update_post_meta($variation_id, '_smarty_label_2', sanitize_text_field($_POST['smarty_label_2'][$variation_id]));
        }
    }
    add_action('woocommerce_save_product_variation', 'smarty_save_custom_fields_variations', 10, 2);
}

if (!function_exists('smarty_custom_css')) {
	/**
	 * Outputs custom CSS to the head of single product pages.
	 *
	 * This function is hooked into the 'wp_head' action hook, so it runs
	 * whenever the head section of the site is generated. It checks if the
	 * current page is a single product page, and if so, it outputs a block
	 * of CSS styles to the head of the page.
	 *
	 * You can modify the CSS styles within the function to suit your needs.
	 */
	function smarty_custom_css() {
        $active_bg_color = get_option('smarty_active_bg_color', 'rgba(210, 184, 133, 0.3)');
        $active_border_color = get_option('smarty_active_border_color', '#D2B885');
        $price_color = get_option('smarty_price_color', '#99B998');
        $old_price_color = get_option('smarty_old_price_color', '#DD5444');
        $free_delivery_bg_color = get_option('smarty_free_delivery_bg_color', '#709900');
        $free_delivery_color = get_option('smarty_free_delivery_color', '#FFFFFF');
        $label_1_bg_color = get_option('smarty_label_1_bg_color', '#FFC045');
        $label_1_color = get_option('smarty_label_1_color', '#FFFFFF');
        $label_2_bg_color = get_option('smarty_label_2_bg_color', '#3F4BA4');
        $label_2_color = get_option('smarty_label_2_color', '#FFFFFF');
        $price_font_size = get_option('smarty_price_font_size', '14');
        $old_price_font_size = get_option('smarty_old_price_font_size', '14');
        $variable_desc_font_size = get_option('smarty_variable_desc_font_size', '14');
        $free_delivery_font_size = get_option('smarty_free_delivery_font_size', '14');
        $label_1_font_size = get_option('smarty_label_1_font_size', '14');
        $label_2_font_size = get_option('smarty_label_2_font_size', '14');
        $savings_text_size = get_option('smarty_savings_text_size', '14') . 'px';
        $savings_text_color = get_option('smarty_savings_text_color', '#000000');
    
        if (is_admin()) {
            echo '<style>
            .woocommerce_variation .form-row {
                overflow: hidden;
            }
            .woocommerce_variation .form-row.full {
                clear: both;
            }
            .woocommerce_variation .form-row.form-row-first,
            .woocommerce_variation .form-row.form-row-last {
                width: 49%;
                float: left;
                box-sizing: border-box;
            }
            </style>';
        }
    
        if (is_product()) {
            echo '<style>
            .product-single .product__actions .product__actions__inner {
                border: none;
            }
            
            .product-single .product__actions .quantity input,
            .woocommerce-variation-add-to-cart .variations_button .woocommerce-variation-add-to-cart-enabled .quantity,
            .checkmark {
                display: none;
            }
            
            .main_title_wrap {
                position: relative;
                height: 115px;
                padding-left: 15px;
                margin: 30px 0;
                box-shadow: 0px 3px 11px -2px rgba(0, 0, 0, 0.55);
                -webkit-box-shadow: 0px 3px 11px -2px rgba(0, 0, 0, 0.55);
                -moz-box-shadow: 0px 3px 11px -2px rgba(0, 0, 0, 0.55);
                transition: all 0.3s ease-in;
                border-radius: 5px;
                border: 2px solid #ffffff00;
            }
            
            .main_title_wrap .var_txt {
                position: absolute;
                top: 24px;
                width: 100%;
                font-size: ' . esc_attr($font_size) . 'px;
            }

            .main_title_wrap.active,
            .main_title_wrap:hover {
                background: ' . esc_attr($active_bg_color) . ';
                border: 2px solid ' . esc_attr($active_border_color) . ';
            }
    
            .price {
                color: ' . esc_attr($price_color) . ';
                font-weight: bold;
                font-size: ' . esc_attr($price_font_size) . 'px;
            }
            
            .old_price {
                text-decoration: line-through;
                color: ' . esc_attr($old_price_color) . ';
                font-weight: bold;
                font-size: ' . esc_attr($old_price_font_size) . 'px;
            }
            
            .main_title_wrap input {
                position: absolute;
                top: 27px;
            }
            
            .variable_content {
                margin-top: 45px;
            }
            
            .variable_title {
                margin-left: 24px !important;
                font-size: 16px;
                font-weight: 700;
            }
            
            variable_desc {
                font-size: ' . esc_attr($variable_desc_font_size) . 'px;
            }
            
            .main_title_wrap .variable_img {
                width: 16%;
                float: right;
                margin-top: 25px;
                margin-right: 10px;
                border: 1px solid rgba(51, 51, 51, .2);
                border-radius: 5px;
            }

            .variations_form .variations {
                padding-top: 0;
            }
            
            .product-single .product__actions .single_variation_wrap .woocommerce-variation {
                height: 40px;
                padding: 12px 50px 20px 160px;
            }
    
            .label_1 {
                font-size: ' . esc_attr($label_1_font_size) . 'px;
                color: ' . esc_attr($label_1_color) . ';
                font-weight: 600;
                position: absolute;
                top: 0;
                right: 0;
                border-radius: 0 0 0 75px;
                padding: 0 18px;
                background: ' . esc_attr($label_1_bg_color) . ';
            }

            .label_2 {
                font-size: ' . esc_attr($label_2_font_size) . 'px;
                color: ' . esc_attr($label_2_color) . ';
                font-weight: 600;
                position: absolute;
                top: 0;
                right: 0;
                border-radius: 0 0 0 75px;
                padding: 0 18px;
                background: ' . esc_attr($label_2_bg_color) . ';
            }
            
            .free_delivery {
                font-size: ' . esc_attr($free_delivery_font_size) . 'px;
                color: ' . esc_attr($free_delivery_color) . ';
                font-weight: 600;
                position: absolute;
                top: 0;
                left: 0;
                border-radius: 0 0 75px 0;
                padding: 0 18px;
                background: ' . esc_attr($free_delivery_bg_color) . ';
            }

            .savings-text {
                font-size: ' . esc_attr($savings_text_size) . ';
                color: ' . esc_attr($savings_text_color) . ';
            }
            </style>';
        }
    }
    add_action('wp_head', 'smarty_custom_css');    
}

if (!function_exists('smarty_admin_custom_js')) {
	/**
	 * This function adds custom JavaScript to the WooCommerce product 
	 * edit screen in the WordPress admin. 
	 */
	function smarty_admin_custom_js() {
		if ('product' != get_post_type()) {
			return;
		}
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				function toggleLabelInputs() {
					// Check the value of each field and disable or enable the other accordingly
					$('.woocommerce_variation').each(function() {
						var labelOneInput = $(this).find('[id^="smarty_label_1"]');
						var labelTwoInput = $(this).find('[id^="smarty_label_2"]');

						if (labelOneInput.val() != '') {
							labelTwoInput.prop('disabled', true);
						} else {
							labelTwoInput.prop('disabled', false);
						}

						if (labelTwoInput.val() != '') {
							labelOneInput.prop('disabled', true);
						} else {
							labelOneInput.prop('disabled', false);
						}
					});
				}

				// Run the toggle function when WooCommerce variations are loaded
				$(document).on('woocommerce_variations_loaded', function() {
					toggleLabelInputs();
				});

				// Bind the toggle function to the keyup event of each input field
				$(document).on('keyup', '[id^="smarty_label_1"], [id^="smarty_label_2"]', function() {
					toggleLabelInputs();
				});
			});
		</script>
		<?php
	}
	add_action('admin_footer', 'smarty_admin_custom_js');
}

if (!function_exists('smarty_public_custom_js')) {
    function smarty_public_custom_js() {
        $currency_symbol = html_entity_decode(get_woocommerce_currency_symbol());
        $currency_position = get_option('smarty_currency_symbol_position', 'left');
        $currency_spacing = get_option('smarty_currency_symbol_spacing', 'no_space');
        $spacing = $currency_spacing === 'space' ? ' ' : '';

        // Get savings text settings
        $savings_text_size = get_option('smarty_savings_text_size', '14') . 'px';
        $savings_text_color = get_option('smarty_savings_text_color', '#000000');

        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                function setActiveUpsell() {
                    // Select the first variation label and add the active class
                    var firstVariation = $('.check_container.has_variations:first .main_title_wrap');
                    if (firstVariation.length) {
                        $('.main_title_wrap').removeClass('active'); // Remove active class from all upsells
                        firstVariation.addClass('active'); // Add active class to first upsell
                    }
                }

                // Currency settings
                var currencySymbol = '<?php echo $currency_symbol; ?>';
                var currencyPosition = '<?php echo $currency_position; ?>';
                var currencySpacing = '<?php echo $spacing; ?>';
                var savingsTextSize = '<?php echo $savings_text_size; ?>';
                var savingsTextColor = '<?php echo $savings_text_color; ?>';

                // Format the price according to the settings
                function formatPrice(price, isRegular) {
                    if (isRegular || currencyPosition === 'left') {
                        return currencySymbol + currencySpacing + price;
                    } else {
                        return price + currencySpacing + currencySymbol;
                    }
                }

                // Format the savings message
                function formatSavings(regularPrice, salePrice) {
                    var savings = regularPrice - salePrice;
                    return '<span class="savings-text" style="font-size:' + savingsTextSize + '; color:' + savingsTextColor + ';">(you save ' + formatPrice(savings.toFixed(2), false) + ')</span>';
                }

                // Call the function to set the active upsell on page load
                setActiveUpsell();

                // Bind click event to upsell elements to set them active on click
                $('.main_title_wrap').on('click', function() {
                    $('.main_title_wrap').removeClass('active'); // Remove active class from all upsells
                    $(this).addClass('active'); // Add active class to clicked upsell
                });

                // Apply the formatted price to the price elements and show savings
                $('.upsell-container .price:not(.old_price)').each(function() {
                    var regularPriceText = $(this).closest('.main_title_wrap').find('.old_price').text().replace(/[^\d.]/g, '');
                    var salePriceText = $(this).text().replace(/[^\d.]/g, '');

                    if (regularPriceText && salePriceText) {
                        var regularPrice = parseFloat(regularPriceText);
                        var salePrice = parseFloat(salePriceText);

                        var formattedRegularPrice = formatPrice(regularPrice.toFixed(2), true); // Force left position for regular price
                        var formattedSalePrice = formatPrice(salePrice.toFixed(2), false);
                        var savingsMessage = formatSavings(regularPrice, salePrice);

                        $(this).closest('.main_title_wrap').find('.old_price').text(formattedRegularPrice);
                        $(this).html(formattedSalePrice + ' ' + savingsMessage);
                    } else {
                        var priceText = $(this).text().replace(/[^\d.]/g, '');
                        $(this).text(formatPrice(priceText, $(this).hasClass('old_price')));
                    }
                });
            });
        </script>
        <?php
    }
    add_action('wp_head', 'smarty_public_custom_js');
}
