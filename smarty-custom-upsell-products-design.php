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
 * WC requires at least: 3.5.0
 * WC tested up to: 9.0.2
 * Requires Plugins: woocommerce
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

if (!function_exists('smarty_enqueue_scripts')) {
    function smarty_enqueue_scripts($hook_suffix) {
        // Only add to the admin page of the plugin
        if ('woocommerce_page_smarty-custom-upsell-settings' !== $hook_suffix) {
            return;
        }

        wp_enqueue_style('select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
        wp_enqueue_script('select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', array('jquery'), '4.0.13', true);

        // Enqueue style and script for using the WordPress color picker.
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }
    add_action('admin_enqueue_scripts', 'smarty_enqueue_scripts');
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
        register_setting('smarty_settings_group', 'smarty_additional_products');
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
        register_setting('smarty_settings_group', 'smarty_image_border_color');
        register_setting('smarty_settings_group', 'smarty_display_savings');
        register_setting('smarty_settings_group', 'smarty_debug_mode');
        register_setting('smarty_settings_group', 'smarty_debug_notices_enabled');

        // Add settings sections
        add_settings_section('smarty_additional_products_section', 'Products', 'smarty_additional_products_section_cb', 'smarty_settings_page');
        add_settings_section('smarty_colors_section', 'Colors', 'smarty_colors_section_cb', 'smarty_settings_page');
        add_settings_section('smarty_font_sizes_section', 'Font Sizes', 'smarty_font_sizes_section_cb', 'smarty_settings_page');
        add_settings_section('smarty_currency_section', 'Currency Symbol', 'smarty_currency_section_cb', 'smarty_settings_page');
        add_settings_section('smarty_display_options_section', 'Display Options', 'smarty_display_options_section_cb', 'smarty_settings_page');
        add_settings_section('smarty_settings_section', 'Debug', 'smarty_settings_section_cb', 'smarty_settings_page');
        
        // Add settings fields for colors
        add_settings_field('smarty_additional_products', 'Additional Products', 'smarty_additional_products_field_cb', 'smarty_settings_page', 'smarty_additional_products_section');
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
        add_settings_field('smarty_image_border_color', 'Image Border', 'smarty_color_field_cb', 'smarty_settings_page', 'smarty_colors_section', ['id' => 'smarty_image_border_color']);
        add_settings_field('smarty_savings_text_color', 'Savings Text', 'smarty_color_field_cb', 'smarty_settings_page', 'smarty_colors_section', ['id' => 'smarty_savings_text_color']);

        // Add settings fields for font sizes
        add_settings_field('smarty_price_font_size', 'Price', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_price_font_size']);
        add_settings_field('smarty_old_price_font_size', 'Old Price', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_old_price_font_size']);
        add_settings_field('smarty_variable_desc_font_size', 'Upsell Description', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_variable_desc_font_size']);
        add_settings_field('smarty_free_delivery_font_size', 'Free Delivery', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_free_delivery_font_size']);
        add_settings_field('smarty_label_1_font_size', 'Label 1', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_label_1_font_size']);
        add_settings_field('smarty_label_2_font_size', 'Label 2', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_label_2_font_size']);
        add_settings_field('smarty_savings_text_size', 'Savings Text', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_savings_text_size']);
        
        // Add settings fields for currency
        add_settings_field('smarty_currency_symbol_position', 'Position', 'smarty_currency_position_field_cb', 'smarty_settings_page', 'smarty_currency_section', ['id' => 'smarty_currency_symbol_position']);
        add_settings_field('smarty_currency_symbol_spacing', 'Spacing', 'smarty_currency_spacing_field_cb', 'smarty_settings_page', 'smarty_currency_section', ['id' => 'smarty_currency_symbol_spacing']);
    
        // Add settings field for display options
        add_settings_field('smarty_display_savings','Turn On/Off savings text', 'smarty_checkbox_field_cb', 'smarty_settings_page', 'smarty_display_options_section', ['id' => 'smarty_display_savings']);
        
        // Add settings fields for debug mode and for toggling debug notices
        add_settings_field('smarty_debug_mode', 'Debug Mode', 'smarty_checkbox_field_cb', 'smarty_settings_page', 'smarty_settings_section', ['id' => 'smarty_debug_mode']);
        add_settings_field('smarty_debug_notices_enabled', 'Enable Debug Notices', 'smarty_checkbox_field_cb', 'smarty_settings_page', 'smarty_settings_section', ['id' => 'smarty_debug_notices_enabled', 'label_for' => 'smarty_debug_notices_enabled']);
    }
    add_action('admin_init', 'smarty_register_settings');
}

if (!function_exists('smarty_additional_products_section_cb')) {
    function smarty_additional_products_section_cb() {
        echo '<p>Choose your WooCommerce additional products.</p>';
    }
}

if (!function_exists('smarty_colors_section_cb')) {
    function smarty_colors_section_cb() {
        echo '<p>Customize the colors for various elements in your WooCommerce upsell products.</p>';
    }
}

if (!function_exists('smarty_color_field_cb')) {
    function smarty_color_field_cb($args) {
        $option = get_option($args['id'], '');
        echo '<input type="text" name="' . $args['id'] . '" value="' . esc_attr($option) . '" class="smarty-color-field" data-default-color="' . esc_attr($option) . '" />';
    }
}

if (!function_exists('smarty_font_sizes_section_cb')) {
    function smarty_font_sizes_section_cb() {
        echo '<p>Customize the font sizes for various elements in your WooCommerce upsell products.</p>';
    }
}

if (!function_exists('smarty_font_size_field_cb')) {
    function smarty_font_size_field_cb($args) {
        $option = get_option($args['id'], '14');
        echo '<input type="range" name="' . $args['id'] . '" min="10" max="30" value="' . esc_attr($option) . '" class="smarty-font-size-slider" />';
        echo '<span id="' . $args['id'] . '-value">' . esc_attr($option) . 'px</span>';
    }
}

if (!function_exists('smarty_currency_section_cb')) {
    function smarty_currency_section_cb() {
        echo '<p>Customize the currency symbol position and spacing for your WooCommerce upsell products.</p>';
    }
}


if (!function_exists('smarty_additional_products_field_cb')) {
    function smarty_additional_products_field_cb() {
        $upsell_products = get_option('smarty_additional_products', []);
        // Ensure $additional_products is always an array
        $additional_products = is_array($upsell_products) ? $upsell_products : [];
        $products = wc_get_products(array('limit' => -1)); // Get all products

        echo '<select name="smarty_additional_products[]" multiple="multiple" id="smarty_additional_products" style="width: 100%;">';
        foreach ($products as $product) {
            $selected = in_array($product->get_id(), $additional_products) ? 'selected' : '';
            echo '<option value="' . esc_attr($product->get_id()) . '" ' . esc_attr($selected) . '>' . esc_html($product->get_name()) . '</option>';
        }
        echo '</select>'; ?>

        <script>
            jQuery(document).ready(function($) {
                $('#smarty_additional_products').select2({
                    placeholder: "Select additional products",
                    allowClear: true
                });
            });
        </script>
        <?php
    }
}

if (!function_exists('smarty_currency_position_field_cb')) {
    function smarty_currency_position_field_cb($args) {
        $option = get_option($args['id'], 'left');
        echo '<select name="' . $args['id'] . '">';
        echo '<option value="left"' . selected($option, 'left', false) . '>Left</option>';
        echo '<option value="right"' . selected($option, 'right', false) . '>Right</option>';
        echo '</select>';
    }
}

if (!function_exists('smarty_currency_spacing_field_cb')) {
    function smarty_currency_spacing_field_cb($args) {
        $option = get_option($args['id'], 'no_space');
        echo '<select name="' . $args['id'] . '">';
        echo '<option value="space"' . selected($option, 'space', false) . '>With Space</option>';
        echo '<option value="no_space"' . selected($option, 'no_space', false) . '>Without Space</option>';
        echo '</select>';
    }
}

if (!function_exists('smarty_display_options_section_cb')) {
    function smarty_display_options_section_cb() {
        echo '<p>Display options for the plugin.</p>';
    }
}

if (!function_exists('smarty_settings_section_cb')) {
    function smarty_settings_section_cb() {
        echo '<p>Adjust debug settings for the plugin.</p>';
    }
}

if (!function_exists('smarty_checkbox_field_cb')) {
    function smarty_checkbox_field_cb($args) {
        $option = get_option($args['id'], '');
        $checked = checked(1, $option, false);
        echo "<label class='smarty-toggle-switch'>";
        echo "<input type='checkbox' id='{$args['id']}' name='{$args['id']}' value='1' {$checked} />";
        echo "<span class='smarty-slider round'></span>";
        echo "</label>";
        // Display the description only for the debug mode checkbox
        if ($args['id'] == 'smarty_debug_mode') {
            echo '<p class="description">' . __('Copies specific template files from a plugin directory to a child theme directory in WordPress. <br><em><b>Important:</b> <span class="smarty-text-danger">Turn this to Off in production.</span></em>', 'smarty-custom-upsell-products-design') . '</p>';
        }
    }
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
        global $pagenow;

        static $already_run = false;
        if ($already_run) {
            return;
        }
    
        // Check if we are on the correct admin page
        if ($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'smarty-custom-upsell-settings') {
            
            // Retrieve debug setting
            $debug = get_option('smarty_debug_mode', false);

            $notices_enabled = get_option('smarty_debug_notices_enabled', false);

            if (!$notices_enabled) {
                return; // Exit if notices are disabled
            }

            $already_run = true; // Set to prevent future execution within the same request
    
            // Only proceed if debugging is true
            if (!$debug) {
                add_action('admin_notices', function() {
                    echo "<div class='notice notice-info is-dismissible'><p>Debug mode is off, not copying files.</p></div>";
                });
            } else {
                $files_to_copy = [
                    'variation.php',
                    'variable.php',
                    'variable-product-upsell-design.php',
                    'variable-product-standard-variations.php',
                ];
        
                // Define the source and destination directories
                $source_directory = plugin_dir_path(__FILE__) . '/templates/woocommerce/single-product/add-to-cart/';
                $destination_directory = get_stylesheet_directory() . '/woocommerce/single-product/add-to-cart/';
        
                // Check if destination directory exists, if not, create it
                if (!file_exists($destination_directory)) {
                    mkdir($destination_directory, 0755, true);
                }
        
                // Loop through each file and copy it
                foreach ($files_to_copy as $file_name) {
                    $source_path = $source_directory . $file_name;
                    $destination_path = $destination_directory . $file_name;
            
                    // Check if the source file exists
                    if (file_exists($source_path)) {
                        if (copy($source_path, $destination_path)) {
                            // Set success message
                            add_action('admin_notices', function() use ($file_name) {
                                echo "<div class='notice notice-success is-dismissible'><p>Copied file: <b>$file_name</b> successfully.</p></div>";
                            });
                        } else {
                            // Set error message
                            add_action('admin_notices', function() use ($file_name) {
                                echo "<div class='notice notice-error is-dismissible'><p>Error: Unable to copy file: <b>$file_name</b>.</p></div>";
                            });
                        }
                    } else {
                        // Set file not found message
                        add_action('admin_notices', function() use ($file_name) {
                            echo "<div class='notice notice-warning is-dismissible'><p>Error: Source file not found: <b>$file_name</b>.</p></div>";
                        });
                    }
                }
            }
        }
    }
    add_action('admin_init', 'smarty_copy_files_to_child_theme');    
}

// Use the function for debugging
$debug = get_option('smarty_debug_mode', false) === '1'; // strict comparison
smarty_copy_files_to_child_theme($debug);

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

if (!function_exists('smarty_admin_custom_css')) {
    function smarty_admin_custom_css() { 
        if (is_admin()) { ?>
            <style>
                /* The switch - the box around the slider */
                .smarty-toggle-switch {
                    position: relative;
                    display: inline-block;
                    width: 60px;
                    height: 34px;
                }

                /* Hide default HTML checkbox */
                .smarty-toggle-switch input {
                    opacity: 0;
                    width: 0;
                    height: 0;
                }

                /* The slider */
                .smarty-slider {
                    position: absolute;
                    cursor: pointer;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: #ccc;
                    transition: .4s;
                    border-radius: 34px;
                }

                .smarty-slider:before {
                    position: absolute;
                    content: "";
                    height: 26px;
                    width: 26px;
                    left: 4px;
                    bottom: 4px;
                    background-color: white;
                    transition: .4s;
                    border-radius: 50%;
                }

                input:checked + .smarty-slider {
                    background-color: #2196F3;
                }

                input:checked + .smarty-slider:before {
                    transform: translateX(26px);
                }

                /* Rounded sliders */
                .smarty-slider.round {
                    border-radius: 34px;
                }

                .smarty-slider.round:before {
                    border-radius: 50%;
                }

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

                /* Helpers */
                .smarty-text-danger  {
                    color: #c51244;
                }
            </style><?php
        } 
    }
    add_action('admin_head', 'smarty_admin_custom_css');
}

if (!function_exists('smarty_public_custom_css')) {
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
	function smarty_public_custom_css() {
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
        $image_border_color = get_option('smarty_image_border_color', '#000000');

        if (is_product()) { ?>
            <style>
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
                    font-size: <?php echo esc_attr($font_size) . 'px'; ?>;
                }

                .main_title_wrap.active,
                .main_title_wrap:hover {
                    background: <?php echo esc_attr($active_bg_color); ?>;
                    border: 2px solid <?php echo esc_attr($active_border_color); ?>;
                }
        
                .price {
                    color: <?php echo esc_attr($price_color); ?>;
                    font-weight: bold;
                    font-size: <?php echo esc_attr($price_font_size) . 'px'; ?>;
                }
                
                .old_price {
                    text-decoration: line-through;
                    color: <?php echo esc_attr($old_price_color); ?>;
                    font-weight: bold;
                    font-size: <?php echo esc_attr($old_price_font_size) . 'px'; ?>;
                }
                
                .main_title_wrap input {
                    position: absolute;
                    top: 27px;
                }
                
                .upsell-container .variable_content {
                    margin-top: 40px;
                }
                
                .upsell-container .variable_title {
                    margin-left: 24px !important;
                    font-size: 16px;
                    font-weight: 700;
                }
                
                .upsell-container .variable_desc {
                    font-size: <?php echo esc_attr($variable_desc_font_size) . 'px'; ?>;
                    padding-top: 7px;
                    display: block;
                }
                
                .upsell-container .variable_img {
                    width: 16%;
                    float: right;
                    margin-top: 18px;
                    margin-right: 10px;
                    border: 1px solid <?php echo esc_attr($image_border_color); ?>;
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
                    font-size: <?php echo esc_attr($label_1_font_size) . 'px'; ?>;
                    color: <?php echo esc_attr($label_1_color); ?>;
                    font-weight: 600;
                    position: absolute;
                    top: 0;
                    right: 0;
                    border-radius: 0 0 0 75px;
                    padding: 0 18px;
                    background: <?php echo esc_attr($label_1_bg_color); ?>;
                }

                .label_2 {
                    font-size: <?php echo esc_attr($label_2_font_size) . 'px'; ?>;
                    color: <?php echo esc_attr($label_2_color); ?>;
                    font-weight: 600;
                    position: absolute;
                    top: 0;
                    right: 0;
                    border-radius: 0 0 0 75px;
                    padding: 0 18px;
                    background: <?php echo esc_attr($label_2_bg_color); ?>;
                }
                
                .free_delivery {
                    font-size: <?php echo esc_attr($free_delivery_font_size) . 'px'; ?>;
                    color: <?php echo esc_attr($free_delivery_color); ?>;
                    font-weight: 600;
                    position: absolute;
                    top: 0;
                    left: 0;
                    border-radius: 0 0 75px 0;
                    padding: 0 18px;
                    background: <?php echo esc_attr($free_delivery_bg_color); ?>;
                }

                .savings-text {
                    font-size: <?php echo esc_attr($savings_text_size); ?>;
                    color: <?php echo esc_attr($savings_text_color); ?>;
                }

                @media (max-width: 480px) { /* Adjusts for devices with width less than 768px */
                    .savings-text {
                        display: block; /* Forces the text onto a new line */
                        margin-top: -5px; /* Adds some space above the text */
                    }
                }

                 /* Additional products*/
                 .additional-products {
                    margin-top: 20px;
                }

                .additional-products label {
                    display: flex;
                    align-items: center;
                    padding: 10px;
                    margin-bottom: 30px;
                    border-radius: 5px;
                    border: 2px solid #ffffff00;
                    box-shadow: 0px 3px 11px -2px rgba(0, 0, 0, 0.55);
                    -webkit-box-shadow: 0px 3px 11px -2px rgba(0, 0, 0, 0.55);
                    -moz-box-shadow: 0px 3px 11px -2px rgba(0, 0, 0, 0.55);
                }

                .additional-products label.active, 
                .additional-products label:hover {
                    background: #efe5d0;
                    border: 2px solid #d2b885;
                }

                .additional-products input[type="checkbox"] {
                    width: 20px;
                    height: 20px;
                    margin-right: 10px;
                    border-radius: 3px;
                    background-color: #f0f4e5;
                    border: 1px solid rgb(112,153,0);
                    cursor: pointer;
                    -webkit-appearance: none;
                    -moz-appearance: none;
                    appearance: none;
                    position: relative;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .additional-products input[type="checkbox"]::after {
                    content: "";
                    width: 10px;
                    height: 10px;
                    background-color: #709900;
                    border-radius: 2px;
                    display: none;
                }

                .additional-products input[type="checkbox"]:checked::after {
                    display: block;
                }

                .additional-product-image {
                    width: 65px;
                    height: 65px;
                    margin-right: 10px;
                }

                .additional-product-title {
                    font-weight: bold;
                    margin-right: auto;
                }

                .additional-product-price {
                    font-size: 18px;
                    color: #333333;
                    display: block;
                }

                .additional-product-regular-price > .woocommerce-Price-amount.amount bdi {
                    font-size: 18px;
                    color: #DD5444;
                    text-decoration: line-through;
                }

                .additional-product-sale-price > .woocommerce-Price-amount.amount bdi {
                    font-size: 18px;
                    color: #333333;
                    font-weight: bold;
                }
            </style><?php
        }
    }
    add_action('wp_head', 'smarty_public_custom_css');    
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
        $decimal_separator = wc_get_price_decimal_separator();
        $thousand_separator = wc_get_price_thousand_separator();
        $decimals = wc_get_price_decimals();
        $spacing = ($currency_spacing === 'space') ? ' ' : '';

        // Savings text settings
        $display_savings = get_option('smarty_display_savings', '0') === '1'; // get the setting and check if it is enabled
        $savings_text_size = get_option('smarty_savings_text_size', '14') . 'px';
        $savings_text_color = get_option('smarty_savings_text_color', '#000000');
        $youSaveText = esc_js(__('you save', 'smarty-custom-upsell-products-design')); // translatable text for 'you save'

        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                function setActiveUpsell() {
                    var firstVariation = $('.check_container.has_variations:first .main_title_wrap');
                    if (firstVariation.length) {
                        $('.main_title_wrap').removeClass('active');
                        firstVariation.addClass('active');
                    }
                }

                var currencySymbol = '<?php echo $currency_symbol; ?>';
                var currencyPosition = '<?php echo $currency_position; ?>';
                var currencySpacing = '<?php echo $spacing; ?>';
                var savingsTextSize = '<?php echo $savings_text_size; ?>';
                var savingsTextColor = '<?php echo $savings_text_color; ?>';
                var decimalSeparator = '<?php echo $decimal_separator; ?>';
                var thousandSeparator = '<?php echo $thousand_separator; ?>';
                var decimals = <?php echo $decimals; ?>;
                var youSaveText = '<?php echo $youSaveText; ?>';
                var displaySavings = <?php echo json_encode($display_savings); ?>;

                function formatPrice(price, isRegular) {
                    var formattedPrice = parseFloat(price).toLocaleString(undefined, {
                        minimumFractionDigits: decimals,
                        maximumFractionDigits: decimals,
                        style: 'decimal',
                        useGrouping: true
                    });

                    formattedPrice = formattedPrice.replace('.', decimalSeparator);

                    if (currencyPosition === 'left') {
                        return currencySymbol + currencySpacing + formattedPrice;
                    } else {
                        return formattedPrice + currencySpacing + currencySymbol;
                    }
                }

                function formatSavings(regularPrice, salePrice) {
                    if (!displaySavings) {
                        return ''; // if disabled, return an empty string
                    }
                    var savings = regularPrice - salePrice;
                    return '<span class="savings-text" style="font-size:' + savingsTextSize + '; color:' + savingsTextColor + ';">(' + youSaveText + ' ' + formatPrice(savings.toFixed(2), false) + ')</span>';
                }

                setActiveUpsell();

                $('.main_title_wrap').on('click', function() {
                    $('.main_title_wrap').removeClass('active');
                    $(this).addClass('active');
                });

                $('.upsell-container .price:not(.old_price)').each(function() {
                    var regularPriceText = $(this).closest('.main_title_wrap').find('.old_price').text().replace(/[^\d.,]/g, '');
                    var salePriceText = $(this).text().replace(/[^\d.,]/g, '');

                    if (regularPriceText && salePriceText) {
                        var regularPrice = parseFloat(regularPriceText.replace(decimalSeparator, '.'));
                        var salePrice = parseFloat(salePriceText.replace(decimalSeparator, '.'));

                        var formattedRegularPrice = formatPrice(regularPrice.toFixed(2), true);
                        var formattedSalePrice = formatPrice(salePrice.toFixed(2), false);
                        var savingsMessage = formatSavings(regularPrice, salePrice);

                        $(this).closest('.main_title_wrap').find('.old_price').text(formattedRegularPrice);
                        $(this).html(formattedSalePrice + ' ' + savingsMessage);
                    } else {
                        var priceText = $(this).text().replace(/[^\d.,]/g, '');
                        priceText = priceText.replace(decimalSeparator, '.');
                        $(this).text(formatPrice(priceText, $(this).hasClass('old_price')));
                    }
                });

                $('form.cart').on('submit', function(e) {
                    e.preventDefault();

                    var form = $(this);
                    var additionalProducts = form.find('input[name="additional_products[]"]:checked').map(function() {
                        return $(this).val();
                    }).get();

                    // AJAX call to add additional products to cart
                    $.ajax({
                        url: wc_add_to_cart_params.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'smarty_additional_products',
                            additional_products: additionalProducts,
                            // You can pass other necessary data here
                        },
                        success: function(response) {
                            // Handle success - add main product to cart after additional products
                            form.off('submit').submit();
                        },
                        error: function(response) {
                            console.log('Error adding additional products');
                        }
                    });
                });
            });
        </script>
        <?php
    }
    add_action('wp_head', 'smarty_public_custom_js');
}

if (!function_exists('smarty_add_additional_products_checkbox')) {
    function smarty_add_additional_products_checkbox() {
        global $product;

        // Get the additional products selected in plugin settings
        $additional_products_ids = get_option('smarty_additional_products', []);

        if (!empty($additional_products_ids) && is_array($additional_products_ids)) {
            $additional_products = wc_get_products(array(
                'include' => $additional_products_ids,
                'status' => 'publish',
                'limit' => -1,
            ));

            if ($additional_products) {
                echo '<div class="additional-products">';
                echo '<p><strong>' . __('You can also add', 'smarty-custom-upsell-products-design') . '</strong></p>';
                foreach ($additional_products as $additional_product) {
                    $product_obj = wc_get_product($additional_product->get_id());
                    $product_image = $product_obj->get_image('thumbnail');
                    $regular_price = $product_obj->get_regular_price();
                    $sale_price = $product_obj->get_sale_price();
                    
                    // If the product is variable, get the price of the first variation
                    if ($product_obj->is_type('variable')) {
                        $available_variations = $product_obj->get_available_variations();
                        if (!empty($available_variations)) {
                            $variation = reset($available_variations);
                            $regular_price = $variation['display_regular_price'];
                            $sale_price = $variation['display_price'];
                        }
                    }

                    $price_html = $sale_price ? 
                        '<span class="additional-product-regular-price">' . wc_price($regular_price) . '</span> <span class="additional-product-sale-price">' . wc_price($sale_price) . '</span>' :
                        '<span class="additional-product-price">' . wc_price($regular_price) . '</span>';
                    echo '<label>';
                    echo '<input type="checkbox" name="additional_products[]" value="' . esc_attr($additional_product->get_id()) . '">';
                    echo '<div class="additional-product-image">' . $product_image . '</div>';
                    echo '<div>';
                    echo '<div class="additional-product-title">' . esc_html($additional_product->get_name()) . '</div>';
                    echo '<div>' . $price_html . '</div>';
                    echo '</div>';
                    echo '</label>';
                }
                echo '</div>';
            }
        }
    }
    add_action('woocommerce_before_single_variation', 'smarty_add_additional_products_checkbox', 5);
}

if (!function_exists('smarty_update_total_price')) {
    function smarty_update_total_price() {
        $amount_text = esc_js(__('Amount: ', 'smarty-custom-upsell-products-design'));
        $currency_symbol = get_woocommerce_currency_symbol();
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var amountText = '<?php echo $amount_text; ?>';
                var currencySymbol = '<?php echo $currency_symbol; ?>';

                function updateTotalPrice() {
                    var basePrice = parseFloat($('.single_variation_wrap .woocommerce-variation-price').data('base-price')) || 0;
                    var additionalPrice = 0;
                    $('input[name="additional_products[]"]:checked').each(function() {
                        var label = $(this).closest('label');
                        var regularPrice = parseFloat(label.find('.additional-product-regular-price').text().replace(/[^\d.,]/g, '').replace(',', '.'));
                        var salePrice = parseFloat(label.find('.additional-product-sale-price').text().replace(/[^\d.,]/g, '').replace(',', '.'));
                        var price = salePrice || regularPrice;
                        additionalPrice += price;
                    });
                    var totalPrice = basePrice + additionalPrice;
                    $('.single_variation_wrap .woocommerce-variation-price').html(amountText + '<strong>' + totalPrice.toFixed(2) + ' ' + currencySymbol + '</strong>');
                }

                $('body').on('change', 'input[name="additional_products[]"]', function() {
                    updateTotalPrice();
                });

                $('body').on('show_variation', function(event, variation) {
                    var basePrice = parseFloat(variation.display_price);
                    $('.single_variation_wrap .woocommerce-variation-price').data('base-price', basePrice);
                    updateTotalPrice();
                });
            });
        </script>
        <?php
    }
    add_action('wp_footer', 'smarty_update_total_price');
}

