<?php
/**
 * Display product quickadd.
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

if( get_option( 'ecomus_buy_now' ) == 'yes' ) {
	$classes[] = 'has-buy-now';
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

<div class="product-quickadd <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<?php
	/**
	 * Hook: ecomus_woocommerce_before_product_quickadd_summary
	 *
	 * @hooked woocommerce_show_product_images - 10
	 */
	do_action( 'ecomus_woocommerce_before_product_quickadd_summary' );
	?>

	<div class="summary entry-summary">
		<div class="entry-summary__header">
			<?php
			/**
			 * Hook: ecomus_woocommerce_product_quickadd_summary
			 *
			 * @hooked woocommerce_template_single_thumbnail - 5
			 * @hooked woocommerce_template_single_title - 10
			 * @hooked woocommerce_template_single_price - 25
			 */
			do_action( 'ecomus_woocommerce_product_quickadd_summary_header' );
			?>
		</div>
		<?php
		/**
		 * Hook: ecomus_woocommerce_product_quickadd_summary
		 *
		 * @hooked woocommerce_template_single_add_to_cart - 50
		 */
		do_action( 'ecomus_woocommerce_product_quickadd_summary' );
		?>
	</div>

	<?php
	/**
	 * Hook: ecomus_woocommerce_after_product_quickadd_summary
	 */
	do_action( 'ecomus_woocommerce_after_product_quickadd_summary' );
	?>
</div>