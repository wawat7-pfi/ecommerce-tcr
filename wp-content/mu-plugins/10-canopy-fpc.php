<?php
/**
 * Plugin Name: Canopy Full-Page Cache Writer
 * Description: Captures rendered HTML and stores it in Redis for the advanced-cache.php drop-in to serve.
 * Supports guest and customer role caching while bypassing cache for Administrators.
 * Version: 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Manage `canopy_admin` cookie to allow Administrators to bypass FPC for live editing,
 * while allowing regular Customers and Guests to enjoy Full-Page Caching.
 */
add_action( 'init', function () {
    if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
        if ( empty( $_COOKIE['canopy_admin'] ) ) {
            setcookie( 'canopy_admin', '1', time() + 86400 * 30, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
        }
    } else {
        if ( ! empty( $_COOKIE['canopy_admin'] ) ) {
            setcookie( 'canopy_admin', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
        }
    }
} );

/**
 * Hook into 'template_redirect' (the last hook before WordPress renders the page)
 * to start output buffering. The buffer callback stores the HTML in Redis.
 */
add_action( 'template_redirect', function () {
    // Only proceed if advanced-cache.php flagged this as a cacheable MISS.
    if ( ! isset( $GLOBALS['_canopy_fpc_key'] ) || empty( $GLOBALS['_canopy_fpc_key'] ) ) {
        return;
    }

    // Don't cache admin, AJAX, REST, or cron.
    if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || wp_doing_cron() ) {
        return;
    }

    // Don't cache 404, search results pages, or feed.
    if ( is_404() || is_feed() ) {
        return;
    }

    // Don't cache WooCommerce transactional pages.
    if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
        return;
    }

    // Don't cache for Administrators (users who can manage options).
    if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
        return;
    }

    ob_start( function ( $html ) {
        // Minimum viable HTML check.
        if ( strlen( $html ) < 255 || false === strpos( $html, '</html>' ) ) {
            return $html;
        }

        // Don't cache non-200 responses.
        $code = http_response_code();
        if ( $code && $code !== 200 ) {
            return $html;
        }

        $fpc_key = $GLOBALS['_canopy_fpc_key'] ?? null;
        if ( ! $fpc_key || ! class_exists( 'Redis' ) ) {
            return $html;
        }

        // Strict verification: ensure WordPress login state matches the FPC cache key context.
        $is_user_logged_in = is_user_logged_in();
        $is_user_key       = ( false !== strpos( $fpc_key, 'canopy_fpc:user:' ) );
        $is_guest_key      = ( false !== strpos( $fpc_key, 'canopy_fpc:guest:' ) );

        if ( ( $is_user_logged_in && ! $is_user_key ) || ( ! $is_user_logged_in && ! $is_guest_key ) ) {
            // Mismatch between WP core login state and early cookie evaluation -> skip caching to prevent state bleed!
            return $html;
        }

        // Apply origin URL rewrite before caching (same as 00-portable-host.php).
        $current_host   = $_SERVER['HTTP_HOST'] ?? '';
        $current_scheme = ( ! empty( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ) ? 'https' : 'http';
        if ( ! empty( $current_host ) ) {
            $origins = array( '127.0.0.1', 'localhost', 'tcr-wordpress.test', '206.189.88.126', 'thecanopy-room.com' );
            $target  = $current_scheme . '://' . $current_host;
            foreach ( $origins as $origin ) {
                if ( false === strpos( $html, $origin ) ) {
                    continue;
                }
                $quoted      = preg_quote( $origin, '#' );
                $html        = preg_replace( '#(https?:)?//' . $quoted . '(?::\d+)*#i', $target, $html );
                $json_target = str_replace( '/', '\\/', $target );
                $html        = preg_replace( '#(https?:)?\\\\/\\\\/' . $quoted . '(?::\d+)*#i', $json_target, $html );
            }
        }

        try {
            $redis = new Redis();
            $host  = defined( 'WP_REDIS_HOST' ) ? WP_REDIS_HOST : '127.0.0.1';
            $port  = defined( 'WP_REDIS_PORT' ) ? WP_REDIS_PORT : 6379;

            if ( @$redis->connect( $host, $port, 0.5 ) ) {
                $stamp = gmdate( 'Y-m-d H:i:s' ) . ' UTC';
                $redis->setex( $fpc_key, 43200, $html . "\n<!-- Canopy FPC: {$stamp} -->" ); // 12 hours TTL
                $redis->close();
            }
        } catch ( Exception $e ) {
            // Silently fail.
        }

        return $html;
    } );
}, -99999 );
