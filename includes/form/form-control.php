<?php

/**
 * Process the post and Validate all. Saves or update the post and post meta.
 *
 * @package BuddyForms
 * @since 0.3 beta
 */

function buddyforms_process_post( $args = Array() ) {
	global $current_user, $buddyforms, $form_slug, $_SERVER;

	$hasError     = false;
	$error_message = '';

	$current_user = wp_get_current_user();

	extract( shortcode_atts( array(
		'post_type'   => '',
		'the_post'    => 0,
		'post_id'     => 0,
		'post_parent' => 0,
		'revision_id' => false,
		'form_slug'   => 0,
		'redirect_to' => $_SERVER['REQUEST_URI'],
	), $args ) );

	$form_type = isset($buddyforms[$form_slug]['form_type']) ? $buddyforms[$form_slug]['form_type'] : '';

	if ( buddyforms_core_fs()->is__premium_only() ) {
		// Get the browser and platform
		$browser_data = buddyforms_get_browser();

		// Collect all submitter data
		$user_data = array();
		if( !isset( $buddyforms[$form_slug]['ipaddress'] ) && isset( $_SERVER['REMOTE_ADDR'] ) ){
			$user_data['ipaddress'] = $_SERVER['REMOTE_ADDR'];
		}
		if( !isset( $buddyforms[$form_slug]['referer'] ) && isset( $_SERVER['REMOHTTP_REFERERTE_ADDR'] ) ){
			$user_data['referer']   = $_SERVER['HTTP_REFERER'];
		}
		if( !isset( $buddyforms[$form_slug]['browser'] ) && isset( $browser_data['name'] ) ){
			$user_data['browser']   = $browser_data['name'];
		}
		if( !isset( $buddyforms[$form_slug]['version'] ) && isset( $browser_data['version'] ) ){
			$user_data['version']   = $browser_data['version'];
		}
		if( !isset( $buddyforms[$form_slug]['platform'] ) && isset( $browser_data['platform'] ) ){
			$user_data['platform']  = $browser_data['platform'];
		}
		if( !isset( $buddyforms[$form_slug]['reports'] ) && isset( $browser_data['reports'] ) ){
			$user_data['reports']   = $browser_data['reports'];
		}
		if( !isset( $buddyforms[$form_slug]['useragent'] ) && isset( $browser_data['useragent'] ) ){
			$user_data['useragent'] = $browser_data['useragent'];
		}
	}



	/* Servers site validation
	 * First we have browser validation. Now let us check from the server site if all is in place
	 * 7 types of validation rules: AlphaNumeric, Captcha, Date, Email, Numeric, RegExp, Required, and Url
	 *
	 * Validation can be extended
	 */
	if( Form::isValid( "buddyforms_form_" . $form_slug, false ) ) {
		if(!apply_filters( 'buddyforms_form_custom_validation', true, $form_slug )) {
			$args = array(
				'hasError'  => true,
				'form_slug' => $form_slug,
			);
			Form::clearValues( "buddyforms_form_" . $form_slug );
			return $args;
		}
	} else {
		$args = array(
			'hasError'  => true,
			'form_slug' => $form_slug,
		);
		Form::clearValues( "buddyforms_form_" . $form_slug );
		return $args;
	}

	// Check if this is a registration form only
	if( $form_type == 'registration' ) {

		$registration = buddyforms_wp_insert_user();
		// Check if registration was successful
		if( !$registration ){
			$args = array(
				'hasError'      => true,
				'form_slug'    => $form_slug,
			);
			return $args;
		}
		if ( buddyforms_core_fs()->is__premium_only() ) {
			// Save the Browser user data
			add_user_meta( $registration, 'buddyforms_browser_user_data', $user_data, true );
		}
		$args = array(
			'hasError'     => $hasError,
			'form_notice'  => $form_notice,
			'customfields' => $customfields,
			'redirect_to'  => $redirect_to,
			'form_slug'    => $form_slug,
		);
		Form::clearValues( "buddyforms_form_" . $form_slug );
		return $args;
	}

	// Check if user is logged in and if not check if registration during submission is enabled.
	if( isset( $buddyforms[$form_slug]['public_submit_create_account'] ) && !is_user_logged_in() ){

		// ok let us try to register a user
		$registration = buddyforms_wp_insert_user();

		// Check if registration was successful
		if( !$registration ){
			$args = array(
				'hasError'      => true,
				'form_slug'    => $form_slug,
			);
			Form::clearValues( "buddyforms_form_" . $form_slug );
			return $args;
		}
		if ( buddyforms_core_fs()->is__premium_only() ) {
			// Save the Browser user data
			add_user_meta( $registration, 'buddyforms_browser_user_data', $user_data, true );
		}
	}

	// Ok let us start processing the post form
	do_action( 'buddyforms_process_post_start', $args );

	if ( isset( $_POST['bf_post_type'] ) ) {
		$post_type = $_POST['bf_post_type'];
	}

	if ( $post_id != 0 ) {

		if ( ! empty( $revision_id ) ) {
			$the_post = get_post( $revision_id );
		} else {
			$post_id  = apply_filters( 'buddyforms_create_edit_form_post_id', $post_id );
			$the_post = get_post( $post_id );
		}

		// Check if the user is author of the post
		$user_can_edit = false;
		if ( $the_post->post_author == $current_user->ID ) {
			$user_can_edit = true;
		}
		$user_can_edit = apply_filters( 'buddyforms_user_can_edit', $user_can_edit );
		if ( $user_can_edit == false ) {
			$args = array(
				'hasError'      => true,
				'error_message' => __( 'You are not allowed to edit this post. What are you doing here?', 'buddyforms' ),
			);

			return $args;
		}

	}

	// check if the user has the roles and capabilities
	$user_can_edit = false;
	if ( $post_id == 0 && current_user_can( 'buddyforms_' . $form_slug . '_create' ) ) {
		$user_can_edit = true;
	} elseif ( $post_id != 0 && current_user_can( 'buddyforms_' . $form_slug . '_edit' ) ) {
		$user_can_edit = true;
	}
	if( isset($buddyforms[$form_slug]['public_submit']) && $buddyforms[$form_slug]['public_submit'][0] == 'public_submit' ){
		$user_can_edit = true;
	}
	$user_can_edit = apply_filters( 'buddyforms_user_can_edit', $user_can_edit );
	if ( $user_can_edit == false ) {
		$args = array(
			'hasError'      => true,
			'error_message' => __( 'You do not have the required user role to use this form', 'buddyforms' ),
		);

		return $args;
	}

	// If post_id == 0 a new post is created
	if ( $post_id == 0 ) {
		require_once( ABSPATH . 'wp-admin/includes/admin.php' );
		$the_post = get_default_post_to_edit( $post_type );
	}

	if ( isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {
		$customfields = $buddyforms[ $form_slug ]['form_fields'];
	}

	$comment_status = $buddyforms[ $form_slug ]['comment_status'];
	if ( isset( $_POST['comment_status'] ) ) {
		$comment_status = $_POST['comment_status'];
	}

	$post_excerpt = '';
	if ( isset( $_POST['post_excerpt'] ) ) {
		$post_excerpt = $_POST['post_excerpt'];
	}

	$action      = 'save';
	$post_status = $buddyforms[ $form_slug ]['status'];
	if ( $post_id != 0 ) {
		$action      = 'update';
		$post_status = get_post_status( $post_id );
	}
	if ( isset( $_POST['status'] ) ) {
		$post_status = $_POST['status'];
	}

	$args = Array(
		'post_id'        => $post_id,
		'action'         => $action,
		'form_slug'      => $form_slug,
		'post_type'      => $post_type,
		'post_excerpt'   => $post_excerpt,
		'post_author'    => $current_user->ID,
		'post_status'    => $post_status,
		'post_parent'    => $post_parent,
		'comment_status' => $comment_status,
	);

	extract( $args = buddyforms_update_post( $args ) );

	/*
	 * Check if the update or insert was successful
	 */
	if ( ! is_wp_error( $post_id ) ) {

		// Check if the post has post meta / custom fields
		if ( isset( $customfields ) ) {
			$customfields = buddyforms_update_post_meta( $post_id, $customfields );
		}

		if ( isset( $_POST['featured_image'] ) ) {
			set_post_thumbnail( $post_id, $_POST['featured_image'] );
		} else {
			delete_post_thumbnail( $post_id );
		}

		// Save the Form slug as post meta
		update_post_meta( $post_id, "_bf_form_slug", $form_slug );

		if ( buddyforms_core_fs()->is__premium_only() ) {
			// Save the User Data like browser ip etc
			update_post_meta( $post_id, "_bf_user_data", $user_data );
		}

		if ( isset( $_POST['post_id'] ) && empty( $_POST['post_id'] ) ) {
			$bf_post = array(
				'ID'             => $post_id,
				'post_title'     => apply_filters( 'bf_update_buddyforms_form_title', isset( $_POST['buddyforms_form_title'] ) && ! empty( $_POST['buddyforms_form_title'] ) ? stripslashes( $_POST['buddyforms_form_title'] ) : 'none' ),
				'post_content'   => apply_filters( 'bf_update_buddyforms_form_content', isset( $_POST['buddyforms_form_content'] ) && ! empty( $_POST['buddyforms_form_content'] ) ? $_POST['buddyforms_form_content'] : '' ),
				'post_type'      => $post_type,
				'post_status'    => $post_status,
				'comment_status' => $comment_status,
				'post_excerpt'   => $post_excerpt,
				'post_parent'    => $post_parent,
			);

			// Update the new post
			$post_id = wp_update_post( $bf_post, true );

		}
	} else {
		$hasError      = true;
		$error_message = $post_id->get_error_message();
	}

	// Display the message
	if ( ! $hasError ) :
		if ( isset( $_POST['post_id'] ) && ! empty( $_POST['post_id'] ) ) {
			$info_message   = __( 'The ', 'buddyforms' ) . $buddyforms[ $form_slug ]['singular_name'] . __( ' has been successfully updated ', 'buddyforms' );
			$form_notice    = '<div class="info alert">' . $info_message . '</div>';
		} else {
			// Update the new post
			$info_message   = __( 'The ', 'buddyforms' ) . $buddyforms[ $form_slug ]['singular_name'] . __( ' has been successfully created ', 'buddyforms' );
			$form_notice    = '<div class="info alert">' . $info_message . '</div>';
		}

	else:
		if ( empty( $error_message ) ) {
			$error_message = __( 'Error! There was a problem submitting the post ;-(', 'buddyforms' );
		}
		$form_notice = '<div class="error alert">' . $error_message . '</div>';

		if ( ! empty( $fileError ) ) {
			$form_notice = '<div class="error alert">' . $fileError . '</div>';
		}

	endif;

	do_action( 'buddyforms_after_save_post', $post_id );

	$args2 = array(
		'hasError'     => $hasError,
		'form_notice'  => $form_notice,
		'customfields' => $customfields,
		//'post_id'		=> $post_id,
		//'revision_id' 	=> $revision_id,
		//'post_parent'   => $post_parent,
		'redirect_to'  => $redirect_to,
		'form_slug'    => $form_slug,
	);

	$args = array_merge( $args, $args2 );

	do_action( 'buddyforms_process_post_end', $args );
	Form::clearValues( "buddyforms_form_" . $form_slug );
	return $args;

}

function buddyforms_update_post( $args ) {

	extract( $args = apply_filters( 'buddyforms_update_post_args', $args ) );

	$buddyforms_form_nonce_value = $_POST['_wpnonce'];

	if ( ! wp_verify_nonce( $buddyforms_form_nonce_value, 'buddyforms_form_nonce' ) ) {
		return false;
	}

	// Check if post is new or edit
	if ( $action == 'update' ) {

		$bf_post = array(
			'ID'             => $_POST['post_id'],
			'post_title'     => apply_filters( 'bf_update_buddyforms_form_title', isset( $_POST['buddyforms_form_title'] ) && ! empty( $_POST['buddyforms_form_title'] ) ? stripslashes( $_POST['buddyforms_form_title'] ) : 'none' ),
			'post_content'   => apply_filters( 'bf_update_buddyforms_form_content', isset( $_POST['buddyforms_form_content'] ) && ! empty( $_POST['buddyforms_form_content'] ) ? $_POST['buddyforms_form_content'] : '' ),
			'post_type'      => $post_type,
			'post_status'    => $post_status,
			'comment_status' => $comment_status,
			'post_excerpt'   => $post_excerpt,
			'post_parent'    => $post_parent,
		);

		// Update the new post
		$post_id = wp_update_post( $bf_post, true );

	} else {

		$bf_post = array(
			'post_parent'    => $post_parent,
			'post_author'    => $post_author,
			'post_title'     => apply_filters( 'bf_update_buddyforms_form_title', isset( $_POST['buddyforms_form_title'] ) && ! empty( $_POST['buddyforms_form_title'] ) ? stripslashes( $_POST['buddyforms_form_title'] ) : 'none' ),
			'post_content'   => apply_filters( 'bf_update_buddyforms_form_content', isset( $_POST['buddyforms_form_content'] ) && ! empty( $_POST['buddyforms_form_content'] ) ? $_POST['buddyforms_form_content'] : '' ),
			'post_type'      => $post_type,
			'post_status'    => $post_status,
			'comment_status' => $comment_status,
			'post_excerpt'   => $post_excerpt,
		);

		// Add optional scheduled post dates
		if ( isset( $_POST['status'] ) && $_POST['status'] == 'future' && $_POST['schedule'] ) {
			$post_date = date( 'Y-m-d H:i:s', strtotime( $_POST['schedule'] ) );
			$bf_post['post_date'] = $post_date;
			$bf_post['post_date_gmt'] = $post_date;
		}

		// Insert the new form
		$post_id = wp_insert_post( $bf_post, true );

	}
	$bf_post['post_id'] = $post_id;

	return $bf_post;
}

function buddyforms_update_post_meta( $post_id, $customfields ) {
	global $buddyforms, $form_slug;

	if ( ! isset( $customfields ) ) {
		return;
	}

	foreach ( $customfields as $key => $customfield ) :

		// Check if file is new and needs to get reassigned to the corect parent
		if( $customfield['type'] == 'file' && !empty( $_POST[$customfield['slug']] ) ){

			$attachement_ids = $_POST[$customfield['slug']];
			$attachement_ids = explode( ',', $attachement_ids );

			if ( is_array( $attachement_ids ) ) {
				foreach ( $attachement_ids as $attachement_id ) {

					$attachement = get_post( $attachement_id );

					if($attachement->post_parent == $buddyforms[$form_slug]['attached_page'] ){
						$attachement = array(
							'ID' => $attachement_id,
							'post_parent' => $post_id,
						);
						wp_update_post( $attachement );
					}
				}
			}
		}

		// Check if featured image is new and needs to get reassigned to the corect parent
		if ( $customfield['type'] == 'featured-image' || $customfield['type'] == 'featured_image' && isset($_POST['featured_image'])) {

			$attachement_id = $_POST['featured_image'];

			$attachement = get_post( $attachement_id );

			if($attachement->post_parent == $buddyforms[$form_slug]['attached_page'] ){
				$attachement = array(
					'ID' => $attachement_id,
					'post_parent' => $post_id,
				);
				wp_update_post( $attachement );
			}

		}

		// Save taxonomies if needed
		//
		//
		//						$wp_insert_term = wp_insert_term( $new_tax, $customfield['taxonomy'] );
		//						wp_set_post_terms( $post_id, $wp_insert_term, $customfield['taxonomy'], true );

		if ( $customfield['type'] == 'taxonomy' ) :

			if ( isset( $_POST[ $customfield['slug'] ] ) ) {

				$taxonomy = get_taxonomy( $customfield['taxonomy'] );

				// Check if multiple selection is allowed and delete all object relationships.
				if ( isset( $customfield['multiple'] ) ) {
				//	wp_delete_object_term_relationships( $post_id, $customfield['taxonomy'] );
				}

				// Check if the taxonomy is hierarchical
				if ( isset( $taxonomy->hierarchical ) && $taxonomy->hierarchical == true ) {

					// Get the tax items
					$tax_item = $_POST[$customfield['slug']];

					// If no tax items are available check if we have some defaults we can use
					if ( $tax_item[0] == - 1 && !empty( $customfield['taxonomy_default'] ) ) {
						foreach ( $customfield['taxonomy_default'] as $key_tax => $tax ) {
							$tax_item[$key_tax] = $tax;
						}
					}

					// Check if new term to insert
					foreach($tax_item as $term_key => $term){
						$term_exist = term_exists( $term, $customfield['taxonomy'] );

						if( !$term_exist ){
							$new_term = wp_insert_term( $term, $customfield['taxonomy'] );
							$tax_item[$term_key] = (string)$new_term['term_id'];
						}
					}

					// Now let us set the post terms
					wp_set_post_terms( $post_id, $tax_item, $customfield['taxonomy'], false );

				// If hierarchical is false only single selction is allowed
				} else {

					$slug = Array();

					$postCategories = $_POST[$customfield['slug']];

					foreach ( $postCategories as $postCategory ) {
						$term = get_term_by( 'id', $postCategory, $customfield['taxonomy'] );
						$slug[] = $term->slug;
					}
					wp_set_post_terms( $post_id, $slug, $customfield['taxonomy'], false );
				}
			}
		endif;

		// Update meta do_action to hook into. This can be needed if you added
		// new form elements and need to manipulate how they get saved.
		do_action( 'buddyforms_update_post_meta', $customfield, $post_id );

		if ( isset( $customfield['slug'] ) ) {
			$slug = $customfield['slug'];
		}

		if ( empty( $slug ) ) {
			$slug = sanitize_title( $customfield['name'] );
		}


		// Update the post
		if ( isset( $_POST[ $slug ] ) ) {
			update_post_meta( $post_id, $slug, $_POST[ $slug ] );
		} else {
			update_post_meta( $post_id, $slug, '' );
		}

	endforeach;

	return $customfields;
}

add_filter( 'wp_handle_upload_prefilter', 'buddyforms_wp_handle_upload_prefilter' );
function buddyforms_wp_handle_upload_prefilter( $file ) {
	if ( isset( $_POST['allowed_type'] ) && ! empty( $_POST['allowed_type'] ) ) {
		//this allows you to set multiple types seperated by a pipe "|"
		$allowed = explode( ",", $_POST['allowed_type'] );
		$ext     = $file['type'];

		//first check if the user uploaded the right type
		if ( ! in_array( $ext, (array) $allowed ) ) {
			$file['error'] = $file['type'] . __( "Sorry, you cannot upload this file type for this field." );

			return $file;
		}

		//check if the type is allowed at all by WordPress
		foreach ( get_allowed_mime_types() as $key => $value ) {
			if ( $value == $ext ) {
				return $file;
			}
		}
		$file['error'] = __( "Sorry, you cannot upload this file type for this field." );
	}

	return $file;
}

function buddyforms_get_browser()
{
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$bname = 'Unknown';
	$platform = 'Unknown';
	$version= "";

	//First get the platform?
	if (preg_match('/linux/i', $u_agent)) {
		$platform = 'linux';
	}
	elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
		$platform = 'mac';
	}
	elseif (preg_match('/windows|win32/i', $u_agent)) {
		$platform = 'windows';
	}

	// Next get the name of the useragent yes seperately and for good reason
	if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
	{
		$bname = 'Internet Explorer';
		$ub = "MSIE";
	}
	elseif(preg_match('/Firefox/i',$u_agent))
	{
		$bname = 'Mozilla Firefox';
		$ub = "Firefox";
	}
	elseif(preg_match('/Chrome/i',$u_agent))
	{
		$bname = 'Google Chrome';
		$ub = "Chrome";
	}
	elseif(preg_match('/Safari/i',$u_agent))
	{
		$bname = 'Apple Safari';
		$ub = "Safari";
	}
	elseif(preg_match('/Opera/i',$u_agent))
	{
		$bname = 'Opera';
		$ub = "Opera";
	}
	elseif(preg_match('/Netscape/i',$u_agent))
	{
		$bname = 'Netscape';
		$ub = "Netscape";
	}

	// finally get the correct version number
	$known = array('Version', $ub, 'other');
	$pattern = '#(?<browser>' . join('|', $known) .
	           ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	if (!preg_match_all($pattern, $u_agent, $matches)) {
		// we have no matching number just continue
	}

	// see how many we have
	$i = count($matches['browser']);
	if ($i != 1) {
		//we will have two since we are not using 'other' argument yet
		//see if version is before or after the name
		if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
			$version= $matches['version'][0];
		}
		else {
			$version= $matches['version'][1];
		}
	}
	else {
		$version= $matches['version'][0];
	}

	// check if we have a number
	if ($version==null || $version=="") {$version="?";}

	return array(
		'userAgent' => $u_agent,
		'name'      => $bname,
		'version'   => $version,
		'platform'  => $platform,
		'pattern'    => $pattern
	);
}
