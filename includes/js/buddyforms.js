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

    jQuery(document).on( "submit", '#editpost', function( event ) {
        event.preventDefault();

        if (typeof(tinyMCE) != "undefined") {
            tinyMCE.triggerSave();
        }

        var btn = jQuery('.bf-submit').attr('name');
        jQuery("#submitted").val(btn);

        var FormData = jQuery('#editpost').serialize();

        jQuery('.bf_modal').show();

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {"action": "buddyforms_ajax_edit_post", "data": FormData},
            beforeSend :function(){
                jQuery('.bf_modal').show();
            },
            success: function(data){
                jQuery('.bf_modal').hide();
                jQuery('.the_buddyforms_form').replaceWith(data);
                // remove existing editor instance
                tinymce.execCommand('mceRemoveEditor', true, 'editpost_content');

                // init editor for newly appended div
                var init = tinymce.extend( {}, tinyMCEPreInit.mceInit[ 'editpost_content' ] );
                try { tinymce.init( init ); } catch(e){}
            }
        });

        return false;
    });

    jQuery('.bf_view_form').click(function(){

        var form_slug = jQuery(this).attr('href');

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {"action": "buddyforms_list_all_ajax", "form_slug": form_slug },
            success: function(data){
                jQuery('.bf_blub').append(data);
            }
        });

        return false;
    });

});