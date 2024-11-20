<?php
$up_sell_design = function_exists('smarty_get_attr_fields') && $attr_id ? smarty_get_attr_fields($attr_id) : false;
$free_delivery_text = get_option('smarty_free_delivery_text', 'Free delivery');

if ($up_sell_design) :
    foreach ($attributes as $attribute_name => $options) :
        if (taxonomy_exists($attribute_name)) :
            $terms = wc_get_product_terms($product->get_id(), $attribute_name, ['fields' => 'all']);
            ?>
            <div class="product_variation_section variations upsell-container">
                <div class="variation_row" id="variation_select">
                    <div class="swatches-select variation_sub">
                        <?php
                        $pr_variations = new WC_Product_Variable($product->get_id());
                        $get_available_variations = $product->get_available_variations();
                        $pr_child_variations = $pr_variations->get_children();
                        $options_flipped = array_flip($options);

                        foreach ($pr_child_variations as $key_v => $v) :
                            $single_variation = new WC_Product_Variation($v);
                            if ($single_variation->exists()) :
                                $attributes = $single_variation->get_attributes();
                                $attr_slug = reset($attributes);
                                $term_slug = $get_available_variations[$key_v]['attributes']['attribute_' . $attribute_name];
                                if (!in_array($attr_slug, $options)) continue;

                                $bg_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($v), 'woocommerce_thumbnail');
                                $srcset_thumb = wp_get_attachment_image_srcset(get_post_thumbnail_id($v));

                                $attr_key = array_search($attr_slug, $options); // Ensure $attr_key is defined

                                [$regular_price, $free_delivery_amount, $price, $currency] = [
                                    !empty($local_prices['regular_price']) ? $local_prices['regular_price'] : $single_variation->get_regular_price(),
                                    function_exists('smarty_free_delivery_amount') ? smarty_free_delivery_amount() : '',
                                    !empty($local_prices['price']) ? $local_prices['price'] : $single_variation->get_price(),
                                    !empty($local_prices['currency']) ? $local_prices['currency'] : $currency,
                                ];
								
                                ?>
                                <label class="check_container has_variations<?= $key_v == 0 ? ' first' : '' ?>">
                                    <span class="main_content">
                                        <div class="main_title_wrap">
                                            <div class="var_txt">
                                                <div class="checkmark">
                                                    <input type="radio" name="radio_attribute_<?= $attribute_name; ?>" value="<?= $attr_slug; ?>" />
                                                </div>
                                                <span class="variation_title"><?= str_replace(get_the_title() . ' - ', '', $single_variation->get_name()); ?></span>
                                            </div>
                                            <?php if (!empty($bg_thumb[0])) : ?>
                                                <img class="variable_img" src="<?= $bg_thumb[0]; ?>" data-srcset="<?= $srcset_thumb; ?>" width="100" height="100" />
                                            <?php endif; ?>
                                            <?php if ($term_slug == $attr_slug) :
                                                $price = isset($local_prices['variations'][$attr_key]['display_price']) ? $local_prices['variations'][$attr_key]['display_price'] : $single_variation->get_price();
                                                $regular_price = isset($local_prices['variations'][$attr_key]['display_regular_price']) ? $local_prices['variations'][$attr_key]['display_regular_price'] : $single_variation->get_regular_price();
                                            endif; ?>
                                            <div class="variable_content">
												<?php if (!empty($single_variation->get_description())) : ?>
                                                	<div class="variable_desc"><?= apply_filters('woocommerce_variation_option_description', $single_variation->get_description()); ?></div>
											<?php else : ?>
												<br>
											<?php endif; ?>
                                                <?php if ($regular_price != $price) : ?>
                                                    <span class="price old_price" style="visibility:visible;word-spacing:normal;"><?= number_format($regular_price, 2) . ' ' . $currency; ?></span>
                                                <?php endif; ?>
                                                <span class="price" style="visibility:visible;word-spacing:normal;"><?= number_format($price, 2); ?> <?= $currency; ?></span>
                                                
                                                <?php $label_1 = get_post_meta($v, '_smarty_label_1', true); ?>
                                                <?php if (!empty($label_1)) { ?>
                                                    <div class="label_1"><?= esc_html($label_1); ?></div>
                                                <?php } ?>

                                                <?php $label_2 = get_post_meta($v, '_smarty_label_2', true); ?>
                                                <?php if (!empty($label_2)) { ?>
                                                    <div class="label_2"><?= esc_html($label_2); ?></div>
                                                <?php } ?>
                                                
                                                <?php if (!is_null($free_delivery_amount) && $price > $free_delivery_amount) : ?>
                                                    <span class="free_delivery"><?= esc_html($free_delivery_text); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </span>
                                </label>
                            <?php endif;
                        endforeach;
                        ?>
                    </div>
                </div>
                <div class="upsell_select_box" style="display: none;">
                    <?php
                    wc_dropdown_variation_attribute_options([
                        'options' => $options,
                        'attribute' => $attribute_name,
                        'product' => $product,
                        'selected' => reset($options), // Pass variable
                    ]);

                    echo end($attribute_keys) === $attribute_name ? wp_kses_post(apply_filters('woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__('Clear', 'woocommerce') . '</a>')) : '';
                    ?>
                </div>
            </div>
        <?php endif;
    endforeach;
else :
    ?>
    <table class="variations" cellspacing="0">
        <tbody>
        <?php foreach ($attributes as $attribute_name => $options) : ?>
            <tr>
                <td class="label"><label for="<?= esc_attr(sanitize_title($attribute_name)); ?>"><?= wc_attribute_label($attribute_name); ?></label></td>
                <td class="value">
                    <?php
                    wc_dropdown_variation_attribute_options([
                        'options' => $options,
                        'attribute' => $attribute_name,
                        'product' => $product,
                        'selected' => reset($options), // Pass variable
                    ]);

                    echo end($attribute_keys) === $attribute_name ? wp_kses_post(apply_filters('woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__('Clear', 'woocommerce') . '</a>')) : '';
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
