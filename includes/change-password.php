<?php

function buddyforms_change_password_form( $redirect_url = '' ) {
	global $post;

	if ( is_singular() ) {
		$current_url = get_permalink( $post->ID );
	} else {
		$pageURL = 'http';
		if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
			$pageURL .= 's';
		}
		$pageURL .= '://';
		if ( isset( $_SERVER['SERVER_PORT'] ) && isset( $_SERVER['SERVER_NAME'] ) && isset( $_SERVER['REQUEST_URI'] ) && $_SERVER['SERVER_PORT'] != '80' ) {
			$pageURL .= esc_url_raw( wp_unslash( $_SERVER['SERVER_NAME'] ) ) . ':' . sanitize_key( $_SERVER['SERVER_PORT'] ) . esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		} else {
			$pageURL .= esc_url_raw( wp_unslash( $_SERVER['SERVER_NAME'] ) ) . esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}
		$current_url = $pageURL;
	}
	$redirect = $current_url;

	if ( ! empty( $redirect_url ) ) {
		$redirect = $redirect_url;
	}

	ob_start();

	// show any error messages after form submission
	buddyforms_show_error_messages();

	// create the plugin template path
	$template_path = BUDDYFORMS_TEMPLATE_PATH . 'buddyforms/bf-change-password.php';

	// Check if template exist in the child or parent theme and use this path if available
	if ( $template_file = locate_template( 'buddyforms/bf-change-password.php', false, false ) ) {
		$template_path = $template_file;
	}

	// Do the include
	include $template_path;

	return ob_get_clean();
}

function buddyforms_reset_password() {
	// reset a users password
	if ( isset( $_POST['buddyforms_action'] ) && $_POST['buddyforms_action'] == 'reset-password' ) {

		global $user_ID;

		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( isset( $_POST['buddyforms_password_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['buddyforms_password_nonce'] ), 'buddyforms-password-nonce' ) ) {
			$global_error = ErrorHandler::get_instance();
			if ( ( isset( $_POST['buddyforms_user_pass'] ) && isset( $_POST['buddyforms_user_pass_confirm'] ) ) && ( $_POST['buddyforms_user_pass'] == '' || $_POST['buddyforms_user_pass_confirm'] == '' ) ) {
				// password(s) field empty
				$global_error->add_error( new BuddyForms_Error( 'buddyforms_form_password_empty', __( 'Please enter a password, and confirm it', 'buddyforms' ) ) );
			}
			if ( $_POST['buddyforms_user_pass'] != $_POST['buddyforms_user_pass_confirm'] ) {
				// passwords do not match
				$global_error->add_error( new BuddyForms_Error( 'buddyforms_form_password_mismatch', __( 'Passwords do not match', 'buddyforms' ) ) );
			}

			$global_bf_error = $global_error->get_global_error();
			if ( ! empty( $global_bf_error ) ) {
				$has_errors = $global_bf_error->has_errors();
				if ( ! $has_errors ) {
					// change the password here
					$user_data = array(
						'ID'        => $user_ID,
						'user_pass' => $_POST['buddyforms_user_pass'],
					);
					wp_update_user( $user_data );

					// send password change email here (if WP doesn't)
					if ( ! isset( $_POST['buddyforms_redirect'] ) ) {
						return;
					}
					$redirect_url = apply_filters( 'buddyforms_reset_password_redirect', esc_url_raw( wp_unslash( $_POST['buddyforms_redirect'] ) ) );

					$bf_pw_redirect_url = get_user_meta( $user_ID, 'bf_pw_redirect_url', true );

					if ( $bf_pw_redirect_url ) {
						$redirect_url = $bf_pw_redirect_url;
						delete_user_meta( $user_ID, 'bf_pw_redirect_url' );
					}

					wp_redirect( add_query_arg( 'bf-password-reset', 'true', $redirect_url ) );
					exit;
				}
			}
		}
	}
}

add_action( 'init', 'buddyforms_reset_password' );
