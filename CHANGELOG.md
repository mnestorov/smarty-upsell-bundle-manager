# Changelog

### 1.0.0 (2023.11.24)
- Initial release.

### 1.0.1 (2024.11.17)
- Added functionality to make variable image sizes dynamic and configurable via the plugin settings.

### 1.0.2 (2024.12.05)
- Added additional description text for the options in plugin settings

### 1.0.3 (2024.12.05)
- Added doc blocks for each function, also added additional prefix 'ubm'

### 1.0.4 (2025.01.17)
- Added additional functionality for the plugin settings page, bug fixes.

### 1.0.5 (2025.01.30)
- Added [HPOS (High-Performance Order Storage)](https://woocommerce.com/document/high-performance-order-storage/) compatibility. The HPOS replaces the old post-based order system with custom database tables. 
    - Implemented WooCommerce's HPOS system to improve order storage performance.
    - Replaced `wc_get_order_item_meta()` with `get_meta()` for better HPOS support.
    - Ensured proper retrieval of order data using `wc_get_container()` for HPOS environments.
- Fixed custom attribute saving:
    - Switched from `update_option()` to `update_term_meta()` for saving attribute metadata.
- Fixed variation label field issue:
    - Custom fields for variations are now properly editable in the WooCommerce product edit screen.