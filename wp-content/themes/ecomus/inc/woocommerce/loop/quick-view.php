<?php
/**
 * Hooks of QuickView.
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
 * Class of QuickView template.
 */
class Quick_View extends \Ecomus\WooCommerce\Single_Product\Product_Base {
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
		add_action( 'wp_enqueue_scripts', array( $this, 'quick_view_scripts' ), 20 );
		add_filter( 'ecomus_wp_script_data', array( $this, 'quickview_script_data' ), 10, 3 );

		// Product Card Quick View
		add_action( 'woocommerce_before_shop_loop_item', array( $this, 'render_product_card' ), 0 );

		// Quick view AJAX.
		add_action( 'wc_ajax_product_quick_view', array( $this, 'quick_view' ) );

		// Gallery
		add_action( 'ecomus_woocommerce_before_product_quickview_summary', 'woocommerce_show_product_images', 10 );

		// Taxonomy and Brand
		add_action( 'ecomus_woocommerce_product_quickview_summary', array( $this, 'product_taxonomy' ), 5 );

		// Summary
		add_action( 'ecomus_woocommerce_product_quickview_summary', 'woocommerce_template_single_rating', 10 );
		add_action( 'ecomus_woocommerce_product_quickview_summary', array( $this, 'product_title' ), 15 );

		// Price
		add_action('ecomus_woocommerce_product_quickview_summary', array( $this, 'open_product_price' ), 24 );
		add_action('ecomus_woocommerce_product_quickview_summary', array( $this, 'close_product_price' ), 27 );

		// Price
		add_action( 'ecomus_woocommerce_product_quickview_summary', array( $this, 'add_format_sale_price' ), 5 );
		add_action( 'ecomus_woocommerce_product_quickview_summary', 'woocommerce_template_single_price', 25 );
		add_action( 'ecomus_woocommerce_product_quickview_summary', array( $this, 'remove_format_sale_price' ), 60 );

		// Description
		add_action( 'ecomus_woocommerce_product_quickview_summary', array( $this, 'short_description' ), 30 );

		add_action( 'ecomus_woocommerce_product_quickview_summary', 'woocommerce_template_single_add_to_cart', 50 );

		// Featured button
		if ( ! is_singular( 'product' )) {
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

		// Button view full details
		add_action( 'ecomus_woocommerce_product_quickview_summary', array( $this, 'view_full_details_button' ), 60 );

	}

