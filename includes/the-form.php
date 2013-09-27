<?php
/**
 * Adds a form shortcode for the create and edit screen
 * @var $args = posttype, the_post, post_id
 * 
 * @package buddyforms
 * @since 0.1-beta	
*/
function buddyforms_create_edit_form( $args = array() ) {
    global $post, $bp, $current_user, $buddyforms, $post_id, $wp_query, $form_slug;
	session_id('buddyforms-create-edit-form');

	// if post edit screen is displayed
	wp_enqueue_style('the-form-css', plugins_url('css/the-form.css', __FILE__));
	
	 // hook for plugins to overwrite the $args.
	$args = apply_filters('buddyforms_create_edit_form_args',$args);
	
	extract(shortcode_atts(array(
		'post_type' => '',
		'the_post' => 0,
		'post_id' => $post_id,
		'revision_id' => false,
		'form_slug' => $form_slug,
	), $args));

	get_currentuserinfo();	

	if(empty($post_type))
		$post_type = $buddyforms['buddyforms'][$form_slug]['post_type'];

	// if post edit screen is displayed in pages
	if(isset($wp_query->query_vars['bf_action'])){
		
		
		$action = $wp_query->query_vars['bf_action'];
		
		$form_slug = '';
		if(isset($wp_query->query_vars['bf_form_slug']))
			$form_slug = $wp_query->query_vars['bf_form_slug'];
		
		$post_id = '';	
		if(isset($wp_query->query_vars['bf_post_id']))
			$post_id = $wp_query->query_vars['bf_post_id'];
		
		$revision_id = '';	
		if(isset($wp_query->query_vars['bf_rev_id']))
			$revision_id = $wp_query->query_vars['bf_rev_id'];
		
		$post_type = $buddyforms['buddyforms'][$form_slug]['post_type'];


		if(!empty($revision_id)) {
			$the_post		= get_post( $revision_id );
		} else {
			$the_post		= get_post($post_id, 'OBJECT');
		}

		if($wp_query->query_vars['bf_action'] == 'edit'){
			
		    $user_can_edit = false;
			if ($the_post->post_author == $current_user->ID){
				$user_can_edit = true;
			}
			$user_can_edit = apply_filters( 'buddyforms_user_can_edit', $user_can_edit );
			
	       	if ( $user_can_edit == false ){
	       		$error_message = __('You are not allowed to edit this post. What are you doing here?', 'buddyforms');
				echo '<div class="error alert">'.$error_message.'</div>';
				return;	
			}
			
		}
		
	}

	// if post edit screen is displayed
	if(!empty($post_id)) {
		   			
		if(!empty($revision_id)) {
			
			$the_post		= get_post( $revision_id );
		} else {
			$the_post		= get_post( $post_id );
		}
		
		$user_can_edit = false;
		if ($the_post->post_author == $current_user->ID){
			$user_can_edit = true;
		}
		$user_can_edit = apply_filters( 'buddyforms_user_can_edit', $user_can_edit );
		
       	if ( $user_can_edit == false ){
       		$error_message = __('You are not allowed to edit this post. What are you doing here?', 'buddyforms');
			echo '<div class="error alert">'.$error_message.'</div>';
			return;	
		}
		
	}
	
	// If post_id == 0 a new post is created 	
	if($post_id == 0){
		$the_post = new stdClass;
		$the_post->ID 			= $post_id;
		$the_post->post_type 	= $post_type;
		$the_post->post_title 	= '';
	}
 	 
   	if( empty( $post_type ) )
   	   $post_type = $the_post->post_type;
		
	if( empty( $form_slug ) )
   	   $form_slug = apply_filters('buddyforms_the_form_to_use',$form_slug, $post_type);
	
	if(isset($buddyforms['buddyforms'][$form_slug]['form_fields']))
		$customfields = $buddyforms['buddyforms'][$form_slug]['form_fields'];
		
	// If the form is submitted we will get in action
	if( isset( $_POST['submitted'] ) ) {
			
		$hasError = false;
		
		if($buddyforms['buddyforms'][$form_slug]['form_type'] == 'mail_form'){
			$error_message = __('Mail Forms are not supported in this Version and this is just a placeholder!', 'buddyforms');
			echo '<div class="error alert">'.$error_message.'</div>';
			return;	
		} 
			
		$comment_status = $buddyforms['buddyforms'][$form_slug]['comment_status'];
		
		if(isset($_POST['comment_status']))
			$comment_status = $_POST['comment_status'];
		
		$post_excerpt = '';
		if(isset($_POST['post_excerpt']))
			$post_excerpt = $_POST['post_excerpt'];
		
		$action			= 'save';
		$post_status	= $buddyforms['buddyforms'][$form_slug]['status'];
		
		echo '$_POST[new_post_id]' . $_POST['new_post_id'];
		
		if( isset( $_POST['new_post_id'] ) && ! empty( $_POST['new_post_id'] ) ){
			$action = 'update';
			$post_status = 'publish';
		}
			
		$args = Array(
			'action'			=> $action,
			'form_slug'			=> $form_slug,
			'post_type' 		=> $post_type,
			'post_excerpt'		=> $post_excerpt,
			'post_author' 		=> $current_user->ID,
			'post_status' 		=> $post_status,
			'comment_status'	=> $comment_status,
		);
			
		   	   //$args = apply_filters('buddyforms_the_form_to_use',$form_slug, $post_type);	
			
		$hasError = bf_post_control($args, $hasError);
				
		// Check if the post has post meta / custom fields 
		if(isset($customfields))
			bf_update_post_meta($post_id, $customfields);
		
		$hasError = bf_set_post_thumbnail($post_id, $hasError);
		
		// Save the Form slug as post meta 
		update_post_meta($post_id, "_bf_form_slug", $form_slug);
		
		// Display the message  
		if( empty( $hasError ) ) :
			
			if(isset( $_POST['new_post_id'] ) && ! empty( $_POST['new_post_id'] )){
				$info_message = __('The post has been successfully updated', 'buddyforms');
				$form_notice = '<div class="info alert">'.$info_message.'</div>';
			} else {
				$info_message = __('The post has been successfully created', 'buddyforms');
				$form_notice = '<div class="info alert">'.$info_message.'</div>';
				//wp_redirect( get_permalink(get_page_by_path( $buddyforms['buddyforms'][$form_slug]['attached_page'] )) );
			} 
			
		 else: 

			$error_message = __('Error! There was a problem submitting the post ;-(', 'buddyforms');
			$form_notice = '<div class="error alert">'.$error_message.'</div>';
			
			if(!empty($fileError))
				$form_notice = '<div class="error alert">'.$fileError.'</div>';
			
		endif;
		
		do_action('buddyforms_after_save_post',$post_id);
		
	} 
	
	$form_html = '<div class="the_buddyforms_form">';
	?>	
		<?php if ( !is_user_logged_in() ) : 
			ob_start();?>
			<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ) ?>" method="post">
				
				<div style="float:left; margin-right:10px;">
					<label><?php _e( 'Username', 'buddyforms' ) ?><br />
					<input type="text" style="width:200px;" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" /></label>
				</div>
				<div style="float:left;margin-right:10px;">
					<label><?php _e( 'Password', 'buddyforms' ) ?><br />
					<input type="password" style="width:200px;" name="pwd" id="sidebar-user-pass" class="input" value="" tabindex="98" /></label>
				</div>
				<label><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="99" /> <?php _e( 'Remember Me', 'buddyforms' ) ?></label>	 
				<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Login', 'buddyforms'); ?>" tabindex="100" />
				<input type="hidden" name="buddyformscookie" value="1" />
				<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
			</form>
				 
		<?php 
			$form_html .= ob_get_contents();
			ob_clean();
		else :

			if( isset( $_POST['editpost_title'])) {
				if(function_exists('stripslashes')) {
					$editpost_title = stripslashes($_POST['editpost_title']);
				} else { $editpost_title = $_POST['editpost_title']; }
			} else {
				$editpost_title =  $the_post->post_title;
			}
			
			$editpost_content_val = false;
			if( isset( $_POST['editpost_content'] ) ){
				$editpost_content_val = $_POST['editpost_content'];
			} else {
				if(!empty($the_post->post_content))
					$editpost_content_val = $the_post->post_content;
			}
			
			$form_html .= '<div class="form_wrapper">';

				$form = new Form("editpost");
				$form->configure(array("prevent" => array("bootstrap", "jQuery", "focus"), "action" => $_SERVER['REQUEST_URI'], "view" => new View_Vertical,'class' => 'standard-form'));
	
				$form->addElement(new Element_HTML(wp_nonce_field('client-file-upload', '_wpnonce', true, false)));
				$form->addElement(new Element_Hidden("new_post_id", $post_id ));
				$form->addElement(new Element_Hidden("redirect_to", $_SERVER['REQUEST_URI']));
				if(isset($form_notice))
					$form->addElement(new Element_HTML($form_notice));
				
				$form->addElement(new Element_HTML('<div class="bf_field_group bf_form_title">'));
				$form->addElement(new Element_Textbox("Title:", "editpost_title", array("required" => 1, 'value' => $editpost_title)));
				$form->addElement(new Element_HTML('</div>'));

				ob_start();
					$settings = array('wpautop' => true, 'media_buttons' => true, 'wpautop' => true, 'tinymce' => true, 'quicktags' => true, 'textarea_rows' => 18);
					if(isset($post_id)){
						wp_editor($editpost_content_val, 'editpost_content', $settings);
					} else {
						$content = false;
						$post = 0;
						wp_editor($content, 'editpost_content', $settings);
					}
					$wp_editor = ob_get_contents();
				ob_clean();
				
				$wp_editor = '<div class="bf_field_group bf_form_content"><label>Content:</label><div class="bf_inputs">'.$wp_editor.'</div></div>';
				$form->addElement(new Element_HTML($wp_editor));
				
				// if the form have custom field to save as post meta data they get displayed here 
				if(isset($customfields))
					bf_post_meta($form, $form_slug, $post_id, $customfields);
				
				// Display upload field for featured image if required is selected for this form
				if (isset($buddyforms['buddyforms'][$form_slug]['featured_image']['required'][0])){
					if ($post_id == 0) {
						$file_attr = array("required" => 1, 'id' => "async-upload");
					} else {
						$file_attr = array('id' => "async-upload");
					}
					$form->addElement(new Element_File('Featured Image:', 'async-upload', $file_attr));
				}
	
				$form->addElement(new Element_Hidden("submitted", 'true', array('value' => 'true', 'id' => "submitted")));
				$form->addElement(new Element_Button('Submit', 'submit', array('id' => 'submitted', 'name' => 'submitted')));
				
				// thats it! render the form!
				ob_start();
					$form->render(); 
					$form_html .= ob_get_contents();
				ob_clean();
		
			$form_html .= '</div>';

			if(isset($buddyforms['buddyforms'][$form_slug]['revision']) && $post_id != 0){
				 
			ob_start(); 
				buddyforms_wp_list_post_revisions($post_id);
				$form_html .= ob_get_contents();
			ob_clean();


			 }
			$form_html .= '</div>';	
		endif;
		
	echo $form_html;
}

