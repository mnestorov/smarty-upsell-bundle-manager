<?php
/**
 * Plugin Name:             SM - Upsell Bundle Manager for WooCommerce
 * Plugin URI:              https://github.com/mnestorov/smarty-upsell-bundle-manager
 * Description:             Designed to change the product variation design for single products in WooCommerce.
 * Version:                 1.0.6
 * Author:                  Martin Nestorov
 * Author URI:              https://github.com/mnestorov
 * Text Domain:             smarty-upsell-bundle-manager
 * Domain Path:             /languages/
 * WC requires at least:    3.5.0
 * WC tested up to:         9.6.0
 * Requires Plugins:        woocommerce
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * HPOS Compatibility Declaration.
 *
 * This ensures that the plugin explicitly declares compatibility with 
 * WooCommerce's High-Performance Order Storage (HPOS).
 * 
 * HPOS replaces the traditional `wp_posts` and `wp_postmeta` storage system 
 * for orders with a dedicated database table structure, improving scalability 
 * and performance.
 * 
 * More details:
 * - WooCommerce HPOS Documentation: 
 *   https://developer.woocommerce.com/2022/09/12/high-performance-order-storage-in-woocommerce/
 * - Declaring Plugin Compatibility: 
 *   https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book#how-to-declare-compatibility
 */
