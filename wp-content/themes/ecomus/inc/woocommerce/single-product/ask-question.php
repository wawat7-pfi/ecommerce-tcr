<?php
/**
 * Single Product hooks.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce\Single_Product;

use Ecomus\Helper;
use Ecomus\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Single Product
 */
class Ask_Question {
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
		add_action( 'woocommerce_single_product_summary', array( $this, 'ask_question' ), 34 );
	}

	/**
	 * Product Share
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function ask_question() {
		if( ! apply_filters( 'ecomus_ask_question_content', true ) ) {
			return;
		}

		\Ecomus\Theme::set_prop( 'modals', 'product-ask-question' );
		
		echo '<a href="#" class="ecomus-extra-link-item ecomus-extra-link-item--ask-question em-font-semibold" data-toggle="modal" data-target="product-ask-question-modal">'. Icon::get_svg( 'question' ) . esc_html__( 'Ask a question', 'ecomus' ) . '</a>';
	}

	/**
	 * Product Share data
	 */
	public static function ask_question_data() {
		return Helper::get_option( 'product_ask_question_form' );
	}
}
