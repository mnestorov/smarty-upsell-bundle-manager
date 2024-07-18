(function ($) {
	'use strict';

    /**
	 * All of the code for plugin admin JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed we will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables us to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 */

    $(document).ready(function ($) {
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

		// ColorPicker init
		$('.smarty-color-field').wpColorPicker();

        // Update the font size value display
        $('.smarty-font-size-slider').on('input', function() {
            var sliderId = $(this).attr('name');
            $('#' + sliderId + '-value').text($(this).val() + 'px');
        });
    });
})(jQuery);