<?php
/**
 * Hooks of Compare.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

use \Ecomus\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Compare template.
 */
class Compare {
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
		add_filter( 'wcboost_products_compare_button_template_args', array( $this, 'products_compare_button_template_args' ), 10, 2 );
		add_filter( 'wcboost_products_compare_add_to_compare_fragments', array( $this, 'products_compare_add_to_compare_fragments' ), 10, 1 );
		add_filter( 'wcboost_products_compare_single_add_to_compare_link', array( $this, 'single_add_to_compare_link' ), 20, 2 );
		add_filter( 'wcboost_products_compare_loop_add_to_compare_link', array( $this, 'loop_add_to_compare_link' ), 20, 2 );

		add_filter( 'wcboost_products_compare_button_icon', array( $this, 'compare_svg_icon' ), 20, 2 );

		// Remove Default Compare button.
		if( class_exists('\WCBoost\ProductsCompare\Frontend') ) {
			$compare = \WCBoost\ProductsCompare\Frontend::instance();
			remove_action( 'woocommerce_after_add_to_cart_form', array( $compare, 'single_add_to_compare_button' ) );
			remove_action( 'woocommerce_after_shop_loop_item', array( $compare, 'loop_add_to_compare_button' ), 15 );
		}

		// Product Card Compare
		add_action( 'woocommerce_before_shop_loop_item', array( $this, 'render_product_card' ), 0 );

		// Single Product Compare
		if( class_exists('\WCBoost\ProductsCompare\Frontend') ) {
			add_action( 'woocommerce_after_add_to_cart_button', array( \WCBoost\ProductsCompare\Frontend::instance(), 'single_add_to_compare_button' ), 21 );
		}

