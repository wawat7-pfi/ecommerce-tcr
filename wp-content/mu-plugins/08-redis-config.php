<?php
/**
 * Plugin Name: Canopy Redis Configuration
 * Description: Environment-aware Redis host configuration for Laragon and Docker.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Auto-detect Redis host based on environment.
 *
 * Laragon (local): 127.0.0.1:6379
 * Docker:          wp_redis:6379
 */
function canopy_get_redis_host() {
    // Docker environment check
    if ( file_exists( '/.dockerenv' ) || getenv( 'WORDPRESS_DB_HOST' ) ) {
        return 'wp_redis';
    }

    // Default: Laragon / local
    return '127.0.0.1';
}

/**
 * Define Redis constants if not already defined.
 * These must be defined BEFORE Redis Object Cache plugin loads.
 */
if ( ! defined( 'WP_REDIS_HOST' ) ) {
    define( 'WP_REDIS_HOST', canopy_get_redis_host() );
}

if ( ! defined( 'WP_REDIS_PORT' ) ) {
    define( 'WP_REDIS_PORT', 6379 );
}

if ( ! defined( 'WP_REDIS_DATABASE' ) ) {
    define( 'WP_REDIS_DATABASE', 0 ); // Use database 0 for WordPress
}

if ( ! defined( 'WP_REDIS_TIMEOUT' ) ) {
    define( 'WP_REDIS_TIMEOUT', 1 ); // 1 second connection timeout
}

if ( ! defined( 'WP_REDIS_READ_TIMEOUT' ) ) {
    define( 'WP_REDIS_READ_TIMEOUT', 1 ); // 1 second read timeout
}

// Prefix all Redis keys to avoid collision with other apps sharing same Redis
if ( ! defined( 'WP_REDIS_PREFIX' ) ) {
    define( 'WP_REDIS_PREFIX', 'cnp_' );
}

// Disable Redis banners in WP Admin
if ( ! defined( 'WP_REDIS_DISABLE_BANNERS' ) ) {
    define( 'WP_REDIS_DISABLE_BANNERS', true );
}

/**
 * Configure non-persistent cache groups.
 */
add_action( 'init', function () {
    if ( function_exists( 'wp_cache_add_non_persistent_groups' ) ) {
        wp_cache_add_non_persistent_groups( array(
            'counts',
            'plugins',
            'themes',
        ) );
    }
}, 1 );

/**
 * Add Redis connection info to admin bar for debugging.
 */
add_action( 'admin_bar_menu', function ( $wp_admin_bar ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $is_redis = wp_using_ext_object_cache();
    $status = $is_redis ? '🟢 Redis' : '🔴 No Cache';

    $wp_admin_bar->add_node( array(
        'id'    => 'canopy-redis-status',
        'title' => $status,
        'href'  => admin_url( 'options-general.php?page=redis-cache' ),
    ) );
}, 100 );

/**
 * Redis Dashboard Widget.
 */
add_action( 'wp_dashboard_setup', function() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    wp_add_dashboard_widget(
        'canopy_redis_dashboard',
        '🔴 Redis Cache Monitor',
        'canopy_redis_dashboard_widget'
    );
});

function canopy_redis_dashboard_widget() {
    if ( ! class_exists( 'Redis' ) || ! defined( 'WP_REDIS_HOST' ) ) {
        echo '<p>❌ Redis not configured.</p>';
        return;
    }

    try {
        $redis = new Redis();
        $redis->connect( WP_REDIS_HOST, WP_REDIS_PORT ?? 6379, 1 );

        $info   = $redis->info();
        $stats  = $redis->info( 'stats' );
        $mem    = $redis->info( 'memory' );
        $keys   = $redis->dbSize();
        $hits   = $stats['keyspace_hits'] ?? 0;
        $misses = $stats['keyspace_misses'] ?? 0;
        $ratio  = ( $hits + $misses > 0 ) ? round( $hits / ( $hits + $misses ) * 100, 1 ) : 0;

        echo '<table style="width:100%;border-collapse:collapse;">';
        echo '<tr><td>Status</td><td><strong style="color:green;">🟢 Connected</strong></td></tr>';
        echo '<tr><td>Version</td><td>' . esc_html( $info['redis_version'] ?? 'N/A' ) . '</td></tr>';
        echo '<tr><td>Uptime</td><td>' . round( ( $info['uptime_in_seconds'] ?? 0 ) / 3600, 1 ) . ' hours</td></tr>';
        echo '<tr><td>Memory</td><td>' . round( ( $mem['used_memory'] ?? 0 ) / 1024 / 1024, 2 ) . ' MB / 256 MB</td></tr>';
        echo '<tr><td>Keys</td><td>' . number_format( $keys ) . '</td></tr>';
        echo '<tr><td>Hit Ratio</td><td><strong>' . $ratio . '%</strong></td></tr>';
        echo '<tr><td>Hits / Misses</td><td>' . number_format( $hits ) . ' / ' . number_format( $misses ) . '</td></tr>';
        echo '</table>';

        $redis->close();
    } catch ( \Exception $e ) {
        echo '<p style="color:red;">❌ Redis Error: ' . esc_html( $e->getMessage() ) . '</p>';
    }
}
