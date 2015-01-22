jQuery(document).ready(function (){
	var config = {
      '.chosen'     : {
      	width:"50%",
      	no_results_text:'No results match',
      	placeholder_text_multiple:'Select multiple Options',
		placeholder_text_single:'Select an Option',
		search_contains: true,
		allow_single_deselect: true,
      }
   };
   
    for (var selector in config) {
        jQuery(selector).chosen(config[selector]);
    }

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

    jQuery("#editpost").submit(function(){
        // Let's find the input to check
        var btn = jQuery(this).find("input[type=submit]:focus" );
        jQuery("#submitted").val(btn.attr('name'));
    });

});      



  