<?php
namespace CPI;

if ( ! defined( 'ABSPATH' ) ) exit;

class Page_Creator {

    /**
     * Map CSV columns to WP post fields/meta. Adjust here for your CSV structure.
     * Example: CSV has 'title', 'content', 'slug', 'excerpt', etc.
     */
    protected $columnMap = [
        'title'   => 'post_title',
        'content' => 'post_content',
        'slug'    => 'post_name',   // optional
        // add more if needed
    ];

    /**
     * Create a page from CSV row.
     *
     * @param array $row CSV associative row.
     * @param int   $batch_id A custom identifier for this import.
     * @return int|WP_Error  Post ID or error.
     */
    public function create_page(array $row, $batch_id = 0) {

        $postarr = [
            'post_type'    => 'page',
            'post_status'  => 'draft',
            'post_title'   => arr_get( $row, 'title' ),
            'post_content' => arr_get( $row, 'content' ),
        ];

        // Optional: slug
        if (!empty( $row['slug'])) {
            $postarr['post_name'] = sanitize_title( $row['slug'] );
        }

        $post_id = wp_insert_post($postarr, true);
        update_post_meta( $post_id, '_wp_page_template', 'page-cpi.php' );

        if ( ! empty( $row['keyword'] ) ) {
            update_post_meta( $post_id, '_cpi_keyword', $row['keyword'] );
        }

        if (is_wp_error($post_id)) {
            return $post_id;
        }

        // Save original row as meta for debug/audit
        add_post_meta( $post_id, '_cpi_row', $row );
        add_post_meta( $post_id, '_cpi_batch_id', $batch_id );

        return $post_id;
    }
}
