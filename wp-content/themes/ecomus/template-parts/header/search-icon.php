<?php
/**
 * Template part for displaying the search icon
 *
 * @package Ecomus
 */

?>

<a href="#" class="em-button em-button-text em-button-icon header-search__icon" data-toggle="modal" data-target="search-modal">
	<?php echo \Ecomus\Icon::get_svg( 'search' ); ?>
	<span class="screen-reader-text"><?php esc_html_e( 'Search', 'ecomus' ) ?></span>
</a>
