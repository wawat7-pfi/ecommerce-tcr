<?php
/**
 * WPML compatibility functions
 *
 * @package Ecomus
 */

namespace Ecomus\Languages;

use \Ecomus\Helper;

class WPML {
	const CAMPAIGNS_DOMAIN = 'Campaign Bar';
	const CAMPAIGN_PREFIX = 'campaign_';
	const TOPBAR_SLIDES_DOMAIN = 'Topbar Slides';
	const TOPBAR_SLIDES_PREFIX = 'topbar_slide_';
	const SEARCH_LINKS_DOMAIN = 'Search Links';
	const SEARCH_LINK_PREFIX = 'search_link_';

	/**
	 * The single instance of the class
	 *
	 * @var WPML
	 */
	protected static $instance = null;

	/**
	 * Main instance
	 *
	 * @return WPML
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'customize_save_after', array( $this, 'register_strings' ) );

		add_filter( 'ecomus_campaign_item_args', array( $this, 'translate_campaign_item_args' ), 10, 2 );
		add_filter( 'ecomus_topbar_slides', array( $this, 'translate_topbar_slides' ) );
		add_filter( 'motta_search_quicklinks', array( $this, 'translate_search_quicklinks' ) );

	}

	/**
	 * Register special theme strings for translation
	 *
	 * @return void
	 */
	public function register_strings() {
		$this->register_campaign_strings();
		$this->register_topbar_slides_strings();
	}

	/**
	 * Register campaign strings for translation
	 */
	public function register_campaign_strings() {
		$campaigns = array_filter( (array) Helper::get_option( 'campaign_items' ) );

		if ( empty( $campaigns ) ) {
			return;
		}

		foreach ( $campaigns as $id => $campaign ) {
			$count = $id + 1;

			do_action( 'wpml_register_single_string', self::CAMPAIGNS_DOMAIN, self::CAMPAIGN_PREFIX . $count . '_text', $campaign['text'] );
		}
	}

	/**
	 * Register header search links for translation
	 */
	public function register_search_link_strings() {
		$links = ( array ) Helper::get_option( 'header_search_links' );

		if ( empty( $links ) ) {
			return;
		}

		foreach ( $links as $id => $link ) {
			$count = $id + 1;

			do_action( 'wpml_register_single_string', self::SEARCH_LINKS_DOMAIN, self::SEARCH_LINK_PREFIX . $count . '_text', $link['text'] );
			do_action( 'wpml_register_single_string', self::SEARCH_LINKS_DOMAIN, self::SEARCH_LINK_PREFIX . $count . '_url', $link['url'] );
		}
	}

	/**
	 * Register header search links for translation
	 */
	public function register_topbar_slides_strings() {
		$links = ( array ) Helper::get_option( 'topbar_slides' );

		if ( empty( $links ) ) {
			return;
		}

		foreach ( $links as $id => $link ) {
			$count = $id + 1;

			do_action( 'wpml_register_single_string', self::TOPBAR_SLIDES_DOMAIN, self::TOPBAR_SLIDES_PREFIX . $count . '_text', $link['text'] );
		}
	}

	/**
	 * Apply the WPML translation for campaign items
	 *
	 * @param array $args
	 * @param int $id
	 *
	 * @return array
	 */
	public function translate_campaign_item_args( $args, $id ) {
		$count = $id + 1;

		$args['text']   = apply_filters( 'wpml_translate_single_string', $args['text'], self::CAMPAIGNS_DOMAIN, self::CAMPAIGN_PREFIX . $count . '_text' );

		return $args;
	}

	/**
	 * Apply the WPML translation for search quick links
	 *
	 * @param array $links
	 *
	 * @return array
	 */
	public function translate_topbar_slides( $slides ) {
		if ( empty( $slides ) ) {
			return $slides;
		}

		foreach ( $slides as $id => $slide ) {
			$count = $id + 1;

			$slides[ $id ]['text'] = apply_filters( 'wpml_translate_single_string', $slide['text'], self::TOPBAR_SLIDES_DOMAIN, self::TOPBAR_SLIDES_PREFIX . $count . '_text' );
		}

		return $slides;
	}

	/**
	 * Apply the WPML translation for search quick links
	 *
	 * @param array $links
	 *
	 * @return array
	 */
	public function translate_search_quicklinks( $links ) {
		if ( empty( $links ) ) {
			return $links;
		}

		foreach ( $links as $id => $link ) {
			$count = $id + 1;

			$links[ $id ]['text'] = apply_filters( 'wpml_translate_single_string', $link['text'], self::SEARCH_LINKS_DOMAIN, self::SEARCH_LINK_PREFIX . $count . '_text' );
			$links[ $id ]['url']  = apply_filters( 'wpml_translate_single_string', $link['url'], self::SEARCH_LINKS_DOMAIN, self::SEARCH_LINK_PREFIX . $count . '_url' );
		}

		return $links;
	}

}