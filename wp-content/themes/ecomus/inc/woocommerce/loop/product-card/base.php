<?php
/**
 * Product Card hooks.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce\Loop\Product_Card;

use Ecomus\Helper, Ecomus\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product Card
 */
class Base {
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
		if( is_admin() ) {
			// add actions for elementor
			add_action('admin_init', array($this, 'actions'), 10);
		} else {
			// add actions for frontend
			add_action('wp', array($this, 'actions'), 10 );
		}

	}

	public function actions(){
		add_filter( 'ecomus_wp_script_data', array( $this, 'card_script_data' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 20 );

		add_filter( 'woocommerce_product_loop_start', array( $this, 'loop_start' ), 20 );
		add_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories', 20 );

		// Product inner wrapper
		add_action( 'woocommerce_before_shop_loop_item', array( $this, 'product_inner_open' ), 1 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'product_inner_close' ), 1000 );

		// Remove wrapper link
		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

		// Change product thumbnail.
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'product_loop_thumbnail' ), 1 );

		// Product summary
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'product_summary_open' ), 1 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'product_summary_close' ), 1000 );

		// Rating
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		if( Helper::get_option('product_card_rating') ) {
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'product_rating' ), 15 );
		}

		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'product_taxonomy' ), 10 );

		// Change the product title.
		remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title' );
		add_action( 'woocommerce_shop_loop_item_title', array( $this, 'product_card_title' ) );

		// Change add to cart text
		add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'product_add_to_cart_text' ), 20, 2 );

		// Add units of measure to price
		add_filter('woocommerce_get_price_html', array( $this, 'product_unit_measure' ), 20, 2 );

		// Add to cart button
		add_filter('woocommerce_loop_add_to_cart_link', array( $this, 'product_add_to_cart_link' ), 20, 3 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );

		// Add to cart button on mobile.
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'product_after_shop_loop_item' ), 30 );
	}

	/**
	 * Product card script data.
	 *
	 * @since 1.0.0
	 *
	 * @param $data
	 *
	 * @return array
	 */
	public function card_script_data( $data ) {
		if ( 'zoom' == Helper::get_option( 'product_card_hover' ) ) {
			$data['product_card_hover'] = 'zoom';
		}

		return $data;
	}

	/**
	 * WooCommerce specific scripts & stylesheets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function scripts() {
		if( Helper::get_option( 'product_card_sale_display_type') && Helper::get_option( 'product_card_sale_display_type') == 'countdown' ) {
			wp_enqueue_script( 'ecomus-countdown',  get_template_directory_uri() . '/assets/js/plugins/jquery.countdown.js', array(), '1.0' );
		}

		if ( 'zoom' == Helper::get_option( 'product_card_hover' ) ) {
			wp_enqueue_style( 'driff-style', get_template_directory_uri() . '/assets/css/plugins/drift-basic.css');
			wp_enqueue_script( 'driff-js', get_template_directory_uri() . '/assets/js/plugins/drift.min.js', array(), '', true );
		}
	}

	/**
	 * Loop start.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html Open loop wrapper with the <ul class="products"> tag.
	 *
	 * @return string
	 */
	public function loop_start( $html ) {
		$html            = '';
		$classes = array(
			'products',
			'em-flex',
			'em-flex-wrap',
		);

		$loop_layout = \Ecomus\WooCommerce\Helper::get_product_card_layout();

		$classes[] = 'product-card-layout-' . $loop_layout;

		if ( $mobile_pl_col = apply_filters( 'ecomus_mobile_product_columns', intval( Helper::get_option( 'mobile_product_columns' ) ) ) ) {
			$classes[] = 'mobile-col-' . $mobile_pl_col;
		}

		if ( intval( Helper::get_option( 'mobile_product_card_atc' ) ) && ! in_array( $loop_layout, array( '6', '7', '8', '9' ) ) ) {
			$classes[] = 'product-card-mobile-show-atc';
		}

		if ( intval( Helper::get_option( 'mobile_product_card_featured_icons' ) ) ) {
			$classes[] = 'mobile-show-featured-icons';
		}

		if ( ! intval( Helper::get_option( 'mobile_product_card_wishlist' ) ) ) {
			$classes[] = 'mobile-wishlist-button--hidden';
		}

		if ( ! intval( Helper::get_option( 'mobile_product_card_compare' ) ) ) {
			$classes[] = 'mobile-compare-button--hidden';
		}

		if( $loop_layout == '6' ) {
			$classes[] = 'product-card-button-atc-transfrom--bottom';
		}

		if( $loop_layout == '7' ) {
			$classes[] = 'product-card-button-atc-transfrom--top';
		}

		$classes[] = 'columns-' . wc_get_loop_prop( 'columns' );

		$html = '<ul class="' . esc_attr( implode( ' ', $classes ) ) . '">';

		return $html;
	}

	/**
	 * Open product inner.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_inner_open() {
		echo '<div class="product-inner">';
	}

	/**
	 * Close product ineer.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_inner_close() {
		echo '</div>';
	}

	/**
	 * Open product summary.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_summary_open() {
		$class = \Ecomus\WooCommerce\Helper::get_product_card_layout() == '8' ? ' product-summary--relative em-relative' : '';
		$class .= \Ecomus\WooCommerce\Helper::get_product_card_layout() == '9' ? ' em-flex em-flex-column em-flex-align-center text-center' : '';
		if( ! in_array(\Ecomus\WooCommerce\Helper::get_product_card_layout(), array('8', '9', 'list') ) ) {
			if(  Helper::get_option( 'product_card_summary' ) == 'center' ) {
				$class .= ' em-flex em-flex-column em-flex-align-center text-center';
			} elseif(Helper::get_option( 'product_card_summary' ) == 'flex-end') {
				$class .= ' em-flex em-flex-column em-flex-align-end text-right';
			}
		}
		echo '<div class="product-summary'. esc_attr( $class ) .'">';
		do_action( 'ecomus_before_product_summary_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ) );
	}

	/**
	 * Close product ineer.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_summary_close() {
		echo '</div>';
	}

	/**
	 * Product thumbnail.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_thumbnail() {
		global $product;

		switch ( Helper::get_option( 'product_card_hover' ) ) {
			case 'slider':
				$image_ids  = $product->get_gallery_image_ids();
				$image_size = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );
				echo '<div class="product-thumbnail em-relative">';
					if ( $image_ids ) {
						echo '<div class="product-thumbnails--slider swiper"><div class="swiper-wrapper">';
					}

					$this->loop_product_link_open();
						woocommerce_template_loop_product_thumbnail();
					$this->loop_product_link_close();

						foreach ( $image_ids as $image_id ) {
							$src = wp_get_attachment_image_src( $image_id, $image_size );

							if ( ! $src ) {
								continue;
							}

							$this->loop_product_link_open();

								printf(
									'<img src="%s" data-src="%s" width="%s" height="%s" alt="%s" class="swiper-lazy">',
									esc_url( $src[0] ),
									esc_url( $src[0] ),
									esc_attr( $src[1] ),
									esc_attr( $src[2] ),
									esc_attr( $product->get_title() )
								);

							$this->loop_product_link_close();
						}
					if ( $image_ids ) {
							echo '</div>';
							echo Icon::inline_svg( array( 'icon' => 'arrow-left-long', 'class' => 'ecomus-product-card-swiper-button em-button-light swiper-button swiper-button-prev' ));
							echo Icon::inline_svg( array( 'icon' => 'arrow-right-long', 'class' => 'ecomus-product-card-swiper-button em-button-light swiper-button swiper-button-next' ));
						echo '</div>';
					}
					do_action( 'ecomus_product_before_loop_thumbnail' );
					do_action( 'ecomus_product_loop_thumbnail_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ) );
				echo '</div>';
				break;

			case 'zoom';
				echo '<div class="product-thumbnail em-relative product-thumbnails--zoom">';
					$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

					if ( $image ) {
						$link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );
						echo '<a href="' . esc_url( $link ) . '" class="woocommerce-loop-product__link woocommerce-LoopProduct-link product-thumbnail-zoom" data-zoom="' . esc_url( $image[0] ) . '">';
					} else {
						$this->loop_product_link_open();
					}
						woocommerce_template_loop_product_thumbnail();
					$this->loop_product_link_close();
					do_action( 'ecomus_product_before_loop_thumbnail' );
					do_action( 'ecomus_product_loop_thumbnail_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ) );
				echo '</div>';
				break;

			case 'fadein':
				$image_ids = $product->get_gallery_image_ids();
				if( ! empty( $image_ids ) ) {
					echo '<div class="product-thumbnail em-relative product-thumbnails--fadein">';
				} else {
					echo '<div class="product-thumbnail em-relative">';
				}

					$this->loop_product_link_open();
						woocommerce_template_loop_product_thumbnail();

						if ( ! empty( $image_ids ) ) {
							$image_size = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );
							echo wp_get_attachment_image( $image_ids[0], $image_size, false, array( 'class' => 'attachment-woocommerce_thumbnail size-woocommerce_thumbnail product-thumbnails--fadein-image' ) );
						}

						$this->loop_product_link_close();
					do_action( 'ecomus_product_before_loop_thumbnail' );
					do_action( 'ecomus_product_loop_thumbnail_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ) );
				echo '</div>';
				break;
			default:
				echo '<div class="product-thumbnail em-relative">';
					$this->loop_product_link_open();
						woocommerce_template_loop_product_thumbnail();
					$this->loop_product_link_close();
					do_action( 'ecomus_product_before_loop_thumbnail' );
					do_action( 'ecomus_product_loop_thumbnail_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ) );
				echo '</div>';
				break;
		}
	}


	/**
	 * Featured icons open
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_featured_icons_open() {
		$classes = [];
		if( \Ecomus\WooCommerce\Helper::get_product_card_layout() == '4' ) {
			$classes[] = 'product-featured-icons--bottom';
		}

		if( \Ecomus\WooCommerce\Helper::get_product_card_layout() == '5' ) {
			$classes[] = 'product-featured-icons--no-spacing';
		}

		if( \Ecomus\WooCommerce\Helper::get_product_card_layout() == '6' || \Ecomus\WooCommerce\Helper::get_product_card_layout() == '9' ) {
			$classes[] = 'no-atc em-icon-round';
		}

		if( \Ecomus\WooCommerce\Helper::get_product_card_layout() == '7' || \Ecomus\WooCommerce\Helper::get_product_card_layout() == '8' ) {
			$classes[] = 'no-atc';
		}

		echo '<div class="product-featured-icons product-featured-icons--primary em-absolute em-flex em-flex-align-center em-flex-center ' . esc_attr( implode( ' ', $classes ) ) . '">';
	}

	/**
	 * Featured icons top open
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_featured_icons_top_open() {
		$classes = [];
		if( in_array( \Ecomus\WooCommerce\Helper::get_product_card_layout(), ['3', '4'] ) ) {
			$classes[] = 'product-featured-icons--right';
		}

		if( \Ecomus\WooCommerce\Helper::get_product_card_layout() == '4' ) {
			$classes[] = 'em-icon-round em-icon-tranform-vertical';
		}

		echo '<div class="product-featured-icons product-featured-icons--second no-atc em-absolute em-flex em-flex-column em-flex-align-center em-flex-center ' . esc_attr( implode( ' ', $classes ) ) . '">';
	}

	/**
	 * Featured icons close
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_featured_icons_close() {
		echo '</div>';
	}

	/**
	 * Change add to cart text
	 *
	 * @return void
	 */
	public function product_add_to_cart_text($button_text, $product) {
		if( $product && $product->is_on_backorder() && ! \Ecomus\Helper::is_pre_order_module_active() ) {
			$button_text = esc_html__( 'Pre-order', 'ecomus' );
		}

		if( $product->get_stock_status() === 'outofstock'  && class_exists('CWG_Instock_Notifier') ) {
			$button_text = esc_html__( 'Notify Me', 'ecomus' );
			\Ecomus\Theme::set_prop( 'modals', 'quickadd' );
		}

		return $button_text;
	}

	public function product_add_to_cart_link( $html, $product, $args ) {
		$product_card = \Ecomus\WooCommerce\Helper::get_product_card_layout();

		$button_classes = isset( $args['button_classes'] ) ? $args['button_classes'] : 'em-button-light';

		if( isset( $args['button_classes'] ) ) {
			$button_classes = $args['button_classes'];
		} else {
			$button_classes = 'em-button-light';
			$button_classes =  $product_card == '6' ? 'em-button-outline-dark' : $button_classes;
			$button_classes =  $product_card == '7' || $product_card == '4' || $product_card == '9' ? 'em-button' : $button_classes;

			if( in_array( $product_card, [ '1', '5', '8', 'list' ] ) ) {
				$button_classes .= ' em-button-icon em-tooltip';
			}

			if( $product_card == '8' ) {
				$button_classes .= ' em-button-addtocart--absolute em-absolute';
			}

			$button_classes = apply_filters( 'ecomus_add_to_cart_button_classes', $button_classes );
		}

		$classes[] = 'ecomus-button product-loop-button product-loop-button-atc em-flex-align-center em-flex-center ' . $button_classes;

        $args['class'] .= ' ' . esc_attr( implode( ' ', $classes ) );

		$args['class'] = apply_filters( 'ecomus_add_to_cart_button_class', $args['class'] );

		if( $product->get_type() == 'variable' ) {
			$args['attributes']['data-toggle'] = 'modal';
			$args['attributes']['data-target'] = 'quick-add-modal';
		}

		if( $product->get_stock_status() === 'outofstock'  && class_exists('CWG_Instock_Notifier') ) {
			$args['attributes']['data-toggle'] = 'modal';
			$args['attributes']['data-target'] = 'quick-add-modal';
		}

		$cart_icons = \Ecomus\Helper::get_cart_icons();

        return sprintf(
            '<a href="%s" data-quantity="%s" class="%s" %s data-tooltip="%s">%s <span class="add-to-cart__text">%s</span></a>',
            esc_url( $product->add_to_cart_url() ),
            esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
            esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
            isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_html( $product->add_to_cart_text() ),
            $cart_icons,
            esc_html( $product->add_to_cart_text() )
        );
    }

	public function product_after_shop_loop_item() {
		do_action( 'ecomus_after_shop_loop_item_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ) );
	}

	public static function product_add_to_cart_mobile() {
		woocommerce_template_loop_add_to_cart(
			array(
				'button_classes' => 'em-button-add-to-cart-mobile ' . apply_filters( 'ecomus_add_to_cart_button_mobile_classes', 'em-button-outline-dark' ),
			)
		);
	}

	/**
	 * Rating count open.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_rating() {
		global $product;

		if(Helper::get_option('product_card_empty_rating') && ! $product->get_rating_count() ) {
			return;
		}

		echo '<div class="ecomus-rating em-flex em-flex-align-center">';
			if ( $product->get_rating_count() ) {
				woocommerce_template_loop_rating();
			} else {
			?>
				<div class="star-rating" role="img">
					<span class="max-rating rating-stars">
						<?php echo Icon::inline_svg( 'icon=star' ); ?>
						<?php echo Icon::inline_svg( 'icon=star' ); ?>
						<?php echo Icon::inline_svg( 'icon=star' ); ?>
						<?php echo Icon::inline_svg( 'icon=star' ); ?>
						<?php echo Icon::inline_svg( 'icon=star' ); ?>
					</span>
				</div>
			<?php
			}

			?>
			<div class="review-count"><?php echo '(' . esc_html( $product->get_review_count() ) . ')'; ?></div>
			<?php
		echo '</div>';
	}

	/**
	 * Get product card title
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_card_title() {
		$heading_tag = Helper::get_option( 'product_card_title_heading_tag' );
		$heading_tag = apply_filters( 'ecomus_product_card_title_heading_tag', $heading_tag );
		echo '<'. esc_attr( $heading_tag ) .' class="woocommerce-loop-product__title em-font-normal">';
			$this->loop_product_link_open();
				the_title();
			$this->loop_product_link_close();
		echo '</'. esc_attr( $heading_tag ) .'>';
	}


	/**
	 * Get product taxonomy
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_taxonomy( $taxonomy = 'product_cat' ) {
		$taxonomy = Helper::get_option( 'product_card_taxonomy' );
		if( empty($taxonomy ) ) {
			return;
		}

		$terms = \Ecomus\WooCommerce\Helper::get_product_taxonomy( $taxonomy );

		if ( ! empty( $terms )  ) {
			echo sprintf(
				'<div class="product--cat"><a href="%s">%s</a></div>',
				esc_url( get_term_link( $terms[0] ), $taxonomy ),
				esc_html( $terms[0]->name ) );
		}
	}

	/**
	 * Product Short Description
	 *
	 * @return  void
	 */
	public function short_description() {
		global $product;
		$content = $product->get_short_description();
		if( empty( $content ) ) {
			return;
		}

		$content = wp_trim_words( $content, Helper::get_option( 'product_card_short_description_length' ) );

		echo '<div class="short-description">';
			echo wp_kses_post($content);
		echo '</div>';
	}

	/**
	 * Insert the opening anchor tag for products in the loop.
	 */
	public function loop_product_link_open() {
		global $product;

		$link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );

		echo '<a href="' . esc_url( $link ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link" aria-label="' . esc_attr( get_the_title() )  . '">';
	}

	/**
	 * Insert the closing anchor tag for products in the loop.
	 */
	function loop_product_link_close() {
		echo '</a>';
	}

	/**
	 * Get product card title
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_unit_measure( $price, $product ) {
		if( $product->get_type() == 'variable' || $product->get_type() == 'grouped' ) {
			return $price;
		}

		$unit = maybe_unserialize( get_post_meta( $product->get_id(), 'unit_measure', true ) );

		if ( $unit ) {
			$unit = '<span class="em-price-unit"><span class="divider">/</span> '. esc_html( $unit ) .'</span>';
			$price = $price . $unit;
		}

		return $price;
	}

}