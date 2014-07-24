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

});      



  