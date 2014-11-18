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
                    $element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $customfield['description']);
                    $form->addElement(new Element_Email($customfield['name'], $slug, $element_attr));
                    break;

                case 'Radiobutton' :
                    $element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $customfield['description']);
                    if (is_array($customfield['value'])) {
                        $form->addElement(new Element_Radio($customfield['name'], $slug, $customfield['value'], $element_attr));
                    }
                    break;

                case 'Checkbox' :
                    $element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $customfield['description']);
                    if (is_array($customfield['value'])) {
                        $form->addElement(new Element_Checkbox($customfield['name'], $slug, $customfield['value'], $element_attr));
                    }
                    break;

                case 'Dropdown' :
                    $element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input chosen', 'shortDesc' => $customfield['description']);
                    if (isset($customfield['value']) && is_array($customfield['value'])) {
                        $element = new Element_Select($customfield['name'], $slug, $customfield['value'], $element_attr);

                        if (isset($customfield['multiple']) && is_array($customfield['multiple']))
                            $element->setAttribute('multiple', 'multiple');

                        bf_add_element($form, $element);
                    }
                    break;

                case 'Comments' :
                    $element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input');
                    $form->addElement(new Element_Select($customfield['name'], 'comment_status', array('open', 'closed'), $element_attr));
                    break;

                case 'Status' :
                    global $buddyforms;

                    if (is_array($customfield['post_status'])){
                        if (in_array('pending', $customfield['post_status']))
                            $post_status['pending'] = 'Pending Review';

                        if (in_array('publish', $customfield['post_status']))
                            $post_status['publish'] = 'Published';

                        if (in_array('draft', $customfield['post_status']))
                            $post_status['draft'] = 'Draft';


                        if (in_array('future', $customfield['post_status']) && empty($customfield_val) || in_array('future', $customfield['post_status']) && get_post_status($post_id) == 'future')
                            $post_status['future'] = 'Scheduled';

                        if (in_array('private', $customfield['post_status']))
                            $post_status['private'] = 'Privately Published';

                        if (in_array('private', $customfield['post_status']))
                            $post_status['trash'] = 'Trash';

                        $element_attr = array('value' => $customfield_val, 'class' => 'settings-input');
                        $form->addElement(new Element_Select($customfield['name'], 'status', $post_status, $element_attr));


                        if (isset($_POST[$slug])) {
                            $schedule_val = $_POST['schedule'];
                        } else {
                            $schedule_val = get_post_meta($post_id, 'schedule', true);
                        }

                        $element_attr = array('value' => $schedule_val, 'class' => 'settings-input, bf_datetime');

                        $form->addElement(new Element_HTML('<div class="bf_datetime_wrap">'));
                        $form->addElement(new Element_Textbox('Schedule Time', 'schedule', $element_attr));
                        $form->addElement(new Element_HTML('</div>'));
                    }
                    break;

                case 'Textarea' :
					$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']);
					$form->addElement( new Element_Textarea($customfield['name'], $slug, $element_attr));
					break;

				case 'Hidden' :
					$form->addElement( new Element_Hidden($customfield['name'], $customfield['value']));
					break;

				case 'Text' :
					$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']);
					$form->addElement( new Element_Textbox($customfield['name'], $slug, $element_attr));
					break;

				case 'Link' :
					$element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']) : array('value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $customfield['description']);
					$form->addElement( new Element_Url($customfield['name'], $slug, $element_attr));
					break;
                case 'Featured-Image':

                    // Display upload field for featured image if required is selected for this form
                    if($customfield['required'] && !has_post_thumbnail( $post_id )){
                        $file_attr = array("required" => 1, 'id' => "file", 'shortDesc' => $customfield['description'] );
                    } else {
                        $file_attr = array('id' => "file", 'shortDesc' => $customfield['description'] );
                    }

                    $form->addElement(new Element_HTML( get_the_post_thumbnail($post_id, array(80,80))));

                    $form->addElement(new Element_File(__('Featured Image:', 'buddyforms'), 'file', $file_attr));



                    break;
                case 'File':

                    $attachment_id  = get_post_meta($post_id, 'file_'.$slug, true);
                    $attachment_url = wp_get_attachment_url($attachment_id);
                    $attachment_desc_view = $customfield['description'];
                    $attachment_desc_view_delete = $customfield['description'];

                    if(!empty($attachment_id)){
                        $attachment_desc_view .= '<div id="'.$attachment_id.'"><a href="' . $attachment_url . '" target="_new">View '. $customfield['name'] .'</a></div>';
                        $attachment_desc_view_delete .= '<div id="'.$attachment_id.'"><a href="' . $attachment_url . '" target="_new">View '. $customfield['name'] .'</a> | <a href="'.$post_id.'/file_'.$slug .'" id="'.$attachment_id.'" class="remove_attachment">Delete '. $customfield['name'] .'</a></div>';
                    }

                    // Display upload field for featured image if required is selected for this form
                    if($customfield['required'] && empty($attachment_id)){
                        $file_attr = array("required" => 1, 'id' => $slug, 'shortDesc' => $attachment_desc_view );
                    } elseif($customfield['required'] && !empty($attachment_id)) {
                        $file_attr = array('id' => $slug, 'shortDesc' => $attachment_desc_view);
                    } else {
                        $file_attr = array('id' => $slug, 'shortDesc' =>  $attachment_desc_view_delete );
                    }

                    $form->addElement(new Element_File($customfield['name'], $slug, $file_attr));

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

					$form->addElement( new Element_HTML($dropdown));
					
					if(isset($customfield['creat_new_tax']) ){
						$form->addElement( new Element_Textbox(__('Create a new ', 'buddyforms') . $customfield['taxonomy'].':', $slug.'_creat_new_tax', array('class' => 'settings-input')));
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
                    $tax_item = $_POST[ $customfield['slug'] ];

                if($tax_item[0] == -1)
                    $tax_item[0] = $customfield['taxonomy_default'];

				wp_set_post_terms( $post_id, $tax_item, $customfield['taxonomy'], false );
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
	global $post_id;
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

        if($_POST['status'] == 'future' && $_POST['schedule'])
            $post_date = date('Y-m-d H:i:s',strtotime($_POST['schedule']));

		  $my_post = array(
            'post_author' 		=> $post_author,
            'post_title' 		=> $_POST['editpost_title'],
            'post_content' 		=> $_POST['editpost_content'],
            'post_type' 		=> $post_type,
            'post_status' 		=> $post_status,
            'comment_status'	=> $comment_status,
            'post_excerpt'		=> $post_excerpt,
            'post_date'         => $post_date,
            'post_date_gmt'     => $post_date
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
function bf_media_handle_upload($post_id){

    foreach($_FILES as $key => $file){
        if( $key != 'file') {

            if( isset( $_FILES[$key]['size'] ) && $_FILES[$key]['size'] > 0 ) {

                require_once(ABSPATH . 'wp-admin/includes/admin.php');
                $attachment_id = media_handle_upload($key, $post_id ); //post id of Client Files page
                //unset( $_FILES );

                if ( is_wp_error( $attachment_id ) ) {
                    echo 'There was an error uploading the file.';
                } else {
                    update_post_meta( $post_id, 'file_'.$key, $attachment_id);
                }

            }
        }

    }
}

function buddyforms_delete_attachment(){

    $delete_attachment_id = $_POST['delete_attachment_id'];
    $delete_attachment_href = $_POST['delete_attachment_href'];

    $delete_attachment_attr = explode('/',$delete_attachment_href);

    wp_delete_attachment( $delete_attachment_id );

    delete_post_meta($delete_attachment_attr[0], $delete_attachment_attr[1]);

    echo $_POST['delete_attachment_id'];

    die();

}
add_action('wp_ajax_buddyforms_delete_attachment', 'buddyforms_delete_attachment');
add_action('wp_ajax_nopriv_buddyforms_delete_attachment', 'buddyforms_delete_attachment');
?>