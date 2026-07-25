<?php
/**
 * Canopy Full-Page Cache (advanced-cache.php drop-in).
 *
 * Serves cached HTML pages directly from Redis on cache HIT.
 * Supports separate cache buckets for Guests vs Logged-in Customers.
 * Site Administrators (canopy_admin cookie) bypass cache for live editing.
 *
 * @package Canopy
 * @version 2.1.0
 */

if ( ! defined( 'WP_CACHE' ) || ! WP_CACHE ) {
    return;
}

if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || 'GET' !== $_SERVER['REQUEST_METHOD'] ) {
    return;
}

// Skip cache for site Administrators / Staff (canopy_admin cookie).
if ( ! empty( $_COOKIE['canopy_admin'] ) ) {
    return;
}

// Determine user context (guest vs logged-in customer).
$user_context = 'guest';
foreach ( array_keys( $_COOKIE ) as $name ) {
    if ( preg_match( '/^wordpress_logged_in_/', $name ) ) {
        $user_context = 'user';
        break;
    }
}

// Skip WooCommerce transactional pages, admin, and API.
$uri = $_SERVER['REQUEST_URI'] ?? '';
$skip = array( '/cart', '/checkout', '/my-account', '/wp-admin', '/wp-login', '/wp-json', '/wp-cron', '/xmlrpc', '/wc-api' );
foreach ( $skip as $p ) {
    if ( false !== strpos( $uri, $p ) ) {
        return;
    }
}

if ( ! empty( $_POST ) || isset( $_GET['preview'] ) || isset( $_GET['customize_changeset_uuid'] ) ) {
    return;
}

if ( ! class_exists( 'Redis' ) ) {
    return;
}

// Connect to Redis.
try {
    $canopy_fpc = new Redis();
    $host = defined( 'WP_REDIS_HOST' ) ? WP_REDIS_HOST : '127.0.0.1';
    $port = defined( 'WP_REDIS_PORT' ) ? WP_REDIS_PORT : 6379;
    if ( ! @$canopy_fpc->connect( $host, $port, 0.5 ) ) {
        return;
    }
} catch ( Exception $e ) {
    return;
}

// Build cache key based on user context (guest vs customer) + device.
$scheme    = ( ! empty( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ) ? 'https' : 'http';
$host_name = $_SERVER['HTTP_HOST'] ?? 'localhost';
$path      = strtok( $uri, '?' );
$qs        = $_SERVER['QUERY_STRING'] ?? '';
$ua        = strtolower( $_SERVER['HTTP_USER_AGENT'] ?? '' );
$device    = preg_match( '/mobile|android|iphone|ipod/i', $ua ) ? 'm' : 'd';
$fpc_key   = 'canopy_fpc:' . $user_context . ':' . md5( $scheme . $host_name . $path . $qs . $device );

// Try cache HIT.
$cached = $canopy_fpc->get( $fpc_key );
if ( false !== $cached && ! empty( $cached ) ) {
    header( 'X-Canopy-Cache: HIT (' . strtoupper($user_context) . ')' );
    echo $cached;
    $canopy_fpc->close();
    exit;
}

// Cache MISS — pass key to globals for MU-Plugin to handle writing.
header( 'X-Canopy-Cache: MISS (' . strtoupper($user_context) . ')' );
$GLOBALS['_canopy_fpc_key'] = $fpc_key;
$canopy_fpc->close();
