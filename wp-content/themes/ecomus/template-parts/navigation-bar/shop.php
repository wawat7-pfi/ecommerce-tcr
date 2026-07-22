<?php
/**
 * Template file for displaying shop mobile
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

$shop_menu = \Ecomus\Helper::get_option( 'mobile_navigation_bar_shop_menu' );
$shop_menu = \Ecomus\Helper::get_option( 'mobile_navigation_bar_shop_icon_behaviour' ) == 'menu' ? $shop_menu : '';
$data_toggle = ! empty( $shop_menu ) ? 'off-canvas' : '';
$data_target = ! empty( $shop_menu ) ? 'mobile-shop-panel' : '';

$shop_link = \Ecomus\Helper::get_option( 'mobile_navigation_bar_shop_link' );
$shop_link = ! empty( $shop_link ) && \Ecomus\Helper::get_option( 'mobile_navigation_bar_shop_icon_behaviour') == 'link' ? $shop_link : get_permalink( wc_get_page_id( 'shop' ) );

?>

<a href="<?php echo esc_url( $shop_link ); ?>" class="ecomus-mobile-navigation-bar__icon em-button em-button-icon em-button-light em-flex em-flex-column em-flex-align-center em-flex-center em-font-semibold" data-toggle="<?php echo esc_attr( $data_toggle );  ?>" data-target="<?php echo esc_attr( $data_target );  ?>">
	<?php echo \Ecomus\Icon::get_svg( 'shop' ); ?>
	<span><?php echo esc_html__( 'Shop', 'ecomus' ); ?></span>
</a>
