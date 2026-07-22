<?php
/**
 * Template part for displaying the language popover
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

?>

<div id="language-popover" class="popover language-popover">
	<div class="popover__backdrop"></div>
	<div class="popover__container">
		<?php echo \Ecomus\Icon::get_svg( 'close', 'ui', array('class' => 'em-button em-button-icon em-button-light popover__button-close') ); ?>
		<div class="popover__content">
        <?php echo \Ecomus\WooCommerce\Language::language_switcher(); ?>
		</div>
	</div>
</div>