<?php
/**
 * Template part for displaying the cart panel
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}
if ( function_exists('is_cart') && is_cart() ) {
	return;
}
$counter = ! empty(WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
?>

<div id="cart-panel" class="offscreen-panel cart-panel woocommerce">
	<div class="panel__backdrop"></div>
	<div class="panel__container">
		<?php echo \Ecomus\Icon::get_svg( 'close', 'ui', 'class=panel__button-close' ); ?>

		<h6 class="panel__header">
			<?php echo esc_html__( 'Shopping Cart ', 'ecomus' ); ?>
		</h6>

		<div class="panel__content">
			<?php do_action( 'ecomus_before_mini_cart_content'); ?>

			<div class="widget_shopping_cart_content">
				<?php woocommerce_mini_cart(); ?>
			</div>

			<?php do_action( 'ecomus_after_mini_cart_content'); ?>
		</div>
	</div>
</div>