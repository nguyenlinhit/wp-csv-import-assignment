<?php
/**
 * #ddev-generated: Automatically generated WordPress settings file.
 * ddev manages this file and may delete or overwrite the file unless this comment is removed.
 * It is recommended that you leave this file alone.
 *
 * @package ddevapp
 */

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/** Authentication Unique Keys and Salts. */
define( 'AUTH_KEY', 'CcfnBThRsysEyJloBYAIXvUzobuouiZIVnkApvlgILoYCMpTzcOPauOvLQPXDHnd' );
define( 'SECURE_AUTH_KEY', 'dUGqdbteTynMjmBPxRxDqOLqJWoTWONFaUFNslNxzDyAkettzoysSDxWwGIjisbb' );
define( 'LOGGED_IN_KEY', 'ycMelOvMdGgXvscuBTqUFcoDAKQvJDVtputpYGsbnwnciVsWihgWHDLTSUjaJfvt' );
define( 'NONCE_KEY', 'xBPpwsVVmNwwmbwciqLitmiOVtLElsfqAAgSxFoBtaBZACYhysGHxZiueLpbcXJa' );
define( 'AUTH_SALT', 'viJCEyJXFiGFwqFMQUtEMSpJtvMsOeUzUniCKvWpGeqTZPTzCHFiUzyNePtOcbBe' );
define( 'SECURE_AUTH_SALT', 'IcNyOCAOnryJdznKcfNlpUaeDRBgBGtvbrnZbjFeSGLIxccjZVeHWxUNWBCVCmVJ' );
define( 'LOGGED_IN_SALT', 'mMqreUSyZsLKVoquUoTSyRcIKAMatrGvkgCgMzmvedpAlehfQEWTvHqxiYLIpjZJ' );
define( 'NONCE_SALT', 'UKDYROKjrzvqwBsCuRWHrEyaIoKUzMShogYSZLeDOLZUQpbxZrOahqxTSIFCnfRB' );

/* Add any custom values between this line and the "stop editing" line. */
//add new jwt secret dUGqdbteTynMjmBPxRxDqOLqJWoTWONFaUFNslNxzDyAkettzoysSDxWwGIjisbb
//define('CPI_JWT_SECRET', 'dUGqdbteTynMjmBPxRxDqOLqJWoTWONFaUFNslNxzDyAkettzoysSDxWwGIjisbb');

// Render ENV
if ( getenv('DB_NAME') ) {
    define( 'DB_NAME', getenv('DB_NAME') );
    define( 'DB_USER', getenv('DB_USER') );
    define( 'DB_PASSWORD', getenv('DB_PASSWORD') );
    define( 'DB_HOST', getenv('DB_HOST') );
}

if ( getenv('WP_HOME') ) {
    define( 'WP_HOME', getenv('WP_HOME') );
    define( 'WP_SITEURL', getenv('WP_SITEURL') ?: getenv('WP_HOME') );
}

if ( getenv('CPI_JWT_SECRET') ) {
    define( 'CPI_JWT_SECRET', getenv('CPI_JWT_SECRET') );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
defined( 'ABSPATH' ) || define( 'ABSPATH', dirname( __FILE__ ) . '/' );

// Include for settings managed by ddev.
$ddev_settings = __DIR__ . '/wp-config-ddev.php';
if ( ! defined( 'DB_USER' ) && getenv( 'IS_DDEV_PROJECT' ) == 'true' && is_readable( $ddev_settings ) ) {
	require_once( $ddev_settings );
}

/** Include wp-settings.php */
if ( file_exists( ABSPATH . '/wp-settings.php' ) ) {
	require_once ABSPATH . '/wp-settings.php';
}
//define('WP_DEBUG', true); define('WP_DEBUG_LOG', true); define('WP_DEBUG_DISPLAY', false);
