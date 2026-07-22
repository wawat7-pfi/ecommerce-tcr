<?php
/**
 * Catalog hooks.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

use \Ecomus\Helper;
use Ecomus\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Catalog
 */

class Catalog {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * @var string catalog view
	 */
	public static $catalog_view;

	protected static $view_cookie_name = 'catalog_view';

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

		// Site content container
		add_filter( 'ecomus_site_content_container_class', array( $this, 'site_content_container_class' ), 10, 1 );

		// Sidebar
		add_filter( 'ecomus_site_layout', array( $this, 'layout' ), 55 );
		add_filter( 'ecomus_get_sidebar', array( $this, 'sidebar' ), 10 );

		add_filter( 'ecomus_primary_sidebar_classes', array( $this, 'sidebar_classes' ), 10 );

		// Top Categories
		if ( apply_filters( 'ecomus_top_categories_elementor', Helper::get_option( 'top_categories' ) ) ) {
			add_action( 'ecomus_after_site_content_open', array( $this, 'top_categories' ), 10 );
		}

		// Catalog Toolbar
		if ( apply_filters( 'ecomus_catalog_toolbar_option_elementor', Helper::get_option( 'catalog_toolbar' ) ) ) {
			add_action( 'woocommerce_before_shop_loop', array( $this, 'catalog_toolbar' ), 40 );
		}

		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

		add_action( 'ecomus_woocommerce_products_toolbar', array( $this, 'toolbar_left' ), 20 );
		add_action( 'ecomus_woocommerce_products_toolbar', array( $this, 'show_catalog_sidebar_mobile' ), 20 );
		add_action( 'ecomus_woocommerce_products_toolbar', array( $this, 'toolbar_center' ), 40 );
		add_action( 'ecomus_woocommerce_products_toolbar', array( $this, 'toolbar_right' ), 60 );

		// Filters Actived
		add_action( 'woocommerce_before_shop_loop', array( $this, 'filters_actived' ), 50 );

		// Pagination
		add_filter( 'next_posts_link_attributes', array( $this, 'ecomus_next_posts_link_attributes' ), 10, 1 );

		remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination' );
		add_action( 'woocommerce_after_shop_loop', array( $this, 'pagination' ) );

		// Add div shop loop
		add_action( 'woocommerce_before_shop_loop', array( $this, 'shop_content_open_wrapper' ), 60 );
		add_action( 'woocommerce_after_shop_loop', array( $this, 'shop_content_close_wrapper' ), 20 );

