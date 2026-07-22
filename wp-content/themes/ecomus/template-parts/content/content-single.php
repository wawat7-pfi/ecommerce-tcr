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
	<header class="entry-header">
		<?php \Ecomus\Blog\Post::category('em-badge-outline'); ?>
		<?php \Ecomus\Blog\Post::title('h1', true); ?>
		<div class="entry-meta text-center">
			<?php \Ecomus\Blog\Post::author(); ?>
			<?php \Ecomus\Blog\Post::date(); ?>
		</div>
	</header>
	<?php if(! \Ecomus\Blog\Single::sidebar()) { ?>
		<?php \Ecomus\Blog\Post::image(); ?>
	<?php } ?>
	<?php \Ecomus\Blog\Post::content(); ?>
	<footer class="entry-footer clearfix"><?php \Ecomus\Blog\Post::tags(); ?><?php \Ecomus\Blog\Post::share(); ?></footer>
</article>
