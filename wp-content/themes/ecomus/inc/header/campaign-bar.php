<?php
/**
 * Campaign Bar functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus\Header;

use Ecomus\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Campaign Bar initial
 *
 */
class Campaign_Bar {
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
	 * Display campaign bar item.
	 *
	 * @since 1.0.0
	 *
	 * @param array $items
	 */
	public static function campaign_items( $items, $classes = '' ) {
		if( empty( $items) ) {
			return;
		}

		foreach ( $items as $id => $item ) {
			$args = wp_parse_args( $item, array(
				'text'   => '',
				'link'   => '#',
			) );

			$args = apply_filters( 'ecomus_campaign_item_args', $args, $id );

			echo '<div class="campaign-bar__item'. esc_attr( $classes ) .'">';
				echo ! empty( $args['link'] ) ? '<a class="campaign-bar__box" href="'. esc_url( $args['link'] ) .'">' : '<div class="campaign-bar__box">';
				if ( $args['text'] ) {
					echo '<div class="campaign-bar__text em-relative">'. wp_kses_post( $args['text'] ) . '</div>';
				}
				echo ! empty( $args['link'] ) ? '</a>' : '</div>';
			echo '</div>';
		}
	}

}
