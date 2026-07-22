<?php
/**
 * Modals functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Modals initial
 *
 */
class Modals {
		/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;


	/**
	 * Modals ID
	 *
	 * @var $post_id
	 */
	protected static $footer_id = null;


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
		add_action( 'wp_footer', array( $this, 'modals_items' ) );
		add_action( 'wp_footer', array( $this, 'panel_items' ) );
		add_action( 'wp_footer', array( $this, 'popover_items' ) );
	}

	/**
	 * Modal items
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function modals_items() {
		$items = apply_filters( 'ecomus_modals_items', (array) \Ecomus\Theme::get_prop( 'modals' ) );

		if ( empty( $items ) ) {
			return;
		}

		foreach ( $items as $item ) {
			$args = array();

			if( $item == 'product-ask-question' ) {
				$args = \Ecomus\WooCommerce\Single_Product\Ask_Question::ask_question_data();
			}

			if( $item == 'product-delivery-return' ) {
				$args = \Ecomus\WooCommerce\Single_Product\Delivery_Return::delivery_return_data();
			}

			if( $item == 'product-share' ) {
				$args = \Ecomus\WooCommerce\Single_Product\Share::product_share_data();
			}

			get_template_part( 'template-parts/modals/' . $item, '', $args );
		}
	}

	/**
	 * Panel items
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function panel_items() {
		$items = apply_filters( 'ecomus_panel_items', (array) \Ecomus\Theme::get_prop( 'panels' ) );

		if ( empty( $items ) ) {
			return;
		}

		foreach ( $items as $item ) {
			$args = array();

			get_template_part( 'template-parts/panels/' . $item, '', $args );
		}
	}

	/**
	 * Popover items
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function popover_items() {
		$items = apply_filters( 'ecomus_popover_items', (array) \Ecomus\Theme::get_prop( 'popovers' ) );

		if ( empty( $items ) ) {
			return;
		}

		foreach ( $items as $item ) {
			$args = array();

			if( $item == 'mobile-orderby' ) {
				$args = \Ecomus\WooCommerce\Catalog::orderby_list();
			}

			get_template_part( 'template-parts/popover/' . $item, '', $args );
		}
	}

}
