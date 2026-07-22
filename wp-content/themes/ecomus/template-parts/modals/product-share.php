<?php
/**
 * Template part for displaying the product share modal
 *
 * @package Ecomus
 */

?>

<div id="product-share-modal" class="product-share-modal modal product-extra-link-modal">
	<div class="modal__backdrop"></div>
	<div class="modal__container">
		<div class="modal__wrapper">
			<div class="modal__header">
				<h3 class="modal__title em-font-h6"><?php esc_html_e( 'Share', 'ecomus' ); ?></h3>
				<a href="#" class="modal__button-close">
					<?php echo \Ecomus\Icon::get_svg( 'close', 'ui' ); ?>
				</a>
			</div>
			<div class="modal__content">
				<div class="product-share__share">
					<div class="product-share__copylink-heading em-font-h6 hidden"><?php echo esc_html__( 'Share', 'ecomus' ); ?></div>
					<?php echo ! empty( $args ) ? $args : '' ; ?>
				</div>
				<div class="product-share__copylink">
					<form class="em-responsive">
						<input class="product-share__copylink--link ecomus-copylink__link" type="text" value="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>" readonly="readonly" />
						<button class="product-share__copylink--button ecomus-copylink__button"><?php echo esc_html__( 'Copy', 'ecomus' ); ?></button>
					</form>
				</div>
			</div>
		</div>
	</div>
	<span class="modal__loader"><span class="ecomusSpinner"></span></span>
</div>