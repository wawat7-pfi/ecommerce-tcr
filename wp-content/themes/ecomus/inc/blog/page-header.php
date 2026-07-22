<?php
/**
 * Ecomus Blog Header functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus\Blog;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ecomus Post
 *
 */
class Page_Header {

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
		add_filter('ecomus_page_header_classes', array( $this, 'classes' ));
		add_filter('ecomus_get_page_header_elements', array( $this, 'elements' ));
	}

	/**
	 * Page Header Classes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function classes( $classes ) {
		if( \Ecomus\Helper::is_blog() || is_search() ) {
			$classes .= ' page-header--blog';
		}

		return $classes;
	}

	/**
	 * Page Header Elements
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function elements( $items ) {
		if( \Ecomus\Helper::is_blog() || is_search() ) {
			$items = \Ecomus\Helper::get_option('blog_header') ? (array) \Ecomus\Helper::get_option( 'blog_header_els' ) : [];
		} elseif( is_singular('post') ) {
			$items = [];
		}

		return $items;
	}
}
