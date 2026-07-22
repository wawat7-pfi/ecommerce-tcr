<?php
/**
 * Template part for displaying the search form
 *
 * @package Ecomus
 */

?>

<div class="header-search">
	<form class="header-search__form em-instant-search__form em-flex em-flex-align-center" method="get" action="<?php echo esc_url( home_url( '/' ) ) ?>">
		<input type="text" name="s" class="header-search__field em-instant-search__field" placeholder="<?php esc_attr_e('Search product', 'ecomus') ?>" autocomplete="off">
		<input type="hidden" name="post_type" class="header-search__post-type" value="product">
		<a href="#" class="close-search-modal__results em-button em-button-outline em-button-icon"><?php echo \Ecomus\Icon::get_svg( 'close', 'ui'); ?></a>
		<button type="submit" class="header-search__button em-button">
			<?php echo \Ecomus\Icon::get_svg( 'search' ); ?>
		</button>
		<div class="header-search__products-suggest"><?php \Ecomus\Header\Search::products_suggest(); ?></div>
		<?php do_action('ecomus_header_search_after_form'); ?>
	</form>
</div>
