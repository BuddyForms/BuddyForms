jQuery(document).ready(function (jQuery) {

    // Check the form type and only display the relevant form setup tabs
    from_setup_form_type(jQuery('#bf-form-type-select').val());

    //
    //Function to show hide form setup tabs navigation
    //
    function from_setup_form_type(value){
        if(value == 'registration') {
            jQuery('.permission, .edit-submissions').hide();
            jQuery('.buddyforms-metabox-hide-if-form-type-register').hide();
        } else {
            jQuery('.permission, .edit-submissions').show();
            jQuery('.buddyforms-metabox-hide-if-form-type-register').show();
        }
        if(value == 'post') {
            jQuery('.buddyforms-metabox-show-if-form-type-post').show();

            jQuery('.bf-after-submission-action option[value=display_form]').show();
            jQuery('.bf-after-submission-action option[value=display_post]').show();
            jQuery('.bf-after-submission-action option[value=display_posts_list]').show();
            jQuery('.create-content').show();

        } else {
            jQuery('#form_post_type').val('bf_submissions');

            jQuery('.buddyforms-metabox-show-if-form-type-post').hide();
            jQuery('.bf-after-submission-action option[value=display_form]').hide();
            jQuery('.bf-after-submission-action option[value=display_post]').hide();
            jQuery('.bf-after-submission-action option[value=display_posts_list]').hide();
            jQuery('.create-content').hide();

            // Select first tab
            jQuery('a[href="#form-submission"]').tab('show');
            jQuery('.activeform-submission').addClass('active');
            jQuery('#form-submission').addClass('active in');

        }
        from_setup_post_type();
        jQuery("#adv-settings input[type='checkbox']").prop("checked", true);
        jQuery("#screen-meta-links").remove();
    }


    // Post Type Select function for the metabox visibility buddyforms-metabox-show-if-post-type-none
    function from_setup_post_type(){

        var post_type = jQuery('#form_post_type').val();

        if(post_type == 'bf_submissions') {
            jQuery('.buddyforms-metabox-show-if-post-type-none').hide();
        } else {
            jQuery('.buddyforms-metabox-show-if-post-type-none').show();
        }
    }

    // On Change kisterner for the post type select
    jQuery(document.body).on('change', '#form_post_type', function () {
        from_setup_post_type();
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
