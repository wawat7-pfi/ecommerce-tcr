<?php
/**
 * Hooks of Wishlist.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

use \Ecomus\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Wishlist template.
 */
class Wishlist {
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
		add_filter( 'wcboost_wishlist_add_to_wishlist_fragments', array( $this, 'update_wishlist_count' ), 10, 1 );

		// Change the button wishlist
		add_filter('wcboost_wishlist_button_template_args', array( $this, 'wishlist_button_template_args' ), 20, 3 );

		add_filter('wcboost_wishlist_svg_icon', array( $this, 'wishlist_svg_icon' ), 20, 3 );

		add_filter( 'wcboost_wishlist_loop_add_to_wishlist_link', array( $this, 'wishlist_button_product_loop' ), 20, 2 );
		add_filter( 'wcboost_wishlist_single_add_to_wishlist_link', array( $this, 'wishlist_button_single_product' ), 20, 2 );

		// Product Card Wishlist
		add_action( 'woocommerce_before_shop_loop_item', array( $this, 'render_product_card' ), 0 );

		// Single Product Wishlist
		if( class_exists('\WCBoost\Wishlist\Frontend') ) {
			add_action( 'woocommerce_after_add_to_cart_button', array( \WCBoost\Wishlist\Frontend::instance(), 'single_add_to_wishlist_button' ), 21 );
		}

		// Wishlist Page
		add_filter( 'wcboost_wishlist_empty_message', array( $this, 'wishlist_empty_message' ), 20, 1 );

