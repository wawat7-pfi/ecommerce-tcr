<?php
/**
 * Hooks of Products Recently Viewed.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce\Single_Product;

use \Ecomus\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Products Recently Viewed template.
 */
class Recently_Viewed {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private $product_ids;

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
		$viewed_products   = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
		$this->product_ids = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );

		// Track Product View
		add_action( 'template_redirect', array( $this, 'track_product_view' ) );

		if( intval( Helper::get_option( 'recently_viewed_products') ) ) {
			add_action( 'woocommerce_after_single_product_summary', array( $this, 'recently_viewed_products' ), 30 );
		}
	}

	/**
	 * Track product views
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function track_product_view() {
		global $post;

		if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) ) {
			$viewed_products = array();
		} else {
			$viewed_products = (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] );
		}

		if ( ! empty( $post->ID ) && ! in_array( $post->ID, $viewed_products ) ) {
			$viewed_products[] = $post->ID;
		}

		if ( sizeof( $viewed_products ) > 15 ) {
			array_shift( $viewed_products );
		}

		// Store for session only
		wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ), time() + 60 * 60 * 24 * 30 );
	}

	/**
	 * Get product content AJAX
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function get_recently_viewed_products_heading() {
		echo '<h2 class="recently-viewed-products__title">'. esc_html__( 'Recently Viewed', 'ecomus' ) .'</h2>';
	}

	/**
	 * Recently Viewed Products
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function recently_viewed_products() {
		if( empty( self::get_product_recently_viewed_ids( get_the_ID() ) ) ) {
			return;
		}

		$columns = \Ecomus\Helper::get_option( 'recently_viewed_products_columns', [] );
		$desktop_columns = isset( $columns['desktop'] ) ? $columns['desktop'] : '4';
		$tablet_columns  = isset( $columns['tablet'] ) ? $columns['tablet'] : '3';
		$mobile_columns  = isset( $columns['mobile'] ) ? $columns['mobile'] : '2';

		$data_columns = [
			'desktop' 	=> $desktop_columns,
			'tablet' 	=> $tablet_columns,
			'mobile' 	=> $mobile_columns
		];

		?>
		<section class="recently-viewed-products <?php echo intval( Helper::get_option( 'recently_viewed_products_ajax' ) ) ? 'has-ajax' : ''; ?>" data-columns=<?php echo json_encode( $data_columns ); ?> data-product-id="<?php echo esc_attr( get_the_ID() ); ?>">
			<?php
				self::get_recently_viewed_products_heading();
				if( ! intval( Helper::get_option( 'recently_viewed_products_ajax' ) ) ) {
					self::get_recently_viewed_products();
				}
			?>
		</section>
		<?php
	}

	/**
	 * Get recently viewed ids
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_product_recently_viewed_ids( $exclude_id = 0 ) {
		$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
		$ids = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );

		if ( $exclude_id ) {
			$ids = array_values( array_diff( $ids, array( absint( $exclude_id ) ) ) );
		}

		return $ids;
	}

	/**
	 * Get products recently viewed
	 *
	 * @return void
	 *
	 */
	public static function get_recently_viewed_products( $settings = null, $exclude_id = null ) {
		if ( is_null( $exclude_id ) ) {
			$exclude_id = get_the_ID();
		}

		$products_ids = self::get_product_recently_viewed_ids( $exclude_id );

		$stock_mode = Helper::get_option( 'recently_viewed_products_stock' );
		if ( ! empty( $products_ids ) && in_array( $stock_mode, array( 'hide', 'end' ), true ) ) {
			$products     = array_filter( array_map( 'wc_get_product', $products_ids ) );
			$products     = Helper::apply_stock_display( $products, $stock_mode );
			$products_ids = array_values( array_map( function( $product ) {
				return $product->get_id();
			}, $products ) );
		}

		$columns = \Ecomus\Helper::get_option( 'recently_viewed_products_columns', [] );
		$columns = isset( $columns['desktop'] ) ? $columns['desktop'] : '4';

		if( ! empty( $settings ) ) {
			$limit = $settings['limit'];
			$column = $settings['columns'];
		} else {
			$limit = Helper::get_option( 'recently_viewed_products_numbers' );
			$column = $columns;
		}

		if ( empty( $products_ids ) ) {
			?>
				<div class="no-products">
					<p><?php echo esc_html__( 'No products in recent viewing history.', 'ecomus' ) ?></p>
				</div>

			<?php
		} else {
			update_meta_cache( 'post', $products_ids );
			update_object_term_cache( $products_ids, 'product' );

			$original_post = $GLOBALS['post'];

			wc_setup_loop(
				array(
					'columns' => $column
				)
			);

			woocommerce_product_loop_start();

			$index = 1;

			foreach ( $products_ids as $product_id ) {
				if ( $index > intval( $limit ) ) {
					break;
				}

				$product_id = apply_filters('wpml_object_id', $product_id, 'product', true);
				if (empty($product_id)) {
					continue;
				}

				$index ++;

				$product = get_post( $product_id );
				if ( empty( $product ) ) {
					continue;
				}

				$GLOBALS['post'] = $product; // WPCS: override ok.
				setup_postdata( $GLOBALS['post'] );
				wc_get_template_part( 'content', 'product' );
			}

			$GLOBALS['post'] = $original_post; // WPCS: override ok.

			woocommerce_product_loop_end();

			wp_reset_postdata();
			wc_reset_loop();
		}
	}
}
