<?php
/**
 * Style functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

use Ecomus\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Dynamic_CSS {
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
		add_action( 'ecomus_inline_style_after_cache', array( $this, 'add_static_css' ) );
		add_filter( 'ecomus_page_header_background_image', array( $this, 'get_page_header_background_image' ) );
	}

	/**
	 * Get get style data
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function add_static_css( $parse_css ) {

		if ( ! class_exists( 'WooCommerce' ) ) {
			return $parse_css;
		}

		$parse_css .= $this->header_shop_static_css();
		$parse_css .= $this->shop_static_css();
		$parse_css .= $this->product_card_static_css();
		$parse_css .= $this->single_product_static_css();

		return $parse_css;
	}

	/**
	 * Get page header background image
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function get_page_header_background_image( $background_image ) {
		$shop_header_image = '';
		if( Helper::get_option('shop_header') && Helper::is_catalog() ) {
			if( function_exists( 'is_product_category' ) && is_product_category() ) { 
				$category_header_image_id = get_term_meta(get_queried_object()->term_id, 'em_page_header_bg_id', true);
				if( $category_header_image_id ) {
					$shop_header_image = wp_get_attachment_url( $category_header_image_id );
				}
			}

			$shop_header_image = empty( $shop_header_image ) ? Helper::get_option( 'shop_header_background_image' ) : $shop_header_image;
		}
		return $shop_header_image;
	}

	/**
	 * Get CSS code of settings for header shop.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function header_shop_static_css() {
		$static_css = '';

		if( ! Helper::is_catalog() ) {
			return $static_css;
		}

		if ( 'image' === Helper::get_option( 'shop_header_logo_type' ) ) {
			$logo_dimension = (array) Helper::get_option( 'shop_header_logo_dimension' );
			$logo_dimension = apply_filters('ecomus_header_shop_logo_dimension', $logo_dimension);
			$logo_width = ! empty($logo_dimension['width']) ? $logo_dimension['width'] : '';
			$logo_height = ! empty($logo_dimension['height']) ? $logo_dimension['height'] : '';

			$unit_width = $logo_width != 'auto' ? 'px;' : ';';
			$unit_height = $logo_height != 'auto' ? 'px;' : ';';

			$width = ! empty($logo_width) ? 'width: ' . $logo_width . $unit_width : '';
			$height = ! empty($logo_height) ? 'height: ' . $logo_height . $unit_height : '';

			if ( $width || $height ) {
				$static_css .= '.ecomus-catalog-page .header-logo > a img, .ecomus-catalog-page .header-logo > a svg {' . $width . $height . '}';
			}
		}

		return $static_css;
	}

	/**
	 * Get CSS code of settings for shop.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function shop_static_css() {
		$static_css = '';

		if( ! Helper::is_catalog() ) {
			return $static_css;
		}

		if( Helper::get_option('shop_header') && Helper::is_catalog() ) {
			if( Helper::get_option( 'shop_header_background_overlay' ) ) {
				$static_css .= '.page-header.page-header--shop::before {background-color: ' . Helper::get_option( 'shop_header_background_overlay' ) . ';}';
			}

			$category_header_text_color = '';
			if( function_exists( 'is_product_category' ) && is_product_category() ) { 
				$category_header_text_color = get_term_meta(get_queried_object()->term_id, 'em_page_header_text_color', true);
				if( $category_header_text_color ) {
					$static_css .= '.page-header.page-header--shop, .page-header.page-header--shop .page-header__title, .page-header.page-header--shop .site-breadcrumb a {color: ' . $category_header_text_color . ';}';
				}
			} 

			if( empty( $category_header_text_color ) ) {
				if ( ( $color = Helper::get_option( 'shop_header_title_color' ) ) && $color != '' ) {
					$static_css .= '.page-header.page-header--shop .page-header__title {color: ' . $color . ';}';
				}

				if ( ( $color = Helper::get_option( 'shop_header_breadcrumb_link_color' ) ) && $color != '' ) {
					$static_css .= '.page-header.page-header--shop .site-breadcrumb a {color: ' . $color . ';}';
				}

				if ( ( $color = Helper::get_option( 'shop_header_breadcrumb_color' ) ) && $color != '' ) {
					$static_css .= '.page-header.page-header--shop .site-breadcrumb {color: ' . $color . ';}';
				}

				if ( ( $color = Helper::get_option( 'shop_header_description_color' ) ) && $color != '' ) {
					$static_css .= '.page-header.page-header--shop .page-header__description {color: ' . $color . ';}';
				}
			}
		}

		return $static_css;
	}

	/**
	 * Get CSS code of settings for product card.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function product_card_static_css() {
		$static_css = '';

		// Product Badges.
		if ( ( $color = Helper::get_option( 'badges_sale_bg' ) ) !== '#FC5732' ) {
			$static_css .= '.woocommerce-badges:not(.woocommerce-badges--single) .onsale {background-color: ' . $color . '}';
		}

		if ( ( $color = Helper::get_option( 'badges_sale_text_color' ) ) !== '#ffffff' ) {
			$static_css .= '.woocommerce-badges:not(.woocommerce-badges--single) .onsale {color: ' . $color . '}';
		}

		if ( ( $color = Helper::get_option( 'badges_new_bg' ) ) !== '#48D4BB' ) {
			$static_css .= '.woocommerce-badges:not(.woocommerce-badges--single) .new {background-color: ' . $color . '}';
		}

		if ( ( $color = Helper::get_option( 'badges_new_text_color' ) ) !== '#ffffff' ) {
			$static_css .= '.woocommerce-badges:not(.woocommerce-badges--single) .new {color: ' . $color . '}';
		}

		if ( ( $color = Helper::get_option( 'badges_featured_bg' ) ) !== '#ff7316' ) {
			$static_css .= '.woocommerce-badges:not(.woocommerce-badges--single) .featured {background-color: ' . $color . '}';
		}

		if ( ( $color = Helper::get_option( 'badges_featured_text_color' ) ) !== '#ffffff' ) {
			$static_css .= '.woocommerce-badges:not(.woocommerce-badges--single) .featured {color: ' . $color . '}';
		}

		if ( ( $color = Helper::get_option( 'badges_soldout_bg' ) ) !== '#f2f2f2' ) {
			$static_css .= '.woocommerce-badges:not(.woocommerce-badges--single) .sold-out, .woocommerce-badges:not(.woocommerce-badges--single).sold-out--center.sold-out {background-color: ' . $color . '}';
		}

		if ( ( $color = Helper::get_option( 'badges_soldout_text_color' ) ) !== '#000000' ) {
			$static_css .= '.woocommerce-badges:not(.woocommerce-badges--single) .sold-out {color: ' . $color . '}';
		}

		if ( ( $color = Helper::get_option( 'badges_pre_order_bg' ) ) !== '#55a653' ) {
			$static_css .= '.woocommerce-badges:not(.woocommerce-badges--single) .pre-order {background-color: ' . $color . '}';
		}

		if ( ( $color = Helper::get_option( 'badges_pre_order_text_color' ) ) !== '#ffffff' ) {
			$static_css .= '.woocommerce-badges:not(.woocommerce-badges--single) .pre-order {color: ' . $color . '}';
		}

		$custom_badge_css = '';
		if ( ( $color = Helper::get_option( 'badges_custom_bg' ) ) ) {
			$custom_badge_css = '--id--badge-custom-bg: ' . $color  . ';';
		}

		if ( ( $color = Helper::get_option( 'badges_custom_color' ) ) ) {
			$custom_badge_css .= '--id--badge-custom-color: ' . $color . ';';
		}

		if( ! empty( $custom_badge_css ) ) {
			$static_css .= '.woocommerce-badges:not(.woocommerce-badges--single) .custom {' . $custom_badge_css . '}';
		}

		$custom_body_var = '';
		$cropping      = get_option( 'woocommerce_thumbnail_cropping', '1:1' );
		if ( 'custom' === $cropping ) {
			$width          = max( 1, get_option( 'woocommerce_thumbnail_cropping_custom_width', '4' ) );
			$height         = max( 1, get_option( 'woocommerce_thumbnail_cropping_custom_height', '3' ) );
			if( $width != $height ) {
				$ratio = $height / $width * 100;
				$custom_body_var = '--product-image-ratio-percent:' . $ratio . '%;';
			}

		}

		switch( Helper::get_option('image_rounded_shape_product_card') ) {
			case 'round':
				$custom_body_var .= '--em-image-rounded-product-card: 10px;';
				break;
			case 'custom':
				if( $number = Helper::get_option('image_rounded_number_product_card')) {
					$custom_body_var .= '--em-image-rounded-product-card:' . $number . 'px;';
				}
				break;
			default:
				$custom_body_var .= '--em-image-rounded-product-card: var(--em-image-rounded);';
				break;
		}

		switch( Helper::get_option('image_rounded_shape_product_gallery') ) {
			case 'round':
				$custom_body_var .= '--em-image-rounded-product-gallery: 10px;';
				break;
			case 'custom':
				if( $number = Helper::get_option('image_rounded_number_product_gallery')) {
					$custom_body_var .= '--em-image-rounded-product-gallery:' . $number . 'px;';
				}
				break;
			default:
				$custom_body_var .= '--em-image-rounded-product-gallery: var(--em-image-rounded);';
				break;
		}

		switch( Helper::get_option('image_rounded_shape_product_thumbnail') ) {
			case 'round':
				$custom_body_var .= '--em-image-rounded-product-thumbnail: 5px;';
				break;
			case 'custom':
				if( $number = Helper::get_option('image_rounded_number_product_thumbnail')) {
					$custom_body_var .= '--em-image-rounded-product-thumbnail:' . $number . 'px;';
				}
				break;
			default:
				$custom_body_var .= '--em-image-rounded-product-thumbnail: var(--em-image-rounded);';
				break;
		}

		if( ! empty( $custom_body_var ) ) {
			$static_css .= ':root {' . $custom_body_var . '}';
		}

		switch( Helper::get_option('featured_button_rounded_shape_product_card') ) {
			case 'circle':
				$static_css .= '.product-featured-icons .product-loop-button.em-button-icon { --em-button-rounded: 100px; }';
				break;
			case 'custom':
				if( $number = Helper::get_option('featured_button_rounded_number_product_card')) {
					$static_css .= '.product-featured-icons .product-loop-button.em-button-icon { --em-button-rounded:' . $number . 'px; }';
				}
				break;
		}

		// Product Title.
		if ( Helper::get_option( 'product_card_title_lines' ) != '' ) {
			$static_css .= 'ul.products li.product .woocommerce-loop-product__title { --em-line-clamp-count: ' . esc_attr( Helper::get_option( 'product_card_title_lines' ) ) . '; }';
		}

		return $static_css;
	}

	/**
	 * Get CSS code of settings for single product.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function single_product_static_css() {
		$static_css = '';

		if( Helper::get_option('product_description') ) {
			$static_css .= '.single-product div.product {
				--em-product-description-lines: ' . Helper::get_option('product_description_lines') . ';
			}';
		}

		return $static_css;
	}
}
