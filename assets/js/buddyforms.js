jQuery(function() {
    var validator = jQuery("#editpost_tryps").submit(function() {
        // update underlying textarea before submit validation
        tinyMCE.triggerSave();
    }).validate({
        ignore: "",
        rules: {
            editpost_title: {
                required: true,
                minlength: 2
            },
            editpost_content: {
                required: true,
                minlength: 10
            },
            featured_image: {
                required: true,
            }
        },
        messages: {
            editpost_title: {
                required: "PLEASE ENTER A TITLE",
                minlength: jQuery.validator.format("At least {0} characters requyyired!")
            },
            editpost_content: {
                required: "PLEASE ENTER A editpost_content",
                minlength: jQuery.validator.format("At least {0} characters editpost_content!")
            }
        },
        errorPlacement: function(label, element) {
            // position error label after generated textarea
            if (element.is("textarea")) {
                jQuery("#editpost_title").prev().css('color','red');
                label.insertBefore("#editpost_content");
            } else {
                label.insertAfter(element)
            }
        }
    });
    validator.focusInvalid = function() {
        // put focus on tinymce on submit validation
        if (this.settings.focusInvalid) {
            try {
                var toFocus = $(this.findLastActive() || this.errorList.length && this.errorList[0].element || []);
                if (toFocus.is("textarea")) {
                    tinyMCE.get(toFocus.attr("id")).focus();
                } else {
                    toFocus.filter(":visible").focus();
                }
            } catch (e) {
                // ignore IE throwing errors when focusing hidden elements
            }
        }
    }
});

