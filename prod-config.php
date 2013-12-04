<?php
// Database.
define( 'DB_NAME', 'wpbrasil' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );
$table_prefix = 'wpbr_';

// Salts (https://api.wordpress.org/secret-key/1.1/salt).
define( 'AUTH_KEY', 'auth_key' );
define( 'SECURE_AUTH_KEY', 'secure_uth_key' );
define( 'LOGGED_IN_KEY', 'logged_in_key' );
define( 'NONCE_KEY', 'nonce_key' );
define( 'AUTH_SALT', 'auth_salt' );
define( 'SECURE_AUTH_SALT', 'secure_auth_salt' );
define( 'LOGGED_IN_SALT', 'logged_in_salt' );
define( 'NONCE_SALT', 'nonce_salt' );

// Debug.
define( 'WP_DEBUG', false );
// define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );

// update_option('siteurl', 'http://wp-brasil.org/wp' );
// define('RELOCATE', true);
