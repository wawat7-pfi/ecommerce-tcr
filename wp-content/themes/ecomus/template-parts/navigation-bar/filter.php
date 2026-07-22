<?php
/**
 * Template file for displaying filter mobile
 *
 * @package Ecomus
 */

 if ( ! function_exists( 'WC' ) ) {
	return;
}

?>

<a href="<?php echo esc_url( wc_get_page_permalink( 'cart' ) ); ?>" class="ecomus-mobile-navigation-bar__icon em-button em-button-icon em-button-light em-flex em-flex-column em-flex-align-center em-flex-center em-font-semibold" data-toggle="off-canvas" data-target="filter-sidebar-panel">
	<?php echo \Ecomus\Icon::get_svg( 'filter-2' ); ?>
	<span><?php echo esc_html__( 'Filter', 'ecomus' ); ?></span>
</a>
