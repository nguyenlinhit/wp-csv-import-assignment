<?php
namespace CPIv2\Security;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if ( ! defined( 'ABSPATH' ) ) exit;

class Token_Service {

    public function generate( int $post_id, int $ttl = 3600 ): string {
        if ( ! defined( 'CPI_JWT_SECRET' ) ) {
            throw new \Exception( 'JWT secret not defined' );
        }
        $payload = [
            'pid' => $post_id,
            'iat' => time(),
            'exp' => time() + $ttl,
        ];
        return JWT::encode( $payload, \CPI_JWT_SECRET, 'HS256' );
    }

    public function verify( string $token, int $post_id ): void {
        $decoded = JWT::decode( $token, new Key( \CPI_JWT_SECRET, 'HS256' ) );
        if ( (int) ( $decoded->pid ?? 0 ) !== $post_id ) {
            throw new \Exception( 'Token not for this page' );
        }
    }
}
