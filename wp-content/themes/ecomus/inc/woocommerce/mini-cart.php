<?php
/**
 * Mini Cart hooks.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

use Ecomus\Helper;
use Ecomus\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Mini Cart
 */
class Mini_Cart {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action('woocommerce_mini_cart_contents', array( $this, 'mini_cart_recommended_products' ));

		// Ajax update mini cart.
		add_action( 'wc_ajax_update_cart_item', array( $this, 'update_cart_item' ) );

		add_action('ecomus_mini_cart_products_recommended_loop_after', array( $this, 'mini_cart_featured_icon' ));

		// Add html before and after mini cart items.
		add_action( 'ecomus_after_woocommerce_mini_cart_items', array( $this, 'note_estimate_coupon_mini_cart' ), 20 );
		add_action( 'ecomus_before_widget_shopping_cart_total', array( $this, 'show_applied_coupons_in_mini_cart' ), 99 );
		add_action( 'ecomus_after_mini_cart_content', array( $this, 'note_coupon_estimate_popover' ), 99 );

		add_action( 'wc_ajax_ecomus_apply_coupon', array( $this, 'ajax_apply_coupon' ) );
		add_action( 'wc_ajax_ecomus_remove_coupon', array( $this, 'ajax_remove_coupon' ) );
		add_action( 'wc_ajax_ecomus_update_shipping_address', array( $this, 'ajax_update_shipping_address' ) );

