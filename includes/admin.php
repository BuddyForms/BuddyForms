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

function save_item_order() {
    global $wpdb;
	
	$cgt_options = get_option('cgt_options');
    $order = explode(',', $_POST['order']);
    $counter = 0;
	//print_r($order);
	foreach ($order as $item_id) {
        // //$wpdb->update($wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $item_id) );
        // $cgt_options_form_fields_order[$counter] = $item_id;
		//         
		$item_id = explode('/', $item_id);
		
        $cgt_options[$item_id[0]][$item_id[1]][$item_id[2]][$counter] = $item_id[3];
		$counter++;
 	}
	update_option("cgt_options", $cgt_options);
    die();
}
add_action('wp_ajax_item_sort', 'save_item_order');
add_action('wp_ajax_nopriv_item_sort', 'save_item_order');


function view_form_fields($args){
	$field_type = explode('/', $_POST['type']);
	
	$cgt_options = get_option('cgt_options');
	
	if(is_array($args)){
		extract($args);
		$field_type[0] = $type;
		$field_type[1] = $post_type;
	}
	switch ($field_type[0]) {
		case 'Text':
			$form_fields = '<input type="text" name="cgt_options[new_group_types]['.$field_type[1].'][form_fields]['.$key.']" value="'.$value.'">';
			$form_fields .= '<input type="hidden" name="cgt_options[new_group_types]['.$field_type[1].'][form_fields_types]['.$key.']" value="Text">';
		break;
		case 'Textarea':
			$field_value = 'Felder';
			break;
		case 'Link':
			$field_value = 'Felder';
			break;
		case 'Mail':
			$field_value = 'Felder';
			break;
		case 'Dropdown':
			$field_value = 'Felder';
			break;
		case 'Radiobutton':
			$field_value = 'Felder';
			break;
		case 'Checkbox':
			$field_value = 'Felder';
			break;
		case 'Taxonomy':
			$field_value = 'Felder';
			break;
		case 'Hidden':

			break;
		case 'AttachGroupType':
			$field_value = 'Felder';
			break;
	}

	ob_start(); ?>
	<li id="new_group_types/<?php echo $field_type[1] ?>/form_fields_order/<?php echo $key ?>">
	<div class="accordion_fields">
		<div class="accordion-group">
			<div class="accordion-heading"><a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_text" href="#accordion_<?php echo $field_type[1]; ?>_<?php echo $field_type[0].'_'.$key; ?>"><?php echo $field_type[0]; ?></a></div>
			<div id="accordion_<?php echo $field_type[1]; ?>_<?php echo $field_type[0].'_'.$key; ?>" class="accordion-body collapse">
				<div class="accordion-inner">
					<?php echo $form_fields; ?>
				</div>
	    	</div>
		</div>
	<div>
	</li>	
	<?php	
	$field_html = ob_get_contents();
	ob_end_clean();
	
	if(is_array($args)){
		return $field_html;
	}else{
		echo $field_html;
		die();
	}


}
add_action( 'wp_ajax_view_form_fields', 'view_form_fields' );
add_action( 'wp_ajax_nopriv_view_form_fields', 'view_form_fields' );

/**
 * Display the settings page
 *
 * @package BuddyPress Custom Group Types
 * @since 0.2-beta
 */
