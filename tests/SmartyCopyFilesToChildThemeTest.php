<?php
use PHPUnit\Framework\TestCase;
use WP_Mock\Tools\TestCase as WP_Mock_TestCase;
use org\bovigo\vfs\vfsStream;

// Include your main plugin file
require_once dirname(__DIR__) . '/smarty-custom-upsell-products-design.php';

class SmartyCopyFilesToChildThemeTest extends WP_Mock_TestCase
{
    private $root;

    public function setUp(): void {
        parent::setUp();
        WP_Mock::setUp();

        // Mock plugin_dir_path and get_stylesheet_directory
        WP_Mock::userFunction('plugin_dir_path', [
            'args' => '*',
            'return' => vfsStream::url('virtual_directory/mock/source/directory/')
        ]);               

        WP_Mock::userFunction('get_stylesheet_directory', [
            'return' => '/real/destination/directory/'
        ]);

        // Set up virtual file system
        $this->root = vfsStream::setup('virtual_directory');
        $source_directory = vfsStream::url(plugin_dir_path( __FILE__ ) . '/templates/woocommerce/single-product/add-to-cart/');
        foreach (['variation.php', 'variable.php', 'variable-product-upsell-design.php', 'variable-product-standard-variations.php'] as $file) {
            vfsStream::newFile($source_directory . $file)->at($this->root)->setContent('dummy content');
        }
    }

    public function tearDown(): void {
        WP_Mock::tearDown();
        parent::tearDown();
    }

    public function testSmartyCopyFilesToChildTheme()
    {
        // Call the function you are testing
        smarty_copy_files_to_child_theme(true);

        // Assert that the destination directory exists (using real path)
        $this->assertTrue(file_exists('/real/destination/directory/'));
    }
}


