<?php
/**
 * Template part for displaying the category menu
 *
 * @package Ecomus
 */

if ( ! has_nav_menu( 'category-menu' ) ) {
	return;
}

$shop_link = class_exists('WooCommerce') ? wc_get_page_permalink( 'shop' ) : get_home_url();
$url = \Ecomus\Helper::get_option( 'category_menu_view_all_url' );
$url = ! empty( $url ) ? $url : $shop_link;
?>
<div class="header-category-menu">
	<div class="header-category__title">
		<button class="header-category__title-button em-flex em-flex-align-center em-font-bold">
			<?php echo \Ecomus\Icon::get_svg( 'category', 'ui', 'class=header-category__arrow' ); ?>
			<span class="header-category__name"><?php echo esc_html__('Browse All Categories', 'ecomus') ?></span>
		</button>
	</div>
	<div class="header-category__content em-absolute">
		<?php
			if ( class_exists( '\Ecomus\Addons\Modules\Mega_Menu\Walker' ) ) {
				wp_nav_menu( apply_filters( 'ecomus_navigation_header_category_content', array(
					'theme_location' 	=> 'category-menu',
					'container'      	=> 'nav',
					'container_class'   => 'header-category__menu',
					'menu_class'     	=> 'nav-menu menu',
					'walker'			=> new \Ecomus\Addons\Modules\Mega_Menu\Walker()
				) ) );
			} else {
				wp_nav_menu( apply_filters( 'ecomus_navigation_header_category_content', array(
					'theme_location' 	=> 'category-menu',
					'container'      	=> 'nav',
					'container_class'   => 'header-category__menu',
					'menu_class'     	=> 'nav-menu menu',
				) ) );
			}
		?>
		<?php if( intval( \Ecomus\Helper::get_option( 'category_menu_view_all' ) ) ) : ?>
			<div class="header-category__view-all">
				<a href="<?php echo esc_url( $url ); ?>">
					<?php echo esc_html__('View all collection', 'ecomus') ?>
					<?php echo \Ecomus\Icon::get_svg( 'arrow-top' ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>
