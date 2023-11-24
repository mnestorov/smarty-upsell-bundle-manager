<div class="single_variation_wrap">
	<?php 
		/**
		 * Hook: woocommerce_before_single_variation.
		 */
		do_action('woocommerce_before_single_variation'); 
	?>
			
	<?php 
		/**
		 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
		 * @since 2.4.0
		 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
		 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
		 */
		do_action('woocommerce_single_variation'); 
	?>
			
	<?php 
		/**
		 * Hook: woocommerce_after_single_variation.
		 */
		do_action('woocommerce_after_single_variation'); 
	?>
 </div>