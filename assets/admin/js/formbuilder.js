/**
 * Hooks object
 *
 * This object needs to be declared early so that it can be used in code.
 * Preferably at a global scope.
 */
var BuddyFormsBuilderHooks = BuddyFormsBuilderHooks || {};

BuddyFormsBuilderHooks.actions = BuddyFormsBuilderHooks.actions || {};
BuddyFormsBuilderHooks.filters = BuddyFormsBuilderHooks.filters || {};

/**
 * Add a new Action callback to BuddyFormsBuilderHooks.actions
 *
 * @param tag The tag specified by do_action()
 * @param callback The callback function to call when do_action() is called
 * @param priority The order in which to call the callbacks. Default: 10 (like WordPress)
 */
BuddyFormsBuilderHooks.addAction = function (tag, callback, priority) {
    if (typeof priority === "undefined") {
        priority = 10;
    }
    // If the tag doesn't exist, create it.
    BuddyFormsBuilderHooks.actions[tag] = BuddyFormsBuilderHooks.actions[tag] || [];
    BuddyFormsBuilderHooks.actions[tag].push({priority: priority, callback: callback});
};

/**
 * Add a new Filter callback to BuddyFormsBuilderHooks.filters
 *
 * @param tag The tag specified by apply_filters()
 * @param callback The callback function to call when apply_filters() is called
 * @param priority Priority of filter to apply. Default: 10 (like WordPress)
 */
BuddyFormsBuilderHooks.addFilter = function (tag, callback, priority) {
    if (typeof priority === "undefined") {
        priority = 10;
    }
    // If the tag doesn't exist, create it.
    BuddyFormsBuilderHooks.filters[tag] = BuddyFormsBuilderHooks.filters[tag] || [];
    BuddyFormsBuilderHooks.filters[tag].push({priority: priority, callback: callback});
};

/**
 * Remove an Action callback from BuddyFormsBuilderHooks.actions
 *
 * Must be the exact same callback signature.
 * Warning: Anonymous functions can not be removed.
 * @param tag The tag specified by do_action()
 * @param callback The callback function to remove
 */
BuddyFormsBuilderHooks.removeAction = function (tag, callback) {
    BuddyFormsBuilderHooks.actions[tag] = BuddyFormsBuilderHooks.actions[tag] || [];
    BuddyFormsBuilderHooks.actions[tag].forEach(function (filter, i) {
        if (filter.callback === callback) {
            BuddyFormsBuilderHooks.actions[tag].splice(i, 1);
        }
    });
};

/**
 * Remove a Filter callback from BuddyFormsBuilderHooks.filters
 *
 * Must be the exact same callback signature.
 * Warning: Anonymous functions can not be removed.
 * @param tag The tag specified by apply_filters()
 * @param callback The callback function to remove
 */
BuddyFormsBuilderHooks.removeFilter = function (tag, callback) {
    BuddyFormsBuilderHooks.filters[tag] = BuddyFormsBuilderHooks.filters[tag] || [];
    BuddyFormsBuilderHooks.filters[tag].forEach(function (filter, i) {
        if (filter.callback === callback) {
            BuddyFormsBuilderHooks.filters[tag].splice(i, 1);
        }
    });
};

/**
 * Calls actions that are stored in BuddyFormsBuilderHooks.actions for a specific tag or nothing
 * if there are no actions to call.
 *
 * @param tag A registered tag in Hook.actions
 * @param options Optional JavaScript object to pass to the callbacks
 */
BuddyFormsBuilderHooks.doAction = function (tag, options) {
    var actions = [];
    if (typeof BuddyFormsBuilderHooks.actions[tag] !== "undefined" && BuddyFormsBuilderHooks.actions[tag].length > 0) {
        BuddyFormsBuilderHooks.actions[tag].forEach(function (hook) {
            actions[hook.priority] = actions[hook.priority] || [];
            actions[hook.priority].push(hook.callback);
        });

        actions.forEach(function (BuddyFormsBuilderHooks) {
            BuddyFormsBuilderHooks.forEach(function (callback) {
                callback(options);
            });
        });
    }
};

/**
 * Calls filters that are stored in BuddyFormsBuilderHooks.filters for a specific tag or return
 * original value if no filters exist.
 *
 * @param tag A registered tag in Hook.filters
 * @param value The value
 * @param options Optional JavaScript object to pass to the callbacks
 * @options
 */
BuddyFormsBuilderHooks.applyFilters = function (tag, value, options) {
    var filters = [];
    if (typeof BuddyFormsBuilderHooks.filters[tag] !== "undefined" && BuddyFormsBuilderHooks.filters[tag].length > 0) {
        BuddyFormsBuilderHooks.filters[tag].forEach(function (hook) {
            filters[hook.priority] = filters[hook.priority] || [];
            filters[hook.priority].push(hook.callback);
        });
        filters.forEach(function (BuddyFormsBuilderHook) {
            BuddyFormsBuilderHook.forEach(function (callback) {
                value = callback(value, options);
            });
        });
    }
    return value;
};

