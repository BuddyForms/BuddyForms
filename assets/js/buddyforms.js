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
function getFieldDataBy(formSlug, search, fieldTargetKey){
	if(buddyformsGlobal && buddyformsGlobal[formSlug]) {
		fieldTargetKey= fieldTargetKey || 'type';
		var fields = buddyformsGlobal[formSlug].form_fields;
		var result = jQuery.map(fields, function(element, key){
			if(element[fieldTargetKey] === search){
				element.id = key;
				return element;
			}
		});
		if(result && result[0]){
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
		var pass1 = jQuery('input[name=buddyforms_user_pass]').val();
		var pass2 = jQuery('input[name=buddyforms_user_pass_confirm]').val();
		var strengthResult = jQuery('#password-strength');
		var submitButton = jQuery('.bf-submit');
		var blacklistArray = ['black', 'listed', 'word'];
		var passwordHint = jQuery('.buddyforms-password-hint');

		// Reset the form & meter
		submitButton.attr('disabled', 'disabled');
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
			submitButton.removeAttr('disabled');
		} else {
			strengthResult.after(hint_html);
		}

		return strength;
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
		var bf_submission_modal_content = jQuery('.buddyforms-posts-content');
		if (bf_submission_modal_content.length > 0) {
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
		var bf_submission_modal_content = jQuery('.buddyforms-posts-content');
		if (bf_submission_modal_content.length > 0) {
			var targetId = jQuery(this).attr('data-id');
			bf_submission_modal_content.find('.bf_posts_' + targetId).prepend(fncBuddyForms.getSubmissionModal());
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
			var match = /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/.test(value);
			if (match) {
				return true;
			}
			return false;
		}, "Please enter a valid URL.");// todo need il18n
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
			});
		}
	}

	function enabledDateTime() {
		var dateElements = jQuery('.bf_datetime');
		if (dateElements && dateElements.length > 0) {
			dateElements.datetimepicker({// todo add more options to control from the field
				controlType: 'select',
				timeFormat: 'hh:mm tt'
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
		if(buddyformsGlobal && buddyformsGlobal[target]) {
			formOptions = buddyformsGlobal[target].status;
		}
		var post_status = jQuery(this).data('status') || formOptions;
		var targetForms = jQuery('form#buddyforms_form_' + target);
		if (targetForms && targetForms.length > 0) {
			var statusElement;
			var fieldStatus = getFieldDataBy(target, 'status');
			if(fieldStatus === false) { //Not exist the field,
				statusElement = targetForms.find('input[type="hidden"][name="status"]');
				if (statusElement && statusElement.length > 0) {
					statusElement.val(post_status);
				}
			}
			targetForms.trigger('submit');
		}
	}

	return {
		submissionModal: function (target) {
			submissionModal = target;
		},
		getSubmissionModal: function () {
			return submissionModal;
		},
		init: function () {
			var redirect = bf_getUrlParameter('redirect_url');
			if (redirect) {
				specialPasswordRedirectAfterRegistration(redirect);
			}
			jQuery(document.body).on('click', 'button[type="button"][name="draft"].btn.btn-alt.bf-draft', actionFromButton);
			jQuery(document.body).on('click', 'button[type="submit"][name="submitted"].btn.btn-primary.bf-submit', actionFromButton);
			jQuery(document.body).on('click', '.button.bf_reset_multi_input', resetInputMultiplesChoices);
			jQuery(document.body).on('keyup', 'input[name=buddyforms_user_pass], input[name=buddyforms_user_pass_confirm]', checkPasswordStrength);
			jQuery(document).on("click", '.bf_delete_post', bf_delete_post);
			jQuery(document).on("click", '.bf-submission-modal', openSubmissionModal);
			jQuery(document).on("click", '.bf-close-submissions-modal', closeSubmissionModal);
			jQuery(document).on("click", '.create-new-tax-item', createTaxItem);

			disableACFPopup();

			if (jQuery && jQuery.validator) {
				addValidationForUserWebsite();
			}

			bf_form_errors();

			enabledGarlic();

			enabledSelect2();

			handleFeaturePost();

			handleFormContent();
		}
	}
}

var fncBuddyForms = BuddyForms();
jQuery(document).ready(function () {
	fncBuddyForms.init();
});