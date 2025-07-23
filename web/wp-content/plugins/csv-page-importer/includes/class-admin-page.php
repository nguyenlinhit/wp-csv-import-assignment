<?php
namespace CPI;

if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_Page {

    const NONCE = 'cpi_import_nonce';
    const OPTION_LAST_BATCH = '_cpi_last_batch_id';

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
        add_action( 'admin_post_cpi_import_csv', [ $this, 'handle_import' ] );
    }

    public function register_menu() {
        add_menu_page(
            __( 'CSV Page Importer', 'csv-page-importer' ),
            __( 'CSV Importer', 'csv-page-importer' ),
            'manage_options',
            'csv-page-importer',
            [ $this, 'render_page' ],
            'dashicons-media-spreadsheet',
            60
        );
    }

    public function render_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'CSV Page Importer', 'csv-page-importer' ); ?></h1>
            <form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( self::NONCE ); ?>
                <input type="hidden" name="action" value="cpi_import_csv" />
                <p>
                    <label><?php esc_html_e( 'Select CSV file', 'csv-page-importer' ); ?></label><br>
                    <input type="file" name="csv_file" accept=".csv" required>
                </p>
                <p>
                    <button class="button button-primary"><?php esc_html_e( 'Import', 'csv-page-importer' ); ?></button>
                </p>
            </form>
            <?php $this->render_last_result(); ?>
        </div>
        <?php
    }

    protected function render_last_result() {
        $batch = get_option( self::OPTION_LAST_BATCH );
        if ( empty( $batch ) ) {
            return;
        }
        echo '<hr><h2>Last Import</h2>';
        echo '<p>Total: ' . intval( $batch['total'] ) . ', Success: ' . intval( $batch['success'] ) . ', Fail: ' . intval( $batch['fail'] ) . '</p>';
        if ( ! empty( $batch['errors'] ) ) {
            echo '<details><summary>Errors</summary><ul>';
            foreach ( $batch['errors'] as $err ) {
                echo '<li>' . esc_html( $err ) . '</li>';
            }
            echo '</ul></details>';
        }
    }

    public function handle_import() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Permission denied.' );
        }
        check_admin_referer( self::NONCE );

        if ( empty( $_FILES['csv_file']['tmp_name'] ) ) {
            wp_redirect( add_query_arg( 'cpi', 'nofile', wp_get_referer() ) );
            exit;
        }

        $file = $_FILES['csv_file'];

        // Use WP upload handler
        $overrides = [
            'test_form' => false,
            'mimes'     => [ 'csv' => 'text/csv' ],
        ];
        $uploaded = wp_handle_upload( $file, $overrides );

        if ( isset( $uploaded['error'] ) ) {
            wp_redirect( add_query_arg( 'cpi', 'upload_error', wp_get_referer() ) );
            exit;
        }

        $csv_path = $uploaded['file'];

        $importer = new CSV_Importer();
        $creator  = new Page_Creator();

        $rows = [];
        $result = [
            'total'   => 0,
            'success' => 0,
            'fail'    => 0,
            'errors'  => [],
        ];

        // Create batch id
        $batch_id = time();

        try {
            $rows = $importer->parse( $csv_path );
        } catch ( \Exception $e ) {
            $result['errors'][] = $e->getMessage();
            update_option( self::OPTION_LAST_BATCH, $result );
            wp_redirect( add_query_arg( 'cpi', 'parse_error', wp_get_referer() ) );
            exit;
        }

        $result['total'] = count( $rows );

        foreach ( $rows as $index => $row ) {
            $post_id = $creator->create_page( $row, $batch_id );
            if ( is_wp_error( $post_id ) ) {
                $result['fail']++;
                $result['errors'][] = sprintf( 'Row %d: %s', $index + 2, $post_id->get_error_message() ); // +2 for header row offset
            } else {
                $result['success']++;
            }
        }

        update_option( self::OPTION_LAST_BATCH, $result );
        wp_redirect( add_query_arg( 'cpi', 'done', wp_get_referer() ) );
        exit;
    }
}
