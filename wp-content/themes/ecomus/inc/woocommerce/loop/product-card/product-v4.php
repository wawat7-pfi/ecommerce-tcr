<?php
/**
 * Product Card v4 hooks.
 *
 * @package Ecomus
 */

 namespace Ecomus\WooCommerce\Loop\Product_Card;

use Ecomus\Helper;
use Ecomus\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product Card v4
 */
class Product_V4 extends Base {
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
		add_action( 'ecomus_product_loop_thumbnail_4', array( $this, 'product_featured_icons_open' ), 3 );
		add_action( 'ecomus_product_loop_thumbnail_4', 'woocommerce_template_loop_add_to_cart', 3 );
		add_action( 'ecomus_product_loop_thumbnail_4', array( $this, 'product_featured_icons_close' ), 3 );

		add_action( 'ecomus_product_loop_thumbnail_4', array( $this, 'product_featured_icons_top_open' ), 5 );

		add_action( 'ecomus_product_loop_thumbnail_4', array( $this, 'product_featured_icons_close' ), 40 );

		add_action( 'ecomus_after_shop_loop_item_4', array( $this, 'product_add_to_cart_mobile' ), 30 );
	}

}
