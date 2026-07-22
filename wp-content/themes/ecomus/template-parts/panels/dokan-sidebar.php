<?php
/**
 * Template part for displaying the filter sidebar panel
 *
 * @package Ecomus
 */

 if ( ! class_exists( 'WeDevs_Dokan' ) ) {
	return
}
?>

<div id="dokan-sidebar-panel" class="offscreen-panel dokan-sidebar-panel offscreen-panel--side-left">
	<div class="panel__backdrop"></div>
	<div class="panel__container">
		<?php echo \Ecomus\Icon::get_svg( 'close', 'ui', 'class=panel__button-close' ); ?>
		<h6 class="panel__header">
			<?php echo \Ecomus\Icon::get_svg( 'filter' ); ?>
			<?php echo esc_html__( 'Filter', 'ecomus' ); ?>
		</h6>
		<div class="panel__content">
		<?php
			if ( is_active_sidebar( 'catalog-filters-sidebar' ) ) {
				dynamic_sidebar( 'catalog-filters-sidebar' );
			}
		?>
		</div>
	</div>
</div>