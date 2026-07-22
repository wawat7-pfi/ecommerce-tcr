<?php
/**
 * The template part for displaying related posts
 *
 * @package Ecomus
 */

$related_posts = new WP_Query( apply_filters( 'ecomus_related_posts_args', array(
	'post_type'           => 'post',
	'posts_per_page'      => intval(\Ecomus\Helper::get_option('posts_related_number')),
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'order'               => 'rand',
	'post__not_in'        => array( $post->ID ),
	'tax_query'           => array(
		'relation' => 'OR',
		array(
			'taxonomy' => 'category',
			'field'    => 'term_id',
			'terms'    => \Ecomus\Blog\Post::get_related_terms( 'category', $post->ID ),
			'operator' => 'IN',
		),
		array(
			'taxonomy' => 'post_tag',
			'field'    => 'term_id',
			'terms'    => \Ecomus\Blog\Post::get_related_terms( 'post_tag', $post->ID ),
			'operator' => 'IN',
		),
	),
	'no_found_rows'          => true,
	'update_post_term_cache' => false,
	'update_post_meta_cache' => false,
	'cache_results'          => false,
	'ignore_sticky_posts'    => true,
) ) );

$posts_spacing = intval(\Ecomus\Helper::get_option('posts_related_spacing'));

if ( ! $related_posts->have_posts() ) {
	return;
}

?>
    <div class="ecomus-posts-related ecomus-carousel--elementor" data-spacing=<?php echo esc_attr( $posts_spacing ); ?>>
        <h5 class="ecomus-posts-related__heading text-center"><?php esc_html_e( 'Related Articles', 'ecomus' ); ?></h5>
        <div class="ecomus-posts-related__content navigation-class-dots swiper">
			<div class="ecomus-posts-related__inner swiper-wrapper">
				<?php
					while ( $related_posts->have_posts() ) : $related_posts->the_post();

						get_template_part( 'template-parts/content/content', 'related' );

					endwhile;
				?>
			</div>
			<span class="swiper-pagination swiper-pagination-bullet--small"></span>
        </div>
    </div>
<?php
wp_reset_postdata();