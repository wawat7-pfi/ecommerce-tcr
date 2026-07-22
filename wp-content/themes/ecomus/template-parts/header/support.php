<?php

/**
 * Template part for displaying the support center in header
 *
 * @package Ecomus
 */


$icon = Ecomus\Helper::get_option( 'header_support_svg' );
$phone = Ecomus\Helper::get_option( 'header_support_phone_number' );
$phone_link = Ecomus\Helper::get_option( 'header_support_phone_number_link' );

if ( empty( $phone )) {
	return;
}
?>

<div class="header-support-center em-flex em-flex-align-center">
	<div class="header-support-center__icon">
		<?php
			if ( ! empty( $icon ) ) {
				echo \Ecomus\Icon::sanitize_svg( $icon );
			} else {
				echo \Ecomus\Icon::get_svg( 'support' );
			}
		 ?>
	</div>
	<div class="header-support-center__content em-flex em-flex-column">
		<div class="header-support-center__phone em-font-bold">
			<a href="<?php echo esc_url( $phone_link ); ?>" aria-describedby="a11y-external-message"><?php echo esc_html( $phone ); ?></a>
		</div>
		<div class="header-support-center__text em-font-medium text-left">
			<?php echo esc_html__('Support Center', 'ecomus');  ?>
		</div>
	</div>
</div>