add_action('before_woocommerce_init', function() {
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

if (!function_exists('smarty_ubm_enqueue_scripts')) {
    /**
     * Enqueues necessary scripts and styles for the admin settings page.
     *
     * @param string $hook_suffix The current admin page hook suffix.
     * @return void
     */
    function smarty_ubm_enqueue_admin_scripts($hook_suffix) {
        // Only add to the admin page of the plugin
        if ('woocommerce_page_smarty-ubm-settings' !== $hook_suffix) {
            return;
        }

        wp_enqueue_style('select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
        wp_enqueue_script('select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', array('jquery'), '4.0.13', true);
        wp_enqueue_script('smarty-ubm-admin-js', plugin_dir_url(__FILE__) . 'js/smarty-ubm-admin.js', array('jquery', 'select2'), '1.0.0', true);
        wp_enqueue_style('smarty-ubm-admin-css', plugin_dir_url(__FILE__) . 'css/smarty-ubm-admin.css', array(), '1.0.0');

        // Enqueue style and script for using the WordPress color picker.
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        wp_localize_script(
            'smarty-ubm-admin-js',
            'smartyUpsellBundleManager',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'siteUrl' => site_url(),
                'nonce'   => wp_create_nonce('smarty_upsell_bundle_nonce'),
            )
        );
    }
    add_action('admin_enqueue_scripts', 'smarty_ubm_enqueue_admin_scripts');
}

if (!function_exists('smarty_ubm_register_settings_page')) {
    /**
     * Registers a submenu page for plugin settings under WooCommerce.
     *
     * @return void
     */
    function smarty_ubm_register_settings_page() {
        add_submenu_page(
            'woocommerce',
            __('Upsell Bundle Manager | Settings', 'smarty-upsell-bundle-manager'),
            __('Upsell Bundle Manager', 'smarty-upsell-bundle-manager'),
            'manage_options',
            'smarty-ubm-settings',
            'smarty_settings_page_content',
        );
    }
    add_action('admin_menu', 'smarty_ubm_register_settings_page');
}

if (!function_exists('smarty_register_settings')) {
    /**
     * Registers settings, sections, and fields for the settings page.
     *
     * @return void
     */
    function smarty_ubm_register_settings() {
        // Register settings
        register_setting('smarty_settings_group', 'smarty_enable_upsell_styling');
        register_setting('smarty_settings_group', 'smarty_enable_additional_products');
        register_setting('smarty_settings_group', 'smarty_choose_additional_products');
        register_setting('smarty_settings_group', 'smarty_main_color');
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
        register_setting('smarty_settings_group', 'smarty_d_image_width');
        register_setting('smarty_settings_group', 'smarty_m_image_width');
        register_setting('smarty_settings_group', 'smarty_image_margin_top');
        register_setting('smarty_settings_group', 'smarty_image_margin_right');
        register_setting('smarty_settings_group', 'smarty_image_border_color');
        register_setting('smarty_settings_group', 'smarty_display_savings');
        register_setting('smarty_settings_group', 'smarty_debug_mode');
        register_setting('smarty_settings_group', 'smarty_debug_notices_enabled');
        register_setting('smarty_settings_group', 'smarty_free_delivery_text');
        register_setting('smarty_settings_group', 'smarty_free_delivery_amount');
        
        // Add settings sections
        add_settings_section('smarty_upsell_styling_section', 'Variable (Upsell) Products', 'smarty_upsell_styling_section_cb', 'smarty_settings_page');
        add_settings_section('smarty_additional_products_section', 'Additional Products', 'smarty_additional_products_section_cb', 'smarty_settings_page');
        add_settings_section('smarty_colors_section', 'Colors', 'smarty_colors_section_cb', 'smarty_settings_page');
        add_settings_section('smarty_image_sizes_section', 'Image Sizes', 'smarty_image_sizes_section_cb', 'smarty_settings_page');
        add_settings_section('smarty_font_sizes_section', 'Font Sizes', 'smarty_font_sizes_section_cb', 'smarty_settings_page');
        add_settings_section('smarty_free_delivery_section', 'Free Delivery', 'smarty_free_delivery_section_cb', 'smarty_settings_page');
        add_settings_section('smarty_currency_section', 'Currency Symbol', 'smarty_currency_section_cb', 'smarty_settings_page');
        add_settings_section('smarty_display_options_section', 'Display Options', 'smarty_display_options_section_cb', 'smarty_settings_page');
        add_settings_section('smarty_settings_section', 'Debug', 'smarty_settings_section_cb', 'smarty_settings_page');

        // Add settings fields for additional products features
        add_settings_field('smarty_enable_upsell_styling', 'Enable Variations (Upsell) Styling', 'smarty_checkbox_field_cb', 'smarty_settings_page', 'smarty_upsell_styling_section', ['id' => 'smarty_enable_upsell_styling']);
        add_settings_field('smarty_enable_additional_products', 'Enable/Disable Additional Products', 'smarty_checkbox_field_cb', 'smarty_settings_page', 'smarty_additional_products_section', ['id' => 'smarty_enable_additional_products']);
        add_settings_field('smarty_choose_additional_products', 'Choose Products', 'smarty_choose_additional_products_field_cb', 'smarty_settings_page', 'smarty_additional_products_section');
        
        // Add settings fields for colors
        add_settings_field('smarty_main_color', 'Main', 'smarty_color_field_cb', 'smarty_settings_page', 'smarty_colors_section', ['id' => 'smarty_main_color']);
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

        // Add settings fields for image sizes
        add_settings_field('smarty_d_image_width', 'Image Width (Desktop)', 'smarty_image_percent_size_field_cb', 'smarty_settings_page', 'smarty_image_sizes_section', ['id' => 'smarty_d_image_width']);
        add_settings_field('smarty_m_image_width', 'Image Width (Mobile)', 'smarty_image_percent_size_field_cb', 'smarty_settings_page', 'smarty_image_sizes_section', ['id' => 'smarty_m_image_width']);
        add_settings_field('smarty_image_margin_top', 'Image Margin Top (px)', 'smarty_image_px_size_field_cb', 'smarty_settings_page', 'smarty_image_sizes_section', ['id' => 'smarty_image_margin_top']);
        add_settings_field('smarty_image_margin_right', 'Image Margin Right (px)', 'smarty_image_px_size_field_cb', 'smarty_settings_page', 'smarty_image_sizes_section', ['id' => 'smarty_image_margin_right']);

        // Add settings fields for font sizes
        add_settings_field('smarty_price_font_size', 'Price', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_price_font_size']);
        add_settings_field('smarty_old_price_font_size', 'Old Price', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_old_price_font_size']);
        add_settings_field('smarty_variable_desc_font_size', 'Upsell Description', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_variable_desc_font_size']);
        add_settings_field('smarty_free_delivery_font_size', 'Free Delivery', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_free_delivery_font_size']);
        add_settings_field('smarty_label_1_font_size', 'Label 1', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_label_1_font_size']);
        add_settings_field('smarty_label_2_font_size', 'Label 2', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_label_2_font_size']);
        add_settings_field('smarty_savings_text_size', 'Savings Text', 'smarty_font_size_field_cb', 'smarty_settings_page', 'smarty_font_sizes_section', ['id' => 'smarty_savings_text_size']);
        
        // Add settings field for custom "free delivery" text and free delivery amount
        add_settings_field('smarty_free_delivery_text', 'Free Delivery Text', 'smarty_text_field_cb', 'smarty_settings_page', 'smarty_free_delivery_section', ['id' => 'smarty_free_delivery_text']);
        add_settings_field('smarty_free_delivery_amount', 'Free Delivery Amount', 'smarty_number_field_cb', 'smarty_settings_page', 'smarty_free_delivery_section');

        // Add settings fields for currency
        add_settings_field('smarty_currency_symbol_position', 'Position', 'smarty_currency_position_field_cb', 'smarty_settings_page', 'smarty_currency_section', ['id' => 'smarty_currency_symbol_position']);
        add_settings_field('smarty_currency_symbol_spacing', 'Spacing', 'smarty_currency_spacing_field_cb', 'smarty_settings_page', 'smarty_currency_section', ['id' => 'smarty_currency_symbol_spacing']);
    
        // Add settings field for display options
        add_settings_field('smarty_display_savings','Turn On/Off savings text', 'smarty_checkbox_field_cb', 'smarty_settings_page', 'smarty_display_options_section', ['id' => 'smarty_display_savings']);
        
        // Add settings fields for debug mode and for toggling debug notices
        add_settings_field('smarty_debug_mode', 'Debug Mode', 'smarty_checkbox_field_cb', 'smarty_settings_page', 'smarty_settings_section', ['id' => 'smarty_debug_mode']);
        add_settings_field('smarty_debug_notices_enabled', 'Enable Debug Notices', 'smarty_checkbox_field_cb', 'smarty_settings_page', 'smarty_settings_section', ['id' => 'smarty_debug_notices_enabled', 'label_for' => 'smarty_debug_notices_enabled']);
    }
    add_action('admin_init', 'smarty_ubm_register_settings');
}

if (!function_exists('smarty_upsell_styling_section_cb')) {
    /**
     * Callback for the upsell styling settings section description.
     *
     * @return void
     */
    function smarty_upsell_styling_section_cb() {
        echo '<p>Enable or disable variation styling of the variable products.</p>';
    }
}

if (!function_exists('smarty_additional_products_section_cb')) {
    /**
     * Callback for the additional products settings section description.
     *
     * @return void
     */
    function smarty_additional_products_section_cb() {
        echo '<p>Enable or disable additional products feature for single or variable products or/and choose your additional products.</p>';
    }
}

if (!function_exists('smarty_colors_section_cb')) {
    /**
     * Callback for the colors settings section description.
     *
     * @return void
     */
    function smarty_colors_section_cb() {
        echo '<p>Customize the colors for various elements in your WooCommerce upsell products.</p>';
    }
}

if (!function_exists('smarty_checkbox_field_cb')) {
    /**
     * Renders a checkbox field for the settings page.
     *
     * @param array $args Arguments for rendering the checkbox.
     * @return void
     */
    function smarty_checkbox_field_cb($args) {
        $option = get_option($args['id'], '0'); // Default to unchecked
        $checked = checked(1, $option, false);
        echo "<label class='smarty-toggle-switch'>";
        echo "<input type='checkbox' id='{$args['id']}' name='{$args['id']}' value='1' {$checked} />";
        echo "<span class='smarty-slider round'></span>";
        echo "</label>";
        // Display the description only for the debug mode checkbox
        if ($args['id'] == 'smarty_debug_mode') {
            echo '<p class="description">' . __('Copies specific template files from a plugin directory to a child theme directory in WordPress. <br><em><b>Important:</b> <span class="smarty-text-danger">Turn this to Off in production.</span></em>', 'smarty-upsell-bundle-manager') . '</p>';
        }
        // Display the description only for the enable upsell styling checkbox
        if ($args['id'] == 'smarty_enable_upsell_styling') {
            echo '<p class="description">' . __('Enable/Disable custom styling for the variations of the variable products. <br><em><b>Important:</b> <span class="smarty-text-danger">Turn this to Off if you have any problems with the styling of the variable products.</span></em>', 'smarty-upsell-bundle-manager') . '</p>';
        }
    }
}

if (!function_exists('smarty_color_field_cb')) {
    /**
     * Renders a color picker field for the settings page.
     *
     * @param array $args Arguments for rendering the color picker.
     * @return void
     */
    function smarty_color_field_cb($args) {
        $option = get_option($args['id'], '');
        echo '<input type="text" name="' . $args['id'] . '" value="' . esc_attr($option) . '" class="smarty-color-field" data-default-color="' . esc_attr($option) . '" />';
        if (in_array($args['id'], ['smarty_label_1_bg_color', 'smarty_label_1_color', 'smarty_label_2_bg_color', 'smarty_label_2_color'])) {
            echo '<p class="description">' . __('This field is available under the Product Edit page > Variable product > Variations tab > Edit Variation.', 'smarty-upsell-bundle-manager') . '</p>';
        }
    }
}

if (!function_exists('smarty_font_sizes_section_cb')) {
    /**
     * Callback for the font sizes settings section description.
     *
     * @return void
     */
    function smarty_font_sizes_section_cb() {
        echo '<p>Customize the font sizes for various elements in your WooCommerce upsell products.</p>';
    }
}

if (!function_exists('smarty_font_size_field_cb')) {
    /**
     * Renders a font size field as a slider for the settings page.
     *
     * @param array $args Arguments for rendering the font size slider.
     * @return void
     */
    function smarty_font_size_field_cb($args) {
        $option = get_option($args['id'], '14');
        echo '<input type="range" name="' . $args['id'] . '" min="10" max="30" value="' . esc_attr($option) . '" class="smarty-font-size-slider" />';
        echo '<span id="' . $args['id'] . '-value">' . esc_attr($option) . 'px</span>';
    }
}

if (!function_exists('smarty_image_sizes_section_cb')) {
    /**
     * Callback for the image sizes settings section description.
     *
     * @return void
     */
    function smarty_image_sizes_section_cb() {
        echo '<p>Customize the sizes for images in your WooCommerce upsell products.</p>';
    }
}

if (!function_exists('smarty_image_percent_size_field_cb')) {
    /**
     * Renders an image size field (percentage) as a slider for the settings page.
     *
     * @param array $args Arguments for rendering the slider.
     * @return void
     */
    function smarty_image_percent_size_field_cb($args) {
        $option = get_option($args['id'], '14');
        echo '<input type="range" name="' . $args['id'] . '" min="10" max="30" value="' . esc_attr($option) . '" class="smarty-image-percent-size-slider" />';
        echo '<span id="' . $args['id'] . '-value">' . esc_attr($option) . '%</span>';
    }
}

if (!function_exists('smarty_image_px_size_field_cb')) {
     /**
     * Renders an image size field (pixels)as a number for the settings page.
     *
     * @param array $args Arguments for rendering the slider.
     * @return void
     */
    function smarty_image_px_size_field_cb($args) {
        $option = get_option($args['id'], '14');
        echo '<input type="range" name="' . $args['id'] . '" min="10" max="30" value="' . esc_attr($option) . '" class="smarty-image-px-size-slider" />';
        echo '<span id="' . $args['id'] . '-value">' . esc_attr($option) . 'px</span>';
    }
}

if (!function_exists('smarty_free_delivery_section_cb')) {
    /**
     * Callback for the free delivery settings section description.
     *
     * @return void
     */
    function smarty_free_delivery_section_cb() {
        echo '<p>Use custom text and set the amount for free delivery.</p>';
    }
}

if (!function_exists('smarty_text_field_cb')) {
    /**
     * Renders a text field for the settings page.
     *
     * @param array $args Arguments for rendering the text field.
     * @return void
     */
    function smarty_text_field_cb($args) {
        $option = get_option($args['id'], ''); // Default is empty
        echo '<input type="text" name="' . $args['id'] . '" value="' . esc_attr($option) . '" />';
        echo '<p class="description">Set the text for free delivery.</p>';
    }
}

if (!function_exists('smarty_number_field_cb')) {
    /**
     * Renders a number field for the settings page.
     *
     * @return void
     */
    function smarty_number_field_cb() {
        $option = get_option('smarty_free_delivery_amount', '100'); // Default to 100
        echo '<input type="number" step="0.01" name="smarty_free_delivery_amount" value="' . esc_attr($option) . '" />';
        echo '<p class="description">Set the minimum amount required for free delivery.</p>';
    }
}

if (!function_exists('smarty_currency_section_cb')) {
    /**
     * Callback for the currency section description.
     *
     * @return void
     */
    function smarty_currency_section_cb() {
        echo '<p>Customize the currency symbol position and spacing for your WooCommerce upsell products.</p>';
    }
}

if (!function_exists('smarty_choose_additional_products_field_cb')) {
    /**
     * Renders a dropdown field for selecting additional products for upsell.
     *
     * This function uses Select2 for enhanced UI and allows selecting multiple products.
     *
     * @return void
     */
    function smarty_choose_additional_products_field_cb() {
        $saved_products = get_option('smarty_choose_additional_products', []);
        $saved_products = is_array($saved_products) ? $saved_products : [];
        $products = wc_get_products(array('limit' => -1)); // Get all products

        echo '<select name="smarty_choose_additional_products[]" multiple="multiple" id="smarty_choose_additional_products" style="width: 100%;">';
        foreach ($products as $product) {
            $selected = in_array($product->get_id(), $saved_products) ? 'selected' : '';
            echo '<option value="' . esc_attr($product->get_id()) . '" ' . esc_attr($selected) . '>' . esc_html($product->get_name()) . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . __('These products will appear as global upsell options. To assign specific upsell products to individual products, use the Product Edit page under the Additional Products tab.', 'smarty-upsell-bundle-manager') . '</p>'; ?>

        <script>
            jQuery(document).ready(function($) {
                $('#smarty_choose_additional_products').select2({
                    placeholder: "Select additional products",
                    allowClear: true
                });
            });
        </script>
        <?php
    }
}

if (!function_exists('smarty_currency_position_field_cb')) {
    /**
     * Renders a dropdown for currency position on the settings page.
     *
     * @param array $args Arguments for rendering the dropdown.
     * @return void
     */
    function smarty_currency_position_field_cb($args) {
        $option = get_option($args['id'], 'left');
        echo '<select name="' . $args['id'] . '">';
        echo '<option value="left"' . selected($option, 'left', false) . '>Left</option>';
        echo '<option value="right"' . selected($option, 'right', false) . '>Right</option>';
        echo '</select>';
    }
}

if (!function_exists('smarty_currency_spacing_field_cb')) {
    /**
     * Renders a dropdown for currency spacing on the settings page.
     *
     * @param array $args Arguments for rendering the dropdown.
     * @return void
     */
    function smarty_currency_spacing_field_cb($args) {
        $option = get_option($args['id'], 'no_space');
        echo '<select name="' . $args['id'] . '">';
        echo '<option value="space"' . selected($option, 'space', false) . '>With Space</option>';
        echo '<option value="no_space"' . selected($option, 'no_space', false) . '>Without Space</option>';
        echo '</select>';
    }
}

if (!function_exists('smarty_display_options_section_cb')) {
    /**
     * Callback for the display options section description.
     *
     * @return void
     */
    function smarty_display_options_section_cb() {
        echo '<p>Display options for the plugin.</p>';
    }
}

if (!function_exists('smarty_settings_section_cb')) {
    /**
     * Callback for the debug settings section description.
     *
     * @return void
     */
    function smarty_settings_section_cb() {
        echo '<p>Adjust debug settings for the plugin.</p>';
    }
}

if (!function_exists('smarty_checkbox_field_cb')) {
    /**
     * Renders a checkbox field with a custom toggle switch design for the settings page.
     *
     * @param array $args Arguments for rendering the checkbox.
     * @return void
     */
    function smarty_checkbox_field_cb($args) {
        $option = get_option($args['id'], '');
        $checked = checked(1, $option, false);
        echo "<label class='smarty-toggle-switch'>";
        echo "<input type='checkbox' id='{$args['id']}' name='{$args['id']}' value='1' {$checked} />";
        echo "<span class='smarty-slider round'></span>";
        echo "</label>";
        // Display the description only for the debug mode checkbox
        if ($args['id'] == 'smarty_debug_mode') {
            echo '<p class="description">' . __('Copies specific template files from a plugin directory to a child theme directory in WordPress. <br><em><b>Important:</b> <span class="smarty-text-danger">Turn this to Off in production.</span></em>', 'smarty-upsell-bundle-manager') . '</p>';
        }
    }
}

if (!function_exists('smarty_settings_page_content')) {
    /**
     * Outputs the content for the settings page.
     *
     * @return void
     */
    function smarty_settings_page_content() {
        ?>
       <div class="wrap">
            <h1><?php _e('Upsell Bundle Manager | Settings', 'smarty-upsell-bundle-manager'); ?></h1>
            <div id="smarty-ubm-settings-container">
                <div>
                    <form method="post" action="options.php">
                        <?php
                        settings_fields('smarty_settings_group');
                        do_settings_sections('smarty_settings_page');
                        ?>
                        <?php submit_button(); ?>
                    </form>
                </div>
                <div id="smarty-ubm-tabs-container">
                    <div>
                        <h2 class="smarty-ubm-nav-tab-wrapper">
                            <a href="#smarty-ubm-documentation" class="smarty-ubm-nav-tab smarty-ubm-nav-tab-active"><?php esc_html_e('Documentation', 'smarty-upsell-bundle-manager'); ?></a>
                            <a href="#smarty-ubm-changelog" class="smarty-ubm-nav-tab"><?php esc_html_e('Changelog', 'smarty-upsell-bundle-manager'); ?></a>
                        </h2>
                        <div id="smarty-ubm-documentation" class="smarty-ubm-tab-content active">
                            <div class="smarty-ubm-view-more-container">
                                <p><?php esc_html_e('Click "View More" to load the plugin documentation.', 'smarty-upsell-bundle-manager'); ?></p>
                                <button id="smarty-ubm-load-readme-btn" class="button button-primary">
                                    <?php esc_html_e('View More', 'smarty-upsell-bundle-manager'); ?>
                                </button>
                            </div>
                            <div id="smarty-ubm-readme-content" style="margin-top: 20px;"></div>
                        </div>
                        <div id="smarty-ubm-changelog" class="smarty-ubm-tab-content">
                            <div class="smarty-ubm-view-more-container">
                                <p><?php esc_html_e('Click "View More" to load the plugin changelog.', 'smarty-upsell-bundle-manager'); ?></p>
                                <button id="smarty-ubm-load-changelog-btn" class="button button-primary">
                                    <?php esc_html_e('View More', 'smarty-upsell-bundle-manager'); ?>
                                </button>
                            </div>
                            <div id="smarty-ubm-changelog-content" style="margin-top: 20px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .wp-color-result { vertical-align: middle; }
        </style>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.smarty-color-field').wpColorPicker();

                // Update the font size and image width value display
                $('.smarty-font-size-slider, .smarty-image-percent-size-slider, .smarty-image-px-size-slider').on('input', function() {
                    var sliderId = $(this).attr('name');
                    var unit;

                    // Determine the unit based on the class
                    if ($(this).hasClass('smarty-image-percent-size-slider')) {
                        unit = '%';
                    } else if ($(this).hasClass('smarty-font-size-slider') || $(this).hasClass('smarty-image-px-size-slider')) {
                        unit = 'px';
                    } else {
                        unit = ''; // Default to no unit if not identified
                    }

                    // Update the value display with the correct unit
                    $('#' + sliderId + '-value').text($(this).val() + unit);
                });
            });
        </script>
        <?php
    }
}

if (!function_exists('smarty_ubm_load_readme')) {
    /**
     * AJAX handler to load and parse the README.md content.
     */
    function smarty_ubm_load_readme() {
        check_ajax_referer('smarty_upsell_bundle_nonce', 'nonce');
    
        if (!current_user_can('manage_options')) {
            wp_send_json_error('You do not have sufficient permissions.');
        }
    
        $readme_path = plugin_dir_path(__FILE__) . 'README.md';
        if (file_exists($readme_path)) {
            // Include Parsedown library
            if (!class_exists('Parsedown')) {
                require_once plugin_dir_path(__FILE__) . 'libs/Parsedown.php';
            }
    
            $parsedown = new Parsedown();
            $markdown_content = file_get_contents($readme_path);
            $html_content = $parsedown->text($markdown_content);
    
            // Remove <img> tags from the content
            $html_content = preg_replace('/<img[^>]*>/', '', $html_content);
    
            wp_send_json_success($html_content);
        } else {
            wp_send_json_error('README.md file not found.');
        }
    }    
    add_action('wp_ajax_smarty_ubm_load_readme', 'smarty_ubm_load_readme');
}

if (!function_exists('smarty_ubm_load_changelog')) {
    /**
     * AJAX handler to load and parse the CHANGELOG.md content.
     */
    function smarty_ubm_load_changelog() {
        check_ajax_referer('smarty_upsell_bundle_nonce', 'nonce');
    
        if (!current_user_can('manage_options')) {
            wp_send_json_error('You do not have sufficient permissions.');
        }
    
        $changelog_path = plugin_dir_path(__FILE__) . 'CHANGELOG.md';
        if (file_exists($changelog_path)) {
            if (!class_exists('Parsedown')) {
                require_once plugin_dir_path(__FILE__) . 'libs/Parsedown.php';
            }
    
            $parsedown = new Parsedown();
            $markdown_content = file_get_contents($changelog_path);
            $html_content = $parsedown->text($markdown_content);
    
            wp_send_json_success($html_content);
        } else {
            wp_send_json_error('CHANGELOG.md file not found.');
        }
    }
    add_action('wp_ajax_smarty_ubm_load_changelog', 'smarty_ubm_load_changelog');
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
        if ($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'smarty-ubm-settings') {
            
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
        // Check if the checkbox should be visible
        $show_checkbox = get_option('smarty_enable_upsell_styling', '0') === '1';
        
        if (!$show_checkbox) {
            return; // Exit if checkbox should not be visible
        }

        $attr_id = isset($_GET['edit']) && $_GET['page'] === 'product_attributes' ? (int) $_GET['edit'] : false;
        $attr_up_sell = smarty_get_attr_fields($attr_id);

        // Sanitization is crucial here for security
        $checked = checked('1', $attr_up_sell, false);

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
    add_action('woocommerce_after_edit_attribute_fields', 'smarty_after_edit_attribute_fields', 10, 0);
}

if (!function_exists('smarty_woocommerce_attribute_updated')) {
    /**
     * Handles saving of the custom attribute fields on update (HPOS compatible).
     *
     * @param int $attribute_id ID of the attribute being updated.
     * @param array $attribute Array of new attribute data.
     * @param string $old_attribute_name Old name of the attribute.
     * @return void Saves the custom field data but does not return a value.
     */
    function smarty_woocommerce_attribute_updated($attribute_id, $attribute, $old_attribute_name) {
        if (isset($_POST['up_sell_design']) && $_POST['up_sell_design'] == 1) {
            update_term_meta($attribute_id, '_up_sell_design', 1);
        } else {
            update_term_meta($attribute_id, '_up_sell_design', 0);
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
     * @param string $wc_variable_price Current price HTML.
     * @param WC_Product $product WooCommerce product object.
     * @return string Modified price HTML.
     */
    function smarty_variable_price_range($wc_variable_price, $product) {
        $prefix = '';

        // Initialize variables
        $wc_variable_min_price = $product->get_variation_price('min', true);
        $wc_variable_max_price = $product->get_variation_price('max', true);
        $wc_variable_reg_min_price = $product->get_variation_regular_price('min', true);
        $wc_variable_min_sale_price = $product->get_variation_sale_price('min', true);

        $wc_variable_price = ($wc_variable_min_sale_price == $wc_variable_reg_min_price) 
            ? wc_price($wc_variable_reg_min_price) 
            : wc_price($wc_variable_min_sale_price);

        // Return the price or the price range based on the condition
        return ($wc_variable_min_price == $wc_variable_max_price) ? $wc_variable_price : sprintf('%s%s', $prefix, $wc_variable_price);
    }
    add_filter('woocommerce_variable_sale_price_html', 'smarty_variable_price_range', 10, 2);
    add_filter('woocommerce_variable_price_html', 'smarty_variable_price_range', 10, 2);
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

if (!function_exists('smarty_check_variation_free_delivery')) {
    /**
     * Checks if a variation qualifies for free delivery.
     *
     * @param array $variation_data Variation data array.
     * @param WC_Product $product WooCommerce product object.
     * @param WC_Product_Variation $variation WooCommerce variation object.
     * @return array Modified variation data array.
     */
    function smarty_check_variation_free_delivery($variation_data, $product, $variation) {
        // Get the free delivery threshold
        $minimum_free_delivery_amount = smarty_free_delivery_amount();

        // Get the variation price
        $variation_price = $variation->get_price();

        // Determine if the variation qualifies for free delivery
        if ($variation_price >= $minimum_free_delivery_amount) {
            $variation_data['free_delivery'] = true;
        } else {
            $variation_data['free_delivery'] = false;
        }

        return $variation_data;
    }
    add_filter('woocommerce_available_variation', 'smarty_check_variation_free_delivery', 10, 3);
}

if (!function_exists('smarty_free_delivery_amount')) {
    /**
     * Calculates the amount required for free delivery.
     *
     * @return float Minimum amount required for free delivery.
     */
    function smarty_free_delivery_amount() {
        // Check if free delivery amount is set in the plugin options
        $plugin_free_delivery_amount = get_option('smarty_free_delivery_amount', '');

        if (is_numeric($plugin_free_delivery_amount) && $plugin_free_delivery_amount > 0) {
            return (float) $plugin_free_delivery_amount;
        }

        // Default behavior: Calculate dynamically
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
        return ($minimum_free_delivery_amount !== PHP_INT_MAX) ? $minimum_free_delivery_amount : 100;
    }
}

register_activation_hook(__FILE__, function() {
    if (get_option('smarty_free_delivery_amount') === false) {
        update_option('smarty_free_delivery_amount', '100'); // Default to 100
    }
});

if (!function_exists('smarty_add_custom_fields_to_variations')) {
    /**
     * Adds custom fields to WooCommerce variation forms in the admin panel.
     *
     * @param int $loop Loop index.
     * @param array $variation_data Data of the current variation.
     * @param WP_Post $variation Current variation object.
     * @return void
     */
    function smarty_add_custom_fields_to_variations($loop, $variation_data, $variation) {
        $product_variation = wc_get_product($variation->ID); // Use WooCommerce CRUD

        // Custom field for Label 1
        woocommerce_wp_text_input(array(
            'id' => 'smarty_label_1_' . $variation->ID,
            'name' => 'smarty_label_1[' . $variation->ID . ']',
            'label' => __('Label 1', 'smarty-upsell-bundle-manager'), 
            'description' => __('Enter the label for example: `Best Seller`', 'smarty-upsell-bundle-manager'),
            'desc_tip' => true,
            'value' => $product_variation->get_meta('_smarty_label_1', true),
            'wrapper_class' => 'form-row form-row-first'
        ));

        // Custom field for Label 2
        woocommerce_wp_text_input(array(
            'id' => 'smarty_label_2_' . $variation->ID,
            'name' => 'smarty_label_2[' . $variation->ID . ']',
            'label' => __('Label 2', 'smarty-upsell-bundle-manager'), 
            'description' => __('Enter the label for example: `Best Value`', 'smarty-upsell-bundle-manager'),
            'desc_tip' => true,
            'value' => $product_variation->get_meta('_smarty_label_2', true),
            'wrapper_class' => 'form-row form-row-last'
        ));
    }
    add_action('woocommerce_product_after_variable_attributes', 'smarty_add_custom_fields_to_variations', 10, 3);
}

if (!function_exists('smarty_save_custom_fields_variations')) {
    /**
     * Saves custom fields for WooCommerce product variations.
     * 
     * This function handles the saving of data entered into the custom fields 
     * ('Label 1' and 'Label 2') for each product variation.
     *
     * @param int $variation_id Variation ID.
     * @param int $i Loop index.
     * @return void
     */
    function smarty_save_custom_fields_variations($variation_id, $i) {
        $product_variation = wc_get_product($variation_id); // Use WooCommerce CRUD functions
    
        if (!$product_variation) {
            return;
        }
    
        // Save Best Seller Label (Label 1)
        if (!empty($_POST['smarty_label_1'][$variation_id])) {
            $product_variation->update_meta_data('_smarty_label_1', sanitize_text_field($_POST['smarty_label_1'][$variation_id]));
        } else {
            $product_variation->delete_meta_data('_smarty_label_1');
        }
    
        // Save Best Value Label (Label 2)
        if (!empty($_POST['smarty_label_2'][$variation_id])) {
            $product_variation->update_meta_data('_smarty_label_2', sanitize_text_field($_POST['smarty_label_2'][$variation_id]));
        } else {
            $product_variation->delete_meta_data('_smarty_label_2');
        }
    
        $product_variation->save(); // Save all changes
    }
    add_action('woocommerce_save_product_variation', 'smarty_save_custom_fields_variations', 10, 2);
}

if (!function_exists('smarty_admin_custom_css')) {
    /**
     * Adds custom CSS for the admin panel.
     *
     * @return void
     */
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

                /* Order Meta */
                .woocommerce_order_items .bundle-items {
                    width: 300px;
                    padding: 0 5px 0 5px;
                    border: 1px solid #bde5b9;
                    border-radius: 5px;
                    background: #edffeb;
                    position: relative;
                }
                
                .woocommerce_order_items .bundle-items .dashicons.dashicons-archive {
                    position: absolute;
                    top: 20%;
                    right: 85px;
                    font-size: 80px;
                    transform: translateY(-50%) rotate(-15deg);
                    color: #bde5b9;
                }

                @media only screen and (max-width: 769px) {
                    .woocommerce_order_items .bundle-items .dashicons.dashicons-archive {
                        display: none;
                    } 
                }

                .woocommerce_order_items .bundle-items ul {
                    list-style-type: none !important; 
                    padding: 0 5px;
                }

                .woocommerce_order_items .bundle-items ul li {
                    font-weight: normal;
                }

                .woocommerce_order_items .bundle-items p strong,
                .woocommerce_order_items .bundle-items ul li span  {
                    color: #888888;
                }

                .woocommerce_order_items .bundle-items ul li .woocommerce-Price-amount.amount bdi,
                .woocommerce_order_items .bundle-items ul li .woocommerce-Price-currencySymbol {
                    color: #3c434a;
                }

                /* Is Bundle column */
                table.wp-list-table .column-is_bundle { 
                    width: 1.5%; 
                }

                .column-is_bundle .dashicons-archive {
                    font-size: 20px;
                    color: #9cb576;
                }

                .column-is_bundle .dashicons {
                    margin: 0 auto;
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
        $upsell_styling_enabled = get_option('smarty_enable_upsell_styling', '0') === '1';

        $main_color = get_option('smarty_main_color', '#ffffff');
        $active_bg_color = get_option('smarty_active_bg_color', 'rgba(210, 184, 133, 0.3)');
        $active_border_color = get_option('smarty_active_border_color', '#000000');
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
        $d_image_width = get_option('smarty_d_image_width', '16');
        $m_image_width = get_option('smarty_m_image_width', '16');
        $image_margin_top = get_option('smarty_image_margin_top', '18');
        $image_margin_right = get_option('smarty_image_margin_right', '10');
        $image_border_color = get_option('smarty_image_border_color', '#000000');
        
        if (is_product()) { ?>
            <?php if ($upsell_styling_enabled) { ?>
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
                        font-size: 16px; /* TODO: Make it dynamic */
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
                        visibility: visible !important; /* Ensure prices are visible */
                        word-spacing: normal !important;
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
                        width: <?php echo esc_attr($d_image_width); ?>%;
                        float: right;
                        margin-top: <?php echo esc_attr($image_margin_top); ?>px;
                        margin-right: <?php echo esc_attr($image_margin_right); ?>px;
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

                    @media only screen and (max-width: 600px) {
						.main_title_wrap .var_txt {
							width: 80%;
							font-size: 15px;
							line-height: 1.3;
						}

                        .upsell-container .variable_img {
                            width: <?php echo esc_attr($m_image_width); ?>%;
                        }
					}
                </style>
            <?php } ?>

            <style>
                .savings-text {
                    font-weight: normal;
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
					margin-bottom: 20px;
    				padding: 10px 15px 0 15px;
					border: 2px dashed rgba(0, 0, 0, 0.105);
					border-radius: 5px;
					background: rgba(249, 247, 246, 0.3);
				}

                .additional-products p {
                    text-align: center;
                    line-height: normal;
                    font-weight: inherit;
					margin: 5px 0 10px;
                }

                .additional-products label {
                    display: flex;
                    align-items: center;
                    padding: 5px 10px;
    				margin-bottom: 20px;
                    background: #ffffff;
                    border: 2px solid #ffffff00;
                    border-radius: 5px;
                    box-shadow: 0px 3px 11px -2px rgba(0, 0, 0, 0.55);
                    -webkit-box-shadow: 0px 3px 11px -2px rgba(0, 0, 0, 0.55);
                    -moz-box-shadow: 0px 3px 11px -2px rgba(0, 0, 0, 0.55);
                }

                .additional-products label.active, 
                .additional-products label:hover {
                    background: <?php echo esc_attr($active_bg_color); ?>;
					border: 2px solid <?php echo esc_attr($active_border_color); ?>;
					transition: all 0.3s ease-in;
                }

                .additional-products input[type="checkbox"] {
                    width: 20px;
                    height: 20px;
                    border-radius: 3px;
                    background-color: rgba(249, 247, 246, 0.9);
                    border: 1px solid <?php echo esc_attr($active_border_color); ?>;
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
                    background-color: <?php echo esc_attr($main_color); ?>;
                    border-radius: 2px;
                    display: none;
                }

                .additional-products input[type="checkbox"]:checked::after {
                    display: block;
                }

                .additional-product-image {
                    width: <?php echo esc_attr($d_image_width); ?>%;
    				border: 1px solid <?php echo esc_attr($image_border_color); ?>;
                    border-radius: 5px;
                    margin-right: 10px;
                }

                .additional-product-title {
                    margin-right: auto;
                }

                .additional-product-regular-price > .woocommerce-Price-amount.amount bdi {
                    font-size: <?php echo esc_attr($old_price_font_size) . 'px'; ?>;
                    color: <?php echo esc_attr($old_price_color); ?>;
                    text-decoration: line-through;
                }

                .additional-product-sale-price > .woocommerce-Price-amount.amount bdi {
                    font-size: <?php echo esc_attr($price_font_size) . 'px'; ?>;
                    color: <?php echo esc_attr($price_color); ?>;
                    font-weight: bold;
                }

                /* Ribbon styles */
                .additional-products-title {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    position: relative;
                }

                .additional-products-title p {
                    margin: 0;
                    padding-right: 10px; /* Adjust spacing as needed */
                    font-weight: bold;
                    text-transform: uppercase;
                }

                .ribbon {
                    background: #FFF3D1;
                    color: #333333;
                    padding: 5px 5px;
					font-size: 13px;
					font-weight: bold;
					position: relative;
					top: 0;
					left: -5px;
                    height: 24px; /* Adjust height as needed */
                    line-height: 25px; /* Adjust line-height as needed */
                }

                .ribbon:before, .ribbon:after {
                    content: "";
                    position: absolute;
                    display: block;
                    top: 0;
                    bottom: 0;
                    width: 0;
                    height: 0;
                    border-style: solid;
                    border-color: transparent;
                }

                .ribbon:before {
                    left: -10px;
                    border-width: 12.5px 10px;
                    border-right-color: #FFF3D1;
                }

                .ribbon:after {
                    right: -10px;
                    border-width: 12.5px 10px;
                    border-left-color: transparent;
                    border-top-color: #FFF3D1;
                    border-bottom-color: #FFF3D1;
                }

                .ribbon span {
                    position: relative;
                    top: -5px;
                }
				
				@media only screen and (max-width: 768px) {
				  	.ribbon {
				   		display: none;
					}
				}

                @media only screen and (max-width: 600px) {
                    .additional-product-image {
                        width: <?php echo esc_attr($m_image_width); ?>%;
                    }
				}
            </style><?php
        }
    }
    add_action('wp_head', 'smarty_public_custom_css');    
}

if (!function_exists('smarty_custom_thankyou_page_css')) {
	function smarty_custom_thankyou_page_css() {
		if (is_order_received_page()) {
			echo '<style>
				.woocommerce-order-received .bundle-items > .dashicons.dashicons-archive {
					display: none;
				}
			</style>';
		}
	}
	add_action('wp_head', 'smarty_custom_thankyou_page_css');
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
                    $('.woocommerce_variation').each(function() {
                        var labelOneInput = $(this).find('[id^="smarty_label_1_"]');
                        var labelTwoInput = $(this).find('[id^="smarty_label_2_"]');

                        if (labelOneInput.val().trim() !== '') {
                            labelTwoInput.prop('disabled', true);
                        } else {
                            labelTwoInput.prop('disabled', false);
                        }

                        if (labelTwoInput.val().trim() !== '') {
                            labelOneInput.prop('disabled', true);
                        } else {
                            labelOneInput.prop('disabled', false);
                        }
                    });
                }

                // Run toggle when WooCommerce variations are loaded
                $(document).on('woocommerce_variations_loaded', function() {
                    toggleLabelInputs();
                });

                // Allow user to clear and re-enable fields
                $(document).on('input', '[id^="smarty_label_1_"], [id^="smarty_label_2_"]', function() {
                    toggleLabelInputs();
                });

                // Ensure fields are toggled when variations are updated
                $(document).on('change', '[id^="smarty_label_1_"], [id^="smarty_label_2_"]', function() {
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
        $youSaveText = esc_js(__('you save', 'smarty-upsell-bundle-manager')); // translatable text for 'you save'

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
				var currencyCode = '<?php echo get_woocommerce_currency(); ?>'; // Get the current currency code (like CZK, HUF)
				var skipFormattingCurrencies = ['CZK', 'HUF'];
				
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
				
				// Function to remove .00 if the currency is CZK or HUF
				function hideDecimalsForCZKHUF() {
					if (skipFormattingCurrencies.includes(currencyCode) && $('body').hasClass('single-product')) {
						// Target only prices in specific containers
						$('.upsell_select_box .price, .upsell-container .price').each(function() {
							var priceText = $(this).text();
							// Remove .00 from prices using regex
							var newText = priceText.replace(/(\.00)/g, '');
							$(this).text(newText);
						});
					}
				}
			
                function formatSavings(regularPrice, salePrice) {
					if (!displaySavings || skipFormattingCurrencies.includes(currencyCode)) {
						return ''; // if disabled or for CZK/HUF, return empty string
					}
					
                    var savings = regularPrice - salePrice;
									
                    // Remove <bdi> tags from formatted savings
                    var formattedSavings = formatPrice(savings.toFixed(2), false);
                    return '<span class="savings-text" style="font-size:' + savingsTextSize + '; color:' + savingsTextColor + ';">(' + youSaveText + ' ' + formattedSavings + ')</span>';
                }

                setActiveUpsell();

                $('.main_title_wrap').on('click', function() {
                    $('.main_title_wrap').removeClass('active');
                    $(this).addClass('active');
                });

                // Apply the price formatting
				$('.upsell-container .price:not(.old_price)').each(function() {
					if (skipFormattingCurrencies.includes(currencyCode)) { return ''; }
					
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
				
				// Apply .00 hiding for CZK/HUF after rendering the prices
				hideDecimalsForCZKHUF(); // only on single product pages
                
                // Update additional product prices and savings
                $('input[name="additional_products[]"]').each(function() {
                    var regularPrice = parseFloat($(this).data('regular-price'));
            		var salePrice = parseFloat($(this).data('sale-price'));

                    //console.log("Debug - Additional Product:", {
                    //    id: $(this).val(),
                    //    regularPrice: regularPrice,
                    //    salePrice: salePrice
                    //});

                    if (salePrice && regularPrice) {
						var formattedRegularPrice = formatPrice(regularPrice.toFixed(2));
						var formattedSalePrice = formatPrice(salePrice.toFixed(2));
						var savingsMessage = formatSavings(regularPrice, salePrice);

						$(this).closest('label').find('.additional-product-price').html('<span class="price old_price">' + formattedRegularPrice + '</span> <span class="price">' + formattedSalePrice + '</span> ' + savingsMessage);
					} else if (regularPrice) {
						var formattedPrice = formatPrice(regularPrice.toFixed(2));
						$(this).closest('label').find('.additional-product-price').html('<span class="price">' + formattedPrice + '</span>');
					}
                });

                $(document).on('found_variation', function(event, variation) {
                    if (variation.free_delivery) {
                        $('.free-delivery-label').show(); // Show the label
                    } else {
                        $('.free-delivery-label').hide(); // Hide the label
                    }
                });
                
                $('form.cart').on('submit', function(e) {
                    e.preventDefault(); // Prevent normal form submit
                    var $form = $(this);
                    var additionalProducts = $('input[name="additional_products[]"]:checked').map(function() {
                        return $(this).val();
                    }).get();
                    var variationId = $form.find('input[name="variation_id"]').val() || 0;
                    var productId = $form.find('input[name="product_id"]').val();
                    var quantity = $form.find('input[name="quantity"]').val() || 1;

                    // Build variation object
                    var variationData = {};
                    $form.find('.variations select').each(function() {
                        variationData[$(this).attr('name')] = $(this).val();
                    });

                    // ✅ MAIN PRODUCT AJAX
                    $.ajax({
                        url: wc_add_to_cart_params.wc_ajax_url.replace('%%endpoint%%', 'add_to_cart'),
                        type: 'POST',
                        data: {
                            product_id: productId,
                            quantity: quantity,
                            variation_id: variationId,
                            variation: variationData
                        },
                        success: function(response) {
                            console.log('Main product added');

                            if (additionalProducts.length > 0) {
                                // ✅ THEN ADD ADDITIONAL PRODUCTS
                                $.ajax({
                                    url: wc_add_to_cart_params.ajax_url,
                                    type: 'POST',
                                    data: {
                                        action: 'smarty_choose_additional_products',
                                        additional_products: additionalProducts
                                    },
                                    success: function(response) {
                                        console.log('Additional products added');
                                        $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);
                                    },
                                    error: function(response) {
                                        console.log('Error adding additional products');
                                    }
                                });
                            } else {
                                // Just update cart
                                $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);
                            }
                        },
                        error: function(response) {
                            console.log('Error adding main product');
                        }
                    });
                });

                function updateActiveState() {
                    $('input[name="additional_products[]"]').each(function() {
                        if ($(this).is(':checked')) {
                            $(this).closest('label').addClass('active');
                        } else {
                            $(this).closest('label').removeClass('active');
                        }
                    });
                }

                // Initial update to handle any pre-checked boxes
                updateActiveState();

                // Update on checkbox change
                $('body').on('change', 'input[name="additional_products[]"]', function() {
                    updateActiveState();
                });
            });
        </script>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				// Prefetch WooCommerce cart fragments early
				$.ajax({
					type: 'POST',
					url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'get_refreshed_fragments'),
					success: function(data) {
						if (data && data.fragments) {
							// Store the fragments in memory for later use if needed
							window.prefetchedCartFragments = data.fragments;
							window.prefetchedCartHash = data.cart_hash;
							console.log('WooCommerce fragments prefetched');
						}
					}
				});
			});
		</script>

        <?php
    }
    add_action('wp_head', 'smarty_public_custom_js');
}

if (!function_exists('smarty_update_total_price')) {
    function smarty_update_total_price() {
        $amount_text = esc_js(__('Amount: ', 'smarty-upsell-bundle-manager'));
        $currency_symbol = get_woocommerce_currency_symbol();
        $currency_position = get_option('smarty_currency_symbol_position', 'left');
        $currency_spacing = get_option('smarty_currency_symbol_spacing', 'no_space');
        $decimal_separator = wc_get_price_decimal_separator();
        $thousand_separator = wc_get_price_thousand_separator();
        $decimals = wc_get_price_decimals();
        $spacing = ($currency_spacing === 'space') ? ' ' : '';
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var amountText = '<?php echo $amount_text; ?>';
                var currencySymbol = '<?php echo $currency_symbol; ?>';
                var currencyPosition = '<?php echo $currency_position; ?>';
                var currencySpacing = '<?php echo $spacing; ?>';
                var decimalSeparator = '<?php echo $decimal_separator; ?>';
                var thousandSeparator = '<?php echo $thousand_separator; ?>';
                var decimals = <?php echo $decimals; ?>;

                function formatPrice(price) {
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

                function updateTotalPrice() {
                    var basePrice = parseFloat($('.single_variation_wrap .woocommerce-variation-price').data('base-price')) || 0;
                    var additionalPrice = 0;
                    $('input[name="additional_products[]"]:checked').each(function() {
                        var regularPrice = parseFloat($(this).data('regular-price')) || 0;
                        var salePrice = parseFloat($(this).data('sale-price')) || 0;
                        var price = salePrice || regularPrice;
                        additionalPrice += price;
                    });
                    var totalPrice = basePrice + additionalPrice;
                    $('.single_variation_wrap .woocommerce-variation-price').html(amountText + '<strong>' + formatPrice(totalPrice) + '</strong>');
                }

                $('body').on('change', 'input[name="additional_products[]"]', function() {
                    updateTotalPrice();
                });

                $('body').on('show_variation', function(event, variation) {
                    var basePrice = parseFloat(variation.display_price) || 0;
                    $('.single_variation_wrap .woocommerce-variation-price').data('base-price', basePrice);
                    updateTotalPrice();
                });
            });
        </script>
        <?php
    }
}

if (!function_exists('smarty_additional_products_data_fields')) {
    function smarty_additional_products_data_fields() {
        global $post;
        ?>
        <div id='smarty_additional_products_data' class='panel woocommerce_options_panel'>
            <?php
            woocommerce_wp_text_input(
                array(
                    'id' => '_smarty_order_ids',
                    'label' => __('Additional Products Order IDs', 'smarty-upsell-bundle-manager'),
                    'desc_tip' => 'true',
                    'description' => __('Enter the order IDs associated with this product.', 'smarty-upsell-bundle-manager'),
                )
            );

            // Additional field for choosing products (can use select2 or similar for better UI)
            $product_ids = get_post_meta($post->ID, '_smarty_additional_products', true);
            $product_ids = !empty($product_ids) ? (array) $product_ids : [];

            echo '<p class="form-field"><label for="smarty_additional_products">' . __('Choose Additional Products', 'smarty-upsell-bundle-manager') . '</label>';
            echo '<select multiple="multiple" name="smarty_additional_products[]" id="smarty_additional_products" class="wc-product-search" style="width: 50%;" data-placeholder="' . esc_attr__('Search for a product…', 'woocommerce') . '" data-action="woocommerce_json_search_products_and_variations">';
            
            // If no products, do not populate any options
            if (!empty($product_ids)) {
                $products = wc_get_products(array('include' => $product_ids));
                foreach ($products as $product) {
                    echo '<option value="' . esc_attr($product->get_id()) . '" selected="selected">' . esc_html($product->get_name()) . '</option>';
                }
            }
            
            echo '</select></p>';
            ?>
        </div>
        <?php
    }
}

if (!function_exists('smarty_save_custom_order_ids_field')) {
    /**
     * Adds custom meta data for order IDs to the WooCommerce product meta.
     *
     * @param int $post_id The product post ID.
     * @return void
     */
    function smarty_save_custom_order_ids_field($post_id) {
        $order_ids = isset($_POST['_smarty_order_ids']) ? sanitize_text_field($_POST['_smarty_order_ids']) : '';
        update_post_meta($post_id, '_smarty_order_ids', $order_ids);
    }
}

if (!function_exists('smarty_add_additional_products_tab')) {
    /**
     * Adds a custom tab for additional products in the WooCommerce product data meta box.
     *
     * @param array $tabs The existing product data tabs.
     * @return array Modified product data tabs array including the additional products tab.
     */
    function smarty_add_additional_products_tab($tabs) {
        $tabs['smarty_additional_products'] = array(
            'label'    => __('Additional Products', 'smarty-upsell-bundle-manager'),
            'target'   => 'smarty_additional_products_data',
            'class'    => array('show_if_simple', 'show_if_variable'),
            'priority' => 21,
        );
        return $tabs;
    }
}

if (!function_exists('smarty_add_additional_products_checkbox')) {
    /**
     * Displays checkboxes for selecting additional products on the single product page.
     *
     * Fetches and displays available additional products as options for the current product.
     *
     * @return void
     */
    function smarty_add_additional_products_checkbox() {
            
        global $product;

        // Fetch the saved order of product IDs and convert to an array
        $order_ids = get_post_meta($product->get_id(), '_smarty_order_ids', true);
        $order_ids_array = !empty($order_ids) ? explode(',', $order_ids) : [];

        // First, attempt to get product-specific additional products
        $product_specific_ids = get_post_meta($product->get_id(), '_smarty_additional_products', true);
        $additional_products_ids = !empty($product_specific_ids) ? $product_specific_ids : get_option('smarty_choose_additional_products', []);

        // Ensure it's an array, if not convert it
        if (!is_array($additional_products_ids)) {
            $additional_products_ids = !empty($additional_products_ids) ? explode(',', $additional_products_ids) : [];
        }

        if (!empty($additional_products_ids)) {
            // Fetch products using IDs
            $additional_products = wc_get_products(array(
                'include' => $additional_products_ids,
                'status' => 'publish',
                'limit' => -1,
            ));

            // Sort the additional products according to the order in $order_ids_array
            usort($additional_products, function($a, $b) use ($order_ids_array) {
                $pos_a = array_search($a->get_id(), $order_ids_array);
                $pos_b = array_search($b->get_id(), $order_ids_array);
                return $pos_a <=> $pos_b;
            });

            $total_savings = 0;

            if ($additional_products) {
                foreach ($additional_products as $additional_product) {
                    $product_obj = wc_get_product($additional_product->get_id());
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

                    // Calculate total savings
                    if ($regular_price && $sale_price) {
                        $total_savings += ($regular_price - $sale_price);
                    }
                }

                // Get currency settings
                $currency_symbol = html_entity_decode(get_woocommerce_currency_symbol());
                $currency_position = get_option('smarty_currency_symbol_position', 'left');
                $currency_spacing = get_option('smarty_currency_symbol_spacing', 'no_space');
                $spacing = ($currency_spacing === 'space') ? ' ' : '';

                // Format the total savings with currency settings
                $formatted_total_savings = number_format($total_savings, 2, wc_get_price_decimal_separator(), wc_get_price_thousand_separator());
                if ($currency_position === 'left') {
                    $formatted_total_savings = $currency_symbol . $spacing . $formatted_total_savings;
                } else {
                    $formatted_total_savings = $formatted_total_savings . $spacing . $currency_symbol;
                }

                echo '<div class="additional-products">';
                echo '<div class="additional-products-title">';
                echo '<p>' . __('One or two more', 'smarty-upsell-bundle-manager') . '</p>';
                echo '<div class="ribbon"><span>' . sprintf(__('SAVE %s', 'smarty-upsell-bundle-manager'), $formatted_total_savings) . '</span></div>';
                echo '</div>';
                echo '<p>' . sprintf(__('Get up to %s off when you bundle one or more products.', 'smarty-upsell-bundle-manager'), $formatted_total_savings) . '</p>';
                
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

                    // Ensure regular_price and sale_price are set
                    $data_regular_price = !empty($regular_price) ? esc_attr($regular_price) : '0';
                    $data_sale_price = !empty($sale_price) ? esc_attr($sale_price) : '0';

                    $price_html = '';
                    if ($sale_price) {
                        $price_html = '<span class="price">' . wc_price($sale_price) . '</span>';
                        if ($regular_price > $sale_price) {
                            $price_html = '<span class="price old_price">' . wc_price($regular_price) . '</span> <span class="price">' . wc_price($sale_price) . '</span>';
                        }
                    } else {
                        $price_html = '<span class="price">' . wc_price($regular_price) . '</span>';
                    }

                    echo '<label>';
                    echo '<input type="checkbox" name="additional_products[]" value="' . esc_attr($additional_product->get_id()) . '" data-regular-price="' . $data_regular_price . '" data-sale-price="' . $data_sale_price . '">';
                    echo '<div class="additional-product-image">' . $product_image . '</div>';
                    echo '<div>';
                    echo '<div class="additional-product-title">' . esc_html($additional_product->get_name()) . '</div>';
                    echo '<div class="additional-product-price">' . $price_html . '</div>';
                    echo '</div>';
                    echo '</label>';
                }
                echo '</div>';
            }
        } else {
            // If no additional products, do not display anything
            error_log('No additional products selected or available for display.');
        }
     }
}

if (!function_exists('smarty_handle_additional_products_cart')) {
    function smarty_handle_additional_products_cart() {
        if (isset($_POST['additional_products']) && is_array($_POST['additional_products'])) {
            $additional_products = array_map('intval', $_POST['additional_products']);
            
            foreach ($additional_products as $additional_product_id) {
                $product = wc_get_product($additional_product_id);
                if ($product) {
                    WC()->cart->add_to_cart($additional_product_id, 1);
                    error_log('Added additional product ID to cart: ' . $additional_product_id);
                } else {
                    error_log('Product ID ' . $additional_product_id . ' not found.');
                }
            }

            // THIS sends back updated mini-cart + cart hash
            WC_AJAX::get_refreshed_fragments();
        } else {
            error_log('No additional products found in request.');
            wp_send_json_error('No additional products selected.');
        }

        wp_die(); // Always die at the end of AJAX
    }
}
    
if (!function_exists('smarty_add_cart_item_data')) {
    /**
     * Adds additional product data to the WooCommerce cart item data.
     *
     * @param array $cart_item_data Current cart item data.
     * @param int $product_id The product ID.
     * @param int $variation_id The variation ID.
     * @return array Modified cart item data including additional products.
     */
    function smarty_add_cart_item_data($cart_item_data, $product_id, $variation_id) {
        if (isset($_POST['additional_products']) && is_array($_POST['additional_products'])) {
            $cart_item_data['additional_products'] = array_map('intval', $_POST['additional_products']);
        }
        return $cart_item_data;
    }
}
    
if (!function_exists('smarty_get_cart_item_from_session')) {
    /**
     * Retrieves additional product data from the WooCommerce cart session.
     *
     * @param array $cart_item Current cart item data.
     * @param array $values Session-stored cart item data.
     * @return array Modified cart item including additional products.
     */
    function smarty_get_cart_item_from_session($cart_item, $values) {
        if (isset($values['additional_products'])) {
            $cart_item['additional_products'] = $values['additional_products'];
        }
        return $cart_item;
    }
}
    
if (!function_exists('smarty_calculate_cart_item_price')) {
    /**
     * Calculates the total price for cart items, including additional products.
     *
     * Updates the cart item's price to reflect the cost of associated additional products.
     *
     * @param WC_Cart $cart_object The WooCommerce cart object.
     * @return void
     */
    function smarty_calculate_cart_item_price($cart_object) {
        foreach ($cart_object->get_cart() as $cart_item) {
            if (isset($cart_item['additional_products']) && is_array($cart_item['additional_products'])) {
                $additional_total = 0;
                foreach ($cart_item['additional_products'] as $additional_product_id) {
                    $additional_product = wc_get_product($additional_product_id);
                    if ($additional_product) {
                        $additional_total += $additional_product->get_price();
                    }
                }
                $base_price = $cart_item['data']->get_price('edit');
                $cart_item['data']->set_price($base_price + $additional_total);
            }
        }
    }
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
            $promo_general_product = get_post_meta($value['product_id'], 'attach_general_products', true);
            
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
                    $value['data']->set_price($new_pr_price[$value['product_id']]);
                }
            }
        }
    }
}
    
