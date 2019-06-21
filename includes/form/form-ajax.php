<?php

add_action( 'wp_ajax_buddyforms_ajax_edit_post', 'buddyforms_ajax_edit_post' );
function buddyforms_ajax_edit_post() {
	$post_id   = intval( $_POST['post_id'] );
	$form_slug = get_post_meta( $post_id, '_bf_form_slug', true );

	$args = Array(
		'post_id'   => $post_id,
		'form_slug' => $form_slug
	);
	ob_start();
	buddyforms_create_edit_form( $args );
	$content = ob_get_clean();
	echo $content;
	die();
}

add_action( 'wp_ajax_bf_load_taxonomy', 'buddyforms_ajax_load_taxonomy' );
add_action( 'wp_ajax_nopriv_bf_load_taxonomy', 'buddyforms_ajax_load_taxonomy' );
function buddyforms_ajax_load_taxonomy(){
	if (! (is_array($_POST) && defined('DOING_AJAX') && DOING_AJAX)) {
		return;
	}

	if ( ! isset($_POST['action']) || wp_verify_nonce($_POST['nonce'], 'bf_tax_loading') === false ) {
		wp_die();
	}

	$args = array(
		'fields'       => 'id=>name',
		'hide_empty'   => 0,
		'child_of'     => 0,
		'orderby'      => 'SLUG',
		'cache_domain' => 'buddyforms_ajax_load_taxonomy',
	);

	$form_slug = '';
	if ( empty( $_POST['form_slug'] ) ) {
		wp_send_json_error( new WP_Error( 'invalid_form_slug', 'Invalid Form Slug' ), 500 );
	} else {
		$form_slug = sanitize_title( $_POST['form_slug'] );
	}

	if ( ! empty( $_POST['search'] ) ) {
		$args['search'] = sanitize_title_for_query( $_POST['search'] );
	}

	if ( ! empty( $_POST['taxonomy'] ) ) {
		$args['taxonomy'] = $_POST['taxonomy'];
	}

	if ( ! empty( $_POST['order'] ) ) {
		$args['order'] = $_POST['order'];
	}

	if ( ! empty( $_POST['exclude'] ) ) {
		$args['exclude'] = $_POST['exclude'];
	}

	if ( ! empty( $_POST['include'] ) ) {
		$args['include'] = $_POST['include'];
	}

	$terms_result = false;

	$terms_result = apply_filters( 'buddyforms_ajax_load_term_query', $terms_result, $args, $form_slug );

	if ( empty( $terms_result ) ) {
		$terms_result = new WP_Term_Query( $args );
	}

	if ( is_wp_error( $terms_result ) ) {
		wp_send_json_error( $terms_result, 500 );
	} else {
		$response = new stdClass;
		$result   = array();
		foreach ( $terms_result->get_terms() as $key => $term ) {
			$current       = new stdClass;
			$current->id   = $key;
			$current->text = $term;
			$result[]      = $current;
		}
		$response->results = $result;
		wp_send_json( $response );
	}
}

