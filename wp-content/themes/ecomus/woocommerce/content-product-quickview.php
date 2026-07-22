<?php
/**
 * Display product quickview.
 *
 * @author        Drfuri
 * @package       Ecomus
 * @version       1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$classes = wc_get_product_class( '', $product  );

$classes[] = 'product-quickview em-flex';

if( get_option( 'ecomus_buy_now' ) == 'yes' ) {
	$classes[] = 'has-buy-now';
}

if( $product->is_on_backorder() && ! \Ecomus\Helper::is_pre_order_module_active() ) {
	$classes[] = 'is-pre-order';
}

if( class_exists( '\WCBoost\Wishlist\Frontend') ) {
	$classes[] = 'has-wishlist';
}

if( class_exists( '\WCBoost\ProductsCompare\Frontend') ) {
	$classes[] = 'has-compare';
}

if( \Ecomus\Helper::get_option( 'disable_out_of_stock_swatch_click' ) ) {
	$classes[] = 'has-disable-outofstock-swatch-click';
}
?>

<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<?php
	/**
	 * Hook: ecomus_woocommerce_before_product_quickview_summary
	 *
	 * @hooked woocommerce_show_product_images - 10
	 */
	do_action( 'ecomus_woocommerce_before_product_quickview_summary' );
	?>

	<div class="summary entry-summary">
		<?php
		/**
		 * Hook: ecomus_woocommerce_product_quickview_summary
		 *
		 * @hooked woocommerce_template_single_rating - 5
		 * @hooked woocommerce_template_taxonomy - 5
		 * @hooked woocommerce_template_single_title - 15
		 * @hooked woocommerce_template_single_price - 25
		 * @hooked woocommerce_template_single_excerpt - 30
		 * @hooked woocommerce_template_single_add_to_cart - 50
		 * @hooked woocommerce_template_view_full_details_button - 60
		 */
		do_action( 'ecomus_woocommerce_product_quickview_summary' );
		?>
	</div>

	<?php
	/**
	 * Hook: ecomus_woocommerce_after_product_quickview_summary
	 */
	do_action( 'ecomus_woocommerce_after_product_quickview_summary' );
	?>
</div>