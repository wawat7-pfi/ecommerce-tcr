<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Ecomus
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('em-post-grid swiper-slide'); ?>>
	<?php if ( has_post_thumbnail() ) { ?>
		<div class="entry-header">
			<?php \Ecomus\Blog\Post::thumbnail(); ?>
			<?php \Ecomus\Blog\Post::category(''); ?>
		</div>
	<?php } ?>
	<?php \Ecomus\Blog\Post::title('h6'); ?>
	<?php \Ecomus\Blog\Post::button(); ?>
</article>