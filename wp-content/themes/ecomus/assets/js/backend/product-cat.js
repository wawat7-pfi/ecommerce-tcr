jQuery(document).ready(function ($) {
	"use strict";

	// Uploading files
	var file_frame,
		$em_page_header_bg_id = $('#em_page_header_bg_id'),
		$em_page_header_bg = $('#em_page_header_bg'),
		$cat_page_header_bg = $em_page_header_bg.find('.em-cat-page-header-bg'),
		$em_page_header_text_color = $('#em_page_header_text_color');

	$em_page_header_bg.on('click', '.upload_images_button', function (event) {
		var $el = $(this);

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if (file_frame) {
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.downloadable_file = wp.media({
			multiple: false
		});

		// When an image is selected, run a callback.
		file_frame.on('select', function () {
			var selection = file_frame.state().get('selection'),
				attachment_ids = $em_page_header_bg_id.val();

			selection.map(function (attachment) {
				attachment = attachment.toJSON();

				if (attachment.id) {
					attachment_ids = attachment.id;
					var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

					$cat_page_header_bg.html('<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" width="100px" height="100px" /><ul class="actions"><li><a href="#" class="delete" title="' + $el.data('delete') + '">' + $el.data('text') + '</a></li></ul></li>');
				}

			});
			$em_page_header_bg_id.val(attachment_ids);
		});


		// Finally, open the modal.
		file_frame.open();
	});

	// Remove images.
	$em_page_header_bg.on('click', 'a.delete', function () {
		$(this).closest('li.image').remove();

		var attachment_ids = '';

		$cat_page_header_bg.find('li.image').css('cursor', 'default').each(function () {
			var attachment_id = $(this).attr('data-attachment_id');
			attachment_ids = attachment_ids + attachment_id + ',';
		});

		$em_page_header_bg_id.val(attachment_ids);

		return false;
	});

	$em_page_header_text_color.wpColorPicker({
		change: function(e, ui) {
			$(e.target).val(ui.color.toString());
			$(e.target).trigger('change');
		},
		clear: function(e, ui) {
			$(e.target).trigger('change');
		}
	});
});
