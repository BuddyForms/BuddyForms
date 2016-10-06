jQuery(document).ready(function (jQuery) {

    // Check the form type and only display the relevant form setup tabs
    from_setup_form_type(jQuery('#bf-form-type-select').val());

    //
    //Function to show hide form setup tabs navigation
    //
    function from_setup_form_type(value){

        switch(value) {
            case 'contact':

                // Set post type value to bf_submissions to make sure it is a contact form if hidden
                jQuery('#form_post_type').val('bf_submissions');

                // Rename edit submissions to View
                jQuery('.nav-tabs .edit-submissions_nav a').text('View Submissions');

                // Show
                jQuery('.permission_nav, .edit-submissions_nav, .notifications_nav').show();

                // Hide
                jQuery('.buddyforms-metabox-hide-if-form-type-contact').hide();
                jQuery('.create-content_nav, .registrations_nav').hide();

                // Show/Hide the corresponding form elements in the form select
                jQuery('.bf_show_if_f_type_all').show();
                jQuery('.bf_show_if_f_type_registration').show();
                jQuery('.bf_show_if_f_type_post').hide();

                // Show/Hide after submission post options
                jQuery('#bf-after-submission-action option[value=display_form]').hide();
                jQuery('#bf-after-submission-action option[value=display_post]').hide();
                jQuery('#bf-after-submission-action option[value=display_posts_list]').hide();


                break;
            case 'registration':

                // Set post type value to bf_submissions to make sure it is a contact form if hidden
                jQuery('#form_post_type').val('bf_submissions');
                jQuery('#attached_page').val('none');

                jQuery('.registrations_nav').show();

                // Hide
                jQuery('.permission_nav, .edit-submissions_nav, .create-content_nav, .notifications_nav').hide();
                jQuery('.buddyforms-metabox-hide-if-form-type-register').hide();

                // Show/Hide the corresponding form elements in the form select
                jQuery('.bf_show_if_f_type_registration').show();
                jQuery('.bf_show_if_f_type_all').hide();
                jQuery('.bf_show_if_f_type_post').hide();

                // Hide after submission post options
                jQuery('#bf-after-submission-action option[value=display_form]').hide();
                jQuery('#bf-after-submission-action option[value=display_post]').hide();
                jQuery('#bf-after-submission-action option[value=display_posts_list]').hide();

                break;
            case 'post':

                // Rename edit submissions to Edit
                jQuery('.nav-tabs .edit-submissions_nav a').text('Edit Submissions');

                // Show
                jQuery('.buddyforms-metabox-show-if-form-type-post').show();
                jQuery('#bf-after-submission-action option[value=display_form]').show();
                jQuery('#bf-after-submission-action option[value=display_post]').show();
                jQuery('#bf-after-submission-action option[value=display_posts_list]').show();

                // View all post related nav items
                jQuery('.create-content_nav,.permission_nav, .edit-submissions_nav, .bf_show_if_f_type_post, .notifications_nav').show();

                jQuery('.registrations_nav').hide();

                // Show the corresponding form elements in the form select
                jQuery('.bf_show_if_f_type_all').show();
                jQuery('.bf_show_if_f_type_post').show();

                break;
        }

        from_setup_post_type();
        from_setup_attached_page()
        from_setup_create_account();

        // Select first tab
        jQuery('a[href="#form-submission"]').tab('show');
        jQuery('.activeform-submission').addClass('active');
        jQuery('#form-submission').addClass('active in');

        jQuery("#adv-settings input[type='checkbox']").prop("checked", true);
        jQuery("#screen-meta-links").remove();
    }


    // Post Type Select function for the metabox visibility buddyforms-metabox-show-if-post-type-none
    function from_setup_post_type(){

        var post_type = jQuery('#form_post_type').val();

        if(post_type == 'bf_submissions') {
            jQuery('.buddyforms-metabox-show-if-post-type-none').hide();
            jQuery('.bf_hide_if_post_type_none').hide();

        } else {
            jQuery('.buddyforms-metabox-show-if-post-type-none').show();
            jQuery('.bf_hide_if_post_type_none').show();
        }
    }

    function from_setup_attached_page(){

        var attached_page = jQuery('#attached_page').val();

        if(attached_page == 'none') {
            jQuery('.buddyforms-metabox-show-if-attached-page').hide();
            jQuery('.bf_hide_if_post_type_none').hide();
            jQuery('#bf-after-submission-action option[value=display_posts_list]').hide();
        } else {
            jQuery('.buddyforms-metabox-show-if-attached-page').show();
            jQuery('.bf_hide_if_post_type_none').show();
            jQuery('#bf-after-submission-action option[value=display_posts_list]').show();

        }
        //jQuery('#siteurl_create').attr("href", "#" + attached_page);
        //jQuery('#siteurl_create').text("#" + attached_page);
    }





    from_setup_create_account();

    // Post Type Select function for the metabox visibility buddyforms-metabox-show-if-post-type-none
    function from_setup_create_account(){
        if( jQuery('#public_submit_create_account-0').is(":checked") ) {
            jQuery('.registrations_nav').show();
        } else {
            jQuery('.registrations_nav').hide();
        }
    }


    // On Change listener for the post type select
    jQuery(document.body).on('change', '#public_submit_create_account-0', function () {
        from_setup_create_account();
    });


    // On Change listener for the post form_post_type
    jQuery(document.body).on('change', '#form_post_type', function () {
        from_setup_post_type();
    });

    // On Change listener for the post attached_page
    jQuery(document.body).on('change', '#attached_page', function () {

        var attached_page   = jQuery('#attached_page').val();
        var form_slug       = jQuery('#attached_page').attr('data-slug');

        from_setup_attached_page();
        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            url: ajaxurl,
            data: {
                "action": "buddyforms_url_builder",
                "attached_page": attached_page,
                "form_slug": form_slug
            },
            success: function (data) {
                console.log(data);

                if(!data['form_slug']){
                    data['form_slug'] = '<span style="color:red">form slug</slug>';
                }
                jQuery( '.siteurl_create_html').html(data['permalink'] + 'create/' + data['form_slug']);
                jQuery( '.siteurl_edit_html').html(data['permalink'] + 'edit/' + data['form_slug']);


            },
            error: function () {
                jQuery('<div></div>').dialog({
                    modal: true,
                    title: "Info",
                    open: function() {
                        var markup = 'Something went wrong ;-(sorry)';
                        jQuery(this).html(markup);
                    },
                    buttons: {
                        Ok: function() {
                            jQuery( this ).dialog( "close" );
                        }
                    }
                });
            }
        });
    });

    // Form Type Select listener for the on change event
    jQuery(document.body).on('change', '#bf-form-type-select', function () {
        from_setup_form_type(jQuery(this).val());
    });

    //
    // On Change event for the after submission action select box
    //
    jQuery(document.body).on('change', '.bf-after-submission-action', function () {
        after_submission_action(jQuery(this));
    });

    // Trigger the change event after page load to load refresh the ui and show hide options
    jQuery('select#bf-after-submission-action').change();


    //
    // after_submission_action will show/hide form elements
    // WORK IN PROCESS!!!!
    //
    function after_submission_action(this_input) {

        if(this_input == null){
            this_input = jQuery('.bf-after-submission-action');

            alert(input_value)
        }

        var input_value = this_input.val();
        var ids         = this_input.attr('data-hidden');
        var id          = this_input.attr('id');

        if (!ids)
            return;

        ids = ids.split(" ");
        ids.forEach(function (entry) {
            jQuery('.' + entry).hide();
        });
        jQuery('.' + input_value).show();

    }

    //
    // Show Hide Form Elements depend on a select input
    //
    jQuery(document.body).on('change', '.bf_hidden_select', function () {

        bf_hidden_select(jQuery(this));

    });

    function bf_hidden_inputs(this_input) {

        var input       = this_input.find("input");
        var input_value = this_input.find(":checked").val();
        var ids         = input.attr('data-hidden');
        var id          = input.attr('id');

        if (!ids)
            return;

        ids = ids.split(" ");
        ids.forEach(function (entry) {
            jQuery('.' + entry).hide();
        });
        jQuery('.' + input_value).show();

    }

    jQuery(document.body).on('change', '.bf_hidden_input', function () {

        bf_hidden_inputs(jQuery(this));

    });

    jQuery(document.body).on('change', '.bf_hidden_checkbox', function () {

        var input = jQuery(this).find("input");
        var ids = input.attr('bf_hidden_checkbox');
        var id = input.attr('id');

        if (!ids)
            return;

        if (jQuery(input).is(':checked')) {
            ids = ids.split(" ");

            ids.forEach(function (entry) {
                jQuery('#table_row_' + entry).removeClass('hidden');
                jQuery('#table_row_' + entry + ' td .checkbox label').removeClass('hidden');
                jQuery('#table_row_' + entry + ' td #' + entry).removeClass('hidden');
                jQuery('#' + entry).removeClass('hidden');
            });
        } else {
            ids = ids.split(" ");
            ids.forEach(function (entry) {
                jQuery('#table_row_' + entry).addClass('hidden');
            });
        }

    });

    jQuery('.bf_tax_select').live('change', function () {

        var id = jQuery(this).attr('id');
        var taxonomy = jQuery(this).val();
        var taxonomy_default = jQuery("#taxonomy_default_" + id);

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                "action": "buddyforms_update_taxonomy_default",
                "taxonomy": taxonomy,
            },
            success: function (data) {
                if (data != false) {
                    taxonomy_default.val(null).trigger("change");
                    taxonomy_default.select2({placeholder: "Select default term"}).trigger("change");

                    taxonomy_default.html(data);
                }

            },
            error: function () {
                jQuery('<div></div>').dialog({
                    modal: true,
                    title: "Info",
                    open: function() {
                        var markup = 'Something went wrong ;-(sorry)';
                        jQuery(this).html(markup);
                    },
                    buttons: {
                        Ok: function() {
                            jQuery( this ).dialog( "close" );
                        }
                    }
                });
            }
        });

    });

});