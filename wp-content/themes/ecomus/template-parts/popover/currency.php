<?php
/**
 * Template part for displaying the currency popover
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

?>

<div id="currency-popover" class="popover currency-popover">
	<div class="popover__backdrop"></div>
	<div class="popover__container">
		<?php echo \Ecomus\Icon::get_svg( 'close', 'ui', array('class' => 'em-button em-button-icon em-button-light popover__button-close') ); ?>
		<div class="popover__content">
        <?php echo \Ecomus\WooCommerce\Currency::woocs_currency_switcher(); ?>
		</div>
	</div>
</div>