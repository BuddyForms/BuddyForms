/**
 * Created by Victor on 28/12/2017.
 */
jQuery(document).ready(function ($) {
	$(".dropzone").each(function (index, value) {
		var current = $(this),
			id = current.attr('id'),
			max_size = current.attr('file_limit'),
			accepted_files = current.attr('accepted_files'),
			action = current.attr('action'),
			multiple_files = current.attr('multiple_files');

		Dropzone.autoDiscover = false;
		var clickeable = action !== 'edit';
		var currentField = jQuery('#field_' + id);

		$("#" + id).dropzone({
			url: dropParam.admin_url,
			maxFilesize: max_size,
			acceptedFiles: accepted_files,
			maxFiles: multiple_files,
			clickable: clickeable,
			addRemoveLinks: true,
			init: function () {
               
				this.on('complete', function () {
					jQuery("button[type=submit].bf-submit").removeAttr("disabled");
				});

				this.on('addedfile', function () {
					jQuery("#field_"+id+"-error").text("");
					jQuery("button[type=submit].bf-submit").attr("disabled", "disabled");
				});

				this.on('sending', function (file, xhr, formData) {
					formData.append('action', 'handle_dropped_media');
					formData.append('nonce', dropParam.ajaxnonce);
				});

				this.on('success', function (file, response) {
					file.previewElement.classList.add("dz-success");
					file['attachment_id'] = response; // push the id for future reference
					var ids = currentField.val() + ',' + response;
					var idsFormat = "";
					if (ids[0] === ',') {
						idsFormat = ids.substring(1, ids.length);
					} else {
						idsFormat = ids;
					}
					currentField.val(idsFormat);
				});

				this.on('error', function (file, response) {
					file.previewElement.classList.add("dz-error");
					jQuery(file.previewElement).find('div.dz-error-message>span').text(response);
					jQuery("button[type=submit].bf-submit").removeAttr("disabled");
				});
				this.on('removedfile', function (file) {
					var attachment_id = file.attachment_id;
					var ids = currentField.val();
					var remainigIds = ids.replace(attachment_id, "");
					if (remainigIds[0] === ',') {
						remainigIds = remainigIds.substring(1, ids.length);
					}
					var lastChar = remainigIds[remainigIds.length - 1];
					if (lastChar === ',') {
						remainigIds = remainigIds.slice(0, -1);
					}
					currentField.val(remainigIds);
					jQuery("button[type=submit].bf-submit").attr("disabled", "disabled");
					jQuery.post(dropParam.admin_url, {
						action: 'handle_deleted_media',
						media_id: attachment_id,
						nonce: dropParam.ajaxnonce
					}, function (data) {
						console.log(data);
					}).always(function () {
						jQuery("button[type=submit].bf-submit").removeAttr("disabled");
					});
				});
			}
		});

	});
});
