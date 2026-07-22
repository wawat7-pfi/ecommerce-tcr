<?php
/**
 * Template part for displaying the secondary menu
 *
 * @package Ecomus
 */
?>

<?php
	if ( ! has_nav_menu( 'secondary-menu' ) ) {
		return;
	}

	wp_nav_menu( apply_filters( 'ecomus_navigation_secondary_menu_content', array(
		'theme_location' 	=> 'secondary-menu',
		'container'      	=> 'nav',
		'container_id'   	=> 'secondary-navigation',
		'container_class'   => 'main-navigation secondary-navigation',
		'menu_class'     	=> 'nav-menu menu',
	) ) );
?>