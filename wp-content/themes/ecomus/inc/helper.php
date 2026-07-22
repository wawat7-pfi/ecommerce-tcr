<?php
/**
 * Ecomus helper functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus;

use Ecomus\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ecomus Helper initial
 *
 */
class Helper {
	/**
	 * Post ID
	 *
	 * @var $post_id
	 */
	protected static $post_id = null;

	/**
	 * is_build_elementor
	 *
	 * @var $is_build_elementor
	 */
	protected static $is_build_elementor = null;

	/**
	 * Get theme option
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_option( $name ) {
		return \Ecomus\Options::instance()->get_option( $name );
	}

	/**
	 * Get theme option default
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_option_default( $name ) {
		return \Ecomus\Options::instance()->get_option_default( $name );
	}

	/**
	 * Check is blog
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public static function is_blog() {
		if ( ( is_archive() || is_author() || is_category() || is_home() || is_tag() || is_search() ) && 'post' == get_post_type() ) {
			return true;
		}

		return false;
	}

	/**
	 * Check is catalog
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public static function is_catalog() {
		if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_category() || is_product_tag() || is_tax( 'product_brand' ) || is_tax( 'product_collection' ) || is_tax( 'product_condition' ) || (function_exists('is_product_taxonomy') && is_product_taxonomy() ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get Post ID
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_post_ID() {
		if( isset( self::$post_id )  ) {
			return self::$post_id;
		}

		if ( self::is_catalog() ) {
			self::$post_id = intval( get_option( 'woocommerce_shop_page_id' ) );
		} elseif ( self::is_blog() ) {
			self::$post_id = intval( get_option( 'page_for_posts' ) );
		} else {
			self::$post_id = get_the_ID();
		}

		return self::$post_id;
	}

	/**
	 * Get font url
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_fonts() {
		if ( ! Helper::get_option( 'typo_font_family' ) ) {
			return;
		}

		$cached_fonts = false;
		if ( ! is_customize_preview() && class_exists( '\Ecomus\Cache_Manager' ) ) {
			$cached_fonts = \Ecomus\Cache_Manager::get( \Ecomus\Cache_Manager::CACHE_KEY_FONTS );
		}

		if ( false === $cached_fonts ) {
			ob_start();
			$fonts = array(
				'assets/fonts/i7dMIFdwYjGaAMFtZd_QA1ZeUFuaHjyV.woff2',
				'assets/fonts/i7dMIFdwYjGaAMFtZd_QA1ZeUFWaHg.woff2',
				'assets/fonts/i7dOIFdwYjGaAMFtZd_QA1ZbYFc.woff2',
				'assets/fonts/i7dOIFdwYjGaAMFtZd_QA1ZVYFeCGg.woff2',
			);
			?>
			<style id="ecomus-custom-fonts" type="text/css">
				/* latin-ext */
				@font-face {
					font-family: 'Albert Sans';
					font-style: italic;
					font-weight: 100 900;
					font-display: swap;
					src: url( '<?php echo get_theme_file_uri( 'assets/fonts/i7dMIFdwYjGaAMFtZd_QA1ZeUFuaHjyV.woff2' ); ?>' ) format('woff2');
					unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF;
				}
				/* latin */
				@font-face {
					font-family: 'Albert Sans';
					font-style: italic;
					font-weight: 100 900;
					font-display: swap;
					src: url( '<?php echo get_theme_file_uri( 'assets/fonts/i7dMIFdwYjGaAMFtZd_QA1ZeUFWaHg.woff2' ); ?>' ) format('woff2');
					unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
				}
				/* latin-ext */
				@font-face {
					font-family: 'Albert Sans';
					font-style: normal;
					font-weight: 100 900;
					font-display: swap;
					src: url( '<?php echo get_theme_file_uri( 'assets/fonts/i7dOIFdwYjGaAMFtZd_QA1ZVYFeCGg.woff2' ); ?>' ) format('woff2');
					unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF;
				}
				/* latin */
				@font-face {
					font-family: 'Albert Sans';
					font-style: normal;
					font-weight: 100 900;
					font-display: swap;
					src: url( '<?php echo get_theme_file_uri( 'assets/fonts/i7dOIFdwYjGaAMFtZd_QA1ZbYFc.woff2' ); ?>' ) format('woff2');
					unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
				}

				<?php self::get_stable_google_fonts_url(); ?>
			</style>
			<?php
			foreach ( $fonts as $font ) {
				printf(
					'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>',
					esc_url( get_theme_file_uri( $font ) )
				);
			}
			$cached_fonts = ob_get_clean();

