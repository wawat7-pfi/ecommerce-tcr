<?php
add_action( 'wp_enqueue_scripts', 'ecomus_child_enqueue_scripts', 20 );
function ecomus_child_enqueue_scripts() {
	wp_enqueue_style( 'ecomus-child-style', get_stylesheet_uri() );
}