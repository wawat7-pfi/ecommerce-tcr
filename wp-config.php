<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * This has been slightly modified (to read environment variables) for use in Docker.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// IMPORTANT: this file needs to stay in-sync with https://github.com/WordPress/WordPress/blob/master/wp-config-sample.php
// (it gets parsed by the upstream wizard in https://github.com/WordPress/WordPress/blob/f27cb65e1ef25d11b535695a660e7282b98eb742/wp-admin/setup-config.php#L356-L392)

// a helper function to lookup "env_FILE", "env", then fallback
if (!function_exists('getenv_docker')) {
	// https://github.com/docker-library/wordpress/issues/588 (WP-CLI will load this file 2x)
	function getenv_docker($env, $default) {
		if ($fileEnv = getenv($env . '_FILE')) {
			return rtrim(file_get_contents($fileEnv), "\r\n");
		}
		else if (($val = getenv($env)) !== false) {
			return $val;
		}
		else {
			return $default;
		}
	}
}

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', getenv_docker('WORDPRESS_DB_NAME', 'wordpress') );

/** Database username */
define( 'DB_USER', getenv_docker('WORDPRESS_DB_USER', 'root') );

/** Database password */
define( 'DB_PASSWORD', getenv_docker('WORDPRESS_DB_PASSWORD', '') );

$raw_db_host = getenv_docker('WORDPRESS_DB_HOST', '127.0.0.1');
define( 'DB_HOST', ($raw_db_host === 'localhost' && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? '127.0.0.1' : $raw_db_host );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', getenv_docker('WORDPRESS_DB_CHARSET', 'utf8mb4') );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', getenv_docker('WORDPRESS_DB_COLLATE', '') );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         getenv_docker('WORDPRESS_AUTH_KEY',         '11d398e3f56149262be405ed724b7058a311e890') );
define( 'SECURE_AUTH_KEY',  getenv_docker('WORDPRESS_SECURE_AUTH_KEY',  '427cb9e09d21c303f3bf1b69318f230368542633') );
define( 'LOGGED_IN_KEY',    getenv_docker('WORDPRESS_LOGGED_IN_KEY',    'ecfd0230ffba17abdcf08e1efc2d49819e7d89cd') );
define( 'NONCE_KEY',        getenv_docker('WORDPRESS_NONCE_KEY',        '9627f2d09815a20342c20d3adbb647807ae114ab') );
define( 'AUTH_SALT',        getenv_docker('WORDPRESS_AUTH_SALT',        '09530223db0f0f0cfa50db12fe927611dd30def8') );
define( 'SECURE_AUTH_SALT', getenv_docker('WORDPRESS_SECURE_AUTH_SALT', 'b12da35079208e29651690fbe9e499f60064b6da') );
define( 'LOGGED_IN_SALT',   getenv_docker('WORDPRESS_LOGGED_IN_SALT',   '334c3494b856aa965b2d6e9bed42a791661828c4') );
define( 'NONCE_SALT',       getenv_docker('WORDPRESS_NONCE_SALT',       '4e31e2b01ebb8d0c4a3cc347c9edeffd2c711100') );
// (See also https://wordpress.stackexchange.com/a/152905/199287)

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = getenv_docker('WORDPRESS_TABLE_PREFIX', 'cnp_');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', !!getenv_docker('WORDPRESS_DEBUG', '') );

/* Add any custom values between this line and the "stop editing" line. */

// Optimization for Laragon local development performance
if (!defined('WP_HOME') && isset($_SERVER['HTTP_HOST'])) {
	$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
	define('WP_HOME', $protocol . $_SERVER['HTTP_HOST']);
	define('WP_SITEURL', $protocol . $_SERVER['HTTP_HOST']);
}
define('DISABLE_WP_CRON', true);

// If we're behind a proxy server and using HTTPS, we need to alert WordPress of that fact
// see also https://wordpress.org/support/article/administration-over-ssl/#using-a-reverse-proxy
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false) {
	$_SERVER['HTTPS'] = 'on';
}
// (we include this by default because reverse proxying is extremely common in container environments)

if ($configExtra = getenv_docker('WORDPRESS_CONFIG_EXTRA', '')) {
	eval($configExtra);
}

// === Redis Object Cache Configuration ===
if ( ! defined( 'WP_REDIS_HOST' ) ) {
	if ( getenv_docker( 'WORDPRESS_REDIS_HOST', '' ) ) {
		define( 'WP_REDIS_HOST', getenv_docker( 'WORDPRESS_REDIS_HOST', 'tcr_redis' ) );
	} elseif ( file_exists( '/.dockerenv' ) || getenv( 'WORDPRESS_DB_HOST' ) ) {
		define( 'WP_REDIS_HOST', 'tcr_redis' );
	} else {
		define( 'WP_REDIS_HOST', '127.0.0.1' );
	}
}
if ( ! defined( 'WP_REDIS_PORT' ) ) {
	define( 'WP_REDIS_PORT', 6379 );
}
if ( ! defined( 'WP_REDIS_DATABASE' ) ) {
	define( 'WP_REDIS_DATABASE', 0 );
}
if ( ! defined( 'WP_REDIS_TIMEOUT' ) ) {
	define( 'WP_REDIS_TIMEOUT', 1 );
}
if ( ! defined( 'WP_REDIS_READ_TIMEOUT' ) ) {
	define( 'WP_REDIS_READ_TIMEOUT', 1 );
}
if ( ! defined( 'WP_REDIS_PREFIX' ) ) {
	define( 'WP_REDIS_PREFIX', 'cnp_' );
}
if ( ! defined( 'WP_REDIS_DISABLE_BANNERS' ) ) {
	define( 'WP_REDIS_DISABLE_BANNERS', true );
}
if ( ! defined( 'WP_REDIS_MAXTTL' ) ) {
	define( 'WP_REDIS_MAXTTL', 86400 );
}
if ( ! defined( 'WP_CACHE' ) ) {
	define( 'WP_CACHE', true );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
