<?php

/**
 * Create "BuddyForms Options" nav menu
 *
 * @package buddyforms
 * @since 0.1-beta
 */
function buddyforms_create_menu() {
/*	if(session_id() != 'buddyforms') {
	  session_start('buddyforms');
	}*/
    if(!session_id());
    @session_start();

	global $bp, $buddyforms;
	
	// echo '<pre>';
	// print_r($buddyforms);
	// echo '</pre>';
	
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




        $update_option = false;
		$update_option = update_option("buddyforms_options", $buddyforms_options);
		
		add_action( 'admin_notices', create_function('', 'echo "<div id=\"settings_updated\" class=\"updated\"> <p><strong>Settings saved.</strong></p></div>";') );
		 
	}
   
	add_menu_page( 'BuddyForms', 'BuddyForms', 'edit_posts', 'buddyforms_options_page', 'buddyforms_options_content' );
	add_submenu_page( 'buddyforms_options_page', 'Add New', 'Add New', 'edit_posts', 'create-new-form', 'bf_import_export_screen' );

}  
add_action('admin_menu', 'buddyforms_create_menu');

/**
 * Display the settings page
 *
 * @package buddyforms
 * @since 0.2-beta
 */
function buddyforms_options_content() {

    // Check that the user is allowed to update options
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    global $bp, $buddyforms;

     /*echo '<pre>';
     print_r($buddyforms);
     echo '</pre>';*/
    ?>
		
	<div id="bf_admin_wrap" class="wrap">

        <div class="credits">
            <p>
                <a class="buddyforms" href="http://buddyforms.com" title="BuddyForms" target="_blank"><img src="<?php echo plugins_url( 'img/buddyforms-s.png' , __FILE__ ); ?>" title="BuddyForms" /></a>
                - &nbsp; <?php _e( 'Collaborative Publishing and Form Magic for WordPress.', 'buddyforms' ); ?>

            </p>
        </div>

        <h1 style="line-height: 58px; margin-top: 20px;"><div style="font-size: 52px; margin-top: -2px; float: left; margin-right: 15px;" class="tk-icon-buddyforms"></div> BuddyForms <span class="version">Beta</span>
            <a href="<?php echo get_admin_url(); ?>admin.php?page=create-new-form" class="add-new-h2">Create New Form</a>
        </h1>

		<div id="bf_support_nav" class="button-nav">
            <a class="btn btn-small" href="https://themekraft.zendesk.com/hc/en-us/categories/200022561-BuddyForms" title="BuddyForms Documentation" target="_new"><i class="icon-list-alt"></i> Documentation</a>
            <a onClick="script: Zenbox.show(); return false;" class="btn btn-small" href="#" title="Write us. Bugs. Ideas. Whatever."><i class="icon-comment"></i> Submit a support ticket</a>
            <!--            &nbsp; &nbsp;-->
            <a class="btn btn-small" href="https://themekraft.zendesk.com/hc/communities/public/topics/200001402-BuddyForms-Ideas" title="Add and vote for ideas in our Ideas Forums!" target="_new"><i class="icon-plus-sign"></i> Submit your ideas</a>
            <a class="btn btn-small" href="https://themekraft.zendesk.com/hc/communities/public/topics/200001402-BuddyForms-Ideas" title="Learn, share and discuss with other users in our free community forums!" target="_new"><i class="icon-circle-arrow-right"></i> Visit community forums</a>
		</div>

        <hr />

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
	
/*	$form->addElement(new Element_HTML('<br><div class="bs-sidebar"><ul id="bf_side_nav" class="nav bs-sidenav">
		<li class="active"><a href="#general-settings" data-toggle="tab"><i>&raquo; Setup</i></a></li>'));
		
	if(isset($buddyforms_options['buddyforms'])){
		foreach( $buddyforms_options['buddyforms'] as $key => $buddyform) {
			$tabname = $buddyform['name'];
			if(empty($tabname))
				$tabname = $buddyform['slug'];

			$form->addElement(new Element_HTML('<li class=""><a href="#'.$buddyform['slug'].'" data-toggle="tab">'.$tabname.'</a></li>'));
		}
	}
	$form->addElement(new Element_HTML('</ul></div>*/
    $form->addElement(new Element_HTML('<div class="tab-content"><div class="subcontainer tab-pane" id="create-new-form">'));
    $form->addElement(new Element_HTML('
			<div class="hero-unit-konrad">sss'));



    $form->addElement(new Element_HTML('</div></div></div>'));
    $form->addElement(new Element_HTML('<div class="tab-content"><div class="subcontainer tab-pane fade in active" id="general-settings">'));
    /*$form->addElement(new Element_HTML('
<div class="accordion_sidebar" id="accordion_save">
    <div class="accordion-group">
        <div class="accordion-heading"><p class="accordion-toggle">Save Setup</p></div>
        <div id="accordion_save" class="accordion-body">
            <div class="accordion-inner">'));
                $form->addElement(new Element_Hidden("submit", "submit"));
                $form->addElement(new Element_Button('submit','submit',array('id' => 'submit', 'name' => 'action','value' => 'Save')));

                $form->addElement(new Element_HTML('
            </div>
        </div>
    </div>
</div>'));*/
					$form->addElement(new Element_HTML('
			<div class="hero-unit-konrad">'));

    $form->addElement(new Element_HTML('
<table class="wp-list-table widefat fixed posts">
	<thead>
		<tr>
			<th scope="col" id="cb" class="manage-column column-cb check-column" style="">
				<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
				<input id="cb-select-all-1" type="checkbox">
			</th>
			<th scope="col" id="name" class="manage-column column-comment sortable desc" style="">Name</th>
			<th scope="col" id="slug" class="manage-column column-description" style="">Slug</th>
			<th scope="col" id="attached-post-type" class="manage-column column-status" style="">Attached Post Type</th>
			<th scope="col" id="attached-page" class="manage-column column-status" style="">Attached Page</th>

	</thead>'));
foreach ($buddyforms['buddyforms'] as $key => $buddyform) {
    $form->addElement(new Element_HTML(' <tr>
        <th scope="row" class="check-column">
            <label class="screen-reader-text" for="aid-' . $buddyform['slug'] . '">' . $buddyform['name'] .'</label>
            <input type="checkbox" name="bf_export_form_slugs[]" value="' . $buddyform['slug'] . '" id="aid-' . $buddyform['slug'] . '">
            <a href="#' . $buddyform['slug'] . '" data-toggle="tab">edit</a>
        </th>
        <td class="slug column-slug"> ' . $buddyform['name'] .' </td>
        <td class="slug column-slug"> ' . $buddyform['name'] .' </td>
       <td class="slug column-slug"> ' . $buddyform['name'] .'</td>
        <td class="slug column-slug"> ' . get_the_title($buddyform['attached_page']) .' </td>
</tr>'));
}
$form->addElement(new Element_HTML('</table>'));



			/*			$form->addElement(new Element_HTML('
			  <h3>BuddyForms Setup</h3>
			'));*/
			/*$form->addElement(new Element_HTML('
	 		<div class="accordion-group create-form-box">
				<div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_buddyforms_general_settings_create_form" href="#accordion_buddyforms_general_settings_create_form">Create New Form</p></div>
			    <div id="accordion_buddyforms_general_settings_create_form" class="accordion-body collapse">
					<div class="accordion-inner">')); 
						$form->addElement(new Element_Textbox("Name:", "create_new_form_name",array('id' => 'create_new_form_name', 'placeholder' => 'e.g. Movies')));
						$form->addElement(new Element_Textbox("Singular Name:", "create_new_form_singular_name",array('id' => 'create_new_form_singular_name', 'placeholder' => 'e.g. Movie')));
						
						$form->addElement(new Element_HTML('<div class="clear"></div><br>'));
						$form->addElement(new Element_Button('button','button',array('class' => 'new_form', 'name' => 'new_form','value' => 'Create Form')));
						
						$form->addElement( new Element_HTML('
					</div>
				</div>
			</div>'));*/
			
			/*$form->addElement(new Element_HTML('<br /><br /><h3>Extensions Setup</h3><p><a style="text-decoration: none;" href="http://themekraft.com/products/wordpress-plugins-free-and-premium/buddyforms-front-end-posting/" target="_new" title="See our BuddyForms Extensions" class="btn">Browse BuddyForms Extensions</a></p>'));*/

			//$form = apply_filters('buddyforms_general_settings', $form);

			$form->addElement(new Element_HTML('</div></div>'));

			if(isset($buddyforms_options['buddyforms'])){
				foreach( $buddyforms_options['buddyforms'] as $key => $buddyform) {
					
			    	$form->addElement(new Element_HTML('<div class="subcontainer tab-pane fade in" id="'.$buddyform['slug'].'">'));
						
					$form->addElement(new Element_HTML('
					<div class="accordion_sidebar" id="accordion_'.$buddyform['slug'].'">
						<div class="accordion-group">
							<div class="accordion-heading"><p class="accordion-toggle">Save This Form</p></div>
							<div id="accordion_'.$buddyform['slug'].'_save" class="accordion-body">
								<div class="accordion-inner">')); 
									$form->addElement(new Element_Hidden("submit", "submit"));
									$form->addElement(new Element_Button('submit','submit',array('id' => 'submit', 'name' => 'action','value' => 'Save')));
									// $form->addElement(new Element_HTML('<p></p>'));
                    $form->addElement(new Element_Button('button','button',array('id' => $buddyform['slug'], 'class' => 'dele_form', 'name' => 'dele_form','value' => 'Delete')));

                    $form->addElement(new Element_HTML('<input type="button" class="button" onClick="history.go(0)" value="Cancel">
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
									<div>
										<p>Add new elements to your form <br>by clicking on them.</p>
										<h5>Classic Fields</h5>
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
					$form->addElement(new Element_HTML('<div class="loading-animation-new alert alert-success">Load new element <i class="icon-ok"></i></div>
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
							<div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$buddyform['slug'].'" href="#accordion_'.$buddyform['slug'].'_status"><b>Form Control</b><br><i class="small">Choose Form Type and Moderation.</i></p></div>
						    <div id="accordion_'.$buddyform['slug'].'_status" class="accordion-body collapse">
								<div class="accordion-inner">')); 
									$form->addElement(new Element_HTML('<p><i class="icon-info-sign" style="margin-top:6px;"></i>&nbsp;<small><i>These settings can be overwritten by shortcodes and other plugins! <br>You can define the defaults here. </i></small></p><br />'));
									$form->addElement(new Element_HTML('<div class="innerblock form-type">'));
									
									if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['form_type']))
										$form_type = $buddyforms_options['buddyforms'][$buddyform['slug']]['form_type'];
									
									if(empty($form_type))
										$form_type = 'post_form';
									
									$form->addElement( new Element_Radio("<h4>Form Type</h4>", "buddyforms_options[buddyforms][".$buddyform['slug']."][form_type]", array('post_form','mail_form'),array('id' => $buddyform['slug'], 'class' => 'form_type', 'value' => $form_type)));
									$form->addElement(new Element_HTML('</div><div class="clear"></div>'));
									$form->addElement(new Element_HTML('<div class="mail_form_'.$buddyform['slug'].' form_type_settings" >'));
											$form->addElement(new Element_HTML('<p>NOT READY YET<br>I will leave the mail/notification development for later and focus on the logic of form and post control first. After the logic is deaply tested, we will put the same patition into mail and notification.</p>'));
											$email = '';
											if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['email']))
												$email = $buddyforms_options['buddyforms'][$buddyform['slug']]['email'];
											
											$form->addElement(new Element_Textbox("Enter your email address:", "buddyforms_options[buddyforms][".$buddyform['slug']."][email]", array('value' => $email)));
											
											$email_subject = '';
											if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['email_subject']))
												$email_subject = $buddyforms_options['buddyforms'][$buddyform['slug']]['email_subject'];
												
											$form->addElement(new Element_Textbox("What should the subject line be?", "buddyforms_options[buddyforms][".$buddyform['slug']."][email_subject]", array('value' => $email_subject)));
									$form->addElement(new Element_HTML('</div>'));
									$form->addElement(new Element_HTML('<div class="post_form_'.$buddyform['slug'].' form_type_settings" >'));
										$form->addElement(new Element_HTML('<div class="buddyforms_accordion_right">'));
											$form->addElement(new Element_HTML('<div class="innerblock featured-image">'));
											
											$required = 'false';
											if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['featured_image']['required']))
												$required = $buddyforms_options['buddyforms'][$buddyform['slug']]['featured_image']['required'];
											
											$form->addElement( new Element_Checkbox("<b>Featured Image</b>","buddyforms_options[buddyforms][".$buddyform['slug']."][featured_image][required]",array('Required'),array('value' => $required)));
											$form->addElement(new Element_HTML('</div>'));
											$form->addElement(new Element_HTML('<div class="innerblock revision">'));
											
											$revision = 'false';
											if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['revision']))
												$revision = $buddyforms_options['buddyforms'][$buddyform['slug']]['revision'];
											
											$form->addElement( new Element_Checkbox("<b>Revision</b><br><i>Enable frontend revison control.</i>","buddyforms_options[buddyforms][".$buddyform['slug']."][revision]",array('Revision'),array('value' => $revision)));
											
											$admin_bar = 'false';
											if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['admin_bar']))
												$admin_bar = $buddyforms_options['buddyforms'][$buddyform['slug']]['admin_bar'];
											
											$form->addElement( new Element_Checkbox("<br><b>Admin Bar</b><br>","buddyforms_options[buddyforms][".$buddyform['slug']."][admin_bar]",array('Add to Admin Bar'),array('value' => $admin_bar)));
											
											$edit_link = 'false';
											if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['edit_link']))
												$edit_link = $buddyforms_options['buddyforms'][$buddyform['slug']]['edit_link'];
											
											$form->addElement( new Element_Checkbox("<br><b>Overwrite Edit-this-entry link?</b><br><i>The link to the backend will be changed<br> to use the frontend editing.</i>","buddyforms_options[buddyforms][".$buddyform['slug']."][edit_link]",array('overwrite'),array('value' => $edit_link)));
											
											$form->addElement(new Element_HTML('</div>'));
										$form->addElement(new Element_HTML('</div>'));
										$form->addElement(new Element_HTML('<div class="buddyforms_accordion_left">'));
											
											$status = 'false';
											if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['status']))
												$status = $buddyforms_options['buddyforms'][$buddyform['slug']]['status'];
												
											$form->addElement( new Element_Select("Status:", "buddyforms_options[buddyforms][".$buddyform['slug']."][status]", array('publish','pending','draft'),array('value' => $status)));
											
											$comment_status = 'false';
											if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['comment_status']))
												$comment_status = $buddyforms_options['buddyforms'][$buddyform['slug']]['comment_status'];
											
											$form->addElement( new Element_Select("Comment Status:", "buddyforms_options[buddyforms][".$buddyform['slug']."][comment_status]", array('open','closed'),array('value' => $comment_status)));
											
											$post_type = 'false';
											if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['post_type']))
												$post_type = $buddyforms_options['buddyforms'][$buddyform['slug']]['post_type'];
											
											$form->addElement( new Element_Select("Post Type:", "buddyforms_options[buddyforms][".$buddyform['slug']."][post_type]", $post_types,array('value' => $post_type)));
										
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
											$form->addElement(new Element_HTML('<br><br><p><b>Attach page to this form</b></p><i>Select a page for the author, <br>call it e.g. "My Posts".</i><br><br>'));
											$form->addElement(new Element_HTML(wp_dropdown_pages($args)));
											
											$form->addElement(new Element_HTML('<br>Or you can <a href="'. admin_url( add_query_arg( array( 'post_type' => 'page' ), 'post-new.php' ) ).'" class="btn btn-small">'. __( 'Create A New Page', 'buddypress' ).'</a>'));
							
										
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
					<p>Add elements from the right box "Form Elements". Change the order via drag and drop.</p>
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
	<p>Checkbox Values:</p>
		 <ul id="'.$form_slug.'_field_'.$field_id.'" class="element_field_sortable">');
		 if(isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['value'])){
		 	$count = 1;
		 	 foreach ($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['value'] as $key => $value) {
				$form_fields['left']['html_li_start_'.$key]	= new Element_HTML('<li class="field_item field_item_'.$field_id.'_'.$count.'">');
				$form_fields['left']['html_value_'.$key] 	= new Element_Textbox("Entry ".$key, "buddyforms_options[buddyforms][".$form_slug."][form_fields][".$field_id."][value][]", array('value' => $value));
				$form_fields['left']['html_li_end_'.$key]	= new Element_HTML('<a href="#" id="'.$field_id.'_'.$count.'" class="delete_input" title="delete me">X</a> - <a href="#" id="'.$field_id.'" title="drag and move me!">move</a></li>');
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