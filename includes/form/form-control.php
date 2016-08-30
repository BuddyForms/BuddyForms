<?php

/**
 * Process the post and Validate all. Saves or update the post and post meta.
 *
 * @package BuddyForms
 * @since 0.3 beta
 */



function buddyforms_process_post( $args = Array() ) {
	global $current_user, $buddyforms;



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

	switch($form_type){
		case 'contact':

			return;
			break;
		case 'registration':
			$registration = buddyforms_add_new_member();

			if(!empty($registration)) {
				$hasError      = true;
				if(is_array($registration)){
					foreach($registration as $error){
						$error_message .= $error . '<br>';
					}
				}
			}

			break;
		default:
			$form_type = 'post';
			break;
	}


	$user_data['ipaddress'] = $_SERVER['REMOTE_ADDR'];
	$user_data['referer']   = $_SERVER['HTTP_REFERER'];

	// Get the browser and platform
	$browser_data = bf_get_browser();

	$user_data['browser']   = $browser_data['name'];
	$user_data['version']   = $browser_data['version'];
	$user_data['platform']  = $browser_data['platform'];
	$user_data['reports']   = $browser_data['reports'];
	$user_data['userAgent'] = $browser_data['userAgent'];




	do_action( 'buddyforms_process_post_start', $args );






	if ( isset( $_POST['bf_post_type'] ) ) {
		$post_type = $_POST['bf_post_type'];
	}

	if ( $post_id != 0 ) {

		if ( ! empty( $revision_id ) ) {
			$the_post = get_post( $revision_id );
		} else {
			$post_id  = apply_filters( 'bf_create_edit_form_post_id', $post_id );
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
			$customfields = bf_update_post_meta( $post_id, $customfields );
		}

		if ( isset( $_POST['featured_image'] ) ) {
			set_post_thumbnail( $post_id, $_POST['featured_image'] );
		} else {
			delete_post_thumbnail( $post_id );
		}

		// Save the Form slug as post meta
		update_post_meta( $post_id, "_bf_form_slug", $form_slug );

		// Save the User Data like browser ip etc
		update_post_meta( $post_id, "_bf_user_data", $user_data );


		if ( isset( $_POST['post_id'] ) && empty( $_POST['post_id'] ) ) {
			$bf_post = array(
				'ID'             => $post_id,
				'post_title'     => apply_filters( 'bf_update_editpost_title', isset( $_POST['editpost_title'] ) && ! empty( $_POST['editpost_title'] ) ? stripslashes( $_POST['editpost_title'] ) : 'none' ),
				'post_content'   => apply_filters( 'bf_update_editpost_content', isset( $_POST['editpost_content'] ) && ! empty( $_POST['editpost_content'] ) ? $_POST['editpost_content'] : '' ),
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
			$info_message .= __( 'The ', 'buddyforms' ) . $buddyforms[ $form_slug ]['singular_name'] . __( ' has been successfully updated ', 'buddyforms' );
			$form_notice = '<div class="info alert">' . $info_message . '</div>';
		} else {
			// Update the new post
			$info_message .= __( 'The ', 'buddyforms' ) . $buddyforms[ $form_slug ]['singular_name'] . __( ' has been successfully created ', 'buddyforms' );
			$form_notice = '<div class="info alert">' . $info_message . '</div>';
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
			'post_title'     => apply_filters( 'bf_update_editpost_title', isset( $_POST['editpost_title'] ) && ! empty( $_POST['editpost_title'] ) ? stripslashes( $_POST['editpost_title'] ) : 'none' ),
			'post_content'   => apply_filters( 'bf_update_editpost_content', isset( $_POST['editpost_content'] ) && ! empty( $_POST['editpost_content'] ) ? $_POST['editpost_content'] : '' ),
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
			'post_title'     => apply_filters( 'bf_update_editpost_title', isset( $_POST['editpost_title'] ) && ! empty( $_POST['editpost_title'] ) ? stripslashes( $_POST['editpost_title'] ) : 'none' ),
			'post_content'   => apply_filters( 'bf_update_editpost_content', isset( $_POST['editpost_content'] ) && ! empty( $_POST['editpost_content'] ) ? $_POST['editpost_content'] : '' ),
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

function bf_update_post_meta( $post_id, $customfields ) {

	if ( ! isset( $customfields ) ) {
		return;
	}

	foreach ( $customfields as $key => $customfield ) :

		if ( $customfield['type'] == 'taxonomy' ) {

			$taxonomy = get_taxonomy( $customfield['taxonomy'] );

			if ( isset( $customfield['multiple'] ) ) {

				if ( isset( $taxonomy->hierarchical ) && $taxonomy->hierarchical == true ) {

					if ( isset( $_POST[ $customfield['slug'] ] ) ) {
						$tax_item = $_POST[ $customfield['slug'] ];

						if ( $tax_item[0] == - 1 && ! empty( $customfield['taxonomy_default'] ) ) {
							//$taxonomy_default = explode(',', $customfield['taxonomy_default'][0]);
							foreach ( $customfield['taxonomy_default'] as $key_tax => $tax ) {
								$tax_item[ $key_tax ] = $tax;
							}
						}

						wp_set_post_terms( $post_id, $tax_item, $customfield['taxonomy'], false );
					}

				} else {

					if ( isset( $_POST[ $customfield['slug'] ] ) ) {
						$slug = Array();

						$postCategories = $_POST[ $customfield['slug'] ];

						foreach ( $postCategories as $postCategory ) {
							$term   = get_term_by( 'id', $postCategory, $customfield['taxonomy'] );
							$slug[] = $term->slug;
						}

						wp_set_post_terms( $post_id, $slug, $customfield['taxonomy'], false );
					}

				}

				if ( isset( $_POST[ $customfield['slug'] . '_creat_new_tax' ] ) && ! empty( $_POST[ $customfield['slug'] . '_creat_new_tax' ] ) ) {
					$creat_new_tax = explode( ',', $_POST[ $customfield['slug'] . '_creat_new_tax' ] );
					if ( is_array( $creat_new_tax ) ) {
						foreach ( $creat_new_tax as $key_tax => $new_tax ) {
							$wp_insert_term = wp_insert_term( $new_tax, $customfield['taxonomy'] );
							wp_set_post_terms( $post_id, $wp_insert_term, $customfield['taxonomy'], true );
						}
					}

				}
			} else {
				wp_delete_object_term_relationships( $post_id, $customfield['taxonomy'] );
				if ( isset( $_POST[ $customfield['slug'] . '_creat_new_tax' ] ) && ! empty( $_POST[ $customfield['slug'] . '_creat_new_tax' ] ) ) {
					$creat_new_tax = explode( ',', $_POST[ $customfield['slug'] . '_creat_new_tax' ] );
					if ( is_array( $creat_new_tax ) ) {
						foreach ( $creat_new_tax as $key_tax => $new_tax ) {
							$wp_insert_term = wp_insert_term( $new_tax, $customfield['taxonomy'] );
							wp_set_post_terms( $post_id, $wp_insert_term, $customfield['taxonomy'], true );
						}
					}

				} else {

					if ( isset( $taxonomy->hierarchical ) && $taxonomy->hierarchical == true ) {

						if ( isset( $_POST[ $customfield['slug'] ] ) ) {
							$tax_item = $_POST[ $customfield['slug'] ];
						}

						if ( $tax_item[0] == - 1 && ! empty( $customfield['taxonomy_default'] ) ) {
							//$taxonomy_default = explode(',', $customfield['taxonomy_default'][0]);
							foreach ( $customfield['taxonomy_default'] as $key_tax => $tax ) {
								$tax_item[ $key_tax ] = $tax;
							}
						}

						wp_set_post_terms( $post_id, $tax_item, $customfield['taxonomy'], false );
					} else {

						$slug = Array();

						if ( isset( $_POST[ $customfield['slug'] ] ) ) {
							$postCategories = $_POST[ $customfield['slug'] ];

							foreach ( $postCategories as $postCategory ) {
								$term   = get_term_by( 'id', $postCategory, $customfield['taxonomy'] );
								$slug[] = $term->slug;
							}
						}

						wp_set_post_terms( $post_id, $slug, $customfield['taxonomy'], false );

					}
				}

			}
		}

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
			//      $customfields[$key]['value'] = $_POST[$slug];
		} else {
			update_post_meta( $post_id, $slug, '' );
			//    $customfields[$key]['value'] = '';
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

function bf_get_browser()
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

// register a new user
function buddyforms_add_new_member() {
	if (isset( $_POST["user_login"] ) && isset( $_POST["user_email"] ) ) {

		$buddyforms_form_nonce_value = $_POST['_wpnonce'];

		if ( ! wp_verify_nonce( $buddyforms_form_nonce_value, 'buddyforms_form_nonce' ) ) {
			return false;
		}

		$user_login		= sanitize_user( $_POST["user_login"] );
		$user_email		= sanitize_email( $_POST["user_email"] );
		$user_first 	= sanitize_text_field( $_POST["user_first"] );
		$user_last	 	= sanitize_text_field( $_POST["user_last"] );
		$user_pass		= esc_attr( $_POST["user_pass"] );
		$pass_confirm 	= esc_attr( $_POST["user_pass_confirm"] );
		$user_url		= isset($_POST["user_website"]) ? esc_url( $_POST["user_website"] ) : '';
		$description    = isset($_POST["user_bio"]) ? esc_textarea( $_POST["user_bio"] ) : '';



		// this is required for username checks
		require_once(ABSPATH . WPINC . '/registration.php');

		if(username_exists($user_login)) {
			// Username already registered
			buddyforms_errors()->add('username_unavailable', __('Username already taken'));
		}
		if(!validate_username($user_login)) {
			// invalid username
			buddyforms_errors()->add('username_invalid', __('Invalid username'));
		}
		if($user_login == '') {
			// empty username
			buddyforms_errors()->add('username_empty', __('Please enter a username'));
		}
		if(!is_email($user_email)) {
			//invalid email
			buddyforms_errors()->add('email_invalid', __('Invalid email'));
		}
		if(email_exists($user_email)) {
			//Email address already registered
			buddyforms_errors()->add('email_used', __('Email already registered'));
		}
		if($user_pass == '') {
			// passwords do not match
			buddyforms_errors()->add('password_empty', __('Please enter a password'));
		}
		if($user_pass != $pass_confirm) {
			// passwords do not match
			buddyforms_errors()->add('password_mismatch', __('Passwords do not match'));
		}

		$errors = buddyforms_errors()->get_error_messages();

		// only create the user in if there are no errors
		if(empty($errors)) {

			$new_user_id = wp_insert_user(array(
					'user_login'		=> $user_login,
					'user_pass'	 		=> $user_pass,
					'user_email'		=> $user_email,
					'first_name'		=> $user_first,
					'last_name'			=> $user_last,
					'user_registered'	=> date('Y-m-d H:i:s'),
					'role'				=> 'subscriber',
					'user_url'			=> $user_url,
					'description'		=> $description
				)
			);
			if($new_user_id) {
				// send an email to the admin alerting them of the registration
				wp_new_user_notification($new_user_id);

			}

		}
		return $errors;
	}
}
add_action('init', 'buddyforms_add_new_member');

// used for tracking error messages
function buddyforms_errors(){
	static $wp_error; // Will hold global variable safely
	return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}