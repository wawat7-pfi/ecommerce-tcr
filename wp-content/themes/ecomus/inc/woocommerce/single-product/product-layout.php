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
class Product_Layout {
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
		\Ecomus\WooCommerce\Single_Product\Product_Base::instance();
		\Ecomus\WooCommerce\Single_Product\Related::instance();
		\Ecomus\WooCommerce\Single_Product\UpSells::instance();
		\Ecomus\WooCommerce\Single_Product\Recently_Viewed::instance();

		if ( intval( Helper::get_option( 'product_ask_question' ) ) && ! empty( Helper::get_option( 'product_ask_question_form' ) ) ) {
			\Ecomus\WooCommerce\Single_Product\Ask_Question::instance();
		}

		if ( intval( Helper::get_option( 'product_delivery_return' ) ) && ! empty( Helper::get_option( 'product_delivery_return_page' ) ) ) {
			\Ecomus\WooCommerce\Single_Product\Delivery_Return::instance();
		}

		if ( intval( Helper::get_option( 'product_share' ) ) ) {
			\Ecomus\WooCommerce\Single_Product\Share::instance();
		}

	}
}
