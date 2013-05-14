jQuery(document).ready(function(jQuery) {       
	
	jQuery(document).on('click','.delete',function() {
		
		var del_id = jQuery(this).attr('id');
		var action = jQuery(this); 
		if (confirm('Delete Permanently'))
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "cpt4bp_item_delete", "post_args": action.attr('href')},
				success: function(data){
					jQuery("." + del_id).remove();
				}
			});
		
		return false;
	});
	
	jQuery('.action').click(function(){
		var numItems = jQuery('.list_item').length;
	
		var action = jQuery(this);
		jQuery('.loading-animation-new').show(); // Show the animate loading gif while waiting
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "cpt4bp_view_form_fields", "post_args": action.attr('href'), 'numItems': numItems},
			success: function(data){
				var myvar = action.attr('href');
				var arr = myvar.split('/');
			jQuery('.sortable_' + arr[1]).append(data);
			jQuery('.info_' + arr[1]).hide();
			jQuery('.loading-animation-new').hide('slow'); // Show the animate loading gif while waiting
		
			}
		});
		return false;
	});
	
	jQuery(document).on('mousedown','.hero-unit div',function() {
	
		itemList = jQuery(this).closest('.sortable').sortable({
	    	
	    	update: function(event, ui) {
				jQuery('.loading-animation-order').show(); // Show the animate loading gif while waiting
	    	    
			    opts = {
	                url: ajaxurl,
	                type: 'POST',
	                async: true,
	                cache: false,
	                dataType: 'json',
	                data:{
	                    action: 'item_sort', // Tell WordPress how to handle this ajax request
	                    order: itemList.sortable('toArray').toString() // Passes ID's of list items in  1,3,2 format
	                },
	                success: function(response) {
	                	
	                    jQuery('.loading-animation-order').hide('slow'); // Hide the loading animation
	                    var testst = itemList.sortable('toArray');
	                   for (var key in testst){
	                   	jQuery("input[id='" + testst[key] + "']").val(key); 
	                   }
	                    return; 
	                },
	                error: function(xhr,textStatus,e) {  // This can be expanded to provide more information
	                    //alert(e);
	                    alert('There was an error saving the order');
	                    jQuery('.loading-animation-order').hide('slow'); // Hide the loading animation
		                    return; 
		                }
		            };
		            
		   			jQuery.ajax(opts);
		       }
	       });
	   });
	
});
	