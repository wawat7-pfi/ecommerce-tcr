<?php
/**
 * Template part for displaying the my login modal
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

if( \Ecomus\Helper::get_option('header_signin_icon_behaviour') == 'page' ) {
	return;
}
?>

<div id="login-modal" class="login-modal modal woocommerce woocommerce-account">
	<div class="modal__backdrop"></div>
	<div class="modal__container">
		<div class="modal__wrapper">
			<a href="#" class="modal__button-close">
				<?php echo \Ecomus\Icon::get_svg( 'close', 'ui' ); ?>
			</a>
			<div class="modal__content">
				<?php wc_get_template( 'myaccount/form-login.php', array('action' => 'popup') ); ?>
			</div>
		</div>
	</div>
	<span class="modal__loader"><span class="ecomusSpinner"></span></span>
</div>