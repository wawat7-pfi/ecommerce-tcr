<?php
/**
 * Template part for displaying the account icon
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

$classes = isset($args['account_classes']) ? $args['account_classes'] : '';
$text_classes = isset($args['account_text_class']) ? $args['account_text_class'] : '';
$data_toggle = isset($args['data_toggle']) ? $args['data_toggle'] : 'off-canvas';
$data_target = isset($args['data_target']) ? $args['data_target'] : 'account-panel';
$account_text = isset($args['account_text']) ? $args['account_text'] : esc_html__( 'Account', 'ecomus' );
?>

<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>" class="em-button em-button-text<?php echo esc_attr( $classes); ?>" data-toggle="<?php echo esc_attr( $data_toggle ) ; ?>" data-target="<?php echo esc_attr( $data_target ); ?>">
	<?php echo \Ecomus\Icon::get_svg( 'account' ); ?>
	<span class="<?php echo esc_attr( $text_classes); ?>"><?php echo esc_html( $account_text ) ?></span>
</a>
