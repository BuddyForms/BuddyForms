<?php

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

	extract( $args );

	if ( $haserror == true ) {


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
					$json['form_notice'] = $buddyforms[ $_POST['form_slug'] ]['after_submission_url'];
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
