<?php

/**
 * Template part for displaying the social in topbar
 *
 * @package Ecomus
 */

?>

<?php
	if ( ! has_nav_menu( 'social-menu' ) ) {
		return;
	}

	wp_nav_menu( apply_filters( 'ecomus_navigation_socials_menu_content', array(
		'theme_location' 	=> 'social-menu',
		'container'      	=> 'nav',
		'container_id'   	=> 'socials-navigation',
		'container_class'   => 'socials-navigation em-color-dark',
		'menu_class'     	=> 'menu',
		'depth'     		=> 1,
	) ) );
?>