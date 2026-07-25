<?php
/** WooCommerce tweaks to match official (snippets from the lost child-theme functions.php). */
if ( ! defined( 'ABSPATH' ) ) { exit; }
add_action( 'wp', function () {
	// Short description in the single-product buy box (below price), like official
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
} );