/**
 * Delete a post 
 *
 * @package BuddyForms
 * @since 0.3 beta
 */
function buddyforms_delete_post(){
	global $wp_query, $buddyforms, $current_user;
	
		if(isset($wp_query->query_vars['bf_action'])){
		
		$action = $wp_query->query_vars['bf_action'];
		$form_slug = $wp_query->query_vars['bf_form_slug'];
		$post_id = $wp_query->query_vars['bf_post_id'];
		$post_type = $buddyforms['buddyforms'][$form_slug]['post_type'];

		if(isset($revision_id)) {
			$the_post		= get_post( $revision_id );
		} else {
			$the_post		= get_post( $post_id );
		}
       	
		if($wp_query->query_vars['bf_action'] == 'delete'){
			if ($the_post->post_author != $current_user->ID) {
				echo '<div id="message" class="info alert"><p>'.__("You are not allowed to delete this entry! What are you doing here?","buddyforms").'</p></div>';
				return;
			}
			do_action('buddyforms_delete_post',$post_id);
			wp_delete_post( $post_id );
		}	
	}
	$args = array(
		'form_slug' => $form_slug,
	);
       
	buddyforms_the_loop($args);
}

function bf_add_element($form, $element){
	if(is_object($form)) {
		$form->addElement($element);
	} else {
		echo '
		<div class="bf_field_group bf_form_title">
			<div class="bf_field_group">
				<label for="editpost-element-' . $element->getAttribute('name') . '">' .  $element->getLabel() . '</label>
				<div class="bf_inputs">';
					$element->render();
		echo 	'</div>
			</div>
		</div>';
	}
}
function bf_post_meta($form, $form_slug, $post_id, $customfields){
	
	if (!isset($customfields))
		return;
			
	foreach ($customfields as $key => $customfield) :
		
		if(isset($customfield['slug']))
			$slug = sanitize_title($customfield['slug']);	
			
		if($slug == '')
			$slug = sanitize_title($customfield['name']);

		if($slug != '') :
		
			if (isset($_POST[$slug] )) {
				$customfield_val = $_POST[$slug];
			} else {
				$customfield_val = get_post_meta($post_id, $slug, true);
			}

			switch( $customfield['type'] ) {
				case 'Mail' :
					$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']);
					$element = new Element_Email($customfield['name'], $slug, $element_attr);
					bf_add_element($form, $element);
					break;

				case 'Radiobutton' :
					$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']);
					if(is_array($customfield['value'])){
						$element = new Element_Radio($customfield['name'], $slug, $customfield['value'], $element_attr);
						bf_add_element($form, $element);
					}
					break;

				case 'Checkbox' :
					$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']);
					if(is_array($customfield['value'])){
						$element = new Element_Checkbox($customfield['name'], $slug, $customfield['value'], $element_attr);
						bf_add_element($form, $element);
					}
					break;

				case 'Dropdown' :
					
					$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input chosen', 'shortDesc' =>  $customfield['description']);
					if(is_array($customfield['value'])){
						$element = new Element_Select($customfield['name'], $slug, $customfield['value'], $element_attr);

						if (isset($customfield['multiple']) && is_array( $customfield['multiple'] )) 
							$element->setAttribute('multiple', 'multiple');

						bf_add_element($form, $element);
					}
					break;
				
				case 'Comments' :
					$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input');
					$element = new Element_Select($customfield['name'] , 'comment_status', array('open','closed'), $element_attr);
					bf_add_element($form, $element);
					break;

				case 'Textarea' :
					$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']);
					$element = new Element_Textarea($customfield['name'], $slug, $element_attr);
					bf_add_element($form, $element);
					break;

				case 'Hidden' :
					$element = new Element_Hidden($customfield['name'], $customfield['value']);
					bf_add_element($form, $element);
					break;

				case 'Text' :
					$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']);
					$element = new Element_Textbox($customfield['name'], $slug, $element_attr);
					bf_add_element($form, $element);
					break;

				case 'Link' :
					$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']);
					$element = new Element_Url($customfield['name'], $slug, $element_attr);
					bf_add_element($form, $element);
					break;

				case 'Taxonomy' :
					
					$args = array(
						'multiple' => $customfield['multiple'],
						'hide_empty' => 0,
						'id' => $key,
						'child_of' => 0,
						'echo' => FALSE,
						'selected' => false,
						'hierarchical' => 1,
						'name' => $slug . '[]',
						'class' => 'postform chosen',
						'depth' => 0,
						'tab_index' => 0,
						'taxonomy' => $customfield['taxonomy'],
						'hide_if_empty' => FALSE,
					);

					$dropdown = wp_dropdown_categories($args);

					if (isset($customfield['multiple']) && is_array( $customfield['multiple'] )) 
						$dropdown = str_replace('id=', 'multiple="multiple" id=', $dropdown);
					
					if (is_array($customfield_val)) {
						foreach ($customfield_val as $value) {
							$dropdown = str_replace(' value="' . $value . '"', ' value="' . $value . '" selected="selected"', $dropdown);
						}
					}
					
					$element = new Element_HTML('<label>'.$customfield['name'] . ':</label><p><i>' . $customfield['description'] . '</i></p>');
					bf_add_element($form, $element);
					
					$element = new Element_HTML($dropdown);
					bf_add_element($form, $element);
					
					if(isset($customfield['creat_new_tax'])){
						$element = new Element_Textbox('Create a new ' . $customfield['taxonomy'].':', $slug.'_creat_new_tax', array('class' => 'settings-input'));
						bf_add_element($form, $element);
					}
					
					break;
					
				default:
					
					// hook to add your form element
					apply_filters('buddyforms_create_edit_form_display_element',$form,$post_id,$form_slug,$customfield,$customfield_val);
					
					break;

			}
		endif;
	endforeach;

}