if (!function_exists('smarty_handle_additional_products_cart')) {
    function smarty_handle_additional_products_cart() {
        // Start session if not already started
        if (!session_id()) {
            session_start();
        }

        // Retrieve additional products
        if (isset($_POST['additional_products']) && is_array($_POST['additional_products'])) {
            $additional_products = array_map('intval', $_POST['additional_products']); // Ensure the IDs are integers

            // Initialize session storage if not set
            if (!isset($_SESSION['added_additional_products'])) {
                $_SESSION['added_additional_products'] = array();
            }

            foreach ($additional_products as $additional_product_id) {
                // Add only if not already added in this session
                if (!in_array($additional_product_id, $_SESSION['added_additional_products'])) {
                    // Add additional product to the cart
                    if (wc_get_product($additional_product_id)) {
                        WC()->cart->add_to_cart($additional_product_id, 1);
                        $_SESSION['added_additional_products'][] = $additional_product_id;
                        error_log('Adding additional product ID: ' . $additional_product_id);
                    } else {
                        error_log('Product ID ' . $additional_product_id . ' not found.');
                    }
                } else {
                    error_log('Product ID ' . $additional_product_id . ' already added.');
                }
            }
        } else {
            error_log('No additional products found in request.');
        }
    }
    add_action('wp_ajax_smarty_additional_products', 'smarty_handle_additional_products_cart');
    add_action('wp_ajax_nopriv_smarty_additional_products', 'smarty_handle_additional_products_cart');
}

