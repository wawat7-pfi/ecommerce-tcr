<?php
/**
 * Template part for displaying the single product sidebar panel
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

$position = \Ecomus\Helper::get_option( 'product_sidebar' ) == 'sidebar-content' ? 'left': 'right';
?>

<div id="single-product-sidebar-panel" class="offscreen-panel single-product-sidebar-panel offscreen-panel--side-<?php echo esc_attr( $position ); ?>">
	<div class="sidebar__backdrop"></div>
	<div class="sidebar__container">
		<?php echo \Ecomus\Icon::get_svg( 'close', 'ui', 'class=sidebar__button-close' ); ?>
		<div class="sidebar__header">
			<?php echo esc_html__( 'Sidebar Product', 'ecomus' ); ?>
		</div>
		<div class="sidebar__content">
		<?php
			if ( is_active_sidebar( 'single-product-sidebar' ) ) {
				dynamic_sidebar( 'single-product-sidebar' );
			}
		?>
		</div>
	</div>
</div>