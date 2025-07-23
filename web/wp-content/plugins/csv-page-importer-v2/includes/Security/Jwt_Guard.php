<?php
namespace CPIv2\Security;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jwt_Guard {

    public function __construct( private Token_Service $token ) {}

    public function hook(): void {
        add_action( 'template_redirect', [ $this, 'maybe_block' ] );
        add_filter( 'preview_post_link', [ $this, 'add_token_to_preview' ], 10, 2 );
    }

    public function maybe_block(): void {
        if ( is_admin() || ! is_singular( 'page' ) ) return;

        $post_id  = get_the_ID();
        $batch_id = get_post_meta( $post_id, '_cpi_batch_id', true );
        if ( empty( $batch_id ) ) return; // not an imported page

        $token = isset( $_GET['token'] ) ? sanitize_text_field( $_GET['token'] ) : '';
        if ( ! $token ) {
            status_header( 403 );
            wp_die( esc_html__( 'Missing token.', 'csv-page-importer-v2' ), 403 );
        }

        try {
            $this->token->verify( $token, $post_id );
        } catch ( \Throwable $e ) {
            status_header( 403 );
            wp_die( esc_html( $e->getMessage() ), 403 );
        }
    }

    public function add_token_to_preview( string $link, \WP_Post $post ): string {
        if ( ! get_post_meta( $post->ID, '_cpi_batch_id', true ) ) return $link;
        try {
            $token = $this->token->generate( $post->ID, 3600 );
            $link  = add_query_arg( 'token', $token, $link );
        } catch ( \Throwable $e ) {}
        return $link;
    }
}
