<?php
/**
 * The plugin functions file.
 *
 * This is used to define general functions, shortcodes etc.
 * 
 * Important: Always use the `smarty_` prefix for function names.
 *
 * @link       https://smartystudio.net
 * @since      1.0.0
 *
 * @package    Smarty_Upsell_Bundle_Manager
 * @subpackage Smarty_Upsell_Bundle_Manager/admin/partials
 * @author     Smarty Studio | Martin Nestorov
 */

 if (!function_exists('smarty_copy_files_to_child_theme')) {
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
        global $pagenow;

        static $already_run = false;
        if ($already_run) {
            return;
        }
    
        // Check if we are on the correct admin page
        if ($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'smarty-upsell-bundle-manager') {
            
            // Retrieve debug setting
            $debug = get_option('smarty_ubm_debug_mode', false);

            $notices_enabled = get_option('smarty_ubm_debug_notices_enabled', false);

            if (!$notices_enabled) {
                return; // Exit if notices are disabled
            }

            $already_run = true; // Set to prevent future execution within the same request
    
            // Only proceed if debugging is true
            if (!$debug) {
                add_action('admin_notices', function() {
                    echo "<div class='notice notice-info is-dismissible'><p>Debug mode is off, not copying files.</p></div>";
                });
            } else {
                $files_to_copy = [
                    'variation.php',
                    'variable.php',
                    'variable-product-upsell-design.php',
                    'variable-product-standard-variations.php',
                ];
        
                // Define the source and destination directories
                $source_directory = plugin_dir_path(__FILE__) . '/templates/woocommerce/single-product/add-to-cart/';
                $destination_directory = get_stylesheet_directory() . '/woocommerce/single-product/add-to-cart/';
        
                // Check if destination directory exists, if not, create it
                if (!file_exists($destination_directory)) {
                    mkdir($destination_directory, 0755, true);
                }
        
                // Loop through each file and copy it
                foreach ($files_to_copy as $file_name) {
                    $source_path = $source_directory . $file_name;
                    $destination_path = $destination_directory . $file_name;
            
                    // Check if the source file exists
                    if (file_exists($source_path)) {
                        if (copy($source_path, $destination_path)) {
                            // Set success message
                            add_action('admin_notices', function() use ($file_name) {
                                echo "<div class='notice notice-success is-dismissible'><p>Copied file: <b>$file_name</b> successfully.</p></div>";
                            });
                        } else {
                            // Set error message
                            add_action('admin_notices', function() use ($file_name) {
                                echo "<div class='notice notice-error is-dismissible'><p>Error: Unable to copy file: <b>$file_name</b>.</p></div>";
                            });
                        }
                    } else {
                        // Set file not found message
                        add_action('admin_notices', function() use ($file_name) {
                            echo "<div class='notice notice-warning is-dismissible'><p>Error: Source file not found: <b>$file_name</b>.</p></div>";
                        });
                    }
                }
            }
        }
    }
    add_action('admin_init', 'smarty_copy_files_to_child_theme');

    // Use the function for debugging
    $debug = get_option('smarty_ubm_debug_mode', false) === '1'; // strict comparison
    smarty_copy_files_to_child_theme($debug);
}

if (!function_exists('smarty_check_compatibility')) {
    /**
     * Helper function to check compatibility.
     * 
     * @since      1.0.0
     */
    function smarty_check_compatibility() {
        $min_wp_version = MIN_WP_VER; // Minimum WordPress version required
        $min_wc_version = MIN_WC_VER; // Minimum WooCommerce version required
        $min_php_version = MIN_PHP_VER; // Minimum PHP version required

        $wp_compatible = version_compare(get_bloginfo('version'), $min_wp_version, '>=');
        $php_compatible = version_compare(PHP_VERSION, $min_php_version, '>=');

        // Check WooCommerce version
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $plugins = get_plugins();
        $wc_version = isset($plugins['woocommerce/woocommerce.php']) ? $plugins['woocommerce/woocommerce.php']['Version'] : '0';
        $wc_compatible = version_compare($wc_version, $min_wc_version, '>=');

        if (!$wp_compatible || !$php_compatible || !$wc_compatible) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die('This plugin requires at least WordPress version ' . $min_wp_version . ', PHP ' . $min_php_version . ', and WooCommerce ' . $min_wc_version . ' to run.');
        }
        
        return array(
            'wp_version' => get_bloginfo('version'),
            'php_version' => PHP_VERSION,
            'wp_compatible' => $wp_compatible,
            'php_compatible' => $php_compatible,
            'wc_version' => $wc_version,
            'wc_compatible' => $wc_compatible,
        );
    }
}

if (!function_exists('_ubm_write_logs')) {
	/**
     * Writes logs for the plugin.
     * 
     * @since      1.0.0
     * @param string $message Message to be logged.
     * @param mixed $data Additional data to log, optional.
     */
    function _ubm_write_logs($message, $data = null) {
        $log_entry = '[' . current_time('mysql') . '] ' . $message;
    
        if (!is_null($data)) {
            $log_entry .= ' - ' . print_r($data, true);
        }

        $logs_file = fopen(GFG_BASE_DIR . DIRECTORY_SEPARATOR . "logs.txt", "a+");
        fwrite($logs_file, $log_entry . "\n");
        fclose($logs_file);
    }
}