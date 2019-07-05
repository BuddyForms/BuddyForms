function uploadHandler() {
    Dropzone.autoDiscover = false;
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
            var current = jQuery(this);
            var clickeable = (current.attr('page') !== 'buddyforms_submissions');
            var maxFileSize = current.attr('file_limit');
            var acceptedFiles = current.attr('accepted_files');
            var multipleFiles = current.attr('multiple_files');
            var entry = current.data('entry');
            var form_slug = current.attr('form-slug');
            jQuery('#buddyforms_form_' + form_slug).show();

            initSingleDropZone(current, current.attr('id'), maxFileSize, acceptedFiles, multipleFiles, clickeable, entry)
        })
    }

    function initSingleDropZone(current, id, maxSize, acceptedFiles, multipleFiles, clickeable, uploadFields) {
        //Hidden field
        var hidden_field = jQuery(current).find('input[type="text"][style*="hidden"]');
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
                        console.log('DropZoneQueueComplete');
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
                }
            };
            jQuery(current).dropzone(options);
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

    function DropZoneSuccess(file, response, currentField) {
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
            var currentDropZone = jQuery(this)[0].dropzone;
            if (currentDropZone && currentDropZone.files.length > 0) {
                var allFilesSuccessDiff = currentDropZone.files.filter(function (file) {
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

    return {
        init: function () {
            var uploadFields = jQuery(".upload_field");
            submitButtons = jQuery("div.form-actions button.bf-submit[type=submit], div.form-actions button.bf-draft[type=button]");
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
    uploadImplementation.init();
});
