<?php
/**
 * Template part for displaying the product ask a question modal
 *
 * @package Ecomus
 */

?>

<div id="product-ask-question-modal" class="product-ask-question-modal modal product-extra-link-modal">
	<div class="modal__backdrop"></div>
	<div class="modal__container">
		<div class="modal__wrapper">
			<div class="modal__header">
				<h3 class="modal__title em-font-h5"><?php esc_html_e( 'Ask a question', 'ecomus' ); ?></h3>
				<a href="#" class="modal__button-close">
					<?php echo \Ecomus\Icon::get_svg( 'close', 'ui' ); ?>
				</a>
			</div>
			<div class="modal__content">
				<div class="ask-question-content"><?php echo do_shortcode( wp_kses_post( $args ) ); ?></div>
			</div>
		</div>
	</div>
	<span class="modal__loader"><span class="ecomusSpinner"></span></span>
</div>