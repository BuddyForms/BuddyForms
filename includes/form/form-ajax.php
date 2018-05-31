<?php

add_action( 'buddyforms_process_submission_end','profile_picture_user_registration_ended' ,10,1 );
add_filter( 'login_redirect', 'login_redirect', 10, 3 );

function login_redirect( $redirect_to, $request, $user ){


    if(!empty($user)){

        $current_user = get_user_by( 'id', $user );
        if($current_user){
            $base_url = bp_core_get_user_domain( bp_loggedin_user_id() ) . 'shop/';
            wp_safe_redirect( $base_url,302);
            exit;
        }

    }

}
function crop_profile_picture($args = array()){

    // Bail if the original file is missing.
    if ( empty( $args['original_file'] ) ) {
        return false;
    }

    /* if ( ! bp_attachments_current_user_can( 'edit_avatar', $args ) ) {
         return false;
     }*/

    if ( 'user' === $args['object'] ) {
        $avatar_dir = 'avatars';
    } else {
        $avatar_dir = sanitize_key( $args['object'] ) . '-avatars';
    }

    $args['item_id'] = (int) $args['item_id'];

    /**
     * Original file is a relative path to the image
     * eg: /avatars/1/avatar.jpg
     */
    $relative_path = sprintf( '/%s/%s/%s', $avatar_dir, $args['item_id'], basename( $args['original_file'] ) );
    $upload_path = bp_core_avatar_upload_path();
    $url         = bp_core_avatar_url();
    $absolute_path = $upload_path. $relative_path;

    // Bail if the avatar is not available.
    if ( ! file_exists( $absolute_path ) )  {

        $create_new_folder = $upload_path."/avatars/". $args['item_id'];
        mkdir($create_new_folder , 0777, true);
        $picture_name =  explode("/", $absolute_path);
        $size = count($picture_name);
        $profile_pitcure = $picture_name[$size-1];
        $origen = $upload_path.'/avatars/0/'.$profile_pitcure;
        $existe = file_exists($origen);
        rename($origen,$create_new_folder.'/'.$profile_pitcure);
    }

    if ( empty( $args['item_id'] ) ) {

        /** This filter is documented in bp-core/bp-core-avatars.php */
        $avatar_folder_dir = apply_filters( 'bp_core_avatar_folder_dir', dirname( $absolute_path ), $args['item_id'], $args['object'], $args['avatar_dir'] );
    } else {

        /** This filter is documented in bp-core/bp-core-avatars.php */
        $avatar_folder_dir = apply_filters( 'bp_core_avatar_folder_dir', $upload_path . '/' . $args['avatar_dir'] . '/' . $args['item_id'], $args['item_id'], $args['object'], $args['avatar_dir'] );
    }

    // Bail if the avatar folder is missing for this item_id.
    if ( ! file_exists( $avatar_folder_dir ) ) {
        return false;
    }
    else{


    }


    // Delete the existing avatar files for the object.
    $existing_avatar = bp_core_fetch_avatar( array(
        'object'  => $args['object'],
        'item_id' => $args['item_id'],
        'html' => false,
    ) );

    /**
     * Check that the new avatar doesn't have the same name as the
     * old one before deleting
     */
    if ( ! empty( $existing_avatar ) && $existing_avatar !== $url . $relative_path ) {
        bp_core_delete_existing_avatar( array( 'object' => $args['object'], 'item_id' => $args['item_id'], 'avatar_path' => $avatar_folder_dir ) );
    }

    // Make sure we at least have minimal data for cropping.
    if ( empty( $args['crop_w'] ) ) {
        $args['crop_w'] = bp_core_avatar_full_width();
    }

    if ( empty( $args['crop_h'] ) ) {
        $args['crop_h'] = bp_core_avatar_full_height();
    }

    // Get the file extension.
    $data = @getimagesize( $absolute_path );
    $ext  = $data['mime'] == 'image/png' ? 'png' : 'jpg';

    $args['original_file'] = $absolute_path;
    $args['src_abs']       = false;
    $avatar_types = array( 'full' => '', 'thumb' => '' );

    $bp_attachmett = new BP_Attachment_Avatar();

    foreach ( $avatar_types as $key_type => $type ) {
        if ( 'thumb' === $key_type ) {
            $args['dst_w'] = bp_core_avatar_thumb_width();
            $args['dst_h'] = bp_core_avatar_thumb_height();
        } else {
            $args['dst_w'] = bp_core_avatar_full_width();
            $args['dst_h'] = bp_core_avatar_full_height();
        }

        $filename         = wp_unique_filename( $avatar_folder_dir, uniqid() . "-bp{$key_type}.{$ext}" );
        $args['dst_file'] = $avatar_folder_dir . '/' . $filename;

        $avatar_types[ $key_type ] = $bp_attachmett->crop( $args );
    }

    // Remove the original.
    @unlink( $absolute_path );

    // Return the full and thumb cropped avatars.
    return $avatar_types;
}
function crop_profile_picture_registration( $args = array() ) {
    $cropped           = crop_profile_picture( $args );

    // Check for errors.
    if ( empty( $cropped['full'] ) || empty( $cropped['thumb'] ) || is_wp_error( $cropped['full'] ) || is_wp_error( $cropped['thumb'] ) ) {
        return false;
    }

    return true;

}
function profile_picture_user_registration_ended($args){
    global $bp;
    if(isset($args['user_id']) && isset($_POST['user_login']) ) {

        $user_id = $args['user_id'];
        $bp->displayed_user->id = $user_id;
        $bp->loggedin_user->id = $user_id;
        $bp->displayed_user->domain = bp_core_get_user_domain( $bp->displayed_user->id );
        $bp->displayed_user->userdata = bp_core_get_core_userdata( $bp->displayed_user->id );
        $bp->displayed_user->fullname = bp_core_get_user_displayname( $bp->displayed_user->id );

        // Set the global user object

        $current_user = get_user_by( 'id', $args['user_id'] );

        // set the WP login cookie
        $secure_cookie = is_ssl() ? true : false;
        wp_set_auth_cookie( $args['user_id'], true, $secure_cookie );
        $r = array(
            'item_id'       => $args['user_id'],
            'object'        => 'user',
            'avatar_dir'    => 'avatars',
            'original_file' => $_POST['original-file-bf'],
            'crop_w'        => $_POST['crop_w_bf'],
            'crop_h'        => $_POST['crop_h_bf'],
            'crop_x'        => $_POST['crop_x_bf'],
            'crop_y'        => $_POST['crop_y_bf']
        );

        if ( crop_profile_picture_registration( $r ) ) {
            $return = array(
                'avatar' => html_entity_decode( bp_core_fetch_avatar( array(
                    'object'  => 'user',
                    'item_id' =>  $args['user_id'],
                    'html'    => false,
                    'type'    => 'full',
                ) ) ),
                'feedback_code' => 2,
                'item_id'       =>  $args['user_id'],
            );

            do_action( 'xprofile_avatar_uploaded', (int) $args['user_id'], 'avatar', $r );
            do_action('template_redirect') ;


          //  wp_send_json_success( $return );
        }

        apply_filters('login_redirect','','',$args['user_id']) ;
    }



}

