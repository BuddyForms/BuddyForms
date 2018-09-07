function uploadHandler() {
    Dropzone.autoDiscover = false;
    var isCompleted = false,
        submitButtons, submitButton, uploadFieldsValidation = [],
        existingHtmlInsideSubmitButton = '';

    function getFirstSubmitButton(submitButtons) {
        submitButton = submitButtons.first();
        existingHtmlInsideSubmitButton = submitButton.html();
    }

    function handleSubmitClick() {
        if (!isCompleted) {
            var form = jQuery(this).closest('form'),
                uploadFields = form.find('.upload_field');
            if (uploadFields.length > 0) {
                uploadFields.each(function () {
                    if (Dropzone) {
                        var currentDropZone = jQuery(this)[0].dropzone;
                        if (currentDropZone ) {
                            var result = currentDropZone.processQueue();
                            console.log('process done', result);
                        }
                    }
                });
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

    function checkIfAllSuccess() {
        var uploadFields = jQuery(this).closest('form').find('.upload_field');
        if (uploadFields.length > 0) {
            uploadFields.each(function () {
                var currentDropZone = jQuery(this)[0].dropzone;
                if (currentDropZone.files.length > 0) {
                    var allFiles = currentDropZone.files.filter(function (file) {
                        return file.status !== Dropzone.SUCCESS;
                    }).map(function (file) {
                        return file;
                    });
                    console.log('files different of success', allFiles);
                    return allFiles.length === 0;
                } else {
                    return false;
                }
            });
        }
        return false;
    }

    function DropZoneQueueComplete(dropzoneStringId) {
       // var isSuccessAll = checkIfAllSuccess();
       // console.log('isSuccessAll ', isSuccessAll, ' #', dropzoneStringId);
        var form = jQuery(dropzoneStringId).closest('form');
        var isValid= form.valid();
        if(isValid){
            form.submit();
        }
       /* if(isSuccessAll){
            var form = jQuery(dropzoneStringId).closest('form');
           var isValid= form.valid();
           if(isValid){
               form.submit();
           }

        }*/
    }

    function DropZoneAddedFile(dropzoneStringId) {
        //add a clean here for jquery validation hide the messages
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
    jQuery.validator.addMethod("upload-group", function (value, element, options) {

        var fields = jQuery('.upload_field_input', element.form);
            var $fieldsFirst = fields.eq(0);
            var enqueueFields = new Array();
            var isValid = true;
            var validator = $fieldsFirst.data("valid_req_grp") ? $fieldsFirst.data("valid_req_grp") : jQuery.extend({}, this);
            for(var i =0; i< fields.length;i++) {
                var dropZoneId = jQuery(fields[i]).attr('name');
                var currentDropZone = jQuery('#' + dropZoneId)[0].dropzone;

                if (currentDropZone.files.length > 0) {
                    enqueueFields[i]= currentDropZone.files.filter(function (file) {
                        return file.status === Dropzone.UPLOADING || file.status === Dropzone.QUEUED;
                    }).length;
                }
            };
         for(var k=0; k < enqueueFields.length; k++){
             if(enqueueFields[k] > 0){
                 isValid = false;
                 break;
             }
         }


        // Store the cloned validator for future validation
        $fieldsFirst.data("valid_req_grp", validator);

        // If element isn't being validated, run each require_from_group field's validation rules
        if (!jQuery(element).data("being_validated")) {
            fields.data("being_validated", true);
            fields.each(function () {
                validator.element(this);
            });
            fields.data("being_validated", false);
        }
        return isValid;
    }, 'uploading or queued');
    uploadImplementation.init();
});
