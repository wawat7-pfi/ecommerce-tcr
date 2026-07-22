<?php
/**
 * Navigation bar functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus\Mobile;

use Ecomus\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Woocommerce initial
 *
 */
class Navigation_bar {
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
		add_filter( 'body_class', array( $this, 'body_classes' ) );

		add_filter( 'ecomus_after_site', array( $this, 'navigation_bar' ), 0 );
		add_filter( 'ecomus_footer', array( $this, 'navigation_shop_menu' ) );
	}

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Classes for the body element.
	 *
	 * @return array
	 */
	public function body_classes( $classes ) {
		$classes[] = 'ecomus-navigation-bar-show';

		return $classes;
	}

	/**
	 * Displays header content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function navigation_bar() {
		$items = apply_filters( 'ecomus_mobile_navigation_bar_items', (array) Helper::get_option( 'mobile_navigation_bar_items' ) );

		if ( ! $items ) {
			return;
		}

		?>
        <div id="ecomus-mobile-navigation-bar" class="ecomus-mobile-navigation-bar">
			<?php $this->navigation_bar_template_item( $items ); ?>
        </div>
		<?php
	}

	/**
	 * Display header items
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function navigation_bar_template_item( $items ) {
		foreach ( $items as $item ) {

		    if( empty($item) ) {
		        continue;
            }

			$template_file = $item;

			switch ( $item ) {
				case 'search':
					\Ecomus\Theme::set_prop( 'panels', 'search' );
					break;
				case 'cart':
					\Ecomus\Theme::set_prop( 'panels', 'cart' );
					break;
				case 'wishlist':
					if ( ! function_exists( 'wcboost_wishlist' ) ) {
						$template_file = '';
						break;
					}
					break;
				case 'compare':
					if ( ! function_exists( 'wcboost_products_compare' ) ) {
						$template_file = '';
						break;
					}
					break;
				case 'account':
					if( function_exists('is_account_page') && is_account_page() ) {
						$template_file = '';
						break;
					}
					if( ! is_user_logged_in() ) {
						\Ecomus\Theme::set_prop( 'modals', 'login' );
					} else {
						\Ecomus\Theme::set_prop( 'panels', 'account' );
					}
					break;
				case 'filter':
					if ( ! apply_filters( 'ecomus_navigation_bar_filter_elementor', \Ecomus\Helper::is_catalog() ) ) {
						$template_file = '';
						break;
					}

					if ( apply_filters( 'ecomus_navigation_bar_filter_sidebar_elementor', true ) ) {
						\Ecomus\Theme::set_prop( 'panels', 'filter-sidebar' );
					}
					break;
			}

			if ( $template_file ) {
				echo '<div class="ecomus-mobile-navigation-bar__item em-flex em-flex-center">';
				get_template_part( 'template-parts/navigation-bar/' . $template_file );
				echo '</div>';
			}
		}
	}

	public function navigation_shop_menu() {
		if( ! in_array( 'shop', ( array ) Helper::get_option( 'mobile_navigation_bar_items' ) ) ) {
			return;
		}

		\Ecomus\Theme::set_prop( 'panels', 'shop' );
	}
}
