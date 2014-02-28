jQuery(document).ready(function(jQuery) {    


  // function handleFileSelect(evt) {
    // var files = evt.target.files; // FileList object
// 
	// // Loop through the FileList
	// for (var i = 0, f; f = files[i]; i++) {
// 
		// var reader = new FileReader();
// 	
	 	// // Closure to capture the file information.
		// reader.onload = (function(theFile) {
		// return function(e) {
// 			
			// jQuery.ajax({
				// type: 'POST',
				// url: ajaxurl,
				// data: {"action": "buddyforms_import", "bf_import_forms": e.target.result},
				// success: function(data){
					// alert(data);
				// },
				// error: function() { 
					// alert('Something went wrong.. ;-(sorry)');
				// }
			// });
// 		
		// };
// 		
	// })(f);
// 
	// // Read in the file
	// //reader.readAsDataText(f,UTF-8);
	// //reader.readAsDataURL(f);
	// reader.readAsText(f);
	// }
// }

//document.getElementById('files').addEventListener('change', handleFileSelect, false);




	jQuery('#bf_export').click(function(){

		var buddyforms_export_forms = [];
        jQuery(':checkbox:checked').each(function(i){
          buddyforms_export_forms[i] = jQuery(this).val();
        });
		alert(buddyforms_export_forms);
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "buddyforms_export", "bf_export_form_slugs": buddyforms_export_forms},
			success: function(data){
				var a = document.createElement('a');
				var blob = new Blob([data], {'type':'application\/octet-stream'});

				a.href = window.URL.createObjectURL(blob);
				a.download = 'buddyforms_export.txt';
				a.click();
			},
			error: function() { 
				alert('Something went wrong.. ;-(sorry)');
			}
		});

	});

	if (typeof(Zenbox) !== "undefined") {
		Zenbox.init({
			dropboxID:   "20181572",
			url:         "https://themekraft.zendesk.com",
			tabTooltip:  "Feedback",
			tabColor:    "black",
			tabPosition: "Left",
			hide_tab: true
		});
	}

	jQuery(".form_type").each(function(idx, li) {
    	var form_type_id = jQuery(this).attr('id').slice(0,-2);
    	var form_type_val = jQuery(this).val();
		
		if(jQuery(this).is(':checked')){
			if(form_type_val == 'post_form'){
				jQuery('.bf_element_display_'+form_type_id).show();
			} else {
				jQuery('.bf_element_display_'+form_type_id).hide();
			}
			jQuery('.'+form_type_val+'_'+form_type_id).show();
		} else {
			jQuery('.'+form_type_val+'_'+form_type_id).hide();
		}
    	 
	});
	
	jQuery('.form_type').click(function(){
    	var form_type_id = jQuery(this).attr('id').slice(0,-2);
    	var form_type_val = jQuery(this).val();

    	if(form_type_val == 'post_form'){
    		jQuery('.bf_element_display_'+form_type_id).show('fast');
    		jQuery('.mail_form_'+form_type_id).hide('fast');
    		jQuery('.'+form_type_val+'_'+form_type_id).show('fast');	
    	} else {
    		jQuery('.bf_element_display_'+form_type_id).hide('fast');
    		jQuery('.post_form_'+form_type_id).hide('fast');
    		jQuery('.'+form_type_val+'_'+form_type_id).show('fast');	
    	}
        
    });

	jQuery(".select_posttype").each(function(idx, li) {
		var li_id = jQuery(this).attr('id');
		if(jQuery(this).is(':checked')){
			jQuery('.'+li_id).show();
		} else {
			jQuery('.'+li_id).hide();
		}
	});

	jQuery('.select_posttype').click(function(){
		var li_id = jQuery(this).attr('id');
		if(jQuery(this).is(':checked')){
			jQuery('.'+li_id).show();
		} else {
			jQuery('.'+li_id).hide();
		}
	});
	
	jQuery('.new_form').click(function(){
		var action = jQuery(this);
		var create_new_form_name = jQuery('#create_new_form_name').val();
		var create_new_form_singular_name = jQuery('#create_new_form_singular_name').val();
		
		jQuery(".nav-tabs li a").each(function(idx, li) {
			var li_href = jQuery(this).attr('href');
			
			if(create_new_form_name == ''){
				alert('You need to enter a name for the form!');
				exit; 
			}
			
			if(li_href == '#'+create_new_form_name.toLowerCase()){
				alert('This form already exists please choose a different name!');
				exit; 
			}
		});
		
		
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "buddyforms_add_form", "create_new_form_name": create_new_form_name, "create_new_form_singular_name": create_new_form_singular_name},
			success: function(data){
				window.location.reload(true);
			},
			error: function() { 
				alert('Something went wrong.. ;-(sorry)');
			}
		});
	});
 	jQuery('.dele_form').click(function(){

 		var dele_form_slug = jQuery(this).attr('id');

		var action = jQuery(this); 
		if (confirm('Delete Permanently'))
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "buddyforms_delete_form", "dele_form_slug": dele_form_slug},
				success: function(data){
					window.location.reload(true);
				}
			});
		
		return false;
 	});
	 
	   
	jQuery('.action').click(function(){
		var numItems = jQuery('.list_item').length;
		var action = jQuery(this);
		var args = action.attr('href').split("/");
		var unique = jQuery("#sortable_"+args[1]+' .'+args[0]);
		 
		if(args[2] == 'unique'){
			if (unique.length){
				alert('This element can only be added once into each form');
				return false;
		    }	
		}
		
		jQuery('.loading-animation-new').show(); // Show the animate loading gif while waiting
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "buddyforms_view_form_fields", "post_args": action.attr('href'), 'numItems': numItems},
			success: function(data){
				if(data == 'unique'){
					alert('This element can only be added once into each form');
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
				alert('Something went wrong ;-(sorry)');
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
	 	jQuery('#'+args[0]+'_field_'+args[1]).append('<li class="field_item field_item_'+args[1]+'_'+numItems+'"> new -> <input class="field-sortable" type="text" name="buddyforms_options[buddyforms]['+args[0]+'][form_fields]['+args[1]+'][value][]"> <a href="#" id="'+args[1]+'_'+numItems+'" class="delete_input">X</a> - <a href="#" id="'+args[1]+'">move</a></li>');     
      	
    	return false;
	
	});
	
	jQuery(document).on('click','.delete_input',function() {
		var del_id = jQuery(this).attr('id');
		jQuery(".field_item_" + del_id).remove();
		return false;
	});
	
	jQuery(document).on('mousedown','.list_item',function() {
		jQuery(".element_field_sortable").sortable({
			update: function(event, ui) {
				var testst = jQuery(".element_field_sortable").sortable('toArray');
				for (var key in testst){
				//	alert(key); this needs to be rethinked ;-)
				}
			}
		});
	});
	
	jQuery(document).on('mousedown','.list_item',function() {
	
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
	                    order: itemList.sortable('toArray').toString() // Passes ID's of list items in 1,3,2 format
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

