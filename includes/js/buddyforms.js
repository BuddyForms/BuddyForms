
jQuery(document).ready(function (){
	
    jQuery('#options_to_connect a').on('click',function(event){   
		event.preventDefault();
		var post_id = jQuery(this).parent().attr('class'); 
		jQuery.ajax({  
			type: "POST",                  
			url:  ajaxurl,  
			data: {"action": "buddyforms_form_ajax", "post_id": post_id}, 
			success: function (msg) {      
				jQuery('#TB_ajaxContent').html(msg);
			},
			error: function () {                  
				alert('Error');                    
			}  
		});           
	});       
});      

