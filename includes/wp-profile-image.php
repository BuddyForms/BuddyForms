<?php

add_action( 'buddyforms_after_activate_user', 'buddyforms_after_activate_user', 10, 1 );

/*
 * after the user activation get the image from the user meta and set it as profile
 * image of the activated user.
 *
 */
function buddyforms_after_activate_user( $user_id ) {
	global $bp;
	$original_file = get_user_meta( $user_id, 'profile_image', true );

	$crop_w = get_user_meta( $user_id, 'crop_w', true );
	$crop_h = get_user_meta( $user_id, 'crop_h', true );
	$crop_x = get_user_meta( $user_id, 'crop_x', true );
	$crop_y = get_user_meta( $user_id, 'crop_y', true );

	$bp->displayed_user->id       = $user_id;
	$bp->loggedin_user->id        = $user_id;
	$bp->displayed_user->domain   = bp_core_get_user_domain( $bp->displayed_user->id );
	$bp->displayed_user->userdata = bp_core_get_core_userdata( $bp->displayed_user->id );
	$bp->displayed_user->fullname = bp_core_get_user_displayname( $bp->displayed_user->id );
	$r                            = array(
		'item_id'       => $user_id,
		'object'        => 'user',
		'avatar_dir'    => 'avatars',
		'original_file' => $original_file,
		'crop_w'        => $crop_w,
		'crop_h'        => $crop_h,
		'crop_x'        => $crop_x,
		'crop_y'        => $crop_y
	);
	if (  buddyforms_crop_profile_picture_registration( $r ) ) {
		$return = array(
			'avatar'        => html_entity_decode( bp_core_fetch_avatar( array(
				'object'  => 'user',
				'item_id' => $user_id,
				'html'    => false,
				'type'    => 'full',
			) ) ),
			'feedback_code' => 2,
			'item_id'       => $user_id,
		);

		 do_action( 'xprofile_avatar_uploaded', (int)$user_id, 'avatar', $r );

		// wp_send_json_success( $return );
	}
}


function  buddyforms_crop_profile_picture( $args = array() ) {

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
	$upload_path   = bp_core_avatar_upload_path();
	$url           = bp_core_avatar_url();
	$absolute_path = $upload_path . $relative_path;

	// Bail if the avatar is not available.
	if ( ! file_exists( $absolute_path ) ) {

		$create_new_folder = $upload_path . "/avatars/" . $args['item_id'];
		mkdir( $create_new_folder, 0777, true );
		$picture_name    = explode( "/", $absolute_path );
		$size            = count( $picture_name );
		$profile_pitcure = $picture_name[ $size - 1 ];
		$origen          = $upload_path . '/avatars/0/' . $profile_pitcure;
		$existe          = file_exists( $origen );
		rename( $origen, $create_new_folder . '/' . $profile_pitcure );
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
	} else {


	}


	// Delete the existing avatar files for the object.
	$existing_avatar = bp_core_fetch_avatar( array(
		'object'  => $args['object'],
		'item_id' => $args['item_id'],
		'html'    => false,
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
	$avatar_types          = array( 'full' => '', 'thumb' => '' );

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
	//  @unlink( $absolute_path );

	// Return the full and thumb cropped avatars.
	return $avatar_types;
}

function  buddyforms_crop_profile_picture_registration( $args = array() ) {
	$cropped =  buddyforms_crop_profile_picture( $args );

	// Check for errors.
	if ( is_wp_error( $cropped['full'] ) || is_wp_error( $cropped['thumb'] ) ) {
		return false;
	}

	return true;
}