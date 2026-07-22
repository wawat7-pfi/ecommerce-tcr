<?php
/**
 * Template file for displaying Custom Link mobile
 *
 * @package Ecomus
 */

?>

<?php
    $url = \Ecomus\Helper::get_option( 'mobile_navigation_bar_custom_link_url' );
    $text = \Ecomus\Helper::get_option( 'mobile_navigation_bar_custom_link_text' );
    $type = \Ecomus\Helper::get_option( 'mobile_navigation_bar_custom_link_type' );
    $image_dimension = \Ecomus\Helper::get_option( 'mobile_navigation_bar_custom_link_image_dimension' );
?>

<a href="<?php echo esc_url( $url ); ?>" class="ecomus-mobile-navigation-bar__icon em-button em-button-icon em-button-light em-flex em-flex-column em-flex-align-center em-flex-center em-font-semibold">
	<?php if( $type == 'image' ) : ?>
		<img src="<?php echo esc_url( \Ecomus\Helper::get_option( 'mobile_navigation_bar_custom_link_image' ) ); ?>" alt="<?php echo esc_attr( $text ); ?>" style="<?php echo ! empty( $image_dimension['width'] ) && is_numeric( $image_dimension['width'] ) ? 'width:' . $image_dimension['width'] . 'px;' : ''; ?> <?php echo ! empty( $image_dimension['height'] ) && is_numeric( $image_dimension['height'] ) ? 'height:' . $image_dimension['height'] . 'px;' : ''; ?>">
	<?php else: ?>
		<?php echo \Ecomus\Icon::sanitize_svg( \Ecomus\Helper::get_option( 'mobile_navigation_bar_custom_link_svg' ) ); ?>
	<?php endif; ?>

    <?php if( ! empty( $text ) ) : ?>
        <span><?php echo esc_html( $text ); ?></span>
    <?php endif; ?>
</a>
