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

    var editpost_content_val = jQuery('#editpost_content_val').html();
    jQuery('#editpost_content').html(editpost_content_val);

    jQuery(document).on( "submit", '.form_wrapper', function( event ) {

        var form_name   = event.target.id;
        var form_slug   = form_name.split("editpost_")[1];
        var $btn = jQuery(document.activeElement);

       if(!jQuery('#' + form_name).valid()){
           alert('Please check all errors before submiting the form!')
           return false;
       }

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
                        var id = "#bf_post_li_";
                        var li = id + data ;
                        li = li.replace(/\s+/g, '');
                        jQuery(li).remove();
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