<?php
/**
 * Cache Manager - Centralized cache system for Ecomus theme.
 *
 * Uses WordPress transients for caching dynamic content.
 *
 * @package Ecomus
 * @since 1.0.0
 */

namespace Ecomus;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cache Manager class.
 */
class Cache_Manager {
	/**
	 * Cache keys.
	 */
	const CACHE_KEY_CSS   = 'ecomus_dynamic_css';
	const CACHE_KEY_FONTS = 'ecomus_fonts_css_output';

	/**
	 * Instance.
	 *
	 * @var Cache_Manager|null
	 */
	protected static $instance = null;

	/**
	 * Transient prefix.
	 *
	 * @var string
	 */
	protected static $transient_prefix = 'ecomus_cache_';

	/**
	 * Get instance.
	 *
	 * @return Cache_Manager
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! apply_filters( 'ecomus_enable_cache_manager', true ) ) {
			return;
		}

		$this->register_hooks();
	}

	/**
	 * Register cleanup hooks.
	 */
	protected function register_hooks() {
		add_action( 'customize_save_after', array( $this, 'clear_css_cache' ) );
		add_action( 'switch_theme', array( $this, 'clear_all_caches' ) );
	}

	/**
	 * Get transient key.
	 *
	 * @param string $key Cache key.
	 * @return string
	 */
	protected static function transient_key( $key ) {
		return self::$transient_prefix . $key;
	}

	/**
	 * Get cached value.
	 *
	 * @param string $key Cache key.
	 * @return mixed|false
	 */
	public static function get( $key ) {
		if ( ! apply_filters( 'ecomus_enable_cache_manager', true ) ) {
			return false;
		}
		return get_transient( self::transient_key( $key ) );
	}

	/**
	 * Set cache value.
	 *
	 * @param string $key        Cache key.
	 * @param mixed  $value      Value to cache.
	 * @param int    $expiration Expiration in seconds. Default WEEK_IN_SECONDS.
	 * @return bool
	 */
	public static function set( $key, $value, $expiration = WEEK_IN_SECONDS ) {
		if ( ! apply_filters( 'ecomus_enable_cache_manager', true ) ) {
			return false;
		}
		return set_transient( self::transient_key( $key ), $value, $expiration );
	}

	/**
	 * Delete cached value.
	 *
	 * @param string $key Cache key.
	 * @return bool
	 */
	public static function delete( $key ) {
		return delete_transient( self::transient_key( $key ) );
	}

	/**
	 * Clear all Ecomus caches.
	 */
	public function clear_all_caches() {
		self::delete( self::CACHE_KEY_CSS );
		self::delete( self::CACHE_KEY_FONTS );
	}

	/**
	 * Clear CSS and Fonts cache when Customizer is saved.
	 */
	public function clear_css_cache() {
		self::delete( self::CACHE_KEY_CSS );
		self::delete( self::CACHE_KEY_FONTS );
	}
}
