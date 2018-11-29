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
// Generate a custom string to append to the field slug in case of duplicate
//
function buddyformsMakeFieldId() {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < 5; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

//
// Validate an email using regex
//
function buddyformsIsEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

//
// Validate multiples email address separated by coma
//
function buddyformsValidateMultiEmail(string) {
    var result = true;
    if (string) {
        var isMulti = /[;,]+/.test(string);
        if (isMulti) {
            var values = string.split(/[;,]+/);
            jQuery.each(values, function (index, email) {
                result = buddyformsIsEmail(email.trim());
                if (!result) {
                    return result;
                }
            });
        } else {
            result = buddyformsIsEmail(string);
            if (!result) {
                return result;
            }
        }
    } else {
        result = false;
    }

    return result;
}
//
// Validate notification email element
//
function buddyforms_validate_notifications_email(element){
    if (element) {
        var value = jQuery(element).val();
        if (value) {
            var isValid = buddyformsValidateMultiEmail(jQuery(element).val());
            if(!isValid){
                jQuery(element)[0].setCustomValidity('Invalid Email(s)');
                jQuery(element).addClass('bf-error');
            } else {
                jQuery(element)[0].setCustomValidity('');
                jQuery(element).removeClass('bf-error');
            }
            return isValid;
        } else {
            jQuery(element)[0].setCustomValidity('');
            jQuery(element).removeClass('bf-error');
        }
    }
    return true;
}

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
    var postTitle = jQuery('input#title');
    if (!postTitle.val()) {
        postTitle.val(buddyformsMakeFieldId());
    }
    jQuery.ajax({
        type: 'POST',
        dataType: "json",
        url: ajaxurl,
        data: {
            "action": "buddyforms_form_template",
            "template": template,
            "title": postTitle.val()
        },
        success: function (data) {
            jQuery.each(data, function (i, val) {
                switch (i) {
                    case 'formbuilder':
                    	var form_builder = jQuery('.buddyforms_forms_builder');
	                    form_builder.replaceWith(val);
                        bf_update_list_item_number();
                        jQuery(document.body).trigger({type: "buddyform:load_fields"});
                        break;
                    case 'mail_notification':
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
	                                var element;
                                    if (form_setup_option instanceof Object) {
                                        jQuery.each(form_setup_option, function (form_setup_key2, form_setup_option2) {
                                             element = jQuery('[name="buddyforms_options[' + i2 + '][' + form_setup_key + '][' + form_setup_key2 + ']"]');
                                             buddyform_apply_template_to_element(element, form_setup_option2);
                                        });
                                    } else {
	                                    element = jQuery('[name="buddyforms_options[' + i2 + '][' + form_setup_key + ']"]');
	                                    buddyform_apply_template_to_element(element, form_setup_option);
                                    }
                                });
                            }

                            if (form_setup instanceof Array) {
	                            buddyform_apply_template_to_element(jQuery('[name="buddyforms_options[' + i2 + '][]"]'), form_setup);
                            } else {
	                            buddyform_apply_template_to_element(jQuery('[name="buddyforms_options[' + i2 + ']"]'), form_setup);
                            }
                            jQuery('.bf-select2').select2();
                            // Check the form type and only display the relevant form setup tabs
                            from_setup_form_type(jQuery('#bf-form-type-select').val());
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

/**
 *
 * @param element
 * @param value
 */
function buddyform_apply_template_to_element(element, value){
	if(element.length === 1){
         element.val(value).trigger('change');
	} else {
		jQuery.each(element, function () {
			var current = jQuery(this);
			var current_val = current.val();
			current.prop( "checked", (current_val === value) );
		});
	}
}

//
// Process the form errors and scroll to it
//
function buddyforms_process_errors(errors) {
    if (errors.length > 0) {
        jQuery.each(errors, function (index, current_error) {
            if (!current_error.isValid) {
                var type = current_error.type || 'accordion';
                switch (type) {
                    case 'accordion': {
                        //close all
                        var sortableBuddyformsElements = jQuery("#sortable_buddyforms_elements");
                        sortableBuddyformsElements.accordion({
                            active: false
                        });
                        //Find the parent, the element id and expand it
                        jQuery(current_error.element).closest(".accordion-body.ui-accordion-content.collapse").addClass("ui-accordion-content-active").css("height", "auto");
                        var li_id = jQuery(current_error.element).closest('li.bf_list_item');
                        var li_position = jQuery('#sortable_buddyforms_elements li.bf_list_item').index(jQuery(li_id));
                        sortableBuddyformsElements.accordion({
                            active: li_position
                        });
                        jQuery('#buddyforms_form_setup').removeClass('closed');
                        jQuery('#buddyforms_form_elements').removeClass('closed');
                        break;
                    }
                    case 'settings': {
                        if (!jQuery(current_error.element).is(':visible')) {
                            var currentId = jQuery(current_error.element).closest('div.tab-pane.ui-widget-content.ui-corner-bottom').attr('id');
                            jQuery('.buddyform-nav-tabs li[aria-controls="' + currentId + '"]>a').click()
                        }
                        break;
                    }
                }
                var element_name = jQuery(current_error.element).attr('name');
                jQuery("html, body").animate({scrollTop: jQuery('[name="' + element_name + '"]').offset().top - 250}, 1000);
                return false;
            }
        });
    }
    return true;
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
            jQuery(this).closest('.wp-list-table').find('.mail_from_name_custom').removeClass('hidden');
        } else {
            jQuery(this).closest('.wp-list-table').find('.mail_from_name_custom').addClass('hidden');
        }

    });

    // Mail Notifications from email display only if selected
    jQuery(document.body).on('change', '.bf_mail_from_multi_checkbox input', function () {

        var val = jQuery(this).val();

        if (val === 'custom') {
            jQuery(this).closest('.wp-list-table').find('.mail_from_custom').removeClass('hidden');
        } else {
            jQuery(this).closest('.wp-list-table').find('.mail_from_custom').addClass('hidden');
        }

    });

    // Mail Notifications sent to display only if selected
    jQuery(document.body).on('change', '.bf_sent_mail_to_multi_checkbox input', function () {

        var val = jQuery(this).val();

        if (jQuery(this).is(':checked')) {
            jQuery(this).closest('.wp-list-table').find('.mail_to_' + val + '_address')
                .removeClass('hidden')
                .prop('required', true);
        } else {
            jQuery(this).closest('.wp-list-table').find('.mail_to_' + val + '_address')
                .addClass('hidden')
                .prop('required', false);
        }

    });

    // Validate the form before publish
    jQuery('#publish').click(function () {

        var post_title = jQuery('[name="post_title"]');
        var errors = [];

        if (post_title.val() === '') {
            post_title.removeClass('bf-ok');
            post_title.addClass('bf-error');
            errors.push({isValid: false, element: post_title, type: 'title'});
        } else {
            post_title.removeClass('bf-error');
            post_title.addClass('bf-ok');
        }

        //Validate emails notifications
        var mail_to_cc_addresses = jQuery('input[name^="buddyforms_options[mail_submissions]"][name$="[mail_to_cc_address]"]');
        jQuery.each(mail_to_cc_addresses, function (index, mail_to_cc_address) {
            var result = buddyforms_validate_notifications_email(mail_to_cc_address);
            errors.push({isValid: result, element: mail_to_cc_address, type: 'settings'});
        });

        var mail_to_bcc_addresses = jQuery('input[name^="buddyforms_options[mail_submissions]"][name$="[mail_to_bcc_address]"]');
        jQuery.each(mail_to_bcc_addresses, function (index, mail_to_bcc_address) {
            var result = buddyforms_validate_notifications_email(mail_to_bcc_address);
            errors.push({isValid: result, element: mail_to_bcc_address, type: 'settings'});
        });

        var mail_to_addresses = jQuery('input[name^="buddyforms_options[mail_submissions]"][name$="[mail_to_address]"]');
        jQuery.each(mail_to_addresses, function (index, mail_to_address) {
            var result = buddyforms_validate_notifications_email(mail_to_address);
            errors.push({isValid: result, element: mail_to_address, type: 'settings'});
        });

        //Fill and avoid duplicates of field slugs
        var findFieldsSlugs = jQuery("#post input[name^='buddyforms_options[form_fields]'][name$='[slug]'][type!='hidden']");
        findFieldsSlugs.each(function () {
            var fieldSlugs = jQuery(this);
            findFieldsSlugs.each(function () {
                if (jQuery(this).val() === fieldSlugs.val() && fieldSlugs.attr('name') !== jQuery(this).attr('name')) {
                    fieldSlugs.val(fieldSlugs.val() + '_' + buddyformsMakeFieldId());
                    return false;
                }
            });
        });

        // traverse all the required elements looking for an empty one
        jQuery("#post input[required]").each(function () {
            // if the value is empty, that means that is invalid
            var isValid = (jQuery(this).val() != "");
            errors.push({isValid: isValid, element: jQuery(this)[0], type: 'accordion'});
            if (isValid) {
                jQuery(this).removeClass("bf-error");
            } else {
                jQuery(this).addClass("bf-error");
                return false;
            }
        });

        return buddyforms_process_errors(errors);

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
    jQuery(document).on('click', '.bf_add_gdpr', function () {


        var action = jQuery( this );
        var gdpr_type = jQuery( this ).attr( 'data-gdpr-type' );

        var numItems = jQuery('#table_row_' + gdpr_type + '_select_options ul li').size();

        var type = jQuery('#gdpr_option_type').val();

        var message = '';
        if(admin_text[type]){
            message = admin_text[type]
        }

        var error_message = '';
        if(admin_text['error_message']){
            error_message = admin_text['error_message']
        }

        numItems = numItems + 1;
        jQuery('#table_row_' + gdpr_type + '_select_options ul').append(
            '<li class="field_item field_item_' + gdpr_type + '_' + numItems + '">' +
            '<table class="wp-list-table widefat posts striped"><tbody><tr><td>' +
            '<textarea rows="5" name="buddyforms_options[form_fields][' + gdpr_type + '][options][' + numItems + '][label]" cols="50">' + message + '</textarea>' +
            '<textarea rows="2" name="buddyforms_options[form_fields][' + gdpr_type + '][options][' + numItems + '][error_message]" cols="50">' + error_message + '</textarea>' +
            '</td><td class="manage-column column-author">' +
            '<div class="checkbox">' +
            '   <label class="">' +
            '       <input type="checkbox" name="buddyforms_options[form_fields][' + gdpr_type + '][options][' + numItems + '][checked][]" value="checked"><span> Checked</span>' +
            '   </label>' +
            '</div>' +
            '<div class="checkbox">' +
            '   <label class="">' +
            '       <input type="checkbox" name="buddyforms_options[form_fields][' + gdpr_type + '][options][' + numItems + '][required][]" value="required"><span> Required</span>' +
            '   </label>' +
            '</div>' +
            '</td><td class="manage-column column-author">' +
            '<a href="#" id="' + gdpr_type + '_' + numItems + '" class="bf_delete_input">Delete</a>' +
            '</td></tr></li></tbody></table><hr>');
        return false;

    });

    //
    // Add new options to gdpr, checkbox form element. The js will add one more line for value and label
    //
    jQuery(document).on('click', '.bf_add_input', function () {


        var action = jQuery(this);
        var args = action.attr('href').split("/");
        var numItems = jQuery('#table_row_' + args[0] + '_select_options ul li').size();

        alert('#table_row_' + args[0] + '_select_options ul li');

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

    jQuery('#mail_notification_add_new').on('click', function () {
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
    jQuery('#post_status_mail_notification_add_new').on('click', function () {

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
    jQuery('#bf_create_page_modal').on('click', function () {

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
