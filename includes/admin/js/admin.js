jQuery(document).ready(function(jQuery) {    
	   
	jQuery('.action').click(function(){
		var numItems = jQuery('.list_item').length;
		var action = jQuery(this);
		var args = action.attr('href').split("/")
		var unique = jQuery("#sortable_"+args[1]+' .'+args[0]);
		 
		if(args[2] == 'unique'){
			if (unique.length){
				alert('This element can only be added ones into each form');
				return false;
		    }	
		}
		
		jQuery('.loading-animation-new').show(); // Show the animate loading gif while waiting
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "buddyforms_view_form_fields", "post_args": action.attr('href'), 'numItems': numItems},
			success: function(data){
				if(data == false){
					alert('This element can only be added ones into each form');
					jQuery('.loading-animation-new').hide('slow'); // Show the animate loading gif while waiting
					return false;
				}
				
				var myvar = action.attr('href');
				var arr = myvar.split('/');
				jQuery('.sortable_' + arr[1]).append(data);
				jQuery('.info_' + arr[1]).hide();
				jQuery('.loading-animation-new').hide('slow'); // Show the animate loading gif while waiting
		
			},
			error: function() { 
				alert('so nich junge');
			}
		});
		return false;
	});
	
	jQuery(document).on('click','.delete',function() {
		
		var del_id = jQuery(this).attr('id');
		var action = jQuery(this); 
		if (confirm('Delete Permanently'))
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "buddyforms_item_delete", "post_args": action.attr('href')},
				success: function(data){
					jQuery("." + del_id).remove();
				}
			});
		
		return false;
	});
	
	jQuery(document).on('click','.add_input',function() {
	
		var action = jQuery(this);
		var args = action.attr('href').split("/");
	 	var	numItems = jQuery('#'+args[0]+'_field_'+args[1]+' li').size();
	 	numItems = numItems + 1;
	 	jQuery('#'+args[0]+'_field_'+args[1]).append('<li class="field_item field_item_'+args[1]+'_'+numItems+'"><div class="buddyforms_field_label">Entry '+numItems+'</div><input class="field-sortable" type="text" name="buddyforms_options[bp_post_types]['+args[0]+'][form_fields]['+args[1]+'][value][]"> <a href="#" id="'+args[1]+'_'+numItems+'" class="delete_field">X</a></li>');     
      	
    	return false;
	
	});
	
	jQuery(document).on('click','.delete_field',function() {
		var del_id = jQuery(this).attr('id');
		jQuery(".field_item_" + del_id).remove();
		return false;
	});
	
	jQuery(document).on('mousedown','.field_item',function() {
		//jQuery('.sortable').sortable('cancel');
		itemList_field = jQuery(this).closest('.element_field_sortable').sortable();
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
	