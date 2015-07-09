jQuery(document).ready(function (){


    jQuery(".bf-select2").select2({
        placeholder: "Select an option"
    });

    jQuery('.remove_attachment').click(function(){
 		var delete_attachment_id = jQuery(this).attr('id');
 		var delete_attachment_href = jQuery(this).attr('href');
        var action = jQuery(this);

		if (confirm('Delete Permanently'))
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "buddyforms_delete_attachment", "delete_attachment_id": delete_attachment_id, "delete_attachment_href": delete_attachment_href},
				success: function(data){
                    jQuery( "#"+data ).remove();
				}
			});

		return false;

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

    var editpost_content_val = jQuery('#editpost_content_val').html();
    jQuery('#editpost_content').html(editpost_content_val);






    //jQuery('#editpost_hierarchicals').ajaxForm({
    //    data: {
    //        action : 'buddyforms_ajax_process_edit_post'
    //    },
    //    dataType: 'json',
    //    beforeSubmit: function(formData, jqForm, options) {
    //        console.log(formData);
    //        console.log(jqForm);
    //        console.log(options);
    //        // optionally process data before submitting the form via AJAX
    //    },
    //    success : function(responseText, statusText, xhr, $form) {
    //        alert('asd');
    //        // code that's executed when the request is processed successfully
    //    },
    //    error: function (request, status, error) {
    //        console.log(request);
    //        console.log(status);
    //        console.log(error);
    //    }
    //});



    //jQuery(document).on( "submit", '.form_wrapper', function( event ) {
    //
    //    var queryString = jQuery('#editpost_hierarchicals').formSerialize();
    //
    //    alert(queryString);
    //    jQuery.post('buddyforms_ajax_process_edit_post', queryString);
    //
    //    beforeSerialize: function($form, options) {
    //        alert('sdadas');
    //        // return false to cancel submit
    //    }
    //
    //    return false;
    //
    //});


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


        var validator = jQuery('#' + form_name ).validate();
        //alert(validator.form()); new form validation in process !

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
                        if(i == 'form_notice'){
                            //jQuery('#message').append(val);
                            //jQuery('.the_buddyforms_form_'+ form_slug).append(val);
                            //jQuery('.the_buddyforms_form_'+ form_slug + ' .form-actions').append(val);
                            jQuery('#form_message_' + form_slug).html(val);
                            //jQuery( val ).insertBefore( '.the_buddyforms_form_'+ form_slug );
                        } else if(i == 'form_remove'){
                            jQuery('.the_buddyforms_form_'+ form_slug + ' .form_wrapper').remove();
                        } else {
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