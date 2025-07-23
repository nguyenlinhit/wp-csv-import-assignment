<?php
namespace CPIv2;

if ( ! defined( 'ABSPATH' ) ) exit;

use CPIv2\Admin\Page_Screen;
use CPIv2\Front\Shortcodes;
use CPIv2\Front\Template_Loader;
use CPIv2\Rest\Token_Endpoint;
use CPIv2\Security\Jwt_Guard;
use CPIv2\Security\Token_Service;
use CPIv2\Service\CsvImporter;
use CPIv2\Service\PageCreator;

add_action( 'plugins_loaded', static function () {

    // Admin
    if ( is_admin() ) {
        ( new Page_Screen( new CsvImporter(), new PageCreator() ) )->hook();
    }

    // Front
    $token = new Token_Service();
    ( new Jwt_Guard( $token ) )->hook();
    ( new Template_Loader() )->hook();
    ( new Shortcodes() )->hook();

    // REST (optional)
    ( new Token_Endpoint( $token ) )->hook();
});

