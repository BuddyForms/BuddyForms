<?php
/**
 * Create "CGT Options" sub nav menu under the Buddypress main admin nav
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function cgt_create_menu() {
	add_menu_page( 'CGT Options', 'CGT Options', 'edit_posts', 'cgt_options_page', 'cgt_options_content' );
}  
add_action('admin_menu', 'cgt_create_menu');

function my_save_item_order() {
    global $wpdb;
	
	$cgt_options_form_fields_order = get_option('cgt_options_form_fields_order');
    $order = explode(',', $_POST['order']);
    $counter = 0;
    foreach ($order as $item_id) {
        //$wpdb->update($wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $item_id) );
        $cgt_options_form_fields_order[$counter] = $item_id;
        
        $counter++;
    }
	update_option("cgt_options_form_fields_order", $cgt_options_form_fields_order);
    die(1);
}
add_action('wp_ajax_item_sort', 'my_save_item_order');
//add_action('wp_ajax_nopriv_item_sort', 'my_save_item_order');

/**
 * Display the settings page
 *
 * @package BuddyPress Custom Group Types
 * @since 0.2-beta
 */
function cgt_options_content() { 
	 wp_enqueue_script('jQuery');
     wp_enqueue_script('jquery-ui-sortable');
	 
	 
	 
	 
	 
	?>
	<script>
	jQuery(document).ready(function(jQuery) {        
    var itemList = $('#sortable');

    itemList.sortable({
        update: function(event, ui) {
            jQuery('#loading-animation').show(); // Show the animate loading gif while waiting

            opts = {
                url: ajaxurl, // ajaxurl is defined by WordPress and points to /wp-admin/admin-ajax.php
                type: 'POST',
                async: true,
                cache: false,
                dataType: 'json',
                data:{
                    action: 'item_sort', // Tell WordPress how to handle this ajax request
                    order: itemList.sortable('toArray').toString() // Passes ID's of list items in  1,3,2 format
                },
                success: function(response) {
                    jQuery('#loading-animation').hide(); // Hide the loading animation
                    return; 
                },
                error: function(xhr,textStatus,e) {  // This can be expanded to provide more information
                    alert(e);
                    // alert('There was an error saving the updates');
                    jQuery('#loading-animation').hide(); // Hide the loading animation
                    return; 
                }
            };
            jQuery.ajax(opts);
        }
    }); 
});
	
	
	
	
	
	</script>

	<style>
		.accordion{
			float:right;
		}
	</style>
	<div class="wrap">
	<?php 
	$cgt_options_form_fields_order = get_option('cgt_options_form_fields_order');
	print_r($cgt_options_form_fields_order);

	?>	
<ul id="sortable">
   <li id="field_id_44" class="ui-state-default">Item 1</li>
   <li id="field_id_5" class="ui-state-default">Item 2</li>
   <li id="field_id_9" class="ui-state-default">Item 3</li>
</ul>
		






		
		
		
		<?php screen_icon('themes') ?>
		<h2>CGT - General Settings</h2>
	      
		<div id="post-body">
			<div id="post-body-content">            
				<?php cgt_settings_page(); ?>
			</div>
		</div>
	
	</div>
<?php
}

