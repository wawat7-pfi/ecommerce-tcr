<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.8.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>
<div class="woocommrece-cart-content">
<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
		<thead class="hidden-xs">
			<tr>
				<th class="product-thumbnail em-font-semibold"><?php esc_html_e( 'Product', 'ecomus' ); ?></th>
				<th class="product-name em-font-semibold"></th>
				<th class="product-price em-font-semibold"><?php esc_html_e( 'Price', 'ecomus' ); ?></th>
				<th class="product-quantity em-font-semibold"><?php esc_html_e( 'Quantity', 'ecomus' ); ?></th>
				<th class="product-subtotal em-font-semibold text-right"><?php esc_html_e( 'Total', 'ecomus' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				/**
				 * Filter whether this cart item is visible in the cart.
				 *
				 * @since 2.1.0
				 * @param bool   $visible       Whether the cart item is visible. Default true.
				 * @param array  $cart_item     The cart item data.
				 * @param string $cart_item_key The cart item key.
				 */
				$visible = apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key );

				if ( $_product instanceof WC_Product && $_product->exists() && $cart_item['quantity'] > 0 && $visible ) {
					/**
					 * Filter the product name.
					 *
					 * @since 2.1.0
					 * @param string $product_name Name of the product in the cart.
					 * @param array $cart_item The product in the cart.
					 * @param string $cart_item_key Key for the product in the cart.
					 */
					$product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );

					if( WC()->cart->display_prices_including_tax() ) {
						$_product_regular_price = floatval( wc_get_price_including_tax( $_product, array( 'price' => $_product->get_regular_price() ) ) );
						$_product_sale_price = floatval( wc_get_price_including_tax( $_product, array( 'price' => $_product->get_price() ) ) );
					} else {
						$_product_regular_price = floatval( $_product->get_regular_price() );
						$_product_sale_price = floatval( $_product->get_price() );
					}

					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
						<td class="product-thumbnail">
						<?php
						$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

						if ( ! $product_permalink ) {
							echo !empty($thumbnail) ? $thumbnail : ''; // PHPCS: XSS ok.
						} else {
							printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
						}
						?>
						</td>

						<td scope="row" role="rowheader" class="product-name" data-title="<?php esc_attr_e( 'Product', 'ecomus' ); ?>">
						<?php
						if ( ! $product_permalink ) {
							echo wp_kses_post( $product_name . '&nbsp;' );
						} else {
							/**
							 * This filter is documented above.
							 *
							 * @since 2.1.0
							 */
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
						}

						do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

						// Meta data.
						echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

						echo '<div class="product-remove">';
						// Product Remove
						echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							'woocommerce_cart_item_remove_link',
							sprintf(
								'<a role="button" href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">%s</a>',
								esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
								/* translators: %s is the product name */
								esc_attr( sprintf( __( 'Remove %s from cart', 'ecomus' ), wp_strip_all_tags( $product_name ) ) ),
								esc_attr( $product_id ),
								esc_attr( $_product->get_sku() ),
								esc_html__( 'Remove', 'ecomus' )
							),
							$cart_item_key
						);
						echo '</div>';

						// Backorder notification.
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'ecomus' ) . '</p>', $product_id ) );
						}
						?>
						</td>

						<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'ecomus' ); ?>">
							<?php
							$unit_measure = maybe_unserialize( get_post_meta( $_product->get_id(), 'unit_measure', true ) );

							if ( $unit_measure ) {
								echo '<div class="woocommerce-cart-form__price em-flex em-flex-align-center">';
							}

							if( $_product_sale_price > 0 && $_product_regular_price > $_product_sale_price ) {
								$product_price = '<span>'. wc_format_sale_price( $_product_regular_price, $_product_sale_price ) .'</span>';
							} else {
								$product_price = WC()->cart->get_product_price( $_product );
							}

							echo apply_filters( 'woocommerce_cart_item_price', $product_price, $cart_item, $cart_item_key ); // PHPCS: XSS ok.

							if ( $unit_measure ) {
								echo '<span class="em-price-unit"><span class="divider">/</span> '. esc_html( $unit_measure ) .'</span>';
								echo '</div>';
							}
							?>
						</td>

						<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'ecomus' ); ?>">
						<?php
						if ( $_product->is_sold_individually() ) {
							$min_quantity = 1;
							$max_quantity = 1;
						} else {
							$min_quantity = 0;
							$max_quantity = $_product->get_max_purchase_quantity();
						}

						$product_quantity = woocommerce_quantity_input(
							array(
								'input_name'   => "cart[{$cart_item_key}][qty]",
								'input_value'  => $cart_item['quantity'],
								'max_value'    => $max_quantity,
								'min_value'    => $min_quantity,
								'product_name' => $product_name,
								'step'		   => apply_filters( 'ecomus_woocommerce_quantity_input_step', 1, $_product, $cart_item, $cart_item_key ),
							),
							$_product,
							false
						);

						echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
						?>
						</td>

						<td class="product-subtotal em-font-semibold text-right" data-title="<?php esc_attr_e( 'Total', 'ecomus' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
							?>
						</td>
					</tr>
					<?php
				}
			}
			?>

			<?php do_action( 'woocommerce_cart_contents' ); ?>

			<tr>
				<td colspan="6" class="actions">

					<?php if ( wc_coupons_enabled() ) { ?>
						<div class="coupon">
							<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'ecomus' ); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'ecomus' ); ?>" /> <button type="submit" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'ecomus' ); ?>"><?php esc_html_e( 'Apply coupon', 'ecomus' ); ?></button>
							<?php do_action( 'woocommerce_cart_coupon' ); ?>
						</div>
					<?php } ?>
					<?php
					$update_button_class = wc_wp_theme_get_element_class_name( 'button' ) ? wc_wp_theme_get_element_class_name( 'button' ) : '';
					$update_button_class .= intval( \Ecomus\Helper::get_option( 'update_cart_page_auto' ) ) ? ' hidden' : '';
					?>
					<button type="submit" class="button em-button-subtle em-button-update-cart <?php echo esc_attr( $update_button_class); ?>" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'ecomus' ); ?>"><?php esc_html_e( 'Update cart', 'ecomus' ); ?></button>

					<?php do_action( 'woocommerce_cart_actions' ); ?>

					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</td>
			</tr>

			<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		</tbody>
	</table>
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>

<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

<div class="cart-collaterals">
	<?php
		/**
		 * Cart collaterals hook.
		 *
		 * @hooked woocommerce_cross_sell_display
		 * @hooked woocommerce_cart_totals - 10
		 */
		do_action( 'woocommerce_cart_collaterals' );
	?>
</div>
</div>
<?php do_action( 'woocommerce_after_cart' ); ?>
