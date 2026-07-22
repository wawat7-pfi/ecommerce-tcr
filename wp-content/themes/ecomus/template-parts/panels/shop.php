<?php
/**
 * Template part for displaying the shop panel
 *
 * @package Ecomus
 */

$shop_menu = \Ecomus\Helper::get_option( 'mobile_navigation_bar_shop_menu' );

if ( empty( $shop_menu)) {
	return;
}

$shop_link = \Ecomus\Helper::get_option( 'mobile_navigation_bar_shop_menu_link' );
$shop_link = ! empty( $shop_link ) ? $shop_link : get_permalink( wc_get_page_id( 'shop' ) );
?>

<div id="mobile-shop-panel" class="offscreen-panel offscreen-panel--side-left mobile-shop-panel">
	<div class="panel__backdrop"></div>
	<div class="panel__container">
		<?php echo \Ecomus\Icon::get_svg( 'close', 'ui', 'class=panel__button-close' ); ?>
		<div class="panel__header"></div>
		<div class="panel__content">
			<?php
				wp_nav_menu( apply_filters( 'ecomus_navigation_shop_menu_content', array(
					'theme_location' 	=> '__no_such_location',
					'menu'				=> $shop_menu,
					'container'      	=> 'nav',
					'container_class'   => 'main-navigation mobile-shop-navigation',
					'menu_class'     	=> 'menu',
				) ) );
			?>
		</div>
		<?php if ( \Ecomus\Helper::get_option( 'mobile_shop_view_all' ) ) : ?>
			<div class="panel__footer">
				<div class="mobile-shop-panel__footer-button">
					<a href="<?php echo esc_url( $shop_link ); ?>" class="em-button em-button-subtle em-font-semibold">
						<span><?php echo esc_html__( 'View all collection', 'ecomus' ); ?></span>
						<?php echo \Ecomus\Icon::get_svg( 'arrow-top' ); ?>
					</a>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>