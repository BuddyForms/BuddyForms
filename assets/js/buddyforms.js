function bf_form_errors() {

    jQuery('input').removeClass('error');

    var errors = jQuery('.bf-alert-wrap ul li span');
    jQuery.each(errors, function (i, error) {
        var field_id = jQuery(error).attr('data-field-id');
        // console.log(field_id);
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

    jQuery(".bf-select2").each(function(){
        var reset = jQuery(this).attr('data-reset');
        var options = {
            placeholder: "Select an option",
            tags: true,
            tokenSeparators: [',', ' ']
        };
        if(reset){
            options['allowClear'] = true;
        }
        jQuery(this).select2(options);
    });

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

// Password Reset Start

function checkPasswordStrength( $pass1,
                                $pass2,
                                $strengthResult,
                                $submitButton,
                                blacklistArray ) {
    var pass1 = $pass1.val();
    var pass2 = $pass2.val();

    // Reset the form & meter
    $submitButton.attr( 'disabled', 'disabled' );
    $strengthResult.removeClass( 'short bad good strong' );

    // Extend our blacklist array with those from the inputs & site data
    blacklistArray = blacklistArray.concat( wp.passwordStrength.userInputBlacklist() )

    // Get the password strength
    var strength = wp.passwordStrength.meter( pass1, blacklistArray, pass2 );

    var hint_html = '<p><small class="buddyforms-password-hint">' + pwsL10n.hint_text + '</small></p>';

    // Add the strength meter results
    console.log('strength ' + strength + 'required_strength ' + pwsL10n.required_strength);
    $('.buddyforms-password-hint').remove();


    switch ( strength ) {
        case 0: case 1:
            $strengthResult.addClass( 'short' ).html( pwsL10n.short );
            break;
        case 2:
            $strengthResult.addClass( 'bad' ).html( pwsL10n.bad );
            break;

        case 3:
            $strengthResult.addClass( 'good' ).html( pwsL10n.good );
            break;

        case 4:
            $strengthResult.addClass( 'strong' ).html( pwsL10n.strong );
            break;

        case 5:
            $strengthResult.addClass( 'short' ).html( pwsL10n.mismatch );
            break;

        default:
            $strengthResult.addClass( 'short' ).html( pwsL10n.short );

    }

    // The meter function returns a result even if pass2 is empty,
    // enable only the submit button if the password is strong and
    // both passwords are filled up

    if ( pwsL10n.required_strength <= strength && strength != 5 && '' !== pass2.trim() ) {
        $('.buddyforms-password-hint').remove();
        $submitButton.removeAttr( 'disabled' );
    } else {
        $strengthResult.after( hint_html );
    }

    return strength;
}

jQuery( document ).ready( function( $ ) {
    // Binding to trigger checkPasswordStrength
    $( 'body' ).on( 'keyup', 'input[name=buddyforms_user_pass], input[name=buddyforms_user_pass_confirm]',
        function( event ) {
            checkPasswordStrength(
                $('input[name=buddyforms_user_pass]'),         // First password field
                $('input[name=buddyforms_user_pass_confirm]'), // Second password field
                $('#password-strength'),           // Strength meter
                $('input[type=submit]'),           // Submit button
                ['black', 'listed', 'word']        // Blacklisted words
            );
        }
    );

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


// Password Reset Ends


// Special password redirects after registration
// If a redirect url is added to the register page url we use this redirect and add it as hidden field to the form
jQuery(document).ready(function (jQuery) {
    var getUrlParameter = function bf_getUrlParameter(sParam) {
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
    var redirect = getUrlParameter('redirect_url');

    if(redirect){
        jQuery('#submitted').append('<input type="hidden" name="bf_pw_redirect_url" value="'+ redirect +'" />');
    }

});
