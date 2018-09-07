function uploadHandler() {
    Dropzone.autoDiscover = false;
    var submitButtons, submitButton,
        existingHtmlInsideSubmitButton = '';

    function getFirstSubmitButton(submitButtons) {
        submitButton = submitButtons.first();
        existingHtmlInsideSubmitButton = submitButton.html();
    }

    function buildDropZoneFieldsOptions() {
        jQuery(".upload_field").each(function () {
            var current = jQuery(this);
            var clickeable = (current.attr('page') !== 'buddyforms_submissions');
            var maxFileSize = current.attr('file_limit');
            var acceptedFiles = current.attr('accepted_files');
            var multipleFiles = current.attr('multiple_files');
            var entry = current.data('entry');

            initSingleDropZone(current.attr('id'), maxFileSize, acceptedFiles, multipleFiles, clickeable, entry)
        })
    }

    function initSingleDropZone(id, maxSize, acceptedFiles, multipleFiles, clickeable, uploadFields) {
        var field = jQuery('#field_' + id);
        var dropzoneStringId = '#' + id;
        //Set default values
        var options = {
            url: dropParam.admin_url,
            maxFilesize: maxSize,
            acceptedFiles: acceptedFiles,
            maxFiles: multipleFiles,
            clickable: clickeable,
            addRemoveLinks: clickeable,
            init: function () {
                this.on('queuecomplete', function () {
                    console.log('DropZoneQueueComplete');
                });
                this.on('addedfile', function () {
                    DropZoneAddedFile(dropzoneStringId);
                });
                this.on('success', function (file, response) {
                    DropZoneSuccess(file, response, field);
                });
                this.on('error', DropZoneError);
                this.on('sending', DropZoneSending);
                this.on('sendingmultiple', DropZoneSending);
                this.on('complete', DropZoneComplete);
                this.on('completemultiple', DropZoneComplete);
                this.on('removedfile', function (file) {
                    DropZoneRemovedFile(file, field);
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
        };
        jQuery(dropzoneStringId).dropzone(options);
    }

    function DropZoneComplete() {
        enabledSubmitButtons();
    }

    function DropZoneAddedFile(dropzoneStringId) {
        jQuery(dropzoneStringId + "-error").text("");
        jQuery('.dz-progress').hide()
    }

    function DropZoneSending(file, xhr, formData) {
        disableSubmitButtons(true);
        formData.append('action', 'handle_dropped_media');
        formData.append('nonce', dropParam.ajaxnonce);
    }

    function DropZoneSuccess(file, response, currentField) {
        console.log('success', currentField);
        file.previewElement.classList.add("dz-success");
        file['attachment_id'] = response; // push the id for future reference
        var ids = jQuery(currentField).val() + ',' + response;
        var idsFormat = "";
        if (ids[0] === ',') {
            idsFormat = ids.substring(1, ids.length);
        } else {
            idsFormat = ids;
        }
        jQuery(currentField).attr('value', idsFormat);
    }

    function DropZoneError(file, response) {
        file.previewElement.classList.add("dz-error");
        jQuery(file.previewElement).find('div.dz-error-message>span').text(response);
        enabledSubmitButtons();
    }

    function DropZoneRemovedFile(file, currentField) {
        var attachment_id = file.attachment_id;
        var ids = jQuery(currentField).val();
        var remainigIds = ids.replace(attachment_id, "");
        if (remainigIds[0] === ',') {
            remainigIds = remainigIds.substring(1, ids.length);
        }
        var lastChar = remainigIds[remainigIds.length - 1];
        if (lastChar === ',') {
            remainigIds = remainigIds.slice(0, -1);
        }
        jQuery(currentField).attr('value', remainigIds);
        handleDeletedMedia(attachment_id);
    }

    function handleDeletedMedia(attachmentId) {
        disableSubmitButtons(false);
        jQuery.post(dropParam.admin_url, {
            action: 'handle_deleted_media',
            media_id: attachmentId,
            nonce: dropParam.ajaxnonce
        }, function (data) {
            console.log(data);
        }).always(function () {
            enabledSubmitButtons();
        });
    }

    function disableSubmitButtons(showButtonText) {
        if (submitButtons.length > 0) {
            showButtonText = !!(showButtonText);
            submitButtons.attr("disabled", "disabled");
            if (showButtonText) {
                submitButton.html('Upload in progress');
            }
        }
    }

    function enabledSubmitButtons() {
        if (submitButtons.length > 0) {
            submitButtons.removeAttr("disabled");
            submitButton.html(existingHtmlInsideSubmitButton);
        }
    }

    return {
        init: function () {
            var uploadFields = jQuery(".upload_field");
            submitButtons = jQuery("button.bf-submit[type=submit]");
            if (submitButtons.length > 0) {
                getFirstSubmitButton(submitButtons);
            }
            if (uploadFields.length > 0) {
                buildDropZoneFieldsOptions();
            }
        }
    }
}

var uploadImplementation = uploadHandler();
jQuery(document).ready(function () {
    if (jQuery.validator) {
        jQuery.validator.addMethod("upload-required", function (value, element) {
            if (Dropzone) {
                var dropZoneId = jQuery(element).attr('name');
                var currentDropZone = jQuery('#' + dropZoneId)[0].dropzone;
                if (currentDropZone) {
                    return currentDropZone.files.length > 0;
                }
            }
            return false;
        }, "This field is required.");
        jQuery.validator.addMethod("upload-max-exceeded", function (value, element, param) {
            if (Dropzone) {
                var dropZoneId = jQuery(element).attr('name');
                var currentDropZone = jQuery('#' + dropZoneId)[0].dropzone;
                if (currentDropZone) {
                    return param >= currentDropZone.files.length;
                }
            }
            return false;
        }, "The number of files is greater than allowed.");
        jQuery.validator.addMethod("upload-group", function (value, element) {
            console.log('validate the group of uploads');
            var $fields = jQuery('.upload_field_input', element.form),
                $fieldsFirst = $fields.eq(0),
                validator = $fieldsFirst.data("valid_req_grp") ? $fieldsFirst.data("valid_req_grp") : jQuery.extend({}, this),
                result = $fields.filter(function () {
                    var dropZoneId = jQuery(this).attr('name');
                    var currentDropZone = jQuery('#' + dropZoneId)[0].dropzone;
                    if (currentDropZone.files.length > 0) {
                        return currentDropZone.files.filter(function (file) {
                            return file.status !== Dropzone.SUCCESS;
                        });
                    } else {
                        console.log('no files, no need this validation');
                        return true;
                    }
                });
            var isValid = true;
            if (jQuery.isArray(result)) {
                isValid = result.length === 0;
            }

            // Store the cloned validator for future validation
            $fieldsFirst.data("valid_req_grp", validator);

            // If element isn't being validated, run each require_from_group field's validation rules
            if (!jQuery(element).data("being_validated")) {
                $fields.data("being_validated", true);
                $fields.each(function () {
                    validator.element(this);
                });
                $fields.data("being_validated", false);
            }
            console.log('isvalid ', isValid);
            return isValid;
        }, 'Need Uploading');
    }
    uploadImplementation.init();
});
