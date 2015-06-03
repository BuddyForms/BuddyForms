<?php

/**
 * Process the post and saves or update the post and post meta does the validation
 *
 * @package BuddyForms
 * @since 0.3 beta
 */

function buddyforms_process_post( $formdata ) {
    global $current_user, $buddyforms;

    $hasError = false;

    get_currentuserinfo();

    extract(shortcode_atts(array(
        'post_type' 	=> '',
        'the_post'		=> 0,
        'post_id'		=> 0,
        'revision_id' 	=> false,
        'form_slug' 	=> 0,
        'redirect_to'   => $_SERVER['REQUEST_URI'],
    ), $formdata));


    if(!empty($post_id)) {

        if(!empty($revision_id)) {
            $the_post	= get_post( $revision_id );
        } else {
            $post_id = apply_filters('bf_create_edit_form_post_id', $post_id);
            $the_post	= get_post( $post_id );
        }


        // Check if the user is author of the post
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

    // check if the user has the roles roles and capabilities
    $user_can_edit = false;
    if( empty($post_id) && current_user_can('buddyforms_' . $form_slug . '_create')) {
        $user_can_edit = true;
    } elseif( !empty($post_id) && current_user_can('buddyforms_' . $form_slug . '_edit')){
        $user_can_edit = true;
    }
    $user_can_edit = apply_filters( 'buddyforms_user_can_edit', $user_can_edit );
    if ( $user_can_edit == false ){
        $error_message = __('You do not have the required user role to use this form', 'buddyforms');
        return '<div class="error alert">'.$error_message.'</div>';
    }

    // If post_id == 0 a new post is created
    if($post_id == 0){
        require_once(ABSPATH . 'wp-admin/includes/admin.php');
        $the_post = get_default_post_to_edit($post_type);
    }

    if(isset($buddyforms['buddyforms'][$form_slug]['form_fields']))
        $customfields = $buddyforms['buddyforms'][$form_slug]['form_fields'];

    $comment_status = $buddyforms['buddyforms'][$form_slug]['comment_status'];
    if(isset($formdata['comment_status']))
        $comment_status = $formdata['comment_status'];

    $post_excerpt = '';
    if(isset($formdata['post_excerpt']))
        $post_excerpt = $formdata['post_excerpt'];

    $action			= 'save';
    $post_status	= $buddyforms['buddyforms'][$form_slug]['status'];
    if($post_id != 0){
        $action = 'update';
        $post_status = get_post_status( $post_id );
    }
    if(isset($formdata['status']))
        $post_status = $formdata['status'];

    $args = Array(
        'post_id'		    => $post_id,
        'action'			=> $action,
        'form_slug'			=> $form_slug,
        'post_type' 		=> $post_type,
        'post_excerpt'		=> $post_excerpt,
        'post_author' 		=> $current_user->ID,
        'post_status' 		=> $post_status,
        'post_parent' 		=> 0,
        'comment_status'	=> $comment_status,
    );

    $post_id = buddyforms_update_post($args);

    if($post_id != 0){

        // Check if the post has post meta / custom fields
        if(isset($customfields))
            bf_update_post_meta($post_id, $customfields);

        $hasError = bf_set_post_thumbnail($post_id);

        bf_media_handle_upload($post_id);

        // Save the Form slug as post meta
        update_post_meta($post_id, "_bf_form_slug", $form_slug);

    } else {
        $hasError = true;
    }

    // Display the message
    if( empty( $hasError ) ) :

        if(isset( $formdata['post_id'] ) && ! empty( $formdata['post_id'] )){
            $info_message = __('The ', 'buddyforms') . $buddyforms['buddyforms'][$form_slug]['singular_name']. __(' has been successfully updated', 'buddyforms'). '<a href="'.get_permalink($post_id).'" target="_blank"> View '.$buddyforms['buddyforms'][$form_slug]['singular_name'].'</a>';
            $form_notice = '<div class="info alert">'.$info_message.'</div>';
        } else {
            $info_message = __('The ', 'buddyforms') . $buddyforms['buddyforms'][$form_slug]['singular_name']. __(' has been successfully created', 'buddyforms'). '<a href="'.get_permalink($post_id).'" target="_blank"> View '.$buddyforms['buddyforms'][$form_slug]['singular_name'].'</a>';
            $form_notice = '<div class="info alert">'.$info_message.'</div>';
        }

    else:

        $error_message = __('Error! There was a problem submitting the post ;-(', 'buddyforms');
        $form_notice = '<div class="error alert">'.$error_message.'</div>';

        if(!empty($fileError))
            $form_notice = '<div class="error alert">'.$fileError.'</div>';

    endif;

    do_action('buddyforms_after_save_post', $post_id);

    $args = array(
        'post_type' 	=> $post_type,
        'the_post'		=> $the_post,
        'customfields'  => $customfields,
        'post_id'		=> $post_id,
        'revision_id' 	=> $revision_id,
        'redirect_to'   => $redirect_to,
        'form_slug' 	=> $form_slug,
        'form_notice'   => $form_notice,
    );

    return $args;

}

function buddyforms_update_post($args){

    extract( $args = apply_filters( 'buddyforms_update_post_args', $args ) );

    if( isset($_POST['data'])){
        parse_str($_POST['data'], $formdata);
    } else {
        $formdata = $_POST;
    }

    $buddyforms_form_nonce_value = $formdata['_wpnonce'];

    if ( !wp_verify_nonce( $buddyforms_form_nonce_value, 'buddyforms_form_nonce' ) ) {
        return false;
    }

    // Check if post is new or edit
    if( $action == 'update' ) {

        $bf_post = array(
            'ID'        		=> $formdata['post_id'],
            'post_title' 		=> $formdata['editpost_title'],
            'post_content' 		=> isset($formdata['editpost_content'])? $formdata['editpost_content'] : '',
            'post_type' 		=> $post_type,
            'post_status' 		=> $post_status,
            'comment_status'	=> $comment_status,
            'post_excerpt'		=> $post_excerpt
        );

        // Update the new post
        $post_id = wp_update_post( $bf_post );

    } else {

        if(isset($formdata['status']) && $formdata['status'] == 'future' && $formdata['schedule'])
            $post_date = date('Y-m-d H:i:s',strtotime($formdata['schedule']));

        $bf_post = array(
            'post_author' 		=> $post_author,
            'post_title' 		=> $formdata['editpost_title'],
            'post_content' 		=> isset($formdata['editpost_content'])? $formdata['editpost_content'] : '',
            'post_type' 		=> $post_type,
            'post_status' 		=> $post_status,
            'comment_status'	=> $comment_status,
            'post_excerpt'		=> $post_excerpt,
            'post_parent'		=> $post_parent,
            'post_date'         => isset($formdata['post_date'])? $formdata['post_date'] : '',
            'post_date_gmt'     => isset($formdata['post_date'])? $formdata['post_date'] : '',
        );

        // Insert the new form
        $post_id = wp_insert_post( $bf_post, true );

    }

    return $post_id;
}

function bf_update_post_meta($post_id, $customfields){

    if(!isset($customfields))
		return;

    if( isset($_POST['data'])){
        parse_str($_POST['data'], $formdata);
    } else {
        $formdata = $_POST;
    }

	foreach( $customfields as $key => $customfield ) : 
	   
		if( $customfield['type'] == 'Taxonomy' ){
				
			$taxonomy = get_taxonomy($customfield['taxonomy']);
			
			if (isset($taxonomy->hierarchical) && $taxonomy->hierarchical == true)  {
				
				if(isset($formdata[ $customfield['slug'] ]))
                    $tax_item = $formdata[ $customfield['slug'] ];

                if($tax_item[0] == -1 && !empty($customfield['taxonomy_default']))
                    $tax_item[0] = $customfield['taxonomy_default'];

				wp_set_post_terms( $post_id, $tax_item, $customfield['taxonomy'], false );
			} else {
			
				$slug = Array();
				
				if(isset($formdata[ $customfield['slug'] ])) {
					$postCategories = $formdata[ $customfield['slug'] ];
				
					foreach ( $postCategories as $postCategory ) {
						$term = get_term_by('id', $postCategory, $customfield['taxonomy']);
						$slug[] = $term->slug;
					}
				}
				
				wp_set_post_terms( $post_id, $slug, $customfield['taxonomy'], false );

			}
			
			if( isset( $formdata[$customfield['slug'].'_creat_new_tax']) && !empty($formdata[$customfield['slug'].'_creat_new_tax'] ) ){
				$creat_new_tax =  explode(',',$formdata[$customfield['slug'].'_creat_new_tax']);
				if(is_array($creat_new_tax)){
					foreach($creat_new_tax as $key => $new_tax){
						$wp_insert_term = wp_insert_term($new_tax,$customfield['taxonomy']);
						wp_set_post_terms( $post_id, $wp_insert_term, $customfield['taxonomy'], true );
					}
				}

			}
		}
		
		// Update meta do_action to hook into. This can be interesting if you added new form elements and want to manipulate how they get saved.
		do_action('buddyforms_update_post_meta',$customfield, $post_id);
       
	   	if(isset($customfield['slug']))
	   		$slug = $customfield['slug'];	
		
		if(empty($slug))
			$slug = sanitize_title($customfield['name']);
		
		// Update the post
		if(isset($formdata[$slug] )){
			update_post_meta($post_id, $slug, $formdata[$slug] );
		} else {
			update_post_meta($post_id, $slug, '' );
		}
			 		                   
    endforeach;

}

function bf_set_post_thumbnail($post_id){

    $hasError = false;
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

                if ( is_wp_error( $attachment_id ) ) {
                    echo 'There was an error uploading the file.';
                } else {
                    update_post_meta( $post_id, 'file_'.$key, $attachment_id);
                }

            }
        }
    }
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