// On Load jQuery
jQuery(function () {

	jQuery('.bf-color').wpColorPicker();

	// Type box.
	jQuery('.bf-form-type-wrap').appendTo('#buddyforms_form_setup .hndle span');

	jQuery(function () {
		// Prevent inputs in meta box headings opening/closing contents.
		jQuery('#buddyforms_form_setup').find('.hndle').unbind('click.postboxes');

		jQuery('#buddyforms_form_setup').on('click', '.hndle', function (event) {

			// If the user clicks on some form input inside the h3 the box should not be toggled.
			if (jQuery(event.target).filter('input, option, label, select').length) {
				return;
			}

			jQuery('#buddyforms_form_setup').toggleClass('closed');
		});
	});

	jQuery('form').bind('submit', function () {
		jQuery(this).find(':input').prop('disabled', false);
	});

	jQuery(document).on('buddyform:load_fields', function (event) {
		var buddyforms_forms_builder = jQuery(this).find('.buddyforms_forms_builder');
		if (buddyforms_forms_builder.length > 0) {
			var accordionContainer = jQuery("#sortable_buddyforms_elements");
			if (!accordionContainer.hasClass('buddyform-ready')) {
				accordionContainer.accordion({
					collapsible: true,
					header: "div.accordion-heading-options",
					heightStyle: "content"
				});
				accordionContainer.addClass('buddyform-ready');
			} else {
				accordionContainer.accordion("refresh");
			}
			jQuery(".buddyform-tabs-left").tabs({
				"heightStyle": "content"
			}).addClass("ui-tabs-vertical ui-helper-clearfix");
			jQuery(".buddyform-tabs-left li").removeClass("ui-corner-top").addClass("ui-corner-left");
		}
	});

	jQuery(document).on('buddyform:load_notifications', function () {
		var buddyforms_notification_builder = jQuery(this).find('.buddyforms_accordion_notification');
		if (buddyforms_notification_builder.length > 0) {
			var accordionNotificationContainer = buddyforms_notification_builder.find("li.bf_trigger_list_item");
			jQuery.each(accordionNotificationContainer, function(){
				var currentAccordionNotificationContainer = jQuery(this);
				if (!currentAccordionNotificationContainer.hasClass('buddyforms-ready')) {
					currentAccordionNotificationContainer.accordion({
						collapsible: true,
						active: false,
						header: "div.accordion-heading-options",
						heightStyle: "content"
					});
					currentAccordionNotificationContainer.addClass('buddyforms-ready');
				}
			});
		}
	});
});
jQuery(document).ready(function () {
	jQuery(document.body).trigger({type: "buddyform:load_fields"});
	jQuery(document.body).trigger({type: "buddyform:load_notifications"});

	//
	// Handle the hide/show moderation of user on update
	//
    jQuery(document.body).on('click', '[name="buddyforms_options[on_user_update][moderate_user_role_change][]"]', function() {
        var element = jQuery(this);
        var rows = element.closest('table.wp-list-table').find('tr[id^=table_row_global_]').not('tr[id=table_row_global_0], tr[id=table_row_global_1]');
        if (element.is(':checked')) {
            jQuery.each(rows, function(){
            	jQuery(this).removeClass('bf-hidden');
                jQuery(this).find('input, select').removeClass('bf-hidden');
			});
        } else {
            jQuery.each(rows, function(){
                jQuery(this).addClass('bf-hidden');
                jQuery(this).find('input, select').addClass('bf-hidden');
            });
		}
    });

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

		var addElement = jQuery('#bf_add_new_form_element');
		var fieldType = addElement.val();

		if (fieldType === 'none') {
			jQuery('#bf_add_new_form_element_modal').val('none');

			jQuery('#formbuilder-action-select-modal').dialog({
				title: "Please Select a Field Type",
				height: 240,
				modal: true,
			});
			jQuery('.formbuilder-spinner').removeClass('is-active');
			return false;
		}

		var unique = addElement.find(':selected').data('unique');
		var search4Element = jQuery("#sortable_buddyforms_elements .bf_" + fieldType);

		if (unique === 'unique') {
			if (search4Element !== null && typeof search4Element === 'object' && search4Element.length > 0) {
				bf_alert('This element can only be added once into each form');
				jQuery('.formbuilder-spinner').removeClass('is-active');
				return false;
			}
		}

		var addNewElement = BuddyFormsBuilderHooks.applyFilters('buddyforms:add_new_form_element', true, {addElement: addElement, fieldType: fieldType, search4Element: search4Element, unique: unique});

		if(addNewElement) {
			if (buddyformsGlobal) {
				jQuery.ajax({
					type: 'POST',
					url: buddyformsGlobal.admin_url,
					data: {
						"action": "buddyforms_display_form_element",
						"fieldtype": fieldType,
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
						jQuery(document.body).trigger({type: "buddyform:load_fields"});
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
					},
					complete: function () {
						jQuery('#formbuilder-show-templates').hide();
					}
				});
			}
		} else {
			var addNewElementErrorMessage = BuddyFormsBuilderHooks.applyFilters('buddyforms:add_new_form_element_error_message', 'This element is not valid to add to the form.', {addElement: addElement, fieldType: fieldType, search4Element: search4Element, unique: unique});
			bf_alert(addNewElementErrorMessage);
			jQuery('.formbuilder-spinner').removeClass('is-active');
		}
		return false;

	});

	//
	// Load the Form From Template
	//
	jQuery(document.body).on('click', '.bf_form_template', function () {
		var button = jQuery(this);
		button.parent().LoadingOverlay('show');
		jQuery('button.bf_form_template').prop('disabled', true);
		var template = button.data('template');
		load_formbuilder_template(template, function() {
			button.parent().LoadingOverlay('hide');
			jQuery('button.bf_form_template').prop('disabled', false);
			jQuery(document.body).trigger({type: "buddyform:load_notifications"});
		});
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
});
