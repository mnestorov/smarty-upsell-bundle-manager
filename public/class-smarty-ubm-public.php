<?php
/**
 * The public functionality of the plugin.
 * 
 * Defines the plugin name, version, and two hooks for how to enqueue 
 * the public-facing stylesheet (CSS) and JavaScript code.
 * 
 * @link       https://smartystudio.net
 * @since      1.0.0
 *
 * @package    smarty_upsell_bundle_manager
 * @subpackage smarty_gupsell_bundle_manager/admin/partials
 * @author     Smarty Studio | Martin Nestorov
 */
class Smarty_Ubm_Public {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name     The name of the plugin.
	 * @param    string    $version         The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/**
		 * This function enqueues custom CSS for the WooCommerce checkout page.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smarty_Ubm_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smarty_Ubm_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
         
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/smarty-ubm-public.css', array(), $this->version, 'all');
    }

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function enqueues custom JavaScript for the WooCommerce checkout page.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smarty_Ubm_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smarty_Ubm_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/smarty-ubm-public.js', array('jquery'), $this->version, true);
	}

	/**
     * Modifies the display format of WooCommerce variable product prices.
     * 
	 * @since    1.0.0
     * @param string $wcv_price Current price HTML.
     * @param WC_Product $product WooCommerce product object.
     * @return string Modified price HTML.
     */
    public function ubm_variable_price_range($wc_variable_price, $product) {
        $prefix = '';
        $wc_variable_min_sale_price = $product->get_variation_sale_price('min', true);
        $wc_variable_reg_min_price = $product->get_variation_regular_price('min', true);
        $wc_variable_max_price = $product->get_variation_price('max', true);
        $wc_variable_min_price = $product->get_variation_price('min', true);
        $wc_variable_price_html = ($wc_variable_min_sale_price == $wc_variable_reg_min_price) 
            ? wc_price($wc_variable_reg_min_price) 
            : wc_price($wc_variable_min_sale_price);

        return ($wc_variable_min_price == $wc_variable_max_price) ? $wc_variable_price_html : sprintf('%s%s', $prefix, $wc_variable_price_html);
    }

	/**
     * Filters the variation data array to modify the price HTML output.
     * 
     * This function adjusts the price displayed in the variation templates to show
     * only the sale price if the product is on sale, or the regular price otherwise.
     * It directly affects the JavaScript-based template by modifying the `price_html` key
     * in the variation data array before it is passed to the front end.
     *
	 * @since    1.0.0
     * @param array       $variation_data Array of variation data.
     * @param WC_Product  $product The variable product object.
     * @param WC_Product_Variation $variation The single variation object.
     * @return array Modified variation data including only the active price HTML.
     */
    public function ubm_woocommerce_available_variation($variation_data, $product, $variation) {
        // Check if the variation is on sale.
        if ($variation->is_on_sale()) {
            // If on sale, set the price_html to the sale price wrapped in appropriate HTML.
            $variation_data['price_html'] = wc_price($variation->get_sale_price());
        } else {
            // If not on sale, set the price_html to the regular price wrapped in appropriate HTML.
            $variation_data['price_html'] = wc_price($variation->get_regular_price());
        }

        // Return the modified variation data array.
        return $variation_data;
    }

	/**
     * Calculates the amount required for free delivery based on the blog ID.
     *
	 * @since    1.0.0
     * @return float Minimum amount required for free delivery.
     */
    public function ubm_free_delivery_amount() {
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

	/**
	 * @since    1.0.0
	 */
	public function ubm_add_additional_products_checkbox() {
            
        global $product;

        // Get the selected products from the plugin settings
        $products_to_show = get_option('smarty_ubm_choose_where_to_show', []);

        // Check if the current product is in the chosen products list
        if (!in_array($product->get_id(), $products_to_show)) {
            return; // Exit if the current product is not in the chosen list
        }

        // Get the additional products selected in plugin settings
        $additional_products_ids = get_option('smarty_ubm_choose_additional_products', []);

        if (!empty($additional_products_ids) && is_array($additional_products_ids)) {
            $additional_products = wc_get_products(array(
                'include' => $additional_products_ids,
                'status'  => 'publish',
                'limit'   => -1,
            ));

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
                $currency_position = get_option('smarty_ubm_currency_symbol_position', 'left');
                $currency_spacing = get_option('smarty_ubm_currency_symbol_spacing', 'no_space');
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
        }
    }

	/**
	 * @since    1.0.0
	 */
	public function ubm_handle_additional_products_cart() {
        if (isset($_POST['additional_products']) && is_array($_POST['additional_products'])) {
            foreach ($_POST['additional_products'] as $product_id) {
                $product_id = intval($product_id);
                if ($product_id > 0 && wc_get_product($product_id)) {
                    WC()->cart->add_to_cart($product_id);
                }
            }
        }
        wp_die(); // Stop further execution and return proper response.
    }

	/**
	 * @since    1.0.0
	 */
	public function ubm_get_cart_item_from_session($cart_item, $values) {
        if (isset($values['additional_products'])) {
            $cart_item['additional_products'] = $values['additional_products'];
        }
        return $cart_item;
    }

	/**
	 * @since    1.0.0
	 */
	public function ubm_add_cart_item_data($cart_item_data, $product_id, $variation_id) {
        if (isset($_POST['additional_products']) && is_array($_POST['additional_products'])) {
            $cart_item_data['additional_products'] = array_map('intval', $_POST['additional_products']);
        }
        return $cart_item_data;
    }

	/**
	 * @since    1.0.0
	 */
	public function ubm_calculate_cart_item_price($cart_object) {
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

	/**
     * Recalculates the price of products in the cart under specific conditions.
     * 
	 * @since    1.0.0
     * @param WC_Cart $cart_object The WooCommerce cart object.
     * @return void Modifies the cart object but does not return a value.
     */
    public function ubm_additional_product_recalculate_price($cart_object) {
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

	/**
	 * @since    1.0.0
	 */
	public function ubm_display_additional_products_in_cart($item_data, $cart_item) {
        if (isset($cart_item['additional_products']) && is_array($cart_item['additional_products'])) {
            $additional_products_list = '<ul style="list-style-type: none !important; padding: 0 5px;">'; // Start an unstyled list
            foreach ($cart_item['additional_products'] as $additional_product_id) {
                $product = wc_get_product($additional_product_id);
                if ($product) {
                    $additional_products_list .= sprintf('<li style="font-weight: normal; margin: 5px 10px;"><span style="color: #a1a1a1;">- 1 <small>x</small></span> %s (%s)</li>',
                        esc_html($product->get_name()),
                        wc_price($product->get_price())
                    );
                }
            }
            $additional_products_list .= '</ul>';
            $item_data[] = array(
                'name'    => __('In a bundle with', 'smarty-upsell-bundle-manager'),
                'value'   => $additional_products_list,
                'display' => '' // This ensures it will render our HTML directly
            );
        }
        return $item_data;
    }

	/**
	 * @since    1.0.0
	 */
	public function ubm_add_order_item_meta($item_id, $values, $cart_item_key) {
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

	/**
	 * @since    1.0.0
	 */
	public function ubm_hide_additional_product_skus($hidden_meta_keys) {
        global $wpdb;

        // Get all order item meta keys with the hidden prefix
        $hidden_meta_keys = array_merge($hidden_meta_keys, $wpdb->get_col("SELECT meta_key FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key LIKE '_hidden_additional_product_sku_%'"));
        
        return $hidden_meta_keys;
    }
}