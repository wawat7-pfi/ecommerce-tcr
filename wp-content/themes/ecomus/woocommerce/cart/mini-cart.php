<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( WC()->cart && ! WC()->cart->is_empty() ) : ?>

	<ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ); ?>">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				/**
				 * This filter is documented in woocommerce/templates/cart/cart.php.
				 *
				 * @since 2.1.0
				 */
				$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
				$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
				$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				$unit_measure 	   = maybe_unserialize( get_post_meta( $_product->get_id(), 'unit_measure', true ) );
				?>
				<li class="woocommerce-mini-cart-item mini-cart-item-<?php echo esc_attr( $_product->get_id() ); ?> <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
					<div class="woocommerce-mini-cart-item__thumbnail">
						<?php if ( $product_permalink ) : ?>
							<a href="<?php echo esc_url( $product_permalink ); ?>">
						<?php endif; ?>
							<?php echo wp_kses_post( $thumbnail ); ?>
						<?php if ( $product_permalink ) : ?>
							</a>
						<?php endif; ?>
					</div>
					<div class="woocommerce-mini-cart-item__summary">
						<div class="woocommerce-mini-cart-item__box">
							<span class="woocommerce-mini-cart-item__data">
								<span class="woocommerce-mini-cart-item__name">
								<?php if ( $product_permalink ) : ?>
									<a href="<?php echo esc_url( $product_permalink ); ?>">
								<?php endif; ?>
										<?php echo wp_kses_post( $product_name ); ?>
									<?php if ( $product_permalink ) : ?>
										</a>
									<?php endif; ?>
								</span>
								<?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
								<?php if( $unit_measure ) : ?>
									<div class="woocommerce-mini-cart-item__price em-flex em-flex-align-center">
								<?php endif; ?>
								<?php
									if( WC()->cart->display_prices_including_tax() ) {
										$_product_regular_price = wc_get_price_including_tax( $_product, array( 'price' => $_product->get_regular_price() ) );
										$_product_sale_price = wc_get_price_including_tax( $_product, array( 'price' => $_product->get_price() ) );
									} else {
										$_product_regular_price = $_product->get_regular_price();
										$_product_sale_price = $_product->get_price();
									}
								?>
								<?php if( ! empty( $_product_sale_price ) && floatval( $_product_regular_price ) > floatval( $_product_sale_price ) ) : ?>
									<span class="price"><?php echo wc_format_sale_price( $_product_regular_price, $_product_sale_price ); ?></span>
								<?php else : ?>
									<span class="woocommerce-Price-amount amount">
										<bdi><?php echo ! empty( $product_price ) ? $product_price : '' ?></bdi>
									</span>
								<?php endif; ?>
								<?php if( $unit_measure ) : ?>
									<span class="em-price-unit"><span class="divider">/</span> <?php echo esc_html( $unit_measure ); ?></span>
									</div>
								<?php endif; ?>
								<span class="woocommerce-mini-cart-item__qty--text hidden">
									<?php esc_html_e( 'QTY:', 'ecomus' ); ?>
									<?php echo wp_kses_post( $cart_item['quantity'] ); ?>
								</span>
							</span>
							<span class="woocommerce-mini-cart-item__qty" data-nonce="<?php echo wp_create_nonce( 'ecomus-update-cart-qty--' . $cart_item_key ); ?>">
								<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times;', $cart_item['quantity'] ) . '</span>', $cart_item, $cart_item_key ); ?>
								<?php
									echo apply_filters(
										'woocommerce_cart_item_remove_link',
										sprintf(
											'<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s" data-success_message="%s">%s</a>',
											esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
											esc_attr__( 'Remove this item', 'ecomus' ),
											esc_attr( $product_id ),
											esc_attr( $cart_item_key ),
											esc_attr( $_product->get_sku() ),
											esc_attr( sprintf( __( '&ldquo;%s&rdquo; has been removed from your cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
											esc_html__( 'Remove', 'ecomus' ),
										),
										$cart_item_key
									);
								?>
							</span>
						</div>
					</div>
					<input type="hidden" name="details_product" data-id="<?php echo esc_attr( $product_id ); ?>" data-price="<?php echo esc_attr($_product->get_price()); ?>" value="<?php echo esc_attr( $_product->get_id() ). '|' .esc_attr( json_encode( $cart_item['variation'] ) ); ?>" />
				</li>
				<?php
			}
		}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul>

	<?php do_action( 'ecomus_after_woocommerce_mini_cart_items' ); ?>

	<div class="widget_shopping_cart_footer">
		<?php do_action( 'ecomus_before_widget_shopping_cart_total' ); ?>

		<p class="woocommerce-mini-cart__total total">
			<?php
			/**
			 * Hook: woocommerce_widget_shopping_cart_total.
			 *
			 * @hooked woocommerce_widget_shopping_cart_subtotal - 10
			 */
			do_action( 'woocommerce_widget_shopping_cart_total' );
			?>
		</p>

		<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

		<p class="woocommerce-mini-cart__buttons buttons"><?php do_action( 'woocommerce_widget_shopping_cart_buttons' ); ?></p>

		<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>
	</div>

<?php else : ?>

	<div class="woocommerce-mini-cart__empty-message">
		<h4><?php echo esc_html__( 'Your cart is empty', 'ecomus' );?></h4>
		<p><?php echo esc_html__( 'You may check out all the available products and buy some in the shop', 'ecomus' );?></p>
		<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ) ?>" class="em-button em-button-hover-eff"><?php echo esc_html__( 'Return to shop', 'ecomus' );?> <?php echo \Ecomus\Icon::get_svg( 'arrow-top' ); ?></a>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