if (!function_exists('smarty_add_cart_item_data')) {
    function smarty_add_cart_item_data($cart_item_data, $product_id) {
        if (isset($_POST['additional_products']) && is_array($_POST['additional_products'])) {
            $cart_item_data['additional_products'] = array_map('intval', $_POST['additional_products']);
        }
        return $cart_item_data;
    }
    add_filter('woocommerce_add_cart_item_data', 'smarty_add_cart_item_data', 10, 2);
}

if (!function_exists('smarty_get_cart_item_from_session')) {
    function smarty_get_cart_item_from_session($cart_item, $values) {
        if (isset($values['additional_products'])) {
            $cart_item['additional_products'] = $values['additional_products'];
        }
        return $cart_item;
    }
    add_filter('woocommerce_get_cart_item_from_session', 'smarty_get_cart_item_from_session', 10, 2);
}

if (!function_exists('smarty_calculate_cart_item_price')) {
    function smarty_calculate_cart_item_price($cart_object) {
        foreach ($cart_object->get_cart() as $cart_item_key => $cart_item) {
            if (isset($cart_item['additional_products']) && is_array($cart_item['additional_products'])) {
                $additional_total = 0;
                foreach ($cart_item['additional_products'] as $additional_product_id) {
                    $additional_product = wc_get_product($additional_product_id);
                    if ($additional_product) {
                        $additional_total += $additional_product->get_price();
                    }
                }
                $cart_item['data']->set_price($cart_item['data']->get_price() + $additional_total);
            }
        }
    }
    add_action('woocommerce_before_calculate_totals', 'smarty_calculate_cart_item_price');
}

