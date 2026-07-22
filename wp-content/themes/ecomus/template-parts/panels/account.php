<?php
/**
 * Template part for displaying the filter sidebar panel
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

if( \Ecomus\Helper::get_option('header_signin_icon_behaviour') == 'page' ) {
	return;
}

$current_user = get_user_by( 'id', get_current_user_id() );
?>

<div id="account-panel" class="offscreen-panel account-panel offscreen-panel--side-right">
	<div class="panel__backdrop"></div>
	<div class="panel__container">
		<?php echo \Ecomus\Icon::get_svg( 'close', 'ui', 'class=panel__button-close' ); ?>
		<h6 class="panel__header">
			<span class="account-panel__avatar logged"><?php echo get_avatar( get_current_user_id(), 30 ); ?></span>
			<span class="account-panel__name"><?php echo !empty($current_user) ? $current_user->display_name : ''; ?></span>
		</h6>
		<div class="panel__content">
			<?php if ( has_nav_menu( 'user_logged' ) ) { ?>
			<ul class="account-panel__links">
				<li>
				<?php
				wp_nav_menu( array(
					'theme_location' => 'user_logged',
					'container'      => false,
				) );
				?>
				</li>
				<li class="logout">
					<?php
					$account = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
					?>
					<a href="<?php echo esc_url( wp_logout_url( $account ) ) ?>"><?php esc_html_e('Logout', 'ecomus'); ?></a>
				</li>
			</ul>
			<?php } else { ?>
			<ul class="account-panel__links">
				<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
					<li class="account-link--<?php echo esc_attr( $endpoint ); ?>">
						<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="underline-hover"><?php echo esc_html( $label ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php } ?>
		</div>
	</div>
</div>