<?php
/**
 * Template Name: CSV Imported Page (v2)
 * Description: Display a CSV-imported page with original row data.
 */

get_header();
?>
    <main id="primary" class="site-main">
        <article <?php post_class(); ?>>
            <header class="entry-header">
                <h1><?php the_title(); ?></h1>
            </header>

            <div class="entry-content">
                <?php the_content(); ?>

                <?php $row = get_post_meta( get_the_ID(), '_cpi_row', true ); ?>
                <?php if ( is_array( $row ) && $row ) : ?>
                    <h3><?php esc_html_e( 'Original CSV Data', 'csv-page-importer-v2' ); ?></h3>
                    <table class="widefat striped">
                        <tbody>
                        <?php foreach ( $row as $k => $v ) : ?>
                            <tr>
                                <th style="width:25%;"><?php echo esc_html( $k ); ?></th>
                                <td><?php echo esc_html( $v ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </article>
    </main>
<?php get_footer(); ?>

