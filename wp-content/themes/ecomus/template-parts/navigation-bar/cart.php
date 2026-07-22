<?php
/**
 * Template file for displaying cart mobile
 *
 * @package Ecomus
 */

 if ( ! function_exists( 'WC' ) ) {
	return;
}

$counter = ! empty(WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
$counter_class = $counter == 0 ? 'empty-counter' : '';
$data_target = \Ecomus\Helper::get_option( 'cart_icon_behaviour' ) == 'panel' ? 'cart-panel' : 'cart-page'; 
?>

<a href="<?php echo esc_url( wc_get_page_permalink( 'cart' ) ); ?>" class="ecomus-mobile-navigation-bar__icon em-button em-button-icon em-button-light em-flex em-flex-column em-flex-align-center em-flex-center em-font-semibold em-relative" data-toggle="off-canvas" data-target="<?php echo esc_attr( $data_target ); ?>">
	<?php echo \Ecomus\Helper::get_cart_icons(); ?>
	<span class="header-counter header-cart__counter <?php echo esc_attr( $counter_class );?>"><?php echo esc_html( $counter ) ?></span>
	<span><?php echo esc_html__( 'Cart', 'ecomus' ); ?></span>
</a>
