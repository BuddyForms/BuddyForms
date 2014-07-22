<?php

/**
 * Create "BuddyForms Options" nav menu
 *
 * @package buddyforms
 * @since 0.1-beta
 */
function buddyforms_create_menu() {

    if(!session_id());
    @session_start();

	add_menu_page( 'BuddyForms', 'BuddyForms', 'edit_posts', 'buddyforms_options_page', 'buddyforms_options_content' );
    add_submenu_page( 'buddyforms_options_page', __('Add New', 'buddyforms'), __('Add New', 'buddyforms'), 'edit_posts', 'create-new-form', 'bf_import_export_screen' );
    add_submenu_page( 'buddyforms_options_page', __('Add-ons', 'buddyforms'), __('Add-ons', 'buddyforms'), 'edit_posts', 'bf_add_ons', 'bf_add_ons_screen' );

}
add_action('admin_menu', 'buddyforms_create_menu');

/**
 * Display the settings page
 *
 * @package buddyforms
 * @since 0.2-beta
 */
function buddyforms_options_content() {

	global $buddyforms;

    // Check that the user is allowed to update options
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'buddyforms'));
    }
    ?>
		
	<div id="bf_admin_wrap" class="wrap">


        <?php
        include('admin-credits.php');
		if (isset($_POST["buddyforms_options"])) {
			$buddyforms_options = $_POST["buddyforms_options"];

			foreach ($buddyforms_options['buddyforms'] as $key => $buddyform) {

				$slug = $buddyform['slug'];

				if($slug != $key){
					$buddyforms_options['buddyforms'][$slug] = $buddyform;
					$buddyforms_options['buddyforms'][$slug]['slug'] = $slug;
					unset($buddyforms_options['buddyforms'][$key]);
					$buddyforms_options = apply_filters('buddyforms_set_globals_new_slug', $buddyforms_options, $slug, $key);
				}

				if(isset($buddyform['form_fields'])){
					foreach ( $buddyform['form_fields'] as $field_key => $field ) {
						if(empty($field['slug']))
							$buddyforms_options['buddyforms'][$key]['form_fields'][$field_key]['slug'] =  sanitize_title($field['name']);
					}
				}

			}
			$buddyforms['buddyforms'] = $buddyforms_options['buddyforms'];
			$update_option = update_option("buddyforms_options", $buddyforms);

			if($update_option)
				echo "<div id=\"settings_updated\" class=\"updated\"> <p><strong>" . __('Settings saved','buddyforms'). ".</strong></p></div>";

		}
		?>
		<div id="post-body">
			<div id="post-body-content">
               	<?php buddyforms_settings_page(); ?>
			</div>
		</div>

	</div>
	
<?php
}

/**
 * Create the BuddyForms settings page
 *
 * @package buddyforms
 * @since 0.1-beta
 */
