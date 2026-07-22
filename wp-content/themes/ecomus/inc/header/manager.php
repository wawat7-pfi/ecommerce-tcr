<?php
/**
 * Blog functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus\Header;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Woocommerce initial
 *
 */
class Manager {
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
		add_action( 'template_redirect', array( $this, 'template_hooks' ) );
	}

	/**
	 * Add template hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function template_hooks() {
		add_action( 'ecomus_before_header', array( $this, 'campaign_bar' ) );

		if( \Ecomus\Helper::get_option( 'topbar' ) ) {
			add_action( 'ecomus_before_header', array( $this, 'topbar' ) );
		}

		add_action( 'ecomus_header', array( $this, 'header' ) );

		add_filter( 'ecomus_header_container_classes', array( $this, 'header_container_class' ) );
		add_filter( 'ecomus_topbar_container_classes', array( $this, 'topbar_container_class' ) );
	}

	/**
	 * Displays header content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function header() {
		$header  = \Ecomus\Header\Main::instance();
		if( ! empty($header->get_layout()) ) {
			$classes = 'header-' .  $header->get_layout();

			if( \Ecomus\Helper::get_option( 'header_sticky' ) ) {
				$classes .= ' ecomus-header-sticky';

				if( \Ecomus\Helper::get_option( 'header_sticky_el' ) === 'both' ) {
					$classes .= ' header-sticky--both';
				}
			}

			if ( $header->get_layout() == 'custom' ) {
				if ( intval( \Ecomus\Helper::get_option( 'header_main_divider' ) ) ) {
					$classes .= ' ecomus-header-main-divider';
				}

				if ( intval( \Ecomus\Helper::get_option( 'header_bottom_divider' ) ) ) {
					$classes .= ' ecomus-header-bottom-divider';
				}
			} else {
				if( in_array( $header->get_layout(), array( 'v2', 'v3', 'v6' ) ) ) {
					$classes .= ' ecomus-header-main-divider';
				}

				if( in_array( $header->get_layout(), array( 'v6' ) ) ) {
					$classes .= ' ecomus-header-bottom-divider';
				}
			}

			echo '<div class="site-header__desktop site-header__section ' . esc_attr( $classes ) . '">';
			$header->render();
			echo '</div>';
		}

		$header_mobile 	= \Ecomus\Header\Mobile::instance();
		if( ! empty($header_mobile->get_layout()) ) {
			$classes = 'header-' .  $header_mobile->get_layout();

			if( \Ecomus\Helper::get_option( 'header_mobile_sticky' ) ) {
				$classes .= ' ecomus-header-mobile-sticky';
			}

			echo '<div class="site-header__mobile site-header__section ' . esc_attr( $classes ) . '">';
			$header_mobile->render();
			echo '</div>';
		}
	}

	/**
	 * Header class container in header version
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function header_container_class( $classes ) {
		$header_full_width = intval(\Ecomus\Helper::get_option( 'header_fullwidth' ));
		$header_full_width = apply_filters( 'ecomus_header_full_width', $header_full_width);
		if ( $header_full_width ) {
			$classes .= ' em-container-fluid';
		}

		return $classes;
	}

	/**
	 * Header class container in topbar version
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function topbar_container_class( $classes ) {
		$topbar_full_width = intval(\Ecomus\Helper::get_option( 'topbar_fullwidth' ));
		$topbar_full_width = apply_filters( 'ecomus_topbar_full_width', $topbar_full_width);
		if ( $topbar_full_width ) {
			$classes .= ' em-container-fluid';
		}

		return $classes;
	}

	/**
	 * Display header campaign bar
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function campaign_bar() {
		if( ! apply_filters( 'ecomus_get_campaign_bar', \Ecomus\Helper::get_option( 'campaign_bar' ) ) ) {
			return;
		}

		$type = \Ecomus\Helper::get_option( 'campaign_bar_type' );

		$class_container = $class_items = $class_item = '';

		if ( $type == 'slides' ) {
			$class_container = ' swiper';
			$class_items = ' swiper-wrapper columns-1';
			$class_item = ' swiper-slide';
		}

		$args = apply_filters( 'ecomus_campaign_bar_elements', array(
			'items' => \Ecomus\Helper::get_option( 'campaign_items' ),
			'type' => $type,
			'speed' => \Ecomus\Helper::get_option( 'campaign_bar_speed' ),
			'class_container' => $class_container,
			'class_items' => $class_items,
			'class_item' => $class_item
		) );

		get_template_part( 'template-parts/header/campaign-bar', '', $args );
	}

	/**
	 * Display header top bar
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function topbar() {
		if( ! apply_filters( 'ecomus_get_topbar', \Ecomus\Helper::get_option( 'topbar' ) ) ) {
			return;
		}

		$items = array(
			'left_items' => (array) \Ecomus\Helper::get_option( 'topbar_left' ),
			'center_items' => (array) \Ecomus\Helper::get_option( 'topbar_center' ),
			'right_items' => (array) \Ecomus\Helper::get_option( 'topbar_right' )
		);

		get_template_part( 'template-parts/header/topbar', '', $items );
	}
}