function bf_update_post_meta($post_id, $customfields){

    if(!isset($customfields))
		return;
	
	foreach( $customfields as $key => $customfield ) : 
	   
		if( $customfield['type'] == 'Taxonomy' ){
				
			$taxonomy = get_taxonomy($customfield['taxonomy']);
			
			if (isset($taxonomy->hierarchical) && $taxonomy->hierarchical == true)  {
				
				if(isset($_POST[ $customfield['slug'] ]))					
					wp_set_post_terms( $post_id, $_POST[ $customfield['slug'] ], $customfield['taxonomy'], false );
			} else {
			
				$slug = Array();
				
				if(isset($_POST[ $customfield['slug'] ])) {
					$postCategories = $_POST[ $customfield['slug'] ];
				
					foreach ( $postCategories as $postCategory ) {
						$term = get_term_by('id', $postCategory, $customfield['taxonomy']);
						$slug[] = $term->slug;
					}
				}
				
				wp_set_post_terms( $post_id, $slug, $customfield['taxonomy'], false );

			}
			
			if(isset($_POST[$customfield['slug'].'_creat_new_tax'])){
				$wp_insert_term = wp_insert_term($_POST[$customfield['slug'].'_creat_new_tax'],$customfield['taxonomy']);
				wp_set_post_terms( $post_id, $wp_insert_term, $customfield['taxonomy'], true );
			}
		}
		// Update meta do_action to hook into. This can be interesting if you added new form elements and want to manipulate how they get saved.
		do_action('buddyforms_update_post_meta',$customfield, $post_id);
       
	   	if(isset($customfield['slug']))
	   		$slug = $customfield['slug'];	
		
		if($slug == '')
			$slug = sanitize_title($customfield['name']);
		
		// Update the post
		if(isset($_POST[$slug] )){
			update_post_meta($post_id, $slug, $_POST[$slug] );
		} else {
			update_post_meta($post_id, $slug, '' );
		}
			 		                   
    endforeach;

}

