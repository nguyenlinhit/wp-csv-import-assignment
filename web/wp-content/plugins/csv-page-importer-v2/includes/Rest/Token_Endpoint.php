<?php
namespace CPIv2\Rest;

use CPIv2\Security\Token_Service;

if ( ! defined( 'ABSPATH' ) ) exit;

class Token_Endpoint {

    public function __construct( private Token_Service $token ) {}

    public function hook(): void {
        add_action( 'rest_api_init', [ $this, 'register' ] );
    }

    public function register(): void {
        register_rest_route( 'cpi/v2', '/token', [
            'methods'             => 'POST',
            'permission_callback' => fn() => current_user_can( 'manage_options' ),
            'callback'            => [ $this, 'create_token' ],
            'args'                => [
                'post_id' => [ 'type' => 'integer', 'required' => true ],
                'ttl'     => [ 'type' => 'integer', 'required' => false, 'default' => 3600 ],
            ],
        ] );
    }

    public function create_token( \WP_REST_Request $req ) {
        $post_id = (int) $req['post_id'];
        $ttl     = (int) $req['ttl'];

        if ( ! get_post( $post_id ) ) {
            return new \WP_Error( 'not_found', 'Post not found', [ 'status' => 404 ] );
        }

        try {
            return [ 'token' => $this->token->generate( $post_id, $ttl ) ];
        } catch ( \Throwable $e ) {
            return new \WP_Error( 'token_error', $e->getMessage(), [ 'status' => 500 ] );
        }
    }
}