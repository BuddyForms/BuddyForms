<?php

/**
 * Create "buddyforms Options" nav menu
 *
 * @package buddyforms
 * @since 0.1-beta
 */
function buddyforms_create_menu() {
	
	add_menu_page( 'BuddyForms', 'BuddyForms', 'edit_posts', 'buddyforms_options_page', 'buddyforms_options_content' );
	
}  
add_action('admin_menu', 'buddyforms_create_menu');

/**
 * Ajax call back function to save the new form elements order
 *
 * @package buddyforms
 * @since 0.1-beta
 */
function buddyforms_save_item_order() {
    global $wpdb;
	
	$buddyforms_options = get_option('buddyforms_options');
    $order = explode(',', $_POST['order']);
    $counter = 0;
	
	foreach ($order as $item_id) {
    	$item_id = explode('/', $item_id);
	    $buddyforms_options[$item_id[0]][$item_id[1]][$item_id[2]][$item_id[3]][$item_id[4]] = $counter;
		$counter++;
 	}
	
	update_option("buddyforms_options", $buddyforms_options);
    die();
}
add_action('wp_ajax_item_sort', 'buddyforms_save_item_order');
add_action('wp_ajax_nopriv_item_sort', 'buddyforms_save_item_order');

/**
 * Ajax call back function to delete a form element
 *
 * @package buddyforms
 * @since 0.1-beta
 */
function buddyforms_item_delete(){
	$post_args = explode('/', $_POST['post_args']);
	
	$buddyforms_options = get_option('buddyforms_options');
		
	unset( $buddyforms_options[$post_args[0]][$post_args[1]][form_fields][$post_args[3]] );
    
	update_option("buddyforms_options", $buddyforms_options);
    die();
}
add_action('wp_ajax_buddyforms_item_delete', 'buddyforms_item_delete');
add_action('wp_ajax_nopriv_buddyforms_item_delete', 'buddyforms_item_delete');

/**
 * get all taxonomies
 *
 * @package buddyforms
 * @since 0.1-beta
 */
function buddyforms_taxonomies(){
	$args=array(
     'public'   => true,
    ); 
    $output = 'names'; // or objects
    $operator = 'and'; // 'and' or 'or'
    $taxonomies=get_taxonomies($args,$output,$operator); 
	
	return $taxonomies;
}

/**
 * View Form Fields
 *
 * @package buddyforms
 * @since 0.1-beta
 */
