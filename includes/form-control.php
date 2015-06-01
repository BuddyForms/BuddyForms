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

function bf_post_control($args){

	extract($args = apply_filters( 'bf_post_control_args', $args ));

    if( isset($_POST['data'])){
        parse_str($_POST['data'], $formdata);
    } else {
        $formdata = $_POST;
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