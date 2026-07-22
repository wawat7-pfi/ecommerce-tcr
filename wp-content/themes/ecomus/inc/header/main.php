<?php
/**
 * Header Main functions and definitions.
 *
 * @package Ecomus
 */

 namespace Ecomus\Header;

use Ecomus\Helper;

use function WPML\FP\Strings\replace;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Header Main initial
 *
 */
class Main {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

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
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render() {
		$layout = self::get_layout();

		if ( 'custom' != $layout ) {
			$this->prebuild( $layout );
		} else {
			$options = array();

			// Header main.
			$sections = array(
				'left'   => Helper::get_option( 'header_main_left' ),
				'center' => Helper::get_option( 'header_main_center' ),
				'right'  => Helper::get_option( 'header_main_right' ),
			);

			$classes = $this->header_classes( 'main', array( 'header-main', 'header-contents' ) );

			if( Helper::get_option( 'header_sticky' ) && Helper::get_option( 'header_sticky_el' ) !== 'header_bottom' ) {
				$classes .= ' header-sticky';
			}

			$this->contents( $sections, $options, array( 'class' => $classes ) );

			// Header bottom.
			$sections = array(
				'left'   => Helper::get_option( 'header_bottom_left' ),
				'center' => Helper::get_option( 'header_bottom_center' ),
				'right'  => Helper::get_option( 'header_bottom_right' ),
			);

			$classes = $this->header_classes( 'bottom', array( 'header-bottom', 'header-contents' ) );

			if( Helper::get_option( 'header_sticky' ) && Helper::get_option( 'header_sticky_el' ) !== 'header_main' ) {
				$classes .= ' header-sticky';
			}

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

		$present = Helper::get_option( 'header_present' );
		if ( $present ) {
			self::$header_layout = 'prebuild' == $present ? Helper::get_option( 'header_version' ) : 'custom';
		} else {
			self::$header_layout = 'v1';
		}

		self::$header_layout = apply_filters( 'ecomus_get_header_layout', self::$header_layout );

		return self::$header_layout;
	}

	/**
	 * Display pre-build header
	 *
	 * @since 1.0.0
	 *
	 * @param string $version
	 */
	public function prebuild( $version = 'v1' ) {
		$sections 		= $this->get_prebuild( $version );

		$classes = $this->header_classes( 'main', array( 'header-main', 'header-contents' ) );

		if( Helper::get_option( 'header_sticky' ) && Helper::get_option( 'header_sticky_el' ) !== 'header_bottom' ) {
			$classes .= ' header-sticky';
		}

		$this->contents( $sections['main'], $sections['main_options'], array( 'class' => $classes ) );

		$classes = $this->header_classes( 'bottom', array( 'header-bottom', 'header-contents' ) );

		if( Helper::get_option( 'header_sticky' ) && Helper::get_option( 'header_sticky_el' ) !== 'header_main' ) {
			$classes .= ' header-sticky';
		}

		$this->contents( $sections['bottom'], $sections['bottom_options'], array( 'class' => $classes ) );
	}

	/**
	 * Display pre-build header
	 *
	 * @since 1.0.0
	 *
	 * @param string $version
	 */
	public function get_prebuild( $version = 'v1' ) {
		switch ( $version ) {
			case 'v1':
				$main_sections   = array(
					'left'   => array(
						array( 'item' => 'logo' ),
					),
					'center' =>  array(
						array( 'item' => 'primary-menu' ),
					),
					'right'  => $this->get_header_items(array('search', 'account', 'compare', 'wishlist', 'cart'))
				);
				$main_options = array(
					'search' => array (
						'search_layout'    		=> 'icon',
					),
					'account' => array (
						'account_display'    	=> 'icon',
					),
					'compare' => array (
						'compare_display'    	=> 'icon',
					),
					'wishlist' => array (
						'wishlist_display'    	=> 'icon',
					),
					'cart' => array (
						'cart_size'    			=> 'medium',
						'cart_divider'    		=> false,
					),
				);
				$bottom_sections = array();
				$bottom_options = array();
				break;
			case 'v2':
				$main_sections   = array(
					'left'   => array(
						array( 'item' => 'secondary-menu' ),
					),
					'center' =>  array(
						array( 'item' => 'logo' ),
					),
					'right'  => $this->get_header_items(array('search', 'account', 'compare', 'wishlist', 'cart'))
				);
				$main_options = array(
					'search' => array (
						'search_layout'    		=> 'icon',
					),
					'account' => array (
						'account_display'    	=> 'icon',
					),
					'compare' => array (
						'compare_display'    	=> 'icon',
					),
					'wishlist' => array (
						'wishlist_display'    	=> 'icon',
					),
					'cart' => array (
						'cart_size'    			=> 'medium',
						'cart_divider'    		=> false,
					),
				);
				$bottom_sections = array(
					'left'   => array(),
					'center' =>  array(
						array( 'item' => 'primary-menu' ),
					),
					'right'  => array()
				);
				$bottom_options = array();
				break;
			case 'v3':
				$main_sections   = array(
					'left'   => array(
						array( 'item' => 'currency' ),
						array( 'item' => 'language' ),
					),
					'center' =>  array(
						array( 'item' => 'logo' ),
					),
					'right'  => $this->get_header_items(array('search', 'account', 'compare', 'wishlist', 'cart'))
				);
				$main_options = array(
					'search' => array (
						'search_layout'    		=> 'icon',
					),
					'account' => array (
						'account_display'    	=> 'icon',
					),
					'compare' => array (
						'compare_display'    	=> 'icon',
					),
					'wishlist' => array (
						'wishlist_display'    	=> 'icon',
					),
					'cart' => array (
						'cart_size'    			=> 'medium',
						'cart_divider'    		=> false,
					),
				);
				$bottom_sections = array(
					'left'   => array(),
					'center' =>  array(
						array( 'item' => 'primary-menu' ),
					),
					'right'  => array()
				);
				$bottom_options = array();
				break;
			case 'v4':
				$main_sections   = array(
					'left'   => array(
						array( 'item' => 'primary-menu' ),
					),
					'center' =>  array(
						array( 'item' => 'logo' ),
					),
					'right'  => $this->get_header_items(array('search', 'account', 'compare', 'wishlist', 'cart'))
				);
				$main_options = array(
					'search' => array (
						'search_layout'    		=> 'icon',
					),
					'account' => array (
						'account_display'    	=> 'icon',
					),
					'compare' => array (
						'compare_display'    	=> 'icon',
					),
					'wishlist' => array (
						'wishlist_display'    	=> 'icon',
					),
					'cart' => array (
						'cart_size'    			=> 'medium',
						'cart_divider'    		=> false,
					),
				);
				$bottom_sections = array();
				$bottom_options = array();
				break;
			case 'v5':
				$main_sections   = array(
					'left'   => array(
						array( 'item' => 'logo' ),
						array( 'item' => 'primary-menu' ),
					),
					'center' =>  array(),
					'right'  => $this->get_header_items(array('search', 'account', 'compare', 'wishlist', 'cart'))
				);
				$main_options = array(
					'search' => array (
						'search_layout'    		=> 'icon',
					),
					'account' => array (
						'account_display'    	=> 'icon',
					),
					'compare' => array (
						'compare_display'    	=> 'icon',
					),
					'wishlist' => array (
						'wishlist_display'    	=> 'icon',
					),
					'cart' => array (
						'cart_size'    			=> 'medium',
						'cart_divider'    		=> false,
					),
				);
				$bottom_sections = array();
				$bottom_options = array();
				break;
			case 'v6':
				$main_sections   = array(
					'left'   => array(
						array( 'item' => 'logo' ),
					),
					'center' =>  array(
						array( 'item' => 'search' ),
					),
					'right'  => $this->get_header_items(array('account', 'compare', 'wishlist', 'cart'))
				);
				$main_options = array(
					'search' => array (
						'search_layout'    		=> 'form',
					),
					'account' => array (
						'account_display'    	=> 'icon-text',
					),
					'compare' => array (
						'compare_display'    	=> 'icon-text',
					),
					'wishlist' => array (
						'wishlist_display'    	=> 'icon-text',
					),
					'cart' => array (
						'cart_size'    			=> 'large',
						'cart_divider'    		=> true,
					),
				);
				$bottom_sections = array(
					'left'   => array(
						array( 'item' => 'category-menu' ),
						array( 'item' => 'primary-menu' ),
					),
					'center' =>  array(),
					'right' =>  array(
						array( 'item' => 'support' ),
					),
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

		return apply_filters( 'ecomus_prebuild_header', array( 'main' => $main_sections, 'main_options' => $main_options, 'bottom' => $bottom_sections, 'bottom_options' => $bottom_options ), $version );
	}

	/**
	 * Display header attributes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function get_header_items( $atts = array('search') ) {
		$items = array();
		foreach( $atts as $item ) {
			if( 'logo' === $item ) {
				$items[] =	array( 'item' => 'logo' );
			}
			$key = str_replace( '-', '_', $item );
			if( Helper::get_option('header_prebuild_' . $key) ) {
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
			<div class="site-header__container <?php echo esc_attr( apply_filters( 'ecomus_header_container_classes', 'em-container' ) ) ?>">
				<?php foreach ( $sections as $section => $items ) : ?>
					<?php
					$class      = [];
					$item_names = wp_list_pluck( $items, 'item' );

					if ( in_array( 'primary-menu', $item_names ) ) {
						$class[] = 'has-menu';
					}
					?>

					<div class="header-<?php echo esc_attr( $section ); ?>-items header-items <?php echo esc_attr( implode( ' ', $class ) ); ?>">
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
			$item['item'] = $item['item'] ? $item['item'] : key( \Ecomus\Options::header_items_option() );
			$template_file = $item['item'];
			$args = array();
			$load_file = true;

			switch ( $item['item'] ) {
				case 'logo':
					$args = $this->logo_options( $options );
					break;
				case 'primary-menu':
					$args = $this->primary_menu_options( $options );
					break;
				case 'search':
					$args = $this->search_options( $options );
					$template_file = 'search-' . $args['search_layout'];

					\Ecomus\Theme::set_prop( 'modals', 'search' );
					break;
				case 'cart':
					$args = $this->cart_options( $options );

					\Ecomus\Theme::set_prop( 'panels', 'cart' );
					break;
				case 'account':
					$args = $this->account_options( $options );

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

					$args = $this->compare_options( $options );
					break;

				case 'wishlist':
					if ( ! class_exists( '\WCBoost\Wishlist\Helper' ) ) {
						break;
					}

					$args = $this->wishlist_options( $options );
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
		$args = array();
		$args['title'] = ! empty( $options ) && isset( $options['logo_title'] ) ? $options['logo_title'] : true;
		$options = isset( $options['logo'] ) ? $options['logo'] : '';
		$args['type'] = ! empty( $options ) && isset( $options['type'] ) ? $options['type'] : Helper::get_option( 'logo_type' );
		$args['type'] = apply_filters( 'ecomus_header_logo_type', $args['type'] );
		$args['logo_light'] = ! empty( $options ) && isset( $options['logo_light'] ) ? $options['logo_light'] : '';
		return $args;
	}

	/**
	 * Primary Menu options
	 *
	 * @since 1.0.0
	 *
	 * @param array $options
	 * @return array $args
	 */
	public function primary_menu_options( $options ) {
		$options = isset( $options['primary_menu'] ) ? $options['primary_menu'] : '';
		$args = array();

		$args['menu_class'] = ! empty( $options ) && isset( $options['menu_class'] ) ? $options['menu_class'] : true;

		$args['container_class'] = ' primary-navigation';

		return $args;
	}

	/**
	 * Search options
	 *
	 * @since 1.0.0
	 *
	 * @param array $options
	 * @return array $args
	 */
	public static function search_options( $options ) {
		$options = isset( $options['search'] ) ? $options['search'] : '';
		$args = array();

		$args['search_layout'] = ! empty( $options ) && isset( $options['search_layout'] ) ? $options['search_layout'] : Helper::get_option( 'header_search_layout' );

		return $args;
	}

	/**
	 * Account options
	 *
	 * @since 1.0.0
	 *
	 * @param array $options
	 * @return array $args
	 */
	public static function cart_options( $options ) {
		$options = isset( $options['cart'] ) ? $options['cart'] : '';
		$args = array();

		$args['cart_size'] = ! empty( $options ) && isset( $options['cart_size'] ) ? $options['cart_size'] : Helper::get_option( 'header_cart_size' );
		$args['cart_divider'] = ! empty( $options ) && isset( $options['cart_divider'] ) ? $options['cart_divider'] : Helper::get_option( 'header_cart_divider' );

		$args['cart_classes'] = '';
		$args['data_target'] = Helper::get_option( 'cart_icon_behaviour' ) == 'panel' ? 'cart-panel' : 'cart-page';

		if ( $args['cart_size'] == 'large' ) {
			$args['cart_classes'] .= ' header-cart__size-large';
		}

		if ( $args['cart_divider'] ) {
			$args['cart_classes'] .= ' header-cart__divider';
		}

		return $args;
	}

	/**
	 * Account options
	 *
	 * @since 1.0.0
	 *
	 * @param array $options
	 * @return array $args
	 */
	public static function account_options( $options ) {
		$options = isset( $options['account'] ) ? $options['account'] : '';
		$args = array();

		$args['account_display'] = ! empty( $options ) && isset( $options['account_display'] ) ? $options['account_display'] : Helper::get_option( 'header_account_display' );

		$args['data_toggle'] = is_user_logged_in() ? 'off-canvas' : 'modal';
		$args['data_target'] = is_user_logged_in() ? 'account-panel' : 'login-modal';
		$args['account_text'] = is_user_logged_in() ? esc_html__( 'Account', 'ecomus' ) : esc_html__( 'Login', 'ecomus' );
		$args['account_classes'] = ' header-account__' . $args['account_display'];
		$args['account_text_class'] = 'header-item__text header-account__text em-font-medium';

		if ( $args['account_display'] == 'icon' ) {
			$args['account_classes'] .= ' em-button-icon';
			$args['account_text_class'] .= ' screen-reader-text';
		}

		return $args;
	}

	/**
	 * Compare options
	 *
	 * @since 1.0.0
	 *
	 * @param array $options
	 * @return array $args
	 */
	public static function compare_options( $options ) {
		$options = isset( $options['compare'] ) ? $options['compare'] : '';
		$args = array();

		$args['compare_display'] = ! empty( $options ) && isset( $options['compare_display'] ) ? $options['compare_display'] : Helper::get_option( 'header_compare_display' );
		$args['compare_count'] = \WCBoost\ProductsCompare\Plugin::instance()->list->count_items();

		$args['compare_classes'] = ' header-compare__' . $args['compare_display'];
		$args['compare_text_class'] = 'header-item__text header-compare__text em-font-medium';
		$args['compare_counter_class'] = 'header-counter header-compare__counter';

		if ( $args['compare_display'] == 'icon' ) {
			$args['compare_classes'] .= ' em-button-icon';
			$args['compare_text_class'] .= ' screen-reader-text';
		}

		if ( $args['compare_count'] == 0 ) {
			$args['compare_counter_class'] .= ' empty-counter';
		}

		return $args;
	}

	/**
	 * Wishlist options
	 *
	 * @since 1.0.0
	 *
	 * @param array $options
	 * @return array $args
	 */
	public static function wishlist_options( $options ) {
		$options = isset( $options['wishlist'] ) ? $options['wishlist'] : '';
		$args = array();

		$args['wishlist_display'] = ! empty( $options ) && isset( $options['wishlist_display'] ) ? $options['wishlist_display'] : Helper::get_option( 'header_wishlist_display' );
		$args['wishlist_count'] = \WCBoost\Wishlist\Helper::get_wishlist()->count_items();

		$args['wishlist_classes'] = ' header-wishlist__' . $args['wishlist_display'];
		$args['wishlist_text_class'] = 'header-item__text header-wishlist__text em-font-medium';
		$args['wishlist_counter_class'] = 'header-counter header-wishlist__counter';

		if ( $args['wishlist_display'] == 'icon' ) {
			$args['wishlist_classes'] .= ' em-button-icon';
			$args['wishlist_text_class'] .= ' screen-reader-text';
		}

		if ( $args['wishlist_count'] == 0 ) {
			$args['wishlist_counter_class'] .= ' empty-counter';
		}

		return $args;
	}

	/**
	 * Return classe
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes
	 * @return array $args
	 */

	public function header_classes( $section, $classes = array() ) {
		return implode( ' ', $classes );
	}

	/**
	 * Display the site branding title
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 * @return void
	 */
	public static function site_branding_title( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'class' => '',
			'echo'  => true,
		) );

		// Ensure included a space at beginning.
		$class = ' site-title';

		// HTML tag for this title.
		$tag = is_front_page() || is_home() ? 'h1' : 'p';
		$tag = apply_filters( 'ecomus_site_branding_title_tag', $tag, $args );

		if ( is_array( $args['class'] ) ) {
			$class = implode( ' ', $args['class'] ) . $class;
		} elseif ( is_string( $args['class'] ) ) {
			$class = $args['class'] . $class;
		}

		$title = sprintf(
			'<%1$s class="%2$s"><a href="%3$s" rel="home">%4$s</a></%1$s>',
			$tag,
			esc_attr( trim( $class ) ),
			esc_url( home_url( '/' ) ),
			get_bloginfo( 'name' )
		);

		if ( ! $args['echo'] ) {
			return $title;
		}

		echo apply_filters( 'ecomus_site_branding_title_html', $title );
	}

	/**
	 * Display the site branding description
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 * @return void
	 */
	public static function site_branding_description( $args = array() ) {
		$text = get_bloginfo( 'description', 'display' );

		if ( empty( $text ) ) {
			return '';
		}

		$args = wp_parse_args( $args, array(
			'class' => '',
			'echo'  => true,
		) );

		// Ensure included a space at beginning.
		$class = ' site-description';

		if ( is_array( $args['class'] ) ) {
			$class = implode( ' ', $args['class'] ) . $class;
		} elseif ( is_string( $args['class'] ) ) {
			$class = $args['class'] . $class;
		}

		$description = sprintf(
			'<p class="%s">%s</p>',
			esc_attr( trim( $class ) ),
			wp_kses_post( $text )
		);

		if ( ! $args['echo'] ) {
			return $description;
		}

		echo apply_filters( 'site_branding_description_html', $description );
	}
}