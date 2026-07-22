<?php
/**
 * Header Main functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus\Header;

use Ecomus\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Header mobile initial
 *
 */
class Mobile {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * header layout
	 *
	 * @var $instance
	 */
	protected static $header_layout = null;

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
	}

	/**
	 * Get the header.
	 *
	 * @return string
	 */
	public function render() {
		$layout = self::get_layout();

		if ( 'custom' != $layout ) {
			$this->prebuild( $layout );
		} else {
			$options = array();

			// Header main.
			$sections = array(
				'left'   => Helper::get_option( 'header_mobile_main_left' ),
				'center' => Helper::get_option( 'header_mobile_main_center' ),
				'right'  => Helper::get_option( 'header_mobile_main_right' ),
			);

			$classes = array( 'header-mobile-main', 'header-mobile-contents' );

			if( Helper::get_option( 'header_mobile_sticky' ) && Helper::get_option( 'header_mobile_sticky_el' ) !== 'header_bottom' ) {
				$classes[] = 'header-mobile-sticky';
			}

			$this->contents( $sections, $options, array( 'class' => $classes ) );

			// Header bottom.
			$sections = array(
				'left'   => Helper::get_option( 'header_mobile_bottom_left' ),
				'center' => Helper::get_option( 'header_mobile_bottom_center' ),
				'right'  => Helper::get_option( 'header_mobile_bottom_right' ),
			);

			if( Helper::get_option( 'header_mobile_sticky' ) && Helper::get_option( 'header_mobile_sticky_el' ) !== 'header_main' ) {
				$classes[] = 'header-mobile-sticky';
			}

			$classes = array( 'header-mobile-bottom', 'header-mobile-contents' );

			$this->contents( $sections, $options, array( 'class' => $classes ) );
		}
	}

	/**
	 * Get the header layout.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function get_layout() {
		if( isset( self::$header_layout )  ) {
			return self::$header_layout;
		}

		$present = Helper::get_option( 'header_mobile_present' );

		if ( $present ) {
			self::$header_layout = 'prebuild' == $present ? Helper::get_option( 'header_mobile_version' ) : 'custom';
		} else {
			self::$header_layout = 'v1';
		}

		self::$header_layout = apply_filters( 'ecomus_get_header_mobile_layout', self::$header_layout );

		return self::$header_layout;
	}

	/**
	 * Display pre-build header
	 *
	 * @param string $version
	 */
	public function prebuild( $version = 'v1' ) {
		$sections = $this->get_prebuild( $version );

		$classes = array( 'header-mobile-main', 'header-mobile-contents' );

		if( Helper::get_option( 'header_mobile_sticky' ) && Helper::get_option( 'header_mobile_sticky_el' ) !== 'header_bottom' ) {
			$classes[] = 'header-mobile-sticky';
		}

		$this->contents( $sections['main'], $sections['main_options'], array( 'class' => $classes ) );

		$classes = array( 'header-mobile-bottom', 'header-mobile-contents' );

		if( Helper::get_option( 'header_mobile_sticky' ) && Helper::get_option( 'header_mobile_sticky_el' ) !== 'header_main' ) {
			$classes[] = 'header-mobile-sticky';
		}

		$this->contents( $sections['bottom'], $sections['bottom_options'], array( 'class' => $classes ) );
	}

	/**
	 * Display pre-build header
	 *
	 * @param string $version
	 */
	public function get_prebuild( $version = 'v1' ) {
		switch ( $version ) {
			case 'v1':
				$main_sections   = array(
					'left'   => array(
						array( 'item' => 'hamburger' ),
					),
					'center' =>  array(
						array( 'item' => 'logo' ),
					),
					'right'  => $this->get_header_items(array('search', 'account', 'wishlist','compare', 'cart'))
				);

				$main_options = array(
					'account' => array (
						'account_display'    	=> 'icon',
					),
					'compare' => array (
						'compare_display'    	=> 'icon',
					),
					'wishlist' => array (
						'wishlist_display'    	=> 'icon',
					),
				);
				$bottom_sections = array();
				$bottom_options = array();
				break;
			case 'v2':
				$main_sections   = array(
					'left'   => array(
						array( 'item' => 'hamburger' ),
					),
					'center' =>  array(
						array( 'item' => 'logo' ),
					),
				'right'  => $this->get_header_items(array('search', 'account', 'wishlist','compare', 'cart'))
				);

				$main_options = array(
					'account' => array (
						'account_display'    	=> 'icon',
					),
					'compare' => array (
						'compare_display'    	=> 'icon',
					),
					'wishlist' => array (
						'wishlist_display'    	=> 'icon',
					),
				);

				$bottom_sections = array(
					'left'   => array(),
					'center' =>  array(
						array( 'item' => 'secondary-menu' ),
					),
					'right'  => array(),
				);
				$bottom_options = array();
				break;
			default:
				$main_sections   = array();
				$main_options = array();
				$bottom_sections = array();
				$bottom_options = array();
				break;
		}

		return apply_filters( 'ecomus_prebuild_header_mobile', array( 'main' => $main_sections, 'main_options' => $main_options, 'bottom' => $bottom_sections, 'bottom_options' => $bottom_options ), $version );
	}

	/**
	 * Display header attributes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_header_items( $atts = array('search') ) {
		$items = array();
		foreach( $atts as $item ) {
			if( 'logo' === $item ) {
				$items[] =	array( 'item' => 'logo' );
			}
			$key = str_replace( '-', '_', $item );
			if( Helper::get_option('header_mobile_prebuild_' . $key) ) {
				$items[] =	array( 'item' => $item );
			}
		}

		return $items;
	}

	/**
	 * Display header items
	 *
	 * @since 1.0.0
	 *
	 * @param string $sections, $atts
	 */
	public function contents( $sections, $options, $atts = array() ) {
		if ( false == array_filter( $sections ) ) {
			return;
		}

		$classes = array();
		if ( isset( $atts['class'] ) ) {
			$classes = (array) $atts['class'];
			unset( $atts['class'] );
		}

		if ( empty( $sections['left'] ) && empty( $sections['right'] ) ) {
			unset( $sections['left'] );
			unset( $sections['right'] );
		}

		if ( ! empty( $sections['center'] ) ) {
			$classes[]    = 'has-center';

			if ( empty( $sections['left'] ) && empty( $sections['right'] ) ) {
				$classes[] = 'no-sides';
			}
		} else {
			$classes[] = 'no-center';
			unset( $sections['center'] );

			if ( empty( $sections['left'] ) ) {
				unset( $sections['left'] );
			}

			if ( empty( $sections['right'] ) ) {
				unset( $sections['right'] );
			}
		}

		$attr = 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';

		foreach ( $atts as $name => $value ) {
			$attr .= ' ' . $name . '="' . esc_attr( $value ) . '"';
		}
		?>
		<div <?php echo ! empty( $attr ) ? $attr : ''; ?>>
			<div class="site-header__container em-container">
				<?php foreach ( $sections as $section => $items ) : ?>
					<?php
					$class      = '';
					$item_names = wp_list_pluck( $items, 'item' );

					if ( in_array( 'primary-menu', $item_names ) ) {
						$class = 'has-menu';
					}
					?>

					<div class="header-<?php echo esc_attr( $section ); ?>-items header-items <?php echo esc_attr( $class ) ?>">
						<?php $this->items( $items, $options ); ?>
					</div>

				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Display header items
	 *
	 * @since 1.0.0
	 *
	 * @param array $items
	 * @param array $options
	 */
	public function items( $items, $options ) {
		if ( empty( $items ) ) {
			return;
		}

		foreach ( $items as $item ) {
			$item['item'] = $item['item'] ? $item['item'] : key( \Ecomus\Options::header_mobile_items_option() );
			$template_file = $item['item'];
			$args = array();
			$load_file = true;

			switch ( $item['item'] ) {
				case 'logo':
					$args = $this->logo_options( $options );
					break;
				case 'hamburger':
					\Ecomus\Theme::set_prop( 'panels', 'hamburger' );
					break;
				case 'search':
					$template_file = 'search-icon';
					\Ecomus\Theme::set_prop( 'modals', 'search' );
					break;
				case 'cart':
					\Ecomus\Theme::set_prop( 'panels', 'cart' );
					break;
				case 'account':
					if( empty( $options ) || empty( $options['account'] ) ) {
						$options['account'] = array (
							'account_display'    	=> Helper::get_option( 'header_mobile_account_display' ),
						);
					}
					$args = \Ecomus\Header\Main::account_options( $options );
					if( function_exists('is_account_page') && is_account_page() ) {
						break;
					}
					if( ! is_user_logged_in() ) {
						\Ecomus\Theme::set_prop( 'modals', 'login' );
					} else {
						\Ecomus\Theme::set_prop( 'panels', 'account' );
					}
					break;
				case 'compare':
					if( ! class_exists('\WCBoost\ProductsCompare\Frontend') ) {
						break;
					}
					if( empty( $options ) || empty( $options['compare'] ) ) {
						$options['compare'] = array (
							'compare_display'    	=> Helper::get_option( 'header_mobile_compare_display' ),
						);
					}
					$args = \Ecomus\Header\Main::compare_options( $options );
					break;

				case 'wishlist':
					if ( ! class_exists( '\WCBoost\Wishlist\Helper' ) ) {
						break;
					}
					if( empty( $options ) || empty( $options['wishlist'] ) ) {
						$options['wishlist'] = array (
							'wishlist_display'    	=> Helper::get_option( 'header_mobile_wishlist_display' ),
						);
					}
					$args = \Ecomus\Header\Main::wishlist_options( $options );
					break;
			}

			if ( $template_file && ! empty( $load_file )) {
				get_template_part( 'template-parts/header/' . $template_file, '', $args );
			}
		}
	}

	/**
	 * Logo options
	 *
	 * @since 1.0.0
	 *
	 * @param array $options
	 * @return array $args
	 */

	public function logo_options( $options ) {
		$options = isset( $options['logo'] ) ? $options['logo'] : '';
		$args = array();
		$mobile_type = Helper::get_option( 'mobile_logo_type' );
		$mobile_type = $mobile_type != 'default' ? $mobile_type : \Ecomus\Helper::get_option('logo_type');
		$args['type'] = ! empty( $options ) && isset( $options['type'] ) ? $options['type'] : $mobile_type;
		$args['type'] = apply_filters( 'ecomus_header_logo_type', $args['type'] );
		$args['title'] = false;
		$args['logo_light'] = ! empty( $options ) && isset( $options['logo_light'] ) ? $options['logo_light'] : Helper::get_option( 'mobile_logo_image_light' );
		$mobile_logo = '';
		switch($args['type']) {
			case 'text':
				$mobile_logo = Helper::get_option( 'mobile_logo_text' );
				break;
			case 'image':
				$mobile_logo = Helper::get_option( 'mobile_logo_image' );
				break;
			case 'svg':
				$mobile_logo = Helper::get_option( 'mobile_logo_svg' );
				break;
			default:
				break;
		}

		if( ! empty( $mobile_logo ) ) {
			$args['logo'] = $mobile_logo;
		}
		return $args;
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
	public static function mobile_menu_items() {
		$items = (array) Helper::get_option('header_mobile_menu_els');

		if ( empty( $items ) ) {
			return;
		}

		foreach ( $items as  $item ) {
			if( empty( $item ) ) {
				continue;
			}

			switch ( $item ) {
				case 'primary-menu':
					$args = [
						'theme_location' => 'mobile-menu',
						'container_class' => ' hambuger-navigation'
					];

					get_template_part( 'template-parts/header/primary-menu', '', $args );
					break;

				case 'custom-link':
					if ( ! empty( Helper::get_option('header_mobile_menu_link_text') ) ) {
						echo '<div class="header-mobile-menu__custom-link"><a class="em-button-subtle em-font-medium" href="'. esc_url( Helper::get_option('header_mobile_menu_link') ) .'">'. esc_html( Helper::get_option('header_mobile_menu_link_text') ) .'</a></div>';
					}
					break;

				case 'custom-text':
					if ( ! empty( Helper::get_option('header_mobile_menu_custom_text') ) ) {
						echo '<div class="header-mobile-menu__custom-text">'. do_shortcode( wp_kses_post( Helper::get_option( 'header_mobile_menu_custom_text' ) ) ) .'</div>';
					}
					break;

				default:
					do_action( 'ecomus_mobile_menu_items', $item );
					break;
			}
		}
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
	public static function currency_language() {
		$items = (array) Helper::get_option('header_mobile_menu_els');

		if ( ! in_array( 'currency-language', $items ) ) {
			return;
		}

		echo '<div class="header-mobile-menu__currency-language">';
			echo '<div class="header-mobile-menu__currency ecomus-currency ecomus-currency-language" data-toggle="popover" data-target="currency-popover" data-device="mobile">';
				echo \Ecomus\WooCommerce\Currency::currency_switcher();
			echo '</div>';
			echo '<div class="header-mobile-menu__language ecomus-language ecomus-currency-language" data-toggle="popover" data-target="language-popover" data-device="mobile">';
				echo \Ecomus\WooCommerce\Language::language_switcher();
			echo '</div>';
		echo '</div>';
	}
}