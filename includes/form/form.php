<?php

/**
 * Adds a form shortcode for the create and edit screen
 * @var $args = posttype, the_post, post_id
 *
 * @package buddyforms
 * @since 0.1-beta
 *
 * @since 2.4.0 The function only return string or empty
 */
function buddyforms_create_edit_form( $args ) {
	global $current_user, $buddyforms, $wp_query, $bf_form_response_args, $bf_form_error;

	// First check if any form error exist
	if ( ! empty( $bf_form_error ) ) {
		echo '<div class="bf-alert error">' . $bf_form_error . '</div>';

		return;
	}

	do_action( 'buddyforms_create_edit_form_loader' );

	// Hook for plugins to overwrite the $args.
	$args = apply_filters( 'buddyforms_create_edit_form_args', $args );

	$post_type   = '';
	$the_post    = 0;
	$post_id     = 0;
	$post_parent = 0;
	$post_status = '';
	$form_slug   = false;
	$form_notice = '';

	$short_array = shortcode_atts( array(
		'post_type'   => '',
		'the_post'    => 0,
		'post_id'     => 0,
		'post_parent' => 0,
		'form_slug'   => false,
		'form_notice' => '',
	), $args );

	extract( $short_array );

	if ( empty( $buddyforms[ $form_slug ] ) ) {
		return;
	}

	buddyforms_switch_to_form_blog( $form_slug );

	$current_user = wp_get_current_user();

	if ( empty( $post_type ) ) {
		$post_type = $buddyforms[ $form_slug ]['post_type'];
	}

	if ( $buddyforms[ $form_slug ]['form_type'] == 'registration' && is_user_logged_in() ) {
		$current_user_entry = new WP_Query( array(
			'post_type'      => $post_type,
			'fields'         => 'ids',
			'posts_per_page' => '1',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'author'         => $current_user->ID,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'   => '_bf_form_slug',
					'value' => sanitize_title( $form_slug ),
				)
			)
		) );
		if ( ! empty( $current_user_entry->posts ) ) {
			$post_id = $current_user_entry->posts[0];
		}
	}

	// if post edit screen is displayed in pages
	if ( isset( $wp_query->query_vars['bf_action'] ) ) {

		$form_slug = '';
		if ( isset( $wp_query->query_vars['bf_form_slug'] ) ) {
			$form_slug = $wp_query->query_vars['bf_form_slug'];
		}

		$post_id = 0;
		if ( isset( $wp_query->query_vars['bf_post_id'] ) ) {
			$post_id = $wp_query->query_vars['bf_post_id'];
		}

		$post_parent = 0;
		if ( isset( $wp_query->query_vars['bf_parent_post_id'] ) ) {
			$post_parent = $wp_query->query_vars['bf_parent_post_id'];
		}

		$revision_id = 0;
		if ( isset( $wp_query->query_vars['bf_rev_id'] ) ) {
			$revision_id = $wp_query->query_vars['bf_rev_id'];
		}

		$post_type = $buddyforms[ $form_slug ]['post_type'];

		if ( ! empty( $revision_id ) ) {
			$the_post = get_post( $revision_id );
		} else {
			$post_id  = apply_filters( 'buddyforms_create_edit_form_post_id', $post_id );
			$the_post = get_post( $post_id, 'OBJECT' );
		}

		if ( $wp_query->query_vars['bf_action'] == 'edit' ) {

			$user_can_edit = false;
			if ( $the_post->post_author == $current_user->ID ) {
				$user_can_edit = true;
			}
			$user_can_edit = apply_filters( 'buddyforms_user_can_edit', $user_can_edit, $form_slug, $post_id );

			if ( $user_can_edit == false ) {
				$error_message = apply_filters( 'buddyforms_user_can_edit_error_message', __( 'You are not allowed to edit this post. What are you doing here?', 'buddyforms' ) );
				echo '<div class="bf-alert error">' . $error_message . '</div>';

				return;
			}

		}

	}

	// if post edit screen is displayed
	if ( ! empty( $post_id ) && $buddyforms[ $form_slug ]['form_type'] !== 'registration') {

		if ( ! empty( $revision_id ) ) {
			$the_post = get_post( $revision_id );
		} else {
			$post_id  = apply_filters( 'buddyforms_create_edit_form_post_id', $post_id );
			$the_post = get_post( $post_id );
		}

		$user_can_edit = false;
		if ( $the_post->post_author == $current_user->ID ) {
			$user_can_edit = true;
		}
		$user_can_edit = apply_filters( 'buddyforms_user_can_edit', $user_can_edit, $form_slug, $post_id );

		if ( $user_can_edit == false ) {
			$error_message = apply_filters( 'buddyforms_user_can_edit_error_message', __( 'You are not allowed to edit this post. What are you doing here?', 'buddyforms' ) );
			echo '<div class="bf-alert error">' . $error_message . '</div>';

			return;
		}
	}

	// If post_id == 0 a new post is created
	if ( $post_id == 0 ) {
		require_once( ABSPATH . 'wp-admin/includes/admin.php' );
		$the_post = get_default_post_to_edit( $post_type, true );
	}

	if ( empty( $post_type ) ) {
		$post_type = $the_post->post_type;
	} //buddyforms??

	if ( empty( $form_slug ) ) {
		$form_slug = apply_filters( 'buddyforms_the_form_to_use', $form_slug, $post_type );
	}

	if ( ! isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {
		$error_message = apply_filters( 'buddyforms_no_form_elements_error_message', __( 'This form has no fields yet. Nothing to fill out so far. Add fields to your form to make it useful.', 'buddyforms' ) );
		echo '<div class="bf-alert error">' . $error_message . '</div>';

		return;
	}

	$customfields = $buddyforms[ $form_slug ]['form_fields'];

	if ( ! empty( $the_post ) ) {
		if(empty($post_parent) && ! empty( $the_post->post_parent )) {
			$post_parent = $the_post->post_parent;
		}
		if(empty($post_status) && ! empty( $the_post->post_status )) {
			$post_status = $the_post->post_status;
		}
	}

	$args = array(
		'post_type'    => $post_type,
		'the_post'     => $the_post,
		'post_parent'  => $post_parent,
		'post_status'  => $post_status,
		'customfields' => $customfields,
		'post_id'      => apply_filters( 'buddyforms_set_post_id_for_draft', $post_id, $args, $customfields ),
		'form_slug'    => $form_slug,
		'form_notice'  => $form_notice,
	);

	if ( isset( $_POST['form_slug'] ) ) {
		//decide if the update of create message will show.
		$form_type = ( ! empty( $args['form_type'] ) ) ? $args['form_type'] : 'submission';
		$form_action = ( ! empty( $args['action'] ) ) ? $args['action'] : 'save';
		$message_source = 'after_submit_message_text';
		if ( 'registration' === $form_type ) {
			if ( is_user_logged_in() ) {
				$message_source = 'after_update_submit_message_text';
			}
		} else {
			if ( 'update' === $form_action ) {
				$message_source = 'after_update_submit_message_text';
			}
		}
		$display_message = buddyforms_form_display_message($_POST['form_slug'], $args['post_id'], $message_source);
		$args['form_notice'] = $display_message;

		if ( isset( $_POST['bf_submitted'] ) && $buddyforms[ $_POST['form_slug'] ]['after_submit'] == 'display_message' ) {
			echo $display_message;

			return;
		}
	}

	if ( isset( $_POST['bf_submitted'] ) ) {
		$args = $bf_form_response_args;
	}

	echo buddyforms_form_html( $args );

	if ( buddyforms_is_multisite() ) {
		restore_current_blog();
	}
}

