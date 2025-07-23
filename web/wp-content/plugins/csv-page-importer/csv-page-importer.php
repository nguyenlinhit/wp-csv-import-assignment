<?php
/**
 * Plugin Name: CSV Page Importer
 * Description: Import a CSV file and create WordPress Pages as draft.
 * Version: 1.0.0
 * Author: Linh (Larry) Nguyen
 * Text Domain: csv-page-importer
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'CPI_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'CPI_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );
define( 'CPI_PLUGIN_VER',  '1.0.0' );

require_once CPI_PLUGIN_PATH . 'includes/helpers.php';
require_once CPI_PLUGIN_PATH . 'includes/class-csv-importer.php';
require_once CPI_PLUGIN_PATH . 'includes/class-page-creator.php';
require_once CPI_PLUGIN_PATH . 'includes/class-admin-page.php';

//required composer autoload
// Try project-root vendor first, then plugin vendor
$root_autoload   = dirname( ABSPATH ) . '/vendor/autoload.php'; // /var/www/html/vendor/autoload.php
$plugin_autoload = CPI_PLUGIN_PATH . 'vendor/autoload.php';

if ( file_exists( $root_autoload ) ) {
    require_once $root_autoload;
} elseif ( file_exists( $plugin_autoload ) ) {
    require_once $plugin_autoload;
} else {
      throw new \Exception( 'Cannot import composer.' );
}

//use libraries
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// === Register and load our custom page template from the plugin ===
add_filter( 'theme_page_templates', function ( $templates ) {
    $templates['page-cpi.php'] = __( 'CSV Imported Page', 'csv-page-importer' );
    return $templates;
} );

add_filter( 'template_include', function ( $template ) {
    if ( is_singular( 'page' ) ) {
        $tpl = get_post_meta( get_the_ID(), '_wp_page_template', true );
        if ( $tpl === 'page-cpi.php' ) {
            $plugin_tpl = CPI_PLUGIN_PATH . 'templates/page-cpi.php';
            if ( file_exists( $plugin_tpl ) ) {
                return $plugin_tpl;
            }
        }
    }
    return $template;
} );

// [cpi_meta key="keyword"]
add_shortcode( 'cpi_meta', function( $atts ) {
    $atts = shortcode_atts( [
        'key'   => '',
        'field' => '', // alias of key
    ], $atts, 'cpi_meta' );

    $k = $atts['key'] ?: $atts['field'];
    if ( ! $k ) return '';

    $row = get_post_meta( get_the_ID(), '_cpi_row', true );
    if ( ! $row || empty( $row[ $k ] ) ) return '';

    return esc_html( $row[ $k ] );
} );

// Bootstrap
add_action( 'plugins_loaded', function () {
    // init admin page
    if ( is_admin() ) {
        new \CPI\Admin_Page();
    }
} );

// Require token only for pages imported by CSV
add_action( 'template_redirect', function () {

    if ( ! is_singular( 'page' ) ) {
        return;
    }

    $post_id = get_the_ID();

    // Only block pages created by plugins (with meta _cpi_batch_id)
    $batch_id = get_post_meta( $post_id, '_cpi_batch_id', true );
    if ( empty( $batch_id ) ) {
        return; // page thường -> không check
    }

    $token = isset( $_GET['token'] ) ? sanitize_text_field( $_GET['token'] ) : '';

    if ( empty( $token ) ) {
        cpi_forbid( 'Missing token.' );
    }

    try {
        $decoded = JWT::decode( $token, new Key( CPI_JWT_SECRET, 'HS256' ) );
        // Optional: check more on payload (exp, post_id, batch_id, roles...)
        if ( ! empty( $decoded->pid ) && intval( $decoded->pid ) !== $post_id ) {
            cpi_forbid( 'Token not for this page.' );
        }
        if ( ! empty( $decoded->exp ) && $decoded->exp < time() ) {
            cpi_forbid( 'Token expired.' );
        }
    } catch ( \Throwable $e ) {
        cpi_forbid( 'Invalid token: ' . $e->getMessage() );
    }
} );

function cpi_forbid( $msg = 'Forbidden' ) {
    status_header( 403 );
    wp_die( esc_html( $msg ), 403 );
}

//(Optional) REST API endpoint to issue tokens
add_action( 'rest_api_init', function () {
    register_rest_route( 'cpi/v1', '/token', [
        'methods'  => 'POST',
        'callback' => function ( $req ) {
            $post_id = intval( $req->get_param( 'post_id' ) );
            $ttl     = intval( $req->get_param( 'ttl' ) ?: 3600 );

            if ( ! current_user_can( 'manage_options' ) ) {
                return new \WP_Error( 'forbidden', 'No permission', [ 'status' => 403 ] );
            }
            if ( ! get_post( $post_id ) ) {
                return new \WP_Error( 'not_found', 'Post not found', [ 'status' => 404 ] );
            }
            $token = \CPI\generate_token_for_page( $post_id, $ttl );
            return [ 'token' => $token ];
        },
        'permission_callback' => '__return_true', // right to check what i did in callback
    ] );
} );

//add place to generate token for page
add_action( 'add_meta_boxes', function() {
    add_meta_box(
        'cpi_token_box',
        'CSV Token Preview',
        function( $post ) {
            if ( ! get_post_meta( $post->ID, '_cpi_batch_id', true ) ) return;

            $token = \CPI\generate_token_for_page( $post->ID, 3600 );
            $url   = add_query_arg( 'token', $token, get_preview_post_link( $post ) );
            echo '<p><input type="text" style="width:100%" value="'. esc_attr( $url ) .'" onclick="this.select()" /></p>';
        },
        'page',
        'side'
    );
} );