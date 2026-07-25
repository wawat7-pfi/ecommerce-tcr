<?php
/**
 * Plugin Name: Canopy WooCommerce Cache Optimization
 * Description: Optimasi WooCommerce caching dengan Redis integration.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Canopy_WC_Cache {

    /**
     * Cache TTL constants (in seconds).
     */
    const PRODUCT_COUNT_TTL    = 3600;    // 1 hour
    const SHIPPING_RATE_TTL    = 1800;    // 30 minutes
    const WIDGET_CACHE_TTL     = 3600;    // 1 hour
    const CATEGORY_COUNT_TTL   = 3600;    // 1 hour
    const LAYERED_NAV_TTL      = 3600;    // 1 hour

    public function __construct() {
        // Only run if Redis Object Cache is active
        if ( ! wp_using_ext_object_cache() ) {
            return;
        }

        // Product count caching
        add_filter( 'woocommerce_product_query_meta_query', array( $this, 'cache_product_meta_queries' ), 10, 2 );

        // Cache WooCommerce widget output
        add_filter( 'woocommerce_layered_nav_count_maybe_cache', '__return_true' );

        // Optimize transient storage
        add_action( 'woocommerce_delete_product_transients', array( $this, 'smart_transient_cleanup' ) );

        // Cache cart fragments more aggressively
        add_filter( 'woocommerce_cart_fragments_group', array( $this, 'cart_fragments_cache_group' ) );

        // Optimize product visibility queries
        add_filter( 'woocommerce_product_query', array( $this, 'cache_product_visibility' ), 5 );

        // Cache price range queries
        add_filter( 'woocommerce_price_filter_results', array( $this, 'cache_price_range' ), 10, 3 );

        // Admin: Show cache stats in WooCommerce status page
        add_action( 'woocommerce_system_status_report', array( $this, 'display_cache_stats' ) );

        // Setup Cache Invalidation Hooks
        $this->setup_invalidation_hooks();
    }

    /**
     * Cache product meta queries to reduce repetitive DB lookups.
     */
    public function cache_product_meta_queries( $meta_query, $query ) {
        $cache_key = 'canopy_pmq_' . md5( serialize( $meta_query ) );
        $cached    = wp_cache_get( $cache_key, 'canopy_wc' );

        if ( false !== $cached ) {
            return $cached;
        }

        wp_cache_set( $cache_key, $meta_query, 'canopy_wc', 3600 );
        return $meta_query;
    }

    /**
     * Selectively clear transients instead of flushing everything.
     */
    public function smart_transient_cleanup( $product_id = 0 ) {
        if ( $product_id ) {
            wp_cache_delete( 'canopy_product_' . $product_id, 'canopy_wc' );
        }

        $this->clear_rest_api_cache();
        wp_cache_delete( 'canopy_cat_counts', 'canopy_wc' );
    }

    /**
     * Clear REST API product transients.
     */
    private function clear_rest_api_cache() {
        global $wpdb;

        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_canopy_products_%'
             OR option_name LIKE '_transient_timeout_canopy_products_%'"
        );

        if ( function_exists( 'wp_cache_flush_group' ) ) {
            wp_cache_flush_group( 'canopy_rest' );
        }
    }

    /**
     * Use dedicated cache group for cart fragments.
     */
    public function cart_fragments_cache_group( $group ) {
        return 'canopy_cart_fragments';
    }

    /**
     * Cache product visibility term IDs.
     */
    public function cache_product_visibility( $query ) {
        $visibility = wp_cache_get( 'canopy_product_visibility_ids', 'canopy_wc' );

        if ( false === $visibility ) {
            $visibility = wc_get_product_visibility_term_ids();
            wp_cache_set( 'canopy_product_visibility_ids', $visibility, 'canopy_wc', self::PRODUCT_COUNT_TTL );
        }
    }

    /**
     * Cache product price range.
     */
    public function cache_price_range( $results, $min, $max ) {
        $cache_key = 'canopy_price_range_' . md5( $min . '_' . $max );
        $cached    = wp_cache_get( $cache_key, 'canopy_wc' );

        if ( false !== $cached ) {
            return $cached;
        }

        wp_cache_set( $cache_key, $results, 'canopy_wc', 3600 );
        return $results;
    }

    /**
     * Setup Cache Invalidation Hooks.
     */
    private function setup_invalidation_hooks() {
        add_action( 'woocommerce_update_product', array( $this, 'invalidate_product_cache' ) );
        add_action( 'woocommerce_new_product', array( $this, 'invalidate_product_cache' ) );
        add_action( 'before_delete_post', function( $post_id ) {
            if ( 'product' === get_post_type( $post_id ) ) {
                $this->invalidate_product_cache( $post_id );
            }
        });

        add_action( 'woocommerce_order_status_completed', array( $this, 'invalidate_all_product_cache' ) );
        add_action( 'woocommerce_order_status_processing', array( $this, 'invalidate_all_product_cache' ) );
        add_action( 'woocommerce_coupon_options_save', array( $this, 'invalidate_all_product_cache' ) );
        add_action( 'woocommerce_settings_saved', array( $this, 'invalidate_all_product_cache' ) );
    }

    public function invalidate_product_cache( $product_id = 0 ) {
        if ( $product_id ) {
            wp_cache_delete( 'canopy_product_' . $product_id, 'canopy_wc' );
        }
        $this->clear_rest_api_cache();
        $this->purge_full_page_cache();
    }

    public function invalidate_all_product_cache() {
        if ( function_exists( 'wp_cache_flush_group' ) ) {
            wp_cache_flush_group( 'canopy_wc' );
            wp_cache_flush_group( 'canopy_rest' );
            wp_cache_flush_group( 'canopy_fragments' );
        }
        if ( function_exists( 'wc_delete_product_transients' ) ) {
            wc_delete_product_transients();
        }
        $this->purge_full_page_cache();
    }

    /**
     * Purge all full-page cache entries from Redis.
     * Called when products, orders, or WooCommerce settings change.
     */
    private function purge_full_page_cache() {
        if ( ! class_exists( 'Redis' ) || ! defined( 'WP_REDIS_HOST' ) ) {
            return;
        }

        try {
            $redis = new \Redis();
            $redis->connect( WP_REDIS_HOST, WP_REDIS_PORT, 1 );
            $keys = $redis->keys( 'canopy_fpc:*' );
            if ( ! empty( $keys ) ) {
                $redis->del( $keys );
            }
            $redis->close();
        } catch ( \RedisException $e ) {
            // Silently fail.
        }
    }

    /**
     * Display cache stats on WooCommerce Status page.
     */
    public function display_cache_stats() {
        if ( ! class_exists( 'Redis' ) || ! defined( 'WP_REDIS_HOST' ) ) {
            return;
        }

        try {
            $redis = new \Redis();
            $redis->connect( WP_REDIS_HOST, WP_REDIS_PORT, 1 );

            $info   = $redis->info( 'stats' );
            $mem    = $redis->info( 'memory' );
            $keys   = $redis->dbSize();
            $hits   = $info['keyspace_hits'] ?? 0;
            $misses = $info['keyspace_misses'] ?? 0;
            $ratio  = ( $hits + $misses > 0 ) ? round( $hits / ( $hits + $misses ) * 100, 1 ) : 0;

            echo '<table class="wc_status_table" cellspacing="0">';
            echo '<thead><tr><th colspan="3" data-export-label="Redis Cache"><h2>Redis Cache</h2></th></tr></thead>';
            echo '<tbody>';
            echo '<tr><td>Status</td><td>🟢 Connected</td></tr>';
            echo '<tr><td>Memory Used</td><td>' . round( $mem['used_memory'] / 1024 / 1024, 2 ) . ' MB</td></tr>';
            echo '<tr><td>Total Keys</td><td>' . number_format( $keys ) . '</td></tr>';
            echo '<tr><td>Cache Hit Ratio</td><td>' . $ratio . '% (' . number_format( $hits ) . ' hits / ' . number_format( $misses ) . ' misses)</td></tr>';
            echo '</tbody></table>';

            $redis->close();
        } catch ( \RedisException $e ) {
            echo '<p>Redis: ❌ ' . esc_html( $e->getMessage() ) . '</p>';
        }
    }
}

new Canopy_WC_Cache();

/**
 * Cache heavy navigation menu theme fragments.
 */
add_action( 'init', function() {
    if ( ! wp_using_ext_object_cache() || is_admin() ) {
        return;
    }

    add_filter( 'wp_nav_menu', function( $nav_menu, $args ) {
        if ( empty( $args->theme_location ) ) {
            return $nav_menu;
        }

        $cache_key = 'canopy_menu_' . $args->theme_location . '_' . md5( serialize( $args ) );
        wp_cache_set( $cache_key, $nav_menu, 'canopy_fragments', 3600 );

        return $nav_menu;
    }, 10, 2 );
}, 1 );
