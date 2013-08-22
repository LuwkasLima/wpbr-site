<?php

define('DB_NAME', 'wpbrasil');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

define('DOMAIN_CURRENT_SITE', 'wpbrasildev');

define( 'AUTH_KEY', 'auth_key' );
define( 'SECURE_AUTH_KEY', 'secure_uth_key' );
define( 'LOGGED_IN_KEY', 'logged_in_key' );
define( 'NONCE_KEY', 'nonce_key' );
define( 'AUTH_SALT', 'auth_salt' );
define( 'SECURE_AUTH_SALT', 'secure_auth_salt' );
define( 'LOGGED_IN_SALT', 'logged_in_salt' );
define( 'NONCE_SALT', 'nonce_salt' );

$table_prefix  = 'wpbr_';

define('WPLANG', 'pt_BR');
define('WP_DEBUG', true);

#define('WP_ALLOW_MULTISITE', true);
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', true);
define('DOMAIN_CURRENT_SITE', 'wpbrasildev');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);
define('COOKIE_DOMAIN', '.wpbrasildev');

if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

require_once(ABSPATH . 'wp-settings.php');
