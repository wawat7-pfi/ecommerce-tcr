<?php
/**
 * WooCommerce Customizer functions and definitions.
 *
 * @package ecomus
 */

namespace Ecomus\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The ecomus WooCommerce Customizer class
 */
class Customizer {
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
		add_filter( 'ecomus_customize_panels', array( $this, 'get_customize_panels' ) );
		add_filter( 'ecomus_customize_sections', array( $this, 'get_customize_sections' ) );
		add_filter( 'ecomus_customize_settings', array( $this, 'get_customize_settings' ) );
	}

	/**
	 * Adds theme options panels of WooCommerce.
	 *
	 * @since 1.0.0
	 *
	 * @param array $panels Theme options panels.
	 *
	 * @return array
	 */
	public function get_customize_panels( $panels ) {
		$panels['woocommerce'] = array(
			'priority' => 50,
			'title'    => esc_html__( 'Woocommerce', 'ecomus' ),
		);

		$panels['shop'] = array(
			'priority' => 55,
			'title'    => esc_html__( 'Shop', 'ecomus' ),
		);

		if( apply_filters('ecomus_get_single_product_settings', true ) ) {
			$panels['single_product'] = array(
				'priority' => 60,
				'title'    => esc_html__( 'Single Product', 'ecomus' ),
			);
		}

		$panels['vendors'] = array(
			'priority' => 60,
			'title'    => esc_html__( 'Vendors', 'ecomus' ),
		);

		return $panels;
	}

	/**
	 * Adds theme options sections of WooCommerce.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sections Theme options sections.
	 *
	 * @return array
	 */
	public function get_customize_sections( $sections ) {
		// Typography
		$sections['typo_catalog'] = array(
			'title'    => esc_html__( 'Product Catalog', 'ecomus' ),
			'panel'    => 'typography',
		);
		$sections['typo_product'] = array(
			'title'    => esc_html__( 'Single Product', 'ecomus' ),
			'panel'    => 'typography',
		);

		// Cart Page
		$sections['woocommerce_cart'] = array(
			'title'    => esc_html__( 'Cart Page', 'ecomus' ),
			'panel'    => 'woocommerce',
		);

		// Mini Cart
		$sections['mini_cart'] = array(
			'title'    => esc_html__( 'Mini Cart', 'ecomus' ),
			'panel'    => 'woocommerce',
		);

		// Compare Page
		$sections['compare_page'] = array(
			'title'    => esc_html__( 'Compare Page', 'ecomus' ),
			'panel'    => 'woocommerce',
		);

		// Header in Shop
		$sections['header_shop'] = array(
			'title'    => esc_html__( 'Header', 'ecomus' ),
			'panel'    => 'shop',
		);
	
		if( apply_filters( 'ecomus_shop_header_elementor', true ) ) {
			// Page Header
			$sections['shop_header'] = array(
				'title'    => esc_html__( 'Page Header', 'ecomus' ),
				'panel'    => 'shop',
			);
		}

		if( apply_filters( 'ecomus_top_categories_elementor', true ) ) {
			// Top Categories
			$sections['shop_top_categories'] = array(
				'title'    => esc_html__( 'Top Categories', 'ecomus' ),
				'panel'    => 'shop',
			);
		}

		if( apply_filters( 'ecomus_catalog_toolbar_elementor', true ) ) {
			// Catalog Toolbar
			$sections['shop_catalog_toolbar'] = array(
				'title'    => esc_html__( 'Catalog Toolbar', 'ecomus' ),
				'panel'    => 'shop',
			);
		}

		if( apply_filters( 'ecomus_taxonomy_description_elementor', true ) ) {
			// Catalog Toolbar
			$sections['taxonomy_description'] = array(
				'title'    => esc_html__( 'Taxonomy Description', 'ecomus' ),
				'panel'    => 'shop',
			);
		}

		if( apply_filters( 'ecomus_product_catalog_elementor', true ) ) {
			// Product Catalog
			$sections['product_catalog'] = array(
				'title'    => esc_html__( 'Product Catalog', 'ecomus' ),
				'panel'    => 'shop',
			);
		}

		// Product Card
		$sections['product_card'] = array(
			'title'    => esc_html__( 'Product Card', 'ecomus' ),
			'panel'    => 'shop',
		);

		// Product Notifications
		$sections['product_notifications'] = array(
			'title'    => esc_html__( 'Product Notifications', 'ecomus' ),
			'panel'    => 'shop',
		);

		// Badges
		$sections['badges'] = array(
			'title'    => esc_html__( 'Badges', 'ecomus' ),
			'panel'    => 'shop',
		);

		// Single Product
		$sections['product'] = array(
			'title'    => esc_html__( 'Product Layout', 'ecomus' ),
			'panel'    => 'single_product',
		);

		// Single Product Sidebar
		$sections['product_sidebar'] = array(
			'title'    => esc_html__( 'Product Sidebar', 'ecomus' ),
			'panel'    => 'single_product',
		);

		$sections['product_gallery'] = array(
			'title'    => esc_html__( 'Product Gallery', 'ecomus' ),
			'panel'    => 'single_product',
		);

		// Single Badges
		$sections['product_badges'] = array(
			'title'    => esc_html__( 'Badges', 'ecomus' ),
			'panel'    => 'single_product',
		);

		// Ask a question
		$sections['product_ask_question'] = array(
			'title'    => esc_html__( 'Ask a question', 'ecomus' ),
			'panel'    => 'single_product',
		);

		// Delivery Return
		$sections['product_delivery_return'] = array(
			'title'    => esc_html__( 'Delivery Return', 'ecomus' ),
			'panel'    => 'single_product',
		);

		// Share
		$sections['product_share'] = array(
			'title'    => esc_html__( 'Product Share', 'ecomus' ),
			'panel'    => 'single_product',
		);

		// Product tabs
		$sections['product_tabs'] = array(
			'title'    => esc_html__( 'Product Tabs', 'ecomus' ),
			'panel'    => 'single_product',
		);

		// Upsells Product
		$sections['upsells_products'] = array(
			'title'    => esc_html__( 'Up-Sells Products', 'ecomus' ),
			'panel'    => 'single_product',
		);

		// Related Product
		$sections['related_products'] = array(
			'title'    => esc_html__( 'Related Products', 'ecomus' ),
			'panel'    => 'single_product',
		);

		// Recently Viewed Product
		$sections['recently_viewed_products'] = array(
			'title'    => esc_html__( 'Recently Viewed Products', 'ecomus' ),
			'panel'    => 'single_product',
		);

		$sections['vendors_store_style'] = array(
			'title'    => esc_html__( 'Store Style', 'ecomus' ),
			'panel'    => 'vendors',
		);

		return $sections;
	}

	/**
	 * Adds theme options of WooCommerce.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields Theme options fields.
	 *
	 * @return array
	 */
	public function get_customize_settings( $settings ) {
		// Product Compare Page
		if ( function_exists('wcboost_products_compare') ) {
			$columns = [
				'rating'      => esc_html__( 'Rating', 'ecomus' ),
				'price'       => esc_html__( 'Price', 'ecomus' ),
				'stock'       => esc_html__( 'Availability', 'ecomus' ),
				'sku'         => esc_html__( 'SKU', 'ecomus' ),
				'dimensions'  => esc_html__( 'Dimensions', 'ecomus' ),
				'weight'      => esc_html__( 'Weight', 'ecomus' ),
				'add-to-cart' => esc_html__( 'Add To Cart', 'ecomus' ),
			];

			$columns = array_merge( $columns, $this->get_product_attributes() );
			if( isset( $columns[''] ) ) {
				unset($columns['']);
			}
			$settings['compare_page'] = array(
				'compare_page_columns'                => array(
					'type'     => 'multicheck',
					'label'    => esc_html__('Table Columns', 'ecomus'),
					'default'  => array('rating', 'price', 'stock', 'sku', 'dimensions', 'weight', 'add-to-cart'),
					'choices'  => $columns,
				)
			);
		}

		// Typography - catalog.
		$settings['typo_catalog'] = array(
			'typo_catalog_page_title'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Page Header Title', 'ecomus' ),
				'description' => esc_html__( 'Customize the font of page header title', 'ecomus' ),
				'default'     => array(
					'font-family'    => 'Albert Sans',
					'variant'        => 'regular',
					'font-size'      => '42px',
					'line-height'    => '',
					'text-transform' => 'none',
					'color'          => '#000000',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing' => '',
				),
				'choices'   => \Ecomus\Options::customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.page-header--shop .page-header__title',
					),
				),
			),
			'typo_catalog_page_description'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Page Header Description', 'ecomus' ),
				'description' => esc_html__( 'Customize the font of page header description', 'ecomus' ),
				'default'     => array(
					'font-family'    => 'Albert Sans',
					'variant'        => 'regular',
					'font-size'      => '16px',
					'line-height'    => '',
					'text-transform' => 'none',
					'color'          => '#545454',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing' => '',
				),
				'choices'   => \Ecomus\Options::customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.page-header--shop .page-header__description',
					),
				),
			),
			'typo_catalog_product_title'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Product Name', 'ecomus' ),
				'description' => esc_html__( 'Customize the font of product name', 'ecomus' ),
				'default'     => array(
					'font-family'    => 'Albert Sans',
					'variant'        => 'regular',
					'font-size'      => '16px',
					'line-height'    => '',
					'text-transform' => 'none',
					'color'          => '#000000',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing' => '',
				),
				'choices'   => \Ecomus\Options::customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'ul.products li.product h2.woocommerce-loop-product__title a',
					),
				),
			),
		);

		// Typography - product.
		$settings['typo_product'] = array(
			'typo_product_title'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Product Name', 'ecomus' ),
				'description' => esc_html__( 'Customize the font of product name', 'ecomus' ),
				'default'     => array(
					'font-family'    => 'Albert Sans',
					'variant'        => 'regular',
					'font-size'      => '28px',
					'line-height'    => '',
					'text-transform' => 'none',
					'color'          => '#000000',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing' => '',
				),
				'choices'   => \Ecomus\Options::customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.single-product div.product .product-gallery-summary h1.product_title',
					),
				),
			),
		);

		// Product Catalog
		$settings['product_catalog'] = array(
			'product_catalog_full_width' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Full Width', 'ecomus' ),
				'default' => false,
			),
			'product_catalog_sidebar' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Sidebar', 'ecomus' ),
				'description'     => esc_html__( 'Go to appearance > widgets find to catalog sidebar to edit your sidebar', 'ecomus' ),
				'default'         => 'no-sidebar',
				'choices'         => array(
					'content-sidebar' => esc_html__( 'Right Sidebar', 'ecomus' ),
					'sidebar-content' => esc_html__( 'Left Sidebar', 'ecomus' ),
					'no-sidebar'      => esc_html__( 'No Sidebar', 'ecomus' ),
				),
			),
			'product_catalog_hr'  => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_catalog_pagination' => array(
				'type'    => 'radio',
				'label'   => esc_html__( 'Pagination Type', 'ecomus' ),
				'default' => 'numeric',
				'choices' => array(
					'numeric'  => esc_attr__( 'Numeric', 'ecomus' ),
					'infinite' => esc_attr__( 'Infinite Scroll', 'ecomus' ),
					'loadmore' => esc_attr__( 'Load More', 'ecomus' ),
				),
			),
			'product_catalog_pagination_ajax_url_change' => array(
				'type'            => 'checkbox',
				'label'           => esc_html__( 'Change the URL after page loaded', 'ecomus' ),
				'default'         => true,
				'active_callback' => array(
					array(
						'setting'  => 'product_catalog_pagination',
						'operator' => '!=',
						'value'    => 'numeric',
					),
				),
			),
		);

		// Product Card
		$settings['product_card'] = array(
			'image_rounded_shape_product_card'       => array(
				'type'            => 'radio-buttonset',
				'label'           => esc_html__( 'Image Border Radius Shape', 'ecomus' ),
				'default'         => '',
				'choices'         => array(
					'' 			=> esc_html__( 'Default', 'ecomus' ),
					'round'  	=> esc_html__( 'Round', 'ecomus' ),
					'custom'  	=> esc_html__( 'Custom', 'ecomus' ),
				),
			),
			'image_rounded_number_product_card'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Border Radius(px)', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'image_rounded_shape_product_card',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'product_card_images_hr' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),
			'product_card_layout' => array(
				'type'    => 'select',
				'label'   => esc_html__( 'Product Layout', 'ecomus' ),
				'default' => '1',
				'choices' => Helper::product_card_layout_select(),
			),
			'product_card_hover' => array(
				'type'              => 'select',
				'label'             => esc_html__( 'Product Hover', 'ecomus' ),
				'description'       => esc_html__( 'Product hover animation.', 'ecomus' ),
				'default'           => '',
				'choices'           => array(
					''                 => esc_html__( 'Standard', 'ecomus' ),
					'slider'           => esc_html__( 'Slider', 'ecomus' ),
					'zoom'             => esc_html__( 'Zoom', 'ecomus' ),
					'fadein'           => esc_html__( 'Fadein', 'ecomus' ),
				),
				'priority'    => 10,
			),
			'product_card_hr' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),
			'product_card_quickadd' => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Quick Add button', 'ecomus' ),
				'description' => esc_html__( 'Disable this setting to return to the default button', 'ecomus' ),
				'default'     => true,
			),
			'product_card_wishlist' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Wishlist button', 'ecomus' ),
				'default' => true,
			),
			'product_card_compare' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Compare button', 'ecomus' ),
				'default' => true,
			),
			'product_card_quick_view' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Quick view button', 'ecomus' ),
				'default' => true,
			),
			'featured_button_rounded_shape_product_card'       => array(
				'type'            => 'radio-buttonset',
				'label'           => esc_html__( 'Featured Button Border Radius Shape', 'ecomus' ),
				'default'         => '',
				'choices'         => array(
					'' 			=> esc_html__( 'Default', 'ecomus' ),
					'circle'  	=> esc_html__( 'Circle', 'ecomus' ),
					'custom'  	=> esc_html__( 'Custom', 'ecomus' ),
				),
			),
			'featured_button_rounded_number_product_card'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Border Radius(px)', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'featured_button_rounded_shape_product_card',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'product_sale_coundown_hr' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),
			'badges_sale_countdown'          => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Show Countdown', 'ecomus' ),
				'default'     => true,
			),
			'product_card_sale_display_type' => array(
				'type'    => 'select',
				'label'   => esc_html__( 'Sale Display Type', 'ecomus' ),
				'default' => 'countdown',
				'choices' => array(
					'countdown'   => esc_html__( 'Countdown', 'ecomus' ),
					'flash-sale'  => esc_html__( 'Flash Sale Marquee', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'badges_sale_countdown',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'sale_display_flash_sale_speed' => array(
				'type'            => 'number',
				'label'     	  => esc_html__( 'Speed', 'ecomus' ),
				'description'     => esc_html__( 'Customize flash sale speed (Example: 0.25)', 'ecomus' ),
				'default'         => 0.1,
				'choices'  => [
					'min'  => 0,
					'max'  => 1,
					'step' => 0.1,
				],
				'active_callback' => array(
					array(
						'setting'  => 'product_card_sale_display_type',
						'operator' => '==',
						'value'    => 'flash-sale',
					),
					array(
						'setting'  => 'badges_sale_countdown',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_card_tooltip'          => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Show Tooltip', 'ecomus' ),
				'default'     => true,
			),
			'product_card_taxonomy_hr' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),
			'product_card_taxonomy'               => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Product Taxonomy', 'ecomus' ),
				'default'         => '',
				'choices'         => array(
					''   => esc_html__( 'None', 'ecomus' ),
					'product_cat'   => esc_html__( 'Category', 'ecomus' ),
					'product_brand' => esc_html__( 'Brand', 'ecomus' ),
				),
			),
			'product_card_rating_hr' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),
			'product_card_rating'          => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Show Rating', 'ecomus' ),
				'default'     => true,
			),
			'product_card_empty_rating'          => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Hide Empty Rating', 'ecomus' ),
				'default'     => false,
			),
			'product_card_title_hr' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),
			'product_card_title_heading_tag' => array(
				'type'               => 'select',
				'label'              => esc_html__( 'Product Title HTML Tag', 'ecomus' ),
				'default'            => 'h2',
				'choices'            => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				),
			),
			'product_card_title_lines' => array(
				'type'               => 'select',
				'label'              => esc_html__( 'Product Title in', 'ecomus' ),
				'default'            => '',
				'choices'            => array(
					''                 => esc_html__( 'Default', 'ecomus' ),
					'1'                 => esc_html__( '1 line', 'ecomus' ),
					'2'                 => esc_html__( '2 lines', 'ecomus' ),
					'3'                 => esc_html__( '3 lines', 'ecomus' ),
					'4'                 => esc_html__( '4 lines', 'ecomus' ),
				),
			),
			'product_card_summary_hr_1' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'product_card_layout',
						'operator' => 'in',
						'value'    => array('1', '2', '3', '4', '5', '6', '7'),
					),
				),
			),
			'product_card_summary' => array(
				'type'              => 'select',
				'label'             => esc_html__( 'Product Summary Alignment', 'ecomus' ),
				'default'           => 'flex-start',
				'choices'           => array(
					'flex-start'   => esc_html__( 'Left', 'ecomus' ),
					'center' => esc_html__( 'Center', 'ecomus' ),
					'flex-end'  => esc_html__( 'Right', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_card_layout',
						'operator' => 'in',
						'value'    => array('1', '2', '3', '4', '5', '6', '7'),
					),
				),
			),
			'product_card_hr_1' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),
			'product_card_attribute' => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Primary Product Attribute', 'ecomus' ),
				'default'     => 'none',
				'choices'     => $this->get_product_attributes(),
				'description' => esc_html__( 'Show primary product attribute in the product card', 'ecomus' ),
			),
			'product_card_attribute_number' => array(
				'type'            => 'number',
				'description'     => esc_html__( 'Primary Product Attribute Number', 'ecomus' ),
				'default'         => 4,
				'choices'  => array(
					'min'  => 1,
				),
			),
			'product_card_attribute_variation_swatches'                => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Variation Swatches Style', 'ecomus' ),
				'default'     => 'default',
				'choices'     => array(
					'default'  => esc_html__( 'By the Theme', 'ecomus' ),
					'swatches'  => esc_html__( 'By Variation Swatches plugin', 'ecomus' ),
				),
			),
			'product_card_attribute_image_swap_hover' => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Enable Image Swap on Hover', 'ecomus' ),
				'default'     => 1,
			),
			'product_card_hr_2' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),
			'product_card_attribute_second' => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Second Product Attribute', 'ecomus' ),
				'default'     => 'none',
				'choices'     => $this->get_product_attributes(),
				'description' => esc_html__( 'Show second product attribute in the product card', 'ecomus' ),
			),
			'product_card_attribute_second_type' => array(
				'type'    => 'radio',
				'label'   => esc_html__( 'Type', 'ecomus' ),
				'default' => 'list',
				'choices' => array(
					'list'   => esc_attr__( 'List', 'ecomus' ),
					'number' => esc_attr__( 'Number', 'ecomus' ),
				),
			),
			'product_card_attribute_second_number' => array(
				'type'            => 'number',
				'description'     => esc_html__( 'Second Product Attribute Number', 'ecomus' ),
				'default'         => 4,
				'choices'  => array(
					'min'  => 1,
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_card_attribute_second_type',
						'operator' => '==',
						'value'    => 'list',
					),
				),
			),
			'product_card_attribute_second_number_single_text' => array(
				'type'            => 'text',
				'description'     => esc_html__( 'Second Product Attribute Number Single Text', 'ecomus' ),
				'default'         => esc_html__( 'size available', 'ecomus' ),
				'active_callback' => array(
					array(
						'setting'  => 'product_card_attribute_second_type',
						'operator' => '==',
						'value'    => 'number',
					),
				),
			),
			'product_card_attribute_second_number_multiple_text' => array(
				'type'            => 'text',
				'description'     => esc_html__( 'Second Product Attribute Number Multiple Text', 'ecomus' ),
				'default'         => esc_html__( 'sizes available', 'ecomus' ),
				'active_callback' => array(
					array(
						'setting'  => 'product_card_attribute_second_type',
						'operator' => '==',
						'value'    => 'number',
					),
				),
			),
			'product_card_attribute_second_in' => array(
				'type'        => 'multicheck',
				'label'       => esc_html__( 'Product Attribute In', 'ecomus' ),
				'default'     => array('variable', 'simple'),
				'choices'  => array(
					'variable' => esc_html__( 'Variable Product', 'ecomus' ),
					'simple'   => esc_html__( 'Simple Product', 'ecomus' ),
				),
			),
			'product_card_attribute_second_tooltip'          => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Show Tooltip', 'ecomus' ),
				'default'     => true,
			),
			'product_card_hr_3' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),
			'product_card_short_description_length' => array(
				'type'            => 'number',
				'label'           => esc_html__('Short Description Length', 'ecomus'),
				'description'     => esc_html__( 'The number of words of the short description', 'ecomus' ),
				'default'         => 30,
			),
		);

		// WCFM
		if ( class_exists( 'WCFMmp' ) ) {
			$settings['product_card']['product_card_vendor_name_custom'] = array(
				'type'            => 'custom',
				'default'         => '<hr/>',
				'priority'    => 42,
			);
			$settings['product_card']['product_card_vendor_name'] = array(
				'type'            => 'select',
				'label'           => esc_html__( 'Vendor Name', 'ecomus' ),
				'default'         => 'avatar',
				'choices'         => array(
					'none' => esc_html__( 'None', 'ecomus' ),
					'avatar' => esc_html__( 'Avatar - Vendor Name', 'ecomus' ),
					'text' => esc_html__( 'By - Vendor Name', 'ecomus' ),
				),
				'priority'    => 42,
			);
			$settings['product_card']['product_card_vendor_position'] = array(
				'type'            => 'select',
				'label'           => esc_html__( 'Vendor Position', 'ecomus' ),
				'default'         => 'after-price',
				'choices'         => array(
					'after-price' => esc_html__( 'After Price', 'ecomus' ),
					'after-thumbnail' => esc_html__( 'After Thumbnail', 'ecomus' ),
				),
				'priority'    => 42,
			);
			$settings['vendors_store_style']['vendor_store_style_theme'] = array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Enable Style From Theme', 'ecomus' ),
				'description' => esc_html__( 'Enable the store list and store page style from theme.', 'ecomus' ),
				'default' => true,
			);
		}

		// Vendor
		if ( class_exists( 'WeDevs_Dokan' ) ) {
			$settings['product_card'] = array_merge(
				$settings['product_card'],
				array(
					'product_card_vendor_custom'      => array(
						'type'     => 'custom',
						'default'  => '<hr/>',
					),
					'product_card_vendor_name'     => array(
						'type'            => 'select',
						'label'           => esc_html__( 'Vendor Name', 'ecomus' ),
						'default'         => 'avatar',
						'choices'         => array(
							'none' => esc_html__( 'None', 'ecomus' ),
							'avatar' => esc_html__( 'Avatar - Vendor Name', 'ecomus' ),
							'text' => esc_html__( 'By - Vendor Name', 'ecomus' ),
						),
					),
					'product_card_vendor_position'     => array(
						'type'            => 'select',
						'label'           => esc_html__( 'Vendor Position', 'ecomus' ),
						'default'         => 'after-price',
						'choices'         => array(
							'after-price' => esc_html__( 'After Price', 'ecomus' ),
							'after-thumbnail' => esc_html__( 'After Thumbnail', 'ecomus' ),
						),
					),
				)
			);
		};

		// Product Notifications
		$settings['product_notifications'] = array(
			'added_to_cart_notice'                => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Added to Cart Notice', 'ecomus' ),
				'description' => esc_html__( 'Display a notification when a product is added to cart.', 'ecomus' ),
				'default'     => 'none',
				'choices'     => array(
					'mini'  => esc_html__( 'Open mini cart', 'ecomus' ),
					'none'  => esc_html__( 'None', 'ecomus' ),
				),
			),
			'added_to_wishlist_custom'                 => array(
				'type'     => 'custom',
				'default'  => '<hr/>',
			),
			'added_to_wishlist_notice' => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Added to Wishlist Notification', 'ecomus' ),
				'description' => esc_html__( 'Display a notification when a product is added to wishlist', 'ecomus' ),
				'section'     => 'product_notifications',
				'default'     => 0,
			),

			'wishlist_notice_auto_hide'   => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Wishlist Notification Auto Hide', 'ecomus' ),
				'description'     => esc_html__( 'How many seconds you want to hide the notification.', 'ecomus' ),
				'section'         => 'product_notifications',
				'active_callback' => array(
					array(
						'setting'  => 'added_to_wishlist_notice',
						'operator' => '==',
						'value'    => 1,
					),
				),
				'default'         => 3,
			),
			'added_to_compare_custom'                 => array(
				'type'     => 'custom',
				'default'  => '<hr/>',
			),
			'added_to_compare_notice' => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Added to Compare Notification', 'ecomus' ),
				'description' => esc_html__( 'Display a notification when a product is added to compare', 'ecomus' ),
				'section'     => 'product_notifications',
				'default'     => 0,
			),

			'compare_notice_auto_hide'   => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Compare Notification Auto Hide', 'ecomus' ),
				'description'     => esc_html__( 'How many seconds you want to hide the notification.', 'ecomus' ),
				'section'         => 'product_notifications',
				'active_callback' => array(
					array(
						'setting'  => 'added_to_compare_notice',
						'operator' => '==',
						'value'    => 1,
					),
				),
				'default'         => 3,
			),
		);

		// Badges
		$settings['badges'] = array(
			'badges_sale'          => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Sale Badge', 'ecomus' ),
				'description' => esc_html__( 'Display a badge for sale products.', 'ecomus' ),
				'default'     => true,
			),
			'badges_sale_type'     => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Type', 'ecomus' ),
				'default'         => 'percent',
				'choices'         => array(
					'percent'        => esc_html__( 'Percentage', 'ecomus' ),
					'text'           => esc_html__( 'Text', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'badges_sale',
						'operator' => '=',
						'value'    => true,
					),
				),
			),
			'badges_sale_bg'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background', 'ecomus' ),
				'default'         => '#FC5732',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_sale',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .onsale',
						'property' => 'background-color',
					),
				),
			),
			'badges_sale_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'ecomus' ),
				'default'         => '#ffffff',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_sale',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .onsale',
						'property' => 'color',
					),
				),
			),
			'badges_hr_2'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'badges_new'           => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'New Badge', 'ecomus' ),
				'description' => esc_html__( 'Display a badge for new products.', 'ecomus' ),
				'default'     => true,
			),
			'badges_newness'       => array(
				'type'            => 'number',
				'description'     => esc_html__( 'Display the "New" badge for how many days?', 'ecomus' ),
				'tooltip'         => esc_html__( 'You can also add the NEW badge to each product in the Advanced setting tab of them.', 'ecomus' ),
				'default'         => 3,
				'active_callback' => array(
					array(
						'setting'  => 'badges_new',
						'operator' => '=',
						'value'    => true,
					),
				),
			),
			'badges_new_bg'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background', 'ecomus' ),
				'default'         => '#48D4BB',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_new',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .new',
						'property' => 'background-color',
					),
				),
			),
			'badges_new_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'ecomus' ),
				'default'         => '#ffffff',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_new',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .new',
						'property' => 'color',
					),
				),
			),
			'badges_hr_3'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'badges_featured'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Featured Badge', 'ecomus' ),
				'description' => esc_html__( 'Display a badge for featured products.', 'ecomus' ),
				'default'     => true,
			),
			'badges_featured_bg'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background', 'ecomus' ),
				'default'         => '#ff7316',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_featured',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .featured',
						'property' => 'background-color',
					),
				),
			),
			'badges_featured_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'ecomus' ),
				'default'         => '#ffffff',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_featured',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .featured',
						'property' => 'color',
					),
				),
			),
			'badges_hr_4'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'badges_soldout'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Sold Out Badge', 'ecomus' ),
				'description' => esc_html__( 'Display a badge for out of stock products.', 'ecomus' ),
				'default'     => true,
			),
			'badges_soldout_position'     => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Position', 'ecomus' ),
				'default'         => 'center',
				'choices'         => array(
					'top' => esc_html__( 'Top', 'ecomus' ),
					'center' => esc_html__( 'Center', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'badges_sale',
						'operator' => '=',
						'value'    => true,
					),
				),
			),
			'badges_soldout_bg'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background', 'ecomus' ),
				'default'         => '#F2F2F2',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_soldout',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .sold-out, .woocommerce-badges.woocommerce-badges.sold-out--center.sold-out',
						'property' => 'background-color',
					),
				),
			),
			'badges_soldout_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'ecomus' ),
				'default'         => '#000000',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_soldout',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .sold-out',
						'property' => 'color',
					),
				),
			),
			'badges_hr_5'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'badges_pre_order'           => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Pre-Order Badge', 'ecomus' ),
				'description' => esc_html__( 'Display a badge for pre-order products.', 'ecomus' ),
				'default'     => true,
			),
			'badges_pre_order_bg'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background', 'ecomus' ),
				'default'         => '#55a653',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_pre_order',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .pre-order',
						'property' => 'background-color',
					),
				),
			),
			'badges_pre_order_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'ecomus' ),
				'default'         => '#ffffff',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_pre_order',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .pre-order',
						'property' => 'color',
					),
				),
			),
			'badges_custom_badge'       => array(
				'type'    => 'custom',
				'default' => '<hr/><h3>' . esc_html__( 'Custom Badge', 'ecomus' ) . '</h3>',
			),

			'badges_custom_bg'    => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background', 'ecomus' ),
				'default'         => '',
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .custom',
						'property' => 'background-color',
					),
				),
			),

			'badges_custom_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color', 'ecomus' ),
				'default'         => '',
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .custom ',
						'property' => 'color',
					),
				),
			),

		);

		// Header in shop.
		$settings['header_shop'] = array(
			'shop_header_background' => array(
				'type' => 'select',
				'label' => esc_html__( 'Header Background', 'ecomus' ),
				'default' => '',
				'choices' => array(
					'' => esc_html__( 'Default', 'ecomus' ),
					'transparent' => esc_html__( 'Transparent', 'ecomus' ),
				),
			),
			'shop_header_text_color' => array(
				'type' => 'select',
				'label' => esc_html__( 'Header Text Color', 'ecomus' ),
				'default' => '',
				'choices' => array(
					'' => esc_html__( 'Default', 'ecomus' ),
					'light'   => esc_html__( 'Light', 'ecomus' ),
					'dark'   => esc_html__( 'Dark', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'shop_header_background',
						'operator' => '==',
						'value'    => 'transparent',
					),
				),
			),
			'shop_header_logo_type' => array(
				'type' => 'select',
				'label' => esc_html__( 'Header Logo Type', 'ecomus' ),
				'default' => '',
				'choices' => array(
					'' => esc_html__( 'Default', 'ecomus' ),
					'image' => esc_html__( 'Image', 'ecomus' ),
					'text'  => esc_html__( 'Text', 'ecomus' ),
					'svg'   => esc_html__( 'SVG', 'ecomus' ),
				),
			),
			'shop_header_logo_text'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Logo Text', 'ecomus' ),
				'default'         => 'Ecomus',
				'active_callback' => array(
					array(
						'setting'  => 'shop_header_logo_type',
						'operator' => '==',
						'value'    => 'text',
					),
				),
			),
			'shop_header_logo_svg'       => array(
				'type'            => 'textarea',
				'label'           => esc_html__( 'Logo SVG', 'ecomus' ),
				'description'     => esc_html__( 'Paste SVG code of your logo here', 'ecomus' ),
				'sanitize_callback' => 'Ecomus\Icon::sanitize_svg',
				'output'          => array(
					array(
						'element' => '.ecomus-catalog-page .site-header .header-logo',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'shop_header_logo_type',
						'operator' => '==',
						'value'    => 'svg',
					),
				),
			),
			'shop_header_logo_image'           => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Logo', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'shop_header_logo_type',
						'operator' => '==',
						'value'    => 'image',
					),
				),
			),
			'shop_header_logo_image_light'           => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Logo Light', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'shop_header_logo_type',
						'operator' => '==',
						'value'    => 'image',
					),
				),
			),
			'shop_header_logo_dimension' => array(
				'type'            => 'dimensions',
				'label'           => esc_html__( 'Logo Dimension', 'ecomus' ),
				'default'         => array(
					'width'  => 'auto',
					'height' => 'auto',
				),
				'active_callback' => array(
					array(
						'setting'  => 'shop_header_logo_type',
						'operator' => 'in',
						'value'    => [ 'image', 'svg' ],
					),
				),
			),
		);

		// Page Header.
		$settings['shop_header'] = array(
			'shop_header' => array(
				'type'        => 'toggle',
				'default'     => true,
				'label'       => esc_html__('Enable Page Header', 'ecomus'),
				'description' => esc_html__('Enable to show a shop header for the shop below the site header', 'ecomus'),
			),
			'shop_header_hr' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'shop_header_els' => array(
				'type'     => 'multicheck',
				'label'    => esc_html__('Elements', 'ecomus'),
				'default'  => array( 'title', 'description' ),
				'choices'  => array(
					'title'      => esc_html__('Title', 'ecomus'),
					'breadcrumb' => esc_html__('BreadCrumb', 'ecomus'),
					'description' => esc_html__('Description', 'ecomus'),
				),
				'description'     => esc_html__('Select which elements you want to show.', 'ecomus'),
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'shop_header_number_lines'                      => array(
				'type'            => 'number',
				'label'           => esc_html__('Description Number Lines', 'ecomus'),
				'default'         => 5,
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'shop_header_els',
						'operator' => 'in',
						'value'    => 'description',
					),
				),
			),
			'shop_header_hr_1' => array(
				'type'            => 'custom',
				'default'         => '<hr/><h3>' . esc_html__('Custom', 'ecomus') . '</h3>',
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'shop_header_background_image'          => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Background Image', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'shop_header_background_overlay' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Overlay', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--shop::before',
						'property' => 'background-color',
					),
				),
			),
			'shop_header_title_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Title Color', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'shop_header_els',
						'operator' => 'in',
						'value'    => 'title',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--shop .page-header__title',
						'property' => 'color',
					),
				),
			),
			'shop_header_breadcrumb_link_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Breadcrumb Link Color', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'shop_header_els',
						'operator' => 'in',
						'value'    => 'breadcrumb',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--shop .site-breadcrumb a',
						'property' => 'color',
					),
				),
			),
			'shop_header_breadcrumb_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Breadcrumb Color', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'shop_header_els',
						'operator' => 'in',
						'value'    => 'breadcrumb',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--shop .site-breadcrumb',
						'property' => 'color',
					),
				),
			),
			'shop_header_description_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Description Color', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'shop_header_els',
						'operator' => 'in',
						'value'    => 'description',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--shop .page-header__description',
						'property' => 'color',
					),
				),
			),
			'shop_header_padding_top' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Padding Top', 'ecomus'),
				'transport' => 'postMessage',
				'default'    => [
					'desktop' => 69,
					'tablet'  => 69,
					'mobile'  => 49,
				],
				'responsive' => true,
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'output'         => array(
					array(
						'element'  => '.page-header.page-header--shop',
						'property' => 'padding-top',
						'units'    => 'px',
						'media_query' => [
							'desktop' => '@media (min-width: 1200px)',
							'tablet'  => is_customize_preview() ? '@media (min-width: 699px) and (max-width: 1199px)' : '@media (min-width: 768px) and (max-width: 1199px)',
							'mobile'  => '@media (max-width: 767px)',
						],
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'shop_header_padding_bottom' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Padding Bottom', 'ecomus'),
				'transport' => 'postMessage',
				'default'    => [
					'desktop' => 65,
					'tablet'  => 65,
					'mobile'  => 48,
				],
				'responsive' => true,
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'output'         => array(
					array(
						'element'  => '.page-header.page-header--shop',
						'property' => 'padding-bottom',
						'units'    => 'px',
						'media_query' => [
							'desktop' => '@media (min-width: 1200px)',
							'tablet'  => is_customize_preview() ? '@media (min-width: 699px) and (max-width: 1199px)' : '@media (min-width: 768px) and (max-width: 1199px)',
							'mobile'  => '@media (max-width: 767px)',
						],
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
		);

		// Top Categories.
		$settings['shop_top_categories'] = array(
			'top_categories'                    => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Top Categories', 'ecomus' ),
				'default' => false,
			),
			'show_brand_page'                    => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Show on Brand Page', 'ecomus' ),
				'default' => true,
			),
			'top_categories_limit' => array(
				'type'            => 'number',
				'label'     	  => esc_html__( 'Limit', 'ecomus' ),
				'description'     => esc_html__( 'Enter 0 to get all categories. Enter a number to get limit number of top categories.', 'ecomus' ),
				'default'         => 0,
				'active_callback' => array(
					array(
						'setting'  => 'top_categories',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'top_categories_order' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Order By', 'ecomus' ),
				'default'         => 'order',
				'choices'         => array(
					'order' => esc_html__( 'Category Order', 'ecomus' ),
					'name'  => esc_html__( 'Category Name', 'ecomus' ),
					'id'    => esc_html__( 'Category ID', 'ecomus' ),
					'count' => esc_html__( 'Product Counts', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'top_categories',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'top_categories_columns' => array(
				'type'      => 'number',
				'label'     => esc_html__('Columns', 'ecomus'),
				'default'    => [
					'desktop' => 5,
					'tablet'  => 3,
					'mobile'  => 2,
				],
				'responsive' => true,
				'choices'   => array(
					'min' => 1,
					'max' => 10,
				),
				'active_callback' => array(
					array(
						'setting'  => 'top_categories',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'top_categories_column_space' => array(
				'type'      => 'number',
				'label'     => esc_html__('Column Space', 'ecomus'),
				'default'    => [
					'desktop' => 30,
					'tablet'  => 30,
					'mobile'  => 15,
				],
				'responsive' => true,
				'choices'   => array(
					'min' => 1,
					'max' => 50,
				),
				'active_callback' => array(
					array(
						'setting'  => 'top_categories',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'top_categories_title_position' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Title Position', 'ecomus' ),
				'default'         => 'inside',
				'choices'         => array(
					'inside' => esc_html__( 'Inside', 'ecomus' ),
					'outside'  => esc_html__( 'Outside', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'top_categories',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		// Catalog toolbar.
		$settings['taxonomy_description'] = array(
			'taxonomy_description_enable'                    => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Taxonomy Description', 'ecomus' ),
				'default' => false,
			),
			'taxonomy_description_position'      => array(
				'type'    => 'select',
				'label'   => esc_html__( 'Taxonomy Description Position', 'ecomus' ),
				'description' => esc_html__('This option works with the taxonomy such as product category, tag, brand...', 'ecomus'),
				'default' => 'above',
				'section' => 'taxonomy_description',
				'choices' => array(
					'above' => esc_html__( 'Default on the Shop', 'ecomus' ),
					'below' => esc_html__( 'Below the Products', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'taxonomy_description_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'taxonomy_description_html'               => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Taxonomy Description HTML', 'ecomus' ),
				'description' => esc_html__('Enable this option to allow HTML in the Taxonomy Description', 'ecomus'),
				'default' => false,
				'active_callback' => array(
					array(
						'setting'  => 'taxonomy_description_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'taxonomy_description_number_lines'                      => array(
				'type'            => 'number',
				'label'           => esc_html__('Description Number Lines', 'ecomus'),
				'default'         => 5,
				'active_callback' => array(
					array(
						'setting'  => 'taxonomy_description_enable',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'taxonomy_description_position',
						'operator' => '==',
						'value'    => 'below',
					),
				),
			),
			'taxonomy_description_alignment'      => array(
				'type'    => 'select',
				'label'   => esc_html__( 'Description Alignment', 'ecomus' ),
				'default' => 'center',
				'section' => 'taxonomy_description',
				'choices' => array(
					'left' => esc_html__( 'Left', 'ecomus' ),
					'center' => esc_html__( 'Center', 'ecomus' ),
					'right' => esc_html__( 'Right', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'taxonomy_description_enable',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'taxonomy_description_position',
						'operator' => '==',
						'value'    => 'below',
					),
				),
			),
		);

		// Catalog toolbar.
		$settings['shop_catalog_toolbar'] = array(
			'catalog_toolbar'                    => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Catalog Toolbar', 'ecomus' ),
				'default' => true,
			),
			'catalog_toolbar_list_hr'  => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),
			'catalog_toolbar_els'         => array(
				'type'            => 'multicheck',
				'label'           => esc_html__( 'Elements', 'ecomus' ),
				'default'         => array( 'filter', 'sortby', 'view' ),
				'choices'         => array(
					'filter'    => esc_html__( 'Filter', 'ecomus' ),
					'sortby'    => esc_html__( 'Sort By', 'ecomus' ),
					'view'  	=> esc_html__( 'View', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'catalog_toolbar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'catalog_toolbar_views' => array(
				'type'               => 'multicheck',
				'label'              => esc_html__( 'View', 'ecomus' ),
				'default'            => array( '1', '2', '3', '4', '5' ),
				'choices'            => array(
					'1'         => esc_html__( 'List', 'ecomus' ),
					'2'       => esc_html__( 'Grid 2 Columns', 'ecomus' ),
					'3'       => esc_html__( 'Grid 3 Columns', 'ecomus' ),
					'4'       => esc_html__( 'Grid 4 Columns', 'ecomus' ),
					'5'       => esc_html__( 'Grid 5 Columns', 'ecomus' ),
					'6'       => esc_html__( 'Grid 6 Columns', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'catalog_toolbar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'catalog_toolbar_els',
						'operator' => 'in',
						'value'    => 'view',
					),
				),
			),
			'catalog_toolbar_default_view' => array(
				'type'               => 'select',
				'label'              => esc_html__( 'Default View', 'ecomus' ),
				'default'            => 'grid',
				'choices'            => array(
					'list'       => esc_html__( 'List', 'ecomus' ),
					'grid'       => esc_html__( 'Grid', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'catalog_toolbar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'catalog_toolbar_els',
						'operator' => 'in',
						'value'    => 'view',
					),
				),
			),
		);

		// Single Product
		$settings['product'] = array(
			'product_taxonomy'               => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Product Taxonomy', 'ecomus' ),
				'default'         => 'product_cat',
				'choices'         => array(
					'product_cat'   => esc_html__( 'Category', 'ecomus' ),
					''              => esc_html__( 'None', 'ecomus' ),
					'product_brand' => esc_html__( 'Brand', 'ecomus' ),
				),
				'description' => esc_html__( 'Show a product taxonomy above the product title on the product page.', 'ecomus' ),
			),
			'product_brand_type'         => array(
					'type'        => 'select',
					'label'       => esc_html__( 'Product Taxonomy Type', 'ecomus' ),
					'default'     => 'title',
					'choices'     => array(
						'title'   => esc_html__( 'Title', 'ecomus' ),
						'logo' => esc_html__( 'Logo', 'ecomus' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'product_taxonomy',
							'operator' => '==',
							'value'    => 'product_brand',
						),
					),
				),
			'product_hr'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_sku' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Product SKU', 'ecomus' ),
				'default' => true,
			),
			'product_categtories' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Product Categories', 'ecomus' ),
				'default' => true,
			),
			'product_brands' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Product Brands', 'ecomus' ),
				'default' => false,
			),
			'product_tags' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Product Tags', 'ecomus' ),
				'default' => true,
			),
			'product_description_hr'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_description'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Product Description', 'ecomus' ),
				'default'     => false,
			),
			'product_description_lines'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Product Description Lines', 'ecomus' ),
				'default'         => 4,
			),
			'product_affiliate_hr'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_affiliate_new_tab'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Open affiliate product in new tab', 'ecomus' ),
				'default'     => false,
			),
		);

		$settings['wcboost_variation_swatches'] = array(
			'disable_out_of_stock_swatch_click_hr' => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'priority' => 90,
			),
			'disable_out_of_stock_swatch_click' => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Disable Click on Out of Stock Swatches', 'ecomus' ),
				'description' => esc_html__( 'Prevent users from clicking on out of stock variation swatches', 'ecomus' ),
				'default'     => false,
				'priority' => 95,
			),
		);

		// Vendor
		if ( class_exists( 'WeDevs_Dokan' ) ) {
			$settings['product'] = array_merge(
				$settings['product'],
				array(
					'single_product_vendor_name_custom' => array(
						'type'        => 'custom',
						'default'  => '<hr/>',
						'priority' => 95,
					),
					'single_product_vendor_name'     => array(
						'type'            => 'select',
						'label'           => esc_html__( 'Vendor Name', 'ecomus' ),
						'default'         => 'avatar',
						'choices'         => array(
							'none' => esc_html__( 'None', 'ecomus' ),
							'avatar' => esc_html__( 'Avatar - Vendor Name', 'ecomus' ),
							'text' => esc_html__( 'By - Vendor Name', 'ecomus' ),
						),
					),
				)
			);
		};

		$settings['product_sidebar'] = array(
			'product_sidebar' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Sidebar', 'ecomus' ),
				'description'     => esc_html__( 'Go to appearance > widgets find to single product sidebar to edit your sidebar', 'ecomus' ),
				'default'         => 'no-sidebar',
				'choices'         => array(
					'content-sidebar' => esc_html__( 'Right Sidebar', 'ecomus' ),
					'sidebar-content' => esc_html__( 'Left Sidebar', 'ecomus' ),
					'no-sidebar'      => esc_html__( 'No Sidebar', 'ecomus' ),
				),
			),
			'product_sidebar_open' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Sidebar Open', 'ecomus' ),
				'default'         => 'panel',
				'choices'         => array(
					'panel' => esc_html__( 'Panel', 'ecomus' ),
					'fixed' => esc_html__( 'Fixed', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_sidebar',
						'operator' => '!==',
						'value'    => 'no-sidebar',
					),
				),
			),
		);
		$settings['product_gallery'] = array(
			'product_gallery_layout' => array(
				'type'               => 'select',
				'label'              => esc_html__( 'Layout', 'ecomus' ),
				'default'            => '',
				'choices'            => array(
					''                  => esc_html__( 'Left thumbnails', 'ecomus' ),
					'grid-1'            => esc_html__( 'Grid 1', 'ecomus' ),
					'grid-2'            => esc_html__( 'Grid 2', 'ecomus' ),
					'stacked'           => esc_html__( 'Stacked', 'ecomus' ),
					'right-thumbnails'  => esc_html__( 'Right thumbnails', 'ecomus' ),
					'bottom-thumbnails' => esc_html__( 'Bottom thumbnails', 'ecomus' ),
				),
			),
			'product_image_zoom' => array(
				'type'               => 'select',
				'label'              => esc_html__( 'Zoom', 'ecomus' ),
				'default'            => 'bounding',
				'choices'            => array(
					'none'  	=> esc_html__( 'None', 'ecomus' ),
					'bounding'  => esc_html__( 'Bounding Box', 'ecomus' ),
					'inner'     => esc_html__( 'Inner Zoom', 'ecomus' ),
					'magnifier' => esc_html__( 'Zoom Magnifier', 'ecomus' ),
				),
				'description' => esc_html__( 'Zooms in where your cursor is on the image', 'ecomus' ),
			),
			'product_image_lightbox_hr'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_image_lightbox'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Lightbox', 'ecomus' ),
				'description' => esc_html__( 'Opens your images against a dark backdrop', 'ecomus' ),
				'default'     => true,
			),
			'product_thumbnail_animation'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Thumbnail Animation', 'ecomus' ),
				'description' => esc_html__( 'Enable the option to display thumbnail animations when the page loads.', 'ecomus' ),
				'default'     => true,
			),
			'image_rounded_shape_product_gallery_hr'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'image_rounded_shape_product_gallery'       => array(
				'type'            => 'radio-buttonset',
				'label'           => esc_html__( 'Gallery Border Radius Shape', 'ecomus' ),
				'default'         => '',
				'choices'         => array(
					'' 			=> esc_html__( 'Default', 'ecomus' ),
					'round'  	=> esc_html__( 'Round', 'ecomus' ),
					'custom'  	=> esc_html__( 'Custom', 'ecomus' ),
				),
			),
			'image_rounded_number_product_gallery'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Border Radius(px)', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'image_rounded_shape_product_gallery',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'image_rounded_shape_product_thumbnail_hr'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'image_rounded_shape_product_thumbnail'       => array(
				'type'            => 'radio-buttonset',
				'label'           => esc_html__( 'Thumbnail Border Radius Shape', 'ecomus' ),
				'default'         => '',
				'choices'         => array(
					'' 			=> esc_html__( 'Default', 'ecomus' ),
					'round'  	=> esc_html__( 'Round', 'ecomus' ),
					'custom'  	=> esc_html__( 'Custom', 'ecomus' ),
				),
			),
			'image_rounded_number_product_thumbnail'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Border Radius(px)', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'image_rounded_shape_product_thumbnail',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
		);

		// Single Badges
		$settings['product_badges'] = array(
			'product_badges_sale'          => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Sale Badge', 'ecomus' ),
				'description' => esc_html__( 'Display a badge for sale products.', 'ecomus' ),
				'default'     => false,
			),
			'product_badges_sale_type'     => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Type', 'ecomus' ),
				'default'         => 'text',
				'choices'         => array(
					'percent'        => esc_html__( 'Percentage', 'ecomus' ),
					'text'           => esc_html__( 'Text', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_badges_sale',
						'operator' => '=',
						'value'    => true,
					),
				),
			),
			'product_badges_hr_2'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_badges_new'           => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'New Badge', 'ecomus' ),
				'description' => esc_html__( 'Display a badge for new product.', 'ecomus' ),
				'default'     => false,
			),
			'product_badges_hr_3'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_badges_featured'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Featured Badge', 'ecomus' ),
				'description' => esc_html__( 'Display a badge for featured product.', 'ecomus' ),
				'default'     => false,
			),
			'product_badges_hr_4'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_badges_stock'           => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Stock Badge', 'ecomus' ),
				'description' => esc_html__( 'Display a badge for stock product.', 'ecomus' ),
				'default'     => true,
			),
		);

		$settings['product_ask_question'] = array(
			'product_ask_question'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Ask a Question', 'ecomus' ),
				'default'     => true,
			),
			'product_ask_question_form'           => array(
				'type'        => 'textarea',
				'label'       => esc_html__('Contact Form', 'ecomus'),
				'description' => esc_html__('Please enter the contact form shortcode', 'ecomus'),
				'default'     => '',
				'input_attrs' => array(
					'placeholder' => '[contact-form-7 id="11" title="Contact form 1"]',
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_ask_question',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		$settings['product_delivery_return'] = array(
			'product_delivery_return'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Delivery Return', 'ecomus' ),
				'default'     => true,
			),
			'product_delivery_return_page'      => array(
				'type'        => 'dropdown-pages',
				'label'       => esc_html__( 'Delivery Return Page', 'ecomus' ),
				'description' => esc_html__( 'Select the page to display the Delivery Return content', 'ecomus' ),
				'default'     => '',
				'active_callback' => array(
					array(
						'setting'  => 'product_delivery_return',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		$settings['product_share'] = array(
			'product_share'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Product Share', 'ecomus' ),
				'default'     => true,
			),
		);

		// Product tabs
		$settings['product_tabs'] = array(
			'product_tabs_layout' => array(
				'type'               => 'select',
				'label'              => esc_html__( 'Layout', 'ecomus' ),
				'default'            => '',
				'choices'            => array(
					''          => esc_html__( 'Defaults', 'ecomus' ),
					'accordion' => esc_html__( 'Accordion', 'ecomus' ),
					'list'      => esc_html__( 'List', 'ecomus' ),
					'vertical'  => esc_html__( 'Vertical', 'ecomus' ),
				),
			),
			'product_tabs_status' => array(
				'type'    => 'select',
				'label'   => esc_html__( 'Product Tabs Status', 'ecomus' ),
				'default' => 'close',
				'choices' => array(
					'close' => esc_html__( 'Close all tabs', 'ecomus' ),
					'first' => esc_html__( 'Open first tab', 'ecomus' ),
					'description' => esc_html__( 'Open description tab', 'ecomus' ),
					'review' => esc_html__( 'Open review tab', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_tabs_layout',
						'operator' => '==',
						'value'    => 'accordion',
					),
				),
			),
		);

		$settings['upsells_products'] = array(
			'upsells_products'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Upsells Products', 'ecomus' ),
				'default'     => true,
			),
			'upsells_products_numbers' => array(
				'type'            => 'number',
				'label'     	  => esc_html__( 'Numbers', 'ecomus' ),
				'default'         => 10,
				'active_callback' => array(
					array(
						'setting'  => 'upsells_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'upsells_products_columns' => array(
				'type'        	=> 'number',
				'label' 		=> esc_html__('Columns', 'ecomus'),
				'default'     	=> [
					'desktop' => 4,
					'tablet'  => 3,
					'mobile'  => 2,
				],
				'responsive'  => true,
				'choices'     => array(
					'min' => 1,
					'max' => 6,
				),
				'active_callback' => array(
					array(
						'setting'  => 'upsells_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'upsells_products_stock' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Out of stock products', 'ecomus' ),
				'default'         => 'default',
				'choices'         => array(
					'default' => esc_html__( 'Default (follow WooCommerce setting)', 'ecomus' ),
					'hide'    => esc_html__( 'Hide out of stock', 'ecomus' ),
					'end'     => esc_html__( 'Show out of stock at the end', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'upsells_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		$settings['related_products']= array(
			'related_products'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Related Products', 'ecomus' ),
				'default'     => true,
			),
			'related_products_by_cats'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'By Categories', 'ecomus' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'related_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'related_products_by_tags'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'By Tags', 'ecomus' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'related_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'related_products_numbers' => array(
				'type'        	 => 'number',
				'label' 	 => esc_html__( 'Numbers', 'ecomus' ),
				'default'     	 => 10,
				'choices'     	 => array(
					'min' => 1,
				),
				'active_callback' => array(
					array(
						'setting'  => 'related_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'related_products_columns' => array(
				'type'        	=> 'number',
				'label' 		=> esc_html__('Columns', 'ecomus'),
				'default'     	=> [
					'desktop' => 4,
					'tablet'  => 3,
					'mobile'  => 2,
				],
				'responsive'  => true,
				'choices'     => array(
					'min' => 1,
					'max' => 6,
				),
				'active_callback' => array(
					array(
						'setting'  => 'related_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'related_products_stock' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Out of stock products', 'ecomus' ),
				'default'         => 'default',
				'choices'         => array(
					'default' => esc_html__( 'Default (follow WooCommerce setting)', 'ecomus' ),
					'hide'    => esc_html__( 'Hide out of stock', 'ecomus' ),
					'end'     => esc_html__( 'Show out of stock at the end', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'related_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		$settings['recently_viewed_products']= array(
			'recently_viewed_products'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Recently Viewed Products', 'ecomus' ),
				'default'     => true,
			),
			'recently_viewed_products_ajax' => array(
				'type'    => 'toggle',
				'label'   => esc_html__('Load With Ajax', 'ecomus'),
				'default' => false,
				'active_callback' => array(
					array(
						'setting'  => 'recently_viewed_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'recently_viewed_products_numbers' => array(
				'type'           => 'number',
				'description'    => esc_html__( 'Numbers', 'ecomus' ),
				'default'        => 10,
				'choices'     	 => array(
					'min' => 1,
				),
				'active_callback' => array(
					array(
						'setting'  => 'recently_viewed_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'recently_viewed_products_columns' => array(
				'type'        	=> 'number',
				'label' 		=> esc_html__('Columns', 'ecomus'),
				'default'     	=> [
					'desktop' => 4,
					'tablet'  => 3,
					'mobile'  => 2,
				],
				'responsive'  => true,
				'choices'     => array(
					'min' => 1,
					'max' => 6,
				),
				'active_callback' => array(
					array(
						'setting'  => 'recently_viewed_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'recently_viewed_products_stock' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Out of stock products', 'ecomus' ),
				'default'         => 'default',
				'choices'         => array(
					'default' => esc_html__( 'Default (follow WooCommerce setting)', 'ecomus' ),
					'hide'    => esc_html__( 'Hide out of stock', 'ecomus' ),
					'end'     => esc_html__( 'Show out of stock at the end', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'recently_viewed_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		$settings['woocommerce_cart'] = array(
			'update_cart_page_auto'       => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Update Cart Automatically', 'ecomus' ),
				'description' => esc_html__( 'Check this option to update cart page automatically', 'ecomus' ),
				'default'     => 0,
			),
			'product_hr_1'                => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'cross_sells_products'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Cross-Sells Products', 'ecomus' ),
				'default'     => true,
			),
			'cross_sells_products_numbers' => array(
				'type'            => 'number',
				'label'     	  => esc_html__( 'Numbers', 'ecomus' ),
				'default'         => 10,
				'active_callback' => array(
					array(
						'setting'  => 'cross_sells_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'cross_sells_products_columns' => array(
				'type'        	=> 'number',
				'label' 		=> esc_html__('Columns', 'ecomus'),
				'default'     	=> [
					'desktop' => 4,
					'tablet'  => 3,
					'mobile'  => 2,
				],
				'responsive'  => true,
				'choices'     => array(
					'min' => 1,
					'max' => 6,
				),
				'active_callback' => array(
					array(
						'setting'  => 'cross_sells_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		return $settings;
	}

	/**
	* Get product attributes
	*
	* @return string
	*/
	public function get_product_attributes() {
		$output = array();
		if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
			$attributes_tax = wc_get_attribute_taxonomies();
			if ( $attributes_tax ) {
				$output[''] = esc_html__( 'None', 'ecomus' );

				foreach ( $attributes_tax as $attribute ) {
					if ($attribute->attribute_name && $attribute->attribute_label ) {
						$output[$attribute->attribute_name] = $attribute->attribute_label;
					}
				}

			}
		}

		return $output;
	}
}
