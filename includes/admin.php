<?php
/**
 * Create "CPT4BP Options" sub nav menu under the Buddypress main admin nav
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function cpt4bp_create_menu() {
	add_menu_page( 'CPT4BP Options', 'CPT4BP Options', 'edit_posts', 'cpt4bp_options_page', 'cpt4bp_options_content' );
}  
add_action('admin_menu', 'cpt4bp_create_menu');

function cpt4bp_save_item_order() {
    global $wpdb;
	
	$cpt4bp_options = get_option('cpt4bp_options');
    $order = explode(',', $_POST['order']);
    $counter = 0;
	
	foreach ($order as $item_id) {
    	$item_id = explode('/', $item_id);
	    $cpt4bp_options[$item_id[0]][$item_id[1]][$item_id[2]][$item_id[3]][$item_id[4]] = $counter;
		$counter++;
 	}
	
	update_option("cpt4bp_options", $cpt4bp_options);
    die();
}
add_action('wp_ajax_item_sort', 'cpt4bp_save_item_order');
add_action('wp_ajax_nopriv_item_sort', 'cpt4bp_save_item_order');

function cpt4bp_item_delete(){
	$post_args = explode('/', $_POST['post_args']);
	
	$cpt4bp_options = get_option('cpt4bp_options');
	
	
	unset( $cpt4bp_options[$post_args[0]][$post_args[1]][form_fields][$post_args[3]] );
    
	update_option("cpt4bp_options", $cpt4bp_options);
    die();
}
add_action('wp_ajax_cpt4bp_item_delete', 'cpt4bp_item_delete');
add_action('wp_ajax_nopriv_cpt4bp_item_delete', 'cpt4bp_item_delete');

function my_taxonomies(){
	$args=array(
     'public'   => true,
    //  '_builtin' => true
      
    ); 
    $output = 'names'; // or objects
    $operator = 'and'; // 'and' or 'or'
    $taxonomies=get_taxonomies($args,$output,$operator); 
	
	return $taxonomies;
}

function cpt4bp_view_form_fields($args){
	$post_args = explode('/', $_POST['post_args']);
	$numItems = $_POST['numItems'];
	$cpt4bp_options = get_option('cpt4bp_options');
	
	if(is_array($args)){
		extract($args);
		$post_args[0] = $field_type;
		$post_args[1] = $post_type;
	}
	
	if($field_id == '')
		$field_id = $mod5 = substr(md5(time() * rand()), 0, 10);;
		
	if($field_position =='')
		$field_position = $numItems;
	
	$form_fields_new = Array();

	$form_field_display		= new Element_Checkbox("Display:","cpt4bp_options[bp_post_types][".$post_args[1]."][form_fields][".$field_id."][display]",array(''),array('value' => $cpt4bp_options['bp_post_types'][$post_args[1]][form_fields][$field_id][display]));
	$form_field_required	= new Element_Checkbox("Required:","cpt4bp_options[bp_post_types][".$post_args[1]."][form_fields][".$field_id."][required]",array(''),array('value' => $cpt4bp_options['bp_post_types'][$post_args[1]][form_fields][$field_id][required]));
	
	$form_fields_new[0] 	= new Element_Textbox("Name:", "cpt4bp_options[bp_post_types][".$post_args[1]."][form_fields][".$field_id."][name]", array('value' => $cpt4bp_options['bp_post_types'][$post_args[1]][form_fields][$field_id][name]));
	$form_fields_new[1] 	= new Element_Textbox("Discription:", "cpt4bp_options[bp_post_types][".$post_args[1]."][form_fields][".$field_id."][discription]", array('value' => $cpt4bp_options['bp_post_types'][$post_args[1]][form_fields][$field_id][discription]));
	$form_fields_new[2] 	= new Element_Hidden("cpt4bp_options[bp_post_types][".$post_args[1]."][form_fields][".$field_id."][type]", $post_args[0]);
	$form_fields_new[3] 	= new Element_Hidden("cpt4bp_options[bp_post_types][".$post_args[1]."][form_fields][".$field_id."][order]", $field_position, array('id' => 'bp_post_types/' . $post_args[1] .'/form_fields/'. $field_id .'/order'));


	switch ($post_args[0]) {

		case 'Link':
			$form_fields_new[4] 	= new Element_Select("Target:", "cpt4bp_options[bp_post_types][".$post_args[1]."][form_fields][".$field_id."][target]", array('_self','_blank'), array('value' => $cpt4bp_options['bp_post_types'][$post_args[1]][form_fields][$field_id][target]))	;
			break;
		case 'Dropdown':
			$form_fields_new[4] 	= new Element_Textbox("Values: <smal>value 1, value 2, ... </smal>", "cpt4bp_options[bp_post_types][".$post_args[1]."][form_fields][".$field_id."][Values]", array('value' => $cpt4bp_options['bp_post_types'][$post_args[1]][form_fields][$field_id][Values]));
			break;
		case 'Radiobutton':
			$form_fields_new[4] 	= new Element_Textbox("Values: <smal>value 1, value 2, ... </smal>", "cpt4bp_options[bp_post_types][".$post_args[1]."][form_fields][".$field_id."][Values]", array('value' => $cpt4bp_options['bp_post_types'][$post_args[1]][form_fields][$field_id][Values]));
			break;
		case 'Checkbox':
			$form_fields_new[4] 	= new Element_Textbox("Values: <smal>value 1, value 2, ... </smal>", "cpt4bp_options[bp_post_types][".$post_args[1]."][form_fields][".$field_id."][Values]", array('value' => $cpt4bp_options['bp_post_types'][$post_args[1]][form_fields][$field_id][Values]));
			break;
		case 'Taxonomy':
			$taxonomies = my_taxonomies();
			$form_fields_new[4] 	= new Element_Select("Taxonomy:", "cpt4bp_options[bp_post_types][".$post_args[1]."][form_fields][".$field_id."][taxonomy]", $taxonomies, array('value' => $cpt4bp_options['bp_post_types'][$post_args[1]][form_fields][$field_id][taxonomy]));
			$form_fields_new[5] 	= new Element_Checkbox("Multiple:","cpt4bp_options[bp_post_types][".$post_args[1]."][form_fields][".$field_id."][multiple]",array(''),array('value' => $cpt4bp_options['bp_post_types'][$post_args[1]][form_fields][$field_id][multiple]));
			break;
		case 'Hidden':
			$form_fields_new[1] 	= new Element_Textbox("Values: <smal>value 1, value 2, ... </smal>", "cpt4bp_options[bp_post_types][".$post_args[1]."][form_fields][".$field_id."][Values]", array('value' => $cpt4bp_options['bp_post_types'][$post_args[1]][form_fields][$field_id][Values]));
			break;
		case 'AttachGroupType':
		    $form_fields_new[4] 	= new Element_Select("Target:", "cpt4bp_options[bp_post_types][".$post_args[1]."][form_fields][".$field_id."][target]", $cpt4bp_options['selected_post_types'], array('value' => $cpt4bp_options['bp_post_types'][$post_args[1]][form_fields][$field_id][target]))	;
		break;
	}

	ob_start(); ?>
	<li id="bp_post_types/<?php echo $post_args[1] ?>/form_fields/<?php echo $field_id ?>/order" class="list_item <?php echo $field_id ?>">
	<div class="accordion_fields">
		<div class="accordion-group">
			<div class="accordion-heading"> 
				
				<div class="accordion-heading-options">
				<b>Delete: </b> <a class="delete" id="<?php echo $field_id ?>" href="bp_post_types/<?php echo $post_args[1] ?>/form_fields/<?php echo $field_id ?>/order">X</a>
				</div>
				
				<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_text" href="#accordion_<?php echo $post_args[1]; ?>_<?php echo $post_args[0].'_'.$field_id; ?>">
				<b>Type: </b> <?php echo $post_args[0]; ?> 
				<br><b>Name: </b> <?php echo $cpt4bp_options['bp_post_types'][$post_args[1]][form_fields][$field_id][name]; ?>
				</a>
				
			</div>
						
			<div id="accordion_<?php echo $post_args[1]; ?>_<?php echo $post_args[0].'_'.$field_id; ?>" class="accordion-body collapse">
				<div class="accordion-inner">
					<div class="cpt4bp_field_options">
						<?php 
						
						echo '<div class="cpt4bp_field_label">' . $form_field_display->getLabel() . '</div>';
						echo '<div class="cpt4bp_form_field">' . $form_field_display->render() . '</div>';
						
						echo '<div class="cpt4bp_field_label">' . $form_field_required->getLabel() . '</div>';
						echo '<div class="cpt4bp_form_field">' . $form_field_required->render() . '</div>';
						
						?>
					</div>
					
					<?php 	
					//print_r($form_fields_new);
					foreach ($form_fields_new as $key => $value) {
					
						echo '<div class="cpt4bp_field_label">' . $form_fields_new[$key]->getLabel() . '</div>';
						
						echo '<div class="cpt4bp_form_field">' . $form_fields_new[$key]->render() . '</div>';

					}
					?>
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
add_action( 'wp_ajax_cpt4bp_view_form_fields', 'cpt4bp_view_form_fields' );
add_action( 'wp_ajax_nopriv_cpt4bp_view_form_fields', 'cpt4bp_view_form_fields' );

/**
 * Display the settings page
 *
 * @package BuddyPress Custom Group Types
 * @since 0.2-beta
 */
