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
