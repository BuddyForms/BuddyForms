<?php

add_action( 'buddyforms_process_submission_end', 'mail_submission_trigger_sent' );
/**
 * @param $args
 */
function mail_submission_trigger_sent( $args ) {
	global $form_slug, $buddyforms;

	if ( ! isset( $args['post_id'] ) ) {
		return;
	}

	$form_slug = $args['form_slug'];
	$post_id   = $args['post_id'];

	$post = get_post( $post_id );

	if ( isset( $buddyforms[ $form_slug ]['mail_submissions'] ) && is_array( $buddyforms[ $form_slug ]['mail_submissions'] ) ) {
		foreach ( $buddyforms[ $form_slug ]['mail_submissions'] as $key => $notification ) {
			buddyforms_send_mail_submissions( $notification, $post );
		}
	}

}

/**
 *
 * Sent mail notifications for contact forms
 *
 * @param $notification
 * @param $post
 */
function buddyforms_send_mail_submissions( $notification, $post ) {
	global $form_slug, $buddyforms, $post;

	$pub_post = $post;
	$post_ID  = $post->ID;

	$author_id  = $pub_post->post_author;
	$post_title = $pub_post->post_title;
	$postperma  = get_permalink( $post_ID );
	$user_info  = get_userdata( $author_id );

	$usernameauth  = $user_info->user_login;
	$user_nicename = $user_info->user_nicename;

	$mail_notification_trigger = $notification;

	$user_email = isset( $_POST['user_email'] ) ? $_POST['user_email'] : $user_info->user_email;

	$mail_to = array();

	// Check the Sent mail to checkbox
	if ( isset( $mail_notification_trigger['mail_to'] ) ) {
		foreach ( $mail_notification_trigger['mail_to'] as $key => $mail_address ) {

			if ( $mail_address == 'submitter' ) {
				array_push( $mail_to, $user_email );
			}

			if ( $mail_address == 'admin' ) {
				array_push( $mail_to, get_option( 'admin_email' ) );
			}

		}
	}

	// Check if mail to addresses
	if ( isset( $mail_notification_trigger['mail_to_address'] ) ) {

		$mail_to_address = explode( ',', str_replace( ' ', '', $mail_notification_trigger['mail_to_address'] ) );

		foreach ( $mail_to_address as $key => $mail_address ) {

			$user_email   = isset( $_POST['user_email'] ) ? $_POST['user_email'] : '';
			$mail_address = str_replace( '[user_email]', $user_email, $mail_address );

			if ( ! empty( $mail_address ) ) {
				array_push( $mail_to, $mail_address );
			}


		}
	}

	// Check if CC
	if ( isset( $mail_notification_trigger['mail_to_bcc_address'] ) ) {
		$mail_to_address = explode( ',', str_replace( ' ', '', $mail_notification_trigger['mail_to_bcc_address'] ) );
		foreach ( $mail_to_address as $key => $mail_address ) {
			if ( ! empty( $mail_address ) ) {
				array_push( $mail_to, $mail_address );
			}
		}
	}

	// Check if BCC
	if ( isset( $mail_notification_trigger['mail_to_bcc_address'] ) ) {
		$mail_to_address = explode( ',', str_replace( ' ', '', $mail_notification_trigger['mail_to_bcc_address'] ) );
		foreach ( $mail_to_address as $key => $mail_address ) {
			if ( ! empty( $mail_address ) ) {
				array_push( $mail_to, $mail_address );
			}
		}
	}

	$first_name = isset( $_POST['user_first'] ) ? $_POST['user_first'] : $user_info->user_firstname;
	$last_name  = isset( $_POST['user_last'] ) ? $_POST['user_last'] : $user_info->user_lastname;

	$blog_title  = get_bloginfo( 'name' );
	$siteurl     = get_bloginfo( 'wpurl' );
	$siteurlhtml = "<a href='$siteurl' target='_blank' >$siteurl</a>";

	$subject = isset( $_POST['subject'] ) ? $_POST['subject'] : $mail_notification_trigger['mail_subject'];


	$from_name = isset( $mail_notification_trigger['mail_from_name'] ) ? $mail_notification_trigger['mail_from_name'] : 'blog_title';

	switch ( $from_name ) {
		case 'user_login':
			$from_name = $usernameauth;
			break;
		case 'user_first':
			$from_name = $first_name;
			break;
		case 'user_last':
			$from_name = $last_name;
			break;
		case 'user_first_last':
			$from_name = $first_name . ' ' . $last_name;
			break;
		case 'custom':
			$from_name = $mail_notification_trigger['mail_from_name_custom'];
			break;
		default:
			$from_name = $blog_title;
			break;
	}


	$from_email = isset( $mail_notification_trigger['mail_from'] ) ? $mail_notification_trigger['mail_from'] : 'admin';

	switch ( $from_email ) {
		case 'submitter':
			$from_email = $user_email;
			break;
		case 'admin':
			$from_email = get_option( 'admin_email' );
			break;
		case 'custom':
			$from_email = isset( $mail_notification_trigger['mail_from_custom'] ) ? $mail_notification_trigger['mail_from_custom'] : $from_email;
			break;
		default:
			$from_email = $user_email;
			break;
	}

	$emailBody = isset( $_POST['message'] ) ? $_POST['message'] : $mail_notification_trigger['mail_body'];

	// If we have content let us check if there are any tags we need to replace with the correct values.
	if ( ! empty( $emailBody ) ) {

		$emailBody = stripslashes( $emailBody );
		if ( isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {
			foreach ( $buddyforms[ $form_slug ]['form_fields'] as $field_id => $field ) {

				$value = isset( $_POST[ $field['slug'] ] ) ? $_POST[ $field['slug'] ] : '';

				// Check if is array
				if ( is_array( $value ) ) {
					$field_value = implode( ',', $value );
				} else {
					$field_value = $value;
				}


				switch ( $field['type'] ) {
					case 'taxonomy':
						if ( is_array( $value ) ) {
							foreach ( $value as $cat ) {
								$term    = get_term( $cat, $field['taxonomy'] );
								$terms[] = $term->name;
							}
							$field_value = implode( ',', $terms );
						} else {
							$term        = get_term( $value, $field['taxonomy'] );
							$field_value = $term->name;
						}
						break;
					case 'link':
						$field_value = "<p><a href='" . $value . "' " . $field['name'] . ">" . $value . " </a></p>";
						break;
					case 'user_website':
						$field_value = "<p><a href='" . $value . "' " . $field['name'] . ">" . $value . " </a></p>";
						break;
				}

				// Replace From name Shortcodes with form element values
				$from_name = str_replace( '[' . $field['slug'] . ']', $field_value, $from_name );

				// Replace Buddytext Shortcodes with form element values
				$emailBody = str_replace( '[' . $field['slug'] . ']', $field_value, $emailBody );
			}
		}

		$emailBody = str_replace( '[user_login]', $usernameauth, $emailBody );
		$emailBody = str_replace( '[user_nicename]', $user_nicename, $emailBody );
		$emailBody = str_replace( '[user_email]', $user_email, $emailBody );
		$emailBody = str_replace( '[first_name]', $first_name, $emailBody );
		$emailBody = str_replace( '[last_name]', $last_name, $emailBody );

		$emailBody = str_replace( '[published_post_link_plain]', $postperma, $emailBody );

		$postlinkhtml = "<a href='$postperma' target='_blank'>$postperma</a>";

		$emailBody = str_replace( '[published_post_link_html]', $postlinkhtml, $emailBody );

		$emailBody = str_replace( '[published_post_title]', $post_title, $emailBody );
		$emailBody = str_replace( '[site_name]', $blog_title, $emailBody );
		$emailBody = str_replace( '[site_url]', $siteurl, $emailBody );
		$emailBody = str_replace( '[site_url_html]', $siteurlhtml, $emailBody );

		$emailBody = str_replace( '[form_elements_table]', buddyforms_mail_notification_form_elements_as_table( $form_slug ), $emailBody );

		$emailBody = nl2br( htmlspecialchars( $emailBody ) );
	}

	// If we do not have any valid eMail Body let us try to create the content from teh from elements as table
	if ( empty( $emailBody ) ) {
		if ( isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {
			$emailBody = buddyforms_mail_notification_form_elements_as_table( $form_slug );
		}
	}

	// Create the email header
	$mailheader = "MIME-Version: 1.0\n";
	$mailheader .= "X-Priority: 1\n";
	$mailheader .= "Content-Type: text/html; charset=\"UTF-8\"\n";
	$mailheader .= "Content-Transfer-Encoding: 7bit\n\n";
	$mailheader .= "From: $from_name <$from_email>" . "\r\n";
	$message    = '<html><head></head><body>' . $emailBody . '</body></html>';

	// OK Let us sent the mail
	wp_mail( $mail_to, $subject, $message, $mailheader );
}

/**
 *
 *  Lets us check for post status change and sent notifications if on transition_post_status
 *
 * @param $new_status
 * @param $old_status
 * @param $post
 */
add_action( 'transition_post_status', 'buddyforms_transition_post_status', 10, 3 );
/**
 * @param $new_status
 * @param $old_status
 * @param $post
 */
function buddyforms_transition_post_status( $new_status, $old_status, $post ) {
	global $form_slug, $buddyforms;

	if ( empty( $form_slug ) ) {
		$form_slug = get_post_meta( $post->ID, '_bf_form_slug', true );
	}

	if ( empty( $form_slug ) ) {
		return;
	}

	if ( ! isset( $buddyforms[ $form_slug ]['mail_notification'][ $new_status ] ) ) {
		return;
	}

	buddyforms_send_post_status_change_notification( $post );

}

/**
 *
 * Create the mail content and sent it with wp_mail
 *
 * @param $post
 */
function buddyforms_send_post_status_change_notification( $post ) {

	global $form_slug, $buddyforms;

	$pub_post = $post;
	$post_ID  = $post->ID;

	$author_id  = $pub_post->post_author;
	$post_title = $pub_post->post_title;
	$postperma  = get_permalink( $post_ID );
	$user_info  = get_userdata( $author_id );

	$usernameauth  = $user_info->user_login;
	$user_nicename = $user_info->user_nicename;

	$post_status = get_post_status( $post_ID );

	$mail_notification_trigger = $buddyforms[ $form_slug ]['mail_notification'][ $post_status ];

	$user_email = $user_info->user_email;

	$mail_to = array();

	if ( isset( $mail_notification_trigger['mail_to'] ) ) {
		foreach ( $mail_notification_trigger['mail_to'] as $key => $mail_address ) {

			if ( $mail_address == 'author' ) {
				array_push( $mail_to, $user_email );
			}

			if ( $mail_address == 'admin' ) {
				array_push( $mail_to, get_option( 'admin_email' ) );
			}

		}
	}

	if ( isset( $mail_notification_trigger['mail_to_address'] ) ) {

		$mail_to_address = explode( ',', str_replace( ' ', '', $mail_notification_trigger['mail_to_address'] ) );

		foreach ( $mail_to_address as $key => $mail_address ) {

			array_push( $mail_to, $mail_address );

		}
	}

	$first_name = $user_info->user_firstname;
	$last_name  = $user_info->user_lastname;

	$blog_title  = get_bloginfo( 'name' );
	$siteurl     = get_bloginfo( 'wpurl' );
	$siteurlhtml = "<a href='$siteurl' target='_blank' >$siteurl</a>";

	$subject    = $mail_notification_trigger['mail_subject'];
	$from_name  = $mail_notification_trigger['mail_from_name'];
	$from_email = $mail_notification_trigger['mail_from'];
	$emailBody  = $mail_notification_trigger['mail_body'];
	$emailBody  = stripslashes( $emailBody );
	$emailBody  = str_replace( '[user_login]', $usernameauth, $emailBody );
	$emailBody  = str_replace( '[user_nicename]', $user_nicename, $emailBody );
	$emailBody  = str_replace( '[user_email]', $user_email, $emailBody );
	$emailBody  = str_replace( '[first_name]', $first_name, $emailBody );
	$emailBody  = str_replace( '[last_name]', $last_name, $emailBody );

	$emailBody = str_replace( '[published_post_link_plain]', $postperma, $emailBody );

	$postlinkhtml = "<a href='$postperma' target='_blank'>$postperma</a>";

	$emailBody = str_replace( '[published_post_link_html]', $postlinkhtml, $emailBody );

	$emailBody = str_replace( '[published_post_title]', $post_title, $emailBody );
	$emailBody = str_replace( '[site_name]', $blog_title, $emailBody );
	$emailBody = str_replace( '[site_url]', $siteurl, $emailBody );
	$emailBody = str_replace( '[site_url_html]', $siteurlhtml, $emailBody );

	//$emailBody = nl2br( htmlspecialchars( $emailBody ) ); todo: find better solution

	$mailheader .= "MIME-Version: 1.0\n";
	$mailheader .= "X-Priority: 1\n";
	$mailheader .= "Content-Type: text/html; charset=\"UTF-8\"\n";
	$mailheader .= "Content-Transfer-Encoding: 7bit\n\n";
	$mailheader .= "From: $from_name <$from_email>" . "\r\n";
	$message    = '<html><head></head><body>' . $emailBody . '</body></html>';

	wp_mail( $mail_to, $subject, $message, $mailheader );

}


/**
 * @param $form_slug
 *
 * @return string
 */
function buddyforms_mail_notification_form_elements_as_table( $form_slug ) {
	global $buddyforms, $post;
	$striped_c = 0;

	// Table start
	$message = '<table rules="all" style="border-color: #666;" cellpadding="10">';
	// Loop all form elements and add as table row
	foreach ( $buddyforms[ $form_slug ]['form_fields'] as $key => $field ) {

		$value = isset( $_POST[ $field['slug'] ] ) ? $_POST[ $field['slug'] ] : '';

		// Check if is array
		if ( is_array( $value ) ) {
			$field_value = implode( ',', $value );
		} else {
			$field_value = $value;
		}

		switch ( $field['type'] ) {
			case 'taxonomy':
				if ( is_array( $value ) ) {
					foreach ( $value as $cat ) {
						$term    = get_term( $cat, $field['taxonomy'] );
						$terms[] = $term->name;
					}
					$field_value = implode( ',', $terms );
				} else {
					$term        = get_term( $value, $field['taxonomy'] );
					$field_value = $term->name;
				}
				break;
			case 'link':
				$field_value = "<p><a href='" . $value . "' " . $field['name'] . ">" . $value . " </a></p>";
				break;
			case 'user_website':
				$field_value = "<p><a href='" . $value . "' " . $field['name'] . ">" . $value . " </a></p>";
				break;
		}

		$striped = ( $striped_c ++ % 2 == 1 ) ? "style='background: #eee;'" : '';
		// Check if the form element exist and have is not empty.
		if ( isset( $_POST[ $field['slug'] ] ) && ! empty( $_POST[ $field['slug'] ] ) ) {
			$message .= "<tr " . $striped . "><td><strong>" . $field['name'] . "</strong> </td><td>" . $field_value . "</td></tr>";
		}
	}
	// Table end
	$message .= "</table>";

	// Let us return the form elements table
	return $message;
}