/**
 * Create the option settings page
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function cgt_settings_page() {
    global $bp, $cgt;
    
	// Check that the user is allowed to update options
	if (!current_user_can('manage_options')) {
	    wp_die('You do not have sufficient permissions to access this page.');
	}
	
	if (isset($_POST['submit'])) {
		$cgt_options = $_POST["cgt_options"];
		update_option("cgt_options", $cgt_options);
		?><div id="message" class="updated"><p>CGT Settings Saved :-)</p></div><?php
	}	
	// Get all needed values
			
	$cgt_options = get_option('cgt_options');
		
	//print_r($cgt_options);
		
	// Get all post types
    $args=array(
      '_builtin' => false
    ); 
    $output = 'names'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'
    $post_types=get_post_types($args,$output,$operator); 
     
     foreach($post_types as $key => $value) {
         if(array_key_exists($key, (array)$cgt->new_post_type_slugs)) {
             unset($post_types[$key]);
         }
     }       
    
	$new_group_types = $cgt->post_types;
	
	
	// Form starts
	$form = new Form("cgt_form");
	$form->configure(array(
		//"prevent" => array("bootstrap", "jQuery", "focus"),
		"action" => $_SERVER['REQUEST_URI'],
		"view" => new View_Vertical
	));
	
	$form->addElement(new Element_Hidden("submit", "submit"));
	$form->addElement(new Element_Button('submit','submit',array('id' => 'submit', 'name' => 'submit')));
	
	$form->addElement(new Element_HTML('					
		<div class="tabbable tabs-top">
			<ul class="nav nav-tabs">
				<label for="cgt_form-element-1"></label>
				<li class="active"><a href="#general-settings" data-toggle="tab">General Settings</a></li>
				
			'));
		
		foreach( $cgt_options['existing_post_types'] as $key => $existing_post_types) {
			$form->addElement(new Element_HTML('<li class=""><a href="#'.$existing_post_types.'" data-toggle="tab">'.$existing_post_types.'</a></li>'));
		}
		
		$form->addElement(new Element_HTML('
		</ul>
		</div>	
		<div class="tab-content">
		<div class="subcontainer tab-pane fade in active" id="general-settings">'));
	$form->addElement(new Element_Checkbox("Use existing post types as custom group type::", "cgt_options[existing_post_types][]", $post_types, array('value' => $cgt_options['existing_post_types'])));
	$form->addElement(new Element_HTML('</div>'));
		
		

	foreach( $cgt_options['existing_post_types'] as $key => $existing_post_types) {
    	$form->addElement(new Element_HTML('<div class="subcontainer tab-pane fade in" id="'.$existing_post_types.'">'));
			
			
		$form->addElement(new Element_HTML('
		<div class="accordion" id="accordion_'.$existing_post_types.'">
		  <div class="accordion-group">
		    <div class="accordion-heading">
		      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$existing_post_types.'" href="#accordion_'.$existing_post_types.'_content">
		       '.$existing_post_types.' Label
		      </a>
		    </div>
		    <div id="accordion_'.$existing_post_types.'_content" class="accordion-body collapse in">
		      <div class="accordion-inner">
			')); 
			
				$form->addElement(new Element_Textbox("Name:", "cgt_options[new_group_types][".$existing_post_types."][name]", array('value' => $cgt_options['new_group_types'][$existing_post_types]['name'])));
				$form->addElement(new Element_Textbox("Singular Name:", "cgt_options[new_group_types][".$existing_post_types."][singular_name]", array('value' => $cgt_options['new_group_types'][$existing_post_types]['singular_name'])));
				$form->addElement(new Element_Textbox("Overwrite slug if needed *:", "cgt_options[new_group_types][".$existing_post_types."][slug]", array('value' => $cgt_options['new_group_types'][$existing_post_types]['slug'])));
				
				$form->addElement(new Element_HTML('
		      </div>
		    </div>
		  </div>
 		<div class="accordion-group">
		    <div class="accordion-heading">
		      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$existing_post_types.'" href="#accordion_'.$existing_post_types.'_status">
		       '.$existing_post_types.' Status
		      </a>
		    </div>
		    <div id="accordion_'.$existing_post_types.'_status" class="accordion-body collapse in">
		      <div class="accordion-inner">
			')); 
			
				
				$form->addElement(new Element_Select("Status:", "cgt_options[new_group_types][".$existing_post_types."][status]", array('publish','pending','draft'),array('value' => $cgt_options['new_group_types'][$existing_post_types]['status'])));
				
				$form->addElement(new Element_HTML('
		      </div>
		    </div>
		  </div>		  
		  
		</div>
		  <div id="cgt_forms_builder_'.$existing_post_types.'" class="cgt_forms_builder">
		  <h3>Hier kommt der form builder angerollt ;-)<h3>
		  </div>
		
		'));
			
		
		$form->addElement(new Element_HTML('</div>'));
    
	}
       
	$form->addElement(new Element_HTML('</div>'));			
		

	
	$form->render();
}?>