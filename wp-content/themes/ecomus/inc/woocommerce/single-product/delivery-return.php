<?php
/**
 * Single Product hooks.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce\Single_Product;

use Ecomus\Helper;
use Ecomus\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Single Product
 */
class Delivery_Return {
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
		add_action( 'woocommerce_single_product_summary', array( $this, 'delivery_return' ), 34 );
	}

	/**
	 * Product Share
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function delivery_return() {
		if( ! apply_filters( 'ecomus_delivery_return_content', true ) ) {
			return;
		}

		\Ecomus\Theme::set_prop( 'modals', 'product-delivery-return' );

		echo '<a href="#" class="ecomus-extra-link-item ecomus-extra-link-item--delivery-return em-font-semibold" data-toggle="modal" data-target="product-delivery-return-modal">'. Icon::get_svg( 'delivery' ) . esc_html__( 'Delivery & Return', 'ecomus' ) . '</a>';
	}

	/**
	 * Product Share data
	 */
	public static function delivery_return_data() {
		$page_ID 	= Helper::get_option( 'product_delivery_return_page' );
		$content = '';

		if( class_exists('\Elementor\Plugin') ) {
			$elementor_instance = \Elementor\Plugin::instance();
			$document = $elementor_instance->documents->get( $page_ID );
			if ( $document && $document->is_built_with_elementor() ) {
				$content = $elementor_instance->frontend->get_builder_content_for_display( $page_ID );
			}
		}

		if ( empty( $content ) ) {
			$the_post 	= get_post( $page_ID );
			if( $the_post ) {
				$content 	= $the_post->post_content;
			}
		}

		$args = [
			'title' => get_the_title( $page_ID ),
			'content' => $content
		];

		return $args;
	}
}
