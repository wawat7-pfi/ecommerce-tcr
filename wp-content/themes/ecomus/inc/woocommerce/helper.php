<?php
/**
 * Woocommerce Setup functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Woocommerce initial
 *
 */
class Helper {

	/**
	 * Get product countdown
	 */
	public static function get_product_countdown( $sale = '', $text = '', $classes = '', $product = null, $expire_date = null ) {
		if( empty( $product ) ) {
			global $product;
		}

		$now         = strtotime( current_time( 'Y-m-d H:i:s' ) );
		$expire_date = $expire_date == null ? self::get_date_on_sale_to( $product ) : $expire_date;
		$expire_date = ! empty( $expire_date ) ? date_i18n( $expire_date ) : '';
		$expire      = ! empty( $expire_date ) ? $expire_date - $now : -1;
		$expire = apply_filters( 'ecomus_countdown_product_second', $expire );

		if ( empty( $sale ) ) {
			$sale = array(
				'weeks'   => esc_html__( 'w', 'ecomus' ),
				'days'    => esc_html__( 'd', 'ecomus' ),
				'hours'   => esc_html__( 'h', 'ecomus' ),
				'minutes' => esc_html__( 'm', 'ecomus' ),
				'seconds' => esc_html__( 's', 'ecomus' ),
			);
		}

		if ( $text ) {
			$text = '<div class="em-product-countdown__text">'. $text .'</div>';
		}

		$classes = empty( $classes ) ? 'em-product-countdown__countdown' : $classes;

		if( is_product() && $product->is_type( 'variable' ) ) {
			$classes .= ' hidden';
		}

		if ( empty( $expire ) || $expire < 0 ) {
			return;
		}

		$days = floor($expire / (60 * 60 * 24));
		$hours = str_pad(floor(($expire % (60 * 60 * 24)) / (60 * 60)), 2, '0', STR_PAD_LEFT);
		$minutes = str_pad(floor(($expire % (60 * 60)) / (60)), 2, '0', STR_PAD_LEFT);
		$seconds = str_pad(floor($expire % 60), 2, '0', STR_PAD_LEFT);

		return sprintf( '<div class="em-product-countdown %s">
							%s
							<div class="ecomus-countdown" data-expire="%s" data-text="%s">
								<span class="days timer">
									<span class="digits">%s</span>
									<span class="text">%s</span>
									<span class="divider">:</span>
								</span>
								<span class="hours timer">
									<span class="digits">%s</span>
									<span class="text">%s</span>
									<span class="divider">:</span>
								</span>
								<span class="minutes timer">
									<span class="digits">%s</span>
									<span class="text">%s</span>
									<span class="divider">:</span>
								</span>
								<span class="seconds timer">
									<span class="digits">%s</span>
									<span class="text">%s</span>
								</span>
							</div>
						</div>',
					! empty( $classes ) ? esc_attr( $classes ) : '',
					$text,
					esc_attr( $expire ),
					esc_attr( wp_json_encode( $sale ) ),
					esc_html( $days ),
					$sale['days'],
					esc_html( $hours ),
					$sale['hours'],
					esc_html( $minutes ),
					$sale['minutes'],
					esc_html( $seconds ),
					$sale['seconds']
				);

	}

	public static function get_date_on_sale_to( $product, $args = array() ) {
		$sale_date = get_post_meta( $product->get_id(), '_sale_price_dates_to', true );

		if( ! $product->is_type( 'variable' ) ) {
			return $sale_date;
		}

		$variation_ids = $product->get_visible_children();

		if( empty( $variation_ids ) ) {
			return $sale_date;
		}

		$sale_dates = array();
		foreach( $variation_ids as $variation_id ) {
			$variation = wc_get_product( $variation_id );

			if ( $variation->is_on_sale() ) {
				$date_on_sale_to   = $variation->get_date_on_sale_to();

				if( ! empty($date_on_sale_to) ) {
					$sale_dates[] = $date_on_sale_to;
				}
			}
		}

		if( ! empty( $sale_dates ) ) {
			$sale_date = strtotime( min( $sale_dates ) );
		}

		$sale_date = apply_filters( 'ecomus_product_sale_dates_to', $sale_date );

		return $sale_date;
	}

	protected static $product_card = null;
	public static function get_product_card_layout() {
		if( isset( self::$product_card )  ) {
			return apply_filters( 'ecomus_product_card_layout', self::$product_card);
		}

		$catalog_view = apply_filters( 'ecomus_catalog_view_layout', self::catalog_view_layout() );

		self::$product_card = $catalog_view ? $catalog_view : \Ecomus\Helper::get_option( 'product_card_layout' );

		return apply_filters( 'ecomus_product_card_layout', self::$product_card);
	}

	public static function catalog_view_layout(  ) {
		$layout = '';
		$catalog_view = \Ecomus\WooCommerce\Catalog::catalog_toolbar_default_view();

		if ( $catalog_view == 'list' && \Ecomus\Helper::is_catalog() ) {
			$layout = 'list';
		}
		return $layout;
	}

	public static function product_card_layout_select() {
		return apply_filters( 'ecomus_product_card_layout_select', array(
			'1' => esc_html__( 'Layout v1', 'ecomus' ),
			'2' => esc_html__( 'Layout v2', 'ecomus' ),
			'3' => esc_html__( 'Layout v3', 'ecomus' ),
			'4' => esc_html__( 'Layout v4', 'ecomus' ),
			'5' => esc_html__( 'Layout v5', 'ecomus' ),
			'6' => esc_html__( 'Layout v6', 'ecomus' ),
			'7' => esc_html__( 'Layout v7', 'ecomus' ),
			'8' => esc_html__( 'Layout v8', 'ecomus' ),
			'9' => esc_html__( 'Layout v9', 'ecomus' ),
		) );
	}

	public static function get_product_taxonomy( $taxonomy = 'product_cat', $product = false ) {
		if( ! $product ) {
			global $product;
		}

		if( empty($taxonomy ) ) {
			return false;
		}

		$terms = wc_get_product_terms(
			$product->get_id(),
			$taxonomy,
			apply_filters(
				'woocommerce_breadcrumb_product_terms_args',
				array(
					'orderby' => 'parent',
					'order'   => 'DESC',
				)
			)
		);
		if( !is_wp_error( $terms ) && !empty($terms) ) {
			return $terms;
		}

		return false;
	}


}