function bf_form_errors() {

    jQuery('input').removeClass('error');

    var errors = jQuery('.bf-alert-wrap ul li span');
    jQuery.each(errors, function (i, error) {
        var field_id = jQuery(error).attr('data-field-id');
        console.log(field_id);
        if (field_id === 'user_pass') {
            jQuery('#' + field_id + '2').addClass('error');
        }
        jQuery('#' + field_id).addClass('error');
    });
}

jQuery(document).ready(function () {

    var bf_submission_modal_content = jQuery(".buddyforms-posts-content");
    var bf_submission_modal = '';

    jQuery(document).on("click", '.bf-submission-modal', function (evt) {

        //console.log(evt);

        bf_submission_modal = jQuery("#bf-submission-modal_" + jQuery(this).attr('data-id'));

        jQuery('.buddyforms-posts-container').html(bf_submission_modal);

        jQuery("#bf-submission-modal_" + jQuery(this).attr('data-id') + " :input").attr("disabled", true);
        jQuery("#bf-submission-modal_" + jQuery(this).attr('data-id')).show();
        return false;
    });


    jQuery(document).on("click", '.bf-close-submissions-modal', function (evt) {
        bf_submission_modal_content.find('.bf_posts_' + jQuery(this).attr('data-id')).prepend(bf_submission_modal);
        jQuery('.buddyforms-posts-container').html(bf_submission_modal_content);
        jQuery("#bf-submission-modal_" + jQuery(this).attr('data-id')).hide();
        return false;
    });

    bf_form_errors();

    jQuery('.bf-garlic').garlic();

    //jQuery(".bf-select2").select2({
    //    placeholder: "Select an option",
    //    tags: true,
    //    tokenSeparators: [',', ' ']
    //});

    jQuery(document).on("click", '.create-new-tax-item', function (evt) {

        var field_id = jQuery(this).attr('data-field_id');
        var field_slug = jQuery(this).attr('data-field_slug');

        var newStateVal = jQuery("#" + field_slug + "_create_new_tax_" + field_id).val();

        // Set the value, creating a new option if necessary
        if (jQuery("#category_create_new_tax").find("option[value='" + newStateVal + "']").length) {
            jQuery("#category_create_new_tax").val(newStateVal).trigger("change");
        } else {

            // Create the DOM option that is pre-selected by default
            var newState = new Option(newStateVal, newStateVal, true, true);

            // Append it to the select
            jQuery("#" + field_id).append(newState).trigger('change');

            // CLear the text field
            jQuery("#" + field_slug + "_create_new_tax_" + field_id).val('');
        }
        return false;
    });


    jQuery('.bf_datetime').datetimepicker({
        controlType: 'select',
        timeFormat: 'hh:mm tt'
    });

    var bf_status = jQuery('select[name=status]').val();

    if (bf_status == 'future') {
        jQuery('.bf_datetime_wrap').show();
    } else {
        jQuery('.bf_datetime_wrap').hide();
    }

    jQuery('select[name=status]').change(function () {
        var bf_status = jQuery(this).val();
        if (bf_status == 'future') {
            jQuery('.bf_datetime_wrap').show();
        } else {
            jQuery('.bf_datetime_wrap').hide();
        }
    });

    var buddyforms_form_content_val = jQuery('#buddyforms_form_content_val').html();
    jQuery('#buddyforms_form_content').html(buddyforms_form_content_val);

    jQuery(document).on("click", '.bf_delete_post', function (event) {
        var post_id = jQuery(this).attr('id');

        if (confirm('Delete Permanently')) {
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {"action": "buddyforms_ajax_delete_post", "post_id": post_id},
                success: function (data) {
                    if (isNaN(data)) {
                        alert(data);
                    } else {
                        var id = "#bf_post_li_";
                        var li = id + data;
                        li = li.replace(/\s+/g, '');
                        jQuery(li).remove();
                    }
                },
                error: function (request, status, error) {
                    alert(request.responseText);
                }
            });
        } else {
            return false;
        }
        return false;
    });
});