	public function render_product_card() {
		switch ( \Ecomus\WooCommerce\Helper::get_product_card_layout() ) {
			case '1':
			case '5':
			case '6':
			case '7':
			case '8':
			case '9':
				add_action( 'ecomus_product_loop_thumbnail_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ), array( $this, 'quick_view_button_icon_light' ), 25 );
				break;

			case '2':
				add_action( 'ecomus_product_loop_thumbnail_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ), array( $this, 'quick_view_button_light' ), 25 );
				if ( Helper::get_option( 'mobile_product_card_atc' ) ) {
					add_action( 'ecomus_product_loop_thumbnail_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ), array( $this, 'quick_view_button_icon_light' ), 55 );
				}
				break;
			case '3':
				add_action( 'ecomus_product_loop_thumbnail_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ), array( $this, 'quick_view_button_icon_light' ), 55 );
				break;

			case '4':
				add_action( 'ecomus_product_loop_thumbnail_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ), array( $this, 'quick_view_button_icon_light' ), 20 );
				break;

			case 'list':
				add_action( 'ecomus_after_shop_loop_item_list', array( $this, 'quick_view_button_icon_light' ), 45 );
				break;

			default:
				break;
		}

		// For product loop primary
		add_action( 'ecomus_product_loop_primary_thumbnail', array( $this, 'quick_view_button_icon_light' ), 25 );
	}

	/**
	 * WooCommerce specific scripts & stylesheets.
	 *
	 * @return void
	 */
	public static function quick_view_scripts() {
		wp_enqueue_script( 'ecomus-countdown',  get_template_directory_uri() . '/assets/js/plugins/jquery.countdown.js', array(), '1.0' );

		if ( wp_script_is( 'wc-add-to-cart-variation', 'registered' ) ) {
			wp_enqueue_script( 'wc-add-to-cart-variation' );
		}

		if ( wp_script_is( 'flexslider', 'registered' ) ) {
			wp_enqueue_script( 'flexslider' );
		}
	}

	/**
	 * Quickview script data.
	 *
	 * @since 1.0.0
	 *
	 * @param $data
	 *
	 * @return array
	 */
	public function quickview_script_data( $data ) {
		$data['product_quickview_nonce'] = wp_create_nonce( 'ecomus-product-quickview' );

		return $data;
	}

	/**
	 * Product quick view template.
	 *
	 * @return string
	 */
	public static function quick_view() {
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
		wc_get_template( 'content-product-quickview.php', array(
			'post_object'      => $post_object,
		) );
		wp_reset_postdata();
		wc_setup_product_data( $GLOBALS['post'] );
		$output = ob_get_clean();

		wp_send_json_success( $output );
		exit;
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

	/**
	 * View full details button
	 *
	 * @return void
	 */
	public function view_full_details_button() {
	?>
		<a class="view-full-details-button em-button em-button-subtle em-font-semibold" href="<?php echo esc_url( get_permalink() ); ?>">
			<span class="ecomus-button-text"><?php esc_html_e( 'View full details', 'ecomus' ); ?></span>
			<?php echo Icon::get_svg( 'arrow-top' ); ?>
		</a>
	<?php
	}

	/**
	 *  Quick view button
	 */
	public function quick_view_button_light() {
		$classes = 'product-loop-button em-flex-align-center em-flex-center em-button-light';

		if( \Ecomus\WooCommerce\Helper::get_product_card_layout() == '2' ) {
			$classes .= ' mobile-hide-button em-hide-text-xs';
		}

		$classes = apply_filters( 'ecomus_quick_view_button_classes', $classes );

		self::quick_view_button_html( $classes );
	}

	/**
	 *  Quick view icon
	 */
	protected function quick_view_button_icon($classes = 'em-button', $product = false) {
		$classes = 'product-loop-button em-flex-align-center em-flex-center em-button-icon em-tooltip ' . $classes;

		if( \Ecomus\WooCommerce\Helper::get_product_card_layout() == '2' ) {
			$classes .= ' mobile-show-button';
		}

		$classes = apply_filters( 'ecomus_quick_view_button_icon_classes', $classes );

		self::quick_view_button_html( $classes, true, $product);
	}

	/**
	 *  Quick view icon
	 */
	public function quick_view_button_icon_dark($product = false) {
		$this->quick_view_button_icon( 'em-button em-tooltip', $product );
	}

	/**
	 *  Quick view icon
	 */
	public function quick_view_button_icon_light($product = false) {
		$this->quick_view_button_icon( 'em-button-light em-tooltip', $product );
	}

	/**
	 * Get Quick view icon
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function quick_view_button_html( $classes = '', $only_icon = false, $_product = false ) {
		global $product;

		$_product = empty( $_product  ) ? $product : $_product ;

		$content = \Ecomus\Icon::inline_svg( 'icon=eye' );
		if( ! $only_icon ) {
			$content = sprintf(
				'<span class="ecomus-button__icon">%s</span>
				<span class="ecomus-quickview-button__text">%s</span>',
				$content,
				esc_html__( 'Quick View', 'ecomus' )
			);
		}
		\Ecomus\Theme::set_prop( 'modals', 'quickview' );
		echo sprintf(
			'<a href="%s" class="ecomus-quickview-button button %s" data-toggle="modal" data-target="quick-view-modal" data-id="%d" data-tooltip="%s" aria-label="%s" rel="nofollow">
				%s
			</a>',
			is_customize_preview() ? '#' : esc_url( get_permalink() ),
			esc_attr( $classes ),
			esc_attr( $_product->get_id() ),
			esc_attr__( 'Quick View', 'ecomus' ),
			esc_attr__( 'Quick View for', 'ecomus' ) . ' ' . $_product->get_title(),
			$content
		);
	}
}
