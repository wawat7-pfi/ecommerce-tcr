<?php
/**
 * Template file for displaying Home mobile
 *
 * @package Ecomus
 */

?>

<a href="<?php echo esc_url( home_url() ); ?>" class="ecomus-mobile-navigation-bar__icon em-button em-button-icon em-button-light em-flex em-flex-column em-flex-align-center em-flex-center em-font-semibold">
	<?php echo \Ecomus\Icon::get_svg( 'home' ); ?>
	<span><?php echo esc_html__( 'Home', 'ecomus' ); ?></span>
</a>
