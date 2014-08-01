jQuery(document).ready(function(jQuery) {

    var hash = window.location.hash;
    if(hash) {
        var activeTab = jQuery('[href=' + hash + ']');
        activeTab && activeTab.tab('show');
    }

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
                    window.location.href = url + '?page=buddyforms_options_page#' + data;
                }

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


    jQuery('#btnAdd').click(function (e) {

        var trigger = jQuery('.buddyforms_notification_trigger').val();
        var href = jQuery(location).attr('href');

        if(trigger == 'none'){
            alert('You need to select a Trigger first.');
            return false;
        }



        var get = [];
        location.search.replace('?', '').split('&').forEach(function (val) {
            split = val.split("=", 2);
            get[split[0]] = split[1];
        });



        var form_slug = get["form_slug"];


        var action = jQuery(this);


        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {"action": "buddyforms_new_mail_notification", "trigger": trigger, 'href': href, 'form_slug': form_slug},
            success: function(data){

                if(data == 0){
                    alert('trigger already exists');
                    return false;
                }

                var nextTab = jQuery('#tabs li').size()+1;

                if(nextTab == 1){
                    jQuery('<ul class="nav nav-tabs" id="tabs"></ul> <div class="tab-content"></div>').appendTo('#mailcontainer');
                }
                // create the tab
                jQuery('<li><a href="#tab'+nextTab+'" data-toggle="tab">'+trigger+'</a></li>').appendTo('#tabs');

                // create the tab content
                jQuery('<div class="tab-pane" id="tab'+nextTab+'">' +data+'</div>').appendTo('.tab-content');

                // make the new tab active
                jQuery('#tabs a:last').tab('show');
            }
        });

        return false;

    });



});

