<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Ecomus
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php \Ecomus\Blog\Post::thumbnail(); ?>
	<div class="entry-summary em-flex em-flex-column em-flex-center">
		<?php \Ecomus\Blog\Post::category(); ?>
		<?php \Ecomus\Blog\Post::title('h5'); ?>
		<?php \Ecomus\Blog\Post::excerpt(); ?>
		<?php \Ecomus\Blog\Post::button(); ?>
	</div>
</article>