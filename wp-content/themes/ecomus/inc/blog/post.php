<?php
/**
 * Ecomus Blog Post functions and definitions.
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
class Post {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;


	/**
	 * $fields
	 *
	 * @var $fields
	 */
	protected static $fields = [];

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
	}

	/**
	 * Get entry thumbmail
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function thumbnail( $size = 'large' ) {
		if ( ! has_post_thumbnail() ) {
			return;
		}

		$size = apply_filters('ecomus_get_post_thumbnail_size', $size);

		$get_image = wp_get_attachment_image( get_post_thumbnail_id( get_the_ID() ), $size);

		if ( empty( $get_image ) ) {
			return;
		}

		echo sprintf(
			'<a class="post-thumbnail em-ratio em-eff-img-zoom em-image-rounded" href="%s" aria-hidden="true" tabindex="-1">%s%s</a>',
			esc_url( get_permalink() ),
			$get_image,
			self::get_format_icon()
		);
	}

	/**
	 * Get format
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function get_format_icon() {
		$icon = '';
		switch ( get_post_format() ) {
			case 'video':
				$icon = \Ecomus\Icon::get_svg( 'video', 'ui', array( 'class' => 'post-format-icon icon-video' ) );
				break;

			case 'gallery':
				$icon = \Ecomus\Icon::get_svg( 'gallery', 'ui', array( 'class' => 'post-format-icon icon-gallery' ) );
				break;
		}

		return $icon;
	}

	/**
	 * Get post image
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function image() {
		if ( ! has_post_thumbnail() ) {
			return;
		}
		echo '<div class="entry-thumbnail entry-single-thumbnail em-ratio">' . get_the_post_thumbnail( get_the_ID(), 'full' ) . '</div>';
	}


	/**
	 * Get entry title
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function title($tag = 'h2', $single = false) {
		if( $single ) {
			the_title( '<'.$tag.' class="entry-title em-font-h4 text-center">', '</'.$tag.'>' );
		} else {
			the_title( '<'.$tag.' class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></'.$tag.'>' );
		}
	}


	/**
	 * Get category
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function category($class = 'em-badge-light') {
		$categories = get_the_category( get_the_ID() );
		if ( empty( $categories ) ){
			return;
		}

		echo '<div class="entry-category">';
		echo '<a class="em-badge em-button-category '. esc_attr( $class ) .'" href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . $categories[0]->name . '</a>';
		echo '</div>';
	}


	/**
	 * Meta author
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function author() {
		$byline = sprintf(
		/* translators: %s: post author. */
			esc_html_x( 'by %s', 'post author', 'ecomus' ),
			'<a class="em-font-semibold" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a>'
		);

		printf( '<span class="entry-meta__author">%s</span>', $byline );
	}

	/**
	 * Meta date
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function date() {
		printf( '<span class="entry-meta__date">%s <span class="em-font-semibold em-color-dark">%s</span></span>', esc_html( 'on', 'ecomus' ), esc_html( get_the_date() ) );
	}

	/**
	 * Meta comment
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function comment() {
		echo '<span class="entry-meta__comments">' . \Ecomus\Icon::get_svg( 'comment-mini' ) . get_comments_number() . '</span>';
	}


	/**
	 * Get Excerpt
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function excerpt() {
		$enable = \Ecomus\Helper::get_option('blog_excerpts');
		if ( \Ecomus\Helper::get_option('blog_layout') == 'list' ) {
			$enable = \Ecomus\Helper::get_option('blog_excerpts_list');
		}

		if ( ! $enable ) {
			return;
		}

		$length = \Ecomus\Helper::get_option('blog_excerpts_length');
		$length = ! empty( $length ) ? $length : 18;

		echo '<div class="entry-excerpt">';
			echo \Ecomus\Helper::get_content_limit( $length, '' );
		echo '</div>';
	}

	/**
	 * Get Content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function content() {
		echo '<div class="entry-content">';
		the_content();
		echo '</div>';
	}

	/**
	 * Readmore button
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function button() {
		echo sprintf(
			'<div class="entry-read-more"><a class="em-button em-button-subtle" href="%s"><span class="ecomus-button-text">%s</span> %s</a></div>',
			get_permalink(),
			esc_html__( 'Read More', 'ecomus' ),
			\Ecomus\Icon::get_svg( 'arrow-top' )
		);
	}

	/**
	 * Meta tag
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function tags() {
		$terms = get_the_terms( get_the_ID(), 'post_tag' );

		if ( empty( $terms ) ) {
			return;
		}

		echo '<div class="entry-tags">';
		the_tags('', '');
		echo '</div>';
	}

	/**
	 * Get entry share social
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function share() {
		if( ! \Ecomus\Helper::get_option('post_sharing') ) {
			return;
		}
		echo '<div class="entry-meta__share">';
		echo '<span class="entry-meta-label em-color-dark">' . esc_html__('Share:', 'ecomus') . '</span>';
		echo \Ecomus\Helper::share_socials();
		echo '</div>';
	}

	/**
	 * Related post
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_related_terms( $term, $post_id = null ) {
		$post_id     = $post_id ? $post_id : get_the_ID();
		$terms_array = array( 0 );

		$terms = wp_get_post_terms( $post_id, $term );
		foreach ( $terms as $term ) {
			$terms_array[] = $term->term_id;
		}

		return array_map( 'absint', $terms_array );
	}

}
