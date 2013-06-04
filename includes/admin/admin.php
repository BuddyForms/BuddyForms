<?php

/**
 * Create "buddyforms Options" nav menu
 *
 * @package buddyforms
 * @since 0.1-beta
 */
function buddyforms_create_menu() {
	session_start();
	add_menu_page( 'BuddyForms', 'BuddyForms', 'edit_posts', 'buddyforms_options_page', 'buddyforms_options_content' );
	
}  
add_action('admin_menu', 'buddyforms_create_menu');


/**
 * Display the settings page
 *
 * @package buddyforms
 * @since 0.2-beta
 */
function buddyforms_options_content() { ?>
		
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
	BuddyForms::set_globals();
	$buddyforms_options = $buddyforms;//get_option('buddyforms_options');
	
	// Get all post types
    $args=array(
		'public' => true,
		'show_ui' => true
    ); 
    $output = 'names'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'
    $post_types = get_post_types($args,$output,$operator); 
   	$post_types_none[none] = none;
	$post_types = array_merge($post_types_none,$post_types);
	
	// Form starts
	$form = new Form("buddyforms_form");
	$form->configure(array(
		"prevent" => array("bootstrap", "jQuery"),
		"action" => $_SERVER['REQUEST_URI'],
		"view" => new View_Inline
	));
	
	$form->addElement(new Element_HTML('<br><div class="tabbable tabs-top"><ul class="nav nav-tabs">
		<li class="active"><a href="#general-settings" data-toggle="tab">General Settings</a></li>'));
		
	if(is_array($buddyforms_options['buddyforms'])){
		foreach( $buddyforms_options['buddyforms'] as $key => $buddyform) {
			$tabname = $buddyform['name'];
			if(empty($tabname))
				$tabname = $buddyform['slug'];

			$form->addElement(new Element_HTML('<li class=""><a href="#'.$buddyform['slug'].'" data-toggle="tab">'.$tabname.'</a></li>'));
		}
	}	
	$form->addElement(new Element_HTML('</ul></div>
		<div class="tab-content"><div class="subcontainer tab-pane fade in active" id="general-settings">'));
		
			$form->addElement(new Element_HTML('
			<div class="accordion_sidebar" id="accordion_'.$buddyform['slug'].'">
				<div class="accordion-group">
					<div class="accordion-heading"><p class="accordion-toggle">Publish</p></div>
					<div id="accordion_'.$buddyform['slug'].'_save" class="accordion-body">
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
			  <lable><b>Create a new Form</b></lable>
			'));
			$form->addElement(new Element_Textbox("<br>Name:<br>", "create_new_form_name",array('id' => 'create_new_form_name')));
			$form->addElement(new Element_Textbox("<br>Singular Name:<br>", "create_new_form_singular_name",array('id' => 'create_new_form_singular_name')));
			
			$form->addElement(new Element_HTML('<br>'));
			$form->addElement(new Element_Button('button','button',array('class' => 'new_tab', 'name' => 'new_tab','value' => 'Creat new Form')));
					
			$form = apply_filters('buddyforms_general_settings', $form);	
					
									
			$form->addElement(new Element_HTML('</div></div>'));
		
			if(is_array($buddyforms_options['buddyforms'])){
				foreach( $buddyforms_options['buddyforms'] as $key => $buddyform) {
					
			    	$form->addElement(new Element_HTML('<div class="subcontainer tab-pane fade in" id="'.$buddyform['slug'].'">'));
						
					$form->addElement(new Element_HTML('
					<div class="accordion_sidebar" id="accordion_'.$buddyform['slug'].'">
						<div class="accordion-group">
							<div class="accordion-heading"><p class="accordion-toggle">Save</p></div>
							<div id="accordion_'.$buddyform['slug'].'_save" class="accordion-body">
								<div class="accordion-inner">')); 
									$form->addElement(new Element_Hidden("submit", "submit"));
									$form->addElement(new Element_Button('submit','submit',array('id' => 'submit', 'name' => 'action','value' => 'Save')));
									$form->addElement(new Element_Button('button','button',array('id' => $buddyform['slug'], 'class' => 'dele_form', 'name' => 'dele_form','value' => 'Delete this Form')));
										
									$form->addElement(new Element_HTML('
								</div>
					    	</div>
						</div>'));
						$form->addElement(new Element_HTML('
						<div class="accordion-group">
							<div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$buddyform['slug'].'" href="#accordion_'.$buddyform['slug'].'_content">Label</p></div>
							<div id="accordion_'.$buddyform['slug'].'_content" class="accordion-body collapse">
								<div class="accordion-inner">')); 
									$form->addElement(new Element_Textbox("Name:", "buddyforms_options[buddyforms][".$buddyform['slug']."][name]", array('value' => $buddyforms_options['buddyforms'][$buddyform['slug']]['name'])));
									$form->addElement(new Element_Textbox("Singular Name:", "buddyforms_options[buddyforms][".$buddyform['slug']."][singular_name]", array('value' => $buddyforms_options['buddyforms'][$buddyform['slug']]['singular_name'])));
									$form->addElement(new Element_Textbox("Overwrite slug if needed *:", "buddyforms_options[buddyforms][".$buddyform['slug']."][slug]", array('value' => sanitize_title($buddyforms_options['buddyforms'][$buddyform['slug']]['slug']))));
									
									$form->addElement(new Element_HTML('
								</div>
					    	</div>
						</div>
				 		<div class="accordion-group">
							<div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$buddyform['slug'].'" href="#accordion_'.$buddyform['slug'].'_status">Post Control</p></div>
						    <div id="accordion_'.$buddyform['slug'].'_status" class="accordion-body collapse">
								<div class="accordion-inner">')); 
									$form->addElement( new Element_Select("Status:", "buddyforms_options[buddyforms][".$buddyform['slug']."][status]", array('publish','pending','draft'),array('value' => $buddyforms_options['buddyforms'][$buddyform['slug']]['status'])));
									$form->addElement( new Element_HTML('<br><br>'));
									$form->addElement( new Element_Select("Comment Status:", "buddyforms_options[buddyforms][".$buddyform['slug']."][comment_status]", array('open','closed'),array('value' => $buddyforms_options['buddyforms'][$buddyform['slug']]['comment_status'])));
									$form->addElement( new Element_HTML('<br><br>'));
									$form->addElement( new Element_Checkbox("Featured Image:","buddyforms_options[buddyforms][".$buddyform['slug']."][featured_image][required]",array('Required'),array('value' => $buddyforms_options['buddyforms'][$buddyform['slug']]['featured_image']['required'])));
									$form->addElement( new Element_HTML('<br>'));
									$form->addElement( new Element_Checkbox("Revision: <br><i> enable frontend revison control   </i>","buddyforms_options[buddyforms][".$buddyform['slug']."][revision]",array('Revision'),array('value' => $buddyforms_options['buddyforms'][$buddyform['slug']]['revision'])));
									
									$form->addElement( new Element_HTML('
								</div>
							</div>
						</div>'));	
					
						$form->addElement( new Element_HTML('<div class="accordion-group">
							<div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$buddyform['slug'].'" href="#accordion_'.$buddyform['slug'].'_post_type">Hook Custom Post Meta</p></div>
						    <div id="accordion_'.$buddyform['slug'].'_post_type" class="accordion-body collapse">
								<div class="accordion-inner">')); 
									$form->addElement( new Element_Select("Post Type:", "buddyforms_options[buddyforms][".$buddyform['slug']."][post_type]", $post_types,array('value' => $buddyforms_options['buddyforms'][$buddyform['slug']]['post_type'])));
									
									$form->addElement( new Element_HTML('
								</div>
							</div>
						</div>'));	
					
					 apply_filters('buddyforms_admin_settings_sidebar_metabox',$form, $buddyform['slug']);
					
					$form->addElement(new Element_HTML('
						<div class="accordion-group">
							<div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$buddyform['slug'].'" href="#accordion_'.$buddyform['slug'].'_fields"> Form Elements</p></div>
						    <div id="accordion_'.$buddyform['slug'].'_fields" class="accordion-body collapse">
								<div class="accordion-inner">
									<div id="#idkommtnoch">
										<p><a href="Text/'.$buddyform['slug'].'" class="action">Text</a></p>
										<p><a href="Textarea/'.$buddyform['slug'].'" class="action">Textarea</a></p>
										<p><a href="Link/'.$buddyform['slug'].'" class="action">Link</a></p>
										<p><a href="Mail/'.$buddyform['slug'].'" class="action">Mail</a></p>
										<p><a href="Dropdown/'.$buddyform['slug'].'" class="action">Dropdown</a></p>
										<p><a href="Radiobutton/'.$buddyform['slug'].'" class="action">Radiobutton</a></p>
										<p><a href="Checkbox/'.$buddyform['slug'].'" class="action">Checkbox</a></p>
										<p><a href="Taxonomy/'.$buddyform['slug'].'" class="action">Taxonomy</a></p>
										<p><a href="Hidden/'.$buddyform['slug'].'" class="action">Hidden</a></p>
										<p><a href="Comments/'.$buddyform['slug'].'/unique" class="action">Comments</a></p>
										'));
										$form = apply_filters('buddyforms_add_form_element_in_sidebar', $form, $buddyform['slug']);
									$form->addElement(new Element_HTML('
									</div>
								</div>
							</div>
						</div>		  
					</div>
					<div id="buddyforms_forms_builder_'.$buddyform['slug'].'" class="buddyforms_forms_builder">'));
					$form->addElement(new Element_HTML('<div class="hero-unit">
						<h3>Post Type General Settings</h3>'));    
					$form->addElement(new Element_HTML('<p class="loading-animation-order alert alert-success">Save new order <i class="icon-ok"></i></p>'));
					$form->addElement(new Element_HTML('<div class="loading-animation-new alert alert-success">Loade new element <i class="icon-ok"></i></div>
					'));
					
					$sortArray = array(); 
					
					if(!empty($buddyforms_options['buddyforms'][$buddyform['slug']]['form_fields'] )){
						foreach($buddyforms_options['buddyforms'][$buddyform['slug']]['form_fields'] as $key => $array) { 
				        	$sortArray[$key] = $array['order']; 
				    	} 
						array_multisort($sortArray, SORT_ASC, SORT_NUMERIC, $buddyforms_options['buddyforms'][$buddyform['slug']]['form_fields']); 
					}
				  
					$form->addElement(new Element_HTML('
					<ul id="sortable_'. $buddyform['slug'] .'" class="sortable sortable_'. $buddyform['slug'] .'">'));
					if(is_array($buddyforms_options['buddyforms'][$buddyform['slug']]['form_fields'])){
						foreach($buddyforms_options['buddyforms'][$buddyform['slug']]['form_fields'] as $field_id => $customfield) {
								
							$slug = sanitize_title($customfield['slug']);	
							if($slug == '')
								$slug = sanitize_title($customfield['name']);
							
							if( $slug != '' ){
								$args = Array(
									'slug'				=> $slug,
									'field_position'	=> $customfield['order'],
									'field_id'			=> $field_id,
									'post_type'			=> $buddyform['slug'],
									'field_type'		=> $customfield['type']
									);
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

function buddyforms_form_element_multyble($form_fields, $args){
		
	extract( $args );
	
	$form_fields['left'][html_1] = new Element_HTML('
	<div class="element_field">
	<p>Checkbox Values:</p>
		 <ul id="'.$post_type.'_field_'.$field_id.'" class="element_field_sortable">');
		 if(isset($buddyforms_options['buddyforms'][$post_type][form_fields][$field_id][value])){
		 	$count = 1;
		 	 foreach ($buddyforms_options['buddyforms'][$post_type][form_fields][$field_id][value] as $key => $value) {
				$form_fields['left']['html_li_start_'.$key]	= new Element_HTML('<li class="field_item field_item_'.$field_id.'_'.$count.'">');
				$form_fields['left']['html_value_'.$key] 	= new Element_Textbox("Entry ".$key, "buddyforms_options[buddyforms][".$post_type."][form_fields][".$field_id."][value][]", array('value' => $value));
				$form_fields['left']['html_li_end_'.$key]	= new Element_HTML('<a href="#" id="'.$field_id.'_'.$count.'" class="delete_input">X</a> - <a href="#" id="'.$field_id.'">move</a></li>');
				$count++;
			 }
		 }   		
		$form_fields['left'][html_2] = new Element_HTML(' 
	    </ul>
     </div>
     <a href="'.$post_type.'/'.$field_id.'" class="button add_input">+</a>
    ');	
	
	return $form_fields;
}
?>