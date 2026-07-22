<?php
/**
 * Page_Header functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus;

use \Ecomus\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Header initial
 *
 */
class Page_Header {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Page Header items
	 *
	 * @var $items
	 */
	protected static $items = null;


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
		add_action( 'get_the_archive_title', array( $this, 'get_archive_title' ), 30 );
		add_action( 'ecomus_after_header', array( $this, 'show_page_header' ), 99 );
	}

	/**
	 * Show page header
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function show_page_header() {
		if ( is_404() ) {
			return;
		}

		if ( ! $this->get_items() ) {
			return;
		}

		get_template_part( 'template-parts/page-header/page-header' );
	}

	/**
	 * Show page header
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_items() {
		$items = [];

		if ( intval( Helper::get_option( 'page_header' ) ) && is_page() ) {
			$items = Helper::get_option( 'page_header_els' );
		}

		self::$items = $items;

		return apply_filters( 'ecomus_get_page_header_elements', self::$items );
	}

	/**
	 * Show classes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function classes( $classes ) {
		if ( intval( Helper::get_option( 'page_header' ) ) && is_page() ) {
			$classes .= ' page-header--page';
		}

		if( ! in_array( 'title', self::get_items() ) ) {
			$classes .= ' hide-title';
		}

		echo apply_filters('ecomus_page_header_classes', $classes);
	}

	/**
	 * Show title
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function title() {
		if( ! in_array( 'title', self::get_items() ) ) {
			return;
		}

		$title = '<h1 class="page-header__title em-font-h4">' . get_the_archive_title() . '</h1>';
		echo apply_filters('ecomus_page_header_title', $title);
	}

	/**
	 * Show breadcrumb
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function breadcrumb() {
		if( in_array( 'breadcrumb', self::get_items() ) ) {
			\Ecomus\Breadcrumb::instance()->breadcrumb();
		}
	}

	public static function background_image() {
		$background_image = apply_filters('ecomus_page_header_background_image', '');
		if( $background_image ) {
			return 'style="background-image: url(' . esc_url($background_image) . ');"';
		}

		return '';
	}

	/**
	 * Show archive title
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_archive_title( $title ) {
		if ( is_search() ) {
			global $wp_query;
			if( (int) $wp_query->found_posts > 0 ) {
				$title = sprintf( 
					esc_html__( '%d Search Results for: "%s"', 'ecomus' ),
					(int) $wp_query->found_posts,
					esc_html( get_search_query() )
				);
			} else {
				$title = esc_html__( 'Search Results', 'ecomus' );
			}
		} elseif ( is_404() ) {
			$title = esc_html__( 'Page Not Found', 'ecomus' );
		} elseif ( is_page() ) {
			$title = get_the_title(\Ecomus\Helper::get_post_ID());
		} elseif ( is_home() && is_front_page() ) {
			$title = esc_html__( 'The Latest Posts', 'ecomus' );
		} elseif ( is_home() && ! is_front_page() ) {
			$title = get_the_title( intval( get_option( 'page_for_posts' ) ) );
		} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
			$current_term = get_queried_object();
			if ( $current_term && isset( $current_term->term_id ) && ( $current_term->taxonomy == 'product_cat' || $current_term->taxonomy == 'product_brand' ) ) {
				$title = $current_term->name;
			} else {
				$title = get_the_title( intval( get_option( 'woocommerce_shop_page_id' ) ) );
			}
		} elseif ( is_single() ) {
			$title = get_the_title();
		} elseif ( is_tax() || is_category() ) {
			$title = single_term_title( '', false );
		}

		return $title;
	}
}