add_action( 'wp_ajax_buddyforms_ajax_edit_post', 'buddyforms_ajax_edit_post' );
function buddyforms_ajax_edit_post() {
	$post_id   = intval( $_POST['post_id'] );
	$form_slug = get_post_meta( $post_id, '_bf_form_slug', true );

	$args = Array(
		'post_id'   => $post_id,
		'form_slug' => $form_slug
	);
	echo buddyforms_create_edit_form( $args );
	die();

}

add_action( 'wp_ajax_buddyforms_ajax_process_edit_post', 'buddyforms_ajax_process_edit_post' );
add_action( 'wp_ajax_nopriv_buddyforms_ajax_process_edit_post', 'buddyforms_ajax_process_edit_post' );
function buddyforms_ajax_process_edit_post() {
	global $buddyforms;

	if ( isset( $_POST['data'] ) ) {
		parse_str( $_POST['data'], $formdata );
		$_POST = $formdata;
	}
	

	$args = buddyforms_process_submission( $formdata );

	$hasError = false;
	$form_notice = '';
	$form_slug = '';
	$json = '';
	$error_message = __('There was an error please check the form!', 'buddyforms');
	
	extract( $args, EXTR_IF_EXISTS );

	if ( $hasError == true ) {
		
		if ( $form_notice ) {
			Form::setError( 'buddyforms_form_' . $form_slug, $form_notice );
		}

		if ( $error_message ) {
			Form::setError( 'buddyforms_form_' . $form_slug, $error_message );
		}

		Form::renderAjaxErrorResponse( 'buddyforms_form_' . $form_slug );

	} else {

		Form::renderAjaxErrorResponse( 'buddyforms_form_' . $form_slug );

		if ( ! empty( $buddyforms[ $_POST['form_slug'] ]['after_submit_message_text'] ) ) {
			$permalink = get_permalink( $buddyforms[ $args['form_slug'] ]['attached_page'] );

			$display_message = $buddyforms[ $_POST['form_slug'] ]['after_submit_message_text'];
			$display_message = str_ireplace( '[form_singular_name]', $buddyforms[ $args['form_slug'] ]['singular_name'], $display_message );
			$display_message = str_ireplace( '[post_title]', get_the_title( $args['post_id'] ), $display_message );
			$display_message = str_ireplace( '[post_link]', '<a title="Display Post" href="' . get_permalink( $args['post_id'] ) . '"">' . __( 'Display Post', 'buddyforms' ) . '</a>', $display_message );
			$display_message = str_ireplace( '[edit_link]', '<a title="Edit Post" href="' . $permalink . 'edit/' . $args['form_slug'] . '/' . $args['post_id'] . '">' . __( 'Continue Editing', 'buddyforms' ) . '</a>', $display_message );

			$args['form_notice'] = $display_message;
		}

		if ( isset( $buddyforms[ $_POST['form_slug'] ]['after_submit'] ) ) {
			switch ( $buddyforms[ $_POST['form_slug'] ]['after_submit'] ) {
				case 'display_post':
					$json['form_remove'] = 'true';
					$json['form_notice'] = buddyforms_after_save_post_redirect( get_permalink( $args['post_id'] ) );
					break;
				case 'display_page':
					$json['form_remove'] = 'true';
					$json['form_notice'] = apply_filters( 'the_content', get_post_field( 'post_content', $buddyforms[ $_POST['form_slug'] ]['after_submission_page'] ) );
					break;
				case 'redirect':
					$json['form_remove'] = 'true';
					$json['form_notice'] = buddyforms_after_save_post_redirect( $buddyforms[ $_POST['form_slug'] ]['after_submission_url'] );
					break;
				case 'display_posts_list':
					$json['form_remove'] = 'true';
					$permalink           = get_permalink( $buddyforms[ $args['form_slug'] ]['attached_page'] );
					$post_list_link      = $permalink . 'view/' . $args['form_slug'] . '/';
					$json['form_notice'] = buddyforms_after_save_post_redirect( $post_list_link );
					$json['form_notice'] .= $display_message;
					break;
				case 'display_message':
					$json['form_remove'] = 'true';
					$json['form_notice'] = $display_message;
					break;
				default:
					if ( isset( $args['post_id'] ) ) {
						$json['post_id'] = $args['post_id'];
					}
					if ( isset( $args['post_title'] ) ) {
						$json['buddyforms_form_title'] = $args['post_title'];
					}
					if ( isset( $args['revision_id'] ) ) {
						$json['revision_id'] = $args['revision_id'];
					}
					if ( isset( $args['post_parent'] ) ) {
						$json['post_parent'] = $args['post_parent'];
					}
					if ( isset( $args['form_notice'] ) ) {
						$json['form_notice'] = $args['form_notice'];
					}
					break;
			}
		}

	}

	$json = apply_filters( 'buddyforms_ajax_process_edit_post_json_response', $json );

	echo json_encode( $json );

	die();
}

