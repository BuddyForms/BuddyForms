<?php

// register a new user
function buddyforms_wp_insert_user() {
	global $buddyforms, $form_slug;

	$hasError = false;

	if ( isset( $_POST["user_email"] ) ) {

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

		// invalid email?
		if(!is_email($user_email)) {
			$hasError = true;
			Form::setError('buddyforms_form_' . $form_slug, '<span data-field-id="user_email"></span>' . __('Error: Invalid email', 'buddyforms') );
		}
		// Email address already registered?
		if(email_exists($user_email)) {
			$hasError = true;
			Form::setError('buddyforms_form_' . $form_slug, '<span data-field-id="user_email"></span>' . __('Error: Email already registered', 'buddyforms') );
		}

		if( isset( $buddyforms[$form_slug]['public_submit_username_from_email'] ) ){
			$user_login = explode( '@', $user_email );
			$user_login = $user_login[0];
		}

		// Username already registered?
		if(username_exists($user_login)) {
			$hasError = true;
			Form::setError('buddyforms_form_' . $form_slug, '<span data-field-id="user_login"></span>' . __('Error: Username already taken', 'buddyforms') );
		}
		// invalid username?
		if(!validate_username($user_login)) {
			$hasError = true;
			Form::setError('buddyforms_form_' . $form_slug, '<span data-field-id="user_login"></span>' . __('Error: Invalid username', 'buddyforms') );
		}
		// empty username?
		if($user_login == '') {
			$hasError = true;
			Form::setError('buddyforms_form_' . $form_slug, '<span data-field-id="user_login"></span>' . __('Error: Please enter a username', 'buddyforms') );
		}

		if($user_pass == '') {
			// Generate the password if generate_password is set
			if( isset( $buddyforms[$form_slug]['registration']['generate_password'] ) ){
				$user_pass = $pass_confirm = wp_generate_password( 12, true );
			} else {
				$hasError = true;
				Form::setError('buddyforms_form_' . $form_slug, '<span data-field-id="user_pass"></span>' . __('Error: Please enter a password', 'buddyforms') );
			}

		}
		// passwords do not match?
		if($user_pass != $pass_confirm) {
			$hasError = true;
			Form::setError('buddyforms_form_' . $form_slug, '<span data-field-id="user_pass"></span>' . __('Error: Passwords do not match', 'buddyforms') );
		}

	} else {
		// General error message that one of the required field sis missing
		$hasError = true;
		Form::setError('buddyforms_form_' . $form_slug,  __('Error: eMail Address is a required fields. You need to add the email address field to the form.', 'buddyforms') );
	}

	// Let us check if we run into any error.

	$user_role = isset( $buddyforms[$form_slug]['registration']['new_user_role'] ) ? $buddyforms[$form_slug]['registration']['new_user_role'] : 'subscriber';

	// only create the user in if there are no errors
	if(!$hasError) {

		$new_user_id = wp_insert_user(array(
				'user_login'		=> $user_login,
				'user_pass'	 		=> $user_pass,
				'user_email'		=> $user_email,
				'first_name'		=> $user_first,
				'last_name'			=> $user_last,
				'user_registered'	=> date('Y-m-d H:i:s'),
				'role'				=> $user_role,
				'user_url'			=> $user_url,
				'description'		=> $description
			)
		);

		// Add the user to the current blog
		add_user_to_blog( get_current_blog_id(), $new_user_id, $user_role );

		// if multisite is enabled we need to make sure the user will become a member of the form blog id
		if( buddyforms_is_multisite() ) {
			add_user_to_blog( $buddyforms[$form_slug]['blog_id'], $new_user_id, $user_role );
		}

		if ( $new_user_id && !is_wp_error( $new_user_id ) ) {
			$code = sha1( $new_user_id . time() );

			$activation_page = get_permalink( $buddyforms[$form_slug]['registration']['activation_page'] );
			$activation_link = add_query_arg( array( 'key' => $code, 'user' => $new_user_id, 'form_slug' => $form_slug ), $activation_page);

			add_user_meta( $new_user_id, 'has_to_be_activated', $code, true );

			// send an activation link to the user asking them to activate there account
			buddyforms_activate_account_mail( $activation_link );

			// send an email to the admin alerting them of the registration
			wp_new_user_notification($new_user_id);

		}

		return $new_user_id;
	}
	return false;
}

// used for tracking error messages
function buddyforms_errors(){
	static $wp_error; // Will hold global variable safely
	return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}

function buddyforms_activate_account_mail( $activation_link ) {
	global $form_slug, $buddyforms;


	$blog_title  = get_bloginfo( 'name' );
	$siteurl     = get_bloginfo( 'wpurl' );
	$siteurlhtml = "<a href='$siteurl' target='_blank' >$siteurl</a>";
	$admin_email = get_option( 'admin_email' );


	$subject    = isset($buddyforms[$form_slug]['registration']['activation_message_from_subject']) ? $buddyforms[$form_slug]['registration']['activation_message_from_subject'] : '';
	$emailBody  = isset($buddyforms[$form_slug]['registration']['activation_message_text']) ? $buddyforms[$form_slug]['registration']['activation_message_text'] : '';

	$from_name  = isset($buddyforms[$form_slug]['registration']['activation_message_from_name']) ? $buddyforms[$form_slug]['registration']['activation_message_from_name'] : '';
	$from_name = str_replace( '[blog_title]', $blog_title, $from_name );

	$from_email = isset($buddyforms[$form_slug]['registration']['activation_message_from_email']) ? $buddyforms[$form_slug]['registration']['activation_message_from_email'] : '';
	$from_email = str_replace( '[admin_email]', $admin_email, $from_email );

	$user_email = isset( $_POST["user_email"] ) ? $_POST["user_email"] : '';



	$emailBody = str_replace( '[activation_link]', $activation_link, $emailBody );
	$emailBody = str_replace( '[blog_title]', $blog_title, $emailBody );
	$emailBody = str_replace( '[siteurl]', $siteurl, $emailBody );
	$emailBody = str_replace( '[siteurlhtml]', $siteurlhtml, $emailBody );
	$emailBody = str_replace( '[admin_email]', $admin_email, $emailBody );

	if(!$user_email)
		return;

	if( isset( $buddyforms[ $form_slug ][ 'form_fields' ] ) ) {
		foreach( $buddyforms[ $form_slug ][ 'form_fields' ] as $field_key => $field ) {
			if( isset( $_POST[ $field[ 'slug' ] ] ) ){
				$emailBody  = str_replace( '[' . $field[ 'slug' ] . ']', $_POST[ $field[ 'slug' ] ], $emailBody );
			}
		}
	}

	$mailheaders  = "MIME-Version: 1.0\n";
	$mailheaders .= "X-Priority: 1\n";
	$mailheaders .= "Content-Type: text/html; charset=\"UTF-8\"\n";
	$mailheaders .= "Content-Transfer-Encoding: 7bit\n\n";

	$mailheaders .= "From: $from_name <$from_email>" . "\r\n";
	$message = '<html><head></head><body>' . $emailBody . '</body></html>';

	$wp_mail = wp_mail( $user_email , $subject, $message, $mailheaders );

}

add_filter( 'authenticate', 'buddyforms_auth_signon', 999, 3 );
function buddyforms_auth_signon( $user, $username, $password ) {

	if (is_wp_error($user) ) {
		return $user;
	}

	if( !isset($user->ID) ){
		return $user;
	}

	if ( get_user_meta( $user->ID, 'has_to_be_activated', true ) != false ) {
		$user = new WP_Error('activation_failed', __('<strong>ERROR</strong>: User is not activated.'));
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