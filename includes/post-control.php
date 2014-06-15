<?php

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

		$form->addElement($element);

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
					if(isset($customfield['value']) && is_array($customfield['value'])){
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
                        'hide_empty'        => 0,
						'id'                => $key,
						'child_of'          => 0,
						'echo'              => FALSE,
						'selected'          => false,
                        'hierarchical'      => 1,
						'name'              => $slug . '[]',
						'class'             => 'postform chosen',
						'depth'             => 0,
						'tab_index'         => 0,
                        'taxonomy'          => $customfield['taxonomy'],
						'hide_if_empty'     => FALSE,
                        'orderby'           => 'SLUG',
                        'order'             => $customfield['taxonomy_order'],
					);

                    if(isset($customfield['show_option_none']) && !isset($customfield['multiple']))
                        $args = array_merge( $args, Array( 'show_option_none' => 'Nothing Selected' ) );

                    if(isset($customfield['multiple']))
                        $args = array_merge( $args, Array( 'multiple' => $customfield['multiple'] ) );

					$dropdown = wp_dropdown_categories($args);

                    if (isset($customfield['multiple']) && is_array( $customfield['multiple'] ))
                        $dropdown = str_replace('id=', 'multiple="multiple" id=', $dropdown);

                    if (isset($customfield['required']) && is_array( $customfield['required'] ))
                        $dropdown = str_replace('id=', 'required id=', $dropdown);

                    if (is_array($customfield_val)) {
						foreach ($customfield_val as $value) {
							$dropdown = str_replace(' value="' . $value . '"', ' value="' . $value . '" selected="selected"', $dropdown);
						}
					}
                    $required = '';
                    if(isset($customfield['required']) && is_array( $customfield['required'] )){
                        $required = '<span class="required">* </span>';
                    }
                    $dropdown = '<div class="bf_field_group">
                        <label for="editpost-element-' . $key . '">
                            '.$required.$customfield['name'] . ':
                        </label>
                        <div class="bf_inputs">' . $dropdown . ' </div>
                        <span class="help-inline">' . $customfield['description'] . '</span>
                    </div>';

					$element = new Element_HTML($dropdown);
					bf_add_element($form, $element);
					
					if(isset($customfield['creat_new_tax']) ){
						$element = new Element_Textbox('Create a new ' . $customfield['taxonomy'].':', $slug.'_creat_new_tax', array('class' => 'settings-input'));
						bf_add_element($form, $element);
					}

					break;
					
				default:
					
					// hook to add your form element
					apply_filters('buddyforms_create_edit_form_display_element',$form, $post_id, $form_slug, $customfield, $customfield_val);
					
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
			
			if( isset( $_POST[$customfield['slug'].'_creat_new_tax']) && !empty($_POST[$customfield['slug'].'_creat_new_tax'] ) ){
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
	if( isset( $_FILES['file']['size'] ) && $_FILES['file']['size'] > 0 ) {

        require_once(ABSPATH . 'wp-admin/includes/admin.php');
        $id = media_handle_upload('file', $post_id ); //post id of Client Files page
        //unset( $_FILES );

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
        return $hasError;
	}

}

?>