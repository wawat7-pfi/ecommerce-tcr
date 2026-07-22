<?php
/**
 * Template part for displaying the product delivery return modal
 *
 * @package Ecomus
 */

?>

<div id="product-delivery-return-modal" class="product-delivery-return-modal modal product-extra-link-modal">
	<div class="modal__backdrop"></div>
	<div class="modal__container">
		<div class="modal__wrapper">
			<div class="modal__header">
				<h3 class="modal__title em-font-h5"><?php echo esc_html( $args['title'] ); ?></h3>
				<a href="#" class="modal__button-close">
					<?php echo \Ecomus\Icon::get_svg( 'close', 'ui' ); ?>
				</a>
			</div>
			<div class="modal__content delivery-return-content"><?php echo ! empty( $args['content'] ) ? $args['content'] : ''; ?></div>
		</div>
	</div>
	<span class="modal__loader"><span class="ecomusSpinner"></span></span>
</div>