function cpt4bp_options_content() { 
	session_start(); ?>
     
	<script type="text/javascript">

	jQuery(document).ready(function(jQuery) {        
	    
		jQuery(".delete").click(function(){
			
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
			
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "cpt4bp_view_form_fields", "post_args": action.attr('href'), 'numItems': numItems},
				success: function(data){
					var myvar = action.attr('href');
					var arr = myvar.split('/');
				jQuery('.sortable_' + arr[1]).append(data);
				//alert('.info_' + arr[1]);
				jQuery('.info_' + arr[1]).hide();
				}
			});
			return false;
		});
	
		jQuery('.cpt4bp_forms_builder div').mousedown(function(){
	  		
	  		itemList = jQuery(this).closest('.sortable').sortable({
	        	
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
		                    var testst = itemList.sortable('toArray');
		                   for (var key in testst){
		                   	// alert(key + ': ' + testst[key]);
		                   	jQuery("input[id='" + testst[key] + "']").val(key); 
		                   }
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

	});
	
	</script>

	<style>

.popover.left .arrow {
top: 50%;
right: -11px;
margin-top: -11px;
border-left-color: #999;
border-left-color: rgba(0, 0, 0, 0.25);
border-right-width: 0;
}

		.cpt4bp_field_options{
			float: right;
			margin-top: 10px;
			margin-right: 15px;
		}
	
		.accordion-heading-options {
			float: right;
			margin-top: 10px;
			margin-right: 15px;
		}
	
		.accordion_sidebar {
			float: right;
			width: 20%;
		}
		.accordion_fields{
			margin-right: 22%;
		}
		
	</style>
	
	<div class="wrap">
		<?php screen_icon('themes') ?>
		<h2>CPT4BP - General Settings</h2>
		<div id="post-body">
			<div id="post-body-content">            
				<?php cpt4bp_settings_page(); ?>
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
function cpt4bp_settings_page() {
    global $bp, $cpt4bp;
    
	// Check that the user is allowed to update options
	if (!current_user_can('manage_options')) {
	    wp_die('You do not have sufficient permissions to access this page.');
	}	
	
	if (isset($_POST['submit'])) {
		$cpt4bp_options = $_POST["cpt4bp_options"];
		update_option("cpt4bp_options", $cpt4bp_options);
		?><div id="message" class="updated"><p>CPT4BP Settings Saved :-)</p></div><?php
	}
	
	// Get all needed values
	$cpt4bp_options = get_option('cpt4bp_options');
		
	// Get all post types
    $args=array(
		'public' => true,
		'show_ui' => true
    ); 
    $output = 'names'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'
    $post_types=get_post_types($args,$output,$operator); 
   
	// Form starts
	$form = new Form("cpt4bp_form");
	$form->configure(array(
		"prevent" => array("bootstrap", "jQuery"),
		"action" => $_SERVER['REQUEST_URI'],
		"view" => new View_Inline
	));
	
	wp_enqueue_script('bootstrapjs', plugins_url('PFBC/Resources/bootstrap/js/bootstrap.min.js', __FILE__), array('jquery') );
    wp_enqueue_style('bootstrapcss', plugins_url('PFBC/Resources/bootstrap/css/bootstrap.min.css', __FILE__));
    wp_enqueue_script('jQuery');
    wp_enqueue_script('jquery-ui-sortable'); 
	    
	
	$form->addElement(new Element_HTML('<br><div class="tabbable tabs-top"><ul class="nav nav-tabs"><label for="cpt4bp_form-element-1"></label>
		<li class="active"><a href="#general-settings" data-toggle="tab">General Settings</a></li>'));
		
	if(is_array($cpt4bp_options['selected_post_types'])){
		foreach( $cpt4bp_options['selected_post_types'] as $key => $selected_post_types) {
			$form->addElement(new Element_HTML('<li class=""><a href="#'.$selected_post_types.'" data-toggle="tab">'.$selected_post_types.'</a></li>'));
		}
	}	
	$form->addElement(new Element_HTML('</ul></div>
		<div class="tab-content"><div class="subcontainer tab-pane fade in active" id="general-settings">'));
		
				$form->addElement(new Element_HTML('
			<div class="accordion_sidebar" id="accordion_'.$selected_post_types.'">
				<div class="accordion-group">
					<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$selected_post_types.'" href="#accordion_'.$selected_post_types.'_save">Save</a></div>
					<div id="accordion_'.$selected_post_types.'_save" class="accordion-body">
						<div class="accordion-inner">')); 
							
							$form->addElement(new Element_Hidden("submit", "submit"));
							$form->addElement(new Element_Button('submit','submit',array('id' => 'submit', 'name' => 'action','value' => 'submit')));
								
							$form->addElement(new Element_HTML('
						</div>
			    	</div>
				</div>
			</div>'));
		
		
				$form->addElement(new Element_HTML('
				<div class="hero-unit">
  <h3>Global Setup</h3>
'));
$form->addElement(new Element_Checkbox("<p>Select the <b>PostTypes</b> you want to make available in <b>BuddyPress</b> ;-)</p>", "cpt4bp_options[selected_post_types][]", $post_types, array('value' => $cpt4bp_options['selected_post_types'])));
	
				$form->addElement(new Element_HTML(' 
</div>
			'));
		
	$form->addElement(new Element_HTML('</div>'));
	
	if(is_array($cpt4bp_options['selected_post_types'])){
		foreach( $cpt4bp_options['selected_post_types'] as $key => $selected_post_types) {
			
	    	$form->addElement(new Element_HTML('<div class="subcontainer tab-pane fade in" id="'.$selected_post_types.'">'));
				
			$form->addElement(new Element_HTML('
			<div class="accordion_sidebar" id="accordion_'.$selected_post_types.'">
				<div class="accordion-group">
					<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$selected_post_types.'" href="#accordion_'.$selected_post_types.'_save">Save</a></div>
					<div id="accordion_'.$selected_post_types.'_save" class="accordion-body">
						<div class="accordion-inner">')); 
							
							$form->addElement(new Element_Hidden("submit", "submit"));
							$form->addElement(new Element_Button('submit','submit',array('id' => 'submit', 'name' => 'action','value' => 'submit')));
								
							$form->addElement(new Element_HTML('
						</div>
			    	</div>
				</div>
				<div class="accordion-group">
					<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$selected_post_types.'" href="#accordion_'.$selected_post_types.'_content">'.$selected_post_types.' Label</a></div>
					<div id="accordion_'.$selected_post_types.'_content" class="accordion-body collapse">
						<div class="accordion-inner">')); 
							$form->addElement(new Element_Textbox("Name:", "cpt4bp_options[bp_post_types][".$selected_post_types."][name]", array('value' => $cpt4bp_options['bp_post_types'][$selected_post_types]['name'])));
							$form->addElement(new Element_Textbox("Singular Name:", "cpt4bp_options[bp_post_types][".$selected_post_types."][singular_name]", array('value' => $cpt4bp_options['bp_post_types'][$selected_post_types]['singular_name'])));
							$form->addElement(new Element_Textbox("Overwrite slug if needed *:", "cpt4bp_options[bp_post_types][".$selected_post_types."][slug]", array('value' => $cpt4bp_options['bp_post_types'][$selected_post_types]['slug'])));
							
							$form->addElement(new Element_HTML('
						</div>
			    	</div>
				</div>
		 		<div class="accordion-group">
					<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$selected_post_types.'" href="#accordion_'.$selected_post_types.'_status">'.$selected_post_types.' Status</a></div>
				    <div id="accordion_'.$selected_post_types.'_status" class="accordion-body collapse">
						<div class="accordion-inner">')); 
							$form->addElement(new Element_Select("Status:", "cpt4bp_options[bp_post_types][".$selected_post_types."][status]", array('publish','pending','draft'),array('value' => $cpt4bp_options['bp_post_types'][$selected_post_types]['status'])));
						
							$form->addElement(new Element_HTML('
						</div>
					</div>
				</div>	
				<div class="accordion-group">
					<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$selected_post_types.'" href="#accordion_'.$selected_post_types.'_group_options">'.$selected_post_types.' Groups Options</a></div>
				    <div id="accordion_'.$selected_post_types.'_group_options" class="accordion-body collapse">
						<div class="accordion-inner">')); 
							$form->addElement(new Element_Checkbox("Redirect to Group?", "cpt4bp_options[bp_post_types][".$selected_post_types."][groups][connect]", array("Yes. I want to create a group for each post of this post type."), array('value' => $cpt4bp_options['bp_post_types'][$selected_post_types]['groups'][connect])));
							$form->addElement(new Element_HTML('<br>'));
							
							$form->addElement(new Element_Select("Redirect Options:", "cpt4bp_options[bp_post_types][".$selected_post_types."][groups][redirect]", array('to group','to post'),array('value' => $cpt4bp_options['bp_post_types'][$selected_post_types]['groups'][redirect])));
							$form->addElement(new Element_Select("Privacy Options:", "cpt4bp_options[bp_post_types][".$selected_post_types."][groups][privacy]", array('public','private','hidden'),array('postHTML' => 'Which members of this group are allowed to invite others?', 'value' => $cpt4bp_options['bp_post_types'][$selected_post_types]['groups'][privacy])));
							$form->addElement(new Element_Select("Group Invitations", "cpt4bp_options[bp_post_types][".$selected_post_types."][groups][invitations]", array('All group members','Group admins and mods only','Group admins only'),array('value' => $cpt4bp_options['bp_post_types'][$selected_post_types]['groups'][invitations])));
							$form->addElement(new Element_HTML('<br>'));
							
							$form->addElement(new Element_Checkbox("Enable Group Forum", "cpt4bp_options[bp_post_types][".$selected_post_types."][groups][forum]", array("Yes. I want this groups to have a forum."), array('value' => $cpt4bp_options['bp_post_types'][$selected_post_types]['groups'][forum])));
							$form->addElement(new Element_HTML('
						</div>
					</div>
				</div>		  
				<div class="accordion-group">
					<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$selected_post_types.'" href="#accordion_'.$selected_post_types.'_fields">'.$selected_post_types.' Form Fields</a></div>
				    <div id="accordion_'.$selected_post_types.'_fields" class="accordion-body collapse">
						<div class="accordion-inner">
							<div id="#idkommtnoch">
								<p><a href="Text/'.$selected_post_types.'" class="action">Text</a></p>
								<p><a href="Textarea/'.$selected_post_types.'" class="action">Textarea</a></p>
								<p><a href="Link/'.$selected_post_types.'" class="action">Link</a></p>
								<p><a href="Mail/'.$selected_post_types.'" class="action">Mail</a></p>
								<p><a href="Dropdown/'.$selected_post_types.'" class="action">Dropdown</a></p>
								<p><a href="Radiobutton/'.$selected_post_types.'" class="action">Radiobutton</a></p>
								<p><a href="Checkbox/'.$selected_post_types.'" class="action">Checkbox</a></p>
								<p><a href="Taxonomy/'.$selected_post_types.'" class="action">Taxonomy</a></p>
								<p><a href="Hidden/'.$selected_post_types.'" class="action">Hidden</a></p>
								<p><a href="AttachGroupType/'.$selected_post_types.'" class="action">AttachGroupType</a></p>
							</div>
						</div>
					</div>
				</div>		  
			</div>
			<div id="cpt4bp_forms_builder_'.$selected_post_types.'" class="cpt4bp_forms_builder">'));
				    
			$sortArray = array(); 
			
			if(!empty($cpt4bp_options['bp_post_types'][$selected_post_types]['form_fields'] )){
				foreach($cpt4bp_options['bp_post_types'][$selected_post_types]['form_fields'] as $key => $array) { 
		        	$sortArray[$key] = $array['order']; 
		    	} 
				array_multisort($sortArray, SORT_ASC, SORT_NUMERIC, $cpt4bp_options['bp_post_types'][$selected_post_types]['form_fields']); 
			}
	
			$form->addElement(new Element_HTML('
			<ul id="sortable_'. $selected_post_types .'" class="sortable sortable_'. $selected_post_types .'">'));
			if(is_array($cpt4bp_options['bp_post_types'][$selected_post_types]['form_fields'])){
				
				foreach($cpt4bp_options['bp_post_types'][$selected_post_types]['form_fields'] as $field_id => $sad) {
					if($cpt4bp_options['bp_post_types'][$selected_post_types]['form_fields'][$field_id]['name'] != ''){
							
						$field_position = $cpt4bp_options['bp_post_types'][$selected_post_types]['form_fields'][$field_id]['order'];
						
						//$field_value = $cpt4bp_options['bp_post_types'][$selected_post_types]['form_fields'][$field_id];
						
						$args = Array('field_position' => $field_position, 'field_id' => $field_id, 'field_value' => $field_value,'post_type' => $selected_post_types, 'field_type' => $cpt4bp_options['bp_post_types'][$selected_post_types]['form_fields'][$field_id][type]);
						
						$form->addElement(new Element_HTML(cpt4bp_view_form_fields($args)));
					}
					
				}
			} else {
				
			$form->addElement(new Element_HTML('<div class="popover left info_'.$selected_post_types.'">
            <div class="arrow"></div>
            <h3 class="popover-title">Popover left</h3>
            <div class="popover-content">
              <p>Sed posuere consectetur est at lobortis. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.</p>
            </div>
          </div>'));
	    	
			}
			$form->addElement(new Element_HTML('</ul></div></div>'));
	    
		}	
	}
	
       
	$form->addElement(new Element_HTML('</div>'));			
		
	$form->render();
}
?>