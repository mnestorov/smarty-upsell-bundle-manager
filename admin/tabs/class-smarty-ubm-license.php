<?php
/**
 * The License-specific functionality of the plugin.
 *
 * @link       https://github.com/mnestorov
 * @since      1.0.0
 *
 * @package    Smarty_Upsell_Bundle_Manager
 * @subpackage Smarty_Upsell_Bundle_Manager/admin/tabs
 * @author     Smarty Studio | Martin Nestorov
 */
class Smarty_Ubm_License {

    /**
     * Instance of Smarty_Ubm_API.
	 * 
     * @since    1.0.0
     * @var Smarty_Ubm_API
     */
    private $api_instance;

    /**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
    public function __construct() {
        // Instantiate the API class
        $this->api_instance = new Smarty_Ubm_API(CK_KEY, CS_KEY);
    }

    /**
	 * Initializes the License settings by registering the settings, sections, and fields.
	 *
	 * @since    1.0.0
	 */
    public function ubm_l_settings_init() {
        register_setting('smarty_ubm_options_license', 'smarty_ubm_settings_license', array($this, 'ubm_sanitize_license_settings'));

        add_settings_section(
			'smarty_ubm_section_license',										// ID of the section
			__('License', 'smarty-upsell-bundle-manager'),						// Title of the section  
			array($this, 'ubm_section_tab_license_cb'),							// Callback function that fills the section with the desired content
			'smarty_ubm_options_license'										// Page on which to add the section
		);

		add_settings_field(
			'smarty_ubm_api_key',												// ID of the field
			__('API Key', 'smarty-upsell-bundle-manager'),						// Title of the field
			array($this, 'ubm_field_api_key_cb'),								// Callback function to display the field
			'smarty_ubm_options_license',										// Page on which to add the field
			'smarty_ubm_section_license'										// Section to which this field belongs
		);
    }

    /**
     * Sanitizes the plugin License settings.
     *
     * Validates and sanitizes user input for the settings.
     *
	 * @since    1.0.0
     * @param array $input The input settings array.
     * @return array Sanitized settings.
     */
	public function ubm_sanitize_license_settings($input) {
		$new_input = array();
		if (isset($input['api_key'])) {
			$new_input['api_key'] = sanitize_text_field($input['api_key']);
		}
		return $new_input;
	}

    /**
     * Check if the API key is valid.
     * 
     * @since    1.0.0
     * @param string $api_key The API key to validate.
     * @return bool True if the API key is valid, false otherwise.
     */
    public function ubm_is_valid_api_key($api_key) {
		$response = $this->api_instance->validate_license($api_key);
	
		if (isset($response['success']) && $response['success']) {
			$isActive = false;
			$activations = $response['data']['activationData'] ?? [];
	
			foreach ($activations as $activation) {
				if (empty($activation['deactivated_at'])) {
					$isActive = true;
					break;
				}
			}
	
			//_ubm_write_logs('Checking API key validity: ' . $api_key);
			//_ubm_write_logs('API Response: ' . print_r($response, true));
			//_ubm_write_logs('License is ' . ($isActive ? 'active' : 'inactive'));
			return $isActive;
		}
	
		return false;
	}

	/**
     * Handle license status check.
     * 
     * @since    1.0.0
     * @param string $option_name The name of the option.
     * @param mixed $old_value The old value of the option.
     * @param mixed $value The new value of the option.
     */
	public function ubm_handle_license_status_check($option_name, $old_value, $value) {
		if (!$this->api_instance) {
			// Handle the error
			return;
		}
	
		if ($option_name == 'smarty_ubm_settings_license' && isset($value['api_key'])) {
			$api_key = $value['api_key'];
	
			// Check the license status
			$isValid = $this->ubm_is_valid_api_key($api_key);
	
			// Add an admin notice based on the validity of the license
			if ($isValid) {
				// Add query arg or admin notice for valid license
				add_query_arg('license-valid', 'true');
			} else {
				// Add query arg or admin notice for invalid license
				add_query_arg('license-invalid', 'true');
			}
		}
	}

    /**
     * Callback function for the License section.
     * 
     * @since    1.0.0
     * @param array $args Arguments for the callback.
     */
	public function ubm_section_tab_license_cb($args) {
		?>
		<p id="<?php echo esc_attr($args['id']); ?>">
			<?php echo esc_html__('Enter your API key to enable advanced features.', 'smarty-upsell-bundle-manager'); ?>
		</p>
		<?php
	}

    /**
     * Callback function for the API key field.
     * 
     * @since    1.0.0
     * @param array $args Arguments for the callback.
     */
	public function ubm_field_api_key_cb($args) {
		$options = get_option('smarty_ubm_settings_license');
		?>
		<input type="text" id="smarty_ubm_api_key" name="smarty_ubm_settings_license[api_key]" size="30" value="<?php echo isset($options['api_key']) ? esc_attr($options['api_key']) : ''; ?>">
		<p class="description">
			<?php echo esc_html__('Enter a valid API key.', 'smarty-upsell-bundle-manager'); ?>
		</p>
		<?php
	}

    /**
     * Function to check for transients and other conditions to display admin notice.
     *
     * @since    1.0.0
     */
    public function ubm_license_notice() {
        $options = get_option('smarty_ubm_settings_license');
		
		if (isset($_GET['license-activated']) && $_GET['license-activated'] == 'true') {
			?>
			<div class="notice notice-success smarty-ubm-auto-hide-notice">
				<p><?php echo esc_html__('License activated successfully.', 'smarty-upsell-bundle-manager'); ?></p>
			</div>
			<?php
		}
    }
}