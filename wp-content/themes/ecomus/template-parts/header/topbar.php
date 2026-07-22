<?php
/**
 * Template part for displaying the topbar
 *
 * @package Ecomus
 */

$topbar_class = '';
if ( \Ecomus\Helper::get_option( 'mobile_topbar' ) ) {
	$topbar_class = 'topbar-mobile';
	$topbar_class .= ' topbar-mobile--keep-' . \Ecomus\Helper::get_option( 'mobile_topbar_section' );
}
?>
<div id="topbar" class="topbar <?php echo \Ecomus\Helper::get_option( 'topbar_border_bottom' ) ? 'has-border' : ''; ?> <?php echo esc_attr( $topbar_class ); ?>">
	<div class="topbar-container <?php echo esc_attr( apply_filters( 'ecomus_topbar_container_classes', 'em-container' ) ) ?>">
		<?php if ( isset( $args['left_items'][0]['item'] ) && ! empty( $args['left_items'][0]['item'] ) ) : ?>
			<div class="topbar-items topbar-left-items">
				<?php \Ecomus\Header\Topbar::items( $args['left_items']); ?>
			</div>
		<?php endif; ?>

		<?php if ( isset( $args['center_items'][0]['item'] ) && ! empty( $args['center_items'][0]['item'] ) ) : ?>
			<div class="topbar-items topbar-center-items">
				<?php \Ecomus\Header\Topbar::items( $args['center_items'] ); ?>
			</div>
		<?php endif; ?>

		<?php if ( isset( $args['right_items'][0]['item'] ) && ! empty( $args['right_items'][0]['item'] ) ) : ?>
			<div class="topbar-items topbar-right-items">
				<?php \Ecomus\Header\Topbar::items( $args['right_items'] ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
