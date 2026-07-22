<?php

/**
 * Template part for displaying the wishlist icon
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

if ( ! class_exists( 'WCBoost\Wishlist\Helper' ) ) {
	return;
}

$counter = isset($args['wishlist_count']) ? $args['wishlist_count'] : 0;
$classes = isset($args['wishlist_classes']) ? $args['wishlist_classes'] : '';
$text_classes = isset($args['wishlist_text_class']) ? $args['wishlist_text_class'] : '';
$counter_class = isset($args['wishlist_counter_class']) ? $args['wishlist_counter_class'] : '';
?>
<a href="<?php echo esc_url( wc_get_page_permalink( 'wishlist' ) ); ?>" class="em-button em-button-text<?php echo esc_attr( $classes); ?>" role="button">
	<?php echo Ecomus\Icon::inline_svg( 'icon=heart' ); ?>
	<span class="<?php echo esc_attr( $counter_class );?>"><?php echo esc_html( $counter ); ?></span>
	<span class="<?php echo esc_attr( $text_classes ); ?>"><?php esc_html_e( 'Wishlist', 'ecomus' ) ?></span>
</a>