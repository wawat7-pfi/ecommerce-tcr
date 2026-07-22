<?php
/**
 * Posts functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus\Header;

use Ecomus\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Posts initial
 *
 */
class Topbar {
	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
	}

	/**
	 * Custom template tags of header
	 *
	 * @package Ecomus
	 *
	 * @since 1.0.0
	 *
	 * @param $items
	 */
	public static function items( $items ) {
		if ( empty( $items ) ) {
			return;
		}

		$args = [];

		foreach ( $items as $item ) {
			$item['item'] = $item['item'] ? $item['item'] : key( \Ecomus\Options::topbar_items_option() );

			switch ( $item['item'] ) {
				case 'language':
					get_template_part( 'template-parts/header/language' );
					break;

				case 'currency':
					get_template_part( 'template-parts/header/currency' );
					break;

				case 'socials':
					get_template_part( 'template-parts/header/socials', '', $args );
					break;

				case 'slides':
					get_template_part( 'template-parts/header/slides' );
					break;

				case 'phone':
					if ( ! empty( \Ecomus\Helper::get_option( 'topbar_phone' ) ) ) {
						get_template_part( 'template-parts/header/phone' );
					}
					break;

				case 'email':
					if ( ! empty( \Ecomus\Helper::get_option( 'topbar_email' ) ) ) {
						get_template_part( 'template-parts/header/email' );
					}
					break;

				case 'menu':
					if ( ! empty( \Ecomus\Helper::get_option( 'topbar_menu' ) ) ) {
						wp_nav_menu( array(
							'theme_location' 	=> '__no_such_location',
							'menu'           	=> Helper::get_option('topbar_menu'),
							'container'      	=> 'nav',
							'container_id'   	=> 'topbar-menu',
							'container_class'   => 'topbar-navigation topbar-menu',
							'menu_class'     	=> 'nav-menu menu',
							'depth'          	=> 1,
						) );
					}
					break;

				case 'custom-text':
					if ( ! empty( \Ecomus\Helper::get_option( 'topbar_custom_text' ) ) ) {
						echo '<div class="topbar-custom-text em-font-medium">'. do_shortcode( Helper::get_option( 'topbar_custom_text' ) ) .'</div>';
					}
					break;

				default:
					do_action( 'ecomus_header_topbar_item', $item['item'] );
					break;
			}
		}
	}

	/**
	 * Display slides item.
	 *
	 * @since 1.0.0
	 *
	 * @param array $items
	 */
	public static function slides() {
		$slides = (array) \Ecomus\Helper::get_option( 'topbar_slides' );

		$slides = apply_filters('ecomus_topbar_slides', $slides);

		if( empty( $slides ) ) {
			return;
		}

		foreach ( $slides as $item ) {
			echo '<div class="topbar-slides__item swiper-slide">' . $item['text'] . '</div>';
		}
	}
}
