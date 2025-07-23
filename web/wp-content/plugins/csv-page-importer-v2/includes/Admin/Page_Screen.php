<?php
namespace CPIv2\Admin;

use CPIv2\Service\CsvImporter;
use CPIv2\Service\PageCreator;

if ( ! defined( 'ABSPATH' ) ) exit;

class Page_Screen {
    const NONCE             = 'cpi_v2_import_nonce';
    const OPTION_LAST_BATCH = '_cpi_v2_last_batch';

    public function __construct(
        private CsvImporter $importer,
        private PageCreator $creator
    ) {}

    public function hook(): void {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
        add_action( 'admin_post_cpi_v2_import_csv', [ $this, 'handle_import' ] );
    }

    public function register_menu(): void {
        add_menu_page(
            __( 'CSV Page Importer v2', 'csv-page-importer-v2' ),
            __( 'CSV Importer v2', 'csv-page-importer-v2' ),
            'manage_options',
            'csv-page-importer-v2',
            [ $this, 'render_page' ],
            'dashicons-media-spreadsheet',
            60
        );
    }

    public function render_page(): void {
        $last = get_option( self::OPTION_LAST_BATCH );
        include CPI_V2_PATH . 'views/admin-import-screen.php';
    }

    public function handle_import(): void {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Permission denied' );
        check_admin_referer( self::NONCE );

        if ( empty( $_FILES['csv_file']['tmp_name'] ) ) {
            $this->redirect_back( 'nofile' );
        }

        $uploaded = wp_handle_upload( $_FILES['csv_file'], [
            'test_form' => false,
            'mimes'     => [ 'csv' => 'text/csv', 'txt' => 'text/plain' ],
        ] );

        if ( isset( $uploaded['error'] ) ) {
            $this->redirect_back( 'upload_error', $uploaded['error'] );
        }

        $csv_path = $uploaded['file'];
        $result   = [ 'total'=>0,'success'=>0,'fail'=>0,'errors'=>[] ];
        $batch_id = time();

        try {
            $rows = $this->importer->parse( $csv_path );
        } catch ( \Throwable $e ) {
            $result['errors'][] = $e->getMessage();
            update_option( self::OPTION_LAST_BATCH, $result );
            $this->redirect_back( 'parse_error', $e->getMessage() );
        }

        $result['total'] = count( $rows );

        foreach ( $rows as $i => $row ) {
            $post_id = $this->creator->create_page( $row, $batch_id );
            if ( is_wp_error( $post_id ) ) {
                $result['fail']++;
                $result['errors'][] = sprintf( 'Row %d: %s', $i + 2, $post_id->get_error_message() );
            } else {
                $result['success']++;
            }
        }

        update_option( self::OPTION_LAST_BATCH, $result );
        $this->redirect_back( 'done' );
    }

    private function redirect_back( string $code, string $msg = '' ): void {
        $url = add_query_arg( [ 'cpi' => $code, 'msg' => rawurlencode( $msg ) ], wp_get_referer() );
        wp_safe_redirect( $url );
        exit;
    }
}

