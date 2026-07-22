<?php
/**
 * Template file for displaying wishlist mobile
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

if ( ! class_exists( 'WCBoost\Wishlist\Helper' ) ) {
	return;
}

$wishlist = \WCBoost\Wishlist\Helper::get_wishlist();

$wishlist_counter = intval( $wishlist->count_items() );
$counter_class = $wishlist_counter == 0 ? 'empty-counter' : '';
?>

<a href="<?php echo esc_url( wc_get_page_permalink( 'wishlist' ) ); ?>" class="ecomus-mobile-navigation-bar__icon em-button em-button-icon em-button-light em-flex em-flex-column em-flex-align-center em-flex-center em-font-semibold em-relative">
	<?php echo Ecomus\Icon::inline_svg( 'icon=heart' ); ?>
	<span class="header-counter header-wishlist__counter <?php echo esc_attr( $counter_class );?>"><?php echo esc_html( $wishlist_counter ); ?></span>
	<span><?php echo esc_html__( 'Wishlist', 'ecomus' ); ?></span>
</a>