function buddyforms_settings_page() {
    global $bp, $buddyforms;
	
	// Get all needed values
	BuddyForms::set_globals();
	$buddyforms_options = $buddyforms; //get_option('buddyforms_options');
	
	// Get all post types
    $args=array(
		'public' => true,
		'show_ui' => true
    ); 
    $output = 'names'; // names or objects, note: names is the default
    $operator = 'and'; // 'and' or 'or'
    $post_types = get_post_types($args,$output,$operator); 
   	$post_types_none['none'] = 'none';
	$post_types = array_merge($post_types_none,$post_types);
	
	// Form starts
	$form = new Form("buddyforms_form");
	$form->configure(array(
		"prevent" => array("bootstrap", "jQuery"),
		"action" => $_SERVER['REQUEST_URI'],
		"view" => new View_Inline
	));

    $form->addElement(new Element_HTML('<div class="tab-content"><div class="subcontainer tab-pane fade in active" id="general-settings">'));

    $form->addElement(new Element_HTML('<div class="hero-unit-konrad">'));

    if(isset($buddyforms['buddyforms']) && count($buddyforms['buddyforms']) > 0 ){
        $form->addElement(new Element_HTML('
        <table class="wp-list-table widefat fixed posts">
            <thead>
                <tr>
                    <th scope="col" id="cb" class="manage-column column-cb check-column" style="">
                        <label class="screen-reader-text" for="cb-select-all-1">' . __('Select All', 'buddyforms') .' </label>
                        <input id="cb-select-all-1" type="checkbox">
                    </th>
                    <th scope="col" id="name" class="manage-column column-comment sortable desc" style="">' . __('Name', 'buddyforms') .'</th>
                    <th scope="col" id="slug" class="manage-column column-description" style="">' . __('Slug', 'buddyforms') .'</th>
                    <th scope="col" id="attached-post-type" class="manage-column column-status" style="">' . __('Attached Post Type', 'buddyforms') .'</th>
                    <th scope="col" id="attached-page" class="manage-column column-status" style="">' . __('Attached Page', 'buddyforms') .'</th>

            </thead>'));
            foreach ($buddyforms['buddyforms'] as $key => $buddyform) {
                $form->addElement(new Element_HTML(' <tr>
                    <th scope="row" class="check-column">
                        <label class="screen-reader-text" for="aid-' . $buddyform['slug'] . '">' . $buddyform['name'] .'</label>
                        <input type="checkbox" name="bf_export_form_slugs[]" value="' . $buddyform['slug'] . '" id="aid-' . $buddyform['slug'] . '">


                    </th>
                    <td class="slug column-slug">

                    <div class="showhim">' . $buddyform['slug'] .'<div class="showme"><a  href="#' . $buddyform['slug'] . '" data-toggle="tab">edit</a></div></div>
                    </td>'
                ));

                $form->addElement(new Element_HTML('<td class="slug column-slug"> '));
                $form->addElement(new Element_HTML( isset($buddyform['name']) ? $buddyform['name']: '--'));
                $form->addElement(new Element_HTML('</td>'));

                $form->addElement(new Element_HTML('<td class="slug column-slug"> '));
                $form->addElement(new Element_HTML( isset($buddyform['post_type']) ? $buddyform['post_type']: '--'));
                $form->addElement(new Element_HTML('</td>'));

                $form->addElement(new Element_HTML('<td class="slug column-slug"> '));
                $form->addElement(new Element_HTML( isset($buddyform['attached_page']) ? $buddyform['attached_page']: '--'));
                $form->addElement(new Element_HTML('</td>'));

            }
        $form->addElement(new Element_HTML('</table>'));
    } else {
        $form->addElement(new Element_HTML('<div class="bf-row"><div class="bf-half-col bf-left"><div class="bf-col-content bf_no_form"><h3 style="margin-top: 30px;">' . __('No Forms here so far...', 'buddyforms') .'</h3> <a href="' . get_admin_url() . 'admin.php?page=create-new-form" class="button-primary add-new-h3" style="font-size: 15px;">' . __('Create A New Form', 'buddyforms') .'</a></div></div></div>'));
    }

			$form->addElement(new Element_HTML('</div></div>'));

			if(isset($buddyforms_options['buddyforms'])){
				foreach( $buddyforms_options['buddyforms'] as $key => $buddyform) {
					
			    	$form->addElement(new Element_HTML('<div class="subcontainer tab-pane fade in" id="'.$buddyform['slug'].'">'));
						
					$form->addElement(new Element_HTML('
					<div class="accordion_sidebar" id="accordion_'.$buddyform['slug'].'">
						<div class="accordion-group">
							<div class="accordion-heading"><p class="accordion-toggle">' . __('Save Form Settings', 'buddyforms') . '</p></div>
							<div id="accordion_'.$buddyform['slug'].'_save" class="accordion-body">
								<div class="accordion-inner">')); 

                    $form->addElement(new Element_HTML('<input type="button" class="button" onClick="history.go(0)" value="' . __('Cancel', 'buddyforms') . '" />'));

                    $form->addElement(new Element_Button('button','button',array('id' => $buddyform['slug'], 'class' => 'button dele_form', 'name' => 'dele_form','value' => __('Delete', 'buddyforms'))));

                    $form->addElement(new Element_Hidden("submit", "submit"));
                    $form->addElement(new Element_Button('submit','submit',array('id' => 'submit', 'name' => 'action','value' => __('Save', 'buddyforms'), 'class' => 'button-primary', 'style' => 'float: right;')));

                    $form->addElement(new Element_HTML('</div>
					    	</div>
						</div>'));
						
					 apply_filters('buddyforms_admin_settings_sidebar_metabox',$form, $buddyform['slug']);
					
					$form->addElement(new Element_HTML('
						<div class="accordion-group">
							<div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$buddyform['slug'].'" href="#accordion_'.$buddyform['slug'].'_fields"> ' . __('Form Elements', 'buddyforms') .'</p></div>
						    <div id="accordion_'.$buddyform['slug'].'_fields" class="accordion-body collapse">
								<div class="accordion-inner">
									<div>
										<p>' . __('Add new elements to your form <br>by clicking on them.', 'buddyforms') .'</p>
										<h5>' . __('Classic Fields', 'buddyforms') .'</h5>
										<p><a href="Text/'.$buddyform['slug'].'" class="action">' . __('Text', 'buddyforms') .'</a></p>
										<p><a href="Textarea/'.$buddyform['slug'].'" class="action">' . __('Textarea', 'buddyforms') .'</a></p>
										<p><a href="Link/'.$buddyform['slug'].'" class="action">' . __('Link', 'buddyforms') .'</a></p>
										<p><a href="Mail/'.$buddyform['slug'].'" class="action">' . __('Mail', 'buddyforms') .'</a></p>
										<p><a href="Dropdown/'.$buddyform['slug'].'" class="action">' . __('Dropdown', 'buddyforms') .'</a></p>
										<p><a href="Radiobutton/'.$buddyform['slug'].'" class="action">' . __('Radiobutton', 'buddyforms') .'</a></p>
										<p><a href="Checkbox/'.$buddyform['slug'].'" class="action">' . __('Checkbox', 'buddyforms') .'</a></p>
										<h5>Post Fields</h5>
										<p><a href="Taxonomy/'.$buddyform['slug'].'" class="action">' . __('Taxonomy', 'buddyforms') .'</a></p>
										<p><a href="Hidden/'.$buddyform['slug'].'" class="action">' . __('Hidden', 'buddyforms') .'</a></p>
										<p><a href="Comments/'.$buddyform['slug'].'/unique" class="action">' . __('Comments', 'buddyforms') .'</a></p>
										<p><a href="Status/'.$buddyform['slug'].'/unique" class="action">' . __('Post Status', 'buddyforms') .'</a></p>
										<p><a href="FeaturedImage/'.$buddyform['slug'].'/unique" class="action">' . __('Featured Image', 'buddyforms') .'</a></p>
                                        <p><a href="File/'.$buddyform['slug'].'/unique" class="action">' . __('File', 'buddyforms') .'</a></p>
										'));
										$form = apply_filters('buddyforms_add_form_element_to_sidebar', $form, $buddyform['slug']);
									$form->addElement(new Element_HTML('
									</div>
								</div>
							</div>
						</div>		  
					</div>
					<div id="buddyforms_forms_builder_'.$buddyform['slug'].'" class="buddyforms_forms_builder">'));
						$form->addElement(new Element_HTML('
						<div class="hero-unit">
						<h3>' . __('Form Settings for', 'buddyforms') .' "'.$buddyform['name'].'"</h3>'));
					$form->addElement(new Element_HTML('<p class="loading-animation-order alert alert-success">' . __('Save new order', 'buddyforms') .' <i class="icon-ok"></i></p>'));
					$form->addElement(new Element_HTML('<div class="loading-animation-new alert alert-success">' . __('Load new element', 'buddyforms') .' <i class="icon-ok"></i></div>
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
							<div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$buddyform['slug'].'" href="#accordion_'.$buddyform['slug'].'_status"><b>' . __('Form Control', 'buddyforms') .'</b></p></div>
						    <div id="accordion_'.$buddyform['slug'].'_status" class="accordion-body collapse">
								<div class="accordion-inner bf-main-settings">'));
                    $form->addElement(new Element_Textbox(__("Name:", 'buddyforms'), "buddyforms_options[buddyforms][".$buddyform['slug']."][name]", array('value' => $buddyforms_options['buddyforms'][$buddyform['slug']]['name'])));
                    $form->addElement(new Element_Textbox(__("Singular Name:", 'buddyforms'), "buddyforms_options[buddyforms][".$buddyform['slug']."][singular_name]", array('value' => $buddyforms_options['buddyforms'][$buddyform['slug']]['singular_name'])));
                    $form->addElement(new Element_Textbox(__("Overwrite slug if needed *:", 'buddyforms'), "buddyforms_options[buddyforms][".$buddyform['slug']."][slug]", array('value' => sanitize_title($buddyforms_options['buddyforms'][$buddyform['slug']]['slug']))));

                    $form->addElement(new Element_HTML('<br><hr /><p><i class="icon-info-sign" style="margin-top:6px;"></i>&nbsp;<small><i>' . __('The following settings can be overwritten by shortcodes and other plugins!', 'buddyforms') . '<br>' . __('You can define the defaults here.', 'buddyforms') . ' </i></small></p><br />'));

									$form->addElement(new Element_HTML('<div class="post_form_'.$buddyform['slug'].' form_type_settings" >'));
										$form->addElement(new Element_HTML('<div class="buddyforms_accordion_right">'));

											$form->addElement(new Element_HTML('<div class="innerblock revision">'));
											
											$revision = 'false';
											if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['revision']))
												$revision = $buddyforms_options['buddyforms'][$buddyform['slug']]['revision'];
											
											$form->addElement( new Element_Checkbox("<b>" . __('Revision', 'buddyforms') . "</b><br><i>" . __('Enable frontend revison control.', 'buddyforms') . "</i>","buddyforms_options[buddyforms][".$buddyform['slug']."][revision]",array('Revision'),array('value' => $revision)));
											
											$admin_bar = 'false';
											if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['admin_bar']))
												$admin_bar = $buddyforms_options['buddyforms'][$buddyform['slug']]['admin_bar'];
											
											$form->addElement( new Element_Checkbox("<br><b>" . __('Admin Bar', 'buddyforms'). "</b><br>","buddyforms_options[buddyforms][".$buddyform['slug']."][admin_bar]",array('Add to Admin Bar'),array('value' => $admin_bar)));
											
											$edit_link = 'false';
											if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['edit_link']))
												$edit_link = $buddyforms_options['buddyforms'][$buddyform['slug']]['edit_link'];
											
											$form->addElement( new Element_Checkbox("<br><b>" . __('Overwrite Edit-this-entry link?', 'buddyforms') . "</b><br><i>" . __('The link to the backend will be changed', 'buddyforms') . "<br>" . __('to use the frontend editing.', 'buddyforms') . "</i>","buddyforms_options[buddyforms][".$buddyform['slug']."][edit_link]",array('overwrite'),array('value' => $edit_link)));
											
											$form->addElement(new Element_HTML('</div>'));
										$form->addElement(new Element_HTML('</div>'));
										$form->addElement(new Element_HTML('<div class="buddyforms_accordion_left">'));
											
											$status = 'false';
											if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['status']))
												$status = $buddyforms_options['buddyforms'][$buddyform['slug']]['status'];
												
											$form->addElement( new Element_Select(__("Status:", 'buddyforms'), "buddyforms_options[buddyforms][".$buddyform['slug']."][status]", array('publish','pending','draft'),array('value' => $status)));
											
											$comment_status = 'false';
											if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['comment_status']))
												$comment_status = $buddyforms_options['buddyforms'][$buddyform['slug']]['comment_status'];
											
											$form->addElement( new Element_Select(__("Comment Status:", 'buddyforms'), "buddyforms_options[buddyforms][".$buddyform['slug']."][comment_status]", array('open','closed'),array('value' => $comment_status)));
											
											$post_type = 'false';
											if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['post_type']))
												$post_type = $buddyforms_options['buddyforms'][$buddyform['slug']]['post_type'];
											
											$form->addElement( new Element_Select(__("Post Type:", 'buddyforms'), "buddyforms_options[buddyforms][".$buddyform['slug']."][post_type]", $post_types,array('value' => $post_type)));
										
											$attached_page = 'false';
											if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['attached_page']))
												$attached_page = $buddyforms_options['buddyforms'][$buddyform['slug']]['attached_page'];
											
											$args = array( 
												'id' => $key, 
												'echo' => FALSE,
												'sort_column'  => 'post_title',
												'show_option_none' => __( 'none', 'buddyforms' ),
												'name' => "buddyforms_options[buddyforms][".$buddyform['slug']."][attached_page]",
												'class' => 'postform',
												'selected' => $attached_page
											);
											$form->addElement(new Element_HTML("<br><br><p><b>" . __('Attach page to this form', 'buddyforms') . "</b></p><i>" . __('Select a page for the author, call it e.g. "My Posts".', 'buddyforms'). "</i><br><br>"));
											$form->addElement(new Element_HTML(wp_dropdown_pages($args)));
											
											$form->addElement(new Element_HTML('<br>'.__('Or you can', 'buddyforms').' <a href="'. admin_url( add_query_arg( array( 'post_type' => 'page' ), 'post-new.php' ) ).'" class="btn btn-small">'. __( 'Create A New Page', 'buddyforms' ).'</a>'));
							
										
										$form->addElement(new Element_HTML('</div>'));
										
									$form->addElement(new Element_HTML('</div>'));
									$form->addElement(new Element_HTML('<div class="buddyforms_accordion_bottom">'));
											$form->addElement(new Element_HTML('<h3><p>'.__('Notification settings', 'buddyforms').'</p></h3>'));
										$form->addElement(new Element_HTML('</div>'));
										
									$form->addElement( new Element_HTML('
								</div>
							</div>
						</div>'));	
					
					$form->addElement(new Element_HTML('
					<br>
					<h4>'.__('Form Builder', 'buddyforms').'</h4>
					<p>'.__('Add additional form elements from the right box "Form Elements". Change the order via drag and drop.', 'buddyforms').'</p>
					<ul id="sortable_'. $buddyform['slug'] .'" class="sortable sortable_'. $buddyform['slug'] .'">'));

					if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['form_fields'])){
						
						foreach($buddyforms_options['buddyforms'][$buddyform['slug']]['form_fields'] as $field_id => $customfield) {
							
							
							if(isset($customfield['slug']))	
								$slug = sanitize_title($customfield['slug']);	
							
							if(empty($slug))
								$slug = sanitize_title($customfield['name']);
							
							
							if( $slug != '' ){
								$args = Array(
									'slug'				=> $slug,
									'field_position'	=> $customfield['order'],
									'field_id'			=> $field_id,
									'form_slug'			=> $buddyform['slug'],
									'post_type'			=> $buddyform['post_type'],
									'field_type'		=> $customfield['type']
									);
								$form->addElement(new Element_HTML(buddyforms_view_form_fields($args)));
							}
							
						}
					} 
					$form->addElement(new Element_HTML('</ul></div></div></div>'));
			    }	
			}

			$form = apply_filters( 'buddyforms_before_admin_form_render', $form);

		$form->addElement(new Element_HTML('</div>'));			
	$form->render();
}

function buddyforms_form_element_multiple($form_fields, $args){
		
	extract( $args );
	
	$form_fields['left']['html_1'] = new Element_HTML('
	<div class="element_field">
	<p>'.__('Checkbox Values', 'buddyforms').':</p>
		 <ul id="'.$form_slug.'_field_'.$field_id.'" class="element_field_sortable">');
		 if(isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['value'])){
		 	$count = 1;
		 	 foreach ($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['value'] as $key => $value) {
				$form_fields['left']['html_li_start_'.$key]	= new Element_HTML('<li class="field_item field_item_'.$field_id.'_'.$count.'">');
				$form_fields['left']['html_value_'.$key] 	= new Element_Textbox(__("Entry ", 'buddyforms').$key, "buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][value][]", array('value' => $value));
				$form_fields['left']['html_li_end_'.$key]	= new Element_HTML('<a href="#" id="'.$field_id.'_'.$count.'" class="delete_input" title="delete me">X</a> - <a href="#" id="'.$field_id.'" title="drag and move me!">'.__('move', 'buddyforms').'</a></li>');
				$count++;
			 }
		 }   		
		$form_fields['left']['html_2'] = new Element_HTML(' 
	    </ul>
     </div>
     <a href="'.$form_slug.'/'.$field_id.'" class="button add_input">+</a>
    ');	
	
	return $form_fields;
}?>