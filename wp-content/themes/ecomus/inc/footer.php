<?php
/**
 * Footer functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Footer initial
 *
 */
class Footer {
		/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;


	/**
	 * Footer ID
	 *
	 * @var $post_id
	 */
	protected static $footer_id = null;


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
		add_action( 'wp_footer', array( $this, 'progress_bar' ) );
		add_action( 'ecomus_after_close_site_footer', array( $this, 'gotop_button' ) );
	}

	/**
	 * Progress bar display
	 *
	 * @return void
	 */
	public function progress_bar() {
		?><div class="em-progress-bar"></div><?php
	}

	/**
	 * Add this back-to-top button to footer
	 *
	 * @since 1.0.0
	 *
	 * @return  void
	 */
	public function gotop_button() {
		if ( apply_filters( 'ecomus_get_back_to_top', \Ecomus\Helper::get_option( 'backtotop' ) ) ) {
			echo '<a href="#page" id="gotop" class="em-button em-button-outline em-button-icon em-button-go-top em-fixed">' . \Ecomus\Icon::get_svg( 'pr-arrow' ) . '</a>';
		}
	}
}
