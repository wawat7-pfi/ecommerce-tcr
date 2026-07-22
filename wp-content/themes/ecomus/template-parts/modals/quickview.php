<?php
/**
 * Template part for displaying the quickview modal
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}
?>

<div id="quick-view-modal" class="quick-view-modal modal single-product modal__quickview">
	<div class="modal__backdrop"></div>
	<div class="modal__container">
		<div class="modal__wrapper">
			<a href="#" class="modal__button-close">
				<?php echo \Ecomus\Icon::get_svg( 'close', 'ui' ); ?>
			</a>
			<div class="modal__content woocommerce">
				<div class="modal__product product-quickview"></div>
			</div>
		</div>
	</div>
</div>