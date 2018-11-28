<?php

/**
 * Process the form submission. Validate all. Saves or update the post and post meta. Sent aut notifications if needed
 *
 * @package BuddyForms
 * @since   0.3 beta
 *
 * @param array $args
 *
 * @return array
 */
function buddyforms_process_submission( $args = Array() ) {
	global $current_user, $buddyforms, $form_slug, $_SERVER;

	$hasError      = false;
	$error_message = '';

	$post_type   = '';
	$the_post    = '';
	$revision_id = '';
	$redirect_to = '';
	$post_id     = 0;
	$post_parent = 0;
	$bf_hweb     = '';

	$current_user = wp_get_current_user();
	$user_id      = $current_user->ID;

	extract( shortcode_atts( array(
		'post_type'   => '',
		'the_post'    => 0,
		'post_id'     => 0,
		'post_parent' => 0,
		'revision_id' => false,
		'form_slug'   => 0,
		'redirect_to' => $_SERVER['REQUEST_URI'],
		'bf_hweb'     => '',
	), $args ) );

	// Check if multisite is enabled and switch to the form blog id
	buddyforms_switch_to_form_blog( $form_slug );

	$form_type = isset( $buddyforms[ $form_slug ]['form_type'] ) ? $buddyforms[ $form_slug ]['form_type'] : '';

	$user_data = array();
	if ( buddyforms_core_fs()->is__premium_only() && isset( $buddyforms[ $form_slug ]['user_data'] ) ) {
		// Get the browser and platform
		$browser_data = buddyforms_get_browser();

		// Collect all submitter data
		if ( ! in_array( 'ipaddress', $buddyforms[ $form_slug ]['user_data'], true ) && isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$user_data['ipaddress'] = $_SERVER['REMOTE_ADDR'];
		}
		if ( ! in_array( 'referer', $buddyforms[ $form_slug ]['user_data'], true ) && isset( $_SERVER['REMOHTTP_REFERERTE_ADDR'] ) ) {
			$user_data['referer'] = $_SERVER['HTTP_REFERER'];
		}
		if ( ! in_array( 'browser', $buddyforms[ $form_slug ]['user_data'], true ) && isset( $browser_data['name'] ) ) {
			$user_data['browser'] = $browser_data['name'];
		}
		if ( ! in_array( 'version', $buddyforms[ $form_slug ]['user_data'], true ) && isset( $browser_data['version'] ) ) {
			$user_data['version'] = $browser_data['version'];
		}
		if ( ! in_array( 'platform', $buddyforms[ $form_slug ]['user_data'], true ) && isset( $browser_data['platform'] ) ) {
			$user_data['platform'] = $browser_data['platform'];
		}
		if ( ! in_array( 'reports', $buddyforms[ $form_slug ]['user_data'], true ) && isset( $browser_data['reports'] ) ) {
			$user_data['reports'] = $browser_data['reports'];
		}
		if ( ! in_array( 'userAgent', $buddyforms[ $form_slug ]['user_data'], true ) && isset( $browser_data['userAgent'] ) ) {
			$user_data['userAgent'] = $browser_data['userAgent'];
		}
	}

	// Check HoneyPot
	$bf_honeypot = $_POST['bf_hweb'];
	if ( ! empty( $bf_honeypot ) ) {
		return array(
			'hasError'      => true,
			'form_slug'     => $form_slug,
			'error_message' => __( 'SPAM Detected!', 'buddyform' ),
		);
	}

	/* Servers site validation
	 * First we have browser validation. Now let us check from the server site if all is in place
	 * 7 types of validation rules: AlphaNumeric, Captcha, Date, Email, Numeric, RegExp, Required, and Url
	 *
	 * Validation can be extended
	 */
	if ( Form::isValid( "buddyforms_form_" . $form_slug, false ) ) {
		if ( ! apply_filters( 'buddyforms_form_custom_validation', true, $form_slug ) ) {
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
	if ( $form_type == 'registration' ) {

		if ( ! is_user_logged_in() ) {
			$user_id = buddyforms_wp_insert_user();
		} else {
			$user_id = buddyforms_wp_update_user();
		}

		// Check if registration or update was successful
		if ( is_wp_error( $user_id ) || ! $user_id ) {

			$error_message = '';
			if ( is_wp_error( $user_id ) ) {
				$error_message = $user_id->get_error_message();
			}

			$args = array(
				'hasError'      => true,
				'form_slug'     => $form_slug,
				'error_message' => $error_message
			);

			return $args;
		}

		if ( buddyforms_core_fs()->is__premium_only() && ! empty( $user_data ) ) {
			/**
			 * Avoid save user meta data.
			 *
			 * This hook prevent buddyforms plugin to save user meta. Is important to use it if you like to save the user meta with your own plugin.
			 *
			 * @since 2.1.7
			 *
			 * @param boolean $grant This parameter determine if the data will be saved by buddyforms functions.
			 * @param string $type The type of information for the next parameter. Possible values is 'browser data' and 'field'.
			 * @param array $data This parameter holds the information what wil be saved.
			 */
			$save_usermeta = apply_filters( 'buddyforms_not_save_usermeta', true, 'browser_data', $user_data );
			if ( $save_usermeta ) {
				// Save the Browser user data
				add_user_meta( $user_id, 'buddyforms_browser_user_data', $user_data, true );
			}
		}

		if ( isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {
			foreach ( $buddyforms[ $form_slug ]['form_fields'] as $field_key => $r_field ) {
				/**
				 * Avoid save user meta data.
				 *
				 * This hook prevent buddyforms plugin to save user meta. Is important to use it if you like to save the user meta with your own plugin.
				 *
				 * @since 2.1.7
				 *
				 * @param boolean $grant This parameter determine if the data will be saved by buddyforms functions.
				 * @param string $type The type of information for the next parameter. Possible values is 'browser data' and 'field'.
				 * @param array $data This parameter holds the information what wil be saved.
				 */
				$save_usermeta = apply_filters( 'buddyforms_not_save_usermeta', true, 'field', $r_field );
				if ( $save_usermeta ) {
					buddyforms_update_user_meta( $user_id, $r_field['type'], $r_field['slug'] );
				}
			}
		}

		$args = array(
			'hasError'     => $hasError,
			'form_notice'  => isset( $form_notice ) ? $form_notice : false,
			'customfields' => isset( $customfields ) ? $customfields : false,
			'redirect_to'  => $redirect_to,
			'form_slug'    => $form_slug,
			'user_id'      => $user_id
		);

		do_action( 'buddyforms_process_submission_end', $args );
		//Form::clearValues( "buddyforms_form_" . $form_slug );

	}

	// Check if user is logged in and if not check if registration during submission is enabled.
	if ( isset( $buddyforms[ $form_slug ]['public_submit_create_account'] ) && ! is_user_logged_in() ) {

		// ok let us try to register a user
		$user_id = buddyforms_wp_insert_user();

		// Check if registration was successful
		if ( ! $user_id ) {
			$args = array(
				'hasError'  => true,
				'form_slug' => $form_slug,
			);
			Form::clearValues( "buddyforms_form_" . $form_slug );

			return $args;
		}
		if ( buddyforms_core_fs()->is__premium_only() && ! empty( $user_data ) ) {
			/**
			 * Avoid save user meta data.
			 *
			 * This hook prevent buddyforms plugin to save user meta. Is important to use it if you like to save the user meta with your own plugin.
			 *
			 * @since 2.1.7
			 *
			 * @param boolean $grant This parameter determine if the data will be saved by buddyforms functions.
			 * @param string $type The type of information for the next parameter. Possible values is 'browser data' and 'field'.
			 * @param array $data This parameter holds the information what wil be saved.
			 */
			$save_usermeta = apply_filters( 'buddyforms_not_save_usermeta', true, 'browser_data', $user_data );
			if ( $save_usermeta ) {
				// Save the Browser user data
				add_user_meta( $user_id, 'buddyforms_browser_user_data', $user_data, true );
			}
		}
	}

	// Ok let us start processing the post form
	do_action( 'buddyforms_process_submission_start', $args );

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
		if ( $the_post->post_author == $user_id ) {
			$user_can_edit = true;
		}
		$user_can_edit = apply_filters( 'buddyforms_user_can_edit', $user_can_edit, $form_slug, $post_id );
		if ( $user_can_edit == false ) {
			$args = array(
				'hasError'      => true,
				'error_message' => apply_filters( 'buddyforms_user_can_edit_error_message', __( 'You do not have the required user role to use this form', 'buddyforms' ) ),
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
	if ( isset( $buddyforms[ $form_slug ]['public_submit'] ) && $buddyforms[ $form_slug ]['public_submit'] == 'public_submit' ) {
		$user_can_edit = true;
	}
	$user_can_edit = apply_filters( 'buddyforms_user_can_edit', $user_can_edit, $form_slug, $post_id );
	if ( $user_can_edit == false ) {
		$args = array(
			'hasError'      => true,
			'error_message' => apply_filters( 'buddyforms_user_role_error_message', __( 'You do not have the required user role to use this form', 'buddyforms' ) ),
		);

		return $args;
	}

	// If post_id == 0 a new post is created
	if ( $post_id == 0 ) {
		require_once( ABSPATH . 'wp-admin/includes/admin.php' );
		// $the_post = get_default_post_to_edit( $post_type );
	}

	if ( isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {
		$customfields = $buddyforms[ $form_slug ]['form_fields'];
	}

	$comment_status = $buddyforms[ $form_slug ]['comment_status'];
	if ( isset( $_POST['comment_status'] ) ) {
		$comment_status = $_POST['comment_status'];
	}

	// Check if post_excerpt form element exist and if has values if empty check for default
	$post_excerpt = apply_filters( 'buddyforms_update_post_excerpt', ! empty( $_POST['post_excerpt'] ) ? $_POST['post_excerpt'] : '' );
	if ( empty( $post_excerpt ) ) {
		$content_field = buddyforms_get_form_field_by_slug( $form_slug, 'post_excerpt' );
		$post_excerpt  = $content_field['generate_post_excerpt'];
		$post_excerpt  = buddyforms_str_replace_form_fields_val_by_slug( $post_excerpt, $customfields, $post_id );
	}

	$action      = 'save';
	$post_status = $buddyforms[ $form_slug ]['status'];
	if ( $post_id != 0 ) {
		$action      = 'update';
		$post_status = get_post_status( $post_id );
	}
	$exist_field_status = buddyforms_exist_field_type_in_form( $form_slug, 'status' );
	if ( ! empty( $args['status'] ) && $exist_field_status ) {
		$post_status = $args['status'];
	}
	$post_status   = apply_filters( 'buddyforms_create_edit_form_post_status', $post_status, $form_slug );
	$the_author_id = apply_filters( 'buddyforms_the_author_id', $user_id, $form_slug, $post_id );

	$args = Array(
		'post_id'        => $post_id,
		'action'         => $action,
		'form_slug'      => $form_slug,
		'post_type'      => $post_type,
		'post_author'    => $the_author_id,
		'post_status'    => $post_status,
		'post_parent'    => $post_parent,
		'comment_status' => $comment_status,
	);

	if ( ! empty( $post_excerpt ) ) {
		$args['post_excerpt'] = $post_excerpt;
	}
	$post_author = '';
	$args        = buddyforms_update_post( $args );
	extract( $args );

	/*
	 * Check if the update or insert was successful
	 */
	if ( ! is_wp_error( $post_id ) && ! empty( $post_id ) ) {

		// Check if the post has post meta / custom fields
		if ( isset( $customfields ) ) {
			$customfields = buddyforms_update_post_meta( $post_id, $customfields );
		}

		$have_user_fields = false;
		foreach ( $customfields as $customfield ) {
			if ( in_array( $customfield, buddyforms_user_fields_array() ) ) {
				$have_user_fields = true;
				break;
			}
		}

		//TODO gfirem this need to be in other way review with @sven
		// Check if user is logged in and update user relevant fields if used in the form
		if ( is_user_logged_in() && 'registration' !== $form_type && $have_user_fields === true ) {
			$user_id = buddyforms_wp_update_user();
		}

		/*
		 * Process field submission for 3rd party and internal code. For example the Upload Field and Feature Image
		 */
		foreach ( $customfields as $customfield ) {
			$field_slug = $customfield['slug'];
			$field_type = $customfield['type'];

			do_action( 'buddyforms_process_field_submission', $field_slug, $field_type, $customfield, $post_id, $form_slug, $args, $action );
		}

		// Save the Form slug as post meta
		update_post_meta( $post_id, "_bf_form_slug", $form_slug );

		// If this was a registration form save the user id
		if ( isset( $user_id ) ) {
			update_post_meta( $post_id, "_bf_registration_user_id", $user_id );
		}

		if ( buddyforms_core_fs()->is__premium_only() && ! empty( $user_data ) ) {
			// Save the User Data like browser ip etc
			update_post_meta( $post_id, "_bf_user_data", $user_data );
		}

		$post_title = apply_filters( 'buddyforms_update_form_title', isset( $_POST['buddyforms_form_title'] ) && ! empty( $_POST['buddyforms_form_title'] ) ? stripslashes( $_POST['buddyforms_form_title'] ) : 'none', $form_slug, $post_id );
		$bf_post    = array(
			'ID'             => $post_id,
			'post_type'      => $post_type,
			'post_title'     => $post_title,
			'post_content'   => apply_filters( 'buddyforms_update_form_content', isset( $_POST['buddyforms_form_content'] ) && ! empty( $_POST['buddyforms_form_content'] ) ? $_POST['buddyforms_form_content'] : '', $form_slug, $post_id ),
			'post_status'    => $post_status,
			'comment_status' => $comment_status,
			'post_parent'    => $post_parent,
		);

		if ( isset( $new_post ) && $post_id == $new_post ) {
			$bf_post['post_name'] = sanitize_title( $post_title );
		}

		if ( ! empty( $post_excerpt ) ) {
			$bf_post['post_excerpt'] = $post_excerpt;
		}

		// Update the new post
		if ( ! empty( $post_id ) ) {
			$post_id = wp_update_post( $bf_post, true );
			if ( is_wp_error( $post_id ) ) {
				$hasError      = true;
				$error_message = $post_id->get_error_message();
				Form::setError( 'buddyforms_form_' . $form_slug, $post_id->get_error_message() );
			}
		}

	} else {
		$hasError      = true;
		$error_message = $post_id->get_error_message();
		Form::setError( 'buddyforms_form_' . $form_slug, $post_id->get_error_message() );
	}

	// Display the message
	if ( ! $hasError ) :
		if ( isset( $_POST['post_id'] ) && ! empty( $_POST['post_id'] ) ) {
			$info_message = __( 'The ', 'buddyforms' ) . $buddyforms[ $form_slug ]['singular_name'] . __( ' has been successfully updated ', 'buddyforms' );
			$form_notice  = '<div class="info alert">' . $info_message . '</div>';
		} else {
			// Update the new post
			$info_message = __( 'The ', 'buddyforms' ) . $buddyforms[ $form_slug ]['singular_name'] . __( ' has been successfully created ', 'buddyforms' );
			$form_notice  = '<div class="info alert">' . $info_message . '</div>';
		}

	else:
		if ( empty( $error_message ) ) {
			$error_message = apply_filters( 'buddyforms_error_submitting_form', __( 'Error! There was a problem submitting the post ;-(', 'buddyforms' ) );
		}
		$form_notice = '<div class="bf-alert error">' . $error_message . '</div>';

		if ( ! empty( $fileError ) ) {
			$form_notice = '<div class="bf-alert error">' . $fileError . '</div>';
		}

	endif;

	do_action( 'buddyforms_after_save_post', $post_id );

	$args2 = array(
		'hasError'     => $hasError,
		'form_notice'  => empty( $form_notice ) ? '' : $form_notice,
		'customfields' => is_array( $customfields ) ? $customfields : array(),
		'redirect_to'  => $redirect_to,
		'form_slug'    => $form_slug,
		'form_type'    => $form_type,
		'action'       => $action,
	);

	$args = array_merge( $args, $args2 );

	do_action( 'buddyforms_process_submission_end', $args );
	Form::clearValues( "buddyforms_form_" . $form_slug );

	if ( buddyforms_is_multisite() ) {
		restore_current_blog();
	}

	return $args;
}

/**
 * @param $args
 *
 * @return array|bool
 */
function buddyforms_update_post( $args ) {

	$action         = '';
	$post_author    = '';
	$post_type      = '';
	$post_status    = '';
	$comment_status = '';
	$post_parent    = 0;
	$form_slug      = '';
	$post_id        = 0;

	$args = apply_filters( 'buddyforms_update_post_args', $args );

	extract( $args );

	$buddyforms_form_nonce_value = $_POST['_wpnonce'];

	if ( ! wp_verify_nonce( $buddyforms_form_nonce_value, 'buddyforms_form_nonce' ) ) {
		return false;
	}

	// Check if post is new or edit
	if ( $action == 'update' ) {

		$bf_post = array(
			'ID'             => intval( $_POST['post_id'] ),
			'post_author'    => $post_author,
			'post_title'     => apply_filters( 'buddyforms_update_form_title', isset( $_POST['buddyforms_form_title'] ) && ! empty( $_POST['buddyforms_form_title'] ) ? stripslashes( $_POST['buddyforms_form_title'] ) : 'none', $form_slug, $post_id ),
			'post_content'   => apply_filters( 'buddyforms_update_form_content', isset( $_POST['buddyforms_form_content'] ) && ! empty( $_POST['buddyforms_form_content'] ) ? $_POST['buddyforms_form_content'] : '', $form_slug, $post_id ),
			'post_type'      => $post_type,
			'post_status'    => $post_status,
			'comment_status' => $comment_status,
			'post_parent'    => $post_parent,
		);

		if ( ! empty( $post_excerpt ) ) {
			$bf_post['post_excerpt'] = $post_excerpt;
		}

		$bf_post = apply_filters( 'buddyforms_wp_update_post_args', $bf_post, $form_slug );

		// Update the new post
		$post_id = wp_update_post( $bf_post, true );

	} else {

		$bf_post = array(
			'post_parent'    => $post_parent,
			'post_author'    => $post_author,
			'post_title'     => apply_filters( 'buddyforms_update_form_title', isset( $_POST['buddyforms_form_title'] ) && ! empty( $_POST['buddyforms_form_title'] ) ? stripslashes( $_POST['buddyforms_form_title'] ) : 'none', $form_slug, $post_id ),
			'post_content'   => apply_filters( 'buddyforms_update_form_content', isset( $_POST['buddyforms_form_content'] ) && ! empty( $_POST['buddyforms_form_content'] ) ? $_POST['buddyforms_form_content'] : '', $form_slug, $post_id ),
			'post_type'      => $post_type,
			'post_status'    => $post_status,
			'comment_status' => $comment_status,
		);

		if ( ! empty( $post_excerpt ) ) {
			$bf_post['post_excerpt'] = $post_excerpt;
		}

		// Add optional scheduled post dates
		if ( isset( $_POST['status'] ) && $_POST['status'] == 'future' && $_POST['schedule'] ) {
			$post_date                = date( 'Y-m-d H:i:s', strtotime( $_POST['schedule'] ) );
			$bf_post['post_date']     = $post_date;
			$bf_post['post_date_gmt'] = $post_date;
		}

		$bf_post = apply_filters( 'buddyforms_wp_insert_post_args', $bf_post, $form_slug );

		// Insert the new form
		$post_id = wp_insert_post( $bf_post, true );

		if ( ! is_wp_error( $post_id ) ) {
			$bf_post['new_post'] = $post_id;
		}

	}

	$bf_post['post_id'] = $post_id;

	return $bf_post;
}

/**
 * @param integer $post_id
 * @param array $customfields
 *
 * @return mixed
 */
function buddyforms_update_post_meta( $post_id, $customfields ) {
	global $buddyforms, $form_slug;

	if ( ! isset( $customfields ) ) {
		return $post_id;
	}

	foreach ( $customfields as $key => $customfield ) :

		if ( isset( $customfield['slug'] ) ) {
			$slug = $customfield['slug'];
		}

		if ( empty( $slug ) ) {
			$slug = sanitize_title( $customfield['name'] );
		}

		// Update the post
		if ( isset( $_POST[ $slug ] ) && ! ( $_POST[ $slug ] == 'user_pass' || $_POST[ $slug ] == 'user_pass_confirm' ) ) {
			update_post_meta( $post_id, $slug, buddyforms_sanitize( $customfield['type'], $_POST[ $slug ] ) );
		} else {
			if ( ! is_admin() ) {
				update_post_meta( $post_id, $slug, '' );
			}
		}

		// Save the GDPR Agreement
		if ( $customfield['type'] == 'gdpr' && isset( $customfield['options'] ) ) {
			foreach ( $customfield['options'] as $key => $option ) {
				$gdpr_data[ $key ]['checked'] = buddyforms_sanitize( $customfield['type'], $_POST[ $slug . '_' . $key ] );
				$gdpr_data[ $key ]['label']   = $option['label'];

			}
			update_post_meta( $post_id, $slug, $gdpr_data );
		}

		//
		// Check if file is new and needs to get reassigned to the correct parent
		//
		if ( $customfield['type'] == 'textarea' && ! empty( $_POST[ $customfield['slug'] ] ) ) {

			$textarea = apply_filters( 'buddyforms_update_form_textarea', isset( $_POST[ $customfield['slug'] ] ) && ! empty( $customfield['slug'] ) ? $_POST[ $customfield['slug'] ] : '' );
			if ( empty( $textarea ) ) {

				$this_customfield = buddyforms_get_form_field_by_slug( $form_slug, $customfield['slug'] );
				$textarea         = $this_customfield['generate_textarea'];

				$textarea = buddyforms_str_replace_form_fields_val_by_slug( $textarea, $customfields, $post_id );

				update_post_meta( $post_id, $slug, buddyforms_sanitize( $customfield['type'], $textarea ) );

			}
		}

		//
		// Check if file is new and needs to get reassigned to the correct parent
		//
		if ( $customfield['type'] == 'file' && ! empty( $_POST[ $customfield['slug'] ] ) ) {

			$attachement_ids = $_POST[ $customfield['slug'] ];
			$attachement_ids = explode( ',', $attachement_ids );

			if ( is_array( $attachement_ids ) ) {
				foreach ( $attachement_ids as $attachement_id ) {

					$attachement = get_post( $attachement_id );

					if ( $attachement->post_parent == $buddyforms[ $form_slug ]['attached_page'] ) {
						$attachement = array(
							'ID'          => $attachement_id,
							'post_parent' => $post_id,
						);
						wp_update_post( $attachement );
					}
				}
			}
		}

		//
		// Check if featured image is new and needs to get reassigned to the corect parent
		//
		if ( $customfield['type'] == 'featured-image' || $customfield['type'] == 'featured_image' && isset( $_POST['featured_image'] ) ) {

			$attachement_id = $_POST['featured_image'];

			$attachement = get_post( $attachement_id );

			if ( is_object( $attachement ) && $attachement->post_parent == $buddyforms[ $form_slug ]['attached_page'] ) {
				$attachement = array(
					'ID'          => $attachement_id,
					'post_parent' => $post_id,
				);
				wp_update_post( $attachement );
				if ( isset( $_POST['action'] ) ) {
					if ( $_POST['action'] == 'editpost' ) {

						set_post_thumbnail( $post_id, $attachement_id );
					}
				}
			}

		}

		//
		// Save post format if needed
		//
		if ( $customfield['type'] == 'post_formats' && isset( $_POST['post_formats'] ) && $_POST['post_formats'] != 'none' ) :
			set_post_format( $post_id, $_POST['post_formats'] );
		endif;

		//
		// Save taxonomies if needed
		// taxonomy, category, tags
		if ( $customfield['type'] == 'taxonomy' || $customfield['type'] == 'category' || $customfield['type'] == 'tags' ) :
			//return when on backend post edit page
			if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
				return;
			}


			if ( ! isset( $customfield['taxonomy'] ) ) {
				$customfield['taxonomy'] = 'none';
			}
			if ( $customfield['taxonomy'] == 'none' ) {

				if ( $customfield['type'] == 'tags' ) {
					$customfield['taxonomy'] = 'post_tag';
				} elseif ( $customfield['type'] == 'category' ) {
					$customfield['taxonomy'] = 'category';
				}

			}

			if ( $customfield['taxonomy'] != 'none' && isset( $_POST[ $customfield['slug'] ] ) ) {

				// Get the tax items
				$tax_terms = $_POST[ $customfield['slug'] ];
				$taxonomy  = get_taxonomy( $customfield['taxonomy'] );

				// Get the term list before delete all term relations
				$term_list = wp_get_post_terms( $post_id, $customfield['taxonomy'], array( "fields" => "ids" ) );

				// Let us delete all and re assign.
				wp_delete_object_term_relationships( $post_id, $customfield['taxonomy'] );

				// Ctreate a new empty arry for our taxonomy terms
				$new_tax_items = array();

				// If no tax items are available check if we have some defaults we can use
				if ( $tax_terms[0] == - 1 && ! empty( $customfield['taxonomy_default'] ) ) {
					foreach ( $customfield['taxonomy_default'] as $key_tax => $tax ) {
						$tax_terms[ $key_tax ] = $tax;
					}
				}

				// Check if new term to insert
				if ( isset( $tax_terms ) && is_array( $tax_terms ) ) {
					foreach ( $tax_terms as $term_key => $term ) {

						if ( (integer) $term == - 1 ) {
							continue;
						}
						// Check if the term exist
						$term_exist = term_exists( (integer) $term, $customfield['taxonomy'] );

						// Create new term if need and add to the new tax items array
						if ( ! $term_exist ) {
							$new_term                              = wp_insert_term( $term, $customfield['taxonomy'] );
							$term                                  = get_term_by( 'id', $new_term['term_id'], $customfield['taxonomy'] );
							$new_tax_items[ $new_term['term_id'] ] = $term->slug;
						} else {
							$term                                    = get_term_by( 'id', $term_exist['term_id'], $customfield['taxonomy'] );
							$new_tax_items[ $term_exist['term_id'] ] = $term->slug;
						}

					}
				}

				// Check if the taxonomy is hierarchical and prepare the string
				if ( isset( $taxonomy->hierarchical ) && $taxonomy->hierarchical == true ) {
					$cat_string = implode( ', ', array_map(
						function ( $v, $k ) {
							return sprintf( "%s", $k );
						},
						$new_tax_items,
						array_keys( $new_tax_items )
					) );
				} else {
					$cat_string = implode( ', ', $new_tax_items );
				}

				// We need to check if an excluded term was added via the backend edit screen.
				// If a excluded term is found we need to make sure to add it to the cat_string. Otherwise the term is lost by every update from teh frontend
				if ( isset( $customfield['taxonomy_exclude'] ) && is_array( $customfield['taxonomy_exclude'] ) ) {
					foreach ( $customfield['taxonomy_exclude'] as $exclude ) {
						if ( in_array( $exclude, $term_list ) ) {
							$cat_string .= ', ' . $exclude;
						}
					}
				}

				// Add the new terms to the taxonomy
				wp_set_post_terms( $post_id, $cat_string, $customfield['taxonomy'], true );

			} else {
				wp_delete_object_term_relationships( $post_id, $customfield['taxonomy'] );
			}

		endif;

		// Update meta do_action to hook into. This can be needed if you added
		// new form elements and need to manipulate how they get saved.
		do_action( 'buddyforms_update_post_meta', $customfield, $post_id );

	endforeach;

	return $customfields;
}

add_filter( 'wp_handle_upload_prefilter', 'buddyforms_wp_handle_upload_prefilter' );
/**
 * @param $file
 *
 * @return mixed
 */
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

/**
 * @return array
 */
function buddyforms_get_browser() {
	$u_agent  = $_SERVER['HTTP_USER_AGENT'];
	$bname    = 'Unknown';
	$platform = 'Unknown';
	$version  = "";

	//First get the platform?
	if ( preg_match( '/linux/i', $u_agent ) ) {
		$platform = 'linux';
	} elseif ( preg_match( '/macintosh|mac os x/i', $u_agent ) ) {
		$platform = 'mac';
	} elseif ( preg_match( '/windows|win32/i', $u_agent ) ) {
		$platform = 'windows';
	}

	// Next get the name of the useragent yes seperately and for good reason
	if ( preg_match( '/MSIE/i', $u_agent ) && ! preg_match( '/Opera/i', $u_agent ) ) {
		$bname = 'Internet Explorer';
		$ub    = "MSIE";
	} elseif ( preg_match( '/Firefox/i', $u_agent ) ) {
		$bname = 'Mozilla Firefox';
		$ub    = "Firefox";
	} elseif ( preg_match( '/Chrome/i', $u_agent ) ) {
		$bname = 'Google Chrome';
		$ub    = "Chrome";
	} elseif ( preg_match( '/Safari/i', $u_agent ) ) {
		$bname = 'Apple Safari';
		$ub    = "Safari";
	} elseif ( preg_match( '/Opera/i', $u_agent ) ) {
		$bname = 'Opera';
		$ub    = "Opera";
	} elseif ( preg_match( '/Netscape/i', $u_agent ) ) {
		$bname = 'Netscape';
		$ub    = "Netscape";
	}

	// finally get the correct version number
	$known   = array( 'Version', $ub, 'other' );
	$pattern = '#(?<browser>' . join( '|', $known ) .
	           ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	if ( ! preg_match_all( $pattern, $u_agent, $matches ) ) {
		// we have no matching number just continue
	}

	// see how many we have
	$i = count( $matches['browser'] );
	if ( $i != 1 ) {
		//we will have two since we are not using 'other' argument yet
		//see if version is before or after the name
		if ( strripos( $u_agent, "Version" ) < strripos( $u_agent, $ub ) ) {
			$version = $matches['version'][0];
		} else {
			$version = $matches['version'][1];
		}
	} else {
		$version = $matches['version'][0];
	}

	// check if we have a number
	if ( $version == null || $version == "" ) {
		$version = "?";
	}

	return array(
		'userAgent' => $u_agent,
		'name'      => $bname,
		'version'   => $version,
		'platform'  => $platform,
		'pattern'   => $pattern
	);
}

function buddyforms_str_replace_form_fields_val_by_slug( $string, $customfields, $post_id ) {
	if ( isset( $customfields ) ) {
		foreach ( $customfields as $f_slug => $t_field ) {
			if ( isset( $t_field['slug'] ) && isset ( $_POST[ $t_field['slug'] ] ) ) {

				$field_val = $_POST[ $t_field['slug'] ];

				switch ( $t_field['type'] ) {
					case 'taxonomy' :
					case 'category' :
					case 'tags' :
						if ( ! is_wp_error( $post_id ) && ! empty( $post_id ) ) {
							$string_tmp = get_the_term_list( $post_id, $t_field['taxonomy'], "<span class='" . $t_field['slug'] . "'>", ' - ', "</span>" );
						}
						break;
					case 'user_website':
						$string_tmp = "<span class='" . $t_field['slug'] . "'><a href='" . $field_val . "' " . $t_field['name'] . ">" . $field_val . " </a></span>";
						break;
					default:
						$string_tmp = "<span class='" . $t_field['slug'] . "'>" . $field_val . "</span>";
						break;
				}

				$string = str_replace( '[' . $t_field['slug'] . ']', $string_tmp, $string );
			} else {
				$string = str_replace( '[' . $t_field['slug'] . ']', '', $string );
			}
		}
	}

	return $string;
}

add_filter( 'buddyforms_update_form_title', 'buddyforms_update_form_title', 2, 10 );
function buddyforms_update_form_title( $post_title, $form_slug, $post_id ) {
	global $buddyforms;

	if ( isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {
		$customfields = $buddyforms[ $form_slug ]['form_fields'];
	}

	$title_field = buddyforms_get_form_field_by_slug( $form_slug, 'buddyforms_form_title' );

	if ( isset( $title_field['generate_title'] ) && ! empty( $title_field['generate_title'] ) ) {
		$post_title = buddyforms_str_replace_form_fields_val_by_slug( $title_field['generate_title'], $customfields, $post_id );
	}

	return $post_title;

}

add_filter( 'buddyforms_update_form_content', 'buddyforms_update_form_content', 2, 10 );
function buddyforms_update_form_content( $post_content, $form_slug, $post_id ) {
	global $buddyforms;

	if ( isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {
		$customfields = $buddyforms[ $form_slug ]['form_fields'];
	}

	$content_field = buddyforms_get_form_field_by_slug( $form_slug, 'buddyforms_form_content' );

	if ( isset( $content_field['generate_content'] ) && ! empty( $content_field['generate_content'] ) ) {
		$post_content = buddyforms_str_replace_form_fields_val_by_slug( $content_field['generate_content'], $customfields, $post_id );
	}

	return $post_content;
}


add_action( 'edit_post', 'buddyforms_after_update_post', 10, 2 );
/**
 * @param integer $post_ID
 * @param WP_Post $post
 */
function buddyforms_after_update_post( $post_ID, $post ) {
	if ( ! empty( $_POST ) && ! empty( $_POST['_bf_form_slug'] ) && intval( $post_ID ) === intval( $_POST['post_ID'] ) && ! empty( $_POST['meta'] ) ) {
		global $buddyforms;

		if ( ! isset( $buddyforms[ $_POST['_bf_form_slug'] ]['form_fields'] ) ) {
			return;
		}

		$fields = $buddyforms[ $_POST['_bf_form_slug'] ]['form_fields'];
		foreach ( $fields as $key => $field ) {
			if ( isset( $field['slug'] ) ) {
				$slug = $field['slug'];
			}
			if ( empty( $slug ) ) {
				$slug = sanitize_title( $field['name'] );
			}
			switch ( $field['type'] ) {
				case 'title':
					$value = isset( $_POST['post_title'] ) ? $_POST['post_title'] : '';
					break;
				case 'content':
					$value = isset( $_POST['content'] ) ? $_POST['content'] : '';
					break;
				default:
					$value = isset( $_POST[ $slug ] ) ? $_POST[ $slug ] : '';
			}
			$_POST[ $field['slug'] ] = $value;
		}
	}
}
