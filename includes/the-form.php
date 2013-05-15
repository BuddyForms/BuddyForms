<?php
/**
 * Adds a form shortcode for the create and edit sreen
 * 
 * @package CPT4BP
 * @since 0.1-beta	
 */
function cpt4bp_create_edit_form( $args = array() ) {
    global $post, $bp, $current_user, $cpt4bp, $post_id;

	$args = apply_filters('cpt4bp_create_edit_form_args',$args);
	
	extract(shortcode_atts(array(
		'posttype' => $bp->current_component,
		'the_post' => 0,
		'post_id' => $post_id
	), $args));

    get_currentuserinfo();	
  	
	if($_GET[post_id]) { 
    			
    	$post_id		= $_GET[post_id]; 
        $posttype		= $bp->current_component;
       	$the_post		= get_post( $post_id );
		$post_id		= $the_post->ID;
	   
		if ($the_post->post_author != $current_user->ID){
			echo '<div class="error alert">You are not allowed to edit this Post what are you doing here?</div>';
			return;	
		}
		
	} elseif($post_id == 0){
		
		$the_post = new stdClass;
		$the_post->ID 			= $post_id;
		$the_post->post_type 	= $bp->current_component;
		$the_post->post_title 	= '';
	
	}
     
   	if( empty( $posttype ) )
   	   $posttype = $the_post->post_type;
	
	$customfields = $cpt4bp['bp_post_types'][$posttype]['form_fields'];
		
	//If the form is submitted
	if( isset( $_POST['submitted'] ) ) {

		$permalink = get_permalink( $_POST['editpost_id'] );
        
        if( isset( $_POST['new_post_id'] ) && ! empty( $_POST['new_post_id'] ) ) {
        	                     
			$my_post = array(
                'ID'        	=> $_POST['new_post_id'],
                'post_title' 	=> $_POST['editpost_title'],
                'post_content' 	=> $_POST['editpost_content'],
                'post_type' 	=> $posttype,
                'post_status' 	=> 'publish'
			);
                
            // update the new post
            $post_id = wp_update_post( $my_post );
			
		} else {
			
			  $my_post = array(
                'post_author' 	=> $current_user->ID,
                'post_title' 	=> $_POST['editpost_title'],
                'post_content' 	=> $_POST['editpost_content'],
                'post_type' 	=> $posttype,
                'post_status' 	=> $cpt4bp['bp_post_types'][$posttype]['status']
            );   
                
            // insert the new form
            $post_id = wp_insert_post($my_post);
			
		}
		
        if(isset($customfields)){
        	
			foreach( $customfields as $key => $customfield ) : 
			   
				if( $customfield['type'] == 'Taxonomy' ){
				
					if($customfield['taxonomy'] == 'category'){
				
						wp_set_post_terms( $post_id, $_POST[ sanitize_title( $customfield['name'] ) ], $customfield['taxonomy'], false );
				
					} else {
				
						$slug = Array();
						$postCategories = $_POST[ sanitize_title( $customfield['name'] ) ];
						
						foreach ( $postCategories as $postCategory ) {
							$term = get_term_by('id', $postCategory, $customfield['taxonomy']);
							$slug[] = $term->slug;
						}
							
						wp_set_post_terms( $post_id, $slug, $customfield['taxonomy'], false );
				
					}
					
				}
		
				do_action('cpt4bp_update_post_meta',$customfield,$post_id,$_POST);
               
				update_post_meta($post_id, sanitize_title($customfield['name']), $_POST[sanitize_title($customfield['name'])] ); 
				                   
            endforeach;
    	}

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
		            $fileError 	= '<p>There has bean an error uploading the image.</p>';  
		            $hasError 	= true;
		        }  
	       	}
		}        
	
		if( empty( $hasError ) ) {
	
			?>
			<div class="thanks">
				<?php if($_GET['post_id']){ ?>
		            <h1><?php _e('Saved', 'cpt4bp')?></h1>
		            <p><?php _e('Post has been updated.', 'cpt4bp'); ?> </p>
	   			<?php } else { ?>
	   				<h1><?php _e('Saved', 'cpt4bp')?></h1>
		    	    <p><?php _e('Post has been created.', 'cpt4bp'); ?> </p>
				<?php } ?>
			</div>
			<?php
	
		}
	}     
	?>
	<div class="the_cpt4bp_form">
		<style>
			.standard-form textarea, .standard-form input[type=text], .standard-form input[type=url],.standard-form input[type=link],.standard-form input[type=email], .standard-form input[type=password]{
				width: 75%;
				border: 1px inset #ccc;
				-moz-border-radius: 3px;
				-webkit-border-radius: 3px;
				border-radius: 3px;
				color: #888;
				font: inherit;
				font-size: 14px;
				padding: 6px;
			}
			
		/** BuddyPress Fix  for the Theme Compatibility **/
			#buddypress table tr td, #buddypress table tr th {
				padding: 0px;
				vertical-align: middle;
			}
			
		</style>
		<?php if ( !is_user_logged_in() ) : ?>
			
			<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ) ?>" method="post">
				
				<div style="float:left; margin-right:10px;">
					<label><?php _e( 'Username', 'cpt4bp' ) ?><br />
					<input type="text" style="width:200px;" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" /></label>
				</div>
				<div style="float:left;margin-right:10px;">
					<label><?php _e( 'Password', 'cpt4bp' ) ?><br />
					<input type="password" style="width:200px;" name="pwd" id="sidebar-user-pass" class="input" value="" tabindex="98" /></label>
				</div>
				<label><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="99" /> <?php _e( 'Remember Me', 'cpt4bp' ) ?></label>	 
				<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In', 'cpt4bp'); ?>" tabindex="100" />
				<input type="hidden" name="cpt4bpcookie" value="1" />
				<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
			</form>
				 
		<?php else :
		
			if( isset( $_POST['editpost_title'])) {
				
				if(function_exists('stripslashes')) {
					$editpost_title = stripslashes($_POST['editpost_title']);
				} else {
					$editpost_title = $_POST['editpost_title'];
				}
				} else {
					$editpost_title =  $the_post->post_title;
				}

				if( isset( $_POST['editpost_content'] ) ){
					$editpost_content_val = $_POST['editpost_content'];
				} else {
					$editpost_content_val = $the_post->post_content;
			}
			?>	
			<div class="form_wrapper">
				<?php // Form starts
				$form = new Form("editpost");
				$form->configure(array("prevent" => array("bootstrap", "jQuery", "focus"), "action" => $_SERVER['REQUEST_URI'], "view" => new View_Vertical,'class' => 'standard-form'));
	
					$form->addElement(new Element_HTML(wp_nonce_field('client-file-upload', '_wpnonce', true, false)));
				$form->addElement(new Element_Hidden("new_post_id", $post_id, array('value' => $post_id, 'id' => "new_post_id")));
				$form->addElement(new Element_Hidden("redirect_to", $_SERVER['REQUEST_URI']));
	
				if ($bp->current_component != 'groups') {
					
					$form->addElement(new Element_HTML('<div class="label"><label>Title</label></div>'));
					$form->addElement(new Element_Textbox("Title:", "editpost_title", array('lable' => 'enter a title', "required" => 1, 'value' => $editpost_title)));
					$form->addElement(new Element_HTML('<div class="label"><label>Content</label></div>'));

					ob_start();
						$settings = array('wpautop' => true, 'media_buttons' => true, 'wpautop' => true, 'tinymce' => true, 'quicktags' => true, 'textarea_rows' => 18);
						$post = get_post($post_id, 'OBJECT');
						wp_editor($post->post_content, 'editpost_content', $settings);
						$wp_editor = ob_get_contents();
					ob_clean();
					
					$form->addElement(new Element_HTML($wp_editor));
					
				} else {
					$post = get_post($post_id, 'OBJECT');
					$form->addElement(new Element_Hidden("editpost_title", $editpost_title));
					$form->addElement(new Element_Hidden("editpost_content", $post->post_content));
				}
	
				if ($customfields) {
					foreach ($customfields as $key => $customfield) :
						if (isset($_POST[sanitize_title($customfield['name'])])) {
							if (function_exists('stripslashes')) {
								$customfield_val = $_POST[sanitize_title($customfield['name'])];
							} else {
								$customfield_val = $_POST[sanitize_title($customfield['name'])];
							}
	
						} else {
							$customfield_val = get_post_meta($post_id, sanitize_title($customfield['name']), true);
						}
						switch( $customfield['type'] ) {
								case 'Mail' :
								$element_attr = $customfield['required'] ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input') : array('value' => $customfield_val, 'class' => 'settings-input');
								$form->addElement(new Element_Email($customfield['name'] . ':<p><smal>' . $customfield['description'] . '</smal></p>', sanitize_title($customfield['name']), $element_attr));
								break;
	
							case 'Radiobutton' :
								$element_attr = $customfield['required'] ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input') : array('value' => $customfield_val, 'class' => 'settings-input');
								$form->addElement(new Element_Radio($customfield['name'] . ':<p><smal>' . $customfield['description'] . '</smal></p>', sanitize_title($customfield['name']), explode(",", $customfield['Values']), $element_attr));
								break;
	
							case 'Checkbox' :
								$element_attr = $customfield['required'] ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input') : array('value' => $customfield_val, 'class' => 'settings-input');
								$form->addElement(new Element_Checkbox($customfield['name'] . ':<p><smal>' . $customfield['description'] . '</smal></p>', sanitize_title($customfield['name']), explode(",", $customfield['Values']), $element_attr));
								break;
	
							case 'Dropdown' :
								$element_attr = $customfield['required'] ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input') : array('value' => $customfield_val, 'class' => 'settings-input');
								$form->addElement(new Element_Select($customfield['name'] . ':', sanitize_title($customfield['name']), explode(",", $customfield['Values']), $element_attr));
								break;
	
							case 'Textarea' :
								$element_attr = $customfield['required'] ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input') : array('value' => $customfield_val, 'class' => 'settings-input');
								$form->addElement(new Element_Textarea($customfield['name'] . ':<p><smal>' . $customfield['description'] . '</smal></p>', sanitize_title($customfield['name']), $element_attr));
								break;
	
							case 'Hidden' :
								$form->addElement(new Element_Hidden(sanitize_title($customfield['name'], $customfield['value'])));
								break;
	
							case 'Text' :
								$element_attr = $customfield['required'] ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input') : array('value' => $customfield_val, 'class' => 'settings-input');
								$form->addElement(new Element_Textbox($customfield['name'] . ':<p><smal>' . $customfield['description'] . '</smal></p>', sanitize_title($customfield['name']), $element_attr));
								break;
	
							case 'Link' :
								$element_attr = $customfield['required'] ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input') : array('value' => $customfield_val, 'class' => 'settings-input');
								$form->addElement(new Element_Url($customfield['name'] . ':<p><smal>' . $customfield['description'] . '</smal></p>', sanitize_title($customfield['name']), $element_attr));
	
								break;
	
							case 'Taxonomy' :
								$term_list = wp_get_post_terms($post_id, $customfield['taxonomy'], array("fields" => "ids"));
	
								$args = array('multiple' => $customfield['multiple'], 'selected_cats' => $term_list, 'hide_empty' => 0, 'id' => $key, 'child_of' => 0, 'echo' => FALSE, 'selected' => false, 'hierarchical' => 1, 'name' => sanitize_title($customfield['name']) . '[]', 'class' => 'postform', 'depth' => 0, 'tab_index' => 0, 'taxonomy' => $customfield['taxonomy'], 'hide_if_empty' => FALSE, );
	
								$dropdown = wp_dropdown_categories($args);
	
								if (is_array($customfield['multiple'])) {
									$dropdown = str_replace('id=', 'multiple="multiple" id=', $dropdown);
								}
								if (is_array($term_list)) {
									foreach ($term_list as $value) {
										$dropdown = str_replace(' value="' . $value . '"', ' value="' . $value . '" selected="selected"', $dropdown);
									}
								}
	
								$form->addElement(new Element_HTML($customfield['taxonomy'] . ':<p><smal>' . $customfield['description'] . '</smal></p>'));
								$form->addElement(new Element_HTML($dropdown));
								break;
								
							default:
								apply_filters('cpt4bp_create_edit_form_display_element',$form,$post_id,$posttype,$customfield,$customfield_val);
								
								break;
	
						}

					endforeach;
				}
	
				
				if ($cpt4bp['bp_post_types'][$posttype]['featured_image']['required'][0] == 'Required'){
					if ($post_id == 0) {
						$file_attr = array("required" => 1, 'id' => "async-upload");
					} else {
						$file_attr = array('id' => "async-upload");
					}
					$form->addElement(new Element_File('Featured Image:', 'async-upload', $file_attr));
				}
	
				$form->addElement(new Element_Hidden("submitted", 'true', array('value' => 'true', 'id' => "submitted")));
				$form->addElement(new Element_Button('submitted', 'submit', array('id' => 'submitted', 'name' => 'submitted')));
				
				$form->render(); ?>
			
			</div>
	</div>		
	<?php endif;
}
add_shortcode('cpt4bp_create_edit_form', 'cpt4bp_create_edit_form');