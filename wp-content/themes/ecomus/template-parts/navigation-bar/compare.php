<?php
/**
 * Template file for displaying Compare mobile
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

if ( ! class_exists( 'WCBoost\ProductsCompare\Plugin' ) ) {
	return;
}


$compare_counter = \WCBoost\ProductsCompare\Plugin::instance()->list->count_items();
$counter_class = $compare_counter == 0 ? 'empty-counter' : '';
?>

<a href="<?php echo esc_url( wc_get_page_permalink( 'compare' ) ); ?>" class="ecomus-mobile-navigation-bar__icon em-button em-button-icon em-button-light em-flex em-flex-column em-flex-align-center em-flex-center em-font-semibold em-relative">
	<?php echo Ecomus\Icon::inline_svg( 'icon=cross-arrow' ); ?>
	<span class="header-counter header-compare__counter <?php echo esc_attr( $counter_class );?>"><?php echo esc_html( $compare_counter ); ?></span>
	<span><?php echo esc_html__( 'Compare', 'ecomus' ); ?></span>
</a>
