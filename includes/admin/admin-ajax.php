<?php

/**
 * Ajax call back function to add a form element
 *
 * @package BuddyForms
 * @since 0.1-beta
 */
function buddyforms_add_form(){
	$buddyforms_options = get_option('buddyforms_options');
	$buddyforms_options['buddyforms'][sanitize_title($_POST['create_new_form_name'])] = Array(
		'slug' => sanitize_title($_POST['create_new_form_name']),
		'name' => $_POST['create_new_form_name'],
		'singular_name' => $_POST['create_new_form_singular_name']);
		
	update_option("buddyforms_options", $buddyforms_options);

	die();
}
add_action( 'wp_ajax_buddyforms_add_form', 'buddyforms_add_form' );
add_action( 'wp_ajax_nopriv_buddyforms_add_form', 'buddyforms_add_form' );


/**
 * Ajax call back function to delete a form element
 *
 * @package BuddyForms
 * @since 0.1-beta
 */
function buddyforms_delete_form(){
	
	$buddyforms_options = get_option('buddyforms_options');
	unset( $buddyforms_options['buddyforms'][$_POST['dele_form_slug']] );
    
	update_option("buddyforms_options", $buddyforms_options);
    die();
}
add_action('wp_ajax_buddyforms_delete_form', 'buddyforms_delete_form');
add_action('wp_ajax_nopriv_buddyforms_delete_form', 'buddyforms_delete_form');

/**
 * Ajax call back function to save the new form elements order
 *
 * @package BuddyForms
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
 * @package BuddyForms
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
 * Get all taxonomies
 *
 * @package BuddyForms
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
 * View form fields
 *
 * @package BuddyForms
 * @since 0.1-beta
 */
