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
// Update ths list number 1,2,3,...
function bf_update_list_item_number() {
    jQuery(".buddyforms_forms_builder ul").each(function () {
        jQuery(this).children("li").each(function (t) {
            jQuery(this).find("td.field_order .circle").first().html(t + 1)
        })
    })
}
// Helper Function to use dialog instead of alert
function bf_alert(alert_message){
    jQuery('<div></div>').dialog({
        modal: true,
        title: "Info",
        open: function() {
            jQuery(this).html(alert_message);
        },
        buttons: {
            Ok: function() {
                jQuery( this ).dialog( "close" );
            }
        }
    });
}
jQuery(document).ready(function (jQuery) {

    //
    // No more bootstrap! @todo There is some bootstrap left in the form builder admin for the tabs and accordion. Its time for a rewrite
    //
    //    jQuery(".tabs, .tabs-left, .tabbable").tabs();
    //    jQuery("#sortable_buddyforms_elements .sortable").accordion({
    //            collapsible: true,
    //            animated: 'slide',
    //            autoHeight: false,
    //            navigation: true
    //    });
    // open content that matches the hash
    //    var hash = window.location.hash;
    //    var thash = hash.substring(hash.lastIndexOf('#'), hash.length);
    //    jQuery('#sortable_buddyforms_elements').find('a[href*='+ thash + ']').closest('h3').trigger('click');

    //
    // This is comment out...  I'm not sure if I should save the latest settings tab...
    //
    //jQuery('#buddyforms_formbuilder_settings a').click(function(e) {
    //    e.preventDefault();
    //    jQuery(this).tab('show');
    //});
    //
    //// store the currently selected tab in the hash value
    //jQuery("#buddyforms_formbuilder_settings ul.nav-tabs > li > a").on("shown.bs.tab", function(e) {
    //    var id = jQuery(e.target).attr("href").substr(1);
    //    window.location.hash = id;
    //});
    //
    //// on load of the page: switch to the currently selected tab
    //var hash = window.location.hash;
    //jQuery('#buddyforms_formbuilder_settings a[href="' + hash + '"]').tab('show');




    // Hide all postbox metabocen exeped the buddyforms etaboxes
    jQuery('div .postbox').not('.buddyforms-metabox').hide();

    // Show the submit metabox
    jQuery('#submitdiv').show();
    jQuery('#post').removeClass('hidden');


    //
    // / Add Select2 Support
    //
    jQuery(".bf-select2").select2({
        placeholder: "Select an option"
    });


    // Mail Notifications sent to display only if selected
    jQuery(document.body).on('change', '.bf_sent_mail_to_multi_checkbox', function () {

        var input = jQuery(this).find("input");
        var id = input.attr('id');

        if(input.val() === 'admin')
            return;

        if (jQuery(input).is(':checked')) {

            jQuery('.' + id).removeClass('hidden');
            jQuery('.' + id + ' td .checkbox label').removeClass('hidden');
            jQuery('.' + id + ' td .' + id).removeClass('hidden');

        } else {

            jQuery('.' + id).addClass('hidden');

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
            '<input class="field-sortable" type="text" name="buddyforms_options[form_fields][' + args[0] + '][options][' + numItems + '][label]">' +
            '</td><td>' +
            '<input class="field-sortable" type="text" name="buddyforms_options[form_fields][' + args[0] + '][options][' + numItems + '][value]">' +
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

    // Update ths list number 1,2,3,... for the mail trigger
    function bf_update_list_item_number_mail() {

        jQuery("#mailcontainer .bf_trigger_list_item").each(function (t) {
            jQuery(this).find("td.field_order .circle").first().html(t + 1)
        })
    }

    bf_update_list_item_number_mail();

    jQuery('#mail_notification_add_new').live('click', function() {
        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            url: ajaxurl,
            data: {"action": "buddyforms_new_mail_notification"},
            success: function (data) {

                console.log(data);

                jQuery('#no-trigger-mailcontainer').hide();
                jQuery('#mailcontainer').append(data['html']);

                tinymce.execCommand( 'mceRemoveEditor', false, 'bf_mail_body' + data['trigger_id'] );
                tinymce.execCommand( 'mceAddEditor', false, 'bf_mail_body' + data['trigger_id'] );

                bf_update_list_item_number_mail();

            }
        });
        return false;
    });

    //
    // Add new mail notification
    //
    jQuery('#post_status_mail_notification_add_new').click(function (e) {

        var error = false;
        var trigger = jQuery('.post_status_mail_notification_trigger').val();

        if(!trigger){
            trigger = 'Mail_Notification'
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
        })

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

                tinymce.execCommand( 'mceRemoveEditor', false, 'bf_mail_body' );
                tinymce.execCommand( 'mceAddEditor', false, 'bf_mail_body' );

                bf_update_list_item_number_mail();

            }
        });
        return false;
    });

    //
    // Permissions Section - select all roles and caps
    //
    jQuery(".bf_check_all").click(function (e) {

        if (jQuery(".bf_permissions input[type='checkbox']").prop("checked")) {
            jQuery('.bf_permissions :checkbox').prop('checked', false);
            jQuery(this).text(admin_text.check);
        } else {
            jQuery('.bf_permissions :checkbox').prop('checked', true);
            jQuery(this).text(admin_text.uncheck);
        }
        e.preventDefault();
    });

});
