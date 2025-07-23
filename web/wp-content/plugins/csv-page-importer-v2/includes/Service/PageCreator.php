<?php
namespace CPIv2\Service;

if ( ! defined( 'ABSPATH' ) ) exit;

class PageCreator {

    private array $columnMap = [
        'title'   => 'post_title',
        'slug'    => 'post_name',
        'content' => 'post_content',
        'excerpt' => 'post_excerpt',
    ];

    /** @return int|\WP_Error */
    public function create_page( array $row, int $batch_id ) {
        $postArr = [
            'post_type'   => 'page',
            'post_status' => 'draft',
        ];

        foreach ( $this->columnMap as $csv => $wp ) {
            if ( isset( $row[ $csv ] ) ) {
                $postArr[ $wp ] = $row[ $csv ];
            }
        }

        if ( empty( $postArr['post_title'] ) ) {
            return new \WP_Error( 'cpi_v2_no_title', 'Missing title column' );
        }

        $post_id = wp_insert_post( $postArr, true );
        if ( is_wp_error( $post_id ) ) return $post_id;

        update_post_meta( $post_id, '_cpi_row', $row );
        update_post_meta( $post_id, '_cpi_batch_id', $batch_id );
        update_post_meta( $post_id, '_wp_page_template', 'page-cpi.php' );

        return $post_id;
    }
}
