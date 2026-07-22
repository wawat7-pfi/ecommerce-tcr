<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
add_action( 'wp_enqueue_scripts', function () {
	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	wp_enqueue_style( 'canopy-child', get_stylesheet_uri(), array(), file_exists( "$dir/style.css" ) ? filemtime( "$dir/style.css" ) : '1.0.0' );
	if ( file_exists( "$dir/custom.css" ) ) {
		wp_enqueue_style( 'canopy-child-custom', "$uri/custom.css", array( 'canopy-child' ), filemtime( "$dir/custom.css" ) );
	}
}, 99 );

/**
 * Ensure WooCommerce registration settings are properly configured across environments.
 */
add_action( 'init', function () {
	if ( 'yes' !== get_option( 'woocommerce_enable_myaccount_registration' ) ) {
		update_option( 'woocommerce_enable_myaccount_registration', 'yes' );
	}
	if ( 'no' !== get_option( 'woocommerce_registration_generate_password' ) ) {
		update_option( 'woocommerce_registration_generate_password', 'no' );
	}
	if ( 'yes' !== get_option( 'woocommerce_registration_generate_username' ) ) {
		update_option( 'woocommerce_registration_generate_username', 'yes' );
	}
} );

