<?php
/**
 * Template part for displaying post navigation
 *
 * @package Ecomus
 */

$next_post = get_next_post();
$prev_post = get_previous_post();

if ( ! $next_post && ! $prev_post ) {
	return;
}
?>

<nav class="navigation post-navigation em-flex" role="navigation">
	<div class="em-md-6 em-xs-6">
		<?php if ( $prev_post ) : ?>
			<a class="nav-previous em-flex" href="<?php echo esc_url( get_permalink( $prev_post ) ) ?>">
				<span class="em-button em-button-outline em-button-icon">
					<?php echo \Ecomus\Icon::get_svg( 'left-mini');?>
				</span>
				<span class="nav-link em-flex">
					<span class="nav-label em-font-bold"><?php esc_html_e( 'Previous', 'ecomus' );?></span>
					<span class="nav-title"><?php  echo esc_html( $prev_post->post_title ); ?></span>
				</span>
			</a>
		<?php endif; ?>
	</div>
	<div class="em-md-6 em-xs-6 em-flex-end">
		<?php if ( $next_post ) : ?>
			<a class="nav-next em-flex" href="<?php echo esc_url( get_permalink( $next_post ) )  ?>">
				<span class="nav-link em-flex text-right">
					<span class="nav-label em-font-bold"><?php esc_html_e( 'Next', 'ecomus' );?></span>
					<span class="nav-title"><?php echo esc_html( $next_post->post_title ); ?></span>
				</span>
				<span class="em-button em-button-outline em-button-icon">
					<?php echo \Ecomus\Icon::get_svg( 'right-mini');?>
				</span>
			</a>
		<?php endif; ?>
	</div>
</nav>