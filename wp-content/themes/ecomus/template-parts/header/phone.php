<?php

/**
 * Template part for displaying the social in topbar
 *
 * @package Ecomus
 */

$phone_number = \Ecomus\Helper::get_option( 'topbar_phone' );
$phone_text = \Ecomus\Helper::get_option( 'topbar_phone_text' );
$phone_text = ! empty( $phone_text ) ? $phone_text : $phone_number;
?>

<div class="header-phone topbar-text em-color-dark em-font-medium">
	<a href="tel:<?php echo esc_attr( $phone_number ); ?>"><?php echo esc_html( $phone_text ); ?></a>
</div>