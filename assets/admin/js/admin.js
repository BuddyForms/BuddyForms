//
// Helper function to get the post id from url
//
var bf_getUrlParameter = function bf_getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

//
// Update form builder form elements list number 1,2,3,...
//
function bf_update_list_item_number() {
    jQuery(".buddyforms_forms_builder ul").each(function () {
        jQuery(this).children("li").each(function (t) {
            jQuery(this).find("td.field_order .circle").first().html(t + 1)
        })
    })
}

//
// Helper Function to use dialog instead of alert
//
function bf_alert(alert_message) {
    jQuery('<div></div>').dialog({
        modal: true,
        title: "Info",
        open: function () {
            jQuery(this).html(alert_message);
        },
        buttons: {
            Ok: function () {
                jQuery(this).dialog("close");
            }
        }
    });
}

// Update ths list number 1,2,3,... for the mail trigger
function bf_update_list_item_number_mail() {
    jQuery("#mailcontainer .bf_trigger_list_item").each(function (t) {
        jQuery(this).find("td.field_order .circle").first().html(t + 1)
    })
}

//
// Helper Function to lode form element templates depend on the form type
//
function load_formbuilder_template(template) {

    jQuery.ajax({
        type: 'POST',
        dataType: "json",
        url: ajaxurl,
        data: {
            "action": "buddyforms_form_template",
            "template": template,
        },
        success: function (data) {

            //console.log(data);

            jQuery.each(data, function (i, val) {
                switch (i) {
                    case 'formbuilder':
                        jQuery('.buddyforms_forms_builder').replaceWith(val);
                        bf_update_list_item_number();
                        break;
                    case 'mail_notification':

                        // console.log(val);

                        jQuery('.buddyforms_accordion_notification').html(val);
                        jQuery('#no-trigger-mailcontainer').hide();

                        tinymce.execCommand('mceRemoveEditor', false, 'bf_mail_body' + val['trigger_id']);
                        tinymce.execCommand('mceAddEditor', false, 'bf_mail_body' + val['trigger_id']);

                        bf_update_list_item_number_mail();

                        break;
                    case 'form_setup':
                        jQuery.each(val, function (i2, form_setup) {
                            if (form_setup instanceof Object) {
                                jQuery.each(form_setup, function (form_setup_key, form_setup_option) {

                                    if (form_setup_option instanceof Object) {
                                        jQuery.each(form_setup_option, function (form_setup_key2, form_setup_option2) {
                                            if (form_setup instanceof Array) {
                                                jQuery('[name="buddyforms_options[' + i2 + '][' + form_setup_key + '][' + form_setup_key2 + ']"]').val(form_setup_option2).trigger('change');
                                            } else {
                                                jQuery('[name="buddyforms_options[' + i2 + '][' + form_setup_key + '][' + form_setup_key2 + ']"]').val(form_setup_option2).trigger('change');
                                            }
                                        });
                                    } else {
                                        if (form_setup instanceof Array) {
                                            jQuery('[name="buddyforms_options[' + i2 + '][' + form_setup_key + ']"]').val(form_setup_option).trigger('change');
                                        } else {
                                            jQuery('[name="buddyforms_options[' + i2 + '][' + form_setup_key + ']"]').val(form_setup_option).trigger('change');
                                        }
                                    }
                                });
                            }

                            if (form_setup instanceof Array) {
                                jQuery('[name="buddyforms_options[' + i2 + '][]"]').val(form_setup).change();
                            } else {
                                jQuery('[name="buddyforms_options[' + i2 + ']"]').val(form_setup).change();
                            }
                            jQuery('.bf-select2').select2();

                        });
                        break;
                    default:
                        bf_alert(val);
                }

            });
            tb_remove();

        },
        error: function () {
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
}

//
// Lets do some stuff after the document is loaded
//
jQuery(document).ready(function (jQuery) {

    var post = jQuery('#post');

    jQuery('#wpbody-content').html('<div class="wrap"></div>');

    jQuery('#wpbody-content .wrap').html(post);

    jQuery(window).scrollTop(0);

    // Hide all post box metaboxes except the buddyforms meta boxes
    jQuery('div .postbox').not('.buddyforms-metabox').hide();

    // Show the submit metabox
    jQuery('#submitdiv').show();
    jQuery('#post').removeClass('hidden');

    // Add Select2 Support
    jQuery(".bf-select2").select2({
        placeholder: "Select an option"
    });

    // Prevent form submission if enter key is pressed on text fields
    jQuery(document).on('keyup keypress', 'form input[type="text"]', function (e) {
        if (e.which == 13) {
            e.preventDefault();
            return false;
        }
    });

    jQuery(document.body).on('click', '.bf-preview', function () {

        var key = jQuery(this).attr('data-key');
        var src = jQuery(this).attr('data-src');

        jQuery('#iframe-' + key).attr('src', src);
        // jQuery('#iframe-' + key).attr('width', 750);
        // jQuery('#iframe-' + key).attr('height', 600);

    });

    // Mail Notifications from email display only if selected
    jQuery(document.body).on('change', '.bf_mail_from_name_multi_checkbox input', function () {

        var val = jQuery(this).val();

        if (val === 'custom') {
            jQuery('.mail_from_name_custom').removeClass('hidden');
        } else {
            jQuery('.mail_from_name_custom').addClass('hidden');
        }

    });

    // Mail Notifications from email display only if selected
    jQuery(document.body).on('change', '.bf_mail_from_multi_checkbox input', function () {

        var val = jQuery(this).val();

        if (val === 'custom') {
            jQuery('.mail_from_custom').removeClass('hidden');
        } else {
            jQuery('.mail_from_custom').addClass('hidden');
        }

    });

    // Mail Notifications sent to display only if selected
    jQuery(document.body).on('change', '.bf_sent_mail_to_multi_checkbox input', function () {

        var val = jQuery(this).val();

        if (jQuery(this).is(':checked')) {
            jQuery('.mail_to_' + val + '_address').removeClass('hidden');
        } else {
            jQuery('.mail_to_' + val + '_address').addClass('hidden');
        }

    });

    // Validate the form before publish
    jQuery('#publish').click(function () {

        var create_new_form_name = jQuery('[name="post_title"]').val();

        var error = false;
        if (create_new_form_name === '') {
            jQuery('[name="post_title"]').removeClass('bf-ok');
            jQuery('[name="post_title"]').addClass('bf-error');
            error = true;
        } else {
            jQuery('[name="post_title"]').removeClass('bf-error');
            jQuery('[name="post_title"]').addClass('bf-ok');
        }

        // traverse all the required elements looking for an empty one
        jQuery("#post input[required]").each(function () {

            // if the value is empty, that means that is invalid
            if (jQuery(this).val() == "") {

                // hide the currently open accordion and open the one with the required field
                jQuery(".accordion-body.collapse.in").removeClass("in");
                jQuery(this).closest(".accordion-body.collapse").addClass("in").css("height", "auto");
                jQuery('#buddyforms_form_setup').removeClass('closed');
                jQuery('#buddyforms_form_elements').removeClass('closed');

                jQuery("html, body").animate({scrollTop: jQuery(this).offset().top - 250}, 1000);

                // stop scrolling through the required elements
                return false;
            }
        });


        if (error === true) {
            return false;
        }

    });


    //
    // Remove form element form the form builder
    //
    jQuery(document).on('click', '.bf_delete_field', function () {

        var del_id = jQuery(this).attr('id');

        if (confirm('Delete Permanently'))
            jQuery("#field_" + del_id).remove();

        return false;
    });

    //
    // Delete mail notification trigger
    //
    jQuery(document).on('click', '.bf_delete_trigger', function () {
        var del_id = jQuery(this).attr('id');
        if (confirm('Delete Permanently')) {
            jQuery("#trigger" + del_id).remove();
            jQuery(".trigger" + del_id).remove();
        }
        return false;
    });

    //
    // Add new options to select, checkbox form element. The js will ad one more line for value and label
    //
    jQuery(document).on('click', '.bf_add_input', function () {

        var action = jQuery(this);
        var args = action.attr('href').split("/");
        var numItems = jQuery('#table_row_' + args[0] + '_select_options ul li').size();

        numItems = numItems + 1;
        jQuery('#table_row_' + args[0] + '_select_options ul').append(
            '<li class="field_item field_item_' + args[0] + '_' + numItems + '">' +
            '<table class="wp-list-table widefat fixed posts"><tbody><tr><td>' +
            '<input class="field-sortable" required="required" type="text" name="buddyforms_options[form_fields][' + args[0] + '][options][' + numItems + '][label]">' +
            '</td><td>' +
            '<input class="field-sortable" required="required" type="text" name="buddyforms_options[form_fields][' + args[0] + '][options][' + numItems + '][value]">' +
            '</td><td class="manage-column column-default">' +
            'You need to Save the Form before you can set this option as default' +
            '</td><td class="manage-column column-author">' +
            '<a href="#" id="' + args[0] + '_' + numItems + '" class="bf_delete_input">Delete</a>' +
            '</td></tr></li></tbody></table>');
        return false;

    });

    //
    // Remove an option from a select or checkbox
    //
    jQuery(document).on('click', '.bf_delete_input', function () {
        var del_id = jQuery(this).attr('id');
        if (confirm('Delete Permanently'))
            jQuery(".field_item_" + del_id).remove();
        return false;
    });


    bf_update_list_item_number();

    jQuery(document).on('mousedown', '.bf_list_item', function () {
        itemList = jQuery(this).closest('.sortable').sortable({
            update: function (event, ui) {
                bf_update_list_item_number();
            }
        });
    });

    bf_update_list_item_number_mail();

    jQuery('#mail_notification_add_new').live('click', function () {
        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            url: ajaxurl,
            data: {"action": "buddyforms_new_mail_notification"},
            success: function (data) {

                //console.log(data);

                jQuery('#no-trigger-mailcontainer').hide();
                jQuery('#mailcontainer').append(data['html']);

                tinymce.execCommand('mceRemoveEditor', false, 'bf_mail_body' + data['trigger_id']);
                tinymce.execCommand('mceAddEditor', false, 'bf_mail_body' + data['trigger_id']);

                bf_update_list_item_number_mail();

            }
        });
        return false;
    });

    //
    // Add new mail notification
    //
    jQuery('#post_status_mail_notification_add_new').live('click', function () {

        var error = false;
        var trigger = jQuery('.post_status_mail_notification_trigger select').val();

        if (!trigger) {
            return false;
        }

        if (trigger == 'none') {
            bf_alert('You have to select a trigger first.');
            return false;
        }

        // traverse all the required elements looking for an empty one
        jQuery("#post-status-mail-container li.bf_trigger_list_item").each(function () {
            if (jQuery(this).attr('id') == 'trigger' + trigger) {
                bf_alert('Trigger already exists');
                error = true;
            }
        });

        if (error == true)
            return false;

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {"action": "buddyforms_new_post_status_mail_notification", "trigger": trigger},
            success: function (data) {

                if (data == 0) {
                    bf_alert('trigger already exists');
                    return false;
                }
                jQuery('#no-trigger-post-status-mail-container').hide();
                jQuery('#post-status-mail-container').append(data);

                tinymce.execCommand('mceRemoveEditor', false, 'bf_mail_body');
                tinymce.execCommand('mceAddEditor', false, 'bf_mail_body');

                bf_update_list_item_number_mail();

            }
        });
        return false;
    });

    //
    // Permissions Section - select all roles and caps
    //
    jQuery(document).on('click', '.bf_check_all', function (e) {

        if (jQuery(".bf_permissions input[type='checkbox']").prop("checked")) {
            jQuery('.bf_permissions :checkbox').prop('checked', false);
            jQuery(this).text(admin_text.check);
        } else {
            jQuery('.bf_permissions :checkbox').prop('checked', true);
            jQuery(this).text(admin_text.uncheck);
        }
        e.preventDefault();
    });

    jQuery(document).on('click', '.bf_check', function (e) {

        if (jQuery(".bf_permissions input[type='checkbox']").prop("checked")) {
            jQuery(this).text(admin_text.check);
        } else {
            jQuery(this).text(admin_text.uncheck);
        }
        e.preventDefault();
    });


    jQuery('.bf_check').trigger('click');
    //
    // #bf-create-page-modal
    //
    jQuery('#bf_create_page_modal').live('click', function () {

        var dialog = jQuery('<div></div>').dialog({
            modal: true,
            title: "Info",
            open: function () {
                var markup = 'Name your Page' +
                    '<input id="bf_create_page_name" type="text" value="">';
                jQuery(this).html(markup);
            },
            buttons: {
                'Add': function () {

                    var page_name = jQuery('#bf_create_page_name').val();
                    dialog.html('<span class="spinner is-active"></span>');

                    jQuery.ajax({
                        type: 'POST',
                        dataType: "json",
                        url: ajaxurl,
                        data: {
                            "action": "buddyforms_new_page",
                            "page_name": page_name
                        },
                        success: function (data) {
                            if (data['error']) {
                                console.log(data['error']);
                            } else {
                                jQuery('#attached_page').append(jQuery('<option>', {
                                    value: data['id'],
                                    text: data['name']
                                }));
                                jQuery('#attached_page').val(data['id']);
                            }
                            dialog.dialog("close");
                        },
                        error: function () {
                            dialog.dialog("close");
                        }
                    });

                }
            }
        });

        return false;

    });

    //
    // At last let as remove elements added by other plugins we could not remove with the default functions.
    //

    // Remove all Visual Composer elements form BuddyForms View
    jQuery('*[class^="vc_"]').remove();

});