<?php
/**
 * Product Card v3 hooks.
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
 * Class of Product Card v3
 */
class Product_V3 extends Base {
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
		add_action( 'ecomus_product_loop_thumbnail_3', array( $this, 'product_featured_icons_open' ), 5 );
		add_action( 'ecomus_product_loop_thumbnail_3', 'woocommerce_template_loop_add_to_cart', 10 );
		add_action( 'ecomus_product_loop_thumbnail_3', array( $this, 'product_featured_icons_close' ), 30 );

		add_action( 'ecomus_product_loop_thumbnail_3', array( $this, 'product_featured_icons_top_open' ), 40 );


		add_action( 'ecomus_product_loop_thumbnail_3', array( $this, 'product_featured_icons_close' ), 60 );

		add_action( 'ecomus_after_shop_loop_item_3', array( $this, 'product_add_to_cart_mobile' ), 30 );
	}

}
