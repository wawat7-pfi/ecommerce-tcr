<?php
/**
 * Posts functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus\Blog;

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
		add_filter( 'ecomus_primary_sidebar_id', array( $this, 'sidebar_id' ), 10 );
		add_filter( 'ecomus_primary_sidebar_classes', array( $this, 'sidebar_classes' ), 10 );
		add_action( 'dynamic_sidebar_before', array( $this, 'blog_sidebar_before_content' ) );
		add_action( 'dynamic_sidebar_after', array( $this, 'blog_sidebar_after_content' ) );

		// Blog sidebar panel
		add_action( 'ecomus_after_close_site_footer', array( $this, 'blog_sidebar_panel' ), 1 );
	}

	/**
	 * Sidebar ID
	 *
	 * @return void
	 */
	public function sidebar_id( $sidebarID ) {
		if ( is_active_sidebar( 'blog-sidebar' ) ) {
			$sidebarID = 'mobile-sidebar-panel';
		}

		return $sidebarID;
	}

	/**
	 * Sidebar ID
	 *
	 * @return void
	 */
	public function sidebar_classes( $sidebarClass ) {
		if ( is_active_sidebar( 'blog-sidebar' ) && ( ( Helper::is_blog() && Helper::get_option( 'blog_sidebar' ) !== 'no-sidebar' ) || is_singular( 'post' ) ) ) {
			if( Helper::is_blog() && Helper::get_option( 'blog_sidebar' ) == 'sidebar-content' ) {
				$sidebarClass = $sidebarClass . ' offscreen-panel--side-right';
			}

			if( is_singular( 'post' ) && Helper::get_option( 'post_sidebar' ) == 'icon' ) {
				$sidebarClass = $sidebarClass . ' offscreen-panel';
			}

			if( is_singular( 'post' ) && Helper::get_option( 'post_layout' ) == 'sidebar-content' ) {
				$sidebarClass = $sidebarClass . ' offscreen-panel--side-right';
			}
		}

		return $sidebarClass;
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
	public function blog_sidebar_before_content( $index ) {
		if ( is_admin() ) {
			return;
		}

		if ( $index != 'blog-sidebar' ) {
			return;
		}

		if( Helper::is_blog() && Helper::get_option( 'blog_sidebar' ) == 'no-sidebar' ) {
			return;
		}

		if ( ! apply_filters( 'ecomus_get_blog_sidebar_before_content', true ) ) {
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
	public function blog_sidebar_after_content( $index ) {
		if ( is_admin() ) {
			return;
		}

		if ( $index != 'blog-sidebar' ) {
			return;
		}

		if( Helper::is_blog() && Helper::get_option( 'blog_sidebar' ) == 'no-sidebar' ) {
			return;
		}

		if ( ! apply_filters( 'ecomus_get_blog_sidebar_before_content', true ) ) {
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
	public function blog_sidebar_panel() {
		if( ( Helper::is_blog() && ( Helper::get_option( 'blog_sidebar' ) == 'no-sidebar' ) ) ) {
			return;
		}

		if( is_singular('post') && Helper::get_option('post_layout') == 'no-sidebar' ) {
			return;
		}

		if( ! is_active_sidebar( 'blog-sidebar' ) ) {
			return;
		}
		?>
			<div class="mobile-sidebar-panel__button em-fixed em-flex em-flex-align-center em-flex-center <?php echo Helper::is_blog() && Helper::get_option( 'blog_sidebar' ) == 'sidebar-content' ? 'mobile-sidebar-position--left' : ''; ?> <?php echo is_singular( 'post' ) && Helper::get_option( 'post_layout' ) == 'sidebar-content' ? 'mobile-sidebar-position--left' : ''; ?>" data-toggle="off-canvas" data-target="mobile-sidebar-panel">
				<?php echo \Ecomus\Icon::get_svg( 'sidebar' ); ?>
				<?php echo is_singular( 'post' ) ? '<span class="button-text em-font-medium">'. esc_html__( 'Open Sidebar', 'ecomus' ) .'</span>' : ''; ?>
			</div>
		<?php
	}
}