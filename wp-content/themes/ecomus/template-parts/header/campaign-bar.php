<?php
/**
 * Template part for displaying the campaign bar
 *
 * @package Ecomus
 */

?>
<div id="campaign-bar" class="campaign-bar em-relative campaign-bar-type--<?php echo esc_attr( $args['type'] ) ?>">
	<div class="campaign-bar__container<?php echo esc_attr( $args['class_container'] ) ?>" data-speed="<?php echo esc_attr( $args['speed'] ) ?>">
		<div class="campaign-bar__items<?php echo esc_attr( $args['class_items'] ) ?>">
			<?php \Ecomus\Header\Campaign_Bar::campaign_items( $args['items'], $args['class_item'] ); ?>
		</div>
	</div>
	<button class="campaign-bar__close em-button-text em-button-icon" aria-label="<?php esc_attr_e('Campaign Bar Close', 'ecomus') ?>">
		<?php echo \Ecomus\Icon::get_svg( 'close' ); ?>
	</button>
</div>