function bf_post_control($args,$hasError){
	global $buddyforms, $post_id;
	extract($args);
	
    // Check if post is new or edit 
    if( $action == 'update' ) {
    
    	                     
		$my_post = array(
            'ID'        		=> $_POST['new_post_id'],
            'post_title' 		=> $_POST['editpost_title'],
            'post_content' 		=> $_POST['editpost_content'],
            'post_type' 		=> $post_type,
            'post_status' 		=> $post_status,
            'comment_status'	=> $comment_status,
            'post_excerpt'		=> $post_excerpt
		);
            
		// Update the new post
        $post_id = wp_update_post( $my_post );
		
		if($post_id == 0 )
			$hasError = true;
		
	} else {
		
		  $my_post = array(
            'post_author' 		=> $post_author,
            'post_title' 		=> $_POST['editpost_title'],
            'post_content' 		=> $_POST['editpost_content'],
            'post_type' 		=> $post_type,
            'post_status' 		=> $buddyforms['buddyforms'][$form_slug]['status'],
            'comment_status'	=> $comment_status,
            'post_excerpt'		=> $post_excerpt
        );   
        
        // Insert the new form
        $post_id = wp_insert_post( $my_post, true );
		
		if($post_id == 0 )
			$hasError = true;
		
	}
	return $hasError;
}

function bf_set_post_thumbnail($post_id,$hasError){
// Featured image? If yes, save via media_handle_upload and set the post thumbnail
	if( ! empty( $_FILES ) ) {
		
		require_once(ABSPATH . 'wp-admin/includes/admin.php');  
        $id = media_handle_upload('async-upload', $post_id ); //post id of Client Files page  

        unset( $_FILES );  
        if( is_wp_error( $id ) ) {  
            $errors['upload_error'] = $id;  
            $id = false;  
        } 
		
        $set_post_thumbnail =  set_post_thumbnail($post_id, $id);
      
       	if( $set_post_thumbnail == false){
           	if( $errors ) {  
	            $fileError 	= '<p>'.__( 'There has been an error uploading the image.', 'buddyforms' ).'</p>';  
	        }  
			$hasError = true;
       	}
	}	
	return $hasError;
}