if (!function_exists('smarty_display_additional_products_in_cart')) {
    function smarty_display_additional_products_in_cart($item_data, $cart_item) {
        if (isset($cart_item['additional_products']) && is_array($cart_item['additional_products'])) {
            foreach ($cart_item['additional_products'] as $additional_product_id) {
                $product = wc_get_product($additional_product_id);
                if ($product) {
                    $item_data[] = array(
                        'key' => __('Additional Product', 'smarty-custom-upsell-products-design'),
                        'value' => $product->get_name() . ' (+ ' . wc_price($product->get_price()) . ')',
                    );
                }
            }
        }
        return $item_data;
    }
    add_filter('woocommerce_get_item_data', 'smarty_display_additional_products_in_cart', 10, 2);
}

if (!function_exists('smarty_add_order_item_meta')) {
    function smarty_add_order_item_meta($item_id, $values, $cart_item_key) {
        if (isset($values['additional_products']) && is_array($values['additional_products'])) {
            wc_add_order_item_meta($item_id, '_additional_products', $values['additional_products']);
        }
    }
    add_action('woocommerce_add_order_item_meta', 'smarty_add_order_item_meta', 10, 3);
}

if (!function_exists('smarty_display_additional_products_order_meta')) {
    function smarty_display_additional_products_order_meta($item_id, $item, $order) {
        $additional_products = wc_get_order_item_meta($item_id, '_additional_products', true);
        if ($additional_products && is_array($additional_products)) {
            echo '<p><strong>' . __('Additional Products', 'smarty-custom-upsell-products-design') . ':</strong></p>';
            echo '<ul>';
            foreach ($additional_products as $additional_product_id) {
                $product = wc_get_product($additional_product_id);
                if ($product) {
                    echo '<li>' . esc_html($product->get_name()) . ' (' . wc_price($product->get_price()) . ')</li>';
                }
            }
            echo '</ul>';
        }
    }
    add_action('woocommerce_order_item_meta_end', 'smarty_display_additional_products_order_meta', 10, 3);
}