		// Compare Page
		add_filter( 'wcboost_products_compare_empty_message', array( $this, 'products_compare_empty_message' ), 20, 1 );
		add_filter( 'wcboost_products_compare_fields', array( $this, 'products_compare_fields' ) );
		add_action( 'wcboost_products_compare_custom_field', array( $this, 'products_compare_custom_field' ), 20, 3 );
	}

	public function render_product_card() {
		if(! Helper::get_option('product_card_compare') ) {
			return;
		}
		switch ( \Ecomus\WooCommerce\Helper::get_product_card_layout() ) {
			case '1':
			case '5':
			case '6':
			case '7':
			case '8':
			case '9':
				add_action( 'ecomus_product_loop_thumbnail_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ), array( \WCBoost\ProductsCompare\Frontend::instance(), 'loop_add_to_compare_button' ), 20 );
				break;

			case '2':
			case '3':
				add_action( 'ecomus_product_loop_thumbnail_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ), array( \WCBoost\ProductsCompare\Frontend::instance(), 'loop_add_to_compare_button' ), 50 );
				break;

			case '4':
				add_action( 'ecomus_product_loop_thumbnail_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ), array( \WCBoost\ProductsCompare\Frontend::instance(), 'loop_add_to_compare_button' ), 15 );
				break;

			case 'list':
				add_action( 'ecomus_after_shop_loop_item_list', array( \WCBoost\ProductsCompare\Frontend::instance(), 'loop_add_to_compare_button' ), 40 );
				break;

			default:
				break;
		}

		// For product loop primary
		add_action( 'ecomus_product_loop_primary_thumbnail', array( \WCBoost\ProductsCompare\Frontend::instance(), 'loop_add_to_compare_button' ), 20 );
	}

	/**
	 * Show button compare.
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function products_compare_button_template_args( $args, $product ) {
		$args['class'][] = 'product-loop-button button em-flex-align-center em-flex-center';

		return $args;
	}

	/**
	 * Ajaxify update count compare
	 *
	 * @since 1.0
	 *
	 * @param array $fragments
	 *
	 * @return array
	 */
	public static function products_compare_add_to_compare_fragments( $data ) {
		$compare_counter = intval(\WCBoost\ProductsCompare\Plugin::instance()->list->count_items());
		$compare_class = $compare_counter == 0 ? ' empty-counter' : '';
		$data['span.header-compare__counter'] = '<span class="header-counter header-compare__counter' . $compare_class . '">'. $compare_counter . '</span>';

		return $data;
	}

	/**
	 * Compare icon
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function compare_svg_icon($svg, $icon) {
		if( $icon == 'arrows' ) {
			$svg = \Ecomus\Icon::inline_svg('icon=cross-arrow');
		} else if( $icon == 'check' ) {
			if( get_option( 'wcboost_products_compare_exists_item_button_behaviour' ) == 'remove' ) {
				$svg = \Ecomus\Icon::get_svg('trash-mt');
			} else {
				$svg = \Ecomus\Icon::inline_svg('icon=check');
			}
		}

		return $svg;
	}

	/**
	 * Change compare link
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function loop_add_to_compare_link($html, $args) {
		$classes = is_singular( 'product' ) ? 'wcboost-products-compare-button wcboost-products-compare-button--single button' : 'wcboost-products-compare-button wcboost-products-compare-button--loop button';
		$product_title = isset( $args['product_title'] ) ? $args['product_title'] : '';
		if( empty( $product_title ) ) {
			$product = isset($args['product_id']) ? wc_get_product( $args['product_id'] ) : '';
			$product_title = $product ? $product->get_title() : '';
		}

		$label_add = \WCBoost\ProductsCompare\Helper::get_button_text( 'add' );
		$label_added = \WCBoost\ProductsCompare\Helper::get_button_text( 'view' );

		if( get_option( 'wcboost_products_compare_exists_item_button_behaviour' ) == 'remove' ) {
			$label_added = \WCBoost\ProductsCompare\Helper::get_button_text( 'remove' );
		}

		return sprintf(
			'<a href="%s" class="em-button-icon em-button-light em-tooltip %s" role="button" %s data-product_title="%s" data-tooltip="%s" data-tooltip_added="%s">
				%s
				<span class="wcboost-products-compare-button__text">%s</span>
			</a>',
			esc_url( isset( $args['url'] ) ? $args['url'] : add_query_arg( [ 'add-to-compare' => $args['product_id'] ] ) ),
			esc_attr( isset( $args['class'] ) ? $args['class'] : esc_attr( $classes ) ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_attr( $product_title ),
			wp_kses_post( $label_add ),
			wp_kses_post( $label_added ),
			empty( $args['icon'] ) ? '' : '<span class="wcboost-products-compare-button__icon">' . $args['icon'] . '</span>',
			esc_html( isset( $args['label'] ) ? $args['label'] : __( 'Compare', 'ecomus' ) )
		);
	}

		/**
	 * Change compare link
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function single_add_to_compare_link($html, $args) {
		$classes = is_singular( 'product' ) ? 'wcboost-products-compare-button wcboost-products-compare-button--single button' : 'wcboost-products-compare-button wcboost-products-compare-button--loop button';
		$product_title = isset( $args['product_title'] ) ? $args['product_title'] : '';
		if( empty( $product_title ) ) {
			$product = isset($args['product_id']) ? wc_get_product( $args['product_id'] ) : '';
			$product_title = $product ? $product->get_title() : '';
		}

		$label_add = \WCBoost\ProductsCompare\Helper::get_button_text( 'add' );
		$label_added = \WCBoost\ProductsCompare\Helper::get_button_text( 'view' );

		if( get_option( 'wcboost_products_compare_exists_item_button_behaviour' ) == 'remove' ) {
			$label_added = \WCBoost\ProductsCompare\Helper::get_button_text( 'remove' );
		}

		return sprintf(
			'<a href="%s" class="em-button-icon em-button-outline em-tooltip %s" role="button" %s data-product_title="%s" data-tooltip="%s" data-tooltip_added="%s">
				%s
				<span class="wcboost-products-compare-button__text">%s</span>
			</a>',
			esc_url( isset( $args['url'] ) ? $args['url'] : add_query_arg( [ 'add-to-compare' => $args['product_id'] ] ) ),
			esc_attr( isset( $args['class'] ) ? $args['class'] : esc_attr( $classes ) ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_attr( $product_title ),
			wp_kses_post( $label_add ),
			wp_kses_post( $label_added ),
			empty( $args['icon'] ) ? '' : '<span class="wcboost-products-compare-button__icon">' . $args['icon'] . '</span>',
			esc_html( isset( $args['label'] ) ? $args['label'] : __( 'Compare', 'ecomus' ) )
		);
	}

	/**
	 * Product compare empty messages
	 *
	 * @return void
	 */
	public function products_compare_empty_message() {
		return sprintf(
						'<h3>%s</h3>
						<p>%s</p>
						<p>%s</p>',
					 	esc_html__( 'Compare list is empty', 'ecomus' ),
					 	esc_html__( 'No products added in the compare list. You must add some products to compare them.', 'ecomus' ),
						esc_html__( 'You will find a lot of interesting products on our "Shop" page.', 'ecomus' ),
					);
	}

	public function products_compare_fields($fields) {
		$options = (array) Helper::get_option('compare_page_columns');
		$fields = $this->get_default_compare_fields($fields, $options);
		$attributes = $this->attribute_taxonomies($options);

		if( $attributes ) {
			if( isset($fields['add-to-cart']) ) {
				unset( $fields['add-to-cart'] );
			}
			if(in_array( 'add-to-cart', $options ) ) {
				$attributes['add-to-cart'] = '';
			}
			$fields = array_merge( $fields, $attributes );
		}

		return $fields;
	}

	private function get_default_compare_fields($fields, $options) {
		$default_columns = [
			'rating'      => esc_html__( 'Rating', 'ecomus' ),
			'price'       => esc_html__( 'Price', 'ecomus' ),
			'stock'       => esc_html__( 'Availability', 'ecomus' ),
			'sku'         => esc_html__( 'SKU', 'ecomus' ),
			'dimensions'  => esc_html__( 'Dimensions', 'ecomus' ),
			'weight'      => esc_html__( 'Weight', 'ecomus' ),
			'add-to-cart' => esc_html__( 'Add To Cart', 'ecomus' ),
		];

		foreach( $default_columns as $key => $name ){
			if (isset($fields[$key]) && !  in_array( $key, $options )) {
				unset($fields[$key]);
			}
		}

		if( isset($fields['attributes']) ) {
			unset( $fields['attributes'] );
		}

		return $fields;

	}

	/**
	 * Get Woocommerce Attribute Taxonomies
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function attribute_taxonomies($options) {

		$attributes = array();

		$attribute_taxonomies = wc_get_attribute_taxonomies();
		if ( empty( $attribute_taxonomies ) ) {
			return array();
		}
		foreach ( $attribute_taxonomies as $attribute ) {
			$tax_name =  $attribute->attribute_name;
			$tax = wc_attribute_taxonomy_name( $tax_name );

			if ( taxonomy_exists( $tax ) && in_array( $tax_name, $options ) ) {
				$attributes[ $tax ] = ucfirst( $attribute->attribute_label );
			}
		}


		return $attributes;
	}

	public function products_compare_custom_field($field, $product, $key) {
		if ( taxonomy_exists( $field ) ) {
			$attributes = array();
			$terms                     = get_the_terms( $product->get_id(), $field );
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$term                        = sanitize_term( $term, $field );
					$attributes[] = $term->name;
				}
			}
			echo implode( ', ', $attributes );
		}
	}
}
