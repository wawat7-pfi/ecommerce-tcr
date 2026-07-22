<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Ecomus
 */

 if( ! \Ecomus\Helper::is_built_with_elementor() ) { ?>
<article id="post-<?php the_ID(); ?>" class="<?php echo join( ' ', get_post_class( '', get_the_ID() ) ); ?>" >
<?php }; ?>
	<?php the_content(); ?>
	<?php
		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'ecomus' ),
			'after'  => '</div>',
		) );
	?>
<?php if( ! \Ecomus\Helper::is_built_with_elementor() ) { ?>
</article>
<?php }; ?>