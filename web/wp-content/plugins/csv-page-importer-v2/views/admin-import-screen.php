<?php
/** @var array|null $last */
?>
<div class="wrap">
    <h1><?php esc_html_e( 'CSV Page Importer v2', 'csv-page-importer-v2' ); ?></h1>

    <form method="post" enctype="multipart/form-data"
          action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php wp_nonce_field( CPIv2\Admin\Page_Screen::NONCE ); ?>
        <input type="hidden" name="action" value="cpi_v2_import_csv" />

        <p>
            <label><strong><?php esc_html_e( 'Select CSV file', 'csv-page-importer-v2' ); ?></strong></label><br>
            <input type="file" name="csv_file" accept=".csv,.txt" required>
        </p>

        <p>
            <button class="button button-primary">
                <?php esc_html_e( 'Import', 'csv-page-importer-v2' ); ?>
            </button>
        </p>
    </form>

    <?php if ( $last ) : ?>
        <hr>
        <h2><?php esc_html_e( 'Last Import', 'csv-page-importer-v2' ); ?></h2>
        <p>
            Total: <?php echo intval( $last['total'] ?? 0 ); ?>,
            Success: <?php echo intval( $last['success'] ?? 0 ); ?>,
            Fail: <?php echo intval( $last['fail'] ?? 0 ); ?>
        </p>
        <?php if ( ! empty( $last['errors'] ) ) : ?>
            <details>
                <summary><?php esc_html_e( 'Errors', 'csv-page-importer-v2' ); ?></summary>
                <ul>
                    <?php foreach ( $last['errors'] as $err ) : ?>
                        <li><?php echo esc_html( $err ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </details>
        <?php endif; ?>
    <?php endif; ?>
</div>
