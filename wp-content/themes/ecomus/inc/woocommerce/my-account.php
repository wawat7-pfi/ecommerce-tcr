<?php
/**
 * Hooks of Account.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Account template.
 */
class My_Account {
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
		add_filter('get_the_archive_title', array( $this, 'page_header_title' ), 40);
	}

	/**
	 * Page Title
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function page_header_title($title) {
		if( is_user_logged_in() ) {
			return $title;
		}

		if( function_exists('is_lost_password_page') && is_lost_password_page() ) {
			return esc_html__('Lost Password', 'ecomus');
		}

		$mode = $_GET && isset( $_GET['mode'] ) ? $_GET['mode'] : '';
		if( $mode == 'register' ) {
			$title = esc_html__('Register', 'ecomus');
		} elseif( empty( $mode ) || $mode == 'login' ) {
			$title = esc_html__('Login', 'ecomus');
		}

		return $title;
	}
}
