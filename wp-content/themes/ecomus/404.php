<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Ecomus
 */

$shop_link = class_exists('WooCommerce') ? wc_get_page_permalink( 'shop' ) : get_home_url();

get_header();
?>
<?php if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('single')) { ?>
	<div id="primary" class="content-area">
		<div class="error-404 not-found text-center">
			<div class="error-404__image">
				<img src="<?php echo esc_url( get_theme_file_uri( 'images/404.svg' ) ); ?>" alt="<?php echo esc_attr__( '404 Image', 'ecomus' ) ?>">
			</div>
			<div class="error-404__wrapper">
				<div class="error-404__content">
					<h1 class="error-404__title em-font-h3"><?php esc_html_e( 'Oops...That link is broken.', 'ecomus' ); ?></h1>
					<?php esc_html_e( 'Sorry for the inconvenience. Go to our homepage or check out our latest collections.', 'ecomus' ); ?>
				</div>
				<a href="<?php echo esc_url( $shop_link ); ?>"
				class="error-404__button ecomus-button em-button-hover-eff em-button em-font-semibold"><?php echo esc_html__( 'Shop now', 'ecomus' ); ?></a>
			</div>
		</div>

	</div><!-- #primary -->
<?php } ?>
<?php
get_footer();
