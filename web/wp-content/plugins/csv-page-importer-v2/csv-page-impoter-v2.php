<?php
/**
 * Plugin Name: CSV Page Importer v2
 * Description: Import pages from CSV, preview protected by JWT. Refactored structure.
 * Version: 2.0.0
 * Author: Linh (Larry) Nguyen
 * Text Domain: csv-page-importer-v2
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'CPI_V2_PATH', plugin_dir_path( __FILE__ ) );
define( 'CPI_V2_URL',  plugin_dir_url( __FILE__ ) );
define( 'CPI_V2_VER',  '2.0.0' );

// simple PSR-4-ish autoloader (no composer required)
spl_autoload_register( static function ( $class ) {
    if ( strpos( $class, 'CPIv2\\' ) !== 0 ) return;
    $rel  = substr( $class, strlen( 'CPIv2\\' ) );
    $file = CPI_V2_PATH . 'includes/' . str_replace( '\\', '/', $rel ) . '.php';
    if ( file_exists( $file ) ) require $file;
});

// load bootstrap
require CPI_V2_PATH . 'bootstrap.php';

// ensure secret
register_activation_hook( __FILE__, static function () {
    if ( ! defined( 'CPI_JWT_SECRET' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( 'Please define CPI_JWT_SECRET in wp-config.php before activating CSV Page Importer v2.' );
    }
});
