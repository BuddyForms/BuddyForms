<?php

add_filter( 'authenticate', 'buddyforms_auth_signon', 999, 3 );
function buddyforms_auth_signon( $user, $username, $password ) {

	$username = sanitize_user($username);
	$password = trim($password);

	$user = apply_filters('authenticate', null, $username, $password);

	if ( $user == null ) {
		$user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
	} elseif ( get_user_meta( $user->ID, 'has_to_be_activated', true ) != false ) {
		$user = new WP_Error('activation_failed', __('<strong>ERROR</strong>: User is not activated.'));
	}

	$ignore_codes = array('empty_username', 'empty_password');

	if (is_wp_error($user) && !in_array($user->get_error_code(), $ignore_codes) ) {
		do_action('wp_login_failed', $username);
	}

	return $user;
}

add_action( 'template_redirect', 'buddyforms_activate_user' );
function buddyforms_activate_user() {
	global $buddyforms;

	if(!is_page()){
		return;
	}

	if( !isset( $_GET['form_slug'] ) ){
		return;
	}

	if( !isset( $buddyforms[$_GET['form_slug']]['registration']['activation_page'] ) ){
		return;
	}

	if ( get_the_ID() == $buddyforms[$_GET['form_slug']]['registration']['activation_page'] ) {
		$user_id = filter_input( INPUT_GET, 'user', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
		if ( $user_id ) {
			// get user meta activation hash field
			$code = get_user_meta( $user_id, 'has_to_be_activated', true );
			if ( $code == filter_input( INPUT_GET, 'key' ) ) {
				delete_user_meta( $user_id, 'has_to_be_activated' );
			}
		}
	}
}