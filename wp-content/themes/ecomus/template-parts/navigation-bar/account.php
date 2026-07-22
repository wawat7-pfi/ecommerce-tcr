<?php
/**
 * Template file for displaying account mobile
 *
 * @package Ecomus
 */

 if( ! function_exists( 'wc_get_account_endpoint_url' ) ) {
	return;
 }

$toggle = is_user_logged_in() ? 'off-canvas' : 'modal';
$target = is_user_logged_in() ? 'account-panel' : 'login-modal';
?>

<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>" class="ecomus-mobile-navigation-bar__icon em-button em-button-icon em-button-light em-flex em-flex-column em-flex-align-center em-flex-center em-font-semibold" data-toggle="<?php echo esc_attr($toggle); ?>" data-target="<?php echo esc_attr($target); ?>">
	<?php echo \Ecomus\Icon::get_svg( 'account' ); ?>
	<span><?php echo esc_html__( 'Account', 'ecomus' ); ?></span>
</a>