add_action( 'wp_ajax_buddyforms_ajax_delete_post', 'buddyforms_ajax_delete_post' );
//add_action('wp_ajax_nopriv_buddyforms_ajax_delete_post', 'buddyforms_ajax_delete_post');
function buddyforms_ajax_delete_post() {
	global $current_user;
	$current_user = wp_get_current_user();

	$post_id  = intval( $_POST['post_id'] );
	$the_post = get_post( $post_id );

	$form_slug = get_post_meta( $post_id, '_bf_form_slug', true );
	if ( ! $form_slug ) {
		_e( 'You are not allowed to delete this entry! What are you doing here?', 'buddyforms' );
		die();
	}

	// Check if the user is author of the post
	$user_can_delete = false;
	if ( $the_post->post_author == $current_user->ID ) {
		$user_can_delete = true;
	}
	$user_can_delete = apply_filters( 'buddyforms_user_can_delete', $user_can_delete, $form_slug, $post_id );
	if ( $user_can_delete == false ) {
		_e( 'You are not allowed to delete this entry! What are you doing here?', 'buddyforms' );
		die();
	}

	// check if the user has the roles roles and capabilities
	$user_can_delete = false;

	if ( current_user_can( 'buddyforms_' . $form_slug . '_delete' ) ) {
		$user_can_delete = true;
	}
	$user_can_delete = apply_filters( 'buddyforms_user_can_delete', $user_can_delete, $form_slug, $post_id );
	if ( $user_can_delete == false ) {
		_e( 'You do not have the required user role to use this form', 'buddyforms' );
		die();
	}

	do_action( 'buddyforms_delete_post', $post_id );
	wp_delete_post( $post_id );

	echo $post_id;
	die();
}

/**
 * @param $url
 *
 * @return string
 */
function buddyforms_after_save_post_redirect( $url ) {
	$url    = apply_filters( 'buddyforms_after_save_post_redirect', $url );
	$string = __( 'Redirecting..', 'buddyforms' ) . '<script type="text/javascript">';
	$string .= 'window.location = "' . $url . '"';
	$string .= '</script>';

	return $string;
}
