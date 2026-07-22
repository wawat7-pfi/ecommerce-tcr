<?php
/**
 * Posts functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

use Ecomus\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Posts initial
 *
 */
class Sidebar {
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
		// Sidebar
		add_action( 'widgets_init', array( $this, 'woocommerce_widgets_register' ) );
		add_filter( 'ecomus_primary_sidebar_id', array( $this, 'sidebar_id' ), 10 );
		add_action( 'dynamic_sidebar_before', array( $this, 'catalog_sidebar_before_content' ) );
		add_action( 'dynamic_sidebar_after', array( $this, 'catalog_sidebar_after_content' ) );

		// Catalog sidebar panel
		add_action( 'ecomus_after_close_site_footer', array( $this, 'catalog_sidebar_panel' ), 1 );
	}

	/**
	 * Register widget areas.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function woocommerce_widgets_register() {
		$sidebars = array(
			'single-product-extra-content' => esc_html__( 'Single Product Extra Content', 'ecomus' ),
		);

		// Register sidebars
		foreach ( $sidebars as $id => $name ) {
			register_sidebar(
				array(
					'name'          => $name,
					'id'            => $id,
					'description'   => esc_html__( 'Add sidebar for the product page', 'ecomus' ),
					'before_widget' => '<section id="%1$s" class="widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h4 class="widget-title">',
					'after_title'   => '</h4>',
				)
			);
		}
	}

	/**
	 * Sidebar ID
	 *
	 * @return void
	 */
	public function sidebar_id( $sidebarID ) {
		if ( is_active_sidebar( 'catalog-sidebar' ) ) {
			$sidebarID = 'mobile-sidebar-panel';
		}

		return $sidebarID;
	}

	/**
	 * Add modal content before Widget Content
	 *
	 * @since 1.0.0
	 *
	 * @param $index
	 *
	 * @return void
	 */
	public function catalog_sidebar_before_content( $index ) {
		if ( is_admin() ) {
			return;
		}

		if ( $index != 'catalog-sidebar' ) {
			return;
		}

		if ( ! apply_filters( 'ecomus_get_catalog_sidebar_before_content', true ) ) {
			return;
		}

		?>
		<div class="sidebar__backdrop"></div>
        <div class="sidebar__container">
			<?php echo \Ecomus\Icon::get_svg( 'close', 'ui', 'class=sidebar__button-close' ); ?>
			<div class="sidebar__header">
				<?php echo esc_html__( 'Sidebar', 'ecomus' ); ?>
			</div>
			<div class="sidebar__content">
		<?php

	}

	/**
	 * Change blog sidebar after content
	 *
	 * @since 1.0.0
	 *
	 * @param $index
	 *
	 * @return void
	 */
	public function catalog_sidebar_after_content( $index ) {
		if ( is_admin() ) {
			return;
		}

		if ( $index != 'catalog-sidebar' ) {
			return;
		}

		if ( ! apply_filters( 'ecomus_get_catalog_sidebar_before_content', true ) ) {
			return;
		}

		?>
        	</div>
        </div>
		<?php

	}

	/**
	 * Blog sidebar panel
	 *
	 * @return void
	 */
	public function catalog_sidebar_panel() {
		if( ! apply_filters( 'ecomus_product_catalog_sidebar_panel', true ) ) {
			return;
		}

		if( ! Helper::is_catalog() ) {
			return;
		}

		if( Helper::get_option( 'mobile_catalog_sidebar' ) !== 'sidebar-menu' ) {
			return;
		}
		
		if( Helper::get_option( 'product_catalog_sidebar' ) == 'no-sidebar' || ! is_active_sidebar( 'catalog-sidebar' ) ) {
			return;
		}

		$position = Helper::get_option( 'product_catalog_sidebar' ) == 'sidebar-content' ? 'left': 'right';
		?>
			<div class="mobile-sidebar-panel__button em-fixed em-flex em-flex-align-center em-flex-center mobile-sidebar-position--<?php echo esc_attr( $position ); ?>" data-toggle="off-canvas" data-target="mobile-sidebar-panel">
				<?php echo \Ecomus\Icon::get_svg( 'sidebar' ); ?>
			</div>
		<?php
	}
}