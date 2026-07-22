<?php
/**
 * Hooks of cart.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of checkout template.
 */
class Cart {
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
		add_filter( 'ecomus_wp_script_data', array( $this, 'cart_script_data' ) );
		add_action( 'template_redirect', array( $this, 'add_actions' ), 10 );
		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'cart_item_subtotal' ), 10, 3 );
		add_action ('woocommerce_after_cart_table', array( $this,'order_comments'));
	}

	public function add_actions() {
		add_action( 'woocommerce_cart_is_empty', array( $this, 'cart_empty_text' ), 20 );

		// Cross sell product
		if( ! intval( \Ecomus\Helper::get_option( 'cross_sells_products') ) ) {
			remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		} else {
			remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
			add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display' );
		}

		add_filter( 'woocommerce_cross_sells_total', array( $this, 'cross_sells_total' ) );
		add_filter( 'woocommerce_cross_sells_columns', array( $this, 'cross_sells_columns' ) );
	}

	/**
	 * Add cart script data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function cart_script_data( $data ) {
		$data['product_card_hover'] 	= \Ecomus\Helper::get_option( 'product_card_hover' );

		if ( intval( \Ecomus\Helper::get_option( 'update_cart_page_auto' ) ) ) {
			$data['update_cart_page_auto'] = 1;
		}

		if ( intval( \Ecomus\Helper::get_option( 'cross_sells_products' ) ) ) {
			$columns = \Ecomus\Helper::get_option( 'cross_sells_products_columns', [] );
			$desktop_columns = isset( $columns['desktop'] ) ? $columns['desktop'] : '4';
			$tablet_columns  = isset( $columns['tablet'] ) ? $columns['tablet'] : '3';
			$mobile_columns  = isset( $columns['mobile'] ) ? $columns['mobile'] : '2';

			$data['cross_sells_products_columns'] = [
				'desktop' 	=> $desktop_columns,
				'tablet' 	=> $tablet_columns,
				'mobile' 	=> $mobile_columns
			];
		}

		return $data;
	}

	/**
	 * Change total cross cells
	 *
	 * @return void
	 */
	public function cross_sells_total( $total ) {
		$total = \Ecomus\Helper::get_option( 'cross_sells_products_numbers' );

		return $total;
	}

	/**
	 * Change columns upsell
	 *
	 * @return void
	 */
	public function cross_sells_columns( $columns ) {
		$columns = \Ecomus\Helper::get_option( 'cross_sells_products_columns', [] );
		$columns = isset( $columns['desktop'] ) ? $columns['desktop'] : '4';

		return $columns;
	}

	/**
	 * Add cart empty heading and sub heading
	 *
	 * @return void
	 */
	public function cart_empty_text() {
		echo sprintf(
			'<div class="em-cart-text-empty text-center"><h5>%s</h5><p>%s</p></div>',
			esc_html__( 'Your cart is empty', 'ecomus' ),
			esc_html__( 'You may check out all the available products and buy some in the shop', 'ecomus' )
		);
	}

	public function cart_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {
		$_product   = $cart_item['data'];
		if( WC()->cart->display_prices_including_tax() ) {
			$_product_regular_price = floatval( wc_get_price_including_tax( $_product, array( 'price' => $_product->get_regular_price() ) ) );
			$_product_sale_price = floatval( wc_get_price_including_tax( $_product, array( 'price' => $_product->get_price() ) ) );
		} else {
			$_product_regular_price = floatval( $_product->get_regular_price() );
			$_product_sale_price = floatval( $_product->get_price() );
		}

		if( $_product_sale_price > 0 && $_product_regular_price > $_product_sale_price ) {
			$subtotal .= '<br/><span class="ecomus-price-saved">' . esc_html__( 'Save: ', 'ecomus' ) . wc_price( ( $_product_regular_price * $cart_item['quantity'] ) - ( $_product_sale_price * $cart_item['quantity'] ) ) .'</span>';
		}

		return $subtotal;
	}

	public function order_comments() {
		echo '<div class="form-row notes" id="order_comments_field">';
		echo '<label for="order_comments">' . esc_html__('Order notes', 'woocommerce') . '</label>';
		echo '<textarea name="order_comments" class="input-text" id="order_comments" placeholder="' . esc_attr__('Notes about your order, e.g. special notes for delivery.', 'woocommerce') . '" rows="4" cols="5"></textarea>';
		echo '</div>';
	}


}