if (!function_exists('smarty_add_order_item_meta')) {
    /**
     * Adds additional products as meta data to WooCommerce order items.
     *
     * Includes additional products' SKUs as hidden meta keys for better visibility
     * and data management in WooCommerce orders.
     *
     * @param int $item_id Order item ID.
     * @param array $values Cart item values.
     * @param string $cart_item_key Cart item key.
     * @return void
     */
    function smarty_add_order_item_meta($item_id, $values, $cart_item_key) {
        if (isset($values['additional_products']) && is_array($values['additional_products'])) {
            wc_add_order_item_meta($item_id, '_additional_products', $values['additional_products']);

            // Loop through each additional product and add its SKU to the order item meta with a hidden prefix
            foreach ($values['additional_products'] as $additional_product_id) {
                $additional_product = wc_get_product($additional_product_id);
                if ($additional_product) {
                    $additional_sku = $additional_product->get_sku();
                    //error_log('Adding SKU for Additional Product ID: ' . $additional_product_id . ' - SKU: ' . $additional_sku); // Debug log
                    wc_add_order_item_meta($item_id, '_hidden_additional_product_sku_' . $additional_product_id, $additional_sku, true);
                } else {
                    //error_log('Failed to get product object for Additional Product ID: ' . $additional_product_id);
                }
            }
        }
    }
}

