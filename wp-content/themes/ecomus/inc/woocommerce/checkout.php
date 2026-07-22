<?php
/**
 * Hooks of checkout.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of checkout template.
 */
class Checkout {
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
		// Wrap checkout login and coupon notices.
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'before_login_form' ), 10 );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'login_form' ), 10 );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'coupon_form' ), 10 );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'after_login_form' ), 10 );

		add_filter( 'woocommerce_checkout_coupon_message', array( $this, 'coupon_form_name' ), 10);
	}

	/**
	 * Checkout Before login form.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function before_login_form() {
		echo '<div class="checkout-form-cols">';
	}

	/**
	 * Checkout After login form.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function after_login_form() {
		echo '</div>';
	}

	/**
	 * Checkout login form.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function login_form() {
		if ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
			return;
		}

		echo '<div class="checkout-login checkout-form-col col-flex em-md-6 em-sm-6 em-xs-12">';
		woocommerce_checkout_login_form();
		echo '</div>';
	}

	/**
	 * Checkout coupon form.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function coupon_form() {
		if ( ! wc_coupons_enabled() ) {
			return;
		}

		echo '<div class="checkout-coupon checkout-form-col col-flex em-md-6 em-sm-6 em-xs-12">';
		woocommerce_checkout_coupon_form();
		echo '</div>';
	}

	/**
	 * Checkout coupon form.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function coupon_form_name( $html) {
		if ( ! wc_coupons_enabled() ) {
			return;
		}

		return esc_html__( 'Have a coupon?', 'ecomus' ) . ' <a href="#" class="showcoupon">' . esc_html__( 'Enter your code', 'ecomus' ) . '</a>' ;
	}

}
