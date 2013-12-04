<?php
// Load environment config.
if ( file_exists( dirname( __FILE__ ) . '/dev-config.php' ) ) {
	define( 'WP_LOCAL_DEV', true );
	include( dirname( __FILE__ ) . '/dev-config.php' );
} else {
	include( dirname( __FILE__ ) . '/prod-config.php' );
}

// Language.
define( 'WPLANG', 'pt_BR' );

// Multiste.
// define('WP_ALLOW_MULTISITE', true);
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', true );
define( 'DOMAIN_CURRENT_SITE', 'wp-brasil.org' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );

// Custom Content Directory
define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/wp-content' );
define( 'WP_CONTENT_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/wp-content' );

// Sets paths.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/wp/' );
}

// Sets up WordPress vars and included files.
require_once( ABSPATH . 'wp-settings.php' );