function buddyforms_view_form_fields($args){
	global $buddyforms;
	
	$buddyforms_options	= get_option('buddyforms_options');
	
	$post_args		= explode('/', $_POST['post_args']);
	
	if(isset($post_args[0]))
		$field_type	= $post_args[0];
	
	if(isset($post_args[1]))
		$post_type = $post_args[1];
	
	if(isset($_POST['numItems']))
		$numItems = $_POST['numItems'];
	
	if(is_array($args))
		extract($args);
	
	if($field_id == '')
		$field_id = $mod5 = substr(md5(time() * rand()), 0, 10);;
		
	if($field_position =='')
		$field_position = $numItems;
	
	$form_fields = Array();
	
	$buddyforms[hooks][form_element] = apply_filters('buddyforms_form_element_hooks',$buddyforms[hooks][form_element],$post_type,$field_id);
	
	 if (count ($buddyforms[hooks][form_element]) == 1 )
	 $msg =  "<br><i>If you see this text, your theme do not support BuddyForms.<br>
	 You can use get_post_meta(sanitize_title('name')); <br>
	 or get_post_meta('slug'); if slug <br> 
	 in your theme to get the custom post meta value. <br>
	 Or you write a smal fuction to add your hooks here. <br>
	 For this please see the <a target='_blank' href='http://buddyforms.com'>documentation</a><br>
	 how to creat and use hooks with BuddyForms</i>";
	 
	$form_fields['right'][display]			= new Element_Select("Display?".$msg, "buddyforms_options[bp_post_types][".$post_type."][form_fields][".$field_id."][display]", $buddyforms[hooks][form_element], array('value' => $buddyforms_options['bp_post_types'][$post_type][form_fields][$field_id][display]));
	$form_fields['right'][display_name]		= new Element_Checkbox("Display name?","buddyforms_options[bp_post_types][".$post_type."][form_fields][".$field_id."][display_name]",array(''),array('value' => $buddyforms_options['bp_post_types'][$post_type][form_fields][$field_id][display_name]));
	$form_fields['right'][required]			= new Element_Checkbox("Required?","buddyforms_options[bp_post_types][".$post_type."][form_fields][".$field_id."][required]",array(''),array('value' => $buddyforms_options['bp_post_types'][$post_type][form_fields][$field_id][required]));
							
	$form_fields['left'][name] 				= new Element_Textbox("Name:", "buddyforms_options[bp_post_types][".$post_type."][form_fields][".$field_id."][name]", array('value' => $buddyforms_options['bp_post_types'][$post_type][form_fields][$field_id][name]));
	$form_fields['left'][slug]		 		= new Element_Textbox("Slug: <i>optional '_name' will create a hidden post meta field</i>", "buddyforms_options[bp_post_types][".$post_type."][form_fields][".$field_id."][slug]", array('value' => $buddyforms_options['bp_post_types'][$post_type][form_fields][$field_id][slug]));
	$form_fields['left'][description] 		= new Element_Textbox("Description:", "buddyforms_options[bp_post_types][".$post_type."][form_fields][".$field_id."][description]", array('value' => $buddyforms_options['bp_post_types'][$post_type][form_fields][$field_id][description]));
	
	$form_fields['left'][type] 				= new Element_Hidden("buddyforms_options[bp_post_types][".$post_type."][form_fields][".$field_id."][type]", $field_type);
	$form_fields['left'][order] 			= new Element_Hidden("buddyforms_options[bp_post_types][".$post_type."][form_fields][".$field_id."][order]", $field_position, array('id' => 'bp_post_types/' . $post_type .'/form_fields/'. $field_id .'/order'));
	
	switch ($field_type) {

		case 'Link':
			$form_fields['left'][target] 	= new Element_Select("Target:", "buddyforms_options[bp_post_types][".$post_type."][form_fields][".$field_id."][target]", array('_self','_blank'), array('value' => $buddyforms_options['bp_post_types'][$post_type][form_fields][$field_id][target]))	;
			break;
		case 'Dropdown':
			$form_fields['left'][value] 	= new Element_Textbox("Values: <smal>value 1, value 2, ... </smal>", "buddyforms_options[bp_post_types][".$post_type."][form_fields][".$field_id."][value]", array('value' => $buddyforms_options['bp_post_types'][$post_type][form_fields][$field_id][value]));
			break;
		case 'Radiobutton':
			$form_fields['left'][value] 	= new Element_Textbox("Values: <smal>value 1, value 2, ... </smal>", "buddyforms_options[bp_post_types][".$post_type."][form_fields][".$field_id."][value]", array('value' => $buddyforms_options['bp_post_types'][$post_type][form_fields][$field_id][value]));
			break;
		case 'Checkbox':
			$form_fields['left'][value] 	= new Element_Textbox("Values: <smal>value 1, value 2, ... </smal>", "buddyforms_options[bp_post_types][".$post_type."][form_fields][".$field_id."][value]", array('value' => $buddyforms_options['bp_post_types'][$post_type][form_fields][$field_id][value]));
			break;
		case 'Taxonomy':
			$taxonomies = buddyforms_taxonomies();
			$form_fields['left'][taxonomy] 	= new Element_Select("Taxonomy:", "buddyforms_options[bp_post_types][".$post_type."][form_fields][".$field_id."][taxonomy]", $taxonomies, array('value' => $buddyforms_options['bp_post_types'][$post_type][form_fields][$field_id][taxonomy]));
			$form_fields['left'][multiple] 	= new Element_Checkbox("Multiple:","buddyforms_options[bp_post_types][".$post_type."][form_fields][".$field_id."][multiple]",array(''),array('value' => $buddyforms_options['bp_post_types'][$post_type][form_fields][$field_id][multiple]));
			break;
		case 'Hidden':
			$form_fields['left'][value] 	= new Element_Hidden("buddyforms_options[bp_post_types][".$post_type."][form_fields][".$field_id."][value]", $buddyforms_options['bp_post_types'][$post_type][form_fields][$field_id][value]);
			break;
		default:
			$form_fields = apply_filters('buddyforms_form_element_add_field',$form_fields,$post_type,$field_type,$field_id,$buddyforms_options['bp_post_types'][$post_type][form_fields][$field_id][AttachGroupType]);
			break;

	}

	ob_start(); ?>
	<li id="bp_post_types/<?php echo $post_type ?>/form_fields/<?php echo $field_id ?>/order" class="list_item <?php echo $field_id ?>">
	<div class="accordion_fields">
		<div class="accordion-group">
			<div class="accordion-heading"> 
				
				<div class="accordion-heading-options">
				<b>Delete: </b> <a class="delete" id="<?php echo $field_id ?>" href="bp_post_types/<?php echo $post_type ?>/form_fields/<?php echo $field_id ?>/order">X</a>
				</div>
				
				<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_text" href="#accordion_<?php echo $post_type; ?>_<?php echo $field_type.'_'.$field_id; ?>">
				<b>Type: </b> <?php echo $field_type; ?> 
				<br><b>Name: </b> <?php echo $buddyforms_options['bp_post_types'][$post_type][form_fields][$field_id][name]; ?>
				</a>
				
			</div>
						
			<div id="accordion_<?php echo $post_type; ?>_<?php echo $field_type.'_'.$field_id; ?>" class="accordion-body collapse">
				<div class="accordion-inner">
					<div class="buddyforms_field_options">
						<?php 
						foreach ($form_fields['right'] as $key => $value) {
						
							echo '<div class="buddyforms_field_label">' . $form_fields['right'][$key]->getLabel() . '</div>';
							echo '<div class="buddyforms_form_field">' . $form_fields['right'][$key]->render() . '</div>';

						}
						?>
					</div>
					
					<?php 	
					foreach ($form_fields['left'] as $key => $value) {
					
						echo '<div class="buddyforms_field_label">' . $form_fields['left'][$key]->getLabel() . '</div>';
						echo '<div class="buddyforms_form_field">' . $form_fields['left'][$key]->render() . '</div>';

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
add_action( 'wp_ajax_buddyforms_view_form_fields', 'buddyforms_view_form_fields' );
add_action( 'wp_ajax_nopriv_buddyforms_view_form_fields', 'buddyforms_view_form_fields' );

/**
 * Display the settings page
 *
 * @package buddyforms
 * @since 0.2-beta
 */
function buddyforms_options_content() { 
	session_start(); ?>
	
	<div class="wrap">
		<?php screen_icon('themes') ?>
		<h2>buddyforms - General Settings</h2>
		<div id="post-body">
			<div id="post-body-content">  
				<?php buddyforms_settings_page(); ?>
			</div>
		</div>
	</div>
<?php
}

/**
 * Create the option settings page
 *
 * @package buddyforms
 * @since 0.1-beta
 */
function buddyforms_settings_page() {
    global $bp, $buddyforms;
    
	// Check that the user is allowed to update options
	if (!current_user_can('manage_options')) {
	    wp_die('You do not have sufficient permissions to access this page.');
	}	
	
	if (isset($_POST['submit'])) {
		$buddyforms_options = $_POST["buddyforms_options"];
		update_option("buddyforms_options", $buddyforms_options);
		?><div id="message" class="updated"><p>buddyforms Settings Saved :-)</p></div><?php
	}
	
	// Get all needed values
	$buddyforms_options = get_option('buddyforms_options');
		
	// Get all post types
    $args=array(
		'public' => true,
		'show_ui' => true
    ); 
    $output = 'names'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'
    $post_types=get_post_types($args,$output,$operator); 
   
	// Form starts
	$form = new Form("buddyforms_form");
	$form->configure(array(
		"prevent" => array("bootstrap", "jQuery"),
		"action" => $_SERVER['REQUEST_URI'],
		"view" => new View_Inline
	));
	
	$form->addElement(new Element_HTML('<br><div class="tabbable tabs-top"><ul class="nav nav-tabs"><label for="buddyforms_form-element-1"></label>
		<li class="active"><a href="#general-settings" data-toggle="tab">General Settings</a></li>'));
		
	if(is_array($buddyforms_options['selected_post_types'])){
		foreach( $buddyforms_options['selected_post_types'] as $key => $selected_post_types) {
			$tabname = $buddyforms['bp_post_types'][$selected_post_types]['name'];
			if(empty($tabname))
				$tabname = $selected_post_types;
				
			$form->addElement(new Element_HTML('<li class=""><a href="#'.$selected_post_types.'" data-toggle="tab">'.$tabname.'</a></li>'));
		}
	}	
	$form->addElement(new Element_HTML('</ul></div>
		<div class="tab-content"><div class="subcontainer tab-pane fade in active" id="general-settings">'));
		
			$form->addElement(new Element_HTML('
			<div class="accordion_sidebar" id="accordion_'.$selected_post_types.'">
				<div class="accordion-group">
					<div class="accordion-heading"><p class="accordion-toggle">Publish</p></div>
					<div id="accordion_'.$selected_post_types.'_save" class="accordion-body">
						<div class="accordion-inner">')); 
							$form->addElement(new Element_Hidden("submit", "submit"));
							$form->addElement(new Element_Button('submit','submit',array('id' => 'submit', 'name' => 'action','value' => 'Save')));
								
							$form->addElement(new Element_HTML('
						</div>
			    	</div>
				</div>
			</div>'));
		
		
			$form->addElement(new Element_HTML('
			<div class="hero-unit">
			  <h3>Global Setup</h3>
			'));
			$form->addElement(new Element_Checkbox("<p>Select the <b>PostTypes</b> you want to make available in <b>BuddyPress</b> ;-)</p>", "buddyforms_options[selected_post_types][]", $post_types, array('value' => $buddyforms_options['selected_post_types'])));
			$form->addElement(new Element_HTML('</div></div>'));
		
			if(is_array($buddyforms_options['selected_post_types'])){
				foreach( $buddyforms_options['selected_post_types'] as $key => $selected_post_types) {
					
			    	$form->addElement(new Element_HTML('<div class="subcontainer tab-pane fade in" id="'.$selected_post_types.'">'));
						
					$form->addElement(new Element_HTML('
					<div class="accordion_sidebar" id="accordion_'.$selected_post_types.'">
						<div class="accordion-group">
							<div class="accordion-heading"><p class="accordion-toggle">Save</p></div>
							<div id="accordion_'.$selected_post_types.'_save" class="accordion-body">
								<div class="accordion-inner">')); 
									$form->addElement(new Element_Hidden("submit", "submit"));
									$form->addElement(new Element_Button('submit','submit',array('id' => 'submit', 'name' => 'action','value' => 'Save')));
										
									$form->addElement(new Element_HTML('
								</div>
					    	</div>
						</div>'));
						$form->addElement(new Element_HTML('
						<div class="accordion-group">
							<div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$selected_post_types.'" href="#accordion_'.$selected_post_types.'_content">Label</p></div>
							<div id="accordion_'.$selected_post_types.'_content" class="accordion-body collapse">
								<div class="accordion-inner">')); 
									$form->addElement(new Element_Textbox("Name:", "buddyforms_options[bp_post_types][".$selected_post_types."][name]", array('value' => $buddyforms_options['bp_post_types'][$selected_post_types]['name'])));
									$form->addElement(new Element_Textbox("Singular Name:", "buddyforms_options[bp_post_types][".$selected_post_types."][singular_name]", array('value' => $buddyforms_options['bp_post_types'][$selected_post_types]['singular_name'])));
									$form->addElement(new Element_Textbox("Overwrite slug if needed *:", "buddyforms_options[bp_post_types][".$selected_post_types."][slug]", array('value' => $buddyforms_options['bp_post_types'][$selected_post_types]['slug'])));
									
									$form->addElement(new Element_HTML('
								</div>
					    	</div>
						</div>
				 		<div class="accordion-group">
							<div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$selected_post_types.'" href="#accordion_'.$selected_post_types.'_status">Post Control</p></div>
						    <div id="accordion_'.$selected_post_types.'_status" class="accordion-body collapse">
								<div class="accordion-inner">')); 
									$form->addElement(new Element_Select("Status:", "buddyforms_options[bp_post_types][".$selected_post_types."][status]", array('publish','pending','draft'),array('value' => $buddyforms_options['bp_post_types'][$selected_post_types]['status'])));
									$form->addElement( new Element_Checkbox("Featured Image:","buddyforms_options[bp_post_types][".$selected_post_types."][featured_image][required]",array('Required'),array('value' => $buddyforms_options['bp_post_types'][$selected_post_types]['featured_image']['required'])));
					
									$form->addElement(new Element_HTML('
								</div>
							</div>
						</div>'));	
					
					 apply_filters('buddyforms_admin_settings_sidebar_metabox',$form, $selected_post_types);
					
					$form->addElement(new Element_HTML('
						<div class="accordion-group">
							<div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$selected_post_types.'" href="#accordion_'.$selected_post_types.'_fields"> Form Elements</p></div>
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
										
										'));
										$form = apply_filters('buddyforms_add_form_element_in_sidebar', $form, $selected_post_types);
									$form->addElement(new Element_HTML('
									</div>
								</div>
							</div>
						</div>		  
					</div>
					<div id="buddyforms_forms_builder_'.$selected_post_types.'" class="buddyforms_forms_builder">'));
					$form->addElement(new Element_HTML('<div class="hero-unit">
						<h3>Post Type General Settings</h3>'));    
					$form->addElement(new Element_HTML('<p class="loading-animation-order alert alert-success">Save new order <i class="icon-ok"></i></p>'));
					$form->addElement(new Element_HTML('<div class="loading-animation-new alert alert-success">Loade new element <i class="icon-ok"></i></div>
					'));
					
					$sortArray = array(); 
					
					if(!empty($buddyforms_options['bp_post_types'][$selected_post_types]['form_fields'] )){
						foreach($buddyforms_options['bp_post_types'][$selected_post_types]['form_fields'] as $key => $array) { 
				        	$sortArray[$key] = $array['order']; 
				    	} 
						array_multisort($sortArray, SORT_ASC, SORT_NUMERIC, $buddyforms_options['bp_post_types'][$selected_post_types]['form_fields']); 
					}
				  
					$form->addElement(new Element_HTML('
					<ul id="sortable_'. $selected_post_types .'" class="sortable sortable_'. $selected_post_types .'">'));
					if(is_array($buddyforms_options['bp_post_types'][$selected_post_types]['form_fields'])){
						foreach($buddyforms_options['bp_post_types'][$selected_post_types]['form_fields'] as $field_id => $sad) {
							if($buddyforms_options['bp_post_types'][$selected_post_types]['form_fields'][$field_id]['name'] != ''){
								$field_position = $buddyforms_options['bp_post_types'][$selected_post_types]['form_fields'][$field_id]['order'];
								$args = Array('field_position' => $field_position, 'field_id' => $field_id, 'field_value' => $field_value,'post_type' => $selected_post_types, 'field_type' => $buddyforms_options['bp_post_types'][$selected_post_types]['form_fields'][$field_id][type]);
								$form->addElement(new Element_HTML(buddyforms_view_form_fields($args)));
							}
						}
					} 
					$form->addElement(new Element_HTML('</ul></div></div></div>'));
			    }	
			}
		$form->addElement(new Element_HTML('</div>'));			
	$form->render();
}
?>