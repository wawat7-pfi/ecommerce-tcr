<?php
/**
 * Template part for displaying the compare icon
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

if ( ! class_exists( 'WCBoost\ProductsCompare\Plugin' ) ) {
	return;
}

$counter = isset($args['compare_count']) ? $args['compare_count'] : 0;
$classes = isset($args['compare_classes']) ? $args['compare_classes'] : '';
$text_classes = isset($args['compare_text_class']) ? $args['compare_text_class'] : '';
$counter_class = isset($args['compare_counter_class']) ? $args['compare_counter_class'] : '';
?>

<a class="em-button em-button-text<?php echo esc_attr( $classes); ?>" role="button" href="<?php echo esc_url( wc_get_page_permalink( 'compare' ) ); ?>">
	<?php echo Ecomus\Icon::inline_svg( 'icon=cross-arrow' ); ?>
	<span class="<?php echo esc_attr( $counter_class ); ?>"><?php echo esc_html( $counter ); ?></span>
	<span class="<?php echo esc_attr( $text_classes ); ?>"><?php esc_html_e( 'Compare', 'ecomus' ) ?></span>
</a>