<?php
$root = dirname(__DIR__, 5); // /var/www/html  (chỉnh 4 nếu khác)
define( 'ABSPATH', $root . '/web/' );

define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL',  'admin@example.org' );
define( 'WP_TESTS_TITLE',  'Test Blog' );
define( 'WP_PHP_BINARY',   PHP_BINARY );

define( 'DB_NAME',     'wordpress_test' );
define( 'DB_USER',     'db' );
define( 'DB_PASSWORD', 'db' );
define( 'DB_HOST',     'db' );

define( 'WP_DEBUG', true );
$table_prefix = 'wptests_';