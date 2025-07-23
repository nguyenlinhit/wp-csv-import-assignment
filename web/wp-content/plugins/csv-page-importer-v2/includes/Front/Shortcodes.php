<?php
namespace CPIv2\Front;

if ( ! defined( 'ABSPATH' ) ) exit;

class Shortcodes {

    public function hook(): void {
        add_shortcode( 'cpi_meta', [ $this, 'meta_shortcode' ] );
    }

    public function meta_shortcode( $atts ): string {
        $atts = shortcode_atts( [ 'key' => '', 'field' => '' ], $atts, 'cpi_meta' );
        $key  = $atts['key'] ?: $atts['field'];
        if ( ! $key ) return '';
        $row = get_post_meta( get_the_ID(), '_cpi_row', true );
        return esc_html( (string) ( $row[ $key ] ?? '' ) );
    }
}
