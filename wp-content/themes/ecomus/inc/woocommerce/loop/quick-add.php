<?php
/**
 * Hooks of QuickAdd.
 *
 * @package Ecomus
 */

 namespace Ecomus\WooCommerce\Loop;

use \Ecomus\Helper;
use Ecomus\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of QuickAdd template.
 */
class Quick_Add extends \Ecomus\WooCommerce\Single_Product\Product_Base {
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
		add_action( 'wp_enqueue_scripts', array( $this, 'quick_add_scripts' ), 20 );
		add_filter( 'ecomus_wp_script_data', array( $this, 'quickadd_script_data' ), 10, 3 );

		add_filter('woocommerce_product_add_to_cart_text', array( $this, 'product_add_to_cart_text' ), 20, 2);

		// Quick add modal.
		add_action( 'wc_ajax_product_quick_add', array( $this, 'quick_add' ) );

		// Thumbnail
		add_action( 'ecomus_woocommerce_product_quickadd_summary_header', array( $this, 'product_thumbnail' ), 5 );

		// Title
		add_action( 'ecomus_woocommerce_product_quickadd_summary_header', array( $this, 'open_product_summary' ), 9 );
		add_action( 'ecomus_woocommerce_product_quickadd_summary_header', array( $this, 'product_title' ), 10 );

		// Price
		add_action( 'ecomus_woocommerce_before_product_quickadd_summary', array( $this, 'add_format_sale_price' ), 5 );
		add_action( 'ecomus_woocommerce_product_quickadd_summary_header', 'woocommerce_template_single_price', 25 );
		add_action( 'ecomus_woocommerce_after_product_quickadd_summary', array( $this, 'remove_format_sale_price' ), 60 );

		// Price
		add_action('ecomus_woocommerce_product_quickadd_summary_header', array( $this, 'open_product_price' ), 24 );
		add_action('ecomus_woocommerce_product_quickadd_summary_header', array( $this, 'close_product_price' ), 27 );

		add_action( 'ecomus_woocommerce_product_quickadd_summary_header', array( $this, 'close_product_summary' ), 40 );

		// Add to cart
		add_action( 'ecomus_woocommerce_product_quickadd_summary', 'woocommerce_template_single_add_to_cart', 50 );

		// Featured button
		if ( ! is_singular( 'product' ) && ! Helper::get_option( 'product_card_quick_view' ) ) {
			// Position columns of product group
			add_action( 'woocommerce_grouped_product_columns', array( $this, 'grouped_product_columns' ), 10, 2 );

			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'open_product_featured_buttons' ), 20 );
			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'close_product_featured_buttons' ), 22 );
			add_filter( 'woocommerce_get_availability', array( $this, 'change_text_stock' ), 1, 2 );

			// Add data sale date
			add_action( 'woocommerce_single_variation', array( $this, 'data_product_variable' ) );

			// Change add to cart text
			add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'product_single_add_to_cart_text' ) );
		}
	}

	public function product_add_to_cart_text($button_text, $product) {
		if( ! Helper::get_option( 'product_card_quickadd' ) ) {
			return $button_text;
		}

		if( $product && $product->get_type() == 'variable' ) {
			$product_card = \Ecomus\WooCommerce\Helper::get_product_card_layout();
			$button_text = $product_card == '4' ? esc_html__( 'Quick Add', 'ecomus' ) : esc_html__( 'Quick Shop', 'ecomus' );

			\Ecomus\Theme::set_prop( 'modals', 'quickadd' );
		}

		return $button_text;
	}

	/**
	 * WooCommerce specific scripts & stylesheets.
	 *
	 * @return void
	 */
	public function quick_add_scripts() {
		wp_enqueue_script( 'ecomus-countdown',  get_template_directory_uri() . '/assets/js/plugins/jquery.countdown.js', array(), '1.0' );
		if ( wp_script_is( 'wc-add-to-cart-variation', 'registered' ) ) {
			wp_enqueue_script( 'wc-add-to-cart-variation' );
		}
	}

	/**
	 * Quickadd script data.
	 *
	 * @since 1.0.0
	 *
	 * @param $data
	 *
	 * @return array
	 */
	public function quickadd_script_data( $data ) {
		$data['product_card_quickadd'] = Helper::get_option( 'product_card_quickadd' );
		$data['product_quickadd_nonce'] = wp_create_nonce( 'ecomus-product-quickadd' );

		return $data;
	}

	/**
	 * Product quick add template.
	 *
	 * @return string
	 */
	public function quick_add() {
		if ( empty( $_POST['product_id'] ) ) {
			wp_send_json_error( esc_html__( 'No product.', 'ecomus' ) );
			exit;
		}

		$post_object = get_post( $_POST['product_id'] );
		if ( ! $post_object || ! in_array( $post_object->post_type, array( 'product', 'product_variation', true ) ) ) {
			wp_send_json_error( esc_html__( 'Invalid product.', 'ecomus' ) );
			exit;
		}

		$GLOBALS['post'] = $post_object;
		wc_setup_product_data( $post_object );
		ob_start();
		wc_get_template( 'content-product-quickadd.php', array(
			'post_object'      => $post_object,
		) );
		wp_reset_postdata();
		wc_setup_product_data( $GLOBALS['post'] );
		$output = ob_get_clean();

		wp_send_json_success( $output );
		exit;
	}

	/**
	 * Product thumbnail
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_thumbnail() {
		echo '<div class="product-thumbnail">';
			woocommerce_template_loop_product_thumbnail();
		echo '</div>';
	}

	/**
	 * Open product summary
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_product_summary() {
		echo '<div class="product-summary">';
	}

	/**
	 * Close product summary
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_product_summary() {
		echo '</div>';
	}

	/**
	 * Product title
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_title() {
		the_title( '<h3 class="product_title entry-title"><a href="' . esc_url( get_permalink() ) .'">', '</a></h3>' );
	}

}
