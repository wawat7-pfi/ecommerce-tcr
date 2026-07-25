<?php
/**
 * Plugin Name: Canopy AJAX Products
 * Description: REST API endpoints & frontend scripts for async product loading via Elasticsearch.
 * Version: 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Canopy_Ajax_Products {

    const NAMESPACE = 'canopy/v1';

    public function __construct() {
        // REST API endpoints (kept for future search/filter API use)
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );

        // ES fallback logging
        add_action( 'ep_failed_request', array( $this, 'log_es_fallback' ), 10, 3 );

        // Cache invalidation on product changes
        add_action( 'save_post_product', array( $this, 'clear_product_cache' ), 20 );
        add_action( 'woocommerce_update_product', array( $this, 'clear_product_cache' ) );

        // NOTE: AJAX product loading disabled.
        // With Full-Page Cache (advanced-cache.php), the cached HTML already
        // contains server-rendered products. AJAX would replace those with a
        // 6-second REST API call, making the page feel slower.
        //
        // Disabled hooks:
        // - intercept_initial_shop_query (pre_get_posts)
        // - intercept_shortcode_shop_query
        // - format_initial_skeleton_html
        // - output_initial_skeleton_loader
        // - enqueue_scripts (ajax-products.js)
    }

    /**
     * Intercept main shop query on initial page load to bypass heavy SQL product queries.
     * Allows instant page shell load while products load asynchronously via AJAX REST API.
     */
    public function intercept_initial_shop_query( $query ) {
        if ( is_admin() ) {
            return;
        }

        // Do not intercept REST API requests
        if ( ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || false !== strpos( $_SERVER['REQUEST_URI'] ?? '', '/wp-json/' ) ) {
            return;
        }

        // Allow bots to get SSR products
        $user_agent = strtolower( $_SERVER['HTTP_USER_AGENT'] ?? '' );
        $bots = array( 'googlebot', 'bingbot', 'yandexbot', 'baidubot', 'facebookexternalhit' );
        foreach ( $bots as $bot ) {
            if ( false !== strpos( $user_agent, $bot ) ) {
                return;
            }
        }

        // Intercept product queries on shop / category / tag page loads
        $req_uri = strtolower( $_SERVER['REQUEST_URI'] ?? '' );
        $is_shop_page = ( false !== strpos( $req_uri, '/shop' ) || false !== strpos( $req_uri, '/product-category/' ) || false !== strpos( $req_uri, '/product-tag/' ) );

        if ( $is_shop_page ) {
            $post_type = $query->get( 'post_type' );
            $is_product = ( 'product' === $post_type || ( is_array( $post_type ) && in_array( 'product', $post_type, true ) ) );

            if ( $is_product || $query->is_main_query() ) {
                $query->set( 'post__in', array( 0 ) );
            }
        }
    }

    /**
     * Intercept Elementor Ecomus Catalog builder product queries on initial load.
     */
    public function intercept_shortcode_shop_query( $query_args, $attributes, $type ) {
        if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || false !== strpos( $_SERVER['REQUEST_URI'] ?? '', '/wp-json/' ) ) {
            return $query_args;
        }

        $req_uri = strtolower( $_SERVER['REQUEST_URI'] ?? '' );
        $is_shop_page = ( false !== strpos( $req_uri, '/shop' ) || false !== strpos( $req_uri, '/product-category/' ) || false !== strpos( $req_uri, '/product-tag/' ) );

        if ( $is_shop_page ) {
            $query_args['post__in'] = array( 0 );
        }

        return $query_args;
    }

    /**
     * Format shortcode HTML on initial load to inject Skeleton Loader cards instead of product items or no products notice.
     */
    public function format_initial_skeleton_html( $html, $attributes, $type ) {
        if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || false !== strpos( $_SERVER['REQUEST_URI'] ?? '', '/wp-json/' ) ) {
            return $html;
        }

        $req_uri = strtolower( $_SERVER['REQUEST_URI'] ?? '' );
        $is_shop_page = ( false !== strpos( $req_uri, '/shop' ) || false !== strpos( $req_uri, '/product-category/' ) || false !== strpos( $req_uri, '/product-tag/' ) );

        if ( $is_shop_page ) {
            // Remove "No products found" alerts
            $html = preg_replace( '/<div[^>]*class="[^"]*woocommerce-info[^"]*"[^>]*>.*?<\/div>/is', '', $html );
            $html = preg_replace( '/<div[^>]*class="[^"]*ecomus-products-nothing-found[^"]*"[^>]*>.*?<\/div>/is', '', $html );

            // Inject 12 Skeleton Loader cards inside <ul class="products ...">
            $skeleton_items = '';
            for ( $i = 0; $i < 12; $i++ ) {
                $skeleton_items .= '<li class="product canopy-skeleton-card">
                    <div class="skeleton-image"></div>
                    <div class="skeleton-text"></div>
                    <div class="skeleton-text" style="width: 60%"></div>
                    <div class="skeleton-price"></div>
                </li>';
            }

            if ( false !== strpos( $html, '<ul class="products' ) ) {
                $html = preg_replace( '/(<ul[^>]*class="[^"]*products[^"]*"[^>]*>)(.*?)(<\/ul>)/is', '$1' . $skeleton_items . '$3', $html );
                $html = str_replace( 'class="products', 'class="products canopy-initial-skeleton', $html );
            } else {
                $html = '<ul class="products columns-4 canopy-initial-skeleton">' . $skeleton_items . '</ul>';
            }
        }

        return $html;
    }

    /**
     * Output skeleton loader markup when main query is intercepted on initial load.
     */
    public function output_initial_skeleton_loader() {
        if ( is_shop() || is_product_taxonomy() ) {
            // Remove WooCommerce and Ecomus "No products found" notices on initial load
            remove_action( 'woocommerce_no_products_found', 'wc_no_products_found', 10 );
            if ( class_exists( '\Ecomus\WooCommerce\Catalog' ) ) {
                remove_action( 'woocommerce_no_products_found', array( \Ecomus\WooCommerce\Catalog::instance(), 'product_filter_no_products_found' ), 20 );
            }
            
            echo '<div class="shop-content-inner"><ul class="products columns-4 canopy-initial-skeleton">';
            for ( $i = 0; $i < 12; $i++ ) {
                echo '<li class="product canopy-skeleton-card">
                    <div class="skeleton-image"></div>
                    <div class="skeleton-text"></div>
                    <div class="skeleton-text" style="width: 60%"></div>
                    <div class="skeleton-price"></div>
                </li>';
            }
            echo '</ul></div>';
        }
    }

    /**
     * Buffer product loop output on initial shop page loads to discard server-rendered product cards.
     */
    public function start_initial_loop_buffer( $echo = true ) {
        if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || false !== strpos( $_SERVER['REQUEST_URI'] ?? '', '/wp-json/' ) ) {
            return;
        }

        $req_uri = strtolower( $_SERVER['REQUEST_URI'] ?? '' );
        $is_shop_page = ( false !== strpos( $req_uri, '/shop' ) || false !== strpos( $req_uri, '/product-category/' ) || false !== strpos( $req_uri, '/product-tag/' ) );

        if ( $is_shop_page && ! doing_action( 'rest_api_init' ) ) {
            ob_start();
        }
    }

    /**
     * Clean product loop output buffer on initial shop page loads.
     */
    public function end_initial_loop_buffer( $echo = true ) {
        if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || false !== strpos( $_SERVER['REQUEST_URI'] ?? '', '/wp-json/' ) ) {
            return;
        }

        $req_uri = strtolower( $_SERVER['REQUEST_URI'] ?? '' );
        $is_shop_page = ( false !== strpos( $req_uri, '/shop' ) || false !== strpos( $req_uri, '/product-category/' ) || false !== strpos( $req_uri, '/product-tag/' ) );

        if ( $is_shop_page && ! doing_action( 'rest_api_init' ) ) {
            if ( ob_get_level() > 0 ) {
                ob_end_clean();
            }

            // Output Skeleton Loader cards placeholder
            echo '<ul class="products columns-4 canopy-initial-skeleton">';
            for ( $i = 0; $i < 12; $i++ ) {
                echo '<li class="product canopy-skeleton-card">
                    <div class="skeleton-image"></div>
                    <div class="skeleton-text"></div>
                    <div class="skeleton-text" style="width: 60%"></div>
                    <div class="skeleton-price"></div>
                </li>';
            }
            echo '</ul>';
        }
    }

    /**
     * Clear transient cache for products.
     */
    public function clear_product_cache() {
        global $wpdb;
        $wpdb->query(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_canopy_products_%' OR option_name LIKE '_transient_timeout_canopy_products_%'"
        );
    }

    /**
     * Register REST API routes.
     */
    public function register_routes() {
        register_rest_route( self::NAMESPACE, '/products', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_products' ),
            'permission_callback' => '__return_true',
            'args'                => $this->get_products_args(),
        ) );
    }

    /**
     * Define accepted query parameters.
     */
    private function get_products_args() {
        return array(
            'page' => array(
                'default'           => 1,
                'sanitize_callback' => 'absint',
            ),
            'per_page' => array(
                'default'           => intval( get_option( 'posts_per_page', 12 ) ),
                'sanitize_callback' => 'absint',
            ),
            'orderby' => array(
                'default'           => 'menu_order',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'order' => array(
                'default'           => 'ASC',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'category' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'search' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'min_price' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'max_price' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'attribute_pa_size' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'attribute_pa_color' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'stock_status' => array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        );
    }

    public function get_products( $request ) {
        $cache_key = 'canopy_products_' . md5( wp_json_encode( $request->get_params() ) );
        if ( wp_using_ext_object_cache() ) {
            $cached = wp_cache_get( $cache_key, 'canopy_rest' );
        } else {
            $cached = get_transient( $cache_key );
        }

        if ( false !== $cached ) {
            return rest_ensure_response( $cached );
        }

        $args = array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => min( max( 1, (int) $request->get_param( 'per_page' ) ), 48 ),
            'paged'          => max( 1, (int) $request->get_param( 'page' ) ),
            'orderby'        => $request->get_param( 'orderby' ),
            'order'          => strtoupper( $request->get_param( 'order' ) ) === 'DESC' ? 'DESC' : 'ASC',
            'ep_integrate'   => true, // Route query to Elasticsearch
        );

        // Search query
        $search = $request->get_param( 'search' );
        if ( ! empty( $search ) ) {
            $args['s'] = $search;
        }

        // Category filter
        $category = $request->get_param( 'category' );
        if ( ! empty( $category ) ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => array_map( 'trim', explode( ',', $category ) ),
            );
        }

        // Price filter
        $min_price = $request->get_param( 'min_price' );
        $max_price = $request->get_param( 'max_price' );
        if ( ! empty( $min_price ) || ! empty( $max_price ) ) {
            $meta_query = array( 'relation' => 'AND' );
            if ( ! empty( $min_price ) ) {
                $meta_query[] = array(
                    'key'     => '_price',
                    'value'   => (float) $min_price,
                    'compare' => '>=',
                    'type'    => 'NUMERIC',
                );
            }
            if ( ! empty( $max_price ) ) {
                $meta_query[] = array(
                    'key'     => '_price',
                    'value'   => (float) $max_price,
                    'compare' => '<=',
                    'type'    => 'NUMERIC',
                );
            }
            $args['meta_query'] = $meta_query;
        }

        // Attribute filters (Size & Color)
        foreach ( array( 'pa_size', 'pa_color' ) as $taxonomy ) {
            $param = 'attribute_' . $taxonomy;
            $value = $request->get_param( $param );
            if ( ! empty( $value ) ) {
                if ( ! isset( $args['tax_query'] ) ) {
                    $args['tax_query'] = array();
                }
                $args['tax_query'][] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => array_map( 'trim', explode( ',', $value ) ),
                );
            }
        }

        // Map WooCommerce orderby
        $args = $this->map_wc_orderby( $args );

        // Query products via ElasticPress / Elasticsearch
        $query = new \WP_Query( $args );

        // Render product grid HTML
        ob_start();
        if ( $query->have_posts() ) {
            $cols = esc_attr( get_option( 'woocommerce_catalog_columns', 4 ) );
            echo '<ul class="products products-elementor columns-' . $cols . '">';
            while ( $query->have_posts() ) {
                $query->the_post();
                wc_get_template_part( 'content', 'product' );
            }
            echo '</ul>';
        } else {
            echo '<p class="woocommerce-info">' . esc_html__( 'No products were found matching your selection.', 'woocommerce' ) . '</p>';
        }
        $html = ob_get_clean();
        wp_reset_postdata();

        // Render pagination HTML
        ob_start();
        $this->render_pagination( $query );
        $pagination_html = ob_get_clean();

        $response_data = array(
            'html'        => $html,
            'pagination'  => $pagination_html,
            'total'       => (int) $query->found_posts,
            'total_pages' => (int) $query->max_num_pages,
            'page'        => (int) $request->get_param( 'page' ),
        );

        if ( wp_using_ext_object_cache() ) {
            wp_cache_set( $cache_key, $response_data, 'canopy_rest', 5 * MINUTE_IN_SECONDS );
        } else {
            set_transient( $cache_key, $response_data, 5 * MINUTE_IN_SECONDS );
        }

        return rest_ensure_response( $response_data );
    }

    /**
     * Map WooCommerce orderby parameters.
     */
    private function map_wc_orderby( $args ) {
        switch ( $args['orderby'] ) {
            case 'popularity':
                $args['meta_key'] = 'total_sales';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            case 'rating':
                $args['meta_key'] = '_wc_average_rating';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            case 'date':
                $args['orderby'] = 'date';
                $args['order']   = 'DESC';
                break;
            case 'price':
                $args['meta_key'] = '_price';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'ASC';
                break;
            case 'price-desc':
                $args['meta_key'] = '_price';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            case 'recommended':
                $args['orderby'] = 'recommended';
                break;
        }
        return $args;
    }

    /**
     * Render pagination links HTML.
     */
    private function render_pagination( $query ) {
        if ( $query->max_num_pages <= 1 ) {
            return;
        }

        echo '<nav class="woocommerce-pagination canopy-ajax-pagination">';
        echo paginate_links( array(
            'total'     => $query->max_num_pages,
            'current'   => max( 1, (int) $query->get( 'paged' ) ),
            'format'    => '?paged=%#%',
            'type'      => 'list',
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
        ) );
        echo '</nav>';
    }

    /**
     * Log Elasticsearch query fallback.
     */
    public function log_es_fallback( $request, $context, $args ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( '[Canopy ES Fallback] Elasticsearch request failed. Falling back to MariaDB. Context: ' . $context );
        }
    }

    /**
     * Enqueue scripts and styles on shop/category pages.
     */
    public function enqueue_scripts() {
        if ( ! is_shop() && ! is_product_taxonomy() && ! is_search() ) {
            return;
        }

        $script_rel_path = '/assets/js/ajax-products.js';
        $script_abs_path = get_stylesheet_directory() . $script_rel_path;
        $script_uri      = get_stylesheet_directory_uri() . $script_rel_path;

        if ( ! file_exists( $script_abs_path ) ) {
            return;
        }

        wp_enqueue_script(
            'canopy-ajax-products',
            $script_uri,
            array( 'jquery' ),
            filemtime( $script_abs_path ),
            true
        );

        wp_localize_script( 'canopy-ajax-products', 'canopyProducts', array(
            'restUrl'    => esc_url_raw( rest_url( self::NAMESPACE . '/products' ) ),
            'nonce'      => wp_create_nonce( 'wp_rest' ),
            'perPage'    => intval( get_option( 'posts_per_page', 12 ) ),
            'isShop'     => is_shop(),
            'isCategory' => is_product_taxonomy(),
            'category'   => is_product_taxonomy() ? get_queried_object()->slug : '',
            'i18n'       => array(
                'loading'    => __( 'Loading products...', 'canopy' ),
                'noProducts' => __( 'No products found.', 'canopy' ),
                'error'      => __( 'Failed to load products. Please try again.', 'canopy' ),
            ),
        ) );

        wp_add_inline_style( 'canopy-child', $this->get_skeleton_css() );
    }

    /**
     * Skeleton loader CSS.
     */
    private function get_skeleton_css() {
        return '
            .canopy-skeleton-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 20px;
                padding: 20px 0;
                width: 100%;
            }
            .canopy-skeleton-card {
                background: #fff;
                border: 1px solid #eee;
                border-radius: 8px;
                overflow: hidden;
            }
            .canopy-skeleton-card .skeleton-image {
                width: 100%;
                padding-top: 133%;
                background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                background-size: 200% 100%;
                animation: skeleton-pulse 1.5s ease-in-out infinite;
            }
            .canopy-skeleton-card .skeleton-text {
                height: 16px;
                margin: 12px 16px 8px;
                background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                background-size: 200% 100%;
                animation: skeleton-pulse 1.5s ease-in-out infinite;
                border-radius: 4px;
            }
            .canopy-skeleton-card .skeleton-price {
                height: 14px;
                width: 40%;
                margin: 0 16px 16px;
                background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                background-size: 200% 100%;
                animation: skeleton-pulse 1.5s ease-in-out infinite;
                border-radius: 4px;
            }
            @keyframes skeleton-pulse {
                0% { background-position: 200% 0; }
                100% { background-position: -200% 0; }
            }
            .canopy-products-container.is-loading {
                opacity: 0.55;
                pointer-events: none;
                transition: opacity 0.2s ease;
            }
            .canopy-products-container .canopy-fade-in {
                animation: canopyFadeIn 0.35s ease-in;
            }
            @keyframes canopyFadeIn {
                from { opacity: 0; transform: translateY(8px); }
                to { opacity: 1; transform: translateY(100%); }
            }
        ';
    }
}

new Canopy_Ajax_Products();