function cgt_options_content() { ?>
     
     <script type="text/javascript">
	
</script>
     
	<script>
	jQuery(document).ready(function(jQuery) {        
	    var itemList = jQuery('#sortable');
	
		jQuery('.action').click(function(){
			var action = jQuery(this);
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "view_form_fields", "type": action.attr('href')},
				success: function(data){
				jQuery('#sortable').append(data);
			}
			});
			return false;
		});
	
	    itemList.sortable({
	        update: function(event, ui) {
	            jQuery('#loading-animation').show(); // Show the animate loading gif while waiting
	
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
	            alert(itemList.sortable('toArray').toString())
	            
	            jQuery.ajax(opts);
	        }
	    }); 
	});
	</script>

	<style>
		.accordion_sidebar{
			float:right;
		}
		.accordion_fields{
			margin-right: 300px;
		}
		
	</style>
	
	<div class="wrap">
		
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
	
	// Form starts
	$form = new Form("cgt_form");
	$form->configure(array(
		"prevent" => array("bootstrap", "jQuery"),
		"action" => $_SERVER['REQUEST_URI'],
		//"view" => new View_Vertical
	));
	
	wp_enqueue_script('bootstrapjs', plugins_url('PFBC/Resources/bootstrap/js/bootstrap.min.js', __FILE__), array('jquery') );
    wp_enqueue_style('bootstrapcss', plugins_url('PFBC/Resources/bootstrap/css/bootstrap.min.css', __FILE__));
    wp_enqueue_script('jQuery');
    wp_enqueue_script('jquery-ui-sortable'); 
	    
	$form->addElement(new Element_Hidden("submit", "submit"));
	$form->addElement(new Element_Button('submit','submit',array('id' => 'submit', 'name' => 'action','value' => 'submit')));
	
	$form->addElement(new Element_HTML('<div class="tabbable tabs-top"><ul class="nav nav-tabs"><label for="cgt_form-element-1"></label>
		<li class="active"><a href="#general-settings" data-toggle="tab">General Settings</a></li>'));
		
	foreach( $cgt_options['existing_post_types'] as $key => $existing_post_types) {
		$form->addElement(new Element_HTML('<li class=""><a href="#'.$existing_post_types.'" data-toggle="tab">'.$existing_post_types.'</a></li>'));
	}
		
	$form->addElement(new Element_HTML('</ul></div>
		<div class="tab-content"><div class="subcontainer tab-pane fade in active" id="general-settings">'));
	$form->addElement(new Element_Checkbox("Use existing post types as custom group type::", "cgt_options[existing_post_types][]", $post_types, array('value' => $cgt_options['existing_post_types'])));
	$form->addElement(new Element_HTML('</div>'));
	
	foreach( $cgt_options['existing_post_types'] as $key => $existing_post_types) {
		
    	$form->addElement(new Element_HTML('<div class="subcontainer tab-pane fade in" id="'.$existing_post_types.'">'));
			
		$form->addElement(new Element_HTML('
		<div class="accordion_sidebar" id="accordion_'.$existing_post_types.'">
			<div class="accordion-group">
				<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$existing_post_types.'" href="#accordion_'.$existing_post_types.'_content">'.$existing_post_types.' Label</a></div>
				<div id="accordion_'.$existing_post_types.'_content" class="accordion-body collapse in">
					<div class="accordion-inner">')); 
						$form->addElement(new Element_Textbox("Name:", "cgt_options[new_group_types][".$existing_post_types."][name]", array('value' => $cgt_options['new_group_types'][$existing_post_types]['name'])));
						$form->addElement(new Element_Textbox("Singular Name:", "cgt_options[new_group_types][".$existing_post_types."][singular_name]", array('value' => $cgt_options['new_group_types'][$existing_post_types]['singular_name'])));
						$form->addElement(new Element_Textbox("Overwrite slug if needed *:", "cgt_options[new_group_types][".$existing_post_types."][slug]", array('value' => $cgt_options['new_group_types'][$existing_post_types]['slug'])));
						
						$form->addElement(new Element_HTML('
					</div>
		    	</div>
			</div>
	 		<div class="accordion-group">
				<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$existing_post_types.'" href="#accordion_'.$existing_post_types.'_status">'.$existing_post_types.' Status</a></div>
			    <div id="accordion_'.$existing_post_types.'_status" class="accordion-body collapse in">
					<div class="accordion-inner">')); 
						$form->addElement(new Element_Select("Status:", "cgt_options[new_group_types][".$existing_post_types."][status]", array('publish','pending','draft'),array('value' => $cgt_options['new_group_types'][$existing_post_types]['status'])));
					
						$form->addElement(new Element_HTML('
					</div>
				</div>
			</div>		  
			<div class="accordion-group">
				<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$existing_post_types.'" href="#accordion_'.$existing_post_types.'_fields">'.$existing_post_types.' Form Fields</a></div>
			    <div id="accordion_'.$existing_post_types.'_fields" class="accordion-body collapse in">
					<div class="accordion-inner">
						<div id="#idkommtnoch">
							<p><a href="Text/'.$existing_post_types.'" class="action">Text</a></p>
							<p><a href="Textarea/'.$existing_post_types.'" class="action">Textarea</a></p>
							<p><a href="Link/'.$existing_post_types.'" class="action">Link</a></p>
							<p><a href="Mail/'.$existing_post_types.'" class="action">Mail</a></p>
							<p><a href="Dropdown/'.$existing_post_types.'" class="action">Dropdown</a></p>
							<p><a href="Radiobutton/'.$existing_post_types.'" class="action">Radiobutton</a></p>
							<p><a href="Checkbox/'.$existing_post_types.'" class="action">Checkbox</a></p>
							<p><a href="Taxonomy/'.$existing_post_types.'" class="action">Taxonomy</a></p>
							<p><a href="Hidden/'.$existing_post_types.'" class="action">Hidden</a></p>
							<p><a href="AttachGroupType/'.$existing_post_types.'" class="action">AttachGroupType</a></p>
						</div>
					</div>
				</div>
			</div>		  
		</div>
		<div id="cgt_forms_builder_'.$existing_post_types.'" class="cgt_forms_builder">
		<h3>Hier kommt der form builder angerollt ;-)</h3>'));
	
			echo '<pre>';
			print_r($cgt_options);
			echo '</pre>';			

		$form->addElement(new Element_HTML('
		<ul id="sortable">'));
		foreach($cgt_options['new_group_types'][$existing_post_types]['form_fields_order'] as $key => $value) {
			$cgt_v = $cgt_options['new_group_types'][$existing_post_types]['form_fields'][$value];
			$args = Array('key' => $value, 'value' => $cgt_v,'post_type' => $existing_post_types, 'type' => $cgt_options['new_group_types'][$existing_post_types]['form_fields_types'][$value]);
			$form->addElement(new Element_HTML(view_form_fields($args), array('description' => false)));
		}
		$form->addElement(new Element_HTML('</ul></div></div>'));
    
	}
       
	$form->addElement(new Element_HTML('</div>'));			
		
	$form->render();
}?>