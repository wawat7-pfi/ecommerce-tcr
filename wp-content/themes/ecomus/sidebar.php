<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Ecomus
 */

$has_sidebar = apply_filters( 'ecomus_get_sidebar', false );

if( ! $has_sidebar ) {
	return;
}

$sidebar = 'blog-sidebar';

if ( \Ecomus\Helper::is_catalog() ) {
	$sidebar = 'catalog-sidebar';
}

if ( is_singular( 'product' ) ) {
	$sidebar = 'single-product-sidebar';
}

if ( ! is_active_sidebar( $sidebar ) ) {
	return;
}

$sidebar_class = apply_filters( 'ecomus_primary_sidebar_classes', $sidebar );

?>

<aside id="<?php echo esc_attr( apply_filters( 'ecomus_primary_sidebar_id', 'primary-sidebar' ) ); ?>" class="widget-area primary-sidebar <?php echo esc_attr( $sidebar_class ) ?>">
	<?php dynamic_sidebar( $sidebar ); ?>
</aside><!-- #primary -->
