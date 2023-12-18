<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 * 
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.5
 */

defined('ABSPATH') || exit;

global $product;

// Attribute and Variations
$attribute_keys  = array_keys($attributes);
$variations_json = wp_json_encode($available_variations);
$attr_id         = false;

// Getting the first attribute ID if it exists
if (count(wc_get_attribute_taxonomies()) > 0 && isset($attribute_keys[0])) {
    $first_attr_name = str_replace('pa_', '', $attribute_keys[0]);
    foreach (wc_get_attribute_taxonomies() as $attr_taxonomies) {
        if ($attr_taxonomies->attribute_name == $first_attr_name) {
            $attr_id = $attr_taxonomies->attribute_id;
        }
    }
}

// Local Pricing Logic
$currency = get_woocommerce_currency_symbol();
$local_prices = function_exists('smarty_get_location_country') ? smarty_get_location_country() : [];
$sa_region_id  = $_COOKIE['sa_region_id'] ?? null;

if (is_array($local_prices) && isset($local_prices[$sa_region_id])) {
    $current_product_sku = $product->get_sku();
    $local_prices        = $local_prices[$sa_region_id][$current_product_sku] ?? [];
}
?>

<?php do_action('woocommerce_before_add_to_cart_form'); ?>

<!-- Add to Cart Form -->
<form class="variations_form cart" method="post" enctype='multipart/form-data' 
      data-product_id="<?= absint($product->get_id()); ?>" 
      data-product_variations="<?= function_exists('wc_esc_json') ? wc_esc_json($variations_json) : esc_attr($variations_json); ?>">
    <?php do_action('woocommerce_before_variations_form'); ?>
	
    <?php if (empty($available_variations) && false !== $available_variations) : ?>
        <p class="stock out-of-stock"><?= esc_html_e('This product is currently out of stock and unavailable.', 'woocommerce'); ?></p>
    <?php else : ?>
        <!-- Upsell Design Logic -->
        <?php include 'variable-product-upsell-design.php'; ?>
	
        <!-- Standard Variations Logic -->
        <?php include 'variable-product-standard-variations.php'; ?> 
    <?php endif; ?>
</form>

<?php do_action('woocommerce_after_add_to_cart_form'); ?>

<!-- Additional JavaScript -->
<script type="text/javascript">
jQuery(document).ready(function($) {
		$('input[name="<?='radio_attribute_'.$attribute_name?>"]').change(function() {
			let check_attr = $(this).val();
			$('select[name="<?='attribute_'.$attribute_name?>"]').val(check_attr).change();
		});

		$('.has_variations').click(function() {
			let variation_img = $(this).find('.variable_img')[0].src;
			let variation_srcset = $(this).find('.variable_img')[0].getAttribute('data-srcset');
			if (variation_img && variation_srcset) {
				$('#product-thumbnails .woocommerce-product-gallery__image:first-of-type img').attr('src', variation_img);
				$('#product-thumbnails .woocommerce-product-gallery__image:first-of-type img').attr('srcset', '');
				$('.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image:nth-of-type(2) img').attr('src', variation_img);
				$('.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image:nth-of-type(2) img').attr('srcset', variation_srcset);
			}
			$('.has_variations').removeClass('active');
			$(this).addClass('active');
		});

		setTimeout(function() {
			$('.swatches-select .has_variations.first').click();
		}, 200);
	});
</script>