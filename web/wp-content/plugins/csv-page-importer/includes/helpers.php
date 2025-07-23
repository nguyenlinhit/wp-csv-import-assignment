<?php
namespace CPI;

if ( ! defined( 'ABSPATH' ) ) exit;

use Firebase\JWT\JWT;

/**
 * Simple logger to WP debug log (enable WP_DEBUG_LOG).
 */
function log_it( $msg, $context = [] ) {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( '[CPI] ' . $msg . ( $context ? ' ' . wp_json_encode( $context ) : '' ) );
    }
}

/**
 * Return sanitized array value.
 */
function arr_get( $arr, $key, $default = '' ) {
    return isset( $arr[ $key ] ) ? $arr[ $key ] : $default;
}

function generate_token_for_page( $post_id, $ttl_seconds = 3600 ) {
    $payload = [
        'pid' => (int) $post_id,         // Bind token to this post
        'iat' => time(),
        'exp' => time() + $ttl_seconds,  // expires in $ttl_seconds
    ];
    return JWT::encode( $payload, CPI_JWT_SECRET, 'HS256' );
}