		// Add class for add to cart button
		add_filter( 'ecomus_add_to_cart_button_class', array( $this, 'add_to_cart_button_class' ) );

	}

	/**
	 * Ajaxify update count wishlist
	 *
	 * @since 1.0
	 *
	 * @param array $fragments
	 *
	 * @return array
	 */
	public function update_wishlist_count($data) {
		$wishlist_counter = intval( \WCBoost\Wishlist\Helper::get_wishlist()->count_items() );
		$wishlist_class = $wishlist_counter == 0 ? ' empty-counter' : '';

		$data['span.header-wishlist__counter'] = '<span class="header-counter header-wishlist__counter' . $wishlist_class . '">'. $wishlist_counter . '</span>';

		return $data;
	}

	public function render_product_card() {
		if(! Helper::get_option('product_card_wishlist') ) {
			return;
		}

		switch ( \Ecomus\WooCommerce\Helper::get_product_card_layout() ) {
			case '1':
			case '5':
			case '6':
			case '7':
			case '8':
			case '9':
				add_action( 'ecomus_product_loop_thumbnail_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ), array( \WCBoost\Wishlist\Frontend::instance(), 'loop_add_to_wishlist_button' ), 15 );
				break;

			case '2':
			case '3':
				add_action( 'ecomus_product_loop_thumbnail_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ), array( \WCBoost\Wishlist\Frontend::instance(), 'loop_add_to_wishlist_button' ), 45 );
				break;

			case '4':
				add_action( 'ecomus_product_loop_thumbnail_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ), array( \WCBoost\Wishlist\Frontend::instance(), 'loop_add_to_wishlist_button' ), 10 );
				break;

			case 'list':
				add_action( 'ecomus_after_shop_loop_item_list', array( \WCBoost\Wishlist\Frontend::instance(), 'loop_add_to_wishlist_button' ), 35 );
				break;

			default:
				break;
		}

		// For product loop primary
		add_action( 'ecomus_product_loop_primary_thumbnail', array( \WCBoost\Wishlist\Frontend::instance(), 'loop_add_to_wishlist_button' ), 15 );
	}

	/**
	 * Change button args: button title
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function wishlist_button_template_args( $args, $wishlist, $product ) {
		$args['class'][] = 'product-loop-button em-flex-align-center em-flex-center';

		return $args;
	}

	/**
	 * Wishlist icon
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function wishlist_svg_icon($svg, $icon) {
		if( $icon == 'heart' ) {
			$svg = \Ecomus\Icon::inline_svg('icon=heart');
		} elseif( $icon == 'heart-filled' ) {
			if( get_option( 'wcboost_wishlist_exists_item_button_behaviour' ) == 'view_wishlist' ) {
				$svg = \Ecomus\Icon::inline_svg('icon=heart-filled');
			} else {
				$svg = \Ecomus\Icon::inline_svg('icon=trash');
			}
		}

		return $svg;
	}

	/**
	 * Change wishlist button product loop
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function wishlist_button_product_loop( $html, $args ) {
		$product_title = isset( $args['product_title'] ) ? $args['product_title'] : '';
		if( empty( $product_title ) ) {
			$product = isset($args['product_id']) ? wc_get_product( $args['product_id'] ) : '';
			$product_title = $product ? $product->get_title() : '';
		}

		$label_add = \WCBoost\Wishlist\Helper::get_button_text( 'add' );
		$label_added = \WCBoost\Wishlist\Helper::get_button_text( 'remove' );

		if( get_option( 'wcboost_wishlist_exists_item_button_behaviour' ) == 'view_wishlist' ) {
			$label_added = \WCBoost\Wishlist\Helper::get_button_text( 'view' );
		}

		return sprintf(
			'<a href="%s" class="em-button-icon em-button-light em-tooltip %s" %s data-product_title="%s" data-tooltip="%s" data-tooltip_added="%s">' .
				( ! empty( $args['icon'] ) ? '<span class="wcboost-wishlist-button__icon">' . $args['icon'] . '</span>' : '' ) .
				'<span class="wcboost-wishlist-button__text">%s</span>' .
			'</a>',
			esc_url( isset( $args['url'] ) ? $args['url'] : add_query_arg( [ 'add-to-wishlist' => $args['product_id'] ] ) ),
			esc_attr( isset( $args['class'] ) ? $args['class'] : 'wcboost-wishlist-button button' ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_attr( $product_title ),
			wp_kses_post( $label_add ),
			wp_kses_post( $label_added ),
			isset( $args['label'] ) ? esc_html( $args['label'] ) : esc_html__( 'Add to wishlist', 'ecomus' )
		);
	}

	/**
	 * Change wishlist button single product
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function wishlist_button_single_product( $html, $args ) {
		$label_add = \WCBoost\Wishlist\Helper::get_button_text( 'add' );
		$label_added = \WCBoost\Wishlist\Helper::get_button_text( 'remove' );

		if( get_option( 'wcboost_wishlist_exists_item_button_behaviour' ) == 'view_wishlist' ) {
			$label_added = \WCBoost\Wishlist\Helper::get_button_text( 'view' );
		}

		return sprintf(
			'<a href="%s" class="em-button-icon em-button-outline em-tooltip %s" %s data-product_title="%s" data-tooltip="%s" data-tooltip_added="%s">' .
				( ! empty( $args['icon'] ) ? '<span class="wcboost-wishlist-button__icon">' . $args['icon'] . '</span>' : '' ) .
				'<span class="wcboost-wishlist-button__text">%s</span>' .
			'</a>',
			esc_url( isset( $args['url'] ) ? $args['url'] : add_query_arg( [ 'add-to-wishlist' => $args['product_id'] ] ) ),
			esc_attr( isset( $args['class'] ) ? $args['class'] : 'wcboost-wishlist-single-button wcboost-wishlist-button button' ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_attr( isset( $args['product_title'] ) ? $args['product_title'] : wc_get_product( $args['product_id'] )->get_title() ),
			wp_kses_post( $label_add ),
			wp_kses_post( $label_added ),
			isset( $args['label'] ) ? esc_html( $args['label'] ) : esc_html__( 'Add to wishlist', 'ecomus' )
		);
	}

	/**
	 * Wishlist Page Empty
	 *
	 * @return void
	 */
	public function wishlist_empty_message() {
		return sprintf(
					'<h3>%s</h3>
					<p>%s</p>',
					esc_html__( 'Wishlist is empty.', 'ecomus' ),
					esc_html__( "You don't have any products in the wishlist yet. You will find a lot of interesting products on our &quot;Shop&quot; page.", 'ecomus' ),
				);
	}

	public function add_to_cart_button_class( $classes ) {
		if( class_exists('\WCBoost\Wishlist\Helper' ) && \WCBoost\Wishlist\Helper::is_wishlist()  ) {
			$classes = 'button product_type_variable add_to_cart_button ecomus-button product-loop-button product-loop-button-atc em-flex-align-center em-flex-center em-button-light em-button-icon em-tooltip';
		}

		return $classes;
	}
}