		// Remove shop loop header
		remove_action('woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header', 10);
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );

		// Change step price filter widget
		add_filter( 'woocommerce_price_filter_widget_step', array( $this, 'price_filter_widget_step' ), 10 );

		// Add button return shop to no product
		add_action( 'woocommerce_no_products_found', array( $this, 'product_filter_no_products_found' ), 20 );

		// Set coookie
		self::set_cookie();

		// Description after content
		if( intval( Helper::get_option( 'taxonomy_description_enable' ) ) && Helper::get_option( 'taxonomy_description_position' ) === 'below' ) {
			add_action( 'woocommerce_after_main_content', array( $this, 'description_after_content' ), 5 );
		};
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
		$classes[] = 'ecomus-catalog-page';

		return $classes;
	}

	/**
	 * Site content container class
	 *
	 * @return string $classes
	 */
	public function site_content_container_class( $classes ) {
		$classes .= ' site-content-container';
		if( Helper::get_option( 'product_catalog_full_width' ) ) {
			$classes .= ' em-container-fluid';
		}
		return $classes;
	}

	/**
	 * Layout
	 *
	 * @return string
	 */
	public function layout( $layout ) {
		if( ! is_active_sidebar( 'catalog-sidebar' ) ){
			return;
		}

		$layout = Helper::get_option( 'product_catalog_sidebar' );

		return $layout;
	}

	/**
	 * Get Sidebar
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function sidebar() {
		if ( ! is_active_sidebar( 'catalog-sidebar' ) ) {
			return false;
		}

		if ( Helper::get_option( 'product_catalog_sidebar' ) == 'no-sidebar' ) {
			return false;
		}

		return true;
	}

	/**
	 * Get Sidebar
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function sidebar_classes( $classes ) {
		if ( ! is_active_sidebar( 'catalog-sidebar' ) ) {
			return;
		}

		if ( Helper::get_option( 'product_catalog_sidebar' ) == 'no-sidebar' ) {
			return;
		}

		if ( Helper::get_option( 'product_catalog_sidebar' ) == 'sidebar-content' ) {
			$classes .= ' offscreen-panel--side-right';
		}

		return $classes;
	}

	/**
	 * Show top categories
	 *
	 * @return void
	 */
	public function top_categories() {
		if( is_search() ) {
			return;
		}

		$top_html = '<div class="catalog-top-categories"></div>';

		if ( is_tax( 'product_brand' ) && ! intval( Helper::get_option( 'show_brand_page' ) )) {
			return $top_html;
		}

		$queried        = get_queried_object();
		if( empty( $queried ) ) {
			return $top_html;
		}

		$current_term   = ! empty ( $queried->term_id ) ? $queried->term_id : '';
		$orderby 		= Helper::get_option( 'top_categories_order' );
		$limit			= Helper::get_option( 'top_categories_limit' );
		$columns 		= Helper::get_option( 'top_categories_columns' );
		$column_space 	= Helper::get_option( 'top_categories_column_space' );
		$title_position = Helper::get_option( 'top_categories_title_position' );
		$ouput          = array();

		$data_settings = array();

		if( ! empty( $columns ) ) {
			$data_settings['columns'] = $columns;
		}

		if( ! empty( $column_space ) ) {
			$data_settings['column_space'] = $column_space;
		}

		if( $this->is_shop() ) {
			$args = array(
				'taxonomy' => 'product_cat',
				'parent'   => 0,
			);

		} else {
			$termchildren  = get_term_children( $queried->term_id, $queried->taxonomy );

			$args = array(
				'taxonomy' => $queried->taxonomy,
			);

			if( ! empty( $termchildren ) ) {
				$args['parent'] = $queried->term_id;

				if( count( $termchildren ) == 1 ) {
					$term = get_term_by( 'id', $termchildren[0], $queried->taxonomy );

					if( $term->count == 0 ) {
						$args['parent'] = $queried->parent;
					}
				}

			} else {
				$args['parent'] = $queried->parent;
			}
		}

		if ( ! empty( $orderby ) ) {
			$args['orderby'] = $orderby;

			if ( $orderby == 'order' ) {
				$args['menu_order'] = 'asc';
			} else {
				if ( $orderby == 'count' ) {
					$args['order'] = 'desc';
				}
			}
		}

		if( ! empty ( $limit ) && $limit !== '0' ) {
			$args['number'] =  Helper::get_option( 'top_categories_limit' );
		}

		$terms = get_terms( $args );

		if ( is_wp_error( $terms ) || ! $terms ) {
			return;
		}

		$thumbnail_size = 'full';

		foreach( $terms as $term ) {
			$thumb_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
			$images = ! empty( wp_get_attachment_image_src( $thumb_id, $thumbnail_size ) ) ? wp_get_attachment_image_src( $thumb_id, $thumbnail_size )[0] : wc_placeholder_img_src( $thumbnail_size );

			$thumb_url = !empty( $thumb_id ) ? $images : wc_placeholder_img_src( $thumbnail_size );
			$term_img = !empty( $thumb_url ) ? '<img class="catalog-top-categories__image" src="' . esc_url( $thumb_url ) . '" alt="' . esc_attr( $term->name ) . '" />' : '<span class="catalog-top-categories__image">' . esc_attr( $term->name ) . '</span>';

			$ouput[] = sprintf(
						'<div class="catalog-top-categories__item swiper-slide %s">
							<a class="catalog-top-categories__inner em-ratio em-eff-img-zoom em-image-rounded" href="%s">
								%s
								%s
								%s
									<span class="catalog-top-categories__text">%s</span>
									%s
								%s
							</a>
						</div>',
						( !empty( $current_term ) && $current_term == $term->term_id ) ? 'active' : '',
						esc_url( get_term_link( $term->term_id ) ),
						$term_img,
						$title_position == 'outside' ? '</a><a class="catalog-top-categories__button" href="'.esc_url( get_term_link( $term->term_id ) ).'">' : '',
						$title_position == 'outside' ? '' : '<span class="catalog-top-categories__title em-button em-button-light em-font-medium">',
						esc_html( $term->name ),
						$title_position == 'outside' ? '' : Icon::get_svg('arrow-top'),
						$title_position == 'outside' ? '' : '</span>',
					);
		}

		echo sprintf(
				'<div class="catalog-top-categories em-ratio--portrait swiper ecomus-carousel--elementor navigation-class-arrows navigation-class--tabletdots" data-settings="%1$s">
					<div class="catalog-top-categories__wrapper swiper-wrapper mobile-col-%2$s tablet-col-%3$s columns-%4$s">%5$s</div>
					<div class="swiper-pagination swiper-pagination-bullet--small"></div>
					%6$s %7$s
				</div>',
				! empty( $data_settings ) ? esc_attr( json_encode( $data_settings ) ) : '',
				isset( $columns['mobile'] ) ? $columns['mobile'] : 2,
				isset( $columns['tablet'] )  ? $columns['tablet'] : 3,
				isset( $columns['desktop'] ) ? $columns['desktop'] : 5,
				implode( '', $ouput ),
				\Ecomus\Icon::get_svg( 'left-mini', 'ui', 'class=em-button-light ecomus-swiper-button swiper-button swiper-button-prev' ),
				\Ecomus\Icon::get_svg( 'right-mini', 'ui', 'class=em-button-light ecomus-swiper-button swiper-button swiper-button-next' )
			);
	}

	/**
	 * Catalog toolbar.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function catalog_toolbar() {
		if ( wc_get_loop_prop( 'is_shortcode' ) ) {
			return;
		}

        echo '<div class="catalog-toolbar">';
			/**
			 * Hook: ecomus_woocommerce_before_products_toolbar
			 */
			do_action( 'ecomus_woocommerce_before_products_toolbar' );

			echo '<div class="catalog-toolbar__toolbar">';
				/**
				 * Hook: ecomus_woocommerce_products_toolbar
				 */
				do_action( 'ecomus_woocommerce_products_toolbar' );

			echo '</div>';
			/**
			 * Hook: ecomus_woocommerce_after_products_toolbar
			 */
			do_action( 'ecomus_woocommerce_after_products_toolbar' );

		echo '</div>';
	}

	/**
	 * Catalog toolbar left item.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function toolbar_left() {
		if( ! in_array( 'filter', ( array ) Helper::get_option( 'catalog_toolbar_els' ) ) || ! is_active_sidebar( 'catalog-filters-sidebar' ) ) {
			return;
		}

		\Ecomus\Theme::set_prop( 'panels', 'filter-sidebar' );

		echo sprintf(
			'<div class="catalog-toolbar__left catalog-toolbar__item">
			<button class="em-button-outline em-font-semibold catalog-toolbar__filter-button" data-toggle="off-canvas" data-target="filter-sidebar-panel">%s %s</button>
			</div>',
			\Ecomus\Icon::get_svg( 'filter' ),
			esc_html__('Filter', 'ecomus')
		);
	}

	public function show_catalog_sidebar_mobile() {
		if( Helper::get_option( 'mobile_catalog_sidebar' ) !== 'filter-toolbar' ) {
			return;
		}

		if( Helper::get_option( 'product_catalog_sidebar' ) == 'no-sidebar' || ! is_active_sidebar( 'catalog-sidebar' ) ) {
			return;
		}

		echo sprintf(
			'<div class="catalog-toolbar__left catalog-toolbar__item hidden-md hidden-lg">
			<button class="em-button-outline em-font-semibold catalog-toolbar__filter-button" data-toggle="off-canvas" data-target="mobile-sidebar-panel">%s %s</button>
			</div>',
			\Ecomus\Icon::get_svg( 'filter' ),
			esc_html__('Filter', 'ecomus')
		);
	}

	/**
	 * Catalog toolbar center item.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function toolbar_center() {
		if( ! in_array( 'view', ( array ) Helper::get_option( 'catalog_toolbar_els' ) ) ) {
			return;
		}

		echo '<div class="catalog-toolbar__center catalog-toolbar__item">';
		$this->toolbar_view();
		echo '</div>';
	}

	/**
	 * Catalog toolbar right item.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function toolbar_right() {
		if( ! in_array( 'sortby', ( array ) Helper::get_option( 'catalog_toolbar_els' ) ) ) {
			return;
		}

		\Ecomus\Theme::set_prop( 'popovers', 'mobile-orderby' );

		echo '<div class="catalog-toolbar__right catalog-toolbar__item">';
		woocommerce_catalog_ordering();
		echo '<button class="em-button-outline catalog-toolbar__orderby-button hidden-sm hidden-md hidden-lg" data-toggle="popover" data-target="mobile-orderby-popover">' . esc_html__('Sort', 'ecomus') . \Ecomus\Icon::get_svg( 'arrow-bottom' ) .'</button>';
		echo '</div>';
	}

	/**
	 * Toolbar view.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function toolbar_view() {
		global $wp;
		$columns = ( array ) Helper::get_option( 'catalog_toolbar_views' );
		$default = $this->catalog_toolbar_default_view();
		$class = $mobile_class = $tablet_class = '';
		$output_type = [];
		foreach( $columns as $column ) {
			if( $column == '1' ) {
				$view = 'list';
				$icon = 'list';
				$class = 'list';
			} else {
				$view = 'grid';
				$icon = 'grid-' . $column;
				$class = 'grid grid-' . $column;
			}
			$class .= $default == $icon ? ' current' : '';
			$tablet_class = $view == 'grid' && $column != '2' && empty( $tablet_class ) ? 'grid-' . $column : $tablet_class;
			$mobile_class = $view == 'grid' && empty( $mobile_class ) ? 'grid-' . $column : $mobile_class;

			$class .= $tablet_class == 'grid-' . $column ? ' tablet-active' : '';
			$class .= $mobile_class == 'grid-' . $column ? ' mobile-active' : '';

			$link_url = array(
				'column' => $column
			);

			if( isset( $_GET ) ) {
				$link_url = wp_parse_args(
					$link_url,
					$_GET
				);
			}

			$current = home_url( $wp->request );

			$current_url = add_query_arg(
				$link_url,
				substr( $current, 0, strpos( $current, 'page/') )
			);

			$output_type[] = sprintf(
				'<a href="%s" class="%s" data-column="%s">%s</a>',
				esc_url($current_url),
				esc_attr( $class ),
				esc_attr( $column ),
				\Ecomus\Icon::get_svg( $icon ),

			);
		}

		echo sprintf(
			'<div id="ecomus-toolbar-view" class="ecomus-toolbar-view">%s</div>',
			implode( $output_type )
		);
	}

	/**
	 * Template redirect
	 *
	 * @return void
	 */
	public static function catalog_toolbar_default_view() {
		if( isset( self::$catalog_view ) ) {
			return self::$catalog_view;
		}
		$column = isset( $_GET['column'] ) && ! empty( $_GET['column'] ) ? $_GET['column'] : '';
		if ( ! empty($column) ) {
			self::$catalog_view = $column == '1' ? 'list' : 'grid-' . $column;
		} else {
			if( isset( $_COOKIE[self::$view_cookie_name] ) ) {
				self::$catalog_view =  $_COOKIE[self::$view_cookie_name];
			} else {
				$default_view = apply_filters( 'ecomus_catalog_default_view', Helper::get_option( 'catalog_toolbar_default_view' ) );
				$default_column = apply_filters( 'ecomus_catalog_default_columns', get_option( 'woocommerce_catalog_columns', 4 ) );
				self::$catalog_view = $default_view == 'list' ? 'list' : 'grid-' . $default_column;
			}
		}

		self::$catalog_view = apply_filters( 'ecomus_catalog_view', self::$catalog_view );

		return self::$catalog_view;
	}

	/**
	 * Filters actived
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function filters_actived() {
		$filter_class = ! isset( $_GET['filter'] ) ? ' hidden' : '';
		$text = wc_get_loop_prop( 'total' ) > 1 ? esc_html__( 'Products Found', 'ecomus' ) : esc_html__( 'Product Found', 'ecomus' );

		echo '<div class="catalog-toolbar__active-filters'. esc_attr( $filter_class ) .'">';
		echo '<div class="catalog-toolbar__result-count"><span class="count">'. wc_get_loop_prop( 'total' ) .'</span>'. $text .'</div>';
		echo '<div class="catalog-toolbar__filters-actived" data-clear-text="'. esc_html__( 'Remove all', 'ecomus' ).'"></div>';
		echo '</div>';
	}

	/**
	 * Next posts link attributes
	 *
	 * @return string $attr
	 */
	public function ecomus_next_posts_link_attributes( $attr ) {
		if( Helper::get_option( 'product_catalog_pagination' ) !== 'numeric' ) {
			$attr = 'class="woocommerce-pagination-button em-button em-button-outline"';
		}

		return $attr;
	}

	/**
	 * Pagination.
	 */
	public static function pagination() {
		if( ! apply_filters( 'ecomus_pagination_elementor', true ) ) {
			return;
		}
		// Display the default pagination for [products] shortcode.
		if ( wc_get_loop_prop( 'is_shortcode' ) ) {
			woocommerce_pagination();
			return;
		}

		$pagination_type = Helper::get_option( 'product_catalog_pagination' );

		if ( 'numeric' == $pagination_type ) {
			woocommerce_pagination();
		} elseif ( get_next_posts_link() ) {

			$classes = array(
				'woocommerce-pagination',
				'woocommerce-pagination--catalog',
				'next-posts-pagination',
				'woocommerce-pagination--ajax',
				'woocommerce-pagination--' . $pagination_type,
				'text-center'
			);

			echo '<nav class="' . esc_attr( implode( ' ', $classes ) ) . '">';
				next_posts_link( '<span>' . esc_html__( 'Load more', 'ecomus' ) . '</span>' );
			echo '</nav>';
		}
	}

	/**
	 * Open Shop Content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function shop_content_open_wrapper() {
		echo '<div id="ecomus-shop-content" class="ecomus-shop-content">';
	}

	/**
	 * Close Shop Content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function shop_content_close_wrapper() {
		echo '</div>';
	}

	/**
	 * Set cookie
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function set_cookie() {
		if( isset( $_GET['column'] ) ) {
			$cookie_value = $_GET['column'] == '1' ? 'list' : 'grid-' . $_GET['column'];
		} else {
			if( isset( $_COOKIE[self::$view_cookie_name] ) ) {
				$cookie_value = $_COOKIE[self::$view_cookie_name];
			} else {
				$column = get_option( 'woocommerce_catalog_columns', 4 );
				$cookie_value = $column == '1' ? 'list' : 'grid-' . $column;
			}
		}

		if ( empty( $cookie_value ) ) {
			return;
		}

		if( isset( $_COOKIE[self::$view_cookie_name] ) && $_COOKIE[self::$view_cookie_name] == $cookie_value ) {
			return;
		}

		setcookie(self::$view_cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
	}

	/**
	 * Price filter widget step
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function price_filter_widget_step() {
		return '1';
	}

	/**
	 * Product filter when no product found
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function product_filter_no_products_found() {
		echo '<div class="em-button-no-products-found text-right"><a class="em-button em-button-subtle" href="'. esc_url( wc_get_page_permalink( 'shop' ) ) .'">'. esc_html__( 'Return to shop', 'ecomus' ) .'</a></div>';
	}

	/**
	 * Order by list
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function orderby_list() {
		if( ! in_array( 'sortby', ( array ) Helper::get_option( 'catalog_toolbar_els' ) ) ) {
			return;
		}

		$show_default_orderby    = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );
		$orderby = apply_filters(
			'woocommerce_catalog_orderby',
			array(
				'menu_order' => __( 'Default sorting', 'ecomus' ),
				'popularity' => __( 'Sort by popularity', 'ecomus' ),
				'rating'     => __( 'Sort by average rating', 'ecomus' ),
				'date'       => __( 'Sort by latest', 'ecomus' ),
				'price'      => __( 'Sort by price: low to high', 'ecomus' ),
				'price-desc' => __( 'Sort by price: high to low', 'ecomus' ),
			)
		);

		if ( wc_get_loop_prop( 'is_search' ) ) {
			$orderby = array_merge( array( 'relevance' => __( 'Relevance', 'woocommerce' ) ), $orderby );

			unset( $orderby['menu_order'] );
		}

		if ( ! $show_default_orderby ) {
			unset( $orderby['menu_order'] );
		}

		if ( ! wc_review_ratings_enabled() ) {
			unset( $orderby['rating'] );
		}

		return $orderby;
	}

	/**
	 * Description after content
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function description_after_content() {
		$description = self::get_below_description();
		if ( $description ) {
			\Ecomus\WooCommerce\Shop_Header::description_content( $description );
		}
	}

	public static function get_below_description() {
		if ( ! Helper::get_option( 'taxonomy_description_enable' ) || Helper::get_option( 'taxonomy_description_position' ) !== 'below' ) {
			return '';
		}

		if ( is_tax() ) {
			$term = get_queried_object();
			if ( $term ) {
				$desc_after = get_term_meta( $term->term_id, 'em_desc_after_content', true );
				return $desc_after ? $desc_after : $term->description;
			}
		}

		return '';
	}

	/**
	 * Check is shop
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function is_shop() {
		if( function_exists('is_product_category') && is_product_category() ) {
			return false;
		} elseif( function_exists('is_shop') && is_shop() ) {
			if ( ! empty( $_GET ) && ( isset($_GET['product_cat']) )) {
				return false;
			}

			return true;
		}

		return true;
	}
}