add_action( 'wp_ajax_buddyforms_ajax_process_edit_post', 'buddyforms_ajax_process_edit_post' );
add_action( 'wp_ajax_nopriv_buddyforms_ajax_process_edit_post', 'buddyforms_ajax_process_edit_post' );
function buddyforms_ajax_process_edit_post() {
	global $buddyforms;

	$form_data = array();

	if ( isset( $_POST['data'] ) ) {
		parse_str( $_POST['data'], $form_data );
		$_POST = $form_data;
	}

	$global_error = ErrorHandler::get_instance();

	$args = buddyforms_process_submission( $form_data );

	$hasError = false;
	$form_notice = '';
	$form_slug = '';

	$json_array = array();

	extract( $args );

	if ( empty( $form_slug ) ) {
		$form_slug = $form_data['form_slug'];
	}

	if ( $hasError == true ) {

		if ( $form_notice ) {
			$global_error->add_error(new BF_Error('buddyforms_form_' . $form_slug, $form_notice, $form_data, $form_slug));
		}

		if ( ! empty( $error_message ) ) {
			$global_error->add_error( new BF_Error( 'buddyforms_form_' . $form_slug, $error_message, $form_data, $form_slug ) );
		}

		$global_error->renderAjaxErrorResponse();

	} else {
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
		$display_message = buddyforms_form_display_message($form_slug, $args['post_id'], $message_source);
		$args['form_notice'] = $display_message;

		if ( isset( $buddyforms[ $form_slug ]['after_submit'] ) ) {
			switch ( $buddyforms[ $form_slug ]['after_submit'] ) {
				case 'display_post':
					$json_array['form_remove'] = 'true';
					$json_array['form_notice'] = buddyforms_after_save_post_redirect( get_permalink( $args['post_id'] ) );
					break;
				case 'display_page':
					$json_array['form_remove'] = 'true';
					$json_array['display_page'] = apply_filters( 'the_content', get_post_field( 'post_content', $buddyforms[ $form_slug ]['after_submission_page'] ) );
					break;
				case 'redirect':
					$json_array['form_remove'] = 'true';
					$json_array['form_notice'] = buddyforms_after_save_post_redirect( $buddyforms[ $form_slug ]['after_submission_url'] );
					break;
				case 'display_posts_list':
					$json_array['form_remove'] = 'true';
					$permalink           = get_permalink( $buddyforms[ $args['form_slug'] ]['attached_page'] );
					$post_list_link      = $permalink . 'view/' . $args['form_slug'] . '/';
					$json_array['form_notice'] = buddyforms_after_save_post_redirect( $post_list_link );
					$json_array['form_notice'] .= $display_message;
					break;
				case 'display_message':
					$json_array['form_remove'] = 'true';
					$json_array['form_notice'] = $display_message;
					break;
				default:
					if ( isset( $args['post_id'] ) ) {
						$json_array['post_id'] = $args['post_id'];
					}
					if ( isset( $args['post_title'] ) ) {
						$json_array['buddyforms_form_title'] = $args['post_title'];
					}
					if ( isset( $args['revision_id'] ) ) {
						$json_array['revision_id'] = $args['revision_id'];
					}
					if ( isset( $args['post_parent'] ) ) {
						$json_array['post_parent'] = $args['post_parent'];
					}
					if ( isset( $args['form_notice'] ) ) {
						$json_array['form_notice'] = $args['form_notice'];
					}
					break;
			}
		}

	}

	$json_array = apply_filters( 'buddyforms_ajax_process_edit_post_json_response', $json_array );

	echo json_encode( $json_array );

	die();
}

add_action( 'wp_ajax_buddyforms_ajax_delete_post', 'buddyforms_ajax_delete_post' );
//add_action('wp_ajax_nopriv_buddyforms_ajax_delete_post', 'buddyforms_ajax_delete_post');
function buddyforms_ajax_delete_post() {
	global $current_user, $buddyforms;
	$current_user = wp_get_current_user();

	$post_id  = intval( $_POST['post_id'] );
	$the_post = get_post( $post_id );

	$form_slug = get_post_meta( $post_id, '_bf_form_slug', true );
	if ( ! $form_slug ) {
		_e( 'You are not allowed to delete this entry! What are you doing here?', 'buddyforms' );
		die();
	}

	//Delete Files from server option
    $buddyFData = isset( $buddyforms[ $form_slug ]['form_fields'] ) ? $buddyforms[ $form_slug ]['form_fields'] : [];
    foreach ( $buddyFData as $key => $value ) {

        $field = $value['slug'];
        $type  = $value['type'];
        if ( $type == 'upload' ) {
            //Check if the option Delete Files When Remove Entry is ON.
            $can_delete_files = isset( $value['delete_files'] ) ? true : false;
            if ( $can_delete_files ) {
                // If true then Delete the files attached to the entry
                $column_val   = get_post_meta( $post_id, $field, true );
                if(!empty($column_val)){
                    $attachmet_id = explode( ",", $column_val );
                    foreach ( $attachmet_id as $id ) {
                        wp_delete_attachment( $id, true );
                    }

                }

            }

        }
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

	if ( bf_user_can( $current_user->ID, 'buddyforms_' . $form_slug . '_delete', array(), $form_slug ) ) {
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