jQuery(document).ready(function (){

    jQuery(".bf-select2").select2({
        placeholder: "Select an option"
    });

    jQuery('.bf_datetime').datetimepicker({
        controlType: 'select',
        timeFormat: 'hh:mm tt'
    });

    jQuery('.bf_price_date').datepicker({
        controlType: 'select',
        dateFormat: 'yy-mm-dd'
    });

    var bf_status = jQuery('select[name=status]').val();

    if(bf_status == 'future'){
        jQuery('.bf_datetime_wrap').show();
    } else {
        jQuery('.bf_datetime_wrap').hide();
    }

    jQuery('select[name=status]').change(function(){
        var bf_status = jQuery(this).val();
        if(bf_status == 'future'){
            jQuery('.bf_datetime_wrap').show();
        } else {
            jQuery('.bf_datetime_wrap').hide();
        }
    });

    //var validator = jQuery("#editpost_tryps").submit(function() {
    //    // update underlying textarea before submit validation
    //    tinyMCE.triggerSave();
    //}).validate({
    //    rules: {
    //        editpost_title: {
    //            required: true,
    //            minlength: 2
    //        },
    //        editpost_content: {
    //            required: true,
    //            minlength: 10
    //        }
    //    },
    //    messages: {
    //        editpost_title: {
    //            required: "PLEASE ENTER A TITLE",
    //            minlength: jQuery.validator.format("At least {0} characters requyyired!")
    //        },
    //        editpost_content: {
    //            required: "PLEASE ENTER A editpost_content",
    //            minlength: jQuery.validator.format("At least {0} characters editpost_content!")
    //        }
    //    },
    //    errorPlacement: function(label, element) {
    //        // position error label after generated textarea
    //        if (element.is("textarea")) {
    //            label.insertAfter(element.next());
    //        } else {
    //            label.insertAfter(element)
    //        }
    //    }
    //});
    //validator.focusInvalid = function() {
    //    // put focus on tinymce on submit validation
    //    if (this.settings.focusInvalid) {
    //        try {
    //            var toFocus = $(this.findLastActive() || this.errorList.length && this.errorList[0].element || []);
    //            if (toFocus.is("textarea")) {
    //                tinyMCE.get(toFocus.attr("id")).focus();
    //            } else {
    //                toFocus.filter(":visible").focus();
    //            }
    //        } catch (e) {
    //            // ignore IE throwing errors when focusing hidden elements
    //        }
    //    }
    //}


    var editpost_content_val = jQuery('#editpost_content_val').html();
    jQuery('#editpost_content').html(editpost_content_val);

    jQuery(document).on( "submit", '.form_wrapper', function( event ) {
        var form_name   = event.target.id;
        var form_slug   = form_name.split("editpost_")[1];
        var $btn = jQuery(document.activeElement);

        if (
            /* there is an activeElement at all */
        $btn.length &&
            /* it's a child of the form */
        jQuery('#' + form_name).has($btn) &&
            /* it's really a submit element */
        $btn.is('button[type="submit"], input[type="submit"], input[type="image"]') &&
            /* it has a "name" attribute */
        $btn.is('[name]')) {
            var submit_type = $btn.attr('name');
        }
        jQuery('#' + form_name + ' #submitted').val(submit_type);


        if(jQuery('#' + form_name + ' input[name="ajax"]').val() != 'off'){

            event.preventDefault();

            var FormData = jQuery('#' + form_name).serialize();

            jQuery.ajax({
                type: 'POST',
                dataType: "json",
                url: ajaxurl,
                data: {"action": "buddyforms_ajax_process_edit_post", "data": FormData},
                beforeSend :function(){
                    jQuery('.the_buddyforms_form_'+ form_slug + ' .form_wrapper .bf_modal').show();
                },
                success: function(data){

                    jQuery('.the_buddyforms_form_'+ form_slug + ' .form_wrapper .bf_modal').hide();

                    jQuery.each(data, function(i, val) {

                        switch(i) {
                            case 'form_notice':
                                jQuery('#form_message_' + form_slug).html(val);
                                break;
                            case 'form_remove':
                                jQuery('.the_buddyforms_form_'+ form_slug + ' .form_wrapper').remove();
                                break;
                            case 'form_actions':
                                jQuery('.the_buddyforms_form_'+ form_slug + ' .form-actions').html(val);
                                break;
                            default:
                                jQuery('input[name="' + i + '"]').val(val);
                        }

                    });

                },
                error: function (request, status, error) {
                    alert(request.responseText);
                    jQuery('.the_buddyforms_form_'+ form_slug + ' .form_wrapper .bf_modal').hide();
                }
            });

            return false;
        }
        return true;
    });

    //jQuery('.bf_view_form').click(function(){
    //
    //    var form_slug = jQuery(this).attr('href');
    //
    //    jQuery.ajax({
    //        type: 'POST',
    //        url: ajaxurl,
    //        data: {"action": "buddyforms_list_all_ajax", "form_slug": form_slug },
    //        success: function(data){
    //            jQuery('.bf_blub').append(data);
    //        }
    //    });
    //
    //    return false;
    //});
    //jQuery(document).on( "click", '.bf_edit_post', function( event ) {
    //    var post_id = jQuery(this).attr('id');
    //
    //    alert('sdds');
    //
    //    event.preventDefault();
    //
    //    //if (typeof(tinyMCE) != "undefined") {
    //    //    tinyMCE.triggerSave();
    //    //}
    //
    //    jQuery.ajax({
    //        type: 'POST',
    //        url: ajaxurl,
    //        data: {"action": "buddyforms_ajax_edit_post", "post_id": post_id},
    //        beforeSend :function(){
    //            jQuery('.buddyforms_posts_list .bf_modal').show();
    //        },
    //        error: function(data){
    //            alert(data);
    //        },
    //        success: function(data){
    //            jQuery('.buddyforms_posts_list .bf_modal').hide();
    //            jQuery('.buddyforms_posts_list').replaceWith(data);
    //            // remove existing editor instance
    //            tinymce.execCommand('mceRemoveEditor', true, 'editpost_content');
    //
    //            // init editor for newly appended div
    //            var init = tinymce.extend( {}, tinyMCEPreInit.mceInit[ 'editpost_content' ] );
    //            try { tinymce.init( init ); } catch(e){}
    //        }
    //    });
    //
    //    return false;
    //});

    jQuery(document).on( "click", '.bf_delete_post', function( event ) {
        var post_id = jQuery(this).attr('id');

        if (confirm('Delete Permanently')){
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {"action": "buddyforms_ajax_delete_post", "post_id": post_id },
                success: function(data){
                    if(isNaN(data)){
                        alert(data);
                    } else {
                        jQuery( "#bf_post_li_"+data ).remove();
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