			if ( class_exists( '\Ecomus\Cache_Manager' ) ) {
				\Ecomus\Cache_Manager::set( \Ecomus\Cache_Manager::CACHE_KEY_FONTS, $cached_fonts, WEEK_IN_SECONDS );
			}
		}

		echo $cached_fonts;
	}

	/**
	 * Content limit
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_content_limit( $num_words, $content = '' ) {
		$content = empty( $content ) ? get_the_excerpt() : $content;

		// Strip tags and shortcodes so the content truncation count is done correctly
		$content = strip_tags(
			strip_shortcodes( $content ), apply_filters(
				'ecomus_content_limit_allowed_tags', '<script>,<style>'
			)
		);

		// Remove inline styles / scripts
		$content = trim( preg_replace( '#<(s(cript|tyle)).*?</\1>#si', '', $content ) );

		// Truncate $content to $max_char
		$content = wp_trim_words( $content, $num_words );

		return sprintf( '<p>%s</p>', $content );
	}

	/**
	 * Check is built with elementor
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function is_built_with_elementor() {
		if( isset( self::$is_build_elementor )  ) {
			return self::$is_build_elementor;
		}

		if( ! class_exists('\Elementor\Plugin') ) {
			self::$is_build_elementor = false;
			return self::$is_build_elementor;
		}

		$document = \Elementor\Plugin::$instance->documents->get( self::get_post_ID() );
		if ( ( is_page() && $document && $document->is_built_with_elementor() ) || apply_filters( 'ecomus_is_page_built_with_elementor', false ) ) {
			self::$is_build_elementor = true;
		}

		return self::$is_build_elementor;
	}

	/**
	 * Get an array of posts.
	 *
	 * @static
	 * @access public
	 *
	 * @param array $args Define arguments for the get_posts function.
	 *
	 * @return array
	 */
	public static function customizer_get_posts( $args ) {

		if ( ! is_admin() ) {
			return;
		}

		if ( is_string( $args ) ) {
			$args = add_query_arg(
				array(
					'suppress_filters' => false,
				)
			);
		} elseif ( is_array( $args ) && ! isset( $args['suppress_filters'] ) ) {
			$args['suppress_filters'] = false;
		}

		$args['posts_per_page'] = apply_filters( 'ecomus_customizer_posts_query_limit', 200 );

		$posts = get_posts( $args );

		// Properly format the array.
		$items    = array();
		$source = isset($args['source']) ? $args['source'] : '';
		if( $args['post_type'] == 'ecomus_builder' && $source == 'page') {
			$items[0] = esc_html__( 'Default Footer Global', 'ecomus' );
			$items['page'] = esc_html__( 'Default Footer Page', 'ecomus' );
		} else {
			$items[0] = esc_html__( 'Select an item', 'ecomus' );
		}
		foreach ( $posts as $post ) {
			$items[ $post->ID ] = $post->post_title;
		}
		wp_reset_postdata();

		return $items;

	}

	/**
	 * Button Share
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function share_socials() {
		if ( ! class_exists( '\Ecomus\Addons\Helper' ) && ! method_exists( '\Ecomus\Addons\Helper','share_link' )) {
			return;
		}

		$args = array();
		$socials = (array) Helper::get_option( 'post_sharing_socials' );
		if ( ( ! empty( $socials ) ) ) {
			$output = array();

			foreach ( $socials as $social => $value ) {
				if( $value == 'whatsapp' ) {
					$args['whatsapp_number'] = Helper::get_option( 'post_sharing_whatsapp_number' );
				}

				if( $value == 'facebook' ) {
					$args[$value]['icon'] = 'facebook-f';
				}

				$output[] = \Ecomus\Addons\Helper::share_link( $value, $args );
			}
			return sprintf( '<div class="post__socials-share">%s</div>', implode( '', $output )	);
		};
	}

	/**
	 * Get counter wishlist
	 *
	 * @since 1.0.0
	 *
	 * @param string $account
	 *
	 * @return string
	 */
	public static function wishlist_counter() {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		if ( ! class_exists( 'WCBoost\Wishlist\Helper' ) ) {
			return;
		}

		$wishlist = \WCBoost\Wishlist\Helper::get_wishlist();

		$wishlist_counter = intval( $wishlist->count_items() );

		return sprintf('<span class="header-counter header-wishlist__counter">%s</span>', $wishlist_counter);
	}

	/**
	 * Compatible custom fonts plugin
	 *
	 * @return void
	 */
	public static function get_stable_google_fonts_url() {
		if( defined( 'BSF_CUSTOM_FONTS_POST_TYPE' ) && class_exists( 'BCF_Google_Fonts_Compatibility' ) ) {
			$bcf = \BCF_Google_Fonts_Compatibility::get_instance();
			$bcf_folder = $bcf->get_fonts_folder();
			$bcf_folder = explode( '/', $bcf_folder );
			$bcf_folder = end( $bcf_folder );
			$args                 = array(
				'post_type'      => BSF_CUSTOM_FONTS_POST_TYPE,
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'posts_per_page' => apply_filters( 'ecomus_custom_fonts_query_limit', 100 ),
			);

			$query = new \WP_Query( $args );
			$bsf_fonts = $query->posts;

			if ( ! empty( $bsf_fonts ) ) {
				foreach ( $bsf_fonts as $key => $post_id ) {
					$bsf_font_data = get_post_meta( $post_id, 'fonts-data', true );
					$bsf_font_type = get_post_meta( $post_id, 'font-type', true );
					if( $bsf_font_type == 'google' ) {
						foreach( $bsf_font_data['variations'] as $variation ) {
							$font_files = $bcf->get_fonts_file_url( $bsf_font_data['font_name'], $variation['font_weight'], $variation['font_style'], $variation['id'] );

							foreach( $font_files as $font_file ) {
								if( ! empty( $font_file ) ) {
									$_file = explode( '/', $font_file );
									$_file = end($_file);
									$_file_url = content_url() . '/'. $bcf_folder .'/' . $bsf_font_data['font_name'] . '/' . $_file;

									$type = explode( '.', $_file );
									$type = end( $type );

									$font_weight = filter_var( $variation['font_weight'], FILTER_SANITIZE_NUMBER_INT );

									if( $bcf->get_remote_url_contents( $_file_url ) ) {
										printf( "@font-face {
												font-family: '%s';
												font-style: %s;
												font-weight: %s;
												font-display: swap;
												src: url( '%s' ) format('%s');
											}",
											$bsf_font_data['font_name'],
											$variation['font_style'],
											! empty( $font_weight ) ? $font_weight : 400,
											esc_url( $_file_url ),
											! empty( $type ) ? $type : 'woff2'
										);
									}
								}
							}
						}
					}
				}
			}

			wp_reset_postdata();
		}
	}

	public static function get_cart_icons() {
		if ( \Ecomus\Helper::get_option( 'cart_icon_source' ) == 'icon' ) {
			$cart_icon = !empty(\Ecomus\Helper::get_option( 'cart_icon' )) ? 'shopping-cart' : 'shopping-bag';
			$html = \Ecomus\Icon::inline_svg( 'icon=' . $cart_icon );
		} else {
			if ( ! empty( \Ecomus\Helper::get_option( 'cart_icon_svg' ) ) ) {
				$html = '<span class="ecomus-svg-icon ecomus-svg-icon--custom-cart">' . \Ecomus\Icon::sanitize_svg( \Ecomus\Helper::get_option( 'cart_icon_svg' ) ) . '</span>';
			} else {
				$html = \Ecomus\Icon::inline_svg( 'icon=shopping-bag' );
			}
		}

		return $html;
	}

	public static function is_pre_order_module_active() {
		if( class_exists( 'YITH_Pre_Order' ) ) {
			return true;
		}
		if( get_option( 'ecomus_pre_order' ) === 'yes' ) {
			return true;
		}
		return false;
	}

	/**
	 * Apply the out of stock display mode to a list of products.
	 *
	 * Used by the Up-Sells, Recently Viewed and Related sections on the single
	 * product page so each can independently hide out of stock products or push
	 * them to the end of the list.
	 *
	 * @since 2.4.8
	 *
	 * @param \WC_Product[] $products Array of product objects.
	 * @param string        $mode     One of 'default', 'hide', 'end'.
	 *
	 * @return \WC_Product[]
	 */
	public static function apply_stock_display( $products, $mode ) {
		if ( empty( $products ) || ! is_array( $products ) || ! in_array( $mode, array( 'hide', 'end' ), true ) ) {
			return $products;
		}

		$in_stock     = array();
		$out_of_stock = array();

		foreach ( $products as $product ) {
			if ( is_object( $product ) && method_exists( $product, 'get_stock_status' ) && 'outofstock' === $product->get_stock_status() ) {
				$out_of_stock[] = $product;
			} else {
				$in_stock[] = $product;
			}
		}

		if ( 'hide' === $mode ) {
			return $in_stock;
		}

		// 'end' — keep out of stock products but move them after the in stock ones.
		return array_merge( $in_stock, $out_of_stock );
	}
}
