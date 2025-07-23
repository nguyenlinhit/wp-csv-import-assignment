<?php
$plugin_dir = dirname(__DIR__);
$root = dirname(__DIR__, 5);
if (!file_exists($root . '/vendor/wp-phpunit/wp-phpunit/includes/bootstrap.php')) {
    $root = dirname(__DIR__, 4);
}

require $root . '/vendor/autoload.php';
require $root . '/vendor/wp-phpunit/wp-phpunit/includes/bootstrap.php';
require $plugin_dir . '/csv-page-importer.php';

tests_add_filter('muplugins_loaded', function () use ($plugin_dir) {
    require $plugin_dir . '/csv-page-importer.php';
});