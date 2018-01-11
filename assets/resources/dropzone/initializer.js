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
            checked = false;

       // var myDropzone = new Dropzone("#"+id, { url: "/uploads"});
        Dropzone.autoDiscover = false;

        $("#"+id).dropzone({
            url: dropParam.upload,
            maxFilesize: max_size,
            acceptedFiles: accepted_files,
            success: function (file, response) {
                file.previewElement.classList.add("dz-success");
                file['attachment_id'] = response; // push the id for future reference
                var ids = jQuery('#media-ids').val() + ',' + response;
                jQuery('#media-ids').val(ids);
            },
            error: function (file, response) {
                file.previewElement.classList.add("dz-error");
            },
            // update the following section is for removing image from library
            addRemoveLinks: true,
            removedfile: function(file) {
                var attachment_id = file.attachment_id;
                jQuery.ajax({
                    type: 'POST',
                    url: dropParam.delete,
                    data: {
                        media_id : attachment_id
                    }
                });
                var _ref;
                return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
            }
        });

    });
    /* </fs_premium_only> */
});