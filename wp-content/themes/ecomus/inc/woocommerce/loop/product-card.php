<?php
/**
 * Product Card hooks.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce\Loop;

use Ecomus\Helper, Ecomus\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product Card
 */
class Product_Card {
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
		\Ecomus\WooCommerce\Loop\Product_Card\Base::instance();
		add_action( 'woocommerce_before_shop_loop_item', array( $this, 'add_actions' ), 0 );
	}

	public function add_actions() {
		switch ( \Ecomus\WooCommerce\Helper::get_product_card_layout() ) {
			case '1':
				\Ecomus\WooCommerce\Loop\Product_Card\Product_V1::instance();
				break;
			case '2':
				\Ecomus\WooCommerce\Loop\Product_Card\Product_V2::instance();
				break;
			case '3':
				\Ecomus\WooCommerce\Loop\Product_Card\Product_V3::instance();
				break;
			case '4':
				\Ecomus\WooCommerce\Loop\Product_Card\Product_V4::instance();
				break;
			case '5':
				\Ecomus\WooCommerce\Loop\Product_Card\Product_V5::instance();
				break;
			case '6':
				\Ecomus\WooCommerce\Loop\Product_Card\Product_V6::instance();
				break;
			case '7':
				\Ecomus\WooCommerce\Loop\Product_Card\Product_V7::instance();
				break;
			case '8':
				\Ecomus\WooCommerce\Loop\Product_Card\Product_V8::instance();
				break;
			case '9':
				\Ecomus\WooCommerce\Loop\Product_Card\Product_V9::instance();
				break;
			case 'list':
				\Ecomus\WooCommerce\Loop\Product_Card\Product_List::instance();
				break;
		}
	}
}