<?php
/**
 * Frontend functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Header initial
 *
 */
class Frontend {
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
		add_action( 'wp_head', array( $this, 'ecomus_preload_fonts' ), 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter('ecomus_get_cart_svg_icon', array( $this, 'ecomus_change_cart_svg_icon' ) );
		add_action( 'ecomus_before_site', array( $this, 'ecomus_include_svg_icons' ) );

		add_action( 'ecomus_after_site_content_open', array( $this, 'open_site_content_container' ) );
		add_action( 'ecomus_before_site_content_close', array( $this, 'close_site_content_container' ), 30 );

		add_action( 'elementor/theme/register_locations', array( $this, 'register_elementor_locations' ) );
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
		// Adds a class of hfeed to non-singular pages.
		if ( ! is_singular() ) {
			$classes[] = 'hfeed';
		}

		$classes[] = $this->content_layout();

		// Add a class of rtl background
		if ( intval( Helper::get_option( 'rtl_smart' ) ) && is_rtl() ) {
			$classes[] = 'ecomus-rtl-smart';
		}

		return $classes;
	}

	/**
	 * Add font
	 */
	public function ecomus_preload_fonts() {
		Helper::get_fonts();
	}

	/**
	 * Get site layout
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function content_layout() {
		$layout = 'no-sidebar';

		return apply_filters( 'ecomus_site_layout', $layout );
	}

	/**
	 * Print the open tags of site content container
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_site_content_container() {
		if( Helper::is_built_with_elementor() ) {
			return;
		}

		$classes = apply_filters( 'ecomus_site_content_container_class', 'em-container' );
		echo '<div class="' . esc_attr( $classes ) . ' clearfix ">';
	}

	/**
	 * Print the close tags of site content container
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_site_content_container() {
		if( Helper::is_built_with_elementor() ) {
			return;
		}

		echo '</div>';
	}


	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_register_style( 'swiper', get_template_directory_uri() . '/assets/css/plugins/swiper.min.css', array(), '8.5.4');

		$style_file = is_rtl() ? 'style-rtl.css' : 'style.css';
		wp_enqueue_style( 'ecomus', apply_filters( 'ecomus_get_style_directory_uri', get_template_directory_uri() ) . '/' . $style_file, array(
			'swiper',
		), '20260207' );

		do_action( 'ecomus_after_enqueue_style' );

		/**
		 * Register and enqueue scripts
		 */
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'swiper', get_template_directory_uri() . '/assets/js/plugins/swiper.min.js', array( 'jquery' ), '8.5.4', true );

		wp_register_script( 'headroom', get_template_directory_uri() . '/assets/js/plugins/headroom.min.js', array(), '0.9.3', true );

		if ( ( \Ecomus\Helper::get_option( 'header_sticky' ) || \Ecomus\Helper::get_option( 'header_mobile_sticky' ) ) && 'up' == \Ecomus\Helper::get_option( 'header_sticky_on' ) ) {
			wp_enqueue_script( 'headroom' );
		}

		wp_enqueue_script( 'notify', get_template_directory_uri() . '/assets/js/plugins/notify.min.js', array(), '1.0.0', true );
		wp_enqueue_script( 'ecomus', get_template_directory_uri() . "/assets/js/scripts" . $debug . ".js", array(
			'jquery',
			'swiper',
			'imagesloaded',
		), '20260207', array('strategy' => 'defer') );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		$ecomus_data = array(
			'direction'                                   => is_rtl() ? 'true' : 'false',
			'ajax_url'                                    => class_exists( 'WC_AJAX' ) ? \WC_AJAX::get_endpoint( '%%endpoint%%' ) : '',
			'admin_ajax_url' 							  => admin_url('admin-ajax.php'),
			'nonce'                                       => wp_create_nonce( '_ecomus_nonce' ),
			'header_search_products'                      => Helper::get_option( 'header_search_products' ),
			'header_search_product_limit'                 => Helper::get_option( 'header_search_product_limit' ),
			'header_sticky'                               => Helper::get_option( 'header_sticky' ),
			'header_sticky_on'                            => Helper::get_option( 'header_sticky_on' ),
			'header_mobile_sticky'                        => Helper::get_option( 'header_mobile_sticky' ),
			'header_mobile_menu_open_primary_submenus_on' => Helper::get_option( 'header_mobile_menu_open_primary_submenus_on' ),
			'product_description_lines'   				  => ! empty( Helper::get_option( 'product_description_lines' ) ) ? intval( Helper::get_option( 'product_description_lines') ) : 4,
			'product_affiliate_new_tab'   				  => Helper::get_option( 'product_affiliate_new_tab' ),
		);

		$ecomus_data = apply_filters( 'ecomus_wp_script_data', $ecomus_data );

		wp_localize_script(
			'ecomus', 'ecomusData', $ecomus_data
		);

	}

	/**
	 * Add icon list as svg after <body> tag and hide it
	 */
	public function ecomus_include_svg_icons() {
		echo '<div id="svg-defs" class="svg-defs hidden" aria-hidden="true" tabindex="-1">';
			\Ecomus\Icon::inline_icons();
		echo '</div>';
	}

	/**
	 * Cart icon
	 */
	public static function ecomus_change_cart_svg_icon( $cart_icon ) {
		if ( \Ecomus\Helper::get_option( 'cart_icon_source' ) == 'icon' ) {
			switch( \Ecomus\Helper::get_option( 'cart_icon' ) ) {
				case 'shopping-bag-2' :
					$cart_icon = '<symbol id="shopping-cart" viewBox="0 0 22 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M20.9581558,19.4649506 L20.0524052,4.49596364 C20.0027065,3.67483636 19.2900935,3.00383377 18.4611584,3.00383377 L5.50845195,3.00383377 C4.67273766,3.00383377 3.96671688,3.67189091 3.91641039,4.49596364 L3.00280519,19.4649506 C2.95268571,20.2860779 3.58315325,20.9570805 4.41138701,20.9570805 L19.5487792,20.9570805 C20.3814078,20.9570805 21.0079948,20.2890234 20.9581558,19.4649506 Z M5.03198961,18.9623065 L5.88425455,4.99865455 L18.084374,4.99865455 L18.9292987,18.9623065 L5.03198961,18.9623065 Z" fill="currentColor" fill-rule="nonzero"></path><path d="M13.9702286,6.99342857 L13.9702286,9.51207273 C13.9702286,9.65948571 13.9101506,9.96001558 13.7381455,10.2468935 C13.4523896,10.7234961 12.9369818,11.0100935 11.9754078,11.0100935 C11.0138338,11.0100935 10.498426,10.7235429 10.2126701,10.2468935 C10.0406649,9.96001558 9.98058701,9.65948571 9.98058701,9.51207273 L9.98058701,6.99342857 L7.98576623,6.99342857 L7.98576623,9.51207273 C7.98576623,9.98848831 8.1127013,10.6237714 8.50173506,11.2726597 C9.15104416,12.3556987 10.3187532,13.0049143 11.975361,13.0049143 C13.6319688,13.0049143 14.7996779,12.3556987 15.448987,11.2726597 C15.8380208,10.6237714 15.9649558,9.98848831 15.9649558,9.51207273 L15.9649558,6.99342857 L13.9702286,6.99342857 Z" fill="currentColor" fill-rule="nonzero"></path></symbol>';
					break;
				case 'shopping-cart' :
					$cart_icon = '<symbol id="shopping-cart" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M20.3470909,6.05683636 C20.1926727,5.84945455 19.9493455,5.72727273 19.6908545,5.72727273 L7.08981818,5.72727273 L6.86765455,3.72785455 C6.82161818,3.31347273 6.47138182,3 6.05449091,3 L3.87267273,3 C3.42081818,3 3.05449091,3.36632727 3.05449091,3.81818182 C3.05449091,4.27003636 3.42081818,4.63636364 3.87267273,4.63636364 L5.32216364,4.63636364 L5.54329091,6.62645455 C5.54394545,6.63338182 5.54470909,6.64025455 5.54558182,6.64712727 L6.33218182,13.7266909 C6.37821818,14.1410727 6.72850909,14.4545455 7.1454,14.4545455 L17.5692545,14.4545455 C17.9307818,14.4545455 18.2494909,14.2172182 18.3530727,13.8709091 L20.4747273,6.78 C20.5488,6.53230909 20.5014545,6.26416364 20.3470909,6.05683636 Z M16.9600364,12.8181818 L7.87772727,12.8181818 L7.27161818,7.36363636 L18.5919818,7.36363636 L16.9600364,12.8181818 Z" fill="currentColor" fill-rule="nonzero"></path><path d="M8.78176364,16.0908 C7.42832727,16.0908 6.32721818,17.1919091 6.32721818,18.5453455 C6.32721818,19.8988364 7.42832727,20.9998909 8.78176364,20.9998909 C10.1352,20.9998909 11.2363091,19.8988364 11.2363091,18.5453455 C11.2363091,17.1919091 10.1352,16.0908 8.78176364,16.0908 Z M8.78176364,19.3635273 C8.33061818,19.3635273 7.96358182,18.9965455 7.96358182,18.5453455 C7.96358182,18.0942 8.33061818,17.7271636 8.78176364,17.7271636 C9.23290909,17.7271636 9.59994545,18.0942 9.59994545,18.5453455 C9.59994545,18.9965455 9.23290909,19.3635273 8.78176364,19.3635273 Z" fill="currentColor" fill-rule="nonzero"></path><path d="M15.2726727,16.0908 C13.9191818,16.0908 12.8181273,17.1919091 12.8181273,18.5453455 C12.8181273,19.8988364 13.9191818,20.9998909 15.2726727,20.9998909 C16.6261636,20.9998909 17.7272182,19.8988364 17.7272182,18.5453455 C17.7272182,17.1919091 16.6261091,16.0908 15.2726727,16.0908 Z M15.2726727,19.3635273 C14.8215273,19.3635273 14.4544909,18.9965455 14.4544909,18.5453455 C14.4544909,18.0942 14.8215273,17.7271636 15.2726727,17.7271636 C15.7238182,17.7271636 16.0908545,18.0942 16.0908545,18.5453455 C16.0908545,18.9965455 15.7238182,19.3635273 15.2726727,19.3635273 Z" fill="currentColor" fill-rule="nonzero"></path></symbol>';
					break;
				case 'shopping-cart-2' :
					$cart_icon = '<symbol id="shopping-cart" viewBox="0 0 28 28" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M21.9353 20.0337L20.7493 8.51772C20.7003 8.0402 20.2981 7.67725 19.8181 7.67725H4.21338C3.73464 7.67725 3.33264 8.03898 3.28239 8.51523L2.06458 20.0368C1.96408 21.0424 2.29928 22.0529 2.98399 22.8097C3.66874 23.566 4.63999 24.0001 5.64897 24.0001H18.3827C19.387 24.0001 20.3492 23.5747 21.0214 22.8322C21.7031 22.081 22.0361 21.0623 21.9353 20.0337ZM19.6348 21.5748C19.3115 21.9312 18.8668 22.1275 18.3827 22.1275H5.6493C5.16836 22.1275 4.70303 21.9181 4.37252 21.553C4.042 21.1878 3.88005 20.7031 3.92749 20.2284L5.056 9.55014H18.9732L20.0724 20.2216C20.1223 20.7281 19.9666 21.2087 19.6348 21.5748Z" fill="currentColor"></path> <path d="M12.1717 0C9.21181 0 6.80365 2.40811 6.80365 5.36803V8.6138H8.67622V5.36803C8.67622 3.44053 10.2442 1.87256 12.1717 1.87256C14.0992 1.87256 15.6674 3.44053 15.6674 5.36803V8.6138H17.5397V5.36803C17.5397 2.40811 15.1316 0 12.1717 0Z" fill="currentColor"></path></symbol>';
					break;
				default:
			}
		}

		return $cart_icon;
	}

	function register_elementor_locations( $elementor_theme_manager ) {
		$elementor_theme_manager->register_all_core_location();
	}
}
