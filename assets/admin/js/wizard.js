jQuery(window).bind("load", function () {
//    jQuery('h1').hide();
});
jQuery(document).ready(function (jQuery) {



    var wizard = bf_getUrlParameter('wizard');

    // Get out of here if not the wizard view
    if(wizard == null){
        return false;
    }

    // Grab all usable and hide the rest
    if(wizard != null){

        jQuery('#post, #postbox-container-1, #postbox-container-2').hide();
        var title                       = jQuery( '#post-body-content' );
        var publishing_action           = jQuery( '#publishing-action' );
        var buddyforms_form_elements    = jQuery( '#buddyforms_form_elements' );
        var buddyforms_form_type_select = jQuery( '#bf-form-type-select' );
        var buddyforms_template         = jQuery( '.buddyforms_template' );
        var buddyforms_metabox_sidebar  = jQuery( '#buddyforms_metabox_sidebar' );
        var buddyforms_notification     = jQuery( '#notification' );
        var buddyforms_permission       = jQuery( '#permission' );

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {"action": "buddyforms_form_builder_wizard_types"},
            success: function (data) {

                jQuery('#poststuff').html(data);
                jQuery('#post').show();
            }
        });

    }


    // STEP 1 Name your form
    if(wizard == 1){

        jQuery('#poststuff').html('<h2>BuddyForms Form Wizard</h2>');
        //jQuery( title ).appendTo( '#poststuff' );
        //jQuery( '<div id="bf-hooker"></div>' ).appendTo( '#poststuff' );
        //jQuery( '<a href="#" data-wizard="2" class="wizard-next-step">Next Step</a>').appendTo( '#poststuff' );


        jQuery( publishing_action ).appendTo( '#poststuff' );

        jQuery( '<div id="hooker-steps"> ' +
            '<h3>Title</h3><section><div id="bf-hooker-name"></div></section>' +
            '<h3>Add Elements</h3><section><div id="bf-hooker-formbuilder"></div></section>' +
            '<h3>Mail Notification</h3><section><div id="bf-hooker-notifications"></div></section>' +
            '<h3>Permissions</h3><section><div id="bf-hooker-permissions"></div></section>' +
            '</div>').appendTo( '#poststuff' );


        jQuery( title ).appendTo( '#bf-hooker-name' );
        //jQuery( buddyforms_form_type_select ).appendTo( '#bf-hooker-name' );
        jQuery( buddyforms_metabox_sidebar ).appendTo( '#bf-hooker-formbuilder' );
        jQuery( buddyforms_form_elements ).appendTo( '#bf-hooker-formbuilder' );
        jQuery( buddyforms_notification ).appendTo( '#bf-hooker-notifications' );
        jQuery( buddyforms_permission ).appendTo( '#bf-hooker-permissions' );


        jQuery("#hooker-steps").steps({
            headerTag: "h3",
            bodyTag: "section",
            transitionEffect: "slideLeft",
            autoFocus: true,
            enableFinishButton: true,
            onStepChanging: function (event, currentIndex, newIndex)
            {

                if(currentIndex == 0) {
                    // Validate Step 1
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


                if(currentIndex == 1) {
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
                    return true;
                }
                if(currentIndex == 2) {
                    return true;
                }
                if(currentIndex == 3) {
                    return true;
                }

            },
            onFinishing: function (event, currentIndex)
            {
                var form = jQuery('#post');

                // Submit form input
                form.submit();
                return true;
            },
            onFinished: function (event, currentIndex)
            {
                return true;
            }
        });
    }
});