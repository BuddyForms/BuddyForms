jQuery(document).ready(function(jQuery) {



    jQuery( "#post" ).addClass( "form-inline" );

    var location = window.location;
    var hash = window.location.hash;
    if(hash)
        hash && jQuery('.showme a[href="' + hash + '"]').tab('show');

    jQuery('.showme a').click(function (e) {
        jQuery(this).tab('show');
        window.location.hash = this.hash;
    });

	jQuery('.new_form').click(function(){

		var action = jQuery(this);
		var create_new_form_name                    = jQuery('[name="create_new_form_name"]').val();
        var create_new_form_singular_name           = jQuery('[name="create_new_form_singular_name"]').val();
        var create_new_form_post_type               = jQuery('[name="create_new_form_post_type"]').val();
        var create_new_form_attached_page           = jQuery('[name="create_new_form_attached_page"]').val();
        var create_new_page                         = jQuery('[name="create_new_page"]').val();

        if( create_new_form_attached_page === ''){
            create_new_form_attached_page           = jQuery('[name="create_new_page"]').val();
        }

        if( create_new_form_name === ''){
            jQuery('[name="create_new_form_name"]').removeClass('bf-ok');
            jQuery('[name="create_new_form_name"]').addClass('bf-error');
        } else {
            jQuery('[name="create_new_form_name"]').removeClass('bf-error');
            jQuery('[name="create_new_form_name"]').addClass('bf-ok');
        }

        if( create_new_form_singular_name === ''){
            jQuery('[name="create_new_form_singular_name"]').removeClass('bf-ok');
            jQuery('[name="create_new_form_singular_name"]').addClass('bf-error');
        } else {
            jQuery('[name="create_new_form_singular_name"]').removeClass('bf-error');
            jQuery('[name="create_new_form_singular_name"]').addClass('bf-ok');
        }

        if( create_new_form_post_type === 'none'){
            jQuery('[name="create_new_form_post_type"]').removeClass('bf-ok');
            jQuery('[name="create_new_form_post_type"]').addClass('bf-error');
        } else {
            jQuery('[name="create_new_form_post_type"]').removeClass('bf-error');
            jQuery('[name="create_new_form_post_type"]').addClass('bf-ok');
        }

        if( create_new_form_attached_page === ''){
            jQuery('[name="create_new_form_attached_page"]').removeClass('bf-ok');
            jQuery('[name="create_new_form_attached_page"]').addClass('bf-error');
        } else {
            jQuery('[name="create_new_form_attached_page"]').removeClass('bf-error');
            jQuery('[name="create_new_form_attached_page"]').addClass('bf-ok');
        }

        var create_new_form_status                  = jQuery('[name="create_new_form_status"]').val();
        var create_new_form_comment_status          = jQuery('[name="create_new_form_comment_status"]').val();

        var create_new_form_revision                = jQuery('[name="create_new_form_revision"]').val();
        var create_new_form_admin_bar               = jQuery('[name="create_new_form_admin_bar"]').val();
        var create_new_form_edit_link               = jQuery('[name="create_new_form_edit_link"]').val();


		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "buddyforms_add_form",
                "create_new_form_name"                      : create_new_form_name,
                "create_new_form_singular_name"             : create_new_form_singular_name,
                "create_new_form_attached_page"             : create_new_form_attached_page,
                "create_new_page"                           : create_new_page,
                "create_new_form_post_type"                 : create_new_form_post_type,
                "create_new_form_status"                    : create_new_form_status,
                "create_new_form_comment_status"            : create_new_form_comment_status,
            },
			success: function(data){
                if(data != false){
                    var url = window.location.href;
                    url = url.slice( 0, url.indexOf('?') );
                    window.location.href = url + '?page=buddyforms_options_page#subcon' + data;
                }

			},
			error: function() {
				alert('Something went wrong.. ;-(sorry)');
			}
		});
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

				data = data.replace('accordion-body collapse','accordion-body in collapse');

				var myvar = action.attr('href');
				var arr = myvar.split('/');
				jQuery('.sortable_' + arr[1]).append(data);
				jQuery('.info_' + arr[1]).hide();
				jQuery('.loading-animation-new').hide('slow'); // Show the animate loading gif while waiting
				//jQuery('.sortable_' + arr[1]).collapse();
				update_list_item_number();

				jQuery('#buddyforms_form_elements').removeClass('closed');
				jQuery("html, body").animate({ scrollTop: jQuery('#buddyforms_form_elements ul li:last').offset().top }, 1000);



			},
			error: function() {
				alert('Something went wrong ;-(sorry)');
			}
		});
		return false;
	});
    //
	//jQuery(".accordion-body").on("shown", function () {
	//	var selected = jQuery(this);
	//	var collapseh = jQuery(".collapse .in").height();
	//	jQuery("html, body").animate({ scrollTop: jQuery('#title1').offset().top }, 1000);
	//	jQuery.scrollTo(selected, 500, {
	//		offset: -(collapseh)
	//	});
	//});

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
	 	jQuery('#'+args[0]+'_field_'+args[1]).append('<li class="field_item field_item_'+args[1]+'_'+numItems+'"> + <input class="field-sortable" type="text" name="buddyforms_options[form_fields]['+args[1]+'][value][]"> <a href="#" id="'+args[1]+'_'+numItems+'" class="delete_input">X</a> - <a href="#" id="'+args[1]+'">move</a></li>');

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

	function update_list_item_number() {
		jQuery(".buddyforms_forms_builder ul").each(function() {
			jQuery(this).children("li").each(function(t) {
				jQuery(this).find("td.field_order .circle").first().html(t + 1)
			})
		})
	}
	update_list_item_number();

	jQuery(document).on('mousedown','.list_item',function() {
		itemList = jQuery(this).closest('.sortable').sortable({
	    	update: function(event, ui) {
				update_list_item_number();
		       }
	       });
	   });

	function update_list_item_number_mail() {
		jQuery(".panel-mail-notifications .wp-list-table").each(function(t) {
			jQuery(this).find("td.field_order .circle").first().html(t + 1)
		})
	}
	update_list_item_number_mail();

    jQuery('#mail_notification_add_new').click(function (e) {

        var trigger = jQuery('.buddyforms_notification_trigger').val();

        if(trigger == 'none'){
            alert('You have to select a trigger first.');
            return false;
        }

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {"action": "buddyforms_new_mail_notification", "trigger": trigger},
            success: function(data){

                if(data == 0){
                    alert('trigger already exists');
                    return false;
                }

				jQuery('#mailcontainer').append(data);
            }
        });
        return false;
    });

    jQuery(".checkall").click(function(){

        if (jQuery("input[type='checkbox']").prop("checked")) {
            jQuery(':checkbox').prop('checked', false);
            jQuery(this).text(admin_text.check);
        } else {
            jQuery(':checkbox').prop('checked', true);
            jQuery(this).text(admin_text.uncheck);
        }

    });

});

