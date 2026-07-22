<?php
/**
 * Search AJAX template hooks.
 *
 * @package Ecomus
 */

namespace Ecomus\Header;
use Ecomus\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Header Search Form template.
 */
class Search {
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

	}

	public static function get_trending() {
		if( ! Helper::get_option('header_search_trending') ) {
			return;
		}

		$trending_searches = (array) apply_filters( 'ecomus_search_trending', Helper::get_option( 'header_search_links' ) );

		if ( empty( $trending_searches ) ) {
			return;
		}

		?>
		<div class="header-search__trending em-col em-md-2">
			<h6 class="header-search__suggestion-label em-font-medium"><?php esc_html_e( 'Quick links', 'ecomus' ); ?></h6>

			<ul class="header-search__trending-links">
				<?php
				foreach ( $trending_searches as $trending_search ) {
					$url = $trending_search['url'];
					printf(
						'<li><a href="%s">%s</a></li>',
						esc_url( $trending_search['url'] ),
						esc_html( $trending_search['text'] )
					);
				}
				?>
			</ul>
		</div>
		<?php

	}

	public static function get_products() {
		$rm_filter = 'remove_filter';
		add_filter('ecomus_product_card_layout', array(__CLASS__, 'product_card_layout'));
		add_filter('ecomus_mobile_product_columns', array(__CLASS__, 'mobile_product_columns'));
		add_action( 'woocommerce_before_shop_loop_item', array(__CLASS__, 'remove_attributes' ), 1 );
		self::products();
		$rm_filter('ecomus_product_card_layout', array(__CLASS__, 'product_card_layout'));
		$rm_filter('ecomus_mobile_product_columns', array(__CLASS__, 'mobile_product_columns'));
	}

	public static function product_card_layout() {
		return '1';
	}

	public static function mobile_product_columns() {
		return '1';
	}

	public static function remove_attributes() {
		remove_action( 'woocommerce_after_shop_loop_item', array( \Ecomus\WooCommerce\Loop\Product_Attribute::instance(), 'product_attribute' ), 10 );
	}

	public static function get_products_ids() {
		if( ! Helper::get_option('header_search_products') ) {
			return;
		}

		if ( ! class_exists( 'WC_Shortcode_Products' ) ) {
            return;
        }

        $limit = Helper::get_option( 'header_search_product_limit' );
        $type  = Helper::get_option( 'header_search_products_type' );

        if('none' == $type){
            return;
        }

        $atts = array(
            'per_page'     => intval( $limit ),
        );

        switch ( $type ) {
            case 'sale_products':
            case 'top_rated_products':
				$atts['orderby'] =  'title';
				$atts['order'] =  'ASC';
                break;

			case 'featured_products':
            case 'recent_products':
				$atts['orderby'] =  'date';
				$atts['order'] =  'DESC';
                break;
        }

        $args  = new \WC_Shortcode_Products( $atts, $type );
        $args  = $args->get_query_args();
        $query = new \WP_Query( $args );

		return $query->posts;
	}

	public static function products() {
		$products_ids = self::get_products_ids();

		if( empty($products_ids) || ! count( $products_ids ) ) {
            return;
        }
		$col = 'em-md-10';
		if( ! Helper::get_option('header_search_trending') ) {
			$col = 'em-md-12';
		}
		?>
       <div class="header-search__products em-col <?php echo esc_attr( $col ); ?>">
	   <h6 class="header-search__suggestion-label em-font-medium"><?php esc_html_e( 'Need some inspiration?', 'ecomus' ); ?></h6>
			<?php
			wc_setup_loop(
				array(
					'columns' => Helper::get_option( 'header_search_type' ) !== 'popup' ? Helper::get_option( 'header_search_product_limit' ) : 4,
				)
			);
			self::get_template_loop( $products_ids );
			?>
        </div>
		<?php
	}

	/**
	 * Loop over products
	 *
	 * @since 1.0.0
	 *
	 * @param string
	 */
	public static function get_template_loop( $products_ids, $template = 'product' ) {
		if( empty( $products_ids ) ) {
			return;
		}
		update_meta_cache( 'post', $products_ids );
		update_object_term_cache( $products_ids, 'product' );

		$original_post = $GLOBALS['post'];

		woocommerce_product_loop_start();

		foreach ( $products_ids as $product_id ) {
			$GLOBALS['post'] = get_post( $product_id ); // WPCS: override ok.
			setup_postdata( $GLOBALS['post'] );
			wc_get_template_part( 'content', $template );
		}

		$GLOBALS['post'] = $original_post; // WPCS: override ok.

		woocommerce_product_loop_end();

		wp_reset_postdata();
		wc_reset_loop();
	}

	public static function products_suggest() {
		$products_ids = (array) self::get_products_ids();

		if( ! count( $products_ids ) ) {
            return;
        }

		?>
       <ul class="search-products-suggest-list list-unstyled">
			<?php
				foreach( $products_ids as $id ) {
					$_product = wc_get_product( $id );
					$price    = $_product->get_price_html();
					$image_id = get_post_thumbnail_id( $id );
					?>
						<li class="em-flex">
							<div class="suggest-list__image">
								<a class="suggest-list__link em-ratio" href="<?php echo esc_url( get_permalink( $id )); ?>">
									<img src="<?php echo esc_url( wp_get_attachment_url( $image_id ) ); ?>" alt="<?php echo esc_attr( $_product->get_title() ); ?>"/>
								</a>
							</div>
							<div class="suggest-list__content">
								<div class="suggest-list__title">
									<a href="<?php echo esc_url( get_permalink( $id )); ?>">
										<?php echo esc_html( $_product->get_title() ); ?>
									</a>
								</div>
								<div class="suggest-list__price"><?php echo wp_kses_post( $price ); ?></div>
							</div>
						</li>
					<?php
				}
			?>
        </ul>
		<?php
	}
}
