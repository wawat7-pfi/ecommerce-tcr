<?php
/**
 * Ecomus Blog Header functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

use Ecomus\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ecomus Post
 *
 */
class Shop_Header {

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
		add_action('ecomus_page_header_content', array( $this, 'description' ), 20);

		add_filter('ecomus_page_header_classes', array( $this, 'classes' ));
		add_filter('ecomus_get_page_header_elements', array( $this, 'elements' ));
	}

	/**
	 * Get description
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function description( $description ) {
		$item_desc = in_array( 'description', \Ecomus\Page_Header::get_items() );
		$item_desc = apply_filters('ecomus_catalog_page_header_description', $item_desc);
		if( ! $item_desc ) {
			return;
		}

		self::description_content( $description );
	}

	/**
	 * Description content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function description_content( $description = '' ) {
		if ( empty( $description ) ) {
			ob_start();
			if ( function_exists('is_shop') && is_shop() ) {
				woocommerce_product_archive_description();
			}
			$description = ob_get_clean();

			if ( is_tax() ) {
				$term = get_queried_object();
				if ( $term ) {
					$description = $term->description;
				}
			}
		}

		$alignment = 'center';
		$number_lines = \Ecomus\Helper::get_option( 'shop_header_number_lines' );

		if ( intval( Helper::get_option( 'taxonomy_description_enable' ) ) && Helper::get_option( 'taxonomy_description_position' ) == 'below' ) {
			$alignment = \Ecomus\Helper::get_option( 'taxonomy_description_alignment' );
			$number_lines = \Ecomus\Helper::get_option( 'taxonomy_description_number_lines' );
		}

		if( $description ) {
			echo '<div class="page-header__description shop-header__description text-'. esc_attr( $alignment ) .'" style="--em-shop-header-description-lines: '. esc_attr( $number_lines ) .'">';
				$option = array(
					'more'   => esc_html__( 'Show More', 'ecomus' ),
					'less'   => esc_html__( 'Show Less', 'ecomus' )
				);

				echo sprintf('<div class="shop-header__content">%s</div>', wpautop( do_shortcode( $description ) ));
				echo sprintf('
					<button class="shop-header__more em-button-subtle show hidden" data-settings="%s">%s</button>',
					htmlspecialchars(json_encode( $option )),
					esc_html__('Show More', 'ecomus')
				);
			echo '</div>';
		}
	}

	/**
	 * Page Header Classes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function classes( $classes ) {
		$classes .= ' page-header--shop';

		return $classes;
	}

	/**
	 * Page Header Elements
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function elements( $items ) {
		$items = \Ecomus\Helper::get_option('shop_header') ? (array) \Ecomus\Helper::get_option( 'shop_header_els' ) : [];

		return apply_filters('ecomus_shop_header_elements', $items);
	}
}
