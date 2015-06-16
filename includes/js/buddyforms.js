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

    jQuery(document).on( "submit", '.form_wrapper', function( event ) {

        var form_name   = event.target.id;
        var form_slug   = form_name.split("editpost_")[1];
        var submit_type = jQuery('#' + form_name + ' #' + form_slug).attr('name');

        event.preventDefault();

        if (typeof(tinyMCE) != "undefined") {
            tinyMCE.triggerSave();
        }

        jQuery('#editpost_' + form_slug + ' #submitted').val(submit_type);

        var FormData = jQuery('#editpost_'+form_slug).serialize();

        jQuery('.the_buddyforms_form_'+ form_slug + ' .form_wrapper .bf_modal').show();

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {"action": "buddyforms_ajax_process_edit_post", "data": FormData},
            beforeSend :function(){
                jQuery('.the_buddyforms_form_'+ form_slug + ' .form_wrapper .bf_modal').show();
            },
            success: function(data){
                event.preventDefault();
                //jQuery(".bf-select2").select2({
                //    placeholder: "Select an option",
                //    allowClear: true,
                //});
                jQuery('.the_buddyforms_form_'+ form_slug + ' .form_wrapper .bf_modal').hide();
                jQuery('.the_buddyforms_form_'+ form_slug).replaceWith(data);
                // remove existing editor instance
                tinymce.execCommand('mceRemoveEditor', true, 'editpost_content');

                // init editor for newly appended div
                var init = tinymce.extend( {}, tinyMCEPreInit.mceInit[ 'editpost_content' ] );
                try { tinymce.init( init ); } catch(e){}
            }
        });

        return false;
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
    jQuery(document).on( "click", '.bf_edit_post', function( event ) {
        var post_id = jQuery(this).attr('id');

        event.preventDefault();

        if (typeof(tinyMCE) != "undefined") {
            tinyMCE.triggerSave();
        }

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {"action": "buddyforms_ajax_edit_post", "post_id": post_id},
            beforeSend :function(){
                jQuery('.buddyforms_posts_list .bf_modal').show();
            },
            error: function(data){
                alert(data);
            },
            success: function(data){
                jQuery('.buddyforms_posts_list .bf_modal').hide();
                jQuery('.buddyforms_posts_list').replaceWith(data);
                // remove existing editor instance
                tinymce.execCommand('mceRemoveEditor', true, 'editpost_content');

                // init editor for newly appended div
                var init = tinymce.extend( {}, tinyMCEPreInit.mceInit[ 'editpost_content' ] );
                try { tinymce.init( init ); } catch(e){}
            }
        });

        return false;
    });
    jQuery(document).on( "click", '.bf_delete_post', function( event ) {
        var post_id = jQuery(this).attr('id');

        if (confirm('Delete Permanently')){
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {"action": "buddyforms_ajax_delete_post", "post_id": post_id },
                error: function(data){
                    alert(data);
                },
                success: function(data){
                    if(isNaN(data)){
                        alert(data);
                    } else {
                        jQuery( "#bf_post_li_"+data ).remove();
                    }
                }
            });
        } else {
            return false;
        }
        return false;
    });

});