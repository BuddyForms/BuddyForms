<?php

/*
 * Get the post type taxonomies to load the new created form element select
 *
 *
 */
add_action('wp_ajax_nopriv_handle_dropped_media', 'BMP_handle_dropped_media');
add_action( 'wp_ajax_handle_dropped_media', 'BMP_handle_dropped_media' );
add_action( 'wp_ajax_nopriv_bp_avatar_upload', 'bf_avatar_ajax_upload' );
add_action( 'buddyforms_process_submission_end','profile_picture_user_registration_ended' ,10,1 );



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



            wp_send_json_success( $return );
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
function bf_avatar_ajax_upload() {
    // Bail if not a POST action.
    if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
        wp_die();
    }

    /**
     * Sending the json response will be different if
     * the current Plupload runtime is html4.
     */
    $is_html4 = false;
    if ( ! empty( $_POST['html4' ] ) ) {
        $is_html4 = true;
    }

    // Check the nonce.
    check_admin_referer( 'bp-uploader' );

    // Init the BuddyPress parameters.
    $bp_params = array();

    // We need it to carry on.
    if ( ! empty( $_POST['bp_params' ] ) ) {
        $bp_params = $_POST['bp_params' ];
    } else {
        bp_attachments_json_response( false, $is_html4 );
    }

    // We need the object to set the uploads dir filter.
    if ( empty( $bp_params['object'] ) ) {
        bp_attachments_json_response( false, $is_html4 );
    }

    // Capability check.
    /* if ( ! bp_attachments_current_user_can( 'edit_avatar', $bp_params ) ) {
         bp_attachments_json_response( false, $is_html4 );
     }*/

    $bp = buddypress();
    $bp_params['upload_dir_filter'] = '';
    $needs_reset = array();

    if ( 'user' === $bp_params['object'] && bp_is_active( 'xprofile' ) ) {
        $bp_params['upload_dir_filter'] = 'xprofile_avatar_upload_dir';

        if ( ! bp_displayed_user_id() && ! empty( $bp_params['item_id'] ) ) {
            $needs_reset = array( 'key' => 'displayed_user', 'value' => $bp->displayed_user );
            $bp->displayed_user->id = $bp_params['item_id'];
        }
    } elseif ( 'group' === $bp_params['object'] && bp_is_active( 'groups' ) ) {
        $bp_params['upload_dir_filter'] = 'groups_avatar_upload_dir';

        if ( ! bp_get_current_group_id() && ! empty( $bp_params['item_id'] ) ) {
            $needs_reset = array( 'component' => 'groups', 'key' => 'current_group', 'value' => $bp->groups->current_group );
            $bp->groups->current_group = groups_get_group( $bp_params['item_id'] );
        }
    } else {
        /**
         * Filter here to deal with other components.
         *
         * @since 2.3.0
         *
         * @var array $bp_params the BuddyPress Ajax parameters.
         */
        $bp_params = apply_filters( 'bp_core_avatar_ajax_upload_params', $bp_params );
    }

    if ( ! isset( $bp->avatar_admin ) ) {
        $bp->avatar_admin = new stdClass();
    }

    /**
     * The BuddyPress upload parameters is including the Avatar UI Available width,
     * add it to the avatar_admin global for a later use.
     */
    if ( isset( $bp_params['ui_available_width'] ) ) {
        $bp->avatar_admin->ui_available_width =  (int) $bp_params['ui_available_width'];
    }

    // Upload the avatar.
    $avatar = bp_core_avatar_handle_upload( $_FILES, $bp_params['upload_dir_filter'] );

    // Reset objects.
    if ( ! empty( $needs_reset ) ) {
        if ( ! empty( $needs_reset['component'] ) ) {
            $bp->{$needs_reset['component']}->{$needs_reset['key']} = $needs_reset['value'];
        } else {
            $bp->{$needs_reset['key']} = $needs_reset['value'];
        }
    }

    // Init the feedback message.
    $feedback_message = false;

    if ( ! empty( $bp->template_message ) ) {
        $feedback_message = $bp->template_message;

        // Remove template message.
        $bp->template_message      = false;
        $bp->template_message_type = false;

        @setcookie( 'bp-message', false, time() - 1000, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
        @setcookie( 'bp-message-type', false, time() - 1000, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
    }

    if ( empty( $avatar ) ) {
        // Default upload error.
        $message = __( 'Upload failed.', 'buddypress' );

        // Use the template message if set.
        if ( ! empty( $feedback_message ) ) {
            $message = $feedback_message;
        }

        // Upload error reply.
        bp_attachments_json_response( false, $is_html4, array(
            'type'    => 'upload_error',
            'message' => $message,
        ) );
    }

    if ( empty( $bp->avatar_admin->image->file ) ) {
        bp_attachments_json_response( false, $is_html4 );
    }

    $uploaded_image = @getimagesize( $bp->avatar_admin->image->file );

    // Set the name of the file.
    $name = $_FILES['file']['name'];
    $name_parts = pathinfo( $name );
    $name = trim( substr( $name, 0, - ( 1 + strlen( $name_parts['extension'] ) ) ) );

    // Finally return the avatar to the editor.
    bp_attachments_json_response( true, $is_html4, array(
        'name'      => $name,
        'url'       => $bp->avatar_admin->image->url,
        'width'     => $uploaded_image[0],
        'height'    => $uploaded_image[1],
        'feedback'  => $feedback_message,
    ) );
}



function BMP_handle_dropped_media() {
	check_ajax_referer( 'fac_drop', 'nonce' );
	status_header( 200 );
	$upload_dir  = wp_upload_dir();
	$upload_path = $upload_dir['path'] . DIRECTORY_SEPARATOR;
	$num_files   = count( $_FILES['file']['tmp_name'] );
	$newupload = 0;
	if ( ! empty( $_FILES ) ) {
		$files = $_FILES;
		foreach ( $files as $file_id => $file ) {
			$newupload = media_handle_upload( $file_id, 0 );
		}
	}
	
	echo $newupload;
	die();
}

add_action( 'wp_ajax_nopriv_handle_deleted_media', 'BMP_handle_delete_media' );
add_action( 'wp_ajax_handle_deleted_media', 'BMP_handle_delete_media' );

function BMP_handle_delete_media() {
	check_ajax_referer( 'fac_drop', 'nonce' );
	if ( isset( $_REQUEST['media_id'] ) ) {
		$post_id = absint( $_REQUEST['media_id'] );
		
		$status = wp_delete_attachment( $post_id, true );
		
		if ( $status ) {
			echo wp_json_encode( array( 'status' => 'OK' ) );
		} else {
			echo wp_json_encode( array( 'status' => 'FAILED' ) );
		}
	}
	
	die();
}
add_action( 'wp_ajax_buddyforms_post_types_taxonomies', 'buddyforms_post_types_taxonomies' );
function buddyforms_post_types_taxonomies() {

	if ( ! isset( $_POST['post_type'] ) ) {
		echo 'false';
		die();
	}

	$post_type             = $_POST['post_type'];
	$buddyforms_taxonomies = buddyforms_taxonomies( $post_type );

	$tmp = '';
	foreach ( $buddyforms_taxonomies as $name => $label ) {
		$tmp .= '<option value="' . $name . '">' . $label . '</option>';
	}

	echo $tmp;
	die();

}

add_action( 'wp_ajax_buddyforms_update_taxonomy_default', 'buddyforms_update_taxonomy_default' );
function buddyforms_update_taxonomy_default() {

	if ( ! isset( $_POST['taxonomy'] ) || $_POST['taxonomy'] == 'none' ) {
		$tmp = '<option value="none">First you need to select a Taxonomy to select the Taxonomy defaults</option>';
		echo $tmp;
		die();
	}

	$taxonomy = $_POST['taxonomy'];

	$args = array(
		'orderby'    => 'name',
		'order'      => 'ASC',
		'hide_empty' => false,
		'fields'     => 'id=>name',
	);

	$terms = get_terms( $taxonomy, $args );

	$tmp = '<option value="none">none</option>';
	foreach ( $terms as $key => $term_name ) {
		$tmp .= '<option value="' . $key . '">' . $term_name . '</option>';
	}

	echo $tmp;

	die();

}

add_action( 'wp_ajax_buddyforms_new_page', 'buddyforms_new_page' );
function buddyforms_new_page() {

	if ( ! is_admin() ) {
		return;
	}

	// Check if a title is entered
	if ( empty( $_POST['page_name'] ) ) {
		$json['error'] = 'Please enter a name';
		echo json_encode( $json );
		die();
	}

	// Create post object
	$new_page = array(
		'post_title'   => wp_strip_all_tags( $_POST['page_name'] ),
		'post_content' => '',
		'post_status'  => 'publish',
		'post_type'    => 'page'
	);

	// Insert the post into the database
	$new_page = wp_insert_post( $new_page );

	// Check if page creation worked successfully
	if ( is_wp_error( $new_page ) ) {
		$json['error'] = $new_page;
	} else {
		$json['id']   = $new_page;
		$json['name'] = wp_strip_all_tags( $_POST['page_name'] );
	}

	echo json_encode( $json );
	die();

}

add_action( 'wp_ajax_buddyforms_url_builder', 'buddyforms_url_builder' );
function buddyforms_url_builder() {
	global $post;
	$page_id   = $_POST['attached_page'];
	$form_slug = $_POST['form_slug'];
	$post      = get_post( $page_id );

	if ( isset( $post->post_name ) ) {
		$json['permalink'] = get_permalink( $page_id );
		$json['form_slug'] = $form_slug;
		echo json_encode( $json );
		die();
	}
	echo json_encode( 'none' );
	die();


}
