/**
 * Created by Victor on 28/12/2017.
 */
jQuery(document).ready(function ($) {
	/* <fs_premium_only> */
	$(".dropzone").each(function (index, value) {
		var current = $(this),
			id = current.attr('id'),
			max_size = current.attr('file_limit'),
			accepted_files = current.attr('accepted_files'),
			action = current.attr('action'),
			multiple_files = current.attr('multiple_files');

		Dropzone.autoDiscover = false;
		var clickeable = action !== 'edit';

		$("#" + id).dropzone({
			url: dropParam.upload,
			maxFilesize: max_size,
			acceptedFiles: accepted_files,
			maxFiles: multiple_files,
			clickable: clickeable,
			success: function (file, response) {
				file.previewElement.classList.add("dz-success");
				file['attachment_id'] = response; // push the id for future reference
				var ids = jQuery('#field_' + id).val() + ',' + response;
				var idsFormat = "";
				if (ids[0] === ',') {
					idsFormat = ids.substring(1, ids.length);
				} else {
					idsFormat = ids;
				}

				jQuery('#field_' + id).val(idsFormat);
			},
			error: function (file, response) {
				file.previewElement.classList.add("dz-error");
				jQuery(file.previewElement).find('div.dz-error-message>span').text(response);
				console.log('error', response);
			},
			maxfilesreached: function (file) {
				console.log('max file reached', file);
			},
			// update the following section is for removing image from library
			addRemoveLinks: true,
			removedfile: function (file) {
				var attachment_id = file.attachment_id;
				var ids = jQuery('#field_' + id).val();

				var remainigIds = ids.replace(attachment_id, "");
				if (remainigIds[0] === ',') {
					remainigIds = remainigIds.substring(1, ids.length);
				}
				var lastChar = remainigIds[remainigIds.length - 1];
				if (lastChar === ',') {
					remainigIds = remainigIds.slice(0, -1);
				}

				jQuery('#field_' + id).val(remainigIds);
				jQuery.ajax({
					type: 'POST',
					url: dropParam.delete,
					data: {
						media_id: attachment_id
					}
				});
				var _ref;
				return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
			}
		});

	});
	/* </fs_premium_only> */
});
