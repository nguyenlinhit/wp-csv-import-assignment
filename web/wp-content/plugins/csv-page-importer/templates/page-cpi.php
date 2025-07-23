<?php
/**
 * Template Name: CSV Imported Page
 * Template Post Type: page
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

// Get meta data originally saved during import
$row = get_post_meta( get_the_ID(), '_cpi_row', true );
$batch_id = get_post_meta( get_the_ID(), '_cpi_batch_id', true );

// WP Standard Content
the_post();
?>
<main id="primary" class="site-main">
    <article <?php post_class(); ?>>
        <header class="entry-header">
            <h1 class="entry-title"><?php the_title(); ?></h1>
        </header>

        <div class="entry-content">
            <?php the_content(); ?>

            <?php if ( $row ) : ?>
                <hr>
                <h2><?php esc_html_e( 'Original CSV Data', 'csv-page-importer' ); ?></h2>
                <table class="cpi-table">
                    <tbody>
                    <?php foreach ( $row as $key => $val ) : ?>
                        <tr>
                            <th><?php echo esc_html( $key ); ?></th>
                            <td><?php echo wp_kses_post( nl2br( $val ) ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <p><em>Batch ID: <?php echo esc_html( $batch_id ); ?></em></p>
            <?php endif; ?>
        </div>
    </article>
</main>

<style>
.cpi-table{border-collapse:collapse;width:100%;max-width:100%}
.cpi-table th,.cpi-table td{border:1px solid #ddd;padding:6px 10px;text-align:left}
.cpi-table th{background:#f7f7f7;width:180px}
</style>

<?php
get_footer(); ?>
