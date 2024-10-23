<?php
/**
 * The Styling-specific functionality of the plugin.
 *
 * @link       https://github.com/mnestorov
 * @since      1.0.0
 *
 * @package    Smarty_Upsell_Bundle_Manager
 * @subpackage Smarty_Upsell_Bundle_Manager/admin/tabs
 * @author     Smarty Studio | Martin Nestorov
 */
class Smarty_Ubm_Styling {

    /**
	 * Initializes the Styling settings by registering the settings, sections, and fields.
	 *
	 * @since    1.0.0
	 */
    public function ubm_s_settings_init() {
        register_setting('smarty_ubm_options_styling', 'smarty_ubm_settings_styling', array($this, 'ubm_sanitize_styling_settings'));

        add_settings_section(
			'smarty_ubm_section_styling',										// ID of the section
			__('Styling', 'smarty-upsell-bundle-manager'),						// Title of the section  
			array($this, 'ubm_section_tab_styling_cb'),							// Callback function that fills the section with the desired content
			'smarty_ubm_options_styling'										// Page on which to add the section
		);

        add_settings_section(
			'smarty_ubm_section_custom_css',									// ID of the section
			__('Custom Styling', 'smarty-upsell-bundle-manager'),				// Title of the section  
			array($this, 'ubm_section_custom_css_cb'),							// Callback function that fills the section with the desired content
			'smarty_ubm_options_styling'										// Page on which to add the section
		);

        add_settings_section(
			'smarty_ubm_section_colors',										// ID of the section
			__('Colors', 'smarty-upsell-bundle-manager'),						// Title of the section  
			array($this, 'ubm_section_colors_cb'),							    // Callback function that fills the section with the desired content
			'smarty_ubm_options_styling'										// Page on which to add the section
		);

        add_settings_section(
			'smarty_ubm_section_font_sizes',									// ID of the section
			__('Font Sizes', 'smarty-upsell-bundle-manager'),					// Title of the section  
			array($this, 'ubm_section_font_sizes_cb'),							// Callback function that fills the section with the desired content
			'smarty_ubm_options_styling'										// Page on which to add the section
		);

        // Custom CSS
        add_settings_field(
			'smarty_ubm_custom_css',										    // ID of the field
			__('Custom CSS', 'smarty-upsell-bundle-manager'),                   // Title of the field
			array($this, 'ubm_textarea_field_cb'),								// Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_custom_css'										// Section to which this field belongs
		);

        // Colors
		add_settings_field(
			'smarty_ubm_active_bg_color',										// ID of the field
			__('Variations (Background)', 'smarty-upsell-bundle-manager'),      // Title of the field
			array($this, 'ubm_color_field_cb'),								    // Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_colors'										    // Section to which this field belongs
		);

        add_settings_field(
			'smarty_ubm_active_border_color',									// ID of the field
			__('Variations (Border)', 'smarty-upsell-bundle-manager'),          // Title of the field
			array($this, 'ubm_color_field_cb'),							        // Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_colors'										    // Section to which this field belongs
		);

        add_settings_field(
			'smarty_ubm_price_color',										    // ID of the field
			__('Price', 'smarty-upsell-bundle-manager'),                        // Title of the field
			array($this, 'ubm_color_field_cb'),								    // Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_colors'										    // Section to which this field belongs
		);

        add_settings_field(
			'smarty_ubm_old_price_color',										// ID of the field
			__('Old Price', 'smarty-upsell-bundle-manager'),                    // Title of the field
			array($this, 'ubm_color_field_cb'),								    // Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_colors'										    // Section to which this field belongs
		);

        add_settings_field(
			'smarty_ubm_free_delivery_bg_color',								// ID of the field
			__('Free Delivery (Background)', 'smarty-upsell-bundle-manager'),   // Title of the field
			array($this, 'ubm_color_field_cb'),						            // Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_colors'										    // Section to which this field belongs
		);

        add_settings_field(
			'smarty_ubm_free_delivery_color',									// ID of the field
			__('Free Delivery (Text)', 'smarty-upsell-bundle-manager'),         // Title of the field
			array($this, 'ubm_color_field_cb'),						            // Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_colors'										    // Section to which this field belongs
		);

        add_settings_field(
			'smarty_ubm_label_1_bg_color',									    // ID of the field
			__('Label 1 (Background)', 'smarty-upsell-bundle-manager'),         // Title of the field
			array($this, 'ubm_color_field_cb'),						            // Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_colors'										    // Section to which this field belongs
		);

        add_settings_field(
			'smarty_ubm_label_1_color',									        // ID of the field
			__('Label 1 (Text)', 'smarty-upsell-bundle-manager'),               // Title of the field
			array($this, 'ubm_color_field_cb'),						            // Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_colors'										    // Section to which this field belongs
		);

        add_settings_field(
			'smarty_ubm_label_2_bg_color',									    // ID of the field
			__('Label 2 (Background)', 'smarty-upsell-bundle-manager'),         // Title of the field
			array($this, 'ubm_color_field_cb'),						            // Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_colors'										    // Section to which this field belongs
		);

        add_settings_field(
			'smarty_ubm_label_2_color',									        // ID of the field
			__('Label 2 (Text)', 'smarty-upsell-bundle-manager'),               // Title of the field
			array($this, 'ubm_color_field_cb'),						            // Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_colors'										    // Section to which this field belongs
		);

        add_settings_field(
			'smarty_ubm_image_border_color',									// ID of the field
			__('Image Border', 'smarty-upsell-bundle-manager'),                 // Title of the field
			array($this, 'ubm_color_field_cb'),						            // Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_colors'										    // Section to which this field belongs
		);

        add_settings_field(
			'smarty_ubm_savings_text_color',									// ID of the field
			__('Savings Text', 'smarty-upsell-bundle-manager'),                 // Title of the field
			array($this, 'ubm_color_field_cb'),						            // Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_colors'										    // Section to which this field belongs
		);

        // Font Sizes
        add_settings_field(
			'smarty_ubm_price_font_size',										// ID of the field
			__('Price', 'smarty-upsell-bundle-manager'),					    // Title of the field
			array($this, 'ubm_font_size_field_cb'),								// Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_font_sizes'										// Section to which this field belongs
		);

        add_settings_field(
			'smarty_ubm_old_price_font_size',									// ID of the field
			__('Old Price', 'smarty-upsell-bundle-manager'),					// Title of the field
			array($this, 'ubm_font_size_field_cb'),						    // Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_font_sizes'										// Section to which this field belongs
		);

        add_settings_field(
			'smarty_ubm_variable_desc_font_size',								// ID of the field
			__('Variation Description', 'smarty-upsell-bundle-manager'),	    // Title of the field
			array($this, 'ubm_font_size_field_cb'),						// Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_font_sizes'										// Section to which this field belongs
		);

        add_settings_field(
			'smarty_ubm_free_delivery_font_size',								// ID of the field
			__('Free Delivery', 'smarty-upsell-bundle-manager'),			    // Title of the field
			array($this, 'ubm_font_size_field_cb'),						// Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_font_sizes'										// Section to which this field belongs
		);

        add_settings_field(
			'smarty_ubm_label_1_font_size',										// ID of the field
			__('Label 1', 'smarty-upsell-bundle-manager'),					    // Title of the field
			array($this, 'ubm_font_size_field_cb'),							// Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_font_sizes'										// Section to which this field belongs
		);

        add_settings_field(
			'smarty_ubm_label_2_font_size',										// ID of the field
			__('Label 2', 'smarty-upsell-bundle-manager'),					    // Title of the field
			array($this, 'ubm_font_size_field_cb'),							// Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_font_sizes'										// Section to which this field belongs
		);

        add_settings_field(
			'smarty_ubm_savings_text_size',										// ID of the field
			__('Savings Text', 'smarty-upsell-bundle-manager'),					// Title of the field
			array($this, 'ubm_font_size_field_cb'),							// Callback function to display the field
			'smarty_ubm_options_styling',										// Page on which to add the field
			'smarty_ubm_section_font_sizes'										// Section to which this field belongs
		);
    }

