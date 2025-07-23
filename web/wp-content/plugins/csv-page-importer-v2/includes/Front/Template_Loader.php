<?php
namespace CPIv2\Front;

if ( ! defined( 'ABSPATH' ) ) exit;

class Template_Loader {

    public function hook(): void {
        add_filter( 'theme_page_templates', [ $this, 'register_template' ] );
        add_filter( 'template_include', [ $this, 'maybe_load' ] );
    }

    public function register_template( array $templates ): array {
        $templates['page-cpi.php'] = __( 'CSV Imported Page', 'csv-page-importer-v2' );
        return $templates;
    }

    public function maybe_load( string $template ): string {
        if ( is_singular( 'page' ) ) {
            $tpl = get_post_meta( get_the_ID(), '_wp_page_template', true );
            $plugin_tpl = CPI_V2_PATH . 'templates/page-cpi.php';
            if ( 'page-cpi.php' === $tpl && file_exists( $plugin_tpl ) ) {
                return $plugin_tpl;
            }
        }
        return $template;
    }
}
