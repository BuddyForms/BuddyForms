// On Load jQuery
jQuery(function ($) {

    $('.bf-color').wpColorPicker();

    // Type box.
    $('.bf-form-type-wrap').appendTo('#buddyforms_form_setup .hndle span');

    $(function () {
        // Prevent inputs in meta box headings opening/closing contents.
        $('#buddyforms_form_setup').find('.hndle').unbind('click.postboxes');

        $('#buddyforms_form_setup').on('click', '.hndle', function (event) {

            // If the user clicks on some form input inside the h3 the box should not be toggled.
            if ($(event.target).filter('input, option, label, select').length) {
                return;
            }

            $('#buddyforms_form_setup').toggleClass('closed');
        });
    });

    $('form').bind('submit', function () {
        $(this).find(':input').prop('disabled', false);
    });

});
jQuery(document).ready(function (jQuery) {

    //
    // Show Hide Color Picker
    //
    jQuery(document.body).on('change', '.bf-color-radio', function () {

        var style = jQuery(this).val();
        var field_id = jQuery(this).attr('data-field_id');

        if (style == 'color') {
            jQuery('#bf_color_container_' + field_id).removeClass('bf-color-hidden');
        } else {
            jQuery('#bf_color_container_' + field_id).addClass('bf-color-hidden');
        }

    });

    //
    // Add the value selected in the modal to the form element select box and trigger the change event to add the new form element to the sortable list
    //
    jQuery(document.body).on('change', '#bf_add_new_form_element_modal', function () {
        jQuery('#formbuilder-action-select-modal').dialog("close");
        jQuery('#bf_add_new_form_element').val(jQuery('#bf_add_new_form_element_modal').val());
        jQuery("#formbuilder-add-element").trigger("click");
    });

    //
    // Add new form element to the form builder sortable list
    //
    jQuery(document.body).on('click', '#formbuilder-add-element', function () {

        jQuery('.formbuilder-spinner').addClass('is-active');

        var action = jQuery(this);
        var post_id = bf_getUrlParameter('post');

        if (post_id == undefined)
            post_id = 0;

        var fieldtype = jQuery('#bf_add_new_form_element').val();

        if (fieldtype === 'none') {
            jQuery('#bf_add_new_form_element_modal').val('none');

            jQuery('#formbuilder-action-select-modal').dialog({
                title: "Please Select a Field Type",
                height: 240,
                modal: true,
            });
            jQuery('.formbuilder-spinner').removeClass('is-active');
            return false;
        }

        var unique = jQuery('#bf_add_new_form_element').find(':selected').data('unique');
        var exist = jQuery("#sortable_buddyforms_elements .bf_" + fieldtype);

        if (unique === 'unique') {
            if (exist !== null && typeof exist === 'object' && exist.length > 0) {
                bf_alert('This element can only be added once into each form');
                jQuery('.formbuilder-spinner').removeClass('is-active');
                return false;
            }
        }

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                "action": "buddyforms_display_form_element",
                "fieldtype": fieldtype,
                "unique": unique,
                "post_id": post_id
            },
            success: function (data) {
                if (data == 'unique') {
                    bf_alert('This element can only be added once into each form');
                    return false;
                }

                jQuery('.buddyforms_template').remove();

                data = data.replace('accordion-body collapse', 'accordion-body in collapse');

                jQuery('#sortable_buddyforms_elements').append(data);
                jQuery('.formbuilder-spinner').removeClass('is-active');

                bf_update_list_item_number();

                jQuery('#buddyforms_form_elements').removeClass('closed');
                jQuery("html, body").animate({scrollTop: jQuery('#buddyforms_form_elements ul li:last').offset().top - 200}, 1000);
                jQuery('.bf-select2').select2();

                var form_post_type = jQuery('#form_post_type').val();

                if (form_post_type == 'bf_submissions') {

                    var field_id = jQuery(data).find('#this_field_id').val();

                    bf_taxonomy_input(field_id)
                }


            },
            error: function () {
                jQuery('.formbuilder-spinner').removeClass('is-active');
                jQuery('<div></div>').dialog({
                    modal: true,
                    title: "Info",
                    open: function () {
                        var markup = 'Something went wrong ;-(sorry)';
                        jQuery(this).html(markup);
                    },
                    buttons: {
                        Ok: function () {
                            jQuery(this).dialog("close");
                        }
                    }
                });
            }
        });
        return false;

    });

    //
    // Load the Form From Template
    //
    jQuery(document.body).on('click', '.bf_form_template', function () {

        var template = jQuery(this).data("template");
        load_formbuilder_template(template);
        return false;

    });

    //
    // Generate the field slug from the label
    //
    jQuery(document.body).on('blur', '.use_as_slug', function () {

        var field_name = jQuery(this).val();
        if (field_name === '')
            return;

        var field_id = jQuery(this).attr('data');
        if (field_id === '')
            return;

        var field_slug_val = jQuery('tr .slug' + field_id).val();


        if (field_slug_val === '') {
            jQuery('tr .slug' + field_id).val(slug(field_name, {lower: true}));
        }
        jQuery(this).unbind('blur');
    });
    //
    // Reset option for multiple choice fields radio and checkboxes
    //
    jQuery(document.body).on('click', '.button.bf_reset_multi_input', function (event) {
        event.preventDefault();
        var group_name = jQuery(this).attr('data-group-name');
        jQuery('input[name="' + group_name + '"]').attr('checked', false);
        return false;
    });
});
