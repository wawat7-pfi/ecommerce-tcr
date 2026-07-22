<?php
/**
 * WooCommerce product attribute template hooks.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce\Loop;
use Ecomus\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product_Attribute
 */
class Product_Attribute {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Product Attribute Types
	 *
	 * @var $product_attr_types
	 */
	protected static $product_attr_types_second = null;

	/**
	 * Product Attribute
	 *
	 * @var $product_attribute
	 */
	protected static $product_attribute = null;
	protected static $product_attribute_second = null;


	/**
	 * Product Attribute Number
	 *
	 * @var $product_attribute_number
	 */
	protected static $product_attribute_number = null;
	protected static $product_attribute_number_second = null;

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
		add_action( 'woocommerce_before_shop_loop_item', array( $this, 'render_product_card' ), 0 );
	}

	public function render_product_card() {
		switch ( \Ecomus\WooCommerce\Helper::get_product_card_layout() ) {
			case '1':
			case '2':
			case '3':
			case '4':
			case '5':
			case '6':
			case '7':
			case '8':
			case '9':
				if( ! empty( Helper::get_option('product_card_attribute_second') ) ) {
					add_action( 'ecomus_product_loop_thumbnail_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ), array( $this, 'product_attribute_second' ), 4 );
				}
				if( ! empty( Helper::get_option('product_card_attribute') ) ) {
					add_action( 'ecomus_after_shop_loop_item_' . esc_attr( \Ecomus\WooCommerce\Helper::get_product_card_layout() ), array( $this, 'product_attribute' ), 10 );
				}

				break;

			case 'list':
				if( ! empty( Helper::get_option('product_card_attribute_second') ) ) {
					add_action( 'ecomus_after_shop_loop_item_list', array( $this, 'product_attribute_second' ), 15 );
				}
				if( ! empty( Helper::get_option('product_card_attribute') ) ) {
					add_action( 'ecomus_after_shop_loop_item_list', array( $this, 'product_attribute' ), 10 );
				}
				break;

			default:
				break;
		}

		// Product attribute
		add_filter( 'ecomus_product_variation_items_second_class', array( $this, 'product_variation_items_second_class'), 10, 1 );
	}

	public function product_loop_primary_attribute() {
		if( ! empty( apply_filters( 'ecomus_product_loop_primary_attribute', Helper::get_option('product_card_attribute') ) ) ) {
			$this->product_attribute();
		}
	}

	/**
	 * Display product attribute
	 *
	 * @since 1.0
	 */
	public function product_attribute( $product = null ) {
		if( empty( $product ) ) {
			global $product;
		}

		if( $product->get_type() != 'variable' ) {
			return;
		}

		if( $product->get_stock_status() == 'outofstock' ) {
			return;
		}

		if ( empty( self::$product_attribute ) ) {
			self::$product_attribute = Helper::get_option( 'product_card_attribute' );
		}

		if ( empty( self::$product_attribute_number ) ) {
			self::$product_attribute_number = Helper::get_option( 'product_card_attribute_number' );
		}

		$attrs_number = get_post_meta( $product->get_id(), 'ecomus_product_attribute_number', true );
		if( ! empty( $attrs_number ) ) {
			self::$product_attribute_number = $attrs_number;
		}

		$attribute_taxonomy = maybe_unserialize( get_post_meta( $product->get_id(), 'ecomus_product_attribute', true ) );
		$attribute_taxonomy = empty( $attribute_taxonomy ) ? 'pa_' . sanitize_title( self::$product_attribute ) : $attribute_taxonomy;
		if ( $attribute_taxonomy == 'none' ) {
			return;
		}

		$product_attributes         = $product->get_attributes();
		if ( ! $product_attributes ) {
			return;
		}
		$product_attribute = isset( $product_attributes[$attribute_taxonomy] ) ? $product_attributes[$attribute_taxonomy] : '';
		if ( ! $product_attribute ) {
			return;
		}

		$output = '';
		$swatches_args  = [];
		$variation_args  = [];
		if ( function_exists( 'wcboost_variation_swatches' ) ) {
			$swatches_args = self::get_product_data( $product_attribute['name'], $product->get_id() );
		}
		$product_image_id = $product->get_image_id();
		$swatches_args['taxonomy'] = $attribute_taxonomy;

		if ( isset( $product_attribute['variation'] ) && $product_attribute['variation'] ) {
			$variation_ids    = $product->get_children();
			$index            = 1;
			$variations_atts  = array();
			$variations_total = $product->get_variation_attributes();
			$attr_key         = 'attribute_' . $attribute_taxonomy;
			$tax_key          = isset( $variations_total[ $attribute_taxonomy ] ) ? $attribute_taxonomy : ( isset( $variations_total[ urldecode( $attribute_taxonomy ) ] ) ? urldecode( $attribute_taxonomy ) : $attribute_taxonomy );
			$first_var        = ! empty( $variation_ids ) ? wc_get_product( $variation_ids[0] ) : null;
			$first_attrs      = $first_var ? $first_var->get_variation_attributes() : array();
			$use_attrs_branch = empty( $first_attrs[ $attr_key ] ) && ! empty( $variations_total ) && ! empty( $variations_total[ $tax_key ] );

			if ( $use_attrs_branch ) {
				foreach ( $variations_total[ $tax_key ] as $key => $name ) {
					$swatches_args['attribute_name'] = $name;
					$output .= self::swatch_html( $swatches_args, $variation_args );

					if ( $index >= self::$product_attribute_number ) {
						$count_more = count( array_unique( $variations_total[ $tax_key ] ) ) - $index;
						if ( $index < count( array_unique( $variations_total[ $tax_key ] ) ) ) {
							$output .= sprintf( '<a href="%s" class="product-variation-item-more">+%s</a>', esc_url( $product->get_permalink() ), $count_more );
						}
						break;
					}
					$index++;
				}
			} else {
				$has_selected = false;
				foreach ( $variation_ids as $variation_id ) {
					if ( $index > self::$product_attribute_number ) {
						break;
					}
					$variation = wc_get_product( $variation_id );
					if ( ! $variation || $variation->get_stock_status() === 'outofstock' ) {
						continue;
					}
					$v_attributes = $variation->get_variation_attributes();
					if ( empty( $v_attributes[ $attr_key ] ) ) {
						continue;
					}
					$attr_slug = sanitize_title( $v_attributes[ $attr_key ] );
					if ( empty( $attr_slug ) || in_array( $attr_slug, $variations_atts ) ) {
						continue;
					}
					$variations_atts[]            = $attr_slug;
					$swatches_args['attribute_name'] = $attr_slug;

					$variation_args = array();
					$attachment_id  = $variation->get_image_id();
					if ( $attachment_id ) {
						$thumbnail                     = wp_get_attachment_image_src( $attachment_id, 'woocommerce_thumbnail' );
						$variation_args['img_src']     = $thumbnail ? $thumbnail[0] : '';
						$variation_args['img_srcset']  = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $attachment_id ) : '';
						$variation_image               = wp_get_attachment_image_src( $attachment_id, 'full' );
						$variation_args['img_original'] = $variation_image ? $variation_image[0] : '';
					}

					$selected = false;
					if ( $product_image_id == $attachment_id && ! $has_selected ) {
						$has_selected = true;
						$selected     = true;
					}

					$output .= self::swatch_html( $swatches_args, $variation_args, $selected );

					if ( $index >= self::$product_attribute_number && ! empty( $variations_total[ $tax_key ] ) ) {
						$count_more = count( array_unique( $variations_total[ $tax_key ] ) ) - $index;
						if ( $index < count( array_unique( $variations_total[ $tax_key ] ) ) ) {
							$output .= sprintf( '<a href="%s" class="product-variation-item-more">+%s</a>', esc_url( $product->get_permalink() ), $count_more );
						}
						break;
					}
					$index++;
				}
			}
		}

		$classes = intval( Helper::get_option( 'product_card_attribute_image_swap_hover' ) ) ? ' em-variation-hover': '';

		if ( function_exists( 'wcboost_variation_swatches' ) ) {
			if ( Helper::get_option('product_card_attribute_variation_swatches') == 'swatches' ) {
				$classes .= ' wcboost-variation-swatches--' . \WCBoost\VariationSwatches\Admin\Settings::instance()->get_option( 'shape' );
			}
		}

		if( $output ) {
			echo sprintf('<div class="product-variation-items em-flex em-flex-align-center%s">%s</div>',$classes, $output);
		}
	}


	/**
	 * Display product attribute
	 *
	 * @since 1.0
	 * **/
	public function product_attribute_second( $product = null, $type = null ) {
		if( empty( $product ) ) {
			global $product;
		}

		if( $product->get_stock_status() == 'outofstock' ) {
			return;
		}

		if( empty( $type ) ) {
			$type = Helper::get_option( 'product_card_attribute_second_type' );
		}

		if ( empty( self::$product_attr_types_second ) ) {
			self::$product_attr_types_second = Helper::get_option( 'product_card_attribute_second_in' );
		}

		if( empty(self::$product_attr_types_second) ) {
			return;
		}

		if( ! in_array( $product->get_type(), (array) self::$product_attr_types_second ) ) {
			return;
		}

		if ( empty( self::$product_attribute_second ) ) {
			self::$product_attribute_second = Helper::get_option( 'product_card_attribute_second' );
		}

		if ( empty( self::$product_attribute_number_second ) ) {
			self::$product_attribute_number_second = Helper::get_option( 'product_card_attribute_second_number' );
		}

		$attrs_number = get_post_meta( $product->get_id(), 'ecomus_product_attribute_number_second', true );
		if( ! empty( $attrs_number ) ) {
			self::$product_attribute_number_second = $attrs_number;
		}

		$attribute_taxonomy = maybe_unserialize( get_post_meta( $product->get_id(), 'ecomus_product_attribute_second', true ) );
		$attribute_taxonomy = empty( $attribute_taxonomy ) ? 'pa_' . self::$product_attribute_second : $attribute_taxonomy;
		if ( $attribute_taxonomy == 'none' ) {
			return;
		}

		$output = '';
		$swatches_args  = [];
		$variation_args  = [];
		$single_text = ! empty( Helper::get_option( 'product_card_attribute_second_number_single_text' ) ) ? Helper::get_option( 'product_card_attribute_second_number_single_text' ) : esc_html__( 'size available', 'ecomus' );
		$multiple_text = ! empty( Helper::get_option( 'product_card_attribute_second_number_multiple_text' ) ) ? Helper::get_option( 'product_card_attribute_second_number_multiple_text' ) : esc_html__( 'sizes available', 'ecomus' );

		if ( function_exists( 'wcboost_variation_swatches' ) ) {
			$swatches_args = array('type' => 'label');
		}
		$swatches_args['taxonomy'] =   $attribute_taxonomy;
		if( $product->get_type() == 'variable' ) {
			$available_variations = $product->get_variation_attributes();
			if( ! $available_variations || ! isset( $available_variations[$attribute_taxonomy] ) ) {
				return;
			}

			$index = 1;
			$available_attributes = $available_variations[$attribute_taxonomy];

			if( $type == 'number' ) {
				$output = sprintf( // WPCS: XSS OK.
					esc_html( _nx( '%1$s %2$s', '%1$s %3$s', count( $available_attributes ), '%3$s', 'ecomus' ) ),
					number_format_i18n( count( $available_attributes ) ),
					$single_text,
					$multiple_text
				);
			} else {
				if ( taxonomy_exists( $attribute_taxonomy ) ) {
					$terms = wc_get_product_terms(
						$product->get_id(),
						$attribute_taxonomy,
						array(
							'fields' => 'all',
						)
					);
					foreach ( $terms as $term ) {
						if ( in_array( $term->slug, $available_attributes, true ) ) {
							$swatches_args['attribute_name'] = $term->name;
							$output .= self::swatch_html($swatches_args, $variation_args, false, Helper::get_option( 'product_card_attribute_second_tooltip' ));

							if( $index == self::$product_attribute_number_second ) {
								break;
							}

							$index++;
						}
					}
				} else {
					foreach( $available_attributes as $key => $name ) {
						$swatches_args['attribute_name'] = $name;
						$output .= self::swatch_html($swatches_args, $variation_args, false, Helper::get_option( 'product_card_attribute_second_tooltip' ));

						if( $index == self::$product_attribute_number_second ) {
							break;
						}

						$index++;
					}
				}


			}
		} else {
			$product_attributes = $product->get_attributes();
			if ( ! $product_attributes ) {
				return;
			}

			$product_attribute = isset( $product_attributes[$attribute_taxonomy] ) ? $product_attributes[$attribute_taxonomy] : '';
			if ( ! $product_attribute ) {
				return;
			}

			if ( ! $product_attribute['is_taxonomy'] ) {
				$attr_options = $product_attribute->get_options();

				if( $attr_options ) {
					$output = sprintf('<span class="product-variation-attrs--second">%s</span>', implode( ', ', $attr_options ) );
				}
			} else {
				$post_terms = wp_get_post_terms( $product->get_id(), $product_attribute['name'] );
				if( $post_terms ) {
					$index = 1;
					if( $type == 'number' ) {
						$output = sprintf( // WPCS: XSS OK.
							esc_html( _nx( '%1$s %2$s', '%1$s %3$s', count( $post_terms ), '%3$s', 'ecomus' ) ),
							number_format_i18n( count( $post_terms ) ),
							$single_text,
							$multiple_text
						);
					} else {

						foreach ( $post_terms as $term ) {
							if ( is_wp_error( $term ) ) {
								continue;
							}
							$swatches_args['attribute_name'] =  $term->name;
							$swatches_args['term'] = $term;
							$output .= self::swatch_html($swatches_args, $variation_args, false, Helper::get_option( 'product_card_attribute_second_tooltip' ) );

							if( $index == self::$product_attribute_number_second ) {
								break;
							}

							$index++;
						}
					}
				}
			}
		}

		if( $output ) {
			echo sprintf('<div class="%s">%s</div>', esc_attr( apply_filters( 'ecomus_product_variation_items_second_class', 'product-variation-items--'. esc_attr( $type ) .' product-variation-items--second em-absolute em-flex em-flex-center em-flex-align-center text-center em-font-semibold' ) ), $output );
		}
	}

	/**
	 * Print HTML of a single swatch
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function swatch_html( $swatches_args, $variation_args, $selected = false, $tooltip = true ) {
		$html           = '';
		$term           = isset( $swatches_args['term'] ) && $swatches_args['term'] ? $swatches_args['term'] : get_term_by( 'slug', $swatches_args['attribute_name'], $swatches_args['taxonomy'] );
		$key            = is_object( $term ) ? $term->term_id : sanitize_title( $term );
		$_attribute_name = isset( $swatches_args['attribute_name'] ) && isset( $swatches_args['taxonomy'] ) && is_object( get_term_by( 'slug', urldecode($swatches_args['attribute_name']), urldecode($swatches_args['taxonomy'])) ) ? get_term_by( 'slug', urldecode($swatches_args['attribute_name']), urldecode($swatches_args['taxonomy']))->name : urldecode( $swatches_args['attribute_name'] );
		$attribute_name = is_object( $term ) ? $term->name : $_attribute_name;
		$type = 'label';
		if( isset( $swatches_args['type'] ) ) {
			$type           = in_array( $swatches_args['type'], array('select', 'button') ) ? 'label' : $swatches_args['type'];
		}

		if ( isset( $swatches_args['attributes'][ $key ] ) && isset( $swatches_args['attributes'][ $key ][ $type ] ) ) {
			$swatches_value = $swatches_args['attributes'][ $key ][ $type ];
		}  else {
			$swatches_value = is_object( $term ) ? self::get_attribute_swatches( $term->term_id, $type) : '';
		}
		$css_class = $variation_attrs = $data_tooltip = '';
		if( $variation_args ) {
			$variation_json =  wp_json_encode( $variation_args );
			$variation_attrs = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variation_json ) : _wp_specialchars( $variation_json, ENT_QUOTES, 'UTF-8', true );
			$variation_attrs = $variation_args ? sprintf('data-product_variations="%s"', $variation_attrs) : '';
			$css_class = $variation_args  ? ' product-variation-item--attrs' : '';
		}

		if ( intval( Helper::get_option('product_card_tooltip') ) ) {
			if ( $tooltip ) {
				$css_class .= ' em-tooltip';
			}
		}

		$css_class = apply_filters( 'ecomus_product_attribute_'.$type.'_css_class', $css_class, $type, $term->term_id );
		$variation_attrs = apply_filters( 'ecomus_product_attribute_'.$type.'_variation_attrs', $variation_attrs, $type, $term->term_id, $swatches_args );

		if( $selected ) {
			$css_class .= ' selected';
		}
		switch ( $type ) {
			case 'color':
				$html = sprintf(
					'<span class="product-variation-item product-variation-item--color%s" %s data-tooltip="%s" data-tooltip_position="bottom"><span class="product-variation-item__color" style="background-color:%s;"></span></span>',
					esc_attr( $css_class ),
					$variation_attrs,
					esc_attr( $attribute_name ),
					esc_attr( $swatches_value )
				);
				break;

			case 'image':
				if ( $swatches_value ) {
					$gallery_thumbnail                = wc_get_image_size( 'gallery_thumbnail' );
					$gallery_thumbnail_size           = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
					$image = wp_get_attachment_image( $swatches_value, $gallery_thumbnail_size );
					$html  = sprintf(
						'<span class="product-variation-item product-variation-item--image%s" %s data-tooltip="%s" data-tooltip_position="bottom"><span>%s</span></span>',
						esc_attr( $css_class ),
						$variation_attrs,
						esc_attr( $attribute_name ),
						$image
					);
				}

				break;

			default:
				$label = $swatches_value ? $swatches_value : $attribute_name;

				$html  = sprintf(
					'<span class="product-variation-item product-variation-item--label%s" %s data-tooltip="%s" data-tooltip_position="bottom">%s</span>',
					esc_attr( $css_class ),
					$variation_attrs,
					esc_attr( $attribute_name ),
					esc_html( $label )
				);
				break;

		}

		return $html;
	}

	public function get_attribute_swatches( $term_id, $type = 'color' ) {
		if ( class_exists( '\WCBoost\VariationSwatches\Admin\Term_Meta' ) ) {
			$data = \WCBoost\VariationSwatches\Admin\Term_Meta::instance()->get_meta( $term_id, $type );
		} else {
			$data = get_term_meta( $term_id, $type, true );
		}

		return $data;
	}

	/**
	 * Get product type
	 *
	 * @since 1.0.0
	 *
	 * @param string $attribute
	 *
	 * @return object
	 */
	protected function get_product_data( $attribute_name, $product_id ) {
		if ( class_exists( '\WCBoost\VariationSwatches\Admin\Product_Data' ) ) {
			$swatches_meta = \WCBoost\VariationSwatches\Admin\Product_Data::instance()->get_meta( $product_id );
			$attribute_key = sanitize_title( $attribute_name );
			$swatches_args = [];
			if ( $swatches_meta && ! empty( $swatches_meta[ $attribute_key ] ) ) {
				$swatches_args = [
					'type'       => $swatches_meta[ $attribute_key ]['type'],
					'attributes' => $swatches_meta[ $attribute_key ]['swatches'],
				];
			}

			if( ! $swatches_args || ( isset($swatches_args['type'] ) && ! $swatches_args['type'] ) ) {
				$attribute_slug     = wc_attribute_taxonomy_slug( $attribute_name );
				$taxonomies         = wc_get_attribute_taxonomies();
				$attribute_taxonomy = wp_list_filter( $taxonomies, [ 'attribute_name' => $attribute_slug ] );
				$attribute_taxonomy = ! empty( $attribute_taxonomy ) ? array_shift( $attribute_taxonomy ) : null;

				if( $attribute_taxonomy ) {
					$swatches_args = [
						'type'       => $attribute_taxonomy->attribute_type,
					];
				}
			}

			return $swatches_args;
		}
	}

		/**
	 * Product attribute second class
	 */
	public function product_variation_items_second_class( $classes ) {
		if( in_array( \Ecomus\WooCommerce\Helper::get_product_card_layout(), ['2', '3'] ) ) {
			$classes .= ' product-variation-items--white';
		}

		if( in_array( \Ecomus\WooCommerce\Helper::get_product_card_layout(), ['5', '6'] ) ) {
			$classes .= ' product-variation-items--white-transparent';
		}

		return $classes;
	}
}
