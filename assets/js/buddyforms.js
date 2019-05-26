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

/**
 * Get a field if it exist into the form, searching by type(default) or a provided key
 *
 * @since 2.4.0
 *
 * @param formSlug
 * @param search
 * @param fieldTargetKey
 * @returns {boolean|object}
 */
function getFieldDataBy(formSlug, search, fieldTargetKey) {
    if (buddyformsGlobal && buddyformsGlobal[formSlug]) {
        fieldTargetKey = fieldTargetKey || 'type';
        var fields = buddyformsGlobal[formSlug].form_fields;
        var result = jQuery.map(fields, function (element, key) {
            if (element[fieldTargetKey] === search) {
                element.id = key;
                return element;
            }
        });
        if (result && result[0]) {
            return result[0];
        }
    }
    return false;
}

/**
 * Helper function to get $_GET parameter
 */
function bf_getUrlParameter(sParam) {
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
}

function BuddyForms() {
    var submissionModalContent;
    var submissionModal;

    /**
     * Reset option for multiple choice fields radio and checkboxes
     */
    function resetInputMultiplesChoices(event) {
        event.preventDefault();
        var group_name = jQuery(this).attr('data-group-name');
        jQuery('input[name="' + group_name + '"]').attr('checked', false);
        return false;
    }

    /**
     * Special password redirects after registration
     * If a redirect url is added to the register page url we use this redirect and add it as hidden field to the form
     *
     * @param redirect
     */
    function specialPasswordRedirectAfterRegistration(redirect) {
        if (redirect) {
            jQuery('#submitted').append('<input type="hidden" name="bf_pw_redirect_url" value="' + redirect + '" />');
        }
    }

    /**
     * Binding to trigger checkPasswordStrength
     */
    function checkPasswordStrength() {
        if (buddyformsGlobal) {
            var pass1 = jQuery('input[name=buddyforms_user_pass]').val();
            var pass2 = jQuery('input[name=buddyforms_user_pass_confirm]').val();
            var strengthResult = jQuery('#password-strength');
            var blacklistArray = ['black', 'listed', 'word'];
            var passwordHint = jQuery('.buddyforms-password-hint');

            // Reset the form & meter
            jQuery(document.body).trigger({type: "buddyforms:submit:disable"});
            strengthResult.removeClass('short bad good strong');

            // Extend our blacklist array with those from the inputs & site data
            blacklistArray = blacklistArray.concat(wp.passwordStrength.userInputBlacklist())

            // Get the password strength
            var strength = wp.passwordStrength.meter(pass1, blacklistArray, pass2);

            var hint_html = '<p><small class="buddyforms-password-hint">' + buddyformsGlobal.pwsL10n.hint_text + '</small></p>';

            // Add the strength meter results
            console.log('strength ' + strength + 'required_strength ' + buddyformsGlobal.pwsL10n.required_strength);
            passwordHint.remove();

            switch (strength) {
                case 0:
                case 1:
                    strengthResult.addClass('short').html(buddyformsGlobal.pwsL10n.short);
                    break;
                case 2:
                    strengthResult.addClass('bad').html(buddyformsGlobal.pwsL10n.bad);
                    break;

                case 3:
                    strengthResult.addClass('good').html(buddyformsGlobal.pwsL10n.good);
                    break;

                case 4:
                    strengthResult.addClass('strong').html(buddyformsGlobal.pwsL10n.strong);
                    break;

                case 5:
                    strengthResult.addClass('short').html(buddyformsGlobal.pwsL10n.mismatch);
                    break;

                default:
                    strengthResult.addClass('short').html(buddyformsGlobal.pwsL10n.short);

            }

            // The meter function returns a result even if pass2 is empty,
            // enable only the submit button if the password is strong and
            // both passwords are filled up

            if (buddyformsGlobal.pwsL10n.required_strength <= strength && strength !== 5 && '' !== pass2.trim()) {
                passwordHint.remove();
                jQuery(document.body).trigger({type: "buddyforms:submit:enable"});
            } else {
                strengthResult.after(hint_html);
            }

            return strength;
        }
        return '';
    }

    function bf_delete_post() {
        var post_id = jQuery(this).attr('id');

        if (confirm('Delete Permanently')) {// todo need il18n
            jQuery.ajax({
                type: 'POST',
                url: buddyformsGlobal.admin_url,
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
                error: function (request) {
                    alert(request.responseText);
                }
            });
        } else {
            return false;
        }
        return false;
    }

    function openSubmissionModal() {
        var bf_submission_modal_content = fncBuddyForms.getSubmissionModalContent();
        if (bf_submission_modal_content && bf_submission_modal_content.length > 0) {
            var targetId = jQuery(this).attr('data-id');
            var target = jQuery("#bf-submission-modal_" + targetId);
            fncBuddyForms.submissionModal(target);
            jQuery('.buddyforms-posts-container').html(target);
            jQuery("#bf-submission-modal_" + targetId + " :input").attr("disabled", true);
            target.show();
        }
        return false;
    }

    function closeSubmissionModal() {
        var bf_submission_modal_content = fncBuddyForms.getSubmissionModalContent();
        if (bf_submission_modal_content && bf_submission_modal_content.length > 0) {
            var targetId = jQuery(this).attr('data-id');
            var submissionTarget = fncBuddyForms.getSubmissionModal();
            bf_submission_modal_content.find('.bf_posts_' + targetId).prepend(submissionTarget);
            jQuery('.buddyforms-posts-container').html(bf_submission_modal_content);
            jQuery("#bf-submission-modal_" + targetId).hide();
        }
        return false;
    }

    function createTaxItem() {
        var field_id = jQuery(this).attr('data-field_id');
        var field_slug = jQuery(this).attr('data-field_slug');
        var target = jQuery("#" + field_slug + "_create_new_tax_" + field_id);
        var newStateVal = target.val();

        // Set the value, creating a new option if necessary
        var createItemElement = jQuery("#category_create_new_tax");
        if (createItemElement && createItemElement.find("option[value='" + newStateVal + "']").length) {
            createItemElement.val(newStateVal).trigger("change");
        } else {
            // Create the DOM option that is pre-selected by default
            var newState = new Option(newStateVal, newStateVal, true, true);
            // Append it to the select
            jQuery("#" + field_id).append(newState).trigger('change');
            // CLear the text field
            target.val('');
        }
        return false;
    }

    /**
     * disable the ACF js navigate away pop up
     */
    function disableACFPopup() {
        if (typeof acf !== 'undefined') {
            acf.unload.active = false;
        }
    }

    function addValidationForUserWebsite() {
        jQuery.validator.addMethod("user-website", function (value, element) {
            var formSlug = getFormSlugFromFormElement(element);
            if (
                formSlug && buddyformsGlobal && buddyformsGlobal[formSlug] && buddyformsGlobal[formSlug].js_validation &&
                buddyformsGlobal[formSlug].js_validation[0] === 'enabled'
            ) {
                return /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/.test(value);
            }
            return true;

        }, "Please enter a valid URL.");// todo need il18n
    }

    function addValidationMinLength() {
        jQuery.validator.addMethod("minlength", function (value, element, param) {
            var formSlug = getFormSlugFromFormElement(element);
            if(
                formSlug && buddyformsGlobal && buddyformsGlobal[formSlug] && buddyformsGlobal[formSlug].js_validation &&
                buddyformsGlobal[formSlug].js_validation[0] === 'enabled'
            ) {
                var count = value.length;
                if (value === "") {
                    return true;
                }
                if (count < param) {
                    jQuery.validator.messages['minlength'] = "The minimum character length is : " + param + ". Please check.";
                    return false;
                }
            }
            return true;
        }, "");
    }

    function addValidationMinValue() {
        jQuery.validator.addMethod("min-value", function (value, element, param) {
            var formSlug = getFormSlugFromFormElement(element);
            if(
                formSlug && buddyformsGlobal && buddyformsGlobal[formSlug] && buddyformsGlobal[formSlug].js_validation &&
                buddyformsGlobal[formSlug].js_validation[0] === 'enabled'
            ) {
                if (value === "") {
                    return true;
                }
                if (value < param) {
                    jQuery.validator.messages['min-value'] = "The minimum value allowed is : " + param + ". Please check.";
                    return false;
                }
            }
            return true;
        }, "");
    }

    function addValidationRequired() {
        jQuery.validator.addMethod("required", function (value, element, param) {
            var formSlug = getFormSlugFromFormElement(element);
            if (
              formSlug && buddyformsGlobal && buddyformsGlobal[formSlug] && buddyformsGlobal[formSlug].js_validation &&
              buddyformsGlobal[formSlug].js_validation[0] === 'enabled'
            ) {
                var fieldSlug = jQuery(element).attr('name');
                var fieldData = getFieldFromSlug(fieldSlug, formSlug);
                if (!fieldData) {//if not field data is not possible to validate it
                    return true;
                }

                var passValidationFieldTypes = ['upload', 'featured_image'];
                var passValidationCallback = function (fieldTypeArray) {
                    passValidationFieldTypes = fieldTypeArray || false;
                };
                jQuery(document.body).trigger({ type: 'buddyforms:validation:pass' }, [value, element, fieldData, formSlug, passValidationFieldTypes, passValidationCallback]);

                if (passValidationFieldTypes && passValidationFieldTypes.length > 0) {
                    var exist = jQuery.inArray(fieldData.type, passValidationFieldTypes);
                    if (exist && exist >= 0) {
                        return true;
                    }
                }

                var result = false;
                var requiredMessage = fieldData.validation_error_message ? fieldData.validation_error_message : 'This field is required.'; //todo need il18n

                switch (fieldData.type) {
                    case 'post_formats':
                        result = value && value !== 'Select a Post Format';
                        break;
                    case 'taxonomy':
                        result = value && value !== "-1";
                        break;
                    default:
                        result = value && value.length > 0;
                }

                var requiredCallback = function (isValid, message) {
                    result = isValid || false;
                    if (message && message.length > 0) {
                        requiredMessage = message;
                    }
                };
                jQuery(document.body).trigger({type: "buddyforms:validation:required"}, [value, element, fieldData, formSlug, requiredCallback]);

                jQuery.validator.messages['required'] = requiredMessage;
                return result;
            }
            return true;
        }, "");
    }

    function addValidationMaxLength() {
        jQuery.validator.addMethod("maxlength", function (value, element, param) {
            var formSlug = getFormSlugFromFormElement(element);
            if(
                formSlug && buddyformsGlobal && buddyformsGlobal[formSlug] && buddyformsGlobal[formSlug].js_validation &&
                buddyformsGlobal[formSlug].js_validation[0] === 'enabled'
            ) {
                if (param === 0 || value === "") {
                    return true;
                }
                var count = value.length;
                if (count > param) {
                    jQuery.validator.messages['maxlength'] = "The maximum character length is : " + param + ". Please check.";
                    return false;
                }
            }
            return true;
        }, "");
    }

    function addValidationMaxValue() {
        jQuery.validator.addMethod("max-value", function (value, element, param) {
            var formSlug = getFormSlugFromFormElement(element);
            if(
                formSlug && buddyformsGlobal && buddyformsGlobal[formSlug] && buddyformsGlobal[formSlug].js_validation &&
                buddyformsGlobal[formSlug].js_validation[0] === 'enabled'
            ) {
                if (param === 0 || value === "") {
                    return true;
                }

                if (value > param) {
                    jQuery.validator.messages['max-value'] = "The maximum value allowed  is : " + param + ". Please check.";
                    return false;
                }
            }
            return true;
        }, "");
    }

    function getFormSlugFromFormElement(element) {
        var formSlug = jQuery(element).data('form');
        if (!formSlug) {
            var form = jQuery(element).closest('form');
            var formId = form.attr('id');
            formSlug = formId.split('buddyforms_form_');
            formSlug = (formSlug[1]) ? formSlug[1] : false;
        }
        return formSlug;
    }

    function getFieldFromSlug(fieldSlug, formSlug) {
        if (fieldSlug && formSlug && buddyformsGlobal && buddyformsGlobal[formSlug] && buddyformsGlobal[formSlug].form_fields) {
            var fieldIdResult = Object.keys(buddyformsGlobal[formSlug].form_fields).filter(function(fieldId){
                fieldSlug = fieldSlug.replace('[]', '');
                return buddyformsGlobal[formSlug].form_fields[fieldId].slug.toLowerCase() === fieldSlug.toLowerCase();
            });
            if(fieldIdResult){
                return buddyformsGlobal[formSlug].form_fields[fieldIdResult];
            }
        }
        return false;
    }

    function enabledGarlic() {
        var bf_garlic = jQuery('.bf-garlic');
        if (bf_garlic.length > 0) {
            bf_garlic.garlic();
        }
    }

    function enabledSelect2() {
        var bf_select_2 = jQuery('.bf-select2');
        if (bf_select_2.length > 0) {
            bf_select_2.each(function () {

                var reset = jQuery(this).attr('data-reset');
                var options = {
                    placeholder: "Select an option", // todo need il18n
                    tags: true,
                    tokenSeparators: [',', ' ']
                };
                if (reset) {
                    options['allowClear'] = true;
                }
                jQuery(this).select2(options);

                jQuery(this).on('change', function () {
                     var formSlug = jQuery(this).data('form');
                     jQuery('form[id="buddyforms_form_'+formSlug+'"]').valid();
                });
            });
        }
    }

    function enabledDateTime() {
        var dateElements = jQuery('.bf_datetimepicker');
        if (dateElements && dateElements.length > 0) {
            jQuery.each(dateElements, function(i, element){
                var currentFieldSlug = jQuery(element).attr('name');
                var formSlug = getFormSlugFromFormElement(element);
                if (currentFieldSlug && formSlug) {
                    var fieldData = getFieldFromSlug(currentFieldSlug, formSlug);
                    var fieldTimeStep = (fieldData.element_time_step) ? fieldData.element_time_step : 60;
                    var fieldSaveFormat = (fieldData.element_save_format) ? fieldData.element_save_format : 'Y/m/d H:i';
                    var fieldDateFormat = (fieldData.element_date_format) ? fieldData.element_date_format : 'Y/m/d';
                    var fieldTimeFormat = (fieldData.element_time_format) ? fieldData.element_time_format : 'H:i';
                    var enableTime = (fieldData.enable_time && fieldData.enable_time[0] && fieldData.enable_time[0] === 'enable_time');
                    var enableDate = (fieldData.enable_date && fieldData.enable_date[0] && fieldData.enable_date[0] === 'enable_date');
                    var isInline = (fieldData.is_inline && fieldData.is_inline[0] && fieldData.is_inline[0] === 'is_inline');
                    if (!enableDate && !enableTime) {
                        enableDate = true;
                    }
                    var dateTimePickerConfig = {
                        format: fieldSaveFormat,
                        formatDate: fieldDateFormat,
                        formatTime: fieldTimeFormat,
                        timepicker: enableTime || false,
                        datepicker: enableDate || false,
                        inline: isInline,
                        step: parseInt(fieldTimeStep),
                        onChangeDateTime: function(){
                            jQuery('form[id="buddyforms_form_'+formSlug+'"]').valid();
                        }
                    };

                    var dateTimePickerConfigCallback = function (config) {
                        if(config) {
                            dateTimePickerConfig = config;
                        }
                    };
                    jQuery(document.body).trigger({type: "buddyforms:field:date"}, [dateTimePickerConfig, element, fieldData, formSlug, dateTimePickerConfigCallback]);

                    jQuery(element).datetimepicker(dateTimePickerConfig);
                }
            });
        }
    }

    function handleFeaturePost() {
        var statusElement = jQuery('select[name=status]');
        if (statusElement && statusElement.length > 0) {
            var bf_status = statusElement.val();
            jQuery('.bf_datetime_wrap').toggle(bf_status === 'future');

            statusElement.change(function () {
                var bf_status = jQuery(this).val();
                jQuery('.bf_datetime_wrap').toggle(bf_status === 'future');
            });
        }
    }

    function handleFormContent() {
        var formContentValElement = jQuery('#buddyforms_form_content_val');
        var formContentElement = jQuery('#buddyforms_form_content');
        if (formContentElement && formContentElement.length > 0 && formContentValElement && formContentValElement.length > 0) {
            var buddyforms_form_content_val = formContentValElement.html();
            formContentElement.html(buddyforms_form_content_val);
        }
    }

    function actionFromButton(event) {
        event.preventDefault();
        var target = jQuery(this).data('target');
        var formOptions = 'publish';
        var draftAction = false;
        if (buddyformsGlobal && buddyformsGlobal[target] && buddyformsGlobal[target].status) {
            formOptions = buddyformsGlobal[target].status;
        }
        if (buddyformsGlobal && buddyformsGlobal[target] && buddyformsGlobal[target].draft_action) {
            draftAction = (buddyformsGlobal[target].draft_action[0] === 'Enable Draft');
        }
        var targetForms = jQuery('form#buddyforms_form_' + target);
        if (targetForms && targetForms.length > 0) {
            var fieldStatus = getFieldDataBy(target, 'status');
            if (fieldStatus === false) { //Not exist the field,
                var statusElement = targetForms.find('input[type="hidden"][name="status"]');
                if (statusElement && statusElement.length > 0) {
                    var post_status = jQuery(this).data('status') || formOptions;
                    statusElement.val(post_status);
                }
            }
            targetForms.trigger('submit');
        }
    }

    function disableFormSubmit(){
        var submitButton = jQuery('button.bf-submit');
        if(submitButton){
            var target = submitButton.data('target');
            if(target){
                submitButton.attr('disabled', 'disabled');
            }
        }
    }

    function enableFormSubmit() {
        var submitButton = jQuery('button.bf-submit');
        if (submitButton) {
            var target = submitButton.data('target');
            if (target) {
                submitButton.removeAttr('disabled');
            }
        }
    }

    function validateGlobalConfig () {
        var forms = jQuery('form[id^="buddyforms_form_"]');
        if (forms && forms.length > 0) {
            jQuery.each(forms, function () {
                jQuery(this).submit(function () {}).validate({
                    ignore: [],
                    errorPlacement: function (label, element) {
                        var formSlug = getFormSlugFromFormElement(element);
                        var fieldSlug = jQuery(element).attr('name');
                        var fieldData = getFieldFromSlug(fieldSlug, formSlug);
                        if (!fieldData) {//if not field data is not possible to validate it
                            return true;
                        }
                        switch (fieldData.type) {
                            case "taxonomy":
                            case "category":
                            case "tags":
                                element.parent().find('span.select2-selection');
                                label.insertAfter(element);
                                break;
                            case "checkbox":
                            case "date":
                            case "radiobutton":
                                var labelElement = jQuery('label[for="'+fieldSlug+'"]');
                                label.insertAfter(labelElement);
                                break;
                            default:
                                 label.insertAfter(element);
                        }
                    },
                    highlight: function (element, errorClass, validClass) {
                        var elem = jQuery(element);
                        if (elem.hasClass('select2-hidden-accessible')) {
                            elem.parent().find('span.select2-selection').addClass(errorClass);
                        } else {
                            elem.addClass(errorClass);
                        }
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        var elem = jQuery(element);
                        if (elem.hasClass('select2-hidden-accessible')) {
                            elem.parent().find('span.select2-selection').removeClass(errorClass);
                        } else {
                            elem.removeClass(errorClass);
                        }
                    },
                });
            });
        }
    }

    return {
        submissionModal: function (target) {
            submissionModal = target;
        },
        getSubmissionModal: function () {
            return submissionModal;
        },
        submissionModalContent: function (target) {
            submissionModalContent = target;
        },
        getSubmissionModalContent: function () {
            return submissionModalContent;
        },
        init: function () {
            var redirect = bf_getUrlParameter('redirect_url');
            if (redirect) {
                specialPasswordRedirectAfterRegistration(redirect);
            }
            var bf_submission_modal_content = jQuery('.buddyforms-posts-content');
            if (bf_submission_modal_content.length > 0) {
                fncBuddyForms.submissionModalContent(bf_submission_modal_content);
            }
            jQuery(document.body).on('click', 'button[type="button"][name="draft"].bf-draft', actionFromButton);
            jQuery(document.body).on('click', 'button[type="submit"][name="submitted"].bf-submit', actionFromButton);
            jQuery(document.body).on('click', '.button.bf_reset_multi_input', resetInputMultiplesChoices);
            jQuery(document.body).on('keyup', 'input[name=buddyforms_user_pass], input[name=buddyforms_user_pass_confirm]', checkPasswordStrength);
            jQuery(document).on("click", '.bf_delete_post', bf_delete_post);
            jQuery(document).on("click", '.bf-submission-modal', openSubmissionModal);
            jQuery(document).on("click", '.bf-close-submissions-modal', closeSubmissionModal);
            jQuery(document).on("click", '.create-new-tax-item', createTaxItem);

            //Events
            jQuery(document).on('buddyforms:submit:disable', disableFormSubmit);
            jQuery(document).on('buddyforms:submit:enable', enableFormSubmit);

            disableACFPopup();

            if (jQuery && jQuery.validator) {
                validateGlobalConfig();
                addValidationForUserWebsite();
                addValidationMinLength();
                addValidationMaxLength();
                addValidationMaxValue();
                addValidationMinValue();
                addValidationRequired();
                enabledSelect2();
                enabledDateTime();
            }

            bf_form_errors();

            enabledGarlic();

            handleFeaturePost();

            handleFormContent();
        }
    }
}

var fncBuddyForms = BuddyForms();
jQuery(document).ready(function () {
    fncBuddyForms.init();
});

jQuery(document).on('buddyforms:init', function () {
    fncBuddyForms.init();
});

// Example to extend the required validation using events
// jQuery(document).on('buddyforms:validation:required', function (event, value, element, fieldData, formSlug, requiredCallback) {
//     var isValid = false;
//     if(fieldData.type === 'title'){
//         isValid = value.length > 0;
//     }
//     requiredCallback(isValid, 'The Title field is required...');
// });