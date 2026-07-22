<?php
/**
 * Single Product hooks.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

use Ecomus\WooCommerce\Single_Product\Product_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Single Product
 */
class Single_Product_Summary extends Product_Base {
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
		add_filter( 'ecomus_single_product_summary_classes', array( $this, 'product_summary_class' ), 10, 1 );

		add_action( 'ecomus_woocommerce_before_single_product_summary', array( $this, 'product_summary_open' ), 1 );

		add_action( 'ecomus_woocommerce_before_single_product_summary', array(	$this, 'open_gallery_summary_wrapper' ), 1 );
		add_action( 'ecomus_woocommerce_after_single_product_summary', array( $this, 'close_gallery_summary_wrapper' ), 2 );

        add_action( 'ecomus_woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );

		add_action( 'ecomus_woocommerce_single_product_summary', array( $this, 'product_taxonomy' ), 5 );

		add_action( 'ecomus_woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );

		add_action( 'ecomus_woocommerce_single_product_summary', 'woocommerce_template_single_title', 15 );

		// Price
		add_action( 'ecomus_woocommerce_single_product_summary', array( $this, 'open_product_price' ), 19 );
		add_action( 'ecomus_woocommerce_single_product_summary', 'woocommerce_template_single_price', 20 );
		add_action( 'ecomus_woocommerce_single_product_summary', array( $this, 'close_product_price' ), 22 );

		// Format Sale Price
		add_action( 'ecomus_woocommerce_single_product_summary', array( $this, 'add_format_sale_price' ), 5 );
		add_action( 'ecomus_woocommerce_single_product_summary', array( $this, 'remove_format_sale_price' ), 60 );

		add_action( 'ecomus_woocommerce_single_product_summary', array( $this, 'short_description' ), 30 );

		// Add product countdown
		add_action( 'ecomus_woocommerce_single_product_summary', array( $this, 'product_countdown' ), 35 );

		add_action( 'ecomus_woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 40 );

		// Extra link
		add_action( 'ecomus_woocommerce_single_product_summary', array( $this, 'open_product_extra_link' ), 45 );

		if ( intval( \Ecomus\Helper::get_option( 'product_ask_question' ) ) && ! empty( \Ecomus\Helper::get_option( 'product_ask_question_form' ) ) ) {
			add_action( 'ecomus_woocommerce_single_product_summary', array( '\Ecomus\WooCommerce\Single_Product\Ask_Question', 'ask_question' ), 47 );
		}

		if ( intval( \Ecomus\Helper::get_option( 'product_delivery_return' ) ) && ! empty( \Ecomus\Helper::get_option( 'product_delivery_return_page' ) ) ) {
			add_action( 'ecomus_woocommerce_single_product_summary', array( '\Ecomus\WooCommerce\Single_Product\Delivery_Return', 'delivery_return' ), 47 );
		}

		if ( intval( \Ecomus\Helper::get_option( 'product_share' ) ) ) {
			add_action( 'ecomus_woocommerce_single_product_summary', array( '\Ecomus\WooCommerce\Single_Product\Share', 'product_share' ), 47 );
		}
		
		add_action( 'ecomus_woocommerce_single_product_summary', array( $this, 'close_product_extra_link' ), 50 );

		// Extra content
		add_action( 'ecomus_woocommerce_single_product_summary', array( $this, 'product_extra_content' ), 70 );

		add_action( 'ecomus_woocommerce_single_product_summary', array( $this, 'view_full_details_button' ), 90 );

		add_action( 'ecomus_woocommerce_after_single_product_summary', array( $this, 'product_summary_close' ), 100 );
	}

	public function product_summary_class( $classes ) {
		if ( get_option( 'ecomus_buy_now' ) == 'yes' ) {
			$classes[] = 'has-buy-now';
		}

		if( class_exists( '\WCBoost\Wishlist\Frontend') ) {
			$classes[] = 'has-wishlist';
		}

		if( class_exists( '\WCBoost\ProductsCompare\Frontend') ) {
			$classes[] = 'has-compare';
		}

		if( \Ecomus\Helper::get_option( 'disable_out_of_stock_swatch_click' ) ) {
			$classes[] = 'has-disable-outofstock-swatch-click';
		}

		return $classes;
	}

	public function view_full_details_button() {
	?>
		<a class="view-full-details-button em-button em-button-subtle em-font-semibold" href="<?php echo esc_url( get_permalink() ); ?>">
			<span class="ecomus-button-text"><?php esc_html_e( 'View full details', 'ecomus' ); ?></span>
			<?php echo \Ecomus\Icon::get_svg( 'arrow-top' ); ?>
		</a>
	<?php
    }

	public function product_summary_open() {
		add_action( 'woocommerce_before_add_to_cart_quantity', array( $this, 'quantity_label' ), 5 );
		add_action( 'woocommerce_product_thumbnails', array( $this, 'product_gallery_thumbnails' ), 20 );
	}

	public function product_summary_close() {
		remove_action( 'woocommerce_before_add_to_cart_quantity', array( $this, 'quantity_label' ), 5 );
		remove_action( 'woocommerce_product_thumbnails', array( $this, 'product_gallery_thumbnails' ), 20 );
	}
}
