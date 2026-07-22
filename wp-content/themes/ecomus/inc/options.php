<?php

/**
 * Theme Options
 *
 * @package Ecomus
 */

namespace Ecomus;

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

class Options {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * $ecomus_customize
	 *
	 * @var $ecomus_customize
	 */
	protected static $ecomus_customize = null;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self:: $instance;
	}

	/**
	 * The class constructor
	 *
	 * @since 1.0.0
	 *
	 */
	public function __construct() {
		add_filter('ecomus_customize_config', array($this, 'customize_settings'));
		self::$ecomus_customize = \Ecomus\Customizer::instance();
	}

	/**
	 * This is a short hand function for getting setting value from customizer
	 *
	 * @since 1.0.0
	 *
	 * @param string $name
	 *
	 * @return bool|string
	 */
	public function get_option($name) {
		if ( is_object( self::$ecomus_customize ) ) {
			$value = self::$ecomus_customize->get_option( $name );
		} elseif (false !== get_theme_mod($name)) {
			$value = get_theme_mod($name);
		} else {
			$value = $this->get_option_default($name);
		}
		return apply_filters('ecomus_get_option', $value, $name);
	}

	/**
	 * Get default option values
	 *
	 * @since 1.0.0
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	public function get_option_default($name) {
		if ( is_object( self::$ecomus_customize ) ) {
			return self::$ecomus_customize->get_option_default( $name );
		}

		$config   = $this->customize_settings();
		$settings = array_reduce( $config['settings'], 'array_merge', array() );

		if ( ! isset( $settings[ $name ] ) ) {
			return false;
		}

		return isset( $settings[ $name ]['default'] ) ? $settings[ $name ]['default'] : false;
	}

	/**
	 * Options of topbar items
	 *
	 * @return array
	 */
	public static function topbar_items_option() {
		return apply_filters( 'ecomus_topbar_items_option', array(
			''     			    => esc_html__( 'Select an Item', 'ecomus' ),
			'language' 			=> esc_html__( 'Language', 'ecomus' ),
			'currency' 			=> esc_html__( 'Currency', 'ecomus' ),
			'socials'        	=> esc_html__( 'Socials', 'ecomus' ),
			'slides'        	=> esc_html__( 'Slides', 'ecomus' ),
			'phone'        		=> esc_html__( 'Phone Number', 'ecomus' ),
			'email'        		=> esc_html__( 'Email Address', 'ecomus' ),
			'menu'        		=> esc_html__( 'Menu', 'ecomus' ),
			'custom-text'       => esc_html__( 'Custom Text', 'ecomus' ),
		) );
	}

	/**
	 * Options of header items
	 *
	 * @return array
	 */
	public static function header_items_option() {
		return apply_filters( 'ecomus_header_items_option', array(
			''     			 => esc_html__( 'Select an Item', 'ecomus' ),
			'logo'           => esc_html__( 'Logo', 'ecomus' ),
			'primary-menu'   => esc_html__( 'Primary Menu', 'ecomus' ),
			'secondary-menu' => esc_html__( 'Secondary Menu', 'ecomus' ),
			'category-menu'  => esc_html__( 'Category Menu', 'ecomus' ),
			'search'   		 => esc_html__( 'Search', 'ecomus' ),
			'account'   	 => esc_html__( 'Account', 'ecomus' ),
			'wishlist'   	 => esc_html__( 'Wishlist', 'ecomus' ),
			'compare'   	 => esc_html__( 'Compare', 'ecomus' ),
			'cart'   	 	 => esc_html__( 'Cart', 'ecomus' ),
			'language'     	 => esc_html__( 'Language', 'ecomus' ),
			'currency'     	 => esc_html__( 'Currency', 'ecomus' ),
			'support' 		 => esc_html__( 'Support Center', 'ecomus' ),
			'custom-html' 		 => esc_html__( 'Custom HTML', 'ecomus' ),
		) );
	}
	/**
	 * Options of header items
	 *
	 * @return array
	 */
	public static function header_mobile_items_option() {
		return apply_filters( 'ecomus_header_mobile_items_option', array(
			''     			 => esc_html__( 'Select an Item', 'ecomus' ),
			'logo'           => esc_html__( 'Logo', 'ecomus' ),
			'hamburger'      => esc_html__( 'Hamburger', 'ecomus' ),
			'search'         => esc_html__( 'Search', 'ecomus' ),
			'cart'           => esc_html__( 'Cart', 'ecomus' ),
			'wishlist'       => esc_html__( 'Wishlist', 'ecomus' ),
			'compare'        => esc_html__( 'Compare', 'ecomus' ),
			'account'        => esc_html__( 'Account', 'ecomus' ),
			'secondary-menu' => esc_html__( 'Secondary Menu', 'ecomus' ),
			'custom-html-mobile' 		 => esc_html__( 'Custom HTML', 'ecomus' ),
		) );
	}

	/**
	 * Options of navigation bar items
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function navigation_bar_items_option() {
		return apply_filters( 'ecomus_navigation_bar_items_option', array(
			'home'     => esc_html__( 'Home', 'ecomus' ),
			'shop'     => esc_html__( 'Shop', 'ecomus' ),
			'filter'  => esc_html__( 'Filter (For shop page only)', 'ecomus' ),
			'search'  => esc_html__( 'Search', 'ecomus' ),
			'account'  => esc_html__( 'Account', 'ecomus' ),
			'wishlist' => esc_html__( 'Wishlist', 'ecomus' ),
			'compare'  => esc_html__( 'Compare', 'ecomus' ),
			'cart'     => esc_html__( 'Cart', 'ecomus' ),
			'customlink' => esc_html__( 'Custom Link', 'ecomus' ),
		) );
	}

	/**
	 * Get customize settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function customize_settings() {
		$settings = array(
			'theme' => 'ecomus',
		);

		$panels = array(
			'general'    => array(
				'priority' => 10,
				'title'    => esc_html__( 'General', 'ecomus' ),
			),
			'styling'    => array(
				'priority' => 15,
				'title'    => esc_html__( 'Styling', 'ecomus' ),
			),
			'typography' => array(
				'priority' => 20,
				'title'    => esc_html__( 'Typography', 'ecomus' ),
			),
			'header'       => array(
				'priority' => 20,
				'title'    => esc_html__( 'Header', 'ecomus' ),
			),
			'page'   => array(
				'title'      => esc_html__('Page', 'ecomus'),
				'priority'   => 30,
			),
			'blog'    => array(
				'priority' => 30,
				'title'    => esc_html__( 'Blog', 'ecomus' ),
			),
			'mobile' => array(
				'priority'   => 90,
				'title'      => esc_html__('Mobile', 'ecomus'),
			),
		);

		$sections = array(
			'maintenance'  => array(
				'title'      => esc_html__('Maintenance', 'ecomus'),
				'priority'   => 10,
				'capability' => 'edit_theme_options',
			),
			'color_scheme' => array(
				'title'    => esc_html__('Color Scheme', 'ecomus'),
				'panel'    => 'styling',
			),
			'styling_images' => array(
				'title'    => esc_html__('Images', 'ecomus'),
				'panel'    => 'styling',
			),
			'styling_buttons' => array(
				'title'    => esc_html__('Buttons', 'ecomus'),
				'panel'    => 'styling',
			),
			'styling_form_fields' => array(
				'title'    => esc_html__('Form Fields', 'ecomus'),
				'panel'    => 'styling',
			),
			'api_keys' => array(
				'title'    => esc_html__( 'API Keys', 'ecomus' ),
				'panel'    => 'general',
			),
			'backtotop' => array(
				'title'    => esc_html__( 'Back To Top', 'ecomus' ),
				'panel'    => 'general',
			),
			// Typography
			'typo_font_family'         => array(
				'title'    => esc_html__( 'Font Family', 'ecomus' ),
				'panel'    => 'typography',
			),
			'typo_main'         => array(
				'title'    => esc_html__( 'Main', 'ecomus' ),
				'panel'    => 'typography',
			),
			'typo_headings'     => array(
				'title'    => esc_html__( 'Headings', 'ecomus' ),
				'panel'    => 'typography',
			),
			'typo_header_logo'         => array(
				'title'    => esc_html__( 'Header Logo Text', 'ecomus' ),
				'panel'    => 'typography',
			),
			'typo_header_menu_primary'       => array(
				'title'    => esc_html__( 'Header Primary Menu', 'ecomus' ),
				'panel'    => 'typography',
			),
			'typo_header_menu_secondary'       => array(
				'title'    => esc_html__( 'Header Secondary Menu', 'ecomus' ),
				'panel'    => 'typography',
			),
			'typo_header_menu_category'       => array(
				'title'    => esc_html__( 'Header Category Menu', 'ecomus' ),
				'panel'    => 'typography',
			),
			'typo_page'         => array(
				'title'    => esc_html__( 'Page', 'ecomus' ),
				'panel'    => 'typography',
			),
			'typo_posts'        => array(
				'title'    => esc_html__( 'Blog', 'ecomus' ),
				'panel'    => 'typography',
			),
			'typo_widget'       => array(
				'title'    => esc_html__( 'Widgets', 'ecomus' ),
				'panel'    => 'typography',
			),
			// Header
			'header_top'        => array(
				'title'    => esc_html__( 'Topbar', 'ecomus' ),
				'panel'    => 'header',
			),
			'header_campaign'   => array(
				'title'    => esc_html__( 'Campaign Bar', 'ecomus' ),
				'panel'    => 'header',
			),
			'header_layout'        => array(
				'title'    => esc_html__( 'Header Layout', 'ecomus' ),
				'panel'    => 'header',
			),
			'header_main'       => array(
				'title'    => esc_html__( 'Header Main', 'ecomus' ),
				'panel'    => 'header',
			),
			'header_bottom'       => array(
				'title'    => esc_html__( 'Header Bottom', 'ecomus' ),
				'panel'    => 'header',
			),
			'header_sticky'       => array(
				'title'    => esc_html__( 'Sticky Header', 'ecomus' ),
				'panel'    => 'header',
			),
			'header_background'       => array(
				'title'    => esc_html__( 'Header Background', 'ecomus' ),
				'panel'    => 'header',
			),
			'header_logo'       => array(
				'title'    => esc_html__( 'Logo', 'ecomus' ),
				'panel'    => 'header',
			),
			'header_account'    => array(
				'title'    => esc_html__( 'Account', 'ecomus' ),
				'panel'    => 'header',
			),
			'header_wishlist'    => array(
				'title'    => esc_html__( 'Wishlist', 'ecomus' ),
				'panel'    => 'header',
			),
			'header_compare'    => array(
				'title'    => esc_html__( 'Compare', 'ecomus' ),
				'panel'    => 'header',
			),
			'header_cart'    => array(
				'title'    => esc_html__( 'Cart', 'ecomus' ),
				'panel'    => 'header',
			),
			'header_search'    => array(
				'title'    => esc_html__( 'Search', 'ecomus' ),
				'panel'    => 'header',
			),
			'header_support'    => array(
				'title'    => esc_html__( 'Support Center', 'ecomus' ),
				'panel'    => 'header',
			),
			'header_category_menu'    => array(
				'title'    => esc_html__( 'Category Menu', 'ecomus' ),
				'panel'    => 'header',
			),
			'header_custom_html'    => array(
				'title'    => esc_html__( 'Custom HTML', 'ecomus' ),
				'panel'    => 'header',
			),
			// Blog
			'post_card'       => array(
				'title'    => esc_html__( 'Post Card Images', 'ecomus' ),
				'panel'    => 'blog',
			),
			'blog_header'       => array(
				'title'    => esc_html__( 'Blog Header', 'ecomus' ),
				'panel'    => 'blog',
			),
			'blog_page'       => array(
				'title'    => esc_html__( 'Blog Page', 'ecomus' ),
				'panel'    => 'blog',
			),
			'blog_single'       => array(
				'title'    => esc_html__( 'Blog Single', 'ecomus' ),
				'panel'    => 'blog',
			),
			'share_socials' => array(
				'title'    => esc_html__( 'Share Socials', 'ecomus' ),
				'panel'    => 'general',
			),
			// Page
			'page_header'       => array(
				'title'    => esc_html__( 'Page Header', 'ecomus' ),
				'panel'    => 'page',
			),
			// Mobile
			'topbar_mobile'        => array(
				'title'    => esc_html__( 'Topbar', 'ecomus' ),
				'panel'    => 'mobile',
			),
			'header_mobile_layout'        => array(
				'title'    => esc_html__( 'Header Layout', 'ecomus' ),
				'panel'    => 'mobile',
			),
			'header_mobile_main'       => array(
				'title'    => esc_html__( 'Header Main', 'ecomus' ),
				'panel'    => 'mobile',
			),
			'header_mobile_bottom'       => array(
				'title'    => esc_html__( 'Header Bottom', 'ecomus' ),
				'panel'    => 'mobile',
			),
			'header_mobile_elements'        => array(
				'title'    => esc_html__( 'Header Elements', 'ecomus' ),
				'panel'    => 'mobile',
			),
			'header_mobile_sticky'       => array(
				'title'    => esc_html__( 'Sticky Header', 'ecomus' ),
				'panel'    => 'mobile',
			),
			'header_mobile_background'       => array(
				'title'    => esc_html__( 'Header Background', 'ecomus' ),
				'panel'    => 'mobile',
			),
			'header_mobile_menu'    => array(
				'title'    => esc_html__( 'Header Mobile Menu', 'ecomus' ),
				'panel'    => 'mobile',
			),
			'mobile_product_catalog'        => array(
				'title'    => esc_html__( 'Product Catalog', 'ecomus' ),
				'panel'    => 'mobile',
			),
			'mobile_product_card'        => array(
				'title'    => esc_html__( 'Product Card', 'ecomus' ),
				'panel'    => 'mobile',
			),
			'mobile_navigation_bar'        => array(
				'title'    => esc_html__( 'Navigation Bar', 'ecomus' ),
				'panel'    => 'mobile',
			),

			// RTL
			'rtl'        => array(
				'title'    => esc_html__( 'RTL', 'ecomus' ),
				'panel'    => 'general',
			),
		);

		$settings   = array();

		// Maintenance
		$settings['maintenance'] = array(
			'maintenance_enable'             => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Enable Maintenance Mode', 'ecomus'),
				'description' => esc_html__('Put your site into maintenance mode', 'ecomus'),
				'default'     => false,
			),
			'maintenance_mode'               => array(
				'type'        => 'radio',
				'label'       => esc_html__('Mode', 'ecomus'),
				'description' => esc_html__('Select the correct mode for your site', 'ecomus'),
				'tooltip'     => wp_kses_post(sprintf(__('If you are putting your site into maintenance mode for a longer perior of time, you should set this to "Coming Soon". Maintenance will return HTTP 503, Comming Soon will set HTTP to 200. <a href="%s" target="_blank">Learn more</a>', 'ecomus'), 'https://yoast.com/http-503-site-maintenance-seo/')),
				'default'     => 'maintenance',
				'choices'     => array(
					'maintenance' => esc_html__('Maintenance', 'ecomus'),
					'coming_soon' => esc_html__('Coming Soon', 'ecomus'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'maintenance_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'maintenance_page'               => array(
				'type'            => 'dropdown-pages',
				'label'           => esc_html__('Maintenance Page', 'ecomus'),
				'default'         => 0,
				'active_callback' => array(
					array(
						'setting'  => 'maintenance_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		// Color Scheme
		$settings['color_scheme'] = array(
			'primary_color_title'  => array(
				'type'  => 'custom',
				'label' => esc_html__( 'Primary Color', 'ecomus' ),
			),
			'primary_color'        => array(
				'type'            => 'color-palette',
				'choices'         => array(
					'colors' => array(
						'#db1215',
						'#814037',
						'#93f859',
						'#ff0b0b',
						'#61b482',
						'#ff7d54',
						'#000000',
						'#fd8484',
						'#a6e57c',
						'#67c08f',
						'#d53a3d',
					),
					'style'  => 'round',
				),
				'active_callback' => array(
					array(
						'setting'  => 'primary_color_custom',
						'operator' => '!=',
						'value'    => true,
					),
				),
			),
			'primary_color_custom' => array(
				'type'      => 'checkbox',
				'label'     => esc_html__( 'Pick my favorite color', 'ecomus' ),
				'default'   => false,

			),
			'primary_color_custom_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Custom Color', 'ecomus' ),
				'default'         => '#db1215',
				'active_callback' => array(
					array(
						'setting'  => 'primary_color_custom',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'primary_text_color'             => array(
				'type'        => 'select',
				'default'     => false,
				'label'       => esc_html__('Text on Primary Color', 'ecomus'),
				'default'         => 'light',
				'choices'         => array(
					'light' 	=> esc_html__( 'Light', 'ecomus' ),
					'dark' 	    => esc_html__( 'Dark', 'ecomus' ),
					'custom'  	=> esc_html__( 'Custom', 'ecomus' ),
				),
			),
			'primary_text_color_custom'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Custom Color', 'ecomus' ),
				'default'         => '#fff',
				'active_callback' => array(
					array(
						'setting'  => 'primary_text_color',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'primary_base_color_hr'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'primary_base_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Base Color', 'ecomus' ),
				'default'         => '',
			),
			'primary_dark_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Dark Color', 'ecomus' ),
				'default'         => '',
			),
			'primary_link_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Link Color', 'ecomus' ),
				'default'         => '',
			),
			'primary_link_hover_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Link Hover Color', 'ecomus' ),
				'default'         => '',
			),
		);

		$settings['styling_images'] = array(
			'image_rounded_shape'       => array(
				'type'            => 'radio-buttonset',
				'label'           => esc_html__( 'Border Radius Shape', 'ecomus' ),
				'default'         => '',
				'choices'         => array(
					'' 			=> esc_html__( 'None', 'ecomus' ),
					'round'  	=> esc_html__( 'Round', 'ecomus' ),
					'custom'  	=> esc_html__( 'Custom', 'ecomus' ),
				),
			),
			'image_rounded_number'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Border Radius(px)', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'image_rounded_shape',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),

		);

		$settings['styling_buttons'] = array(
			'button_rounded_shape'       => array(
				'type'            => 'radio-buttonset',
				'label'           => esc_html__( 'Border Radius Shape', 'ecomus' ),
				'default'         => '',
				'choices'         => array(
					'' 			=> esc_html__( 'None', 'ecomus' ),
					'round'  	=> esc_html__( 'Round', 'ecomus' ),
					'circle'  	=> esc_html__( 'Circle', 'ecomus' ),
					'custom'  	=> esc_html__( 'Custom', 'ecomus' ),
				),
			),
			'button_rounded_number'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Border Radius(px)', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'button_rounded_shape',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'button_custom_hr_1'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'button_solid_dark_headline' => array(
				'type'            => 'headline',
				'label'           => esc_html__( 'Solid Dark', 'ecomus' ),
			),
			'button_solid_dark_bg_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color', 'ecomus' ),
				'default'         => '',
			),
			'button_solid_dark_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color', 'ecomus' ),
				'default'         => '',
			),
			'button_solid_dark_hover_bg_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color Hover', 'ecomus' ),
				'default'         => '',
			),
			'button_solid_dark_eff_hover_bg_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Effect Background Color Hover', 'ecomus' ),
				'default'         => '',
			),
			'button_solid_dark_hover_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color Hover', 'ecomus' ),
				'default'         => '',
			),
			'button_custom_hr_2'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			// Button Light
			'button_solid_light_headline' => array(
				'type'            => 'headline',
				'label'           => esc_html__( 'Solid Light', 'ecomus' ),
			),
			'button_solid_light_bg_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color', 'ecomus' ),
				'default'         => '',
			),
			'button_solid_light_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color', 'ecomus' ),
				'default'         => '',
			),
			'button_solid_light_hover_bg_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color Hover', 'ecomus' ),
				'default'         => '',
			),
			'button_solid_light_hover_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color Hover', 'ecomus' ),
				'default'         => '',
			),
			'button_custom_hr_3'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			// Button Outline
			'button_outline_dark_headline' => array(
				'type'            => 'headline',
				'label'           => esc_html__( 'Outline Dark', 'ecomus' ),
			),
			'button_outline_dark_border_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color', 'ecomus' ),
				'default'         => '',
			),
			'button_outline_dark_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color', 'ecomus' ),
				'default'         => '',
			),
			'button_outline_dark_hover_border_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color Hover', 'ecomus' ),
				'default'         => '',
			),
			'button_outline_dark_hover_bg_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color Hover', 'ecomus' ),
				'default'         => '',
			),
			'button_outline_dark_hover_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color Hover', 'ecomus' ),
				'default'         => '',
			),
			'button_custom_hr_4'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			// Button Outline
			'button_outline_light_headline' => array(
				'type'            => 'headline',
				'label'           => esc_html__( 'Outline Light', 'ecomus' ),
			),
			'button_outline_light_border_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color', 'ecomus' ),
				'default'         => '',
			),
			'button_outline_light_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color', 'ecomus' ),
				'default'         => '',
			),
			'button_outline_light_hover_border_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color Hover', 'ecomus' ),
				'default'         => '',
			),
			'button_outline_light_hover_bg_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color Hover', 'ecomus' ),
				'default'         => '',
			),
			'button_outline_light_hover_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color Hover', 'ecomus' ),
				'default'         => '',
			),
			'button_custom_hr_5'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			// Button Underline
			'button_underline_headline' => array(
				'type'            => 'headline',
				'label'           => esc_html__( 'Underline', 'ecomus' ),
			),
			'button_underline_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color', 'ecomus' ),
				'default'         => '',
			),
			'button_underline_hover_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color Hover', 'ecomus' ),
				'default'         => '',
			),
			'button_custom_hr_6'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			// Button Text
			'button_text_headline' => array(
				'type'            => 'headline',
				'label'           => esc_html__( 'Text', 'ecomus' ),
			),
			'button_text_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color', 'ecomus' ),
				'default'         => '',
			),
			'button_text_hover_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color Hover', 'ecomus' ),
				'default'         => '',
			),
		);

		$settings['styling_form_fields'] = array(
			'form_fields_rounded_shape'       => array(
				'type'            => 'radio-buttonset',
				'label'           => esc_html__( 'Border Radius Shape', 'ecomus' ),
				'default'         => '',
				'choices'         => array(
					'' 			=> esc_html__( 'None', 'ecomus' ),
					'round'  	=> esc_html__( 'Round', 'ecomus' ),
					'circle'  	=> esc_html__( 'Circle', 'ecomus' ),
					'custom'  	=> esc_html__( 'Custom', 'ecomus' ),
				),
			),
			'form_fields_rounded_number'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Border Radius(px)', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'form_fields_rounded_shape',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'form_fields_custom_hr_1'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'form_fields_bg_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color', 'ecomus' ),
				'default'         => '',
			),
			'form_fields_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color', 'ecomus' ),
				'default'         => '',
			),
			'form_fields_border_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color', 'ecomus' ),
				'default'         => '',
			),
			'form_fields_hover_border_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color Hover', 'ecomus' ),
				'default'         => '',
			),
		);

		// Typography
		// Typography - body.
		$settings['typo_main'] = array(
			'typo_body'                      => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Body', 'ecomus' ),
				'description' => esc_html__( 'Customize the body font', 'ecomus' ),
				'default'     => array(
					'font-family' => 'Albert Sans',
					'variant'     => 'regular',
					'font-size'   => '14px',
					'line-height' => '1.6',
					'color'       => '#545454',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing'  => '',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'body',
					),
				),
			),
		);

		$settings['typo_font_family'] = array(
			'typo_font_family'     => array(
				'type'        => 'toggle',
				'default'     => true,
				'label'       => esc_html__('Albert Sans Font', 'ecomus'),
				'description' => esc_html__('Enable this option to load Albert Sans Font', 'ecomus'),
			),
		);


		// Typography - headings.
		$settings['typo_headings'] = array(
			'typo_heading'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading', 'ecomus' ),
				'description' => esc_html__( 'Customize the Heading font', 'ecomus' ),
				'default'     => array(
					'font-family'    => 'Albert Sans',
					'variant'        => 'regular',
					'line-height'    => '1.2',
					'color'          => '#000000',
					'text-transform' => 'none',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing'  => '',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
				array(
						'element' => 'h1,h2,h3,h4,h5,h6',
					),
				),
			),
			'typo_heading_hr_1'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'typo_h1'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading 1', 'ecomus' ),
				'default'     => array(
					'font-size'      => '80px',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'h1, .h1',
					),
				),
			),
			'typo_heading_hr_2'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'typo_h2'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading 2', 'ecomus' ),
				'default'     => array(
					'font-size'      => '68px',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
				array(
						'element' => 'h2, .h2',
					),
				),
			),
			'typo_heading_hr_3'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'typo_h3'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading 3', 'ecomus' ),
				'default'     => array(
					'font-size'      => '52px',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'h3, .h3',
					),
				),
			),
			'typo_heading_hr_4'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'typo_h4'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading 4', 'ecomus' ),
				'default'     => array(
					'font-size'      => '42px',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'h4, .h4',
					),
				),
			),
			'typo_heading_hr_5'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'typo_h5'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading 5', 'ecomus' ),
				'default'     => array(
					'font-size'      => '28px',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'h5, .h5',
					),
				),
			),
			'typo_heading_hr_6'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'typo_h6'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading 6', 'ecomus' ),
				'default'     => array(
					'font-size'      => '20px',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'h6, .h6',
					),
				),
			),
		);

		// Typography - header primary menu.
		$settings['typo_header_logo'] = array(
			'logo_font'      => array(
				'type'            => 'typography',
				'label'           => esc_html__( 'Logo Font', 'ecomus' ),
				'default'         => array(
					'font-family'    => '',
					'variant'		 => '',
					'font-size'      => '',
					'letter-spacing' => '',
					'subsets'        => array( 'latin-ext' ),
					'text-transform' => 'none',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'output'          => array(
					array(
						'element' => '.site-header .header-logo__text',
					),
				),
			),
		);

		// Typography - header primary menu.
		$settings['typo_header_menu_primary'] = array(
			'typo_menu'                      => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Menu', 'ecomus' ),
				'description' => esc_html__( 'Customize the menu font', 'ecomus' ),
				'default'     => array(
					'font-family'    => 'Albert Sans',
					'variant'        => '500',
					'font-size'      => '16px',
					'line-height' 	 => '1.6',
					'text-transform' => 'none',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing' => '',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.primary-navigation .nav-menu > li > a',
					),
				),
			),
			'typo_submenu'                   => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Sub-Menu', 'ecomus' ),
				'description' => esc_html__( 'Customize the sub-menu font', 'ecomus' ),
				'default'     => array(
					'font-family'    => 'Albert Sans',
					'variant'        => 'regular',
					'font-size'      => '16px',
					'line-height' 	 => '1.6',
					'text-transform' => 'none',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing' => '',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.primary-navigation li .menu-item > a, .primary-navigation li .menu-item--widget > a, .primary-navigation .mega-menu ul.mega-menu__column .menu-item--widget-heading a, .primary-navigation li .menu-item > span, .primary-navigation li .menu-item > h6',
					),
				),
			),
		);

		// Typography - header secondary menu.
		$settings['typo_header_menu_secondary'] = array(
			'typo_secondary_menu'                      => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Menu', 'ecomus' ),
				'description' => esc_html__( 'Customize the menu font', 'ecomus' ),
				'default'     => array(
					'font-family'    => 'Albert Sans',
					'variant'        => '800',
					'font-size'      => '12px',
					'line-height' 	 => '1.6',
					'text-transform' => 'uppercase',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing' => '',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.secondary-navigation .nav-menu > li > a',
					),
				),
			),
			'typo_sub_secondary_menu'                   => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Sub-Menu', 'ecomus' ),
				'description' => esc_html__( 'Customize the sub-menu font', 'ecomus' ),
				'default'     => array(
					'font-family'    => 'Albert Sans',
					'variant'        => 'regular',
					'font-size'      => '14px',
					'line-height' 	 => '1.6',
					'text-transform' => 'none',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing' => '',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.secondary-navigation li .menu-item > a, .secondary-navigation li .menu-item--widget > a, .secondary-navigation .mega-menu ul.mega-menu__column .menu-item--widget-heading a, .secondary-navigation li .menu-item > span, .secondary-navigation li .menu-item > h6',
					),
				),
			),
		);

		$settings['typo_page'] = array(
			'typo_page_title'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Page Title', 'ecomus' ),
				'description' => esc_html__( 'Customize the page title font', 'ecomus' ),
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
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.page-header--page .page-header__title',
					),
				),
			),
		);

		// Typography - posts.
		$settings['typo_posts'] = array(
			'typo_blog_header_title'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Blog Header Title', 'ecomus' ),
				'description' => esc_html__( 'Customize the font of blog header', 'ecomus' ),
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
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.page-header--blog .page-header__title',
					),
				),
			),
			'typo_blog_post_title'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Blog Post Title', 'ecomus' ),
				'description' => esc_html__( 'Customize the font of blog post title', 'ecomus' ),
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
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.single-post .hentry .entry-header .entry-title',
					),
				),
			),
		);

		$settings['header_top'] = array(
			'topbar'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Topbar', 'ecomus' ),
				'description' => esc_html__( 'Display a bar on the top', 'ecomus' ),
				'default'     => false,
				'priority' => 5,
			),
			'topbar_fullwidth'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Topbar Full Width', 'ecomus' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 5,
			),
			'topbar_border_bottom'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Topbar Border Bottom', 'ecomus' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 5,
			),
			'topbar_custom_hr_1'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 10,
			),
			'topbar_left'       => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Left Items', 'ecomus' ),
				'description'     => esc_html__( 'Control items on the left side of the topbar', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'ecomus' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->topbar_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 15,
			),
			'topbar_center'       => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Center Items', 'ecomus' ),
				'description'     => esc_html__( 'Control items on the center side of the topbar', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'ecomus' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->topbar_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 20,
			),
			'topbar_right'      => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Right Items', 'ecomus' ),
				'description'     => esc_html__( 'Control items on the right side of the topbar', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'ecomus' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->topbar_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 25,
			),
			'topbar_custom_hr_2'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 30,
			),
			'topbar_slides'       => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Slides Item', 'ecomus' ),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Slide', 'ecomus' ),
					'field' => 'text',
				),
				'fields'          => array(
					'text' => array(
						'type'    => 'textarea',
						'label'   => esc_html__( 'Text', 'ecomus' ),
						'sanitize_callback' => 'Ecomus\Icon::sanitize_svg',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 35,
			),
			'topbar_custom_heading_3'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 40,
			),
			'topbar_phone'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Phone Number', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 40,
			),
			'topbar_phone_text'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Phone Text', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 40,
			),
			'topbar_email'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Email Item', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 40,
			),
			'topbar_menu'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Menu Item', 'ecomus' ),
				'default'         => '',
				'choices'         => $this->get_menus(),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 40,
			),
			'topbar_custom_text'           => array(
				'type'        => 'textarea',
				'label'       => esc_html__('Custom Text', 'ecomus'),
				'default'     => '',
				'sanitize_callback' => 'Ecomus\Icon::sanitize_svg',
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 40,
			),
			'topbar_custom_heading_4'    => array(
				'type'    => 'custom',
				'default' => '<hr/><h2>'. esc_html__( 'Topbar Background', 'ecomus' ) .'</h2>',
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 45,
			),
			'topbar_background_color' => array(
				'type'    => 'color',
				'label'   => esc_html__( 'Background Color', 'ecomus' ),
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '.topbar',
						'property' => 'background-color',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 60,
			),
			'topbar_color' => array(
				'type'    => 'color',
				'label'   => esc_html__( 'Color', 'ecomus' ),
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '.topbar',
						'property' => '--em-color__dark',
					),
					array(
						'element'  => '.topbar',
						'property' => '--em-color__base',
					),
					array(
						'element'  => '.topbar',
						'property' => '--em-link-color',
					),
					array(
						'element'  => '.topbar-slides__item',
						'property' => 'color',
					),
					array(
						'element'  => '.topbar .ecomus-currency-language .current',
						'property' => 'color',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 65,
			),
			'topbar_border_bottom_color' => array(
				'type'    => 'color',
				'label'   => esc_html__( 'Border Bottom Color', 'ecomus' ),
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '.topbar',
						'property' => '--em-border-color',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'topbar_border_bottom',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 65,
			),
			'topbar_custom_heading_5'    => array(
				'type'    => 'custom',
				'default' => '<hr/><h2>'. esc_html__( 'Topbar Style', 'ecomus' ) .'</h2>',
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 70,
			),
			'topbar_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Height', 'ecomus'),
				'transport' => 'postMessage',
				'default'    => [
					'desktop' => 45,
					'tablet'  => 45,
					'mobile'  => 45,
				],
				'responsive' => true,
				'choices'   => array(
					'min' => 0,
					'max' => 200,
				),
				'output'         => array(
					array(
						'element'  => '.topbar',
						'property' => 'height',
						'units'    => 'px',
						'media_query' => [
							'desktop' => '@media (min-width: 1200px)',
							'tablet'  => is_customize_preview() ? '@media (min-width: 699px) and (max-width: 1199px)' : '@media (min-width: 768px) and (max-width: 1199px)',
							'mobile'  => '@media (max-width: 767px)',
						],
					),
					array(
						'element'  => '.topbar .topbar-items',
						'property' => 'line-height',
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
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 70,
			),
			'topbar_slides_width' => array(
				'type'      => 'slider',
				'label'       => esc_html__( 'Slides Max Width (px)', 'ecomus' ),
				'transport' => 'postMessage',
				'default'   => '700',
				'choices'   => array(
					'min' => 200,
					'max' => 1199,
				),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 70,
			),
		);

		// Header layout settings.
		$settings['header_layout'] = array(
			'header_present' => array(
				'type'        => 'radio',
				'label'       => esc_html__( 'Present', 'ecomus' ),
				'description' => esc_html__( 'Select a prebuilt header or build your own', 'ecomus' ),
				'default'     => 'prebuild',
				'choices'     => array(
					'prebuild' => esc_html__( 'Use pre-build header', 'ecomus' ),
					'custom'   => esc_html__( 'Build my own', 'ecomus' ),
				),
			),
			'header_version' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Prebuilt Header', 'ecomus' ),
				'description'     => esc_html__( 'Select a prebuilt header present', 'ecomus' ),
				'default'         => 'v1',
				'choices'         => array(
					'v1'  => esc_html__( 'Header V1', 'ecomus' ),
					'v2'  => esc_html__( 'Header V2', 'ecomus' ),
					'v3'  => esc_html__( 'Header V3', 'ecomus' ),
					'v4'  => esc_html__( 'Header V4', 'ecomus' ),
					'v5'  => esc_html__( 'Header V5', 'ecomus' ),
					'v6'  => esc_html__( 'Header V6', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
			'header_fullwidth'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Full Width', 'ecomus' ),
				'default'     => true,
			),
			'header_element'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'header_prebuild_search'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Search', 'ecomus' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
			'header_prebuild_account'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Account', 'ecomus' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
			'header_prebuild_wishlist'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Wishlist', 'ecomus' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
			'header_prebuild_compare'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Compare', 'ecomus' ),
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
			'header_prebuild_cart'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Cart', 'ecomus' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
		);

		// Header main settings.
		$settings['header_main'] = array(
			'header_main_left'   => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Left Items', 'ecomus' ),
				'description'     => esc_html__( 'Control items on the left side of header main', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'ecomus' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_main_left' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Ecomus\Header\Main::instance(), 'render' ),
					),
				),
				'priority' => 10,
			),
			'header_main_center' => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Center Items', 'ecomus' ),
				'description'     => esc_html__( 'Control items at the center of header main', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'ecomus' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_main_center' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Ecomus\Header\Main::instance(), 'render' ),
					),
				),
				'priority' => 15,
			),
			'header_main_right'  => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Right Items', 'ecomus' ),
				'description'     => esc_html__( 'Control items on the right of header main', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'ecomus' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_main_right' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Ecomus\Header\Main::instance(), 'render' ),
					),
				),
				'priority' => 20,
			),
			'header_main_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'priority' => 25,
			),
			'header_main_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Height', 'ecomus' ),
				'transport' => 'postMessage',
				'default'   => '85',
				'choices'   => array(
					'min' => 50,
					'max' => 500,
				),
				'js_vars'   => array(
					array(
						'element'  => '.site-header__desktop .header-main',
						'property' => 'height',
						'units'    => 'px',
					),
				),
				'priority' => 30,
			),
			'header_main_divider'        => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Divider', 'ecomus' ),
				'default'         => true,
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'priority' => 35,
			),
		);

		// Header bottom settings.
		$settings['header_bottom'] = array(
			'header_bottom_left'   => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Left Items', 'ecomus' ),
				'description'     => esc_html__( 'Control items on the left side of header bottom', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'ecomus' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_bottom_left' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Ecomus\Header\Main::instance(), 'render' ),
					),
				),
				'priority' => 10,
			),
			'header_bottom_center' => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Center Items', 'ecomus' ),
				'description'     => esc_html__( 'Control items at the center of header bottom', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'ecomus' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_bottom_center' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Ecomus\Header\Main::instance(), 'render' ),
					),
				),
				'priority' => 15,
			),
			'header_bottom_right'  => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Right Items', 'ecomus' ),
				'description'     => esc_html__( 'Control items on the right of header bottom', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'ecomus' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_bottom_right' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Ecomus\Header\Main::instance(), 'render' ),
					),
				),
				'priority' => 20,
			),
			'header_bottom_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'priority' => 25,
			),
			'header_bottom_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Height', 'ecomus' ),
				'transport' => 'postMessage',
				'default'   => '60',
				'choices'   => array(
					'min' => 30,
					'max' => 500,
				),
				'js_vars'   => array(
					array(
						'element'  => '.site-header__desktop .header-bottom',
						'property' => 'height',
						'units'    => 'px',
					),
				),
				'priority' => 30,
			),
			'header_bottom_divider'        => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Divider', 'ecomus' ),
				'default'         => true,
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'priority' => 35,
			),
		);

		// Header sticky settings.
		$settings['header_sticky'] = array(
			'header_sticky'        => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Sticky Header', 'ecomus' ),
				'default'         => false,
			),
			'header_sticky_on'   => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Sticky On', 'ecomus' ),
				'default'         => 'down',
				'choices'         => array(
					'down' => esc_html__( 'Scroll Down', 'ecomus' ),
					'up'   => esc_html__( 'Scroll Up', 'ecomus' ),
				),
			),
			'header_sticky_el'   => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Sticky Header Section', 'ecomus' ),
				'default'         => 'header_main',
				'choices'         => array(
					'header_main'   => esc_html__('Header Main', 'ecomus'),
					'header_bottom' => esc_html__('Header Bottom', 'ecomus'),
					'both'          => esc_html__('Both', 'ecomus'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_sticky',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'header_sticky_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'header_sticky_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Header Main Height', 'ecomus' ),
				'transport' => 'postMessage',
				'default'   => '85',
				'choices'   => array(
					'min' => 30,
					'max' => 400,
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_sticky',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'header_sticky_el',
						'operator' => '!==',
						'value'    => 'header_bottom',
					),
				),
				'js_vars'   => array(
					array(
						'element'  => '.site-header__desktop.minimized .header-main, .site-header__desktop.headroom--not-top .header-main',
						'property' => 'height',
						'units'    => 'px',
					),
					array(
						'element'  => '.site-header__desktop.minimized .header-sticky + .header-bottom, .site-header__desktop.headroom--not-top .header-sticky + .header-bottom',
						'property' => 'top',
						'units'    => 'px',
					),
				),
			),
			'header_sticky_bottom_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Header Bottom Height', 'ecomus' ),
				'transport' => 'postMessage',
				'default'   => '64',
				'choices'   => array(
					'min' => 30,
					'max' => 400,
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_sticky',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'header_sticky_el',
						'operator' => '!==',
						'value'    => 'header_main',
					),
				),
				'js_vars'   => array(
					array(
						'element'  => '.site-header__desktop.minimized .header-bottom, .site-header__desktop.headroom--not-top .header-bottom',
						'property' => 'height',
						'units'    => 'px',
					),
				),
			),
		);

		$settings['header_background'] = array(
			'header_background_heading_1'    => array(
				'type'    => 'custom',
				'default' => '<h2>'. esc_html__( 'Header Main', 'ecomus' ) .'</h2>',
			),
			'header_main_background_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color', 'ecomus' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-main',
						'property' => 'background-color',
					),
				),
			),
			'header_main_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'ecomus' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-main',
						'property' => '--em-header-color',
					),
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-main',
						'property' => 'color',
					),
				),
			),
			'header_main_border_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color', 'ecomus' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-main',
						'property' => '--em-header-main-border-color',
					),
				),
			),
			'header_main_shadow_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Box Shadow Color', 'ecomus' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-main',
						'property' => '--em-header-main-shadow-color',
					),
				),
			),
			'header_background_heading_2'    => array(
				'type'    => 'custom',
				'default' => '<hr/><h2>'. esc_html__( 'Header Bottom', 'ecomus' ) .'</h2>',
			),
			'header_bottom_background_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color', 'ecomus' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-bottom',
						'property' => 'background-color',
					),
				),
			),
			'header_bottom_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'ecomus' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-bottom',
						'property' => '--em-header-color',
					),
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-bottom',
						'property' => 'color',
					),
				),
			),
			'header_bottom_border_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color', 'ecomus' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-bottom',
						'property' => '--em-header-bottom-border-color',
					),
				),
			),
			'header_bottom_shadow_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Box Shadow Color', 'ecomus' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-bottom',
						'property' => '--em-header-bottom-shadow-color',
					),
				),
			),
			'header_background_heading_3'    => array(
				'type'    => 'custom',
				'default' => '<hr/><h2>'. esc_html__( 'Header Counter', 'ecomus' ) .'</h2>',
			),
			'header_counter_background_color' => array(
				'type'    => 'color',
				'label'   => esc_html__( 'Background Color', 'ecomus' ),
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '.header-counter',
						'property' => '--em-color__primary',
					),
				),
			),
			'header_counter_color' => array(
				'type'    => 'color',
				'label'   => esc_html__( 'Color', 'ecomus' ),
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '.header-counter',
						'property' => '--em-text-color-on-primary',
					),
				),
			),
			'header_background_heading_4'    => array(
				'type'    => 'custom',
				'default' => '<hr/><h2>'. esc_html__( 'Header Cart', 'ecomus' ) .'</h2>',
			),
			'header_cart_divider_color' => array(
				'type'    => 'color',
				'label'   => esc_html__( 'Devider Color', 'ecomus' ),
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '.header-cart__divider',
						'property' => '--em-cart-divider-color',
					),
				),
			),
		);


		// Campaign bar.
		$settings['header_campaign'] = array(
			'campaign_bar' => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Campaign Bar', 'ecomus' ),
				'description' => esc_html__( 'Display a bar before the site header.', 'ecomus' ),
				'default'     => false,
				'priority' => 0,
			),
			'campaign_bar_type'                 => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Type', 'ecomus' ),
				'default'     => 'marquee',
				'choices'     => array(
					'marquee'   => esc_html__('Marquee', 'ecomus'),
					'slides' 	=> esc_html__('Slides', 'ecomus'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 10,
			),
			'campaign_bar_width' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Width', 'ecomus' ),
				'transport' => 'postMessage',
				'default'   => '480',
				'choices'   => array(
					'min' => 100,
					'max' => 2000,
				),
				'js_vars'   => array(
					array(
						'element'  => '.campaign-bar-type--slides .campaign-bar__container',
						'property' => 'max-width',
						'units'    => 'px',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'campaign_bar_type',
						'operator' => '==',
						'value'    => 'slides',
					),
				),
			),
			'campaign_bar_speed' => array(
				'type'            => 'number',
				'label'     	  => esc_html__( 'Speed', 'ecomus' ),
				'description'     => esc_html__( 'Customize marquee speed (Example: 0.3)', 'ecomus' ),
				'default'         => 0.3,
				'choices'  => [
					'min'  => 0,
					'max'  => 1,
					'step' => 0.1,
				],
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'campaign_bar_type',
						'operator' => '==',
						'value'    => 'marquee',
					),
				),
			),
			'campaign_items_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 20,
			),
			'campaign_items'       => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Campaign Items', 'ecomus' ),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Campaign', 'ecomus' ),
					'field' => 'text',
				),
				'fields'          => array(
					'text' => array(
						'type'    => 'textarea',
						'label'   => esc_html__( 'Text', 'ecomus' ),
					),
					'link' => array(
						'type'    => 'text',
						'label'   => esc_html__( 'Link URL', 'ecomus' ),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 25,
			),
			'campaign_custom_heading'    => array(
				'type'    => 'custom',
				'default' => '<hr/><h2>'. esc_html__( 'Campaign Background', 'ecomus' ) .'</h2>',
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 55,
			),
			'campaign_background_color' => array(
				'type'    => 'color',
				'label'   => esc_html__( 'Background Color', 'ecomus' ),
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '.campaign-bar',
						'property' => '--em-campaign-background',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 60,
			),
			'campaign_color' => array(
				'type'    => 'color',
				'label'   => esc_html__( 'Color', 'ecomus' ),
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '.campaign-bar',
						'property' => '--em-campaign-text-color',
					),
					array(
						'element'  => '.campaign-bar__close',
						'property' => '--em-button-color',
					),
					array(
						'element'  => '.campaign-bar__close.em-button-text',
						'property' => '--em-button-color-hover',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 65,
			),
		);

		// Logo.
		$settings['header_logo'] = array(
			'logo_type'      => array(
				'type'    => 'radio',
				'label'   => esc_html__( 'Logo Type', 'ecomus' ),
				'default' => 'image',
				'choices' => array(
					'image' => esc_html__( 'Image', 'ecomus' ),
					'text'  => esc_html__( 'Text', 'ecomus' ),
					'svg'   => esc_html__( 'SVG', 'ecomus' ),
				),
			),
			'logo_text'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Logo Text', 'ecomus' ),
				'default'         => 'Ecomus',
				'active_callback' => array(
					array(
						'setting'  => 'logo_type',
						'operator' => '==',
						'value'    => 'text',
					),
				),
			),
			'logo_svg'       => array(
				'type'            => 'textarea',
				'label'           => esc_html__( 'Logo SVG', 'ecomus' ),
				'description'     => esc_html__( 'Paste SVG code of your logo here', 'ecomus' ),
				'sanitize_callback' => 'Ecomus\Icon::sanitize_svg',
				'output'          => array(
					array(
						'element' => '.site-header .header-logo',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'logo_type',
						'operator' => '==',
						'value'    => 'svg',
					),
				),
			),
			'logo'           => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Logo', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'logo_type',
						'operator' => '==',
						'value'    => 'image',
					),
				),
			),
			'logo_light'           => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Logo Light', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'logo_type',
						'operator' => '==',
						'value'    => 'image',
					),
				),
			),
			'logo_dimension' => array(
				'type'            => 'dimensions',
				'label'           => esc_html__( 'Logo Dimension', 'ecomus' ),
				'default'         => array(
					'width'  => 'auto',
					'height' => 'auto',
				),
				'active_callback' => array(
					array(
						'setting'  => 'logo_type',
						'operator' => '!=',
						'value'    => 'text',
					),
				),
			),
		);

		// Header account.
		$settings['header_account'] = array(
			'header_signin_icon_behaviour' => array(
				'type'            => 'radio',
				'label'           => esc_html__( 'Sign in Icon Behaviour', 'ecomus' ),
				'default'         => 'popup',
				'choices'         => array(
					'popup'   => esc_html__( 'Open the account popup', 'ecomus' ),
					'page'  => esc_html__( 'Open the account page', 'ecomus' ),
				),
			),
			'header_account_display' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Account Display', 'ecomus' ),
				'default'         => 'icon',
				'choices'         => array(
					'icon'   => esc_html__( 'Icon Only', 'ecomus' ),
					'icon-text'  => esc_html__( 'Icon & Text', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
		);

		// Header wishlist.
		$settings['header_wishlist'] = array(
			'header_wishlist_display' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Wishlist Display', 'ecomus' ),
				'default'         => 'icon',
				'choices'         => array(
					'icon'   => esc_html__( 'Icon Only', 'ecomus' ),
					'icon-text'  => esc_html__( 'Icon & Text', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
		);

		// Header wishlist.
		$settings['header_compare'] = array(
			'header_compare_display' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Compare Display', 'ecomus' ),
				'default'         => 'icon',
				'choices'         => array(
					'icon'   => esc_html__( 'Icon Only', 'ecomus' ),
					'icon-text'  => esc_html__( 'Icon & Text', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
		);

		// Header cart.
		$settings['header_cart'] = array(
			'cart_icon_behaviour' => array(
				'type'            => 'radio',
				'label'           => esc_html__( 'Cart Icon Behaviour', 'ecomus' ),
				'default'         => 'panel',
				'choices'         => array(
					'panel'   => esc_html__( 'Open the cart panel', 'ecomus' ),
					'page'  => esc_html__( 'Open the cart page', 'ecomus' ),
				),
			),
			'cart_icon_source'      => array(
				'type'    => 'radio',
				'label'   => esc_html__( 'Cart Icon', 'ecomus' ),
				'default' => 'icon',
				'choices' => array(
					'icon'  => esc_attr__( 'Built-in Icon', 'ecomus' ),
					'svg'   => esc_attr__( 'SVG Code', 'ecomus' ),
				),
			),
			'cart_icon'             => array(
				'type'    => 'radio-image',
				'default' => '',
				'choices' => array(
					''   	=> get_template_directory_uri() . '/assets/svg/shopping-bag.svg',
					'shopping-bag-2' 	=> get_template_directory_uri() . '/assets/svg/shopping-bag-2.svg',
					'shopping-cart'  	=> get_template_directory_uri() . '/assets/svg/shopping-cart.svg',
					'shopping-cart-2'  	=> get_template_directory_uri() . '/assets/svg/shopping-cart-2.svg',
				),
				'active_callback' => array(
					array(
						'setting'  => 'cart_icon_source',
						'operator' => '==',
						'value'    => 'icon',
					),
				),
			),
			'cart_icon_svg'         => array(
				'type'              => 'textarea',
				'description'       => esc_html__( 'Icon SVG code', 'ecomus' ),
				'sanitize_callback' => '\Ecomus\Icon::sanitize_svg',
				'active_callback'   => array(
					array(
						'setting'  => 'cart_icon_source',
						'operator' => '==',
						'value'    => 'svg',
					),
				),
			),
			'cart_icon_svg_size' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Size', 'ecomus'),
				'transport' => 'postMessage',
				'default'    => 24,
				'choices'   => array(
					'min' => 0,
					'max' => 50,
				),
				'output'         => array(
					array(
						'element'  => '.header-cart__icon .ecomus-svg-icon--custom-cart, ul.products li.product .product-loop-button .ecomus-svg-icon.ecomus-svg-icon--custom-cart, .ecomus-mobile-navigation-bar__icon .ecomus-svg-icon--custom-cart',
						'property' => 'font-size',
						'units'    => 'px',
					),
				),
				'active_callback'   => array(
					array(
						'setting'  => 'cart_icon_source',
						'operator' => '==',
						'value'    => 'svg',
					),
				),
			),
			'cart_hr_1'          => array(
				'type'    => 'custom',
				'section' => 'header_cart',
				'default' => '<hr>',
			),
			'header_cart_size' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Icon Size', 'ecomus' ),
				'default'         => 'medium',
				'choices'         => array(
					'medium'   => esc_html__( 'Medium', 'ecomus' ),
					'large'  => esc_html__( 'Large', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'header_cart_divider'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Divider', 'ecomus' ),
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'mini_cart_products'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Recommended Products', 'ecomus' ),
				'description'     => esc_html__( 'Display recommended products on the mini cart', 'ecomus' ),
				'default'         => 'recent_products',
				'choices'         => array(
					'none'                  => esc_html__( 'None', 'ecomus' ),
					'best_selling_products' => esc_html__( 'Best selling products', 'ecomus' ),
					'featured_products'     => esc_html__( 'Featured products', 'ecomus' ),
					'recent_products'       => esc_html__( 'Recent products', 'ecomus' ),
					'sale_products'         => esc_html__( 'Sale products', 'ecomus' ),
					'top_rated_products'    => esc_html__( 'Top rated products', 'ecomus' ),
					'crosssells_products'   => esc_html__( 'Cross-sells products', 'ecomus' ),

				),
			),
			'mini_cart_products_limit' => array(
				'type'            => 'number',
				'description'     => esc_html__( 'Number of products', 'ecomus' ),
				'default'         => 4,
			),
			'mini_cart_featured_icon' => array(
				'type'     => 'select',
				'label'    => esc_html__('Featured Icon Action', 'ecomus'),
				'default'  => 'quick-view',
				'choices'  => array(
					'quick-view'     => __( 'Quick View', 'ecomus' ),
					'add-to-cart'     => __( 'Add to Cart', 'ecomus' ),
				),
			),
			'cart_hr_2'          => array(
				'type'    => 'custom',
				'section' => 'header_cart',
				'default' => '<hr>',
			),
			'cart_note_enable' => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Enable cart note', 'ecomus' ),
				'default'     => false,
			),
			'cart_discount_enable' => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Enable cart discount', 'ecomus' ),
				'default'     => false,
			),
			'cart_estimate_enable' => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Enable cart estimate', 'ecomus' ),
				'default'     => false,
			),
		);

		// Header search.
		$settings['header_search'] = array(
			'header_search_layout' => array(
				'type'     => 'select',
				'label'    => esc_html__('Layout', 'ecomus'),
				'default'  => 'icon',
				'choices'  => array(
					'icon'     => __( 'Icon', 'ecomus' ),
					'form'     => __( 'Form', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'priority' => 5
			),
			'header_search_form_width' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Search Field Width', 'ecomus' ),
				'transport' => 'postMessage',
				'default'   => '400',
				'choices'   => array(
					'min' => 0,
					'max' => 1000,
				),
				'js_vars'   => array(
					array(
						'element'  => '.header-search__field',
						'property' => 'width',
						'units'    => 'px',
					),
				),
				'active_callback' => function() {
					return ! $this->display_header_search_icon_layout();
				},
				'priority' => 5
			),
			'header_search_type' => array(
				'type'     => 'select',
				'label'    => esc_html__('Type', 'ecomus'),
				'default'  => 'popup',
				'choices'  => array(
					'popup'       => __( 'Popup', 'ecomus' ),
					'sidebar'     => __( 'Sidebar', 'ecomus' ),
				),
				'active_callback' => function() {
					return $this->display_header_search_icon_layout();
				},
				'priority' => 5
			),
			'header_search_hr_1'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => function() {
					return $this->display_header_search_icon_layout();
				},
				'priority' => 10
			),
			'header_search_trending' => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Trending', 'ecomus' ),
				'description'     => esc_html__( 'Display a list of links in the search modal', 'ecomus' ),
				'default'         => false,
				'priority' => 15
			),
			'header_search_links'       => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Links', 'ecomus' ),
				'description'     => esc_html__( 'Add custom links of the trending searches', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Link', 'ecomus' ),
					'field' => 'text',
				),
				'fields'          => array(
					'text' => array(
						'type'  => 'text',
						'label' => esc_html__( 'Text', 'ecomus' ),
					),
					'url'  => array(
						'type'  => 'text',
						'label' => esc_html__( 'URL', 'ecomus' ),
					),
				),
				'priority' => 20
			),
			'header_search_hr_5'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => function() {
					return $this->display_header_search_icon_layout();
				},
				'priority' => 25
			),
			'header_search_products' => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Products', 'ecomus' ),
				'description'     => esc_html__( 'Display a products list before searching', 'ecomus' ),
				'default'         => false,
				'priority' => 30
			),
			'header_search_products_type' => array(
				'type'     => 'select',
				'label'    => esc_html__('Type', 'ecomus'),
				'default'  => 'recent_products',
				'choices'  => array(
					'recent_products'       => __( 'Recent Products', 'ecomus' ),
					'featured_products'     => __( 'Featured Products', 'ecomus' ),
					'sale_products'         => __( 'Sale Products', 'ecomus' ),
					'best_selling_products' => __( 'Best Selling Products', 'ecomus' ),
					'top_rated_products'    => __( 'Top Rated Products', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_search_products',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'priority' => 35
			),
			'header_search_product_limit'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Limit', 'ecomus' ),
				'default'         => '5',
				'active_callback' => array(
					array(
						'setting'  => 'header_search_products',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'priority' => 40
			),
		);

		// Support Center
		$settings['header_support'] = array(
			'header_support_phone_number'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Phone Number', 'ecomus' ),
				'default'         => '190010088',
			),
			'header_support_phone_number_link'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Phone Link', 'ecomus' ),
				'default'         => 'tel:190010088',
			),
			'header_support_svg'       => array(
				'type'            => 'textarea',
				'label'           => esc_html__( 'SVG Icon', 'ecomus' ),
				'description'     => esc_html__( 'Paste SVG code of your icon here', 'ecomus' ),
				'sanitize_callback' => 'Ecomus\Icon::sanitize_svg',
				'output'          => array(
					array(
						'element' => '.header-support-center',
					),
				),
			),
		);

		// Category Menu
		$settings['header_category_menu'] = array(
			'category_menu_view_all' => array(
				'type'        => 'toggle',
				'default'     => false,
				'label'       => esc_html__('Enable View All Categories', 'ecomus'),
			),
			'category_menu_view_all_url'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'View All Url', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'category_menu_view_all',
						'operator' => '==',
						'value'    => true
					),
				),
			),
			'category_menu_button_heading'    => array(
				'type'    => 'custom',
				'default' => '<hr/><h2>'. esc_html__( 'Custom Button', 'ecomus' ) .'</h2>',
			),
			'category_menu_button_bg_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color', 'ecomus' ),
				'transport' => 'postMessage',
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => '.header-category__title-button',
						'property' => '--em-button-bg-color',
					),
				),
			),
			'category_menu_button_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'ecomus' ),
				'transport' => 'postMessage',
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => '.header-category__title-button',
						'property' => '--em-button-color',
					),
				),
			),
			'category_menu_button_border_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color', 'ecomus' ),
				'transport' => 'postMessage',
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => '.header-category__title-button',
						'property' => '--em-button-border-color',
					),
				),
			),
			'category_menu_button_bg_color_hover'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color Hover', 'ecomus' ),
				'transport' => 'postMessage',
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => '.header-category__title-button',
						'property' => '--em-button-bg-color-hover',
					),
				),
			),
			'category_menu_button_color_hover'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color Hover', 'ecomus' ),
				'transport' => 'postMessage',
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => '.header-category__title-button',
						'property' => '--em-button-color-hover',
					),
				),
			),
			'category_menu_button_border_color_hover'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color Hover', 'ecomus' ),
				'transport' => 'postMessage',
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => '.header-category__title-button',
						'property' => '--em-button-border-color-hover',
					),
				),
			),
			'category_menu_button_hr_1'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'category_menu_button_width' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Min Width', 'ecomus' ),
				'transport' => 'postMessage',
				'default'   => '',
				'choices'   => array(
					'min' => 0,
					'max' => 1000,
				),
				'js_vars'   => array(
					array(
						'element'  => '.header-category__title-button',
						'property' => 'min-width',
						'units'    => 'px',
					),
				),
			)
		);

		// Custom HTML
		$settings['header_custom_html'] = array(
			'header_custom_html'       => array(
				'type'            => 'textarea',
				'label'           => esc_html__( 'Custom HTML', 'ecomus' ),
				'description'     => esc_html__( 'Paste your HTML here', 'ecomus' ),
			),
		);

		// Hambuger menu
		$settings['header_mobile_menu'] = array(
			'header_mobile_menu_els' => array(
				'type'     => 'multicheck',
				'label'    => esc_html__('Elements', 'ecomus'),
				'default'  => array( 'primary-menu' ),
				'choices'  => array(
					'primary-menu' 		=> esc_html__('Primary Menu', 'ecomus'),
					'custom-link' 		=> esc_html__('Custom Link', 'ecomus'),
					'custom-text' 		=> esc_html__('Custom Text', 'ecomus'),
					'currency-language' => esc_html__('Currency Language', 'ecomus'),
				),
				'description'     => esc_html__('Select which elements you want to show.', 'ecomus'),
			),
			'header_mobile_menu_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_menu_els',
						'operator' => 'in',
						'value'    => 'custom-link',
					),
				),
			),
			'header_mobile_menu_link_text'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Custom Link Text', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_menu_els',
						'operator' => 'in',
						'value'    => 'custom-link',
					),
				),
			),
			'header_mobile_menu_link'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Custom Link Url', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_menu_els',
						'operator' => 'in',
						'value'    => 'custom-link',
					),
				),
			),
			'header_mobile_menu_custom_text_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_menu_els',
						'operator' => 'in',
						'value'    => 'custom-text',
					),
				),
			),
			'header_mobile_menu_custom_text'           => array(
				'type'        => 'textarea',
				'label'       => esc_html__('Custom Text', 'ecomus'),
				'default'     => '',
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_menu_els',
						'operator' => 'in',
						'value'    => 'custom-text',
					),
				),
			),
			'header_mobile_menu_open_primary_submenus_on_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'header_mobile_menu_open_primary_submenus_on' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Open primary submenus on', 'ecomus' ),
				'default'         => 'all',
				'choices'         => array(
					'all'   => esc_html__( 'Title & Icon click', 'ecomus' ),
					'icon'  => esc_html__( 'Icon click', 'ecomus' ),
				),
			),
		);

		$settings['post_card'] = array(
			'image_rounded_shape_post_card'       => array(
				'type'            => 'radio-buttonset',
				'label'           => esc_html__( 'Border Radius Shape', 'ecomus' ),
				'default'         => '',
				'choices'         => array(
					'' 			=> esc_html__( 'Default', 'ecomus' ),
					'round'  	=> esc_html__( 'Round', 'ecomus' ),
					'custom'  	=> esc_html__( 'Custom', 'ecomus' ),
				),
			),
			'image_rounded_number_post_card'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Border Radius(px)', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'image_rounded_shape_post_card',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),

		);

		// Blog Header.
		$settings['blog_header'] = array(
			'blog_header' => array(
				'type'        => 'toggle',
				'default'     => true,
				'label'       => esc_html__('Enable Blog Header', 'ecomus'),
				'description' => esc_html__('Enable to show a blog header for the page below the site header', 'ecomus'),
			),
			'blog_header_hr' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'blog_header_els' => array(
				'type'     => 'multicheck',
				'label'    => esc_html__('Elements', 'ecomus'),
				'default'  => array( 'title', 'breadcrumb' ),
				'choices'  => array(
					'title'      => esc_html__('Title', 'ecomus'),
					'breadcrumb' => esc_html__('BreadCrumb', 'ecomus'),
				),
				'description'     => esc_html__('Select which elements you want to show.', 'ecomus'),
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'blog_header_hr_1' => array(
				'type'            => 'custom',
				'default'         => '<hr/><h3>' . esc_html__('Custom', 'ecomus') . '</h3>',
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'blog_header_background_image'          => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Background Image', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'blog_header_background_overlay' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Overlay', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => true,
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--blog::before',
						'property' => 'background-color',
					),
				),
			),
			'blog_header_title_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Title Color', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'blog_header_els',
						'operator' => 'in',
						'value'    => 'title',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--blog .page-header__title',
						'property' => 'color',
					),
				),
			),
			'blog_header_breadcrumb_link_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Breadcrumb Link Color', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'blog_header_els',
						'operator' => 'in',
						'value'    => 'breadcrumb',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--blog .site-breadcrumb a',
						'property' => 'color',
					),
				),
			),
			'blog_header_breadcrumb_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Breadcrumb Color', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'blog_header_els',
						'operator' => 'in',
						'value'    => 'breadcrumb',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--blog .site-breadcrumb',
						'property' => 'color',
					),
				),
			),
			'blog_header_padding_top' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Padding Top', 'ecomus'),
				'transport' => 'postMessage',
				'default'    => [
					'desktop' => 69,
					'tablet'  => 69,
					'mobile'  => 48,
				],
				'responsive' => true,
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'output'         => array(
					array(
						'element'  => '.page-header.page-header--blog',
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
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'blog_header_padding_bottom' => array(
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
						'element'  => '.page-header.page-header--blog',
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
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
		);

		// Blog.
		$settings['blog_page'] = array(
			'blog_layout'    => array(
				'type'        => 'radio',
				'label'       => esc_html__( 'Layout', 'ecomus' ),
				'default'     => 'list',
				'choices'     => array(
					'list'          => esc_html__('List', 'ecomus'),
					'grid'          => esc_html__('Grid', 'ecomus'),
					'classic'  		=> esc_html__('Classic', 'ecomus'),
				),
			),
			'blog_sidebar'    => array(
				'type'        => 'radio',
				'label'       => esc_html__( 'Sidebar', 'ecomus' ),
				'default'     => 'no-sidebar',
				'choices'     => array(
					'no-sidebar'      => esc_html__('No Sidebar', 'ecomus'),
					'sidebar-content' => esc_html__('Left Sidebar', 'ecomus'),
					'content-sidebar' => esc_html__('Right Sidebar', 'ecomus'),
				),
			),
			'blog_excerpts' => array(
				'type'      => 'toggle',
				'label'     => esc_html__( 'Show Post Excerpts', 'ecomus' ),
				'default'   => false,
				'active_callback' => array(
					array(
						'setting'  => 'blog_layout',
						'operator' => '!=',
						'value'    => 'list',
					),
				),
			),
			'blog_excerpts_list' => array(
				'type'      => 'toggle',
				'label'     => esc_html__( 'Show Post Excerpts', 'ecomus' ),
				'default'   => true,
				'active_callback' => array(
					array(
						'setting'  => 'blog_layout',
						'operator' => '==',
						'value'    => 'list',
					),
				),
			),
			'blog_excerpts_length'                      => array(
				'type'            => 'number',
				'label'           => esc_html__('Post Excerpts Length', 'ecomus'),
				'default'         => 18,
			),
			'blog_hr'  => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'blog_pagination' => array(
				'type'    => 'radio',
				'label'   => esc_html__( 'Pagination Type', 'ecomus' ),
				'default' => 'numeric',
				'choices' => array(
					'numeric'  => esc_attr__( 'Numeric', 'ecomus' ),
					'infinite' => esc_attr__( 'Infinite Scroll', 'ecomus' ),
					'loadmore' => esc_attr__( 'Load More', 'ecomus' ),
				),
			),
			'blog_pagination_ajax_url_change' => array(
				'type'            => 'checkbox',
				'label'           => esc_html__( 'Change the URL after page loaded', 'ecomus' ),
				'default'         => true,
				'active_callback' => array(
					array(
						'setting'  => 'blog_pagination',
						'operator' => '!=',
						'value'    => 'numeric',
					),
				),
			),
		);

		// Blog single.
		$settings['blog_single'] = array(
			'post_layout'                 => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Post Layout', 'ecomus' ),
				'description' => esc_html__( 'The layout of single posts', 'ecomus' ),
				'default'     => 'no-sidebar',
				'choices'     => array(
					'no-sidebar'      => esc_html__('No Sidebar', 'ecomus'),
					'content-sidebar' => esc_html__('Right Sidebar', 'ecomus'),
					'sidebar-content' => esc_html__('Left Sidebar', 'ecomus'),
				),
			),
			'post_sidebar'    => array(
				'type'        => 'radio',
				'label'       => esc_html__( 'Sidebar Display', 'ecomus' ),
				'default'     => 'icon',
				'choices'     => array(
					'icon'     => esc_html__( 'Opens as a Panel', 'ecomus' ),
					'expanded' => esc_html__( 'Always Visible', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'post_layout',
						'operator' => '!=',
						'value'    => 'no-sidebar',
					),
				),
			),
			'post_sharing'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Post Sharing', 'ecomus' ),
				'description' => esc_html__( 'Enable post sharing.', 'ecomus' ),
				'default'     => false,
			),
			'post_navigation'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Post Navigation', 'ecomus' ),
				'description' => esc_html__( 'Display the next and previous posts', 'ecomus' ),
				'default'     => true,
			),
			'posts_related_custom'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'posts_related'   => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Related Posts', 'ecomus' ),
				'description' => esc_html__( 'Display related posts', 'ecomus' ),
				'default'     => true,
			),
			'posts_related_number'                      => array(
				'type'            => 'number',
				'label'           => esc_html__('Posts Numbers', 'ecomus'),
				'default'         => 5,
			),
			'posts_related_spacing'                      => array(
				'type'            => 'number',
				'label'           => esc_html__('Posts Spacing', 'ecomus'),
				'default'         => 30,
			),
			'single_post_image_rounded_shape_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'image_rounded_shape_featured_post'       => array(
				'type'            => 'radio-buttonset',
				'label'           => esc_html__( 'Featured Image Border Radius Shape', 'ecomus' ),
				'default'         => '',
				'choices'         => array(
					'' 			=> esc_html__( 'Default', 'ecomus' ),
					'round'  	=> esc_html__( 'Round', 'ecomus' ),
					'custom'  	=> esc_html__( 'Custom', 'ecomus' ),
				),
			),
			'image_rounded_number_featured_post'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Border Radius(px)', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'image_rounded_shape_featured_post',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
		);

		// API Keys.
		// $settings['api_keys'] = array(
		// 	'api_instagram_token' => array(
		// 		'type'        => 'text',
		// 		'label'       => esc_html__( 'Instagram Access Token', 'ecomus' ),
		// 		'description' => esc_html__( 'This Access Token is required to display your Instagram photos on this website.', 'ecomus' ),
		// 	),
		// );

		// Back To Top.
		$settings['backtotop'] = array(
			'backtotop'    => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Back To Top', 'ecomus' ),
				'description' => esc_html__( 'Check this to show back to top.', 'ecomus' ),
				'default'     => true,
			),
		);

		// Share socials
		$settings['share_socials'] = array(
			'post_sharing_socials' => array(
				'type'            => 'sortable',
				'description'     => esc_html__( 'Select social media for sharing posts/products', 'ecomus' ),
				'default'         => array(
					'facebook',
					'twitter',
					'tumblr',
					'whatsapp',
					'email',
				),
				'choices'         => array(
					'facebook'    => esc_html__( 'Facebook', 'ecomus' ),
					'twitter'     => esc_html__( 'Twitter', 'ecomus' ),
					'googleplus'  => esc_html__( 'Google Plus', 'ecomus' ),
					'pinterest'   => esc_html__( 'Pinterest', 'ecomus' ),
					'tumblr'      => esc_html__( 'Tumblr', 'ecomus' ),
					'reddit'      => esc_html__( 'Reddit', 'ecomus' ),
					'linkedin'    => esc_html__( 'Linkedin', 'ecomus' ),
					'stumbleupon' => esc_html__( 'StumbleUpon', 'ecomus' ),
					'digg'        => esc_html__( 'Digg', 'ecomus' ),
					'telegram'    => esc_html__( 'Telegram', 'ecomus' ),
					'whatsapp'    => esc_html__( 'WhatsApp', 'ecomus' ),
					'vk'          => esc_html__( 'VK', 'ecomus' ),
					'email'       => esc_html__( 'Email', 'ecomus' ),
				),
			),
			'post_sharing_whatsapp_number' => array(
				'type'        => 'text',
				'description' => esc_html__( 'WhatsApp Phone Number', 'ecomus' ),
				'active_callback' => array(
					array(
						'setting'  => 'post_sharing_socials',
						'operator' => 'contains',
						'value'    => 'whatsapp',
					),
				),
			),
		);

		// Page Header.
		$settings['page_header'] = array(
			'page_header' => array(
				'type'        => 'toggle',
				'default'     => true,
				'label'       => esc_html__('Enable Page Header', 'ecomus'),
				'description' => esc_html__('Enable to show a page header for the page below the site header', 'ecomus'),
			),
			'page_header_hr' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'page_header_els' => array(
				'type'     => 'multicheck',
				'label'    => esc_html__('Elements', 'ecomus'),
				'default'  => array( 'title' ),
				'choices'  => array(
					'title'      => esc_html__('Title', 'ecomus'),
					'breadcrumb' => esc_html__('BreadCrumb', 'ecomus'),
				),
				'description'     => esc_html__('Select which elements you want to show.', 'ecomus'),
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'page_header_hr_1' => array(
				'type'            => 'custom',
				'default'         => '<hr/><h3>' . esc_html__('Custom', 'ecomus') . '</h3>',
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'page_header_background_image'          => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Background Image', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'page_header_background_overlay' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Overlay', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--page::before',
						'property' => 'background-color',
					),
				),
			),
			'page_header_title_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Title Color', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'page_header_els',
						'operator' => 'in',
						'value'    => 'title',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--page .page-header__title',
						'property' => 'color',
					),
				),
			),
			'page_header_breadcrumb_link_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Breadcrumb Link Color', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'page_header_els',
						'operator' => 'in',
						'value'    => 'breadcrumb',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--page .site-breadcrumb a',
						'property' => 'color',
					),
				),
			),
			'page_header_breadcrumb_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Breadcrumb Color', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'page_header_els',
						'operator' => 'in',
						'value'    => 'breadcrumb',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--page .site-breadcrumb',
						'property' => 'color',
					),
				),
			),
			'page_header_padding_top' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Padding Top', 'ecomus'),
				'transport' => 'postMessage',
				'default'   => 69,
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--page',
						'property' => 'padding-top',
						'units'    => 'px',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'page_header_padding_bottom' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Padding Bottom', 'ecomus'),
				'transport' => 'postMessage',
				'default'   => 65,
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--page',
						'property' => 'padding-bottom',
						'units'    => 'px',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		$settings['topbar_mobile'] = array(
			'mobile_topbar' => array(
				'type'      => 'toggle',
				'label'     => esc_html__( 'Topbar', 'ecomus' ),
				'description' => esc_html__( 'Display topbar on mobile', 'ecomus' ),
				'default'   => false,
			),
			'mobile_topbar_breakpoint' => array(
				'type'      => 'slider',
				'label'       => esc_html__( 'Breakpoint (px)', 'ecomus' ),
				'description' => esc_html__( 'Set a breakpoint where the mobile navigation bar displays.', 'ecomus' ),
				'transport' => 'postMessage',
				'default'   => '767',
				'choices'   => array(
					'min' => 375,
					'max' => 1199,
				),
				'active_callback' => array(
					array(
						'setting'  => 'mobile_topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'mobile_topbar_section' => array(
				'type'      => 'select',
				'label'     => esc_html__( 'Topbar Items', 'ecomus' ),
				'default'   => 'left',
				'choices' => array(
					'left'   => esc_html__( 'Keep left items', 'ecomus' ),
					'center' => esc_html__( 'Keep center items', 'ecomus' ),
					'right'  => esc_html__( 'Keep right items', 'ecomus' ),
					'both'   => esc_html__( 'Keep both left and right items', 'ecomus' ),
					'all'    => esc_html__( 'Keep all items', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'mobile_topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'mobile_topbar_slides_width' => array(
				'type'      => 'slider',
				'label'       => esc_html__( 'Slides Max Width (px)', 'ecomus' ),
				'transport' => 'postMessage',
				'default'   => '500',
				'choices'   => array(
					'min' => 375,
					'max' => 1199,
				),
				'active_callback' => array(
					array(
						'setting'  => 'mobile_topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		// Header Mobile
		$settings['header_mobile_layout'] = array(
			'header_mobile_breakpoint' => array(
				'type'      => 'slider',
				'label'       => esc_html__( 'Breakpoint (px)', 'ecomus' ),
				'description' => esc_html__( 'Set a breakpoint where the mobile header displays and the desktop header is hidden.', 'ecomus' ),
				'transport' => 'postMessage',
				'default'   => '1199',
				'choices'   => array(
					'min' => 991,
					'max' => 1199,
				),
			),
			'header_mobile_present_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'header_mobile_present' => array(
				'type'        => 'radio',
				'label'       => esc_html__( 'Present', 'ecomus' ),
				'description' => esc_html__( 'Select a prebuilt header or build your own', 'ecomus' ),
				'default'     => 'prebuild',
				'choices'     => array(
					'prebuild' => esc_html__( 'Use pre-build header', 'ecomus' ),
					'custom'   => esc_html__( 'Build my own', 'ecomus' ),
				),
			),
			'header_mobile_version' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Prebuilt Header', 'ecomus' ),
				'description'     => esc_html__( 'Select a prebuilt header present', 'ecomus' ),
				'default'         => 'v1',
				'choices'         => array(
					'v1'  => esc_html__( 'Header V1', 'ecomus' ),
					'v2'  => esc_html__( 'Header V2', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
			'header_mobile_prebuild_search'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Search', 'ecomus' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
					array(
						'setting'  => 'header_mobile_version',
						'operator' => 'in',
						'value'    => array('v1', 'v2'),
					),
				),
			),
			'header_mobile_prebuild_account'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Account', 'ecomus' ),
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
					array(
						'setting'  => 'header_mobile_version',
						'operator' => 'in',
						'value'    => array('v1', 'v2'),
					),
				),
			),
			'header_mobile_prebuild_wishlist'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Wishlist', 'ecomus' ),
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
					array(
						'setting'  => 'header_mobile_version',
						'operator' => 'in',
						'value'    => array('v1', 'v2'),
					),
				),
			),
			'header_mobile_prebuild_compare'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Compare', 'ecomus' ),
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
					array(
						'setting'  => 'header_mobile_version',
						'operator' => 'in',
						'value'    => array('v1', 'v2'),
					),
				),
			),
			'header_mobile_prebuild_cart'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Cart', 'ecomus' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
					array(
						'setting'  => 'header_mobile_version',
						'operator' => 'in',
						'value'    => array('v1', 'v2'),
					),
				),
			),
			'header_mobile_main_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'header_mobile_main_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Header Main Height', 'ecomus' ),
				'transport' => 'postMessage',
				'default'   => '64',
				'choices'   => array(
					'min' => 30,
					'max' => 500,
				),
				'js_vars'   => array(
					array(
						'element'  => '.site-header__mobile .header-mobile-main',
						'property' => 'height',
						'units'    => 'px',
					),
				),
			),
			'header_mobile_bottom_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'header_mobile_bottom_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Header Bottom Height', 'ecomus' ),
				'transport' => 'postMessage',
				'default'   => '60',
				'choices'   => array(
					'min' => 30,
					'max' => 500,
				),
				'js_vars'   => array(
					array(
						'element'  => '.site-header__mobile .header-mobile-bottom',
						'property' => 'height',
						'units'    => 'px',
					),
				),
			),
		);

		// Header sticky settings.
		$settings['header_mobile_sticky'] = array(
			'header_mobile_sticky'        => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Sticky Header', 'ecomus' ),
				'default'         => false,
			),
			'header_mobile_sticky_el'   => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Sticky Header Section', 'ecomus' ),
				'default'         => 'header_main',
				'choices'         => array(
					'header_main'   => esc_html__('Header Main', 'ecomus'),
					'header_bottom' => esc_html__('Header Bottom', 'ecomus'),
					'both'          => esc_html__('Both', 'ecomus'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_sticky',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'header_mobile_sticky_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'header_mobile_sticky_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Header Main Height', 'ecomus' ),
				'transport' => 'postMessage',
				'default'   => '64',
				'choices'   => array(
					'min' => 30,
					'max' => 200,
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_sticky',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'header_mobile_sticky_el',
						'operator' => '!==',
						'value'    => 'header_bottom',
					),
				),
				'js_vars'   => array(
					array(
						'element'  => '.site-header__mobile.minimized .header-mobile-main, .site-header__mobile.headroom--not-top .header-mobile-main',
						'property' => 'height',
						'units'    => 'px',
					),
					array(
						'element'  => '.site-header__mobile.minimized .header-mobile-sticky + .header-mobile-bottom, .site-header__mobile.headroom--not-top .header-mobile-sticky + .header-mobile-bottom',
						'property' => 'top',
						'units'    => 'px',
					),
				),
			),
			'header_mobile_sticky_bottom_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Header Bottom Height', 'ecomus' ),
				'transport' => 'postMessage',
				'default'   => '60',
				'choices'   => array(
					'min' => 30,
					'max' => 200,
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_sticky',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'header_mobile_sticky_el',
						'operator' => '!==',
						'value'    => 'header_main',
					),
				),
				'js_vars'   => array(
					array(
						'element'  => '.site-header__mobile.minimized .header-mobile-bottom, .site-header__mobile.headroom--not-top .header-mobile-bottom',
						'property' => 'height',
						'units'    => 'px',
					),
				),
			),
		);

		// Header main settings.
		$settings['header_mobile_main'] = array(
			'header_mobile_main_left'   => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Left Items', 'ecomus' ),
				'description'     => esc_html__( 'Control items on the left side of header mobile main', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'ecomus' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_mobile_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_mobile_main_left' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Ecomus\Header\Mobile::instance(), 'render' ),
					),
				),
			),
			'header_mobile_main_center' => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Center Items', 'ecomus' ),
				'description'     => esc_html__( 'Control items at the center of header mobile main', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'ecomus' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_mobile_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_mobile_main_center' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Ecomus\Header\Mobile::instance(), 'render' ),
					),
				),
			),
			'header_mobile_main_right'  => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Right Items', 'ecomus' ),
				'description'     => esc_html__( 'Control items on the right of header mobile main', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'ecomus' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_mobile_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_mobile_main_right' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Ecomus\Header\Mobile::instance(), 'render' ),
					),
				),
			),
		);

		// Header bottom settings.
		$settings['header_mobile_bottom'] = array(
			'header_mobile_bottom_left'   => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Left Items', 'ecomus' ),
				'description'     => esc_html__( 'Control items on the left side of header mobile bottom', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'ecomus' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_mobile_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_mobile_bottom_left' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Ecomus\Header\Mobile::instance(), 'render' ),
					),
				),
			),
			'header_mobile_bottom_center' => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Center Items', 'ecomus' ),
				'description'     => esc_html__( 'Control items at the center of header mobile bottom', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'ecomus' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_mobile_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_mobile_bottom_center' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Ecomus\Header\Mobile::instance(), 'render' ),
					),
				),
			),
			'header_mobile_bottom_right'  => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Right Items', 'ecomus' ),
				'description'     => esc_html__( 'Control items on the right of header mobile bottom', 'ecomus' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'ecomus' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_mobile_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_mobile_bottom_right' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Ecomus\Header\Mobile::instance(), 'render' ),
					),
				),
			),
		);

		$settings['header_mobile_background'] = array(
			'header_mobile_background_heading_1'    => array(
				'type'    => 'custom',
				'default' => '<h2>'. esc_html__( 'Header Main', 'ecomus' ) .'</h2>',
			),
			'header_mobile_main_background_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color', 'ecomus' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__mobile .header-mobile-main',
						'property' => 'background-color',
					),
				),
			),
			'header_mobile_main_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'ecomus' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .header-mobile-main',
						'property' => '--em-color__dark',
					),
					array(
						'element'  => 'body:not(.header-transparent) .header-mobile-main',
						'property' => '--em-header-color',
					),
					array(
						'element'  => 'body:not(.header-transparent) .header-mobile-main',
						'property' => 'color',
					),
				),
			),
			'header_mobile_main_border_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color', 'ecomus' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .header-mobile-main',
						'property' => '--em-header-mobile-main-border-color',
					),
				),
			),
			'header_mobile_main_shadow_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Box Shadow Color', 'ecomus' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__mobile .header-mobile-main',
						'property' => '--em-header-mobile-main-shadow-color',
					),
				),
			),
			'header_mobile_background_heading_2'    => array(
				'type'    => 'custom',
				'default' => '<hr/><h2>'. esc_html__( 'Header Bottom', 'ecomus' ) .'</h2>',
			),
			'header_mobile_bottom_background_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color', 'ecomus' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__mobile .header-mobile-bottom',
						'property' => 'background-color',
					),
				),
			),
			'header_mobile_bottom_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'ecomus' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .header-mobile-bottom',
						'property' => '--em-color__dark',
					),
					array(
						'element'  => 'body:not(.header-transparent) .header-mobile-bottom',
						'property' => '--em-header-color',
					),
					array(
						'element'  => 'body:not(.header-transparent) .header-mobile-bottom',
						'property' => 'color',
					),
				),
			),
			'header_mobile_bottom_border_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color', 'ecomus' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .header-mobile-bottom',
						'property' => '--em-header-mobile-bottom-border-color',
					),
				),
			),
			'header_mobile_bottom_shadow_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Box Shadow Color', 'ecomus' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__mobile .header-mobile-bottom',
						'property' => '--em-header-mobile-bottom-shadow-color',
					),
				),
			),
		);

		// Header mobile menu.
		$settings['header_mobile_elements'] = array(
			'mobile_logo_type'      => array(
				'type'    => 'radio',
				'label'   => esc_html__( 'Logo Type', 'ecomus' ),
				'default' => 'default',
				'choices' => array(
					'default' => esc_html__( 'Default', 'ecomus' ),
					'image' => esc_html__( 'Image', 'ecomus' ),
					'text'  => esc_html__( 'Text', 'ecomus' ),
					'svg'   => esc_html__( 'SVG', 'ecomus' ),
				),
			),
			'mobile_logo_text'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Logo Text', 'ecomus' ),
				'default'         => 'Ecomus',
				'active_callback' => array(
					array(
						'setting'  => 'mobile_logo_type',
						'operator' => '==',
						'value'    => 'text',
					),
				),
			),
			'mobile_logo_svg'       => array(
				'type'            => 'textarea',
				'label'           => esc_html__( 'Logo SVG', 'ecomus' ),
				'description'     => esc_html__( 'Paste SVG code of your logo here', 'ecomus' ),
				'sanitize_callback' => 'Ecomus\Icon::sanitize_svg',
				'output'          => array(
					array(
						'element' => '.site-header .header-logo',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'mobile_logo_type',
						'operator' => '==',
						'value'    => 'svg',
					),
				),
			),
			'mobile_logo_image'   => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Logo', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'mobile_logo_type',
						'operator' => '==',
						'value'    => 'image',
					),
				),
			),
			'mobile_logo_image_light'   => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Logo Light', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'mobile_logo_type',
						'operator' => '==',
						'value'    => 'image',
					),
				),
			),
			'mobile_logo_dimension' => array(
				'type'            => 'dimensions',
				'label'           => esc_html__( 'Logo Dimension', 'ecomus' ),
				'default'         => array(
					'width'  => '',
					'height' => '',
				),
				'active_callback' => array(
					array(
						'setting'  => 'logo_type',
						'operator' => '!=',
						'value'    => 'text',
					),
				),
			),
			'mobile_header_hamburger_menu_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'mobile_header_hamburger_menu_text'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Hamburger Menu Text', 'ecomus' ),
				'default'         => '',
			),
			'mobile_header_account_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'header_mobile_account_display' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Account Display', 'ecomus' ),
				'default'         => 'icon',
				'choices'         => array(
					'icon'   => esc_html__( 'Icon Only', 'ecomus' ),
					'icon-text'  => esc_html__( 'Icon & Text', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'mobile_header_wishlist_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'header_mobile_wishlist_display' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Wishlist Display', 'ecomus' ),
				'default'         => 'icon',
				'choices'         => array(
					'icon'   => esc_html__( 'Icon Only', 'ecomus' ),
					'icon-text'  => esc_html__( 'Icon & Text', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'mobile_header_compare_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'header_mobile_compare_display' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Compare Display', 'ecomus' ),
				'default'         => 'icon',
				'choices'         => array(
					'icon'   => esc_html__( 'Icon Only', 'ecomus' ),
					'icon-text'  => esc_html__( 'Icon & Text', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'mobile_header_html_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'header_mobile_custom_html'       => array(
				'type'            => 'textarea',
				'label'           => esc_html__( 'Custom HTML', 'ecomus' ),
				'description'     => esc_html__( 'Paste your HTML here', 'ecomus' ),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
		);


		// Mobile Product Catalog
		$settings['mobile_product_catalog'] = array(
			'mobile_product_columns'     => array(
				'label'   => esc_html__( 'Product Columns', 'ecomus' ),
				'type'    => 'select',
				'default' => '2',
				'choices' => array(
					'1' => esc_html__( '1 Column', 'ecomus' ),
					'2' => esc_html__( '2 Columns', 'ecomus' ),
				),
			),
			'mobile_catalog_sidebar'     => array(
				'label'   => esc_html__( 'Show Catalog Sidebar', 'ecomus' ),
				'description' => esc_html__( 'Show the sidebar on the catalog page by clicking on the icon', 'ecomus' ),
				'type'    => 'select',
				'default' => 'sidebar-menu',
				'choices' => array(
					'sidebar-menu' => esc_html__( 'Side Menu', 'ecomus' ),
					'filter-toolbar' => esc_html__( 'Filter on the Toolbar', 'ecomus' ),
				),
			),
		);

		// Mobile Product Card
		$settings['mobile_product_card'] = array(
			'mobile_product_card_featured_icons'        => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Always Show Featured Icons', 'ecomus' ),
				'default'         => true,
			),
			'mobile_product_card_atc'        => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Show Add To Cart Button', 'ecomus' ),
				'default'         => false,
				'active_callback' => array(
					array(
						'setting'  => 'product_card_layout',
						'operator' => 'in',
						'value'    => [ '1', '2', '3', '4', '5' ],
					),
				),
			),
			'mobile_product_card_wishlist' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Wishlist button', 'ecomus' ),
				'default' => true,
			),
			'mobile_product_card_compare' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Compare button', 'ecomus' ),
				'default' => true,
			),
		);

		// Mobile Navigation Bar
		$settings['mobile_navigation_bar'] = array(
			'mobile_navigation_bar' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Navigation Bar', 'ecomus' ),
				'default' => false,
			),
			'mobile_navigation_breakpoint' => array(
				'type'      => 'slider',
				'label'       => esc_html__( 'Breakpoint (px)', 'ecomus' ),
				'description' => esc_html__( 'Set a breakpoint where the mobile navigation bar displays.', 'ecomus' ),
				'transport' => 'postMessage',
				'default'   => '767',
				'choices'   => array(
					'min' => 375,
					'max' => 1199,
				),
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'mobile_navigation_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'mobile_navigation_bar_items'           => array(
				'type'            => 'sortable',
				'label'           => esc_html__( 'Items', 'ecomus' ),
				'default'         => array( 'shop', 'search', 'account', 'wishlist', 'cart' ),
				'choices'         => $this->navigation_bar_items_option(),
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'shop_view_all_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'mobile_shop_view_all' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Shop View All', 'ecomus' ),
				'default' => true,
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'mobile_navigation_bar_shop_icon_behaviour'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Shop Icon Behaviour', 'ecomus' ),
				'default'         => 'link',
				'choices'         => array(
					'menu' 	=> esc_html__( 'Open the menu popup', 'ecomus' ),
					'link' 	=> esc_html__( 'Navigate to a page', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'mobile_navigation_bar_items',
						'operator' => 'contains',
						'value'    => 'shop',
					),
				),
			),
			'mobile_navigation_bar_shop_menu_link'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'View All Link', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'mobile_navigation_bar_items',
						'operator' => 'contains',
						'value'    => 'shop',
					),
					array(
						'setting'  => 'mobile_navigation_bar_shop_icon_behaviour',
						'operator' => '==',
						'value'    => 'menu',
					),
				),
			),
			'mobile_navigation_bar_shop_menu'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Shop Menu', 'ecomus' ),
				'default'         => '',
				'choices'         => $this->get_menus(),
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'mobile_navigation_bar_shop_icon_behaviour',
						'operator' => '==',
						'value'    => 'menu',
					),
					array(
						'setting'  => 'mobile_navigation_bar_items',
						'operator' => 'contains',
						'value'    => 'shop',
					),
				),
			),
			'mobile_navigation_bar_shop_link'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Custom Shop Menu Link', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'mobile_navigation_bar_items',
						'operator' => 'contains',
						'value'    => 'shop',
					),
					array(
						'setting'  => 'mobile_navigation_bar_shop_icon_behaviour',
						'operator' => '==',
						'value'    => 'link',
					),
				),
			),
			'mobile_navigation_bar_custom_link_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr><h3>' . esc_html__( 'Custom Link', 'ecomus' ) . '</h3>',
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'mobile_navigation_bar_items',
						'operator' => 'contains',
						'value'    => 'customlink',
					),
				),
			),
			'mobile_navigation_bar_custom_link_type'      => array(
				'type'    => 'radio',
				'label'   => esc_html__( 'Image or Icon', 'ecomus' ),
				'default' => 'image',
				'choices' => array(
					'image' => esc_html__( 'Image', 'ecomus' ),
					'svg'   => esc_html__( 'SVG', 'ecomus' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'mobile_navigation_bar_items',
						'operator' => 'contains',
						'value'    => 'customlink',
					),
				),
			),
			'mobile_navigation_bar_custom_link_svg'       => array(
				'type'            => 'textarea',
				'label'           => esc_html__( 'SVG', 'ecomus' ),
				'description'     => esc_html__( 'Paste SVG code of your here', 'ecomus' ),
				'sanitize_callback' => 'Ecomus\Icon::sanitize_svg',
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'mobile_navigation_bar_items',
						'operator' => 'contains',
						'value'    => 'customlink',
					),
					array(
						'setting'  => 'mobile_navigation_bar_custom_link_type',
						'operator' => '==',
						'value'    => 'svg',
					),
				),
			),
			'mobile_navigation_bar_custom_link_image' => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Image', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'mobile_navigation_bar_items',
						'operator' => 'contains',
						'value'    => 'customlink',
					),
					array(
						'setting'  => 'mobile_navigation_bar_custom_link_type',
						'operator' => '==',
						'value'    => 'image',
					),
				),
			),
			'mobile_navigation_bar_custom_link_image_dimension' => array(
				'type'            => 'dimensions',
				'label'           => esc_html__( 'Image Dimension', 'ecomus' ),
				'default'         => array(
					'width'  => 'auto',
					'height' => 'auto',
				),
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'mobile_navigation_bar_items',
						'operator' => 'contains',
						'value'    => 'customlink',
					),
					array(
						'setting'  => 'mobile_navigation_bar_custom_link_type',
						'operator' => '==',
						'value'    => 'image',
					),
				),
			),
			'mobile_navigation_bar_custom_link_text'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Text', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'mobile_navigation_bar_items',
						'operator' => 'contains',
						'value'    => 'customlink',
					),
				),
			),
			'mobile_navigation_bar_custom_link_url'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Link', 'ecomus' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'mobile_navigation_bar_items',
						'operator' => 'contains',
						'value'    => 'customlink',
					),
				),
			),
			'mobile_navigation_bar_custom_style_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'mobile_navigation_bar_custom_style_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr><h3>' . esc_html__( 'Custom Style', 'ecomus' ) . '</h3>'
			),
			'mobile_navigation_bar_background_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color', 'ecomus' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => '.ecomus-mobile-navigation-bar',
						'property' => 'background-color',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'mobile_navigation_bar_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'ecomus' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => '.ecomus-mobile-navigation-bar',
						'property' => 'color',
					),
					array(
						'element'  => '.ecomus-mobile-navigation-bar .ecomus-mobile-navigation-bar__icon',
						'property' => '--em-button-color',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'mobile_navigation_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		$settings['rtl'] = array(
			'rtl_smart'     => array(
				'type'        => 'toggle',
				'default'     => false,
				'label'       => esc_html__('Smart RTL', 'ecomus'),
				'description' => esc_html__('Enable this option to automatically change background image, padding, and position... to RTL', 'ecomus'),
			),
		);

		return array(
			'theme'    => 'ecomus',
			'panels'   => apply_filters( 'ecomus_customize_panels', $panels ),
			'sections' => apply_filters( 'ecomus_customize_sections', $sections ),
			'settings' => apply_filters( 'ecomus_customize_settings', $settings ),
		);

	}

	/**
	 * Get nav menus
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_menus() {
		if ( ! is_admin() ) {
			return [];
		}

		$menus = wp_get_nav_menus();
		if ( ! $menus ) {
			return [];
		}

		$output = array(
			0 => esc_html__( 'Select Menu', 'ecomus' ),
		);
		foreach ( $menus as $menu ) {
			$output[ $menu->slug ] = $menu->name;
		}

		return $output;
	}

	/**
	 * Get the list of fonts for Kirki
	 *
	 * @return array
	 */
	public static function customizer_fonts_choices() {
		if( get_theme_mod('typo_font_family', true) ) {
			$args_fonts = array(
				'families' => array(
					array( 'id' => 'Albert Sans', 'text' => 'Albert Sans' ),
				),
				'variants' => array(
					'Albert Sans' => array( 'regular', '500', '600', '700', '800' ),
				),
			);
		} else {
			$args_fonts = array();
		}

		// Compatible custom fonts plugin
		if( defined( 'BSF_CUSTOM_FONTS_POST_TYPE' ) ) {
			$args                 = array(
				'post_type'      => BSF_CUSTOM_FONTS_POST_TYPE,
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'posts_per_page' => apply_filters( 'ecomus_custom_fonts_admin_query_limit', 100 ),
			);

			$query = new \WP_Query( $args );
			$bsf_fonts = $query->posts;

			if ( ! empty( $bsf_fonts ) ) {
				foreach ( $bsf_fonts as $key => $post_id ) {
					$bsf_font_data = get_post_meta( $post_id, 'fonts-data', true );
					$variants = [];
					foreach( $bsf_font_data['variations'] as $variations ) {
						$variants[] = $variations['font_weight'] == '400' ? 'regular' : $variations['font_weight'];
					}

					$args_fonts['families'][] = array(
						'id' => $bsf_font_data['font_name'],
						'text' => $bsf_font_data['font_name']
					);

					$args_fonts['variants'][$bsf_font_data['font_name']] = $variants;
				}
			}

			wp_reset_postdata();
		}

		$custom_fonts = apply_filters( 'ecomus_custom_fonts_options', $args_fonts );

		$fonts = array(
			'standard' => array( 'serif', 'sans-serif', 'monospace' ),
			'google'   => array(),
		);

		if ( ! empty( $custom_fonts) && ! empty( $custom_fonts['families'] ) ) {
			$fonts['families'] = array(
				'custom' => array(
					'text'     => esc_html__( 'Ecomus Custom Fonts', 'ecomus' ),
					'children' => $custom_fonts['families'],
				),
			);

			if ( ! empty( $custom_fonts['variants'] ) ) {
				$fonts['variants'] = $custom_fonts['variants'];
			}
		}

		return apply_filters( 'ecomus_customize_fonts_choices', array(
			'fonts' => $fonts,
		) );
	}

	/**
	 * Display header search
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function display_header_search_icon_layout() {
		if ( 'custom' == get_theme_mod( 'header_present' ) ) {
			if ( 'icon' == get_theme_mod( 'header_search_layout' ) ) {
				return true;
			}

			return false;
		} else {
			if(  in_array( get_theme_mod( 'header_version' ), array( 'v1', 'v2', 'v3', 'v4', 'v5' ) ) ) {
				return true;
			}

			return false;
		}
	}
}
