<?php
/**
 * Template file for displaying search mobile
 *
 * @package Ecomus
 */
?>

<a href="#" class="ecomus-mobile-navigation-bar__icon em-button em-button-icon em-button-light em-flex em-flex-column em-flex-align-center em-flex-center em-font-semibold" data-toggle="modal" data-target="search-modal">
	<?php echo \Ecomus\Icon::get_svg( 'search' ); ?>
	<span><?php echo esc_html__( 'Search', 'ecomus' ); ?></span>
</a>
