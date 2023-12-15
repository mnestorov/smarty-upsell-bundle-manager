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
 * WC tested up to: 5.1.0
 */

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

// Use the function with debugging enabled
//smarty_copy_files_to_child_theme(true);

// Use the function without debugging (in production, for example)
//smarty_copy_files_to_child_theme(false);

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
    if (is_product()) {
        echo '<style>
        .product-single .product__actions .product__actions__inner {
            border: none;
        }
        
        .product-single .product__actions .quantity input
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
        }
        
        .price {
                color: #00A651;
                font-weight: bold;
        }
        
        .old_price {
            text-decoration: line-through;
            color: #c00;
            font-weight: bold;
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
        
        .variable_desc {
                font-size: 14px;
        }
        
        .variable_img {
                width: 16%;
                float: right;
                margin-top: 10px;
                margin-right: 10px;
        }
        
        .product-single .product__actions .single_variation_wrap .woocommerce-variation {
                height: 40px;
                padding: 12px 50px 20px 160px;
        }
        
        .free_delivery {
            font-size: 13px;
            color: #ffffff;
            font-weight: 600;
            position: absolute;
            top: 0;
            left: 0;
            border-radius: 15px 15px 75px 3px;
            padding: 0 18px;
            background: #00A651;
        }
        
        .active .main_title_wrap {
                background: rgba(210, 184, 133, 0.3);
                border: 2px solid #D2B885;
        }
        </style>';
    }
}
add_action('wp_head', 'smarty_custom_css');