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
		
		//$hasError = bf_set_post_thumbnail($post_id, $hasError);
		
		// Save the Form slug as post meta 
		update_post_meta($post_id, "_bf_form_slug", $form_slug);
		
		// Display the message  
		if( empty( $hasError ) ) :
			
			if(isset( $_POST['new_post_id'] ) && ! empty( $_POST['new_post_id'] )){
				$info_message = __('The '.$buddyforms['buddyforms'][$form_slug]['singular_name'].' has been successfully updated', 'buddyforms'). '<a href="'.get_permalink($post_id).'" target="_blank"> View '.$buddyforms['buddyforms'][$form_slug]['singular_name'].'</a>';
				$form_notice = '<div class="info alert">'.$info_message.'</div>';
			} else {
				$info_message = __('The '.$buddyforms['buddyforms'][$form_slug]['singular_name'].' has been successfully created', 'buddyforms'). '<a href="'.get_permalink($post_id).'" target="_blank"> View '.$buddyforms['buddyforms'][$form_slug]['singular_name'].'</a>';
				$form_notice = '<div class="info alert">'.$info_message.'</div>';
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
				$form->addElement(new Element_HTML(do_action( 'template_notices' )));
				
				$form->configure(array("prevent" => array("bootstrap", "jQuery", "focus"), "action" => $_SERVER['REQUEST_URI'], "view" => new View_Vertical,'class' => 'standard-form'));
	
				$form->addElement(new Element_HTML(wp_nonce_field('client-file-upload', '_wpnonce', true, false)));
				$form->addElement(new Element_Hidden("new_post_id", $post_id ));
				$form->addElement(new Element_Hidden("redirect_to", $_SERVER['REQUEST_URI']));
				if(isset($form_notice))
					$form->addElement(new Element_HTML($form_notice));
				
				$form->addElement(new Element_HTML('<div class="bf_field_group bf_form_title">'));
				$form->addElement(new Element_Textbox("Title:", "editpost_title", array("required" => 1, 'value' => $editpost_title)));
				

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
						$file_attr = array("required" => 1, 'id' => "file");
					} else {
						$file_attr = array('id' => "file");
					}
					$form->addElement(new Element_File('Featured Image:', 'file', $file_attr));

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
?>