if (!function_exists('smarty_hide_additional_product_skus')) {
    /**
     * Hides additional product SKUs from being displayed in the WooCommerce admin meta section.
     *
     * @param array $hidden_meta_keys Existing hidden meta keys.
     * @return array Modified hidden meta keys array.
     */
    function smarty_hide_additional_product_skus($hidden_meta_keys) {
        global $wpdb;

        // Get all order item meta keys with the hidden prefix
        $hidden_meta_keys = array_merge($hidden_meta_keys, $wpdb->get_col("SELECT meta_key FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key LIKE '_hidden_additional_product_sku_%'"));
        
        return $hidden_meta_keys;
    }
}
    
if (!function_exists('smarty_display_additional_products_order_meta')) {
    /**
     * Displays additional products in order meta details for WooCommerce orders.
     *
     * Shows the additional products associated with an order in the order details section.
     *
     * @param int $item_id Order item ID.
     * @param WC_Order_Item $item WooCommerce order item object.
     * @param WC_Order $order WooCommerce order object.
     * @return void
     */
    function smarty_display_additional_products_order_meta($item_id, $item, $order) {
        $additional_products = wc_get_order_item_meta($item_id, '_additional_products', true);
        //error_log('Additional Products: ' . print_r($additional_products, true)); // Debug log
        
        if ($additional_products && is_array($additional_products)) {
            // Use ob_start to capture the output
            ob_start();
            echo '<div class="bundle-items">';
            if (is_page('checkout') || is_admin()) {
                echo '<span class="dashicons dashicons-archive"></span>';
            } 
            echo '<p style="font-size: 13 px;"><strong>' . __('In a bundle with', 'smarty-upsell-bundle-manager') . ':</strong></p>';
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
}

if (!function_exists('smarty_add_order_list_column')) {
    /**
     * Adds a custom column to the WooCommerce orders list for indicating bundled orders.
     *
     * @param array $columns Existing columns in the orders list.
     * @return array Modified columns array including the new "is_bundle" column.
     */
    function smarty_add_order_list_column($columns) {
        $new_columns = array();
    
        foreach ($columns as $key => $column) {
            if ('order_number' === $key) {
                $new_columns['is_bundle'] = '';
            }
            $new_columns[$key] = $column;
        }
    
        return $new_columns;
    }
}

if (!function_exists('smarty_add_order_list_column_content')) {
    /**
     * Adds content to the custom "is_bundle" column in the WooCommerce orders list (HPOS compatible).
     *
     * @param string $column The column name.
     * @param int $post_id The ID of the current order.
     * @return void
     */
    function smarty_add_order_list_column_content($column, $post_id) {
        if ('is_bundle' === $column) {
            // Check if HPOS is enabled
            if (class_exists('\Automattic\WooCommerce\Utilities\OrderUtil') && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled()) {
                $order = wc_get_container()->get(\Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController::class)->get_order($post_id);
            } else {
                $order = wc_get_order($post_id);
            }

            if (!$order) {
                return;
            }

            $items = $order->get_items();
            $has_bundle = false;

            foreach ($items as $item_id => $item) {
                $additional_products = $item->get_meta('_additional_products', true);
                if ($additional_products && is_array($additional_products)) {
                    $has_bundle = true;
                    break;
                }
            }

            if ($has_bundle) {
                echo '<span class="dashicons dashicons-archive" title="' . __('This order contains bundled products', 'smarty-upsell-bundle-manager') . '"></span>';
            }
        }
    }
}

if (!function_exists('smarty_choose_additional_products_for_product_cb')) {
    /**
     * Displays a dropdown for choosing additional products for a specific product in the admin panel.
     *
     * @param WP_Post $post The current product post object.
     * @return void
     */
    function smarty_choose_additional_products_for_product_cb($post) {
        $product_additional_products = get_post_meta($post->ID, '_smarty_additional_products', true);
        $product_additional_products = is_array($product_additional_products) ? $product_additional_products : [];

        $products = wc_get_products(array('limit' => -1)); // Fetch all products
        
        echo '<select name="smarty_choose_additional_products[]" multiple="multiple" id="smarty_choose_additional_products" style="width: 100%;">';
        foreach ($products as $product) {
            $selected = in_array($product->get_id(), $product_additional_products) ? 'selected' : '';
            echo '<option value="' . esc_attr($product->get_id()) . '" ' . esc_attr($selected) . '>' . esc_html($product->get_name()) . '</option>';
        }
        echo '</select>';
    ?>
        <script>
            jQuery(document).ready(function($) {
                $('#smarty_choose_additional_products').select2({
                    placeholder: "Select additional products",
                    allowClear: true
                });
            });
        </script>
    <?php
    }
}

if (!function_exists('smarty_save_additional_products_for_product')) {
    /**
     * Saves the selected additional products for a specific product in the admin panel.
     *
     * @param int $post_id The product post ID.
     * @return void
     */
    function smarty_save_additional_products_for_product($post_id) {
        if (isset($_POST['smarty_additional_products']) && !empty($_POST['smarty_additional_products'])) {
            $additional_products = array_map('intval', $_POST['smarty_additional_products']);
            update_post_meta($post_id, '_smarty_additional_products', $additional_products);
            error_log('Products saved: ' . implode(', ', $additional_products));
        } else {
            delete_post_meta($post_id, '_smarty_additional_products');
            error_log('No products selected. Meta field deleted.');
            
            // Clear WooCommerce product cache
            wc_delete_product_transients($post_id);
            
            // Log the value to ensure it has been deleted
            $meta_value = get_post_meta($post_id, '_smarty_additional_products', true);
            error_log('Meta value after delete: ' . print_r($meta_value, true));
        }
		
		// Save the checkbox for styling last 3 variations
        $style_last_three = isset($_POST['_smarty_style_last_three_variations']) ? 'yes' : 'no';
        update_post_meta($post_id, '_smarty_style_last_three_variations', $style_last_three);
    }
}

if (get_option('smarty_enable_additional_products', '1') === '1') {
    // Functions related to additional products
    add_action('woocommerce_product_data_panels', 'smarty_additional_products_data_fields');
    add_action('woocommerce_process_product_meta', 'smarty_save_custom_order_ids_field');
    add_action('woocommerce_process_product_meta', 'smarty_save_additional_products_for_product');
    add_filter('woocommerce_product_data_tabs', 'smarty_add_additional_products_tab');
    add_action('woocommerce_before_single_variation', 'smarty_add_additional_products_checkbox', 5);
    add_action('wp_footer', 'smarty_update_total_price');
    add_action('wp_ajax_smarty_choose_additional_products', 'smarty_handle_additional_products_cart');
    add_action('wp_ajax_nopriv_smarty_choose_additional_products', 'smarty_handle_additional_products_cart');
    add_action('wp_ajax_woocommerce_update_cart_action', 'smarty_calculate_cart_item_price');
    add_action('wp_ajax_nopriv_woocommerce_update_cart_action', 'smarty_calculate_cart_item_price');
    add_action('wp_ajax_woocommerce_add_to_cart', 'smarty_calculate_cart_item_price');
    add_action('wp_ajax_nopriv_woocommerce_add_to_cart', 'smarty_calculate_cart_item_price');
    add_filter('manage_edit-shop_order_columns', 'smarty_add_order_list_column');
    add_action('manage_shop_order_posts_custom_column', 'smarty_add_order_list_column_content', 10, 2);
    add_filter('woocommerce_hidden_order_itemmeta', 'smarty_hide_additional_product_skus');
}

if (!function_exists('smarty_ubm')) {
    /**
     * Checks if the Upsell Bundle Manager plugin is active.
     *
     * @return bool True if the plugin is active, false otherwise.
     */
    function smarty_ubm() {
        return function_exists('is_plugin_active') 
            && is_plugin_active('smarty-upsell-bundle-manager/smarty-upsell-bundle-manager.php');
    }
}