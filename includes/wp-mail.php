<?php

add_action('buddyforms_process_post_end', 'mail_submission_trigger_sent');
/**
 * @param $args
 */
function mail_submission_trigger_sent($args){
	global $form_slug, $buddyforms;

	$form_slug = $args['form_slug'];
	$post_id = $args['post_id'];

	$post = get_post($post_id);

	if( $buddyforms[$form_slug]['mail_submissions'] ){
		foreach( $buddyforms[$form_slug]['mail_submissions'] as $key => $notification ){
			buddyforms_send_mail_submissions( $notification, $post );
		}
	}

}

/**
 * @param $notification
 * @param $post
 */
function buddyforms_send_mail_submissions( $notification, $post ) {
	global $form_slug, $buddyforms;

	$pub_post = $post;
	$post_ID  = $post->ID;

	$author_id  = $pub_post->post_author;
	$post_title = $pub_post->post_title;
	$postperma  = get_permalink( $post_ID );
	$user_info  = get_userdata( $author_id );

	$usernameauth  = $user_info->user_login;
	$user_nicename = $user_info->user_nicename;

	$mail_notification_trigger = $notification;

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

			$user_email =  isset($_POST['user_email']) ? $_POST['user_email'] : '';
			$mail_address  = str_replace( '[user_email]', $user_email, $mail_address );

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
	if(isset($buddyforms[$form_slug]['form_fields'])){
		foreach($buddyforms[$form_slug]['form_fields'] as $field_id => $field){
			$field_value =  isset($_POST[$field['slug']]) ? $_POST[$field['slug']] : '';
			$emailBody  = str_replace( '[' . $field['slug'] . ']', $field_value, $emailBody );
		}
	}
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

	$emailBody = stripslashes( htmlspecialchars_decode( $emailBody ) );

	$mailheaders  = "MIME-Version: 1.0\n";
	$mailheaders .= "X-Priority: 1\n";
	$mailheaders .= "Content-Type: text/html; charset=\"UTF-8\"\n";
	$mailheaders .= "Content-Transfer-Encoding: 7bit\n\n";
	$mailheaders .= "From: $from_name <$from_email>" . "\r\n";
	$message = '<html><head></head><body>' . $emailBody . '</body></html>';

	wp_mail( $mail_to, $subject, $message, $mailheaders );

}


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

	buddyforms_send_email_notification( $post );

}

/**
 * @param $post
 */
function buddyforms_send_email_notification( $post ) {

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

	$emailBody = stripslashes( htmlspecialchars_decode( $emailBody ) );

	$mailheaders .= "MIME-Version: 1.0\n";
	$mailheaders .= "X-Priority: 1\n";
	$mailheaders .= "Content-Type: text/html; charset=\"UTF-8\"\n";
	$mailheaders .= "Content-Transfer-Encoding: 7bit\n\n";
	$mailheaders .= "From: $from_name <$from_email>" . "\r\n";
	$message = '<html><head></head><body>' . $emailBody . '</body></html>';

	wp_mail( $mail_to, $subject, $message, $mailheaders );

}