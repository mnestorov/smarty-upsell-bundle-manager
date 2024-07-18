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

if (!function_exists('smarty_enqueue_scripts')) {}

if (!function_exists('smarty_register_settings_page')) {}

if (!function_exists('smarty_register_settings')) {
    function smarty_register_settings() {
        // Register settings
        register_setting('smarty_settings_group', 'smarty_enable_upsell_styling');

        // Add settings sections
        add_settings_section('smarty_upsell_styling_section', 'Variable (Upsell) Products', 'smarty_upsell_styling_section_cb', 'smarty_settings_page');

        // Add settings fields for additional products features
        add_settings_field('smarty_enable_upsell_styling', 'Enable Variations (Upsell) Styling', 'smarty_checkbox_field_cb', 'smarty_settings_page', 'smarty_upsell_styling_section', ['id' => 'smarty_enable_upsell_styling']);
    }
    add_action('admin_init', 'smarty_register_settings');
}

if (!function_exists('smarty_upsell_styling_section_cb')) {
    function smarty_upsell_styling_section_cb() {
        echo '<p>Enable or disable variation styling of the variable products.</p>';
    }
}

if (!function_exists('smarty_after_edit_attribute_fields')) {}

if (!function_exists('smarty_variable_price_range')) {}

if (!function_exists('smarty_woocommerce_available_variation')) {}

if (!function_exists('smarty_free_delivery_amount')) {}

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
        $custom_css = get_option('smarty_custom_css', '');

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
                    padding: 20px 20px 0 20px;
                    border: 2px dashed rgba(112,153,0, 0.7);
                    border-radius: 5px;
                    background: rgba(112,153,0, 0.075);
                }

                .additional-products p {
                    text-align: center;
                    line-height: normal;
                    font-weight: normal;
                }

                .additional-products label {
                    display: flex;
                    align-items: center;
                    padding: 10px;
                    margin-bottom: 30px;
                    background: #ffffff;
                    border: 2px solid #ffffff00;
                    border-radius: 5px;
                    box-shadow: 0px 3px 11px -2px rgba(0, 0, 0, 0.55);
                    -webkit-box-shadow: 0px 3px 11px -2px rgba(0, 0, 0, 0.55);
                    -moz-box-shadow: 0px 3px 11px -2px rgba(0, 0, 0, 0.55);
                }

                .additional-products label.active, 
                .additional-products label:hover {
                    background: #ffffff;
                    border: 2px solid rgb(112,153,0);
                    transition: all 0.3s ease-in;
                }

                .additional-products input[type="checkbox"] {
                    width: 20px;
                    height: 20px;
                    margin-right: 10px;
                    border-radius: 3px;
                    background-color: rgba(112,153,0, 0.075);
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
                    width: 18%;
                    margin-right: 10px;
                    border: 1px solid <?php echo esc_attr($image_border_color); ?>;
                    border-radius: 5px;
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
                    background: #FFD966;
                    color: #333333;
                    padding: 5px 10px;
                    font-size: 14px;
                    font-weight: bold;
                    position: relative;
                    top: 0;
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
                    border-right-color: #FFD966;
                }

                .ribbon:after {
                    right: -10px;
                    border-width: 12.5px 10px;
                    border-left-color: transparent;
                    border-top-color: #FFD966;
                    border-bottom-color: #FFD966;
                }

                .ribbon span {
                    position: relative;
                    top: -5px;
                }
            </style>

            <?php echo $custom_css; // Output the custom CSS
        }
    }
    add_action('wp_head', 'smarty_public_custom_css');    
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
                    
                    // Remove <bdi> tags from formatted savings
                    var formattedSavings = formatPrice(savings.toFixed(2), false);
                    return '<span class="savings-text" style="font-size:' + savingsTextSize + '; color:' + savingsTextColor + ';">(' + youSaveText + ' ' + formattedSavings + ')</span>';
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
                
                $('form.cart').on('submit', function(e) {
                    e.preventDefault(); // Prevent the form from submitting normally
                    var additionalProducts = $('input[name="additional_products[]"]:checked').map(function() {
                        return $(this).val();
                    }).get();

                    $.ajax({
                        url: wc_add_to_cart_params.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'smarty_choose_additional_products',
                            additional_products: additionalProducts,
                        },
                        success: function(response) {
                            // Handle success - add main product to cart after additional products
                            $(this).off('submit').submit();
                        },
                        error: function(response) {
                            console.log('Error adding additional products');
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
        <?php
    }
    add_action('wp_head', 'smarty_public_custom_js');
}

if (!function_exists('smarty_update_total_price')) {
    function smarty_update_total_price() {
        $amount_text = esc_js(__('Amount: ', 'smarty-custom-upsell-products-design'));
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

if (!function_exists('smarty_add_additional_products_checkbox')) {}

if (!function_exists('smarty_handle_additional_products_cart')) {}
    
if (!function_exists('smarty_add_cart_item_data')) {}
    
if (!function_exists('smarty_get_cart_item_from_session')) {}
    
if (!function_exists('smarty_calculate_cart_item_price')) {}

if (!function_exists('smarty_additional_product_recalculate_price')) {}
    
if (!function_exists('smarty_display_additional_products_in_cart')) {}
    
if (!function_exists('smarty_add_order_item_meta')) {}

if (!function_exists('smarty_hide_additional_product_skus')) {}
    
if (!function_exists('smarty_display_additional_products_order_meta')) {}

if (!function_exists('smarty_add_order_list_column')) {}

if (!function_exists('smarty_add_order_list_column_content')) {}

if (get_option('smarty_enable_additional_products', '1') === '1') {
    // Functions related to additional products
    add_action('wp_footer', 'smarty_update_total_price');   
}