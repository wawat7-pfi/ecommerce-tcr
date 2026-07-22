<?php
/**
 * Template part for displaying the filter sidebar panel
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

?>

<div id="mobile-orderby-popover" class="popover mobile-orderby-popover">
	<div class="popover__backdrop"></div>
	<div class="popover__container">
		<?php echo \Ecomus\Icon::get_svg( 'close', 'ui', array('class' => 'em-button em-button-icon em-button-light popover__button-close') ); ?>
		<div class="popover__content">
			<ul class="mobile-orderby-list">
				<?php foreach ( $args as $id => $name ) : ?>
					<li><a href="#" data-id="<?php echo esc_attr( $id ); ?>" data-title="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $name ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>