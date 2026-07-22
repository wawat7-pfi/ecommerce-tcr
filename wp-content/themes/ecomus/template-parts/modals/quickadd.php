<?php
/**
 * Template part for displaying the quickadd modal
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}
?>

<div id="quick-add-modal" class="quick-add-modal modal single-product modal__quickadd">
	<div class="modal__backdrop"></div>
	<div class="modal__container">
		<div class="modal__wrapper">
			<a href="#" class="modal__button-close">
				<?php echo \Ecomus\Icon::get_svg( 'close', 'ui' ); ?>
			</a>
			<div class="modal__content woocommerce">
				<div class="modal__product product-quickadd"></div>
			</div>
		</div>
	</div>
</div>