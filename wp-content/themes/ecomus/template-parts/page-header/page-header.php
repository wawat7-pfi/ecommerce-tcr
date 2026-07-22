<?php
/**
 * Template part for displaying the blog header
 *
 * @package Ecomus
 */

?>

<div id="page-header" class="<?php \Ecomus\Page_Header::classes('page-header'); ?>" <?php echo \Ecomus\Page_Header::background_image(); ?>>
	<div class="em-container clearfix">
		<?php do_action('ecomus_before_page_header_content'); ?>
		<div class="page-header__content em-flex em-flex-column em-flex-align-center text-center">
			<?php \Ecomus\Page_Header::title(); ?>
			<?php \Ecomus\Page_Header::breadcrumb(); ?>
			<?php do_action('ecomus_page_header_content'); ?>
		</div>
		<?php do_action('ecomus_after_page_header_content'); ?>
	</div>
</div>