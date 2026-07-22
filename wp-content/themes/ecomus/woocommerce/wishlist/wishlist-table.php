<?php
/**
 * Template for displaying wishlist table.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wishlist/wishlist-table.php.
 *
 * @author  WCBoost
 * @package WCBoost\Wishlist\Templates
 * @version 1.1.5
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $wishlist ) ) {
	return;
}
?>

<?php do_action( 'wcboost_wishlist_before_wishlist_table', $wishlist ); ?>

<form class="ecomus-form" action="<?php echo esc_url( wc_get_page_permalink( 'wishlist' ) ); ?>" method="post">
	<ul class="products columns-4 em-flex em-flex-wrap list-unstyled mobile-col-2 product-card-mobile-show-atc shop_table shop_table_responsive wishlist_table wishlist" cellspacing="0">

			<?php
			foreach ( $wishlist->get_items() as $item_key => $item ) :
				/** @var WC_Product */
				$_product = $item->get_product();

				if ( ! $_product || ! $_product->exists() ) {
					continue;
				}

				$product_permalink = $_product->is_visible() ? $_product->get_permalink() : '';

				if ( ! $_product->is_in_stock() ) {
					$class = 'outofstock';
				} else {
					$class = '';
				}

				?>
				<li class="product product-<?php echo esc_attr( $_product->get_id() ); ?> <?php echo esc_attr( $class ) ?> <?php echo esc_attr( apply_filters( 'wcboost_wishlist_item_class', 'ecomus-item', $item, $item_key ) ); ?>">
					<div class="product-inner">
						<div class="product-thumbnail em-relative product-thumbnails--fadein">
						<?php
							if ( ! $product_permalink ) {
								echo '<span class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
							} else {
								echo sprintf( '<a class="woocommerce-LoopProduct-link woocommerce-loop-product__link" href="%s">', esc_url( $product_permalink ) );
							}

							echo $_product->get_image();

							$image_ids = $_product->get_gallery_image_ids();
							if ( ! empty( $image_ids ) ) {
								$image_size = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );
								echo wp_get_attachment_image( $image_ids[0], $image_size, false, array( 'class' => 'attachment-woocommerce_thumbnail size-woocommerce_thumbnail product-thumbnails--fadein-image' ) );
							}

							if ( ! $product_permalink ) {
								echo '</span>';
							} else {
								echo '</a>';
							}
						?>

							<?php \Ecomus\WooCommerce\Badges::badges( $_product, 'sold-out--center woocommerce-badges--right' ); ?>
							<?php if ( $wishlist->can_edit() ) : ?>
								<div class="product-remove product-featured-icons product-featured-icons--second em-absolute em-flex em-flex-column em-flex-align-center em-flex-center product-featured-icons--right em-icon-tranform-vertical">
									<?php
										echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											'wcboost_wishlist_item_remove_link',
											sprintf(
												'<a href="%s" class="remove button product-loop-button em-flex-align-center em-flex-center em-button-icon em-button-light" aria-label="%s" data-product_id="%s" data-product_sku="%s" data-tooltip="%s">%s</a>',
												esc_url( $item->get_remove_url() ),
												esc_html__( 'Remove this item', 'ecomus' ),
												esc_attr( $_product->get_id() ),
												esc_attr( $_product->get_sku() ),
												esc_html__( 'Remove', 'ecomus' ),
												\Ecomus\Icon::inline_svg( 'icon=trash' )
											),
											$item_key
										);
									?>
								</div>
							<?php endif; ?>
							<?php echo \Ecomus\WooCommerce\Loop\Product_Attribute::instance()->product_attribute_second( $_product ); ?>
							<div class="product-featured-icons product-featured-icons--primary em-absolute em-flex em-flex-align-center em-flex-center">
								<?php if ( $args['columns']['purchase'] ) : ?>
								<?php
									if ( $_product->is_purchasable() ) {
										$GLOBALS['product'] = $_product;

										woocommerce_template_loop_add_to_cart( [ 'quantity' => max( 1, $item->get_quantity() ) ] );

										wc_setup_product_data( $GLOBALS['post'] );
									}
								?>
								<?php endif; ?>
								<?php
									if( class_exists( 'WCBoost\ProductsCompare\Frontend' ) && \Ecomus\Helper::get_option( 'product_card_compare' ) ) {
										$GLOBALS['product'] = $_product;
										\WCBoost\ProductsCompare\Frontend::instance()->loop_add_to_compare_button();
									}
								?>
								<?php
									if( \Ecomus\Helper::get_option( 'product_card_quick_view') ) {
										echo \Ecomus\WooCommerce\Loop\Quick_View::quick_view_button_html( 'product-loop-button em-flex-align-center em-flex-center em-button-icon em-button-light', true, $_product );
									}
								?>
							</div>
						</div>

						<div class="product-summary">
							<?php
								$rating  = $_product->get_average_rating();
								$count   = $_product->get_rating_count();

								if ( function_exists('wc_review_ratings_enabled') && wc_review_ratings_enabled() ) {
									echo '<div class="ecomus-rating em-flex em-flex-align-center">';
										if ( $count && function_exists('wc_get_rating_html') ) {
											echo wc_get_rating_html( $rating );
										} else {
									?>
										<div class="star-rating" role="img">
											<span class="max-rating rating-stars">
												<?php echo \Ecomus\Icon::inline_svg( 'icon=star' ); ?>
												<?php echo \Ecomus\Icon::inline_svg( 'icon=star' ); ?>
												<?php echo \Ecomus\Icon::inline_svg( 'icon=star' ); ?>
												<?php echo \Ecomus\Icon::inline_svg( 'icon=star' ); ?>
												<?php echo \Ecomus\Icon::inline_svg( 'icon=star' ); ?>
											</span>
										</div>
									<?php
										}

									?>
										<div class="review-count"><?php echo '(' . esc_html( $_product->get_review_count() ) . ')'; ?></div>
									<?php
									echo '</div>';
								}
							?>

							<h2 class="woocommerce-loop-product__title product-name" data-title="<?php esc_attr_e( 'Product', 'ecomus' ); ?>">
								<?php
								if ( ! $product_permalink ) {
									echo wp_kses_post( $_product->get_name() );
								} else {
									echo wp_kses_post( sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ) );
								}

								do_action( 'wcboost_wishlist_after_item_name', $item, $item_key, $wishlist );

								if ( $args['show_variation_data'] && $_product->is_type( 'variation' ) ) {
									echo wp_kses_post( wc_get_formatted_variation( $_product ) );
								}
								?>
							</h2>

							<?php if ( $args['columns']['price'] ) : ?>
								<span class="price product-price" data-title="<?php esc_attr_e( 'Price', 'ecomus' ); ?>">
									<?php echo wp_kses_post( $_product->get_price_html() ); ?>
								</span>
							<?php endif; ?>

							<?php echo \Ecomus\WooCommerce\Loop\Product_Attribute::instance()->product_attribute( $_product ); ?>

							<?php if ( $args['columns']['quantity'] && $wishlist->can_edit() ) : ?>
								<div class="product-quantity quantity" data-title="<?php esc_attr_e( 'Quantity', 'ecomus' ); ?>">
									<?php
									if ( $_product->is_sold_individually() ) {
										printf( '1 <input type="hidden" name="wishlist_item[%s][qty]" value="1" />', $item_key );
									} else {
										woocommerce_quantity_input(
											[
												'input_name'   => "wishlist_item[{$item_key}][qty]",
												'input_value'  => $item->get_quantity(),
												'max_value'    => $_product->get_max_purchase_quantity(),
												'min_value'    => '0',
												'product_name' => $_product->get_name(),
											],
											$_product
										);
									}
									?>
								</div>
							<?php endif; ?>

							<?php if ( $args['columns']['stock'] ) : ?>
								<div class="product-stock-status" data-title="<?php esc_attr_e( 'Stock status', 'ecomus' ); ?>">
									<?php
									$availability = $_product->get_availability();
									printf( '<span class="%s">%s</span>', esc_attr( $availability['class'] ), $availability['availability'] ? esc_html( $availability['availability'] ) : esc_html__( 'In Stock', 'ecomus' ) );
									?>
								</div>
							<?php endif; ?>

							<?php if ( $args['columns']['date'] ) : ?>
								<div class="product-date"><?php echo esc_html( $item->get_date_added()->format( get_option( 'date_format' ) ) ); ?></div>
							<?php endif; ?>

						</div>

						<?php if ( $args['columns']['purchase'] ) : ?>
							<div class="product-add-to-cart product-actions">
								<?php
								if ( $_product->is_purchasable() ) {
									$GLOBALS['product'] = $_product;

									woocommerce_template_loop_add_to_cart( [ 'quantity' => max( 1, $item->get_quantity() ) ] );

									wc_setup_product_data( $GLOBALS['post'] );
								}
								?>
							</div>
						<?php endif; ?>
					</div>

				</li>
				<?php
			endforeach;
			?>
	</ul>

	<?php do_action( 'wcboost_wishlist_after_wishlist_table', $wishlist ); ?>

	<?php if ( $wishlist->can_edit() && $args['columns']['quantity'] ) : ?>
		<div class="ecomus-actions">
			<button type="submit" class="button alt" name="update_wishlist" value="<?php esc_attr_e( 'Update wishlist', 'ecomus' ); ?>"><?php esc_html_e( 'Update wishlist', 'ecomus' ); ?></button>
			<input type="hidden" name="wishlist_id" value="<?php echo esc_attr( $wishlist->get_id() ); ?>" />
			<?php wp_nonce_field( 'ecomus-update' ); ?>
		</div>
	<?php endif; ?>
</form>