<?php
/**
 * Catalog Header hooks.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

use \Ecomus\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Catalog Header
 */

class Catalog_Header {
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
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter('ecomus_header_logo_type', array( $this, 'get_header_logo_type' ));
		add_filter('ecomus_header_logo', array( $this, 'get_header_logo' ), 20, 2);
		add_filter('ecomus_header_logo_light', array( $this, 'get_header_logo_light' ), 20, 2);
	}

	/**
	 * Add 'woocommerce-active' class to the body tag.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $classes CSS classes applied to the body tag.
	 *
	 * @return array $classes modified to include 'woocommerce-active' class.
	 */
	public function body_class( $classes ) {
		if( ! empty( Helper::get_option('shop_header_background') ) ) {
			if ( ! in_array( 'header-transparent', $classes, true ) ) {
				$classes[] = 'header-transparent';
			}

			if( ! empty( Helper::get_option('shop_header_text_color') ) ) {

				$classes = array_filter( $classes, function ( $class ) {
					return strpos( $class, 'header-transparent-text-' ) !== 0;
				});

				$classes[] = 'header-transparent-text-' . esc_attr( Helper::get_option('shop_header_text_color') );
			}
		}

		return $classes;
	}

	public function get_header_logo_type($logo_type) {
		if( ! empty( Helper::get_option('shop_header_logo_type') ) ) {
			$logo_type = Helper::get_option('shop_header_logo_type');
		}

		return $logo_type;
	}

	public function get_header_logo($logo, $logo_type) {
		if ( 'text' == $logo_type ) {
			if( ! empty( Helper::get_option('shop_header_logo_text') ) ) {
				$logo = Helper::get_option('shop_header_logo_text');
			}
		} elseif ( 'svg' == $logo_type ) {
			if( ! empty( Helper::get_option('shop_header_logo_svg') )) {
				$logo = Helper::get_option('shop_header_logo_svg');
			}

		} elseif ( 'image' == $logo_type ) {
			if( ! empty( Helper::get_option('shop_header_logo_image') )) {
				$logo = Helper::get_option('shop_header_logo_image');
			}
		}

		return $logo;
	}

	public function get_header_logo_light($logo, $logo_type) {
		if ( 'image' == $logo_type ) {
			if( ! empty( Helper::get_option('shop_header_logo_image_light') )) {
				$logo = Helper::get_option('shop_header_logo_image_light');
			}
		}

		return $logo;
	}
}