function buddyforms_view_form_fields($args){
	global $buddyforms;
	
	$buddyforms_options	= $buddyforms;
	
	if(!isset($_POST))
		return;

	if(isset($_POST['post_args']))
		$post_args	= explode('/', $_POST['post_args']);
	
	if(isset($post_args[0]))
		$field_type	= $post_args[0];
	
	if(isset($post_args[1]))
		$form_slug = $post_args[1];
	
	if(isset($post_args[2]))
		$field_unique = $post_args[2];
	

	if(isset($field_unique) && $field_unique == 'unique' ) {
		if(isset($buddyforms['buddyforms'][$form_slug]['form_fields'])){
				
			foreach ($buddyforms['buddyforms'][$form_slug]['form_fields'] as $key => $form_field) {
				if($form_field['type'] == $field_type)
					return 'unique';
			}
			
		}		
	}
	
	if(isset($_POST['numItems']))
		$numItems = $_POST['numItems'];
	
	if(is_array($args))
		extract($args);
	
	
	if(!isset($field_id))
		$field_id = $mod5 = substr(md5(time() * rand()), 0, 10);;
		
	if(isset($field_position) =='')
		$field_position = $numItems;
	
	$form_fields = Array();
	
	$buddyforms['hooks']['form_element'] = apply_filters('buddyforms_form_element_hooks',$buddyforms['hooks']['form_element'],$form_slug,$field_id);
	$style = 'false';
	if(isset($buddyforms_options['buddyforms'][$form_slug]['form_type']) && $buddyforms_options['buddyforms'][$form_slug]['form_type'] != 'post_form')
		$style = 'style="display: none;"';
		
		$form_fields['right']['html_display']		= new Element_HTML('<div class="bf_element_display_'.$form_slug.'" '.$style.'>');
		
		$display = 'false';
		if(isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['display']))
			$display = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['display'];
												
		$form_fields['right']['display']			= new Element_Select("Display?", "buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][display]", $buddyforms['hooks']['form_element'], array('value' => $display));
		
		$display_name = 'false';
		if(isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['display_name']))
			$display_name = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['display_name'];
		$form_fields['right']['display_name']		= new Element_Checkbox("Display name?","buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][display_name]",array(''),array('value' => $display_name));
		
		$form_fields['right']['html_display_end']	= new Element_HTML('</div>');
		
		$required = 'false';
		if(isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['required']))
			$required = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['required'];
		$form_fields['right']['required']			= new Element_Checkbox("Required?","buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][required]",array(''),array('value' => $required));
								
		$name = '';
		if(isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['name']))
			$name = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['name'];
		$form_fields['left']['name'] 				= new Element_Textbox("Name:", "buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][name]", array('value' => $name));
				
		if(empty($slug))
			$slug  = sanitize_title($name);
		$form_fields['left']['slug']		 		= new Element_Textbox("Slug: <i>optional '_name' will create a hidden post meta field</i>", "buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][slug]", array('value' => $slug));
		
		$description = '';
		if(isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['description']))
			$description = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['description'];
		
		$form_fields['left']['description'] 		= new Element_Textbox("Description:", "buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][description]", array('value' => $description));
		
		$form_fields['left']['type'] 				= new Element_Hidden("buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][type]", $field_type);
		$form_fields['left']['order'] 				= new Element_Hidden("buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][order]", $field_position, array('id' => 'buddyforms/' . $form_slug .'/form_fields/'. $field_id .'/order'));
		
		switch ($field_type) {
	
			case 'Link':
				$target = 'false';
				if(isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['target']))
					$target = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['target'];
			
				$form_fields['left']['target'] 	= new Element_Select("Target:", "buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][target]", array('_self','_blank'), array('value' => $target))	;
			break;
			case 'Dropdown':
				$field_args = Array(
					'post_type' => $post_type, 
					'field_id' => $field_id,
					'buddyforms_options' => $buddyforms_options
				);
				$form_fields = buddyforms_form_element_multiple($form_fields, $field_args);
			break;
			case 'Radiobutton':
				$field_args = Array(
					'post_type' => $post_type, 
					'field_id' => $field_id,
					'buddyforms_options' => $buddyforms_options
				);
				$form_fields = buddyforms_form_element_multiple($form_fields, $field_args);
			break;
			case 'Checkbox':
				$field_args = Array(
					'post_type' => $post_type, 
					'field_id' => $field_id,
					'buddyforms_options' => $buddyforms_options
				);
				$form_fields = buddyforms_form_element_multiple($form_fields, $field_args);
				break;
			case 'Taxonomy':
				$taxonomies = buddyforms_taxonomies();
				
				$taxonomy = 'false';
				if(isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['taxonomy']))
					$taxonomy = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['taxonomy'];
				$form_fields['left']['taxonomy'] 		= new Element_Select("Taxonomy:", "buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][taxonomy]", $taxonomies, array('value' => $taxonomy));
				
				$multiple = 'false';
				if(isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['multiple']))
					$multiple = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['multiple'];
				$form_fields['left']['multiple']		= new Element_Checkbox("Multiple:","buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][multiple]",array(''),array('value' => $multiple));
				
				$creat_new_tax = 'false';
				if(isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['creat_new_tax']))
					$creat_new_tax = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['creat_new_tax'];
				$form_fields['left']['creat_new_tax'] = new Element_Checkbox("User can creat new?:","buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][creat_new_tax]",array(''),array('value' => $creat_new_tax));
				break;
			case 'Hidden':
				unset($form_fields);
				
				$slug = '';
				if(isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['slug']))
					$slug = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['slug'];
				$form_fields['left']['name']		= new Element_Hidden("buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][name]", $slug);
				$form_fields['left']['slug']		= new Element_Textbox("Slug:", "buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][slug]", array('required' => true, 'value' => $slug));
				$form_fields['left']['type']		= new Element_Hidden("buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][type]", $field_type);
				$form_fields['left']['order']		= new Element_Hidden("buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][order]", $field_position, array('id' => 'buddyforms/' . $form_slug .'/form_fields/'. $field_id .'/order'));
				
				$value = '';
				if(isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['value']))
					$value = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['value'];
				$form_fields['left']['value'] 	= 	new Element_Textbox("Value:", "buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][value]", array('value' => $value));
				break;
			case 'Comments':
				unset($form_fields);
				$form_fields['left']['name']		= new Element_Hidden("buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][name]", 'Comments');
				$form_fields['left']['type']		= new Element_Hidden("buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][type]", $field_type);
				$form_fields['left']['order']		= new Element_Hidden("buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][order]", $field_position, array('id' => 'buddyforms/' . $form_slug .'/form_fields/'. $field_id .'/order'));
				//$form_fields['left'][comments]	= new Element_Select("Comments open?", "buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][comments]", array('open','closed'), array('value' => $buddyforms_options['buddyforms'][$form_slug][form_fields][$field_id][comments]));
				$form_fields['left']['html']		= new Element_HTML('There are no settings needed so far You can change theh global comment settings in the sidebar, and with the comments element added to the form the user has the posebility to overwrite the global settings and open close comments from ther eown post');
				
				break;
			default:
				$form_fields = apply_filters('buddyforms_form_element_add_field',$form_fields,$form_slug,$field_type,$field_id);
				break;
	
		}

	ob_start(); ?>
	<li id="buddyforms/<?php echo $form_slug ?>/form_fields/<?php echo $field_id ?>/order" class="list_item <?php echo $field_id.' '. $field_type ?>">
	<div class="accordion_fields">
		<div class="accordion-group">
			<div class="accordion-heading"> 
				
				<div class="accordion-heading-options">
					<a class="delete" id="<?php echo $field_id ?>" href="buddyforms/<?php echo $form_slug ?>/form_fields/<?php echo $field_id ?>/order">
						<i class="icon-remove-sign" style="margin-top:0px;"></i><b><?php _e( 'Delete', 'buddyforms' ); ?></b>
					</a>
				</div>
				
				<p class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_text" href="#accordion_<?php echo $form_slug; ?>_<?php echo $field_type.'_'.$field_id; ?>">
				<b><?php echo $field_type; ?></b>  
				<i><?php  if(isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['name'])) 
					echo $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['name']; ?></i>
				</p>
				
			</div>
						
			<div id="accordion_<?php echo $form_slug; ?>_<?php echo $field_type.'_'.$field_id; ?>" class="accordion-body collapse">
				<div class="accordion-inner">
					<div class="buddyforms_accordion_right">
						<?php 
						if(isset($form_fields['right'])){
							foreach ($form_fields['right'] as $key => $value) {
								echo '<div class="buddyforms_field_label">' . $form_fields['right'][$key]->getLabel() . '</div>';
								echo '<div class="buddyforms_form_field">' . $form_fields['right'][$key]->render() . '</div>';
							}
						}
						?>
					</div>
					<div class="buddyforms_accordion_left">
						<?php 	
						if($form_fields['left']){
							foreach ($form_fields['left'] as $key => $value) {
								if(substr($key, 0,4) == 'html'){
									echo $form_fields['left'][$key]->getLabel();
									echo $form_fields['left'][$key]->render();
								} else {
									echo '<div class="buddyforms_field_label">' . $form_fields['left'][$key]->getLabel() . '</div>';
									echo '<div class="buddyforms_form_field">' . $form_fields['left'][$key]->render() . '</div>';
								}
								
							}
						}
						?>
					</div>
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

?>