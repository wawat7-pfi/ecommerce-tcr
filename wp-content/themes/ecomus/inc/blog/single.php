<?php
/**
 * Single functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus\Blog;
use Ecomus\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Single initial
 */
class Single {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * $post
	 *
	 * @var $post
	 */
	protected $post = null;

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
		if (Helper::get_option('post_navigation') ) {
			add_action( 'ecomus_after_post_content', array( $this, 'navigation' ), 40 );
		}

		if (Helper::get_option('posts_related') ) {
			add_action( 'ecomus_after_post_content', array( $this, 'related_posts' ), 60 );
		}

		add_action( 'ecomus_site_layout', array( $this, 'content_layout' ));

		// Sidebar
		add_filter('ecomus_get_sidebar', array( $this, 'sidebar' ), 10 );

		// Body Class
		add_filter( 'body_class', array( $this, 'body_classes' ) );

		add_action( 'ecomus_after_site_content_open', array( $this, 'featured_image' ) );
	}

	/**
	 * Classes Body
	 */
	public function body_classes( $classes ) {
		$classes[] = 'em-post-layout-' . Helper::get_option( 'post_sidebar' );

		return $classes;
	}

	/**
	 * Meta post navigation
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */

	public function navigation() {
		get_template_part( 'template-parts/post/post', 'navigation');
	}

	/**
	 * Related post
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function related_posts() {
		get_template_part( 'template-parts/post/related-posts' );
	}

	/**
	 * Get site layout
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function content_layout($layout) {
		if ( ! is_active_sidebar( 'blog-sidebar' ) ) {
			$layout = 'no-sidebar';
		} else {
			$layout = Helper::get_option( 'post_layout' );
		}

		return $layout;
	}


	/**
	 * Get Sidebar
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function sidebar() {
		if ( ! is_active_sidebar( 'blog-sidebar' ) ) {
			return false;
		} elseif( Helper::get_option( 'post_layout' ) == 'no-sidebar') {
			return false;
		}

		return true;

	}

	public function featured_image() {
		if( \Ecomus\Blog\Single::sidebar() ) {
			\Ecomus\Blog\Post::image();
		}
	}
}