		remove_action( 'woocommerce_widget_shopping_cart_total', 'woocommerce_widget_shopping_cart_subtotal', 10 );
		add_action( 'woocommerce_widget_shopping_cart_total', array( $this, 'custom_mini_cart_total' ), 10 );
	}

		/**
	 * Update a cart item.
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function update_cart_item() {
		if ( empty( $_POST['cart_item_key'] ) || ! isset( $_POST['qty'] ) ) {
			wp_send_json_error();
			exit;
		}

		$cart_item_key 		= wc_clean( isset( $_POST['cart_item_key'] ) ? wp_unslash( $_POST['cart_item_key'] ) : '' );
		$cart_item_length 	= isset( $_POST['cart_item_length'] ) ? $_POST['cart_item_length'] : '';
		$qty           		= floatval( $_POST['qty'] );

		check_admin_referer( 'ecomus-update-cart-qty--' . $cart_item_key, 'security' );

		do_action( 'ecomus_update_cart_item', $cart_item_key, $qty );

		ob_start();
		WC()->cart->set_quantity( $cart_item_key, $qty );

		if ( $cart_item_key && false !== WC()->cart->set_quantity( $cart_item_key, $qty ) ) {
			if ( $cart_item_length == 1 && ! $qty ) {
				WC()->cart->empty_cart();
			}

			\WC_AJAX::get_refreshed_fragments();
		} else {
			wp_send_json_error();
		}
	}

	function mini_cart_recommended_products() {
        if ( ! class_exists( 'WC_Shortcode_Products' ) ) {
            return;
        }

        $limit = Helper::get_option( 'mini_cart_products_limit' );
        $type  = Helper::get_option( 'mini_cart_products' );

        if('none' == $type){
            return;
        } elseif('crosssells_products' == $type) {
			$cross_sells = array_filter( array_map( 'wc_get_product', WC()->cart->get_cross_sells() ), 'wc_products_array_filter_visible' );
			$orderby = 'rand';
			$order = 'desc';
			$orderby     = apply_filters( 'woocommerce_cross_sells_orderby', $orderby );
			$order       = apply_filters( 'woocommerce_cross_sells_order', $order );
			$cross_sells = wc_products_array_orderby( $cross_sells, $orderby, $order );
			$limit       = intval( apply_filters( 'woocommerce_cross_sells_total', $limit ) );
			$cross_sells = $limit > 0 ? array_slice( $cross_sells, 0, $limit ) : $cross_sells;
			if( empty( $cross_sells ) ) {
				return;
			}
			$this->products_recommended_content($cross_sells);
		} else {
			$atts = array(
				'per_page'     => intval( $limit ),
				'category'     => '',
				'cat_operator' => 'IN',
			);

			switch ( $type ) {
				case 'sale_products':
				case 'top_rated_products':
					$atts = array_merge( array(
						'orderby' => 'title',
						'order'   => 'ASC',
					), $atts );
					break;

				case 'recent_products':
					$atts = array_merge( array(
						'orderby' => 'date',
						'order'   => 'DESC',
					), $atts );
					break;

				case 'featured_products':
					$atts = array_merge( array(
						'orderby' => 'date',
						'order'   => 'DESC',
					), $atts );
					break;
			}

			$args  = new \WC_Shortcode_Products( $atts, $type );
			$args  = $args->get_query_args();

			foreach( WC()->cart->get_cart() as $cart_item ){
				$product_id[] = $cart_item['product_id'];
			}

			if ( $product_id ) {
				$args = array_merge( array(
					'post__not_in' => $product_id,
				), $args );
			}

			$query = new \WP_Query( $args );

			if( !count($query->posts) ) {
				return;
			}

			$this->products_recommended_content($query->posts);
			wp_reset_postdata();
		}
	}

	/**
    * Get products recommended content
    *
    * @since 1.0.0
    *
    * @param $query_posts
    *
    * @return void
    */
    public function products_recommended_content($query_posts) {
        ?>
        <li>
			<div class="ecomus-mini-products-recommended">
				<div class="products-recommended-header">
					<h2 class="recommendation-heading em-font-semibold"> <?php echo esc_html__( 'Customers also bought', 'ecomus' ); ?> </h2>
					<span class="swiper-pagination"></span>
				</div>
				<div class="swiper">
					<ul class="woocommerce-loop-products swiper-wrapper">
						<?php
						foreach ( $query_posts as $product ) {
							$_product = is_numeric( $product ) ? wc_get_product( $product ) : $product;

							if( empty( $_product ) || ! is_object( $_product ) ) {
								continue;
							}
							?>

							<li class="woocommerce-loop-product">
								<a class="woocommerce-loop-product__thumbnail" href="<?php echo esc_url( $_product->get_permalink() ); ?>">
									<?php echo ! empty( $_product ) ?  $_product->get_image( 'woocommerce_thumbnail' ) : ''; ?>
								</a>
								<div class="woocommerce-loop-product__summary">
									<a href="<?php echo esc_url( $_product->get_permalink() ); ?>">
										<span class="woocommerce-loop-product__title"><?php echo esc_html( $_product->get_name() ); ?></span>
									</a>
									<span class="price"><?php echo ! empty( $_product ) ? $_product->get_price_html() : ''; ?></span>
								</div>
								<?php do_action('ecomus_mini_cart_products_recommended_loop_after', $_product) ?>
							</li>

							<?php
						}
					?>
					</ul>
				</div>
			</div>
		</li>
	<?php
	}

	/**
	 *  Featured icon
	 */
	 public function mini_cart_featured_icon($product = false) {
		if ( Helper::get_option( 'mini_cart_featured_icon' ) == 'quick-view' ) {
			$this->mini_cart_quick_view_icon($product);
		} else {
			$this->mini_cart_add_to_cart_button($product);
		}
	}

	/**
	 *  Featured icon
	 */
	public function mini_cart_quick_view_icon($product) {
		$classes = 'em-button product-loop-button em-flex-align-center em-flex-center em-button-icon em-tooltip';

		if( \Ecomus\WooCommerce\Helper::get_product_card_layout() == '2' ) {
			$classes .= ' mobile-show-button';
		}

		$classes = apply_filters( 'ecomus_quick_view_button_icon_classes', $classes );

		\Ecomus\WooCommerce\Loop\Quick_View::quick_view_button_html( $classes, true, $product );
	}

	/**
	 *  Add to cart button
	 */
	 public function mini_cart_add_to_cart_button($product) {
		$classes = 'button em-button product-loop-button em-flex-align-center em-flex-center em-button-icon em-tooltip ecomus-featured-icons';
		$classes .= ' product_type_' . $product->get_type();
		$classes .= $product->is_purchasable() && $product->is_in_stock() ? ' add_to_cart_button' : '';
		$classes .= $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? ' ajax_add_to_cart' : '';

		$classes = apply_filters( 'ecomus_quick_view_button_icon_classes', $classes );

		$data_toggle = $product->get_type() == 'variable' ? 'data-toggle="modal"' : '';
		$data_target = $product->get_type() == 'variable' ? 'data-target="quick-add-modal"' : '';

		echo sprintf(
			'<a href="%s" data-quantity="1" class="%s" data-product_id="%s" data-tooltip="%s" aria-label="%s" %s %s rel="nofollow">%s</a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( $classes ),
			esc_attr($product->get_id()),
			$this->mini_cart_add_to_cart_button_text($product),
			$this->mini_cart_add_to_cart_button_text($product) . esc_attr__( ' for ', 'ecomus' ) . $product->get_title(),
			$data_toggle,
			$data_target,
			\Ecomus\Helper::get_cart_icons()
		);
	 }

	/**
	 *  Add to cart button text
	 */
	 public function mini_cart_add_to_cart_button_text($product) {
		if( $product && $product->get_type() == 'variable' ) {
			$product_card = \Ecomus\WooCommerce\Helper::get_product_card_layout();
			$button_text = $product_card == '4' ? esc_html__( 'Quick Add', 'ecomus' ) : esc_html__( 'Quick Shop', 'ecomus' );

			\Ecomus\Theme::set_prop( 'modals', 'quickadd' );
		} else {
			$button_text = $product->add_to_cart_text();
		}

		return $button_text;
	 }

	public function note_estimate_coupon_mini_cart() {
		if ( Helper::get_option( 'cart_note_enable' ) || Helper::get_option( 'cart_discount_enable' ) || Helper::get_option( 'cart_estimate_enable' ) ) :
		?>
			<div class="ecomus-estimate-coupon em-flex em-flex-center em-flex-align-center">
				<?php if ( Helper::get_option( 'cart_note_enable' ) ) : ?>
					<div class="ecomus-estimate-coupon__button ecomus-discount em-button em-button-light" data-tooltip="<?php esc_attr_e( 'Add Order Note', 'ecomus' ); ?>" data-toggle="popover" data-target="note-popover" data-padding="false">
						<?php echo \Ecomus\Icon::get_svg( 'note', 'ui', 'class=icon-fill-none' ); ?>
					</div>
				<?php endif; ?>
				<?php if ( Helper::get_option( 'cart_discount_enable' ) ) : ?>
					<?php if( wc_coupons_enabled() ) : ?>
						<div class="ecomus-estimate-coupon__button ecomus-discount em-button em-button-light" data-toggle="popover" data-target="discount-popover" data-padding="false">
							<?php echo \Ecomus\Icon::get_svg( 'discount', 'ui', 'class=icon-fill-none' ); ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ( Helper::get_option( 'cart_estimate_enable' ) ) : ?>
					<?php if ( WC()->cart->needs_shipping() && 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>
						<div class="ecomus-estimate-coupon__button ecomus-estimate em-button em-button-light" data-toggle="popover" data-target="estimate-popover" data-padding="false">
							<?php echo \Ecomus\Icon::get_svg( 'box', 'ui', 'class=icon-fill-none' ); ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		<?php
		endif;
	}

	/**
	 * Output the note, coupon and estimate popover.
	 */
	public function note_coupon_estimate_popover() {
		?>
		<?php if ( Helper::get_option( 'cart_note_enable' ) ) : ?>
			<div id="note-popover" class="popover note-popover ecomus-estimate-coupon__popover" data-padding="false">
				<div class="popover__backdrop"></div>
				<div class="popover__container">
					<div class="popover__content">
						<div class="ecomus-note__icon em-flex em-flex-align-center em-font-semibold em-color-dark">
							<?php echo \Ecomus\Icon::get_svg( 'note', 'ui', 'class=icon-fill-none' ); ?>
							<?php esc_html_e('Add Order Note', 'ecomus'); ?>
						</div>
						<div id="order_comments_field" class="woocommerce-form-row">
							<textarea name="order_comments" class="input-text" id="order_comments" placeholder="<?php echo esc_attr__('Notes about your order, e.g. special notes for delivery.', 'woocommerce'); ?>" rows="4" cols="5"></textarea>
						</div>
						<button class="order-comments-save em-button-hover-effect  popover__button-close" data-popover="close"><?php esc_html_e( 'Save', 'ecomus' ); ?></button>
						<button class="em-button em-button-outline-dark popover__button-close" data-popover="close"><?php esc_html_e( 'Close', 'ecomus' ); ?></button>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php if ( Helper::get_option( 'cart_discount_enable' ) ) : ?>
			<?php if( wc_coupons_enabled() ) : ?>
				<div id="discount-popover" class="popover discount-popover ecomus-estimate-coupon__popover" data-padding="false">
					<div class="popover__backdrop"></div>
					<div class="popover__container">
						<div class="popover__content">
							<div class="ecomus-coupon__icon text-center em-color-dark">
								<?php echo \Ecomus\Icon::get_svg( 'discount', 'ui', 'class=icon-fill-none' ); ?>
							</div>
							<div class="woocommerce-notices-wrapper"></div>
							<?php if( ! empty( WC()->cart->get_coupons() ) ) : ?>
								<div class="ecomus-mini-cart-coupons em-flex em-flex-column">
									<?php self::coupon_html(); ?>
								</div>
							<?php endif; ?>
							<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
								<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_html_e( 'Coupon code', 'ecomus' ); ?>" />
								<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
								<button type="submit" class="button em-button-hover-effect <?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'ecomus' ); ?>"><?php esc_html_e( 'Apply coupon', 'ecomus' ); ?></button>
								<button class="em-button em-button-outline-dark popover__button-close"><?php esc_html_e( 'Close', 'ecomus' ); ?></button>
							</form>
						</div>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( Helper::get_option( 'cart_estimate_enable' ) ) : ?>
			<?php if ( 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>
				<div id="estimate-popover" class="popover estimate-popover ecomus-estimate-coupon__popover" data-padding="false">
					<div class="popover__backdrop"></div>
					<div class="popover__container">
						<div class="popover__content">
							<div class="ecomus-estimate__icon em-flex em-flex-align-center em-font-semibold em-color-dark">
								<?php echo \Ecomus\Icon::get_svg( 'box', 'ui', 'class=icon-fill-none' ); ?>
								<?php esc_html_e('Estimate Shipping', 'ecomus'); ?>
							</div>
							<div class="woocommerce-notices-wrapper"></div>
							<div id="mini-cart-shipping-calculator-popover" class="ecomus-mini-cart-shipping-calculator">
								<?php woocommerce_shipping_calculator(); ?>
							</div>
							<button class="em-button em-button-outline-dark popover__button-close"><?php esc_html_e( 'Close', 'ecomus' ); ?></button>
						</div>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php
	}

	/**
	 * Show applied coupons in mini cart.
	 */
	public function show_applied_coupons_in_mini_cart() {
		if ( wc_coupons_enabled() && ! empty( WC()->cart->get_applied_coupons() ) ) {
			?>
			<div class="ecomus-mini-cart__coupons em-flex em-flex-column">
			<?php self::coupon_html(); ?>
			</div>
			<?php
		}
	}

	/**
	 * Ajax apply coupon.
	 */
	public function ajax_apply_coupon() {
		if( $_POST['action'] !== 'ecomus_apply_coupon' ) {
			return;
		}

		if ( ! isset( $_POST['coupon_code'] ) ) {
			return;
		}


		WC()->cart->add_discount( wc_format_coupon_code( $_POST['coupon_code'] ) );

		ob_start();
		self::coupon_html();
		$coupon_html = ob_get_clean();

		wp_send_json( array(
			'coupon_html' => $coupon_html,
			'notices' => wc_print_notices( true )
		) );
	}

	/**
	 * Ajax remove coupon.
	 */
	public function ajax_remove_coupon() {
		if( $_POST['action'] !== 'ecomus_remove_coupon' ) {
			return;
		}

		if ( ! isset( $_POST['coupon_code'] ) ) {
			return;
		}

		WC()->cart->remove_coupon( wc_format_coupon_code( $_POST['coupon_code'] ) );

		wp_send_json_success();
	}

	/**
	 * Coupon HTML.
	 */
	public function coupon_html() {
		foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?> em-flex em-flex-align-center em-flex-space-between em-color-dark">
				<div class="em-font-semibold"><?php wc_cart_totals_coupon_label( $coupon ); ?></div>
				<div><?php wc_cart_totals_coupon_html( $coupon ); ?></div>
			</div>
		<?php endforeach;
	}

	/**
	 * Ajax update shipping address.
	 */
	public function ajax_update_shipping_address() {
		if( $_POST['action'] !== 'ecomus_update_shipping_address' ) {
			return;
		}

		$postcode = isset($_POST['calc_shipping_postcode']) ? sanitize_text_field(wp_unslash($_POST['calc_shipping_postcode'])) : '';
		$country  = isset($_POST['calc_shipping_country']) ? sanitize_text_field(wp_unslash($_POST['calc_shipping_country'])) : '';
		$city     = isset($_POST['calc_shipping_city']) ? sanitize_text_field(wp_unslash($_POST['calc_shipping_city'])) : '';
		$state    = isset($_POST['calc_shipping_state']) ? sanitize_text_field(wp_unslash($_POST['calc_shipping_state'])) : '';

		$customer = WC()->customer;
		if ($country) {
			$customer->set_shipping_country($country);
		}
		if ($postcode) {
			$customer->set_shipping_postcode($postcode);
		}
		if ($city) {
			$customer->set_shipping_city($city);
		}
		if ($state && $country) {
			$customer->set_shipping_state($state);
		}

		$customer->save();

		WC()->cart->calculate_shipping();
    	WC()->cart->calculate_totals();

		wp_send_json(array( 'notices' => wc_print_notices( true ) ));
	}

	/**
	 * Custom mini cart total.
	 */
	public function custom_mini_cart_total() {
		$applied_coupons = WC()->cart->get_applied_coupons();

		$subtotal = WC()->cart->get_cart_subtotal();

		if ( ! empty( $applied_coupons ) ) {
			$subtotal = wc_price( WC()->cart->get_subtotal() - WC()->cart->get_discount_total() );
		}

		echo '<strong>' . esc_html__( 'Subtotal:', 'woocommerce' ) . '</strong> ' . $subtotal;
	}
}