<?php
/**
 * Template part for displaying the search modal
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}
$post_type = 'product';
if( \Ecomus\Helper::is_blog() || is_singular('post') ) {
	$post_type = 'post';
}

$search_type = \Ecomus\Helper::get_option('header_search_type');

?>

<div id="search-modal" class="search-modal modal search-type-<?php echo esc_attr( $search_type ); ?>">
	<div class="modal__backdrop"></div>
	<div class="modal__container">
		<a href="#" class="modal__button-close">
			<?php echo \Ecomus\Icon::get_svg( 'close', 'ui' ); ?>
		</a>
		<div class="modal__header">
			<div class="em-container">
				<h5 class="search-modal__title em-font-medium"><?php esc_html_e('Search our site', 'ecomus'); ?></h5>
				<form method="get" class="search-modal__form em-instant-search__form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<div class="modal__content-search-field">
						<?php echo \Ecomus\Icon::get_svg( 'search', 'ui' ); ?>
						<input type="text" name="s" class="search-modal__field em-instant-search__field" autocomplete="off"
							placeholder="<?php esc_attr_e( 'Search', 'ecomus' ); ?>">
						<input type="hidden" name="post_type" class="search-modal__post-type" value="<?php echo esc_attr( $post_type ); ?>">
						<a href="#" class="close-search-modal__results em-button em-button-outline em-button-icon"><?php echo \Ecomus\Icon::get_svg( 'close', 'ui'); ?></a>
					</div>
				</form>
			</div>
		</div>
		<div class="modal__content woocommerce">
			<div class="em-container">
				<div class="modal__content-suggestion em-row">
					<?php
					\Ecomus\Header\Search::get_trending();
					\Ecomus\Header\Search::get_products();
					?>
				</div>
				<?php do_action('ecomus_search_modal_after_form'); ?>
			</div>
		</div>
		<div class="modal__footer hidden">
			<div class="search-modal__footer-button text-right">
				<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="em-button em-button-subtle em-font-semibold">
					<span><?php echo esc_html__( 'View all', 'ecomus' ); ?></span>
					<?php echo \Ecomus\Icon::get_svg( 'arrow-top' ); ?>
				</a>
			</div>
		</div>
	</div>
</div>