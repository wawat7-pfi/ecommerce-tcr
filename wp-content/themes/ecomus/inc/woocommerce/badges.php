<?php
/**
 * Badges hooks.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

use Ecomus\Helper, Ecomus\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Badges
 */
class Badges {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Request-level cache for sale percent (avoid repeated lookups).
	 *
	 * @var array
	 */
	protected static $request_cache = array();

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
		add_filter( 'ecomus_woocommerce_badges_class', array( $this, 'woocommerce_badges_class' ), 10, 2 );
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash' );
		add_action( 'ecomus_product_before_loop_thumbnail', array( $this, 'badges' ), 2 );

		// Single product
		add_action( 'woocommerce_single_product_summary', array( $this, 'single_badges' ), 6 );
		add_action( 'ecomus_woocommerce_product_quickview_summary', array( $this, 'single_badges' ), 17 );

		// For product loop primary
		add_action( 'ecomus_product_loop_primary_thumbnail', array( $this, 'badges' ), 2 );

		// Add badges for data product
		add_filter( 'woocommerce_available_variation', array( $this, 'data_variation_badges' ), 10, 3 );
	}

	/**
	 * Product badges class
	 *
	 * @return void
	 */
	public function woocommerce_badges_class( $classes ) {
		if( Helper::get_option( 'badges_soldout_position' ) == 'center' ) {
			$classes .= ' sold-out--center';
		}

		if( in_array( \Ecomus\WooCommerce\Helper::get_product_card_layout(), ['3', '4'] ) ) {
			$classes .= ' woocommerce-badges--right';
		}

		return $classes;
	}

	/**
	 * Product badges.
	 */
	public static function badges( $product = null, $classes = '', $args = array() ) {
		if( empty( $product ) ) {
			global $product;
		}

		$badges = array();
		$custom_badges       = maybe_unserialize( get_post_meta( $product->get_id(), 'custom_badges_text', true ) );
		if ( $custom_badges && $product->get_stock_status() !== 'outofstock' ) {
			$style    = '';
			$custom_badges_bg    = get_post_meta( $product->get_id(), 'custom_badges_bg', true );
			$custom_badges_color = get_post_meta( $product->get_id(), 'custom_badges_color', true );
			$bg_color = ! empty( $custom_badges_bg ) ? '--id--badge-custom-bg:' . $custom_badges_bg . ';' : '';
			$color    = ! empty( $custom_badges_color ) ? '--id--badge-custom-color:' . $custom_badges_color . ';' : '';

			if ( $bg_color || $color ) {
				$style = 'style="' . $color . $bg_color . '"';
			}

			$badges['html']['custom'] = '<span class="custom woocommerce-badge"' . $style . '>' . esc_html( $custom_badges ) . '</span>';
		} else {
			$badges = self::get_badges( $product, $args );
		}

		if ( ! empty( $badges['html'] ) && ! empty( implode( '',$badges['html'] ) ) ) {
			$classes = ! empty( $badges['class'] ) ? $badges['class'] : $classes;
			printf( '<div class="woocommerce-badges %s">%s</div>', esc_attr( apply_filters( 'ecomus_woocommerce_badges_class', $classes ) ), implode( '', $badges['html'] ) );
		}

		$countdown = self::get_countdown( $args );
		echo ! empty( $countdown ) ? $countdown : '';
	}

	/**
	 * Single product badges.
	 */
	public static function single_badges( $product, $args = array(), $classes = 'woocommerce-badges--single woocommerce-badges--single-primary' ) {
		if( empty( $product ) ) {
			global $product;
		}
		$args = wp_parse_args(
			$args,
			array(
				'badges_sale'           => Helper::get_option( 'product_badges_sale' ),
				'badges_sale_type'      => Helper::get_option( 'product_badges_sale_type' ),
				'badges_new'            => Helper::get_option( 'product_badges_new' ),
				'badges_featured'       => Helper::get_option( 'product_badges_featured' ),
				'badges_in_stock'       => Helper::get_option( 'product_badges_stock' ),
				'badges_soldout'        => Helper::get_option( 'product_badges_stock' ),
				'badges_pre_order'      => Helper::get_option( 'product_badges_stock' ),
				'is_single'	            => true,
				'badges_sale_countdown' => false,
			)
		);

		self::badges( $product, $classes, $args );
	}

	/**
	 * Get product badges.
	 *
	 * @return array
	 */
	public static function get_badges( $product = array(), $args = array() ) {
		if( empty( $product ) ) {
			global $product;
		}

		$args = wp_parse_args(
			$args,
			array(
				'badges_soldout'        => Helper::get_option( 'badges_soldout' ),
				'badges_soldout_text'   => esc_html__( 'Sold out', 'ecomus' ),
				'badges_sale'           => Helper::get_option( 'badges_sale' ),
				'badges_sale_type'      => Helper::get_option( 'badges_sale_type' ),
				'badges_sale_text'      => esc_html__( 'Sale', 'ecomus' ),
				'badges_new'            => Helper::get_option( 'badges_new' ),
				'badges_new_text'       => esc_html__( 'New', 'ecomus' ),
				'badges_featured'       => Helper::get_option( 'badges_featured' ),
				'badges_featured_text'  => esc_html__( 'Hot', 'ecomus' ),
				'badges_pre_order'      => Helper::get_option( 'badges_pre_order' ),
				'badges_pre_order_text' => esc_html__( 'Pre-Order', 'ecomus' ),
			)
		);

		$badges = array();
		$badges['class'] = $badges['countdown'] = '';

		if ( $args['badges_soldout'] && $product->get_stock_status() == 'outofstock' ) {
			$text               = ! empty( $args['badges_soldout_text'] ) ? $args['badges_soldout_text'] : esc_html__( 'Out Of Stock', 'ecomus' );
			$badges['html']['sold-out'] = '<div class="stock-badge"><p class="stock sold-out woocommerce-badge">' . esc_html( $text ) . '</p></div>';

			if( ! empty( $args['is_single'] ) ) {
				$badges['class']    = 'woocommerce-badges--single woocommerce-badges--single-primary sold-out';
			} else {
				$badges['class']    = 'sold-out';
			}

		} else {
			if( $args['badges_pre_order'] && self::is_pre_order_active( $product ) && ($product->get_type() === 'simple' || $product->get_type() === 'variation') ) {
				$text          = empty( $text ) ? esc_html__( 'Pre-Order', 'ecomus' ) : $text;
				$badges['html']['pre_order'] = '<div class="stock-badge"><p class="stock pre-order  woocommerce-badge">' . esc_html( $text ) . '</p></div>';

			} else {
				if( $product->is_in_stock() && ! empty( $args['badges_in_stock'] ) && ! $product->is_on_backorder() && $product->is_purchasable() ) {
					$product_availability = $product ? $product->get_availability() : '';
					$text = $product_availability && !empty( $product_availability['availability'] ) ? $product_availability['availability'] : esc_html__( 'In Stock', 'ecomus' );
					$badges['html']['in_stock'] = '<div class="stock-badge"><p class="stock in-stock woocommerce-badge">' . $text . '</p></div>';
				}
			}


			if ( $product->is_on_sale() && $args['badges_sale'] ) {
				$badges['html']['sale'] = self::sale_flash( $product, $args );

				$classes = 'ecomus-badges-sale__countdown';
				$classes .= Helper::get_option( 'product_card_attribute_second' ) !== 'none' && ! empty( $product->get_attribute( esc_attr( Helper::get_option( 'product_card_attribute_second' ) ) ) ) ? ' ecomus-badges-sale__attribute-second' : '';

			}

			else if ( $args['badges_new'] && in_array( $product->get_id(), WooCommerce\General::ecomus_woocommerce_get_new_product_ids() ) ) {
				$text          = $args['badges_new_text'];
				$text          = empty( $text ) ? esc_html__( 'New', 'ecomus' ) : $text;
				$badges['html']['new'] = '<span class="new woocommerce-badge">' . esc_html( $text ) . '</span>';
			}

			else if ( $product->is_featured() && $args['badges_featured'] ) {
				$text               = $args['badges_featured_text'];
				$text               = empty( $text ) ? esc_html__( 'Hot', 'ecomus' ) : $text;
				$badges['html']['featured'] = '<span class="featured woocommerce-badge">' . esc_html( $text ) . '</span>';
			}

			if( $args['badges_pre_order'] && self::is_pre_order_active( $product ) && $product->get_type() === 'variable' ) {
				if( empty( $badges['html'] ) )				 {
					$text          = empty( $text ) ? esc_html__( 'Pre-Order', 'ecomus' ) : $text;
					$badges['html']['pre_order'] = '<div class="stock-badge"><p class="stock pre-order  woocommerce-badge">' . esc_html( $text ) . '</p></div>';
				}
			}

			if ( $args['badges_pre_order'] && ! \Ecomus\Helper::is_pre_order_module_active() && $product->is_on_backorder() && empty( $badges['html']['pre_order'] ) ) {
				$text          = $args['badges_pre_order_text'];
				$text          = empty( $text ) ? esc_html__( 'Pre-Order', 'ecomus' ) : $text;
				$badges['html']['pre_order'] = '<div class="stock-badge"><p class="stock pre-order  woocommerce-badge">' . esc_html( $text ) . '</p></div>';
			}
		}


		$badges = apply_filters( 'ecomus_product_badges', $badges, $product );

		return $badges;
	}

	/**
	 * Sale badge.
	 *
	 * @param string $output  The sale flash HTML.
	 * @param object $post    The post object.
	 * @param object $product The product object.
	 *
	 * @return string
	 */
	public static function sale_flash( $product, $args ) {
		if ( 'grouped' == $product->get_type() ) {
			return '';
		}
		$output = '';
		$type       = $args[ 'badges_sale_type' ];
		$text       =  ! empty( $args['badges_sale_text'] ) ? $args['badges_sale_text'] : esc_html__( 'Sale', 'ecomus' );
		$percentage = 0;

		if ( 'percent' == $type || false !== strpos( $text, '{%}' ) || false !== strpos( $text, '{$}' ) ) {
			//$percentage = self::get_product_discount_percent( $product );
		}

		if ( 'percent' == $type ) {
			if( $percentage >= 1 ) {
				$output = '<span class="onsale woocommerce-badge">-' . $percentage . '%</span>';
			}
		} else {
			$output = '<span class="onsale woocommerce-badge">' . wp_kses_post( $text ) . '</span>';
		}

		return $output;
	}

	public static function sale_flash_marquee( $product ) {
		$percent = self::get_product_discount_percent( $product );
		?>

		<div class="ecomus-sale-flash-marquee ecomus-elementor--marquee em-absolute hidden-xs hidden-sm hidden-md" data-speed="<?php echo esc_attr( Helper::get_option( 'sale_display_flash_sale_speed' ) ); ?>">
			<div class="ecomus-marquee__inner ecomus-marquee--inner">
				<div class="ecomus-marquee__items ecomus-marquee--items ecomus-marquee--original" data-id="<?php echo esc_attr( $product->get_id() ); ?>">
					<div class="ecomus-marquee__item em-flex em-flex-align-center">
						<?php echo \Ecomus\Icon::get_svg( 'flash-sale' ); ?>
						<div class="ecomus-marquee__text">
							<?php echo esc_html__( 'Flash Sale', 'ecomus' ); ?>
							<span><?php echo sprintf( esc_html__( '%d%% Off', 'ecomus' ), $percent ); ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * Meta key for storing sale percent (saved on product save).
	 */
	const SALE_PERCENT_META_KEY = '_ecomus_sale_percent';

	/**
	 * Get sale percent for badges. Product card uses parent meta (max); single uses calculated per variation.
	 *
	 * @param \WC_Product $product Product or variation.
	 * @return int Sale percent.
	 */
	public static function get_product_discount_percent( $product ) {
		$product_id   = $product->get_id();
		$is_variation = $product->is_type( 'variation' );

		if ( $is_variation ) {
			return Admin\Product_Settings::calculate_product_discount_percent( $product );
		}

		if ( isset( self::$request_cache[ $product_id ] ) ) {
			return self::$request_cache[ $product_id ];
		}

		$percentage = get_post_meta( $product_id, self::SALE_PERCENT_META_KEY, true );

		if ( '' !== $percentage && is_numeric( $percentage ) ) {
			$percentage = (int) $percentage;
			$percentage = Admin\Product_Settings::validate_sale_percent_meta( $product, $percentage );
			self::$request_cache[ $product_id ] = $percentage;
			return $percentage;
		}

		$percentage = Admin\Product_Settings::calculate_and_maybe_populate_sale_percent( $product );
		self::$request_cache[ $product_id ] = $percentage;
		return $percentage;
	}

	/**
	 * Get sold of product deal
	 */
	public static function get_sold() {
		global $product;

		$limit = get_post_meta( $product->get_id(), '_deal_quantity', true );
		$sold  = intval( get_post_meta( $product->get_id(), '_deal_sales_counts', true ) );

		$output = ! empty( $limit ) ? '(' . esc_html( $sold ) . '/' . esc_html( $limit ) . ' ' . esc_html__( 'sold', 'ecomus' ) . ')' : '';

		return $output;
	}

	public static function get_countdown($args) {
		global $product;

		if ( isset( $args['badges_sale_countdown'] ) || ! Helper::get_option( 'badges_sale_countdown' ) ) {
			return;
		}

		if (  Helper::get_option( 'product_card_sale_display_type' ) == 'countdown' ) {
			if ( $product && $product->is_on_sale() ) {
				$classes = 'ecomus-badges-sale__countdown';
				$classes .= Helper::get_option( 'product_card_attribute_second' ) !== 'none' && ! empty( $product->get_attribute( esc_attr( Helper::get_option( 'product_card_attribute_second' ) ) ) ) ? ' ecomus-badges-sale__attribute-second' : '';
	
				return \Ecomus\WooCommerce\Helper::get_product_countdown( '', '', $classes, $product );
			}
		} else {
			if ( $product && $product->is_on_sale() && $product->get_stock_status() !== 'outofstock'  ) {
				return self::sale_flash_marquee( $product );
			}
		}
	}

	public function data_variation_badges( $data, $parent, $variation ) {
		ob_start();
		$this->single_badges( $variation );
		$badges_html = ob_get_clean();
		$data['badges_html'] = $badges_html;
		return $data;
	}

	public static function is_pre_order_active($product = false) {
		if( $product === false ) {
			global $product;
		}
		if( class_exists( 'YITH_Pre_Order' ) ) {
			if( $product->is_type('simple') || $product->is_type('variation') ) {
				if( get_post_meta($product->get_id(),'_ywpo_preorder', true) === 'yes' ) {
					return true;
				}
			} elseif( $product->is_type('variable') ) {
				$variations = $product->get_children();
				foreach( $variations as $variation_id ) {
					if( get_post_meta($variation_id,'_ywpo_preorder', true) === 'yes' ) {
						return true;
					}
				}
			}
		} elseif( get_option( 'ecomus_pre_order' ) === 'yes' ) {
			if( $product->is_type('simple') || $product->is_type('variation') ) {
				if( get_post_meta($product->get_id(),'_pre_order_status', true) === 'yes' ) {
					return true;
				}
			} elseif( $product->is_type('variable') ) {
				$variations = $product->get_children();
				foreach( $variations as $variation_id ) {
					if( get_post_meta($variation_id,'_pre_order_status', true) === 'yes' ) {
						return true;
					}
				}
			}
		}

		return false;
	}
}
