<?php
/**
 * Template part for displaying the hamburger panel
 *
 * @package Ecomus
 */

?>

<div id="mobile-menu-panel" class="offscreen-panel offscreen-panel--side-left hamburger-panel">
	<div class="panel__backdrop"></div>
	<div class="panel__container">
		<?php echo \Ecomus\Icon::get_svg( 'close', 'ui', 'class=panel__button-close' ); ?>
		<div class="panel__header"></div>
		<div class="panel__content">
			<?php echo \Ecomus\Header\Mobile::mobile_menu_items(); ?>
		</div>
		<div class="panel__footer">
			<?php echo \Ecomus\Header\Mobile::currency_language(); ?>
		</div>
	</div>
</div>