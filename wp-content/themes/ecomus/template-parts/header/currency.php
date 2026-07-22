<?php

/**
 * Template part for displaying the currency
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

?>

<div class="header-currency ecomus-currency ecomus-currency-language em-color-dark">
	<?php echo \Ecomus\WooCommerce\Currency::currency_switcher(); ?>
</div>