    /**
     * Callback function for the Styling section.
     * 
     * @since    1.0.0
     * @param array $args Arguments for the callback.
     */
	public function ubm_section_tab_styling_cb($args) {
		?>
		<p id="<?php echo esc_attr($args['id']); ?>">
			<?php echo esc_html__('Customize the various elements in your WooCommerce single products page.', 'smarty-upsell-bundle-manager'); ?>
		</p>
		<?php
	}

    /**
     * Callback function for the Custom CSS section.
     * 
     * @since    1.0.0
     */
    public function ubm_section_custom_css_cb() {
        echo '<p>Add custom styling to the variation products or to the additional (bundle) products.</p>';
    }

    /**
     * Callback function for the Colors section.
     * 
     * @since    1.0.0
     */
    public function ubm_section_colors_cb() {
        echo '<p>Customize the colors of the various elements in your WooCommerce variation and additional (bundle) products.</p>';
    }
    
    /**
     * Callback function for the Font Sizes section.
     * 
     * @since    1.0.0
     */
    public function ubm_section_font_sizes_cb() {
        echo '<p>Customize the font sizes of the various elements in your WooCommerce variation and additional (bundle) products.</p>';
    }

    /**
     * Callback function for the color field.
     * 
     * @since    1.0.0
     */
    public function ubm_color_field_cb($args) {
        $option = get_option($args['id'], '');
        echo '<input type="text" name="' . $args['id'] . '" value="' . esc_attr($option) . '" class="smarty-color-field" data-default-color="' . esc_attr($option) . '" />';
    }
    
    /**
     * Callback function for the font size field.
     * 
     * @since    1.0.0
     */
    public function ubm_font_size_field_cb($args) {
        $option = get_option($args['id'], '14');
        echo '<input type="range" name="' . $args['id'] . '" min="10" max="30" value="' . esc_attr($option) . '" class="smarty-font-size-slider" />';
        echo '<span id="' . $args['id'] . '-value">' . esc_attr($option) . 'px</span>';
    }
    
    /**
     * Callback function for the textarea field.
     * 
     * @since    1.0.0
     */
    public function ubm_textarea_field_cb($args) {
        $option = get_option($args['id'], '');
        echo '<textarea name="' . $args['id'] . '" id="' . $args['id'] . '" rows="10" cols="50" class="large-text">' . esc_textarea($option) . '</textarea>';
    }
}