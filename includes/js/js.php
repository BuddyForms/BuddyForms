<?php
function buddyforms_dynamic_js(){

    ob_start();
?>
    jQuery(document).on( "submit", '.form_wrapper', function( event ) {
    event.preventDefault();

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

    //if (typeof(tinyMCE) != "undefined") {
    //    tinyMCE.triggerSave();
    //}


    //var FormData = jQuery(this).serialize();

    var FormData = jQuery('#' + form_name).serialize();
    alert(FormData);
    //jQuery('.the_buddyforms_form_'+ form_slug + ' .form_wrapper .bf_modal').show();

    jQuery.ajax({
    type: 'POST',
    dataType: "json",
    url: ajaxurl,
    data: {"action": "buddyforms_ajax_process_edit_post", "data": FormData},
    beforeSend :function(){
    alert('asd');
    jQuery('.the_buddyforms_form_'+ form_slug + ' .form_wrapper .bf_modal').show();
    },

    success: function(data){
    alert(data);
    jQuery('.the_buddyforms_form_'+ form_slug + ' .form_wrapper .bf_modal').hide();
    //jQuery('.the_buddyforms_form_'+ form_slug).replaceWith(data);


    jQuery.each(data, function(i, val) {
    if(i == 'form_notice'){
    //jQuery('#message').append(val);
    //jQuery('.the_buddyforms_form_'+ form_slug).append(val);
    //jQuery('.the_buddyforms_form_'+ form_slug + ' .form-actions').append(val);
    jQuery( val ).insertBefore( '.the_buddyforms_form_'+ form_slug );
    } else {
    jQuery('input[name="' + i + '"]').val(val);
    }
    });

    // remove existing editor instance
    //tinymce.execCommand('mceRemoveEditor', true, 'editpost_content');
    //
    //// init editor for newly appended div
    //var init = tinymce.extend( {}, tinyMCEPreInit.mceInit[ 'editpost_content' ] );
    //try { tinymce.init( init ); } catch(e){}
    },
    error: function (request, status, error) {
    alert(request.responseText);
    }
    });

    return false;
    });


<?php
   $tmp = ob_get_contents();
    ob_clean();

return $tmp;
} ?>