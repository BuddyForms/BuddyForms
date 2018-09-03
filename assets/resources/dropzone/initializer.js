function uploadHandler() {
    Dropzone.autoDiscover = false;
    var isCompleted = false,
        uploadFieldsLength = 0,
        submitButtons, submitButton,
        existingHtmlInsideSubmitButton = '';

    function getFirstSubmitButton(submitButtons) {
        submitButton = submitButtons.first();
        existingHtmlInsideSubmitButton = submitButton.html();
    }

    function handleSubmitClick(event) {
        event.preventDefault();
        //base case is to let form send, if something is wrong stop send it
        if (!isCompleted) {
            var form = $(this).closest('form'),
                uploadFields = form.find('.upload_field');
            if (uploadFields.length > 0) {
                var existFiles = false;
                uploadFieldsLength = uploadFields.length;
                uploadFields.each(function () {
                    if (Dropzone) {
                        var current = jQuery(this);
                        var currentDropZone = jQuery(current)[0].dropzone;
                        if (currentDropZone) {
                            existFiles = currentDropZone.files.length > 0;
                            if (existFiles) {
                                return false;
                            }
                        }
                    }
                });
                if (existFiles) {
                    if (submitButtons.length > 0) {
                        submitButtons.attr("disabled", "disabled");
                        submitButton.html("Upload in progress");
                    }
                    uploadFields.each(function () {
                        if (Dropzone) {
                            var current = jQuery(this);
                            var currentDropZone = jQuery(current)[0].dropzone;
                            if (currentDropZone) {
                                var result = currentDropZone.processQueue();
                                console.log(result)
                            }
                        }
                    });
                    console.log('onclick preventDefault');
                    //event.preventDefault();
                }
            }
        }
    }

    function buildDropZoneFieldsOptions() {
        jQuery(".upload_field").each(function () {
            var current = jQuery(this);
            var clickeable = (current.attr('page') !== 'buddyforms_submissions');
            var maxFileSize = current.attr('file_limit');
            var acceptedFiles = current.attr('accepted_files');
            var multipleFiles = current.attr('multiple_files');
            var isRequired = current.attr('required');
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
            autoProcessQueue: false,
            clickable: clickeable,
            addRemoveLinks: clickeable,
            init: function () {
                this.on('queuecomplete', function () {
                    DropZoneQueueComplete(dropzoneStringId);
                });
                this.on('addedfile', function () {
                    DropZoneAddedFile(dropzoneStringId);
                });
                this.on('success', function (file, response) {
                    DropZoneSuccess(file, response, field);
                });
                this.on('error', DropZoneError);
                this.on('sending', DropZoneSending);
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

    function DropZoneQueueComplete(dropzoneStringId) {
        uploadFieldsLength--;
        if (uploadFieldsLength === 0) {
            var form = jQuery(dropzoneStringId).closest('form');
            // form.submit();
            console.log('submit');
        }
    }

    function DropZoneAddedFile(dropzoneStringId) {
        jQuery(dropzoneStringId + "-error").text("");
        jQuery('.dz-progress').hide()
    }

    function DropZoneSending(file, xhr, formData) {
        formData.append('action', 'handle_dropped_media');
        formData.append('nonce', dropParam.ajaxnonce);
    }

    function DropZoneSuccess(file, response, currentField) {
        console.log('success');
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
    }

    function DropZoneError(file, response) {
        file.previewElement.classList.add("dz-error");
        jQuery(file.previewElement).find('div.dz-error-message>span').text(response);
        enabledSubmitButtons();
    }

    function DropZoneRemovedFile(file, currentField) {
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

    function disableSubmitButtons(changeButtonText) {
        if (submitButtons.length > 0) {
            changeButtonText = changeButtonText || true;
            submitButtons.attr("disabled", "disabled");
            if (changeButtonText) {
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
            submitButtons = jQuery("button.bf-submit[type=submit]");
            if (submitButtons.length > 0) {
                getFirstSubmitButton(submitButtons);
                submitButtons.on('click', handleSubmitClick);
                buildDropZoneFieldsOptions();
            }
        }
    }
}

var uploadImplementation = uploadHandler();
jQuery(document).ready(function () {
    uploadImplementation.init()
});
