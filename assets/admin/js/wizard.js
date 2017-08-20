jQuery(document).ready(function (jQuery) {

    function key13control(e) {
        if (e.keyCode == 13) {

            var all = jQuery(".steps ul li").length;
            var current = jQuery("#hooker-steps").steps("getCurrentIndex");

            if (current === undefined)
                return false;

            if (all == (current + 1)) {
                jQuery('a[href="#finish"]').trigger('click');
            } else {
                jQuery('a[href="#next"]').trigger('click');
            }

            e.preventDefault();
            return false;
        }
    }

    // get the url parameter to create the UI
    var wizard = bf_getUrlParameter('wizard');
    var type = bf_getUrlParameter('type');
    var template = bf_getUrlParameter('template');
    var post_id = bf_getUrlParameter('post');

    // Get out of here if not the wizard view
    if (wizard == null) {
        return false;
    }

    // Grab all needed form parts from the dom and add it into vars for later usage.
    if (wizard != null) {

        // Load the form elements template depend on the type
        if (template) {
            load_formbuilder_template(template);
        }

        jQuery(document.body).on('change', '#public_submit_create_account-0 ', function () {

            if (jQuery(this).prop('checked')) {
                jQuery('#hooker-steps').steps("insert", 6, {
                    title: "Registration",
                    content: buddyforms_registration
                });
            } else {
                jQuery('#hooker-steps').steps("remove", 6);
            }

        });

        // first hide all so we have a consitend feeling
        jQuery('#post, #postbox-container-1, #postbox-container-2, #message, .error').hide();

        jQuery('#menu-posts-buddyforms a').removeClass('current');
        jQuery('#menu-posts-buddyforms li').removeClass('current');

        jQuery('#menu-posts-buddyforms [href*="wizard"]').addClass('current');
        jQuery('#menu-posts-buddyforms [href*="wizard"]').parent('li').addClass('current');

        // get the parts
        var poststuff = jQuery('#poststuff');
        //
        var container1 = poststuff.find('#postbox-container-1');
        var container2 = poststuff.find('#postbox-container-2');

        var post_body = poststuff.find('#post-body');

        var title = poststuff.find('#post-body-content');
        var submitdiv = poststuff.find('#submitdiv');
        var submitdiv_actions = submitdiv.find('#buddyforms-actions');
        var buddyforms_form_shortcodes = poststuff.find('#buddyforms_form_shortcodes');
        var buddyforms_form_go_pro = poststuff.find('#buddyforms_form_go_pro');

        var buddyforms_form_elements = poststuff.find('#buddyforms_form_elements');
        var buddyforms_template = poststuff.find('.buddyforms_template');
        var buddyforms_metabox_sidebar = poststuff.find('#buddyforms_metabox_sidebar');
        var buddyforms_notification = poststuff.find('#notification');
        buddyforms_notification.find('#no-trigger-mailcontainer').hide();
        buddyforms_notification.find('#no-trigger-post-status-mail-container').hide();


        var buddyforms_permission = poststuff.find('#permission');
        var buddyforms_registration = poststuff.find('#registration');
        var buddyforms_create_content = poststuff.find('#create-content');
        var buddyforms_edit_submissions = poststuff.find('#edit-submissions');

    }

    // Workaround for now. Change the url and reload. Would love to have this step just start the wizard. Help Needed from a guru
    jQuery(document.body).on('click', '.bf_wizard_types', function () {
        URL = document.URL;
        type = jQuery(this).attr('data-type');
        type = type.split('_')[0];

        template = jQuery(this).attr('data-template');

        URL = URL.replace('wizard=1', 'wizard=2&type=' + type + '&template=' + template);
        window.location = URL;
    });

    // STEP 1 Select the Form Type
    if (wizard == 1) {
        select_form_type();
    }

    // STEP 2 Start the Wizard
    if (wizard == 2) {
        start_wizard();
    }

    if (wizard == 'done') {

        URL = document.URL;
        // URL  = document.origin;
        URL = URL.slice(0, URL.indexOf('?'));

        // console.log('url  ' + URL);

        post_body.prepend('<h1 style="margin: 60px 0; padding: 0px; font-size: 78px; display: block;">High Five!</h1>' +
            '<h2 style="margin: 60px 0; padding: 4px; font-size: 24px; font-weight: normal;">You have created the form ' + title.find('#title').val() + '</h2>');

        title.remove();

        submitdiv.find('#major-publishing-actions').remove();
        submitdiv.find('h2 span').html('View Form and Submissions');


        var link_to_pro = buddyforms_form_go_pro.find('.buddyforms_get_pro');

        var URL = URL.split("?");

        var url_formbuilder = URL[0] + '?post=' + post_id + '&action=edit';

        var url_close = URL[0].replace('post.php', 'edit.php');
        url_close = url_close + '?post_type=buddyforms';


        submitdiv_actions.append('<ul>' +
            '<li><a class="button button-large bf_button_action" href="' + url_formbuilder + '"><span class="dashicons dashicons-edit"></span> Jump in the Form Builder</a></li>' +
            '<li><a class="button button-large bf_button_action" href="' + url_close + '"><span class="dashicons dashicons-yes"></span> Close the wizard </a></li>' +
            '</ul>');

        submitdiv_actions.append(link_to_pro);
        buddyforms_form_go_pro.remove();

        buddyforms_form_shortcodes.find('h2 span').html('Embed Your Form via Shortcodes');
        container2.html(buddyforms_form_shortcodes);

        jQuery('#post').html(poststuff);
        jQuery('#post, #postbox-container-1, #postbox-container-2').show();
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {"action": "buddyforms_wizard_rewrite_rules"},
            success: function (data) {
                //console.log('Done ' + data);
            }
        });
    }

    // Get the Form Type Templates for Step 1
    function select_form_type() {
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {"action": "buddyforms_form_builder_wizard_types"},
            success: function (data) {
                jQuery('#poststuff #post-body-content').html('<p style="font-size: 21px; margin: 20px 0 30px;">Select the form type to start the Wizard:</p>');
                jQuery(data).appendTo('#poststuff #post-body-content');

                jQuery('#poststuff .buddyforms_wizard_types h5:first').hide();
                jQuery('#post').show();

            }
        });
    }

    // Start the wizard
    function start_wizard() {

        // Add a hidden input with the form type for later usage
        jQuery('<input id="bf-form-type-select" name="buddyforms_options[form_type]" type="hidden" value="' + type + '">').appendTo('#poststuff');

        // Create the html for the contact form steps
        jQuery(
            '<div id="hooker-steps"> ' +
            '<h3>Name Your Form</h3><section><div id="bf-hooker-name"></div></section>' +
            '</div>'
        ).appendTo('#poststuff');

        // Check if form type is post and add additional steps to the wizard
        if (type == 'post') {
            jQuery(
                '<h3>Post Settings</h3><section><div id="bf-hooker-create-content"></div></section>' +
                '<h3>Submissions</h3><section><div id="bf-hooker-edit-submissions"></div></section>'
            ).appendTo('#hooker-steps');

            // Add the form parts for create and edit to the wizard sections
            jQuery(buddyforms_create_content).appendTo('#bf-hooker-create-content');
            jQuery(buddyforms_edit_submissions).appendTo('#bf-hooker-edit-submissions');
        }

        // All Forms have form elements ;) they come preloaded form he json
        jQuery('<h3>Form Elements</h3><section><div id="bf-hooker-formbuilder"></div></section>').appendTo('#hooker-steps');

        // Contact and Post form have mail Notifications
        if (type == 'contact' || type == 'post') {
            jQuery('<h3>Mail Notifications</h3><section><div id="bf-hooker-notifications"></div></section>'
            ).appendTo('#hooker-steps');
        }
        // Registration options needed if type is == registration
        if (type == 'registration') {
            jQuery('<h3>Registration</h3><section><div id="bf-hooker-registration"></div></section>').appendTo('#hooker-steps');
            jQuery(buddyforms_registration).appendTo('#bf-hooker-registration');
        }

        // Permissions we only make available for post forms
        if (type == 'post') {
            jQuery('<h3>Permissions</h3><section><div id="bf-hooker-permissions"></div></section>').appendTo('#hooker-steps');
        }

        // Add the form parts to the steps sections
        jQuery(title).appendTo('#bf-hooker-name');
        jQuery(buddyforms_form_elements).appendTo('#bf-hooker-formbuilder');
        jQuery(buddyforms_notification).appendTo('#bf-hooker-notifications');
        jQuery(buddyforms_permission).appendTo('#bf-hooker-permissions');

        // Change the Form Builder h2 Title
        jQuery('#buddyforms_form_elements h2 span').html('Choose a field type and click "Add Field"');

        // Hide the normal form builder templates. They are not needed.
        jQuery(buddyforms_template).hide();

        function add_form_elements_select() {
            // Get all form elements for the selected form type and add them to the form builder
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {"action": "buddyforms_form_builder_wizard_elements", "type": type},
                success: function (data) {
                    jQuery('#formbuilder-actions-wrap').append(data);
                }
            });
        }

        add_form_elements_select();


        // All should be in place. Show the wizard
        var form = jQuery('#post').show();

        // Let us initial and start the wizard steps
        form.find('#hooker-steps').steps({
            headerTag: "h3",
            bodyTag: "section",
            transitionEffect: "slideLeft",
            autoFocus: true,
            enableFinishButton: true,
            saveState: true,
            startIndex: 0,
            onStepChanging: function (event, currentIndex, newIndex) {

                // Validate Step 1 the form label
                if (currentIndex == 0) {

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
                    if (error === true) {
                        return false;
                    }

                    return true;
                }

                // Validate Step 2 the form builder form elements
                if (currentIndex == 1) {

                    if (type == 'post') {
                        var post_type = jQuery('#form_post_type').val();
                        if (post_type == 'bf_submissions') {

                            jQuery('#form_post_type').addClass('bf-error');
                            return false;
                        } else {
                            jQuery('#form_post_type').addClass('bf-ok');
                            return true;
                        }

                    } else {
                        var error = false;
                        // traverse all the required elements looking for an empty one
                        jQuery("#buddyforms_forms_builder input[required]").each(function () {

                            // if the value is empty, that means that is invalid
                            if (jQuery(this).val() == "") {

                                jQuery(this).addClass('bf-error');
                                error = true;
                                jQuery(".accordion-body.collapse.in").removeClass("in");
                                jQuery(this).closest(".accordion-body.collapse").addClass("in").css("height", "auto");
                                jQuery('#buddyforms_form_setup').removeClass('closed');
                                jQuery('#buddyforms_form_elements').removeClass('closed');
                            }
                        });
                        if (error === true) {
                            return false;
                        }
                    }
                    return true;
                }
                if (currentIndex == 2) {
                    //add_form_elements_select();
                    return true;
                }
                if (currentIndex == 3) {
                    if (type == 'post') {
                        var error = false;
                        // traverse all the required elements looking for an empty one
                        jQuery("#buddyforms_forms_builder input[required]").each(function () {

                            // if the value is empty, that means that is invalid
                            if (jQuery(this).val() == "") {

                                jQuery(this).addClass('bf-error');
                                error = true;
                                jQuery(".accordion-body.collapse.in").removeClass("in");
                                jQuery(this).closest(".accordion-body.collapse").addClass("in").css("height", "auto");
                                jQuery('#buddyforms_form_setup').removeClass('closed');
                                jQuery('#buddyforms_form_elements').removeClass('closed');
                            }
                        });
                        if (error === true) {
                            return false;
                        }
                    }

                    return true;
                }
                if (currentIndex == 4) {
                    return true;
                }
                if (currentIndex == 5) {
                    return true;
                }

            },
            onFinishing: function (event, currentIndex) {
                return true;
            },
            onFinished: function (event, currentIndex) {
                var FormData = jQuery(form).serialize();
                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {"action": "buddyforms_form_builder_wizard_save", "FormData": FormData},
                    success: function (data) {
                        jQuery('#post').html('<h2 style="margin: 30px 0; padding: 4px;">Well Done! Form Processing.</h2><br><span class="dashicons dashicons-smiley"></span>');
                        window.location = data;
                        return false;
                    }
                });
                return false;
            }

        });

    }
});