/**
 * Save the submited form and create a global array with the response array
 *
 * @package buddyforms
 * @since 1.5
 */

add_action( 'wp', 'buddyforms_form_response_no_ajax' );
function buddyforms_form_response_no_ajax() {
	global $buddyforms, $bf_form_response_args;

	// If the form is submitted we will get in action
	if ( isset( $_POST['bf_submitted'] ) ) {

		$bf_form_response_args = buddyforms_process_submission( $_POST );

		$post_id = 0;
		extract( $bf_form_response_args );

		if ( isset( $hasError ) ) {
			wp_redirect( $_SERVER['HTTP_REFERER'], 302 );
			exit;
		}

		if ( isset( $buddyforms[ $_POST['form_slug'] ]['after_submit'] ) ) {
			if ( $buddyforms[ $_POST['form_slug'] ]['after_submit'] == 'display_post' ) {
				$permalink = get_permalink( $post_id );
				$permalink = apply_filters( 'buddyforms_after_save_post_redirect', $permalink );
				wp_redirect( $permalink, 302 );
				exit;
			}
			if ( $buddyforms[ $_POST['form_slug'] ]['after_submit'] == 'display_page' ) {
				$permalink = get_permalink( $buddyforms[ $_POST['form_slug'] ]['after_submission_page'] );
				$permalink = apply_filters( 'buddyforms_after_save_post_redirect', $permalink );
				wp_redirect( $permalink, 302 );
				exit;
			}
			if ( $buddyforms[ $_POST['form_slug'] ]['after_submit'] == 'redirect' ) {
				$permalink = $buddyforms[ $_POST['form_slug'] ]['after_submission_url'];
				$permalink = apply_filters( 'buddyforms_after_save_post_redirect', $permalink );
				wp_redirect( $permalink, 302 );
				exit;
			}
			if ( $buddyforms[ $_POST['form_slug'] ]['after_submit'] == 'display_posts_list' ) {
				$permalink      = get_permalink( $buddyforms[ $_POST['form_slug'] ]['attached_page'] );
				$post_list_link = $permalink . 'view/' . $_POST['form_slug'] . '/';
				$post_list_link = apply_filters( 'buddyforms_after_save_post_redirect', $post_list_link );
				wp_redirect( $post_list_link, 302 );
				exit;
			}

		}

	}

}
