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
		<h2>BuddyForms <span class="version">Beta</span></h2>
		
		<div class="themekraft">Proudly presented by <a href="http://themekraft.com" title="ThemeKraft WordPress Solutions" target="_blank"><img src="<?php echo plugins_url( 'img/themekraft-logo-s.png' , __FILE__ ); ?>" title="ThemeKraft WordPress Solutions" /></a></div>
		
		<div class="button-nav">
			<script type="text/javascript" src="//assets.zendesk.com/external/zenbox/v2.6/zenbox.js"></script>
			<style type="text/css" media="screen, projection">
			  @import url(//assets.zendesk.com/external/zenbox/v2.6/zenbox.css);
			</style>
			<script type="text/javascript">
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
			</script>
			<a class="btn btn-small" href="http://buddyforms.com/" title="BuddyForms Documentation" target="_blank"><i class="icon-list-alt"></i> Documentation</a>
			<a onClick="script: Zenbox.show(); return false;" class="btn btn-small" href="#" title="Write us. Bugs. Ideas. Whatever."><i class="icon-comment"></i> Submit an issue</a>
		</div>
		
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
		?><div id="message" class="updated"><p>Settings saved.</p></div><?php
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
					<div class="accordion-heading"><p class="accordion-toggle">Save Settings</p></div>
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
			<div class="hero-unit">'));
						$form->addElement(new Element_HTML('
			  <h3>General Settings</h3>
			'));
			$form->addElement(new Element_HTML('
	 		<div class="accordion-group create-form-box">
				<div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_buddyforms_general_settings_create_form" href="#accordion_buddyforms_general_settings_create_form">Create A New Form</p></div>
			    <div id="accordion_buddyforms_general_settings_create_form" class="accordion-body collapse">
					<div class="accordion-inner">')); 
						$form->addElement(new Element_Textbox("Name:", "create_new_form_name",array('id' => 'create_new_form_name', 'placeholder' => 'e.g. Movie')));
						$form->addElement(new Element_Textbox("Singular Name:", "create_new_form_singular_name",array('id' => 'create_new_form_singular_name', 'placeholder' => 'e.g. Movies')));
						
						$form->addElement(new Element_HTML('<div class="clear"></div><br>'));
						$form->addElement(new Element_Button('button','button',array('class' => 'new_form', 'name' => 'new_form','value' => 'Create Form')));
						
						$form->addElement( new Element_HTML('
					</div>
				</div>
			</div>'));	
					
			$form = apply_filters('buddyforms_general_settings', $form);	
									
			$form->addElement(new Element_HTML('</div></div>'));
		
			if(is_array($buddyforms_options['buddyforms'])){
				foreach( $buddyforms_options['buddyforms'] as $key => $buddyform) {
					
			    	$form->addElement(new Element_HTML('<div class="subcontainer tab-pane fade in" id="'.$buddyform['slug'].'">'));
						
					$form->addElement(new Element_HTML('
					<div class="accordion_sidebar" id="accordion_'.$buddyform['slug'].'">
						<div class="accordion-group">
							<div class="accordion-heading"><p class="accordion-toggle">Save This Form</p></div>
							<div id="accordion_'.$buddyform['slug'].'_save" class="accordion-body">
								<div class="accordion-inner">')); 
									$form->addElement(new Element_Hidden("submit", "submit"));
									$form->addElement(new Element_Button('submit','submit',array('id' => 'submit', 'name' => 'action','value' => 'Save Form')));
									$form->addElement(new Element_HTML('<p></p>'));
									$form->addElement(new Element_Button('button','button',array('id' => $buddyform['slug'], 'class' => 'dele_form', 'name' => 'dele_form','value' => 'Delete Form')));
										
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
						</div>'));
						
					 apply_filters('buddyforms_admin_settings_sidebar_metabox',$form, $buddyform['slug']);
					
					$form->addElement(new Element_HTML('
						<div class="accordion-group">
							<div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$buddyform['slug'].'" href="#accordion_'.$buddyform['slug'].'_fields"> Form Elements</p></div>
						    <div id="accordion_'.$buddyform['slug'].'_fields" class="accordion-body collapse">
								<div class="accordion-inner">
									<div id="#idkommtnoch">
										<p>Add new elements to your form <br>by clicking on them.</p>
										<h5>Normal Fields</h5>
										<p><a href="Text/'.$buddyform['slug'].'" class="action">Text</a></p>
										<p><a href="Textarea/'.$buddyform['slug'].'" class="action">Textarea</a></p>
										<p><a href="Link/'.$buddyform['slug'].'" class="action">Link</a></p>
										<p><a href="Mail/'.$buddyform['slug'].'" class="action">Mail</a></p>
										<p><a href="Dropdown/'.$buddyform['slug'].'" class="action">Dropdown</a></p>
										<p><a href="Radiobutton/'.$buddyform['slug'].'" class="action">Radiobutton</a></p>
										<p><a href="Checkbox/'.$buddyform['slug'].'" class="action">Checkbox</a></p>
										<h5>Post Fields</h5>
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
						$form->addElement(new Element_HTML('
						<div class="hero-unit">
						<h3>Form Settings for "'.$buddyform['name'].'"</h3>'));    
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
				 		<div class="accordion-group">
							<div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$buddyform['slug'].'" href="#accordion_'.$buddyform['slug'].'_status">Form Control<br><i class="small">Choose Form Type and Moderation.</i></p></div>
						    <div id="accordion_'.$buddyform['slug'].'_status" class="accordion-body collapse">
								<div class="accordion-inner">')); 
									$form->addElement(new Element_HTML('<i class="icon-info-sign" style="margin-top:-1px;"></i>&nbsp;These settings can be overwritten by shortcodes and other plugins! Define the defaults here.<br><br>'));
									$form->addElement(new Element_HTML('<div class="innerblock form-type">'));
									$form->addElement( new Element_Radio("<h4>Form Type</h4>", "buddyforms_options[buddyforms][".$buddyform['slug']."][form_type]", array('post_form','mail_form'),array('id' => $buddyform['slug'], 'class' => 'form_type', 'value' => $buddyforms_options['buddyforms'][$buddyform['slug']]['form_type'])));
									$form->addElement(new Element_HTML('</div><div class="clear"></div>'));
									$form->addElement(new Element_HTML('<div class="mail_form_'.$buddyform['slug'].' form_type_settings" >'));
											$form->addElement(new Element_Textbox("Enter your email address:", "buddyforms_options[buddyforms][".$buddyform['slug']."][email]", array('value' => $buddyforms_options['buddyforms'][$buddyform['slug']]['email'])));
											$form->addElement(new Element_Textbox("What should the subject line be?", "buddyforms_options[buddyforms][".$buddyform['slug']."][email_subject]", array('value' => $buddyforms_options['buddyforms'][$buddyform['slug']]['email_subject'])));
									$form->addElement(new Element_HTML('</div>'));
									$form->addElement(new Element_HTML('<div class="post_form_'.$buddyform['slug'].' form_type_settings" >'));
										$form->addElement(new Element_HTML('<div class="buddyforms_accordion_right">'));
											$form->addElement(new Element_HTML('<div class="innerblock featured-image">'));
											$form->addElement( new Element_Checkbox("<b>Featured Image</b>","buddyforms_options[buddyforms][".$buddyform['slug']."][featured_image][required]",array('Required'),array('value' => $buddyforms_options['buddyforms'][$buddyform['slug']]['featured_image']['required'])));
											$form->addElement(new Element_HTML('</div>'));
											$form->addElement(new Element_HTML('<div class="innerblock revision">'));
											$form->addElement( new Element_Checkbox("<b>Revision</b><br><i>Enable frontend revison control.</i>","buddyforms_options[buddyforms][".$buddyform['slug']."][revision]",array('Revision'),array('value' => $buddyforms_options['buddyforms'][$buddyform['slug']]['revision'])));
											$form->addElement(new Element_HTML('</div>'));
										$form->addElement(new Element_HTML('</div>'));
										$form->addElement(new Element_HTML('<div class="buddyforms_accordion_left">'));
											$form->addElement( new Element_Select("Status:", "buddyforms_options[buddyforms][".$buddyform['slug']."][status]", array('publish','pending','draft'),array('value' => $buddyforms_options['buddyforms'][$buddyform['slug']]['status'])));
											$form->addElement( new Element_Select("Comment Status:", "buddyforms_options[buddyforms][".$buddyform['slug']."][comment_status]", array('open','closed'),array('value' => $buddyforms_options['buddyforms'][$buddyform['slug']]['comment_status'])));
											$form->addElement( new Element_Select("Post Type:", "buddyforms_options[buddyforms][".$buddyform['slug']."][post_type]", $post_types,array('value' => $buddyforms_options['buddyforms'][$buddyform['slug']]['post_type'])));
										$form->addElement(new Element_HTML('</div>'));
										
									$form->addElement(new Element_HTML('</div>'));
									$form->addElement(new Element_HTML('<div class="buddyforms_accordion_bottom">'));
										//	$form->addElement(new Element_HTML('<h3><p>Notification settings</p></h3>'));
										$form->addElement(new Element_HTML('</div>'));
										
									$form->addElement( new Element_HTML('
								</div>
							</div>
						</div>'));	
					
					$form->addElement(new Element_HTML('
					<br>
					<h3>Form Builder</h3>
					<p>Add elements from the right box "Form Elements".</p>
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