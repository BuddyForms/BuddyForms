function uploadHandler() {
    var submitButtons, submitButton,
        existingHtmlInsideSubmitButton = '';

    function getFirstSubmitButton(submitButtons) {
        submitButton = jQuery.map(submitButtons, function (element) {
            return (jQuery(element).attr('type') === 'submit' && jQuery(element).hasClass('bf-submit')) ? jQuery(element) : null;
        })[0];
        existingHtmlInsideSubmitButton = submitButton.html();
    }

    function buildDropZoneFieldsOptions() {
        jQuery(".upload_field").each(function () {
            var $field = jQuery(this);
            var clickeable = ($field.attr('page') !== 'buddyforms_submissions');
            var maxFileSize = $field.attr('file_limit');
            var acceptedFiles = $field.attr('accepted_files');
            var multipleFiles = $field.attr('multiple_files');
            var entry = $field.data('entry');
            var form_slug = $field.attr('form-slug');
            jQuery('#buddyforms_form_' + form_slug).show();

            initSingleDropZone($field, $field.attr('id'), maxFileSize, acceptedFiles, multipleFiles, clickeable, entry)
        })
    }

    function initSingleDropZone($field, id, maxSize, acceptedFiles, multipleFiles, clickeable, uploadFields) {
        //Hidden field
        var hidden_field = jQuery($field).find('input[type="text"][style*="hidden"]');
        //Container field
        var dropzoneStringId = '#' + id;
        //Set default values
        if (buddyformsGlobal) {
            var options = {
                url: buddyformsGlobal.admin_url,
                maxFilesize: maxSize,
                parallelUploads: 1,
                acceptedFiles: acceptedFiles,
                maxFiles: multipleFiles,
                clickable: clickeable,
                addRemoveLinks: clickeable,
                init: function () {
                    this.on('queuecomplete', function () {
                        $field.removeClass('error');
                    });
                    this.on('addedfile', function () {
                        DropZoneAddedFile(dropzoneStringId);
                    });
                    this.on('success', function (file, response) {
                        DropZoneSuccess(file, response, hidden_field);
                    });
                    this.on('error', DropZoneError);
                    this.on('sending', DropZoneSending);
                    this.on('sendingmultiple', DropZoneSending);
                    this.on('complete', DropZoneComplete);
                    this.on('completemultiple', DropZoneComplete);
                    this.on('removedfile', function (file) {
                        DropZoneRemovedFile(file, hidden_field);
                    });

                    if (uploadFields) {
                        for (var key in uploadFields) {
                            if (key) {
                                var mockFile = {
                                    name: uploadFields[key]['name'],
                                    size: uploadFields[key]['size'],
                                    url: uploadFields[key]['url'],
                                    attachment_id: uploadFields[key]['attachment_id'],
                                };
                                this.emit('addedfile', mockFile);
                                this.emit('thumbnail', mockFile, mockFile.url);
                                this.emit('complete', mockFile);
                                this.files.push(mockFile);
                            }
                        }
                    }
                },
                //Language options
                dictMaxFilesExceeded: buddyformsGlobal.localize.upload.dictMaxFilesExceeded || "You can not upload any more files.",
                dictRemoveFile: buddyformsGlobal.localize.upload.dictRemoveFile || "Remove file",
                dictCancelUploadConfirmation: buddyformsGlobal.localize.upload.dictCancelUploadConfirmation || "Are you sure you want to cancel this upload?",
                dictCancelUpload: buddyformsGlobal.localize.upload.dictCancelUpload || "Cancel upload",
                dictResponseError: buddyformsGlobal.localize.upload.dictResponseError || "Server responded with {{statusCode}} code.",
                dictInvalidFileType: buddyformsGlobal.localize.upload.dictInvalidFileType || "You can't upload files of this type.",
                dictFileTooBig: buddyformsGlobal.localize.upload.dictFileTooBig || "File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.",
                dictFallbackMessage: buddyformsGlobal.localize.upload.dictFallbackMessage || "Your browser does not support drag'n'drop file uploads.",
                dictDefaultMessage: buddyformsGlobal.localize.upload.dictDefaultMessage || "Drop files here to upload",
            };
            jQuery($field).dropzone(options);
        }
    }

    function DropZoneComplete() {
        enabledSubmitButtons();
    }

    function DropZoneAddedFile(dropzoneContainer) {
        jQuery(dropzoneContainer).find("label[class*='error']").text("");
        jQuery(dropzoneContainer).find('.dz-progress').hide()
    }

    function DropZoneSending(file, xhr, formData) {
        disableSubmitButtons(true);
        formData.append('action', 'handle_dropped_media');
        formData.append('nonce', buddyformsGlobal.ajaxnonce);
    }

    function DropZoneSuccess(file, response, $fieldField) {
        file.previewElement.classList.add("dz-success");
        file['attachment_id'] = response; // push the id for future reference
        var ids = jQuery($fieldField).val() + ',' + response;
        var idsFormat = "";
        if (ids[0] === ',') {
            idsFormat = ids.substring(1, ids.length);
        } else {
            idsFormat = ids;
        }
        jQuery($fieldField).attr('value', idsFormat);
    }

    function DropZoneError(file, response) {
        file.previewElement.classList.add("dz-error");
        jQuery(file.previewElement).find('div.dz-error-message>span').text(response);
        enabledSubmitButtons();
    }

    function DropZoneRemovedFile(file, $fieldField) {
        var attachment_id = file.attachment_id;
        var ids = jQuery($fieldField).val();
        var remainigIds = ids.replace(attachment_id, "");
        if (remainigIds[0] === ',') {
            remainigIds = remainigIds.substring(1, ids.length);
        }
        var lastChar = remainigIds[remainigIds.length - 1];
        if (lastChar === ',') {
            remainigIds = remainigIds.slice(0, -1);
        }
        jQuery($fieldField).attr('value', remainigIds);
        handleDeletedMedia(attachment_id);
    }

    function handleDeletedMedia(attachmentId) {
        disableSubmitButtons(false);
        jQuery.post(buddyformsGlobal.admin_url, {
            action: 'handle_deleted_media',
            media_id: attachmentId,
            nonce: buddyformsGlobal.ajaxnonce
        }, function (data) {
            console.log(data);
        }).always(function () {
            enabledSubmitButtons();
        });
    }

    function disableSubmitButtons(showButtonText) {
        if (buddyformsGlobal) {
            if (submitButtons.length > 0) {
                showButtonText = !!(showButtonText);
                submitButtons.attr("disabled", "disabled");
                if (showButtonText) {
                    submitButton.html(buddyformsGlobal.localize.upload.submitButton || 'Upload in progress'); // todo need il18n
                }
            }
        }
    }

    function checkToEnableSubmit() {
        var result = true;
        jQuery(".upload_field").each(function () {
            var $fieldDropZone = jQuery(this)[0].dropzone;
            if ($fieldDropZone && $fieldDropZone.files.length > 0) {
                var allFilesSuccessDiff = $fieldDropZone.files.filter(function (file) {
                    return file.status === Dropzone.UPLOADING;
                });
                result = allFilesSuccessDiff.length === 0;
            }
        });

        return result;
    }

    function enabledSubmitButtons() {
        if (submitButtons.length > 0 && checkToEnableSubmit()) {
            submitButtons.removeAttr("disabled");
            submitButton.html(existingHtmlInsideSubmitButton);
        }
    }

    function validateAndUploadImage(field) {

        const $field         = jQuery(field);
        const id             = $field.attr("field-id");
        const accepted_files = $field.attr("accepted_files");
        const maxFileSize    = $field.data('max-file-size');
        const url            = jQuery("#" + id + "_upload_from_url").val();

        const uploadedImages  = parseInt($field.closest('.bf-input').find('.bf-uploaded-image-wrapper').length);
        const uploadMaxExceeded = parseInt($field .data('upload-max-exceeded'));
        if (uploadedImages>= uploadMaxExceeded) {
            const dictMaxFilesExceeded = (((buddyformsGlobal || {}).localize || {}).upload || {}).dictMaxFilesExceeded || "You can not upload any more files.";
            jQuery("#" + id + "_label").text(dictMaxFilesExceeded);
            return;
        }

        jQuery("#" + id + "_label").text("");

        if (checkURL(url)) {

            jQuery("#" + id + "_upload_button").text("Uploading..");
            jQuery("#" + id + "_upload_button").attr('disabled', true);
            var submitButtons = jQuery("div.form-actions button.bf-submit[type=submit], div.form-actions button.bf-draft[type=button]");
            submitButtons.attr('disabled', true);

            jQuery.ajax({
                url: buddyformsGlobal.admin_url,
                type: 'post',
                data: {
                    action: 'upload_image_from_url',
                    url: encodeURIComponent(url),
                    accepted_files: accepted_files,
                    id: id,
                    max_file_size: maxFileSize
                },
                success: function (response) {
                    var result = JSON.parse(response);
                    if (result.status ==="OK"){

                        const attachmentWrapper      = jQuery('<div class="bf-uploaded-image-wrapper"></div>');
                        const removeImageLocalizeStr = (((buddyformsGlobal || {} ).localize || {}).upload || {}).removeImage || 'Remove Image';

                        jQuery("#field_" + id).closest('.bf-input').append(
                            attachmentWrapper
                                .append(`<img id="${id}_image" src="${result.response}" width="150" height="150">`)
                                .append(`<br><a data-field-id="${id}" data-attachment-id="${result.attachment_id}" class="remove_image_button">${removeImageLocalizeStr}</a>`)
                        );

                        const $fieldValue = jQuery("#field_" + id).val();
                        jQuery("#field_" + id).val($fieldValue.trim() + "," + result.attachment_id);

                        jQuery("#" + id + "_upload_button").text("Upload");
                        jQuery("#" + id + "_upload_button").attr('disabled', false);
                        submitButtons.attr('disabled', false);
                    }else{
                        if(result.status ==="FAILED"){
                            jQuery("#" + id + "_label").text(result.response);
                            jQuery("#" + id + "_upload_button").text("Upload");
                            jQuery("#" + id + "_upload_button").attr('disabled', false);
                            submitButtons.attr('disabled', false);
                        }
                    }
                },
                error: function (error) {
                    var result = JSON.parse(error);
                }

            });

        } else {
            jQuery("#" + id + "_label").text("Wrong Url Format");
        }
    }

    function uploadFromUrlRemoveFile(el) {
        const $el = jQuery(el);
        const attachmentId = $el.data('attachment-id');

        // Remove image from dom.
        $el.closest('.bf-uploaded-image-wrapper').remove();

        // Delete the media via AJAX
        handleDeletedMedia(attachmentId);
    }

    function checkURL(url) {
        return (url.match(/\.(jpeg|jpg|gif|png)$/) != null);
    }

    return {
        init: function () {
            var uploadFields = jQuery(".upload_field");
            submitButtons = jQuery("div.form-actions button.bf-submit[type=submit], div.form-actions button.bf-draft[type=submit]");
            if (submitButtons.length > 0) {
                getFirstSubmitButton(submitButtons);
            }
            if (uploadFields.length > 0) {
                buildDropZoneFieldsOptions();
            }

            jQuery(document).on('click', '.upload_button', function(){
                validateAndUploadImage(this);
            });

            jQuery(document).on('click', '.remove_image_button', function() {
                uploadFromUrlRemoveFile(this);
            });
        }
    }
}

var uploadImplementation = uploadHandler();
jQuery(document).ready(function () {
    uploadImplementation.init();
});
if(Dropzone) {
    Dropzone.autoDiscover = false;
}
