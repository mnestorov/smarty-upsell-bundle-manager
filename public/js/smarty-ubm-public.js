(function($) {
	'use strict';

	/**
	 * All of the code for plugin public JavaScript source
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

	function setActiveUpsell() {
		var firstVariation = $('.check_container.has_variations:first .main_title_wrap');
		if (firstVariation.length) {
			$('.main_title_wrap').removeClass('active');
			firstVariation.addClass('active');
		}
	}

	var smartyUbmVars = smartyUbmVars || {};
	var amountText = smartyUbmVars.ubmAmountText;
    var currencySymbol = smartyUbmVars.ubmCurrencySymbol;
    var currencyPosition = smartyUbmVars.ubmCurrencyPosition;
    var currencySpacing = smartyUbmVars.ubmCurrencySpacing;
    var savingsTextSize = smartyUbmVars.ubmSavingsTextSize;
    var savingsTextColor = smartyUbmVars.ubmSavingsTextColor;
    var decimalSeparator = smartyUbmVars.ubmDecimalSeparator;
    var thousandSeparator = smartyUbmVars.ubmThousandSeparator;
    var decimals = smartyUbmVars.ubmDecimals;
    var youSaveText = smartyUbmVars.ubmYouSaveText;
    var displaySavings = smartyUbmVars.ubmDisplaySavings;

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
				action: 'smarty_ubm_choose_additional_products',
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
})(jQuery);