jQuery(document).ready(function($) {
    var submitButtons = jQuery("button[type=submit].bf-submit");
    if (submitButtons.length > 0) {
        var submitButton = submitButtons.first();
        var existingHtmlInsideSubmitButton = submitButton.html();
    }
    $(".featured-image-uploader").each(function(index, value) {
        var current = $(this),
            id = current.attr('id'),
            max_size = current.attr('file_limit'),
            accepted_files = current.attr('accepted_files'),
            action = current.attr('action'),
            page = current.attr('page'),
            uploadFields = current.data('entry'),
            multiple_files = current.attr('multiple_files');

        Dropzone.autoDiscover = false;
        var clickeable = page !== 'buddyforms_submissions';
        var currentField = jQuery('#field_' + id);

        var myDropzone = new Dropzone("div#" + id, {
            url: dropParam.admin_url,
            maxFilesize: 2,
            acceptedFiles: 'image/*',
            maxFiles: 1,
            clickable: clickeable,
            addRemoveLinks: clickeable,
            init: function() {
                this.on('complete', function() {
                    if (submitButtons.length > 0) {
                        submitButtons.removeAttr("disabled");
                        submitButton.html(existingHtmlInsideSubmitButton);
                    }
                });
                this.on('addedfile', function() {
                    jQuery("#field_" + id + "-error").text("");
                    if (this.files.length > 1) {
                        this.removeFile(this.files[0]);
                    }
                    if (submitButtons.length > 0) {
                        submitButtons.attr("disabled", "disabled");
                        submitButton.html("Upload in progress");
                    }
                });

                this.on('sending', function(file, xhr, formData) {
                    formData.append('action', 'handle_dropped_media');
                    formData.append('nonce', dropParam.ajaxnonce);
                });

                this.on('success', function(file, response) {
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

                this.on('error', function(file, response) {
                    file.previewElement.classList.add("dz-error");
                    jQuery(file.previewElement).find('div.dz-error-message>span').text(response);
                    if (submitButtons.length > 0) {
                        submitButtons.removeAttr("disabled");
                        submitButton.html(existingHtmlInsideSubmitButton);
                    }
                });
                this.on('removedfile', function(file) {
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
                    }, function(data) {
                        console.log(data);
                    }).always(function() {
                        if (submitButtons.length > 0) {
                            submitButtons.removeAttr("disabled");
                            submitButton.html(existingHtmlInsideSubmitButton);
                        }
                    });
                });

                for (var key in uploadFields) {
                    var mockFile = {
                        name: uploadFields[key]['name'],
                        size: uploadFields[key]['size'],
                        url: uploadFields[key]['url'],
                        attachment_id: uploadFields[key]['attachment_id']
                    };
                    this.emit('addedfile', mockFile);
                    this.emit('thumbnail', mockFile, mockFile.url);
                    this.emit('complete', mockFile);
                    this.files.push(mockFile);
                }
            }
        });

    });
});
