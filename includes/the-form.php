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
		
		if($buddyforms['buddyforms'][$form_slug]['form_type'] == 'mail_form'){
			//$wpmail = wp_mail( $buddyforms['buddyforms'][$form_slug]['email'], $buddyforms['buddyforms'][$form_slug]['email_subject'], 'Ein test');

			// if($wpmail == TRUE){ 
				// echo '<p>Form has been sent successful.</p>';
			// } elseif($wpmail == FALSE){
				// echo '<p>There has been an error submitting the form.</p>';
			// }
			return;
		} 
			
		$comment_status = $buddyforms['buddyforms'][$form_slug]['comment_status'];
		
		if(isset($_POST['comment_status']))
				$comment_status = $_POST['comment_status'];
			
        // Check if post is new or edit 
        if( isset( $_POST['new_post_id'] ) && ! empty( $_POST['new_post_id'] ) ) {
        	                     
			$my_post = array(
                'ID'        		=> $_POST['new_post_id'],
                'post_title' 		=> $_POST['editpost_title'],
                'post_content' 		=> $_POST['editpost_content'],
                'post_type' 		=> $post_type,
                'post_status' 		=> 'publish',
                'comment_status'	=> $comment_status,
			);
                
			// Update the new post
            $post_id = wp_update_post( $my_post );
			
			
		} else {
			
			  $my_post = array(
                'post_author' 		=> $current_user->ID,
                'post_title' 		=> $_POST['editpost_title'],
                'post_content' 		=> $_POST['editpost_content'],
                'post_type' 		=> $post_type,
                'post_status' 		=> $buddyforms['buddyforms'][$form_slug]['status'],
                'comment_status'	=> $comment_status,
            );   
            
            // Insert the new form
            $post_id = wp_insert_post( $my_post );
			
		}
		// Check if the post has post meta / custom fields 
        if(isset($customfields)){
        	
			foreach( $customfields as $key => $customfield ) : 
			   
				if( $customfield['type'] == 'Taxonomy' ){
					
					// Check if the custom field is a taxonomy
					// * We need to check if the taxonomy is a normal category, because categories want id's and custom taxonomies slugs... ;-()
					if($customfield['taxonomy'] == 'category'){
				
						wp_set_post_terms( $post_id, $_POST[ sanitize_title( $customfield['name'] ) ], $customfield['taxonomy'], false );
						
						if($_POST[$customfield['slug'].'_creat_new_tax']){
							$wp_insert_term = wp_insert_term($_POST[$customfield['slug'].'_creat_new_tax'],$customfield['taxonomy']);
							wp_set_post_terms( $post_id, $wp_insert_term, $customfield['taxonomy'], true );
						}
						
					} else {
				
						$slug = Array();
						
						if(isset($_POST[ sanitize_title( $customfield['name'] ) ])) {
							$postCategories = $_POST[ sanitize_title( $customfield['name'] ) ];
						
							foreach ( $postCategories as $postCategory ) {
								$term = get_term_by('id', $postCategory, $customfield['taxonomy']);
								$slug[] = $term->slug;
							}
							
							wp_set_post_terms( $post_id, $slug, $customfield['taxonomy'], false );
						}
						if(isset($_POST[$customfield['slug'].'_creat_new_tax'])){
							$wp_insert_term = wp_insert_term($_POST[$customfield['slug'].'_creat_new_tax'],$customfield['taxonomy']);
							wp_set_post_terms( $post_id, $wp_insert_term, $customfield['taxonomy'], true );
						}
					}
					
				}
				// Update meta do_action to hook into. This can be interesting if you added new form elements and want to manipulate how they get saved.
				do_action('buddyforms_update_post_meta',$customfield,$post_id,$_POST);
               
			   	if(isset($customfield['slug']))
			   		$slug = sanitize_title($customfield['slug']);	
				
				if($slug == '')
					$slug = sanitize_title($customfield['name']);
				
				// Update the post
				if(isset($_POST[$slug] ))
					update_post_meta($post_id, $slug, $_POST[$slug] ); 
				                   
            endforeach;
    	}
		

		// Save the Form slug as post meta 
		update_post_meta($post_id, "_bf_form_slug", $form_slug);
		
		// Featured image? If yes, save via media_handle_upload and set the post thumbnail
		if( ! empty( $_FILES ) ) {
			
			require_once(ABSPATH . 'wp-admin/includes/admin.php');  
	        $id = media_handle_upload('async-upload', $post_id ); //post id of Client Files page  
	
	        unset( $_FILES );  
	        if( is_wp_error( $id ) ) {  
	            $errors['upload_error'] = $id;  
	            $id = false;  
	        } 
			
	        set_post_thumbnail($post_id, $id);
	      
	       	if( ! $the_post->ID){
	           	if( $errors ) {  
		            $fileError 	= '<p>'.__( 'There has been an error uploading the image.', 'buddyforms' ).'</p>';  
		            $hasError 	= true;
		        }  
	       	}
		}        
		
		// Display the message  
		if( empty( $hasError ) ) {
			ob_start();?>
				<div class="thanks">
					<?php if(isset($_GET['post_id'])){ ?>
			            <h1><?php _e( 'Saved', 'buddyforms' ); ?></h1>
			            <p><?php _e( 'Post has been updated.', 'buddyforms' ); ?> </p>
		   			<?php } else { ?>
		   				<h1><?php _e( 'Saved', 'buddyforms' ); ?></h1>
			    	    <p><?php _e( 'Post has been created.', 'buddyforms' ); ?> </p>
					<?php } ?>
				</div>
			<?php
			$form_notice = ob_get_contents();
			ob_clean();
		}
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
				} else {
					$editpost_title = $_POST['editpost_title'];
				}
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
				$form->addElement(new Element_Hidden("new_post_id", $post_id, array('value' => $post_id, 'id' => "new_post_id")));
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
					//$wp_editor = apply_filters( 'buddyforms_wp_editor', $wp_editor );
					$form->addElement(new Element_HTML($wp_editor));
					
					// $form->addElement(new Element_HTML($wp_editor));
					
					// $post = get_post($post_id, 'OBJECT');
					// $form->addElement(new Element_Hidden("editpost_title", $editpost_title));
					// $form->addElement(new Element_Hidden("editpost_content", $post->post_content));
				
				// if the form have custom field to save as post meta data they get displayed here 
				if (isset($customfields)) {
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
							print_r($customfield_val);
							echo '<br>';
							switch( $customfield['type'] ) {
								case 'Mail' :
									$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']);
									$form->addElement(new Element_Email($customfield['name'], $slug, $element_attr));
									break;
		
								case 'Radiobutton' :
									$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']);
									if(is_array($customfield['value']))
										$form->addElement(new Element_Radio($customfield['name'], $slug, $customfield['value'], $element_attr));
									break;
		
								case 'Checkbox' :
									$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']);
									if(is_array($customfield['value']))
										$form->addElement(new Element_Checkbox($customfield['name'], $slug, $customfield['value'], $element_attr));
									break;
		
								case 'Dropdown' :
									$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']);
									if(is_array($customfield['value']))
										$form->addElement(new Element_Select($customfield['name'], $slug, $customfield['value'], $element_attr));
									break;
								
								case 'Comments' :
									$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input');
									$form->addElement(new Element_Select($customfield['name'] , 'comment_status', array('open','closed'), $element_attr));
									break;
		
								case 'Textarea' :
									$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']);
									$form->addElement(new Element_Textarea($customfield['name'], $slug, $element_attr));
									break;
		
								case 'Hidden' :
									$form->addElement(new Element_Hidden($customfield['name'], $customfield['value']));
									break;
		
								case 'Text' :
									$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']);
									$form->addElement(new Element_Textbox($customfield['name'], $slug, $element_attr));
									break;
		
								case 'Link' :
									$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']);
									$form->addElement(new Element_Url($customfield['name'], $slug, $element_attr));		
									break;
		
								case 'Taxonomy' :
									$term_list = wp_get_post_terms($post_id, $customfield['taxonomy'], array("fields" => "ids"));
		
									$args = array('multiple' => $customfield['multiple'], 'selected_cats' => $term_list, 'hide_empty' => 0, 'id' => $key, 'child_of' => 0, 'echo' => FALSE, 'selected' => false, 'hierarchical' => 1, 'name' => $slug . '[]', 'class' => 'postform', 'depth' => 0, 'tab_index' => 0, 'taxonomy' => $customfield['taxonomy'], 'hide_if_empty' => FALSE, );
		
									$dropdown = wp_dropdown_categories($args);
		
									if (is_array($customfield['multiple'])) {
										$dropdown = str_replace('id=', 'multiple="multiple" id=', $dropdown);
									}
									if (is_array($term_list)) {
										foreach ($term_list as $value) {
											$dropdown = str_replace(' value="' . $value . '"', ' value="' . $value . '" selected="selected"', $dropdown);
										}
									}
		
									$form->addElement(new Element_HTML('<label>'.$customfield['name'] . ':</label><p><i>' . $customfield['description'] . '</i></p>'));
									$form->addElement(new Element_HTML($dropdown));
	
									if($customfield['creat_new_tax'])
										$form->addElement(new Element_Textbox('Create a new ' . $customfield['taxonomy'].':', $slug.'_creat_new_tax', array('class' => 'settings-input')));
									
									break;
									
								default:
									
									// hook to add your form element
									apply_filters('buddyforms_create_edit_form_display_element',$form,$post_id,$post_type,$customfield,$customfield_val);
									
									break;
		
							}
						endif;
					endforeach;
				}
	
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

			if(isset($buddyforms['buddyforms'][$form_slug]['revision'])){
				 
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
