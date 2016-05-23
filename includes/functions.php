<?php

/**
 * Add the forms to the admin bar
 *
 * @package BuddyForms
 * @since 0.3 beta
 */
add_action( 'wp_before_admin_bar_render', 'buddyforms_wp_before_admin_bar_render', 1, 2 );
function buddyforms_wp_before_admin_bar_render() {
	global $wp_admin_bar, $buddyforms;

	if ( ! $buddyforms ) {
		return;
	}

	foreach ( $buddyforms as $key => $buddyform ) {

		if ( ! isset( $buddyform['post_type'] ) || $buddyform['post_type'] == 'none' ) {
			continue;
		}

		if ( isset( $buddyform['admin_bar'][0] ) && $buddyform['post_type'] != 'none' && ! empty( $buddyform['attached_page'] ) ) {

			if ( current_user_can( 'buddyforms_' . $key . '_create' ) ) {
				$permalink = get_permalink( $buddyform['attached_page'] );
				$wp_admin_bar->add_menu( array(
					'parent' => 'my-account',
					'id'     => 'my-account-' . $buddyform['slug'],
					'title'  => $buddyform['name'],
					'href'   => $permalink
				) );
				$wp_admin_bar->add_menu( array(
					'parent' => 'my-account-' . $buddyform['slug'],
					'id'     => 'my-account-' . $buddyform['slug'] . '-view',
					'title'  => __( 'View my ', 'buddyforms' ) . $buddyform['name'],
					'href'   => $permalink . '/view/' . $buddyform['slug'] . '/'
				) );

				$wp_admin_bar->add_menu( array(
					'parent' => 'my-account-' . $buddyform['slug'],
					'id'     => 'my-account-' . $buddyform['slug'] . '-new',
					'title'  => __( 'New ', 'buddyforms' ) . $buddyform['singular_name'],
					'href'   => $permalink . 'create/' . $buddyform['slug'] . '/'
				) );

			}
		}
	}
}

// Create the buddyforms post status array.
// Other Plugins use the filter bf_get_post_status_array to add there post status to the options array
function bf_get_post_status_array( $select_condition = false ) {

	$status_array = array(
		'publish' => __( 'Published', 'buddyforms' ),
		'pending' => __( 'Pending Review', 'buddyforms' ),
		'draft'   => __( 'Draft', 'buddyforms' ),
		'future'  => __( 'Scheduled', 'buddyforms' ),
		'private' => __( 'Privately Published', 'buddyforms' ),
		'trash'   => __( 'Trash', 'buddyforms' ),
	);

	return apply_filters( 'bf_get_post_status_array', $status_array );
}

/**
 * Restricting users to view only media library items they upload.
 *
 * @package BuddyForms
 * @since 0.5 beta
 */
add_action( 'pre_get_posts', 'buddyforms_restrict_media_library' );
function buddyforms_restrict_media_library( $wp_query_obj ) {
	global $current_user, $pagenow;

	if ( is_super_admin( $current_user->ID ) ) {
		return;
	}

	if ( ! is_a( $current_user, 'WP_User' ) ) {
		return;
	}

	if ( 'admin-ajax.php' != $pagenow || $_REQUEST['action'] != 'query-attachments' ) {
		return;
	}

	if ( ! current_user_can( 'manage_media_library' ) ) {
		$wp_query_obj->set( 'author', $current_user->ID );
	}

	return;
}

/**
 * Check if a subscriber have the needed rights to upload images and add this capabilities if needed.
 *
 * @package BuddyForms
 * @since 0.5 beta
 */
add_action( 'init', 'buddyforms_allow_subscriber_uploads' );
function buddyforms_allow_subscriber_uploads() {

	if ( current_user_can( 'subscriber' ) && ! current_user_can( 'upload_files' ) ) {
		$contributor = get_role( 'subscriber' );

		$contributor->add_cap( 'upload_files' );
	}

}

/**
 * Get the BuddyForms template directory.
 *
 * @package BuddyForms
 * @since 0.1 beta
 *
 * @uses apply_filters()
 * @return string
 */
function buddyforms_get_template_directory() {
	return apply_filters( 'buddyforms_get_template_directory', constant( 'BUDDYFORMS_TEMPLATE_PATH' ) );
}

/**
 * Locate a template
 *
 * @package BuddyForms
 * @since 0.1 beta
 */
function buddyforms_locate_template( $file ) {
	if ( locate_template( array( $file ), false ) ) {
		locate_template( array( $file ), true );
	} else {
		include( BUDDYFORMS_TEMPLATE_PATH . $file );
	}
}

// Display the WordPress Login Form
function buddyforms_login_form() {
	// Get The Login Form
	echo buddyforms_get_login_form();
}

// Create the BuddyForms Login Form
function buddyforms_get_login_form() {
	$wp_login_form = '<h3>' . __( 'You need to be logged in to use this Form', 'buddyforms' ) . '</h3>';
	$wp_login_form .= wp_login_form( array( 'echo' => false ) );
	$wp_login_form = apply_filters( 'buddyforms_wp_login_form', $wp_login_form );

	return $wp_login_form;
}

// Helper Function to get the Get the REQUEST_URI Vars
function bf_get_url_var( $name ) {
	$strURL  = $_SERVER['REQUEST_URI'];
	$arrVals = explode( "/", $strURL );
	$found   = 0;
	foreach ( $arrVals as $index => $value ) {
		if ( $value == $name ) {
			$found = $index;
		}
	}
	$place = $found + 1;

	return ( $found == 0 ) ? 1 : $arrVals[ $place ];
}


function contact_form_submission_no_user_can_submit($user_can_edit){
	global $buddyforms;

	//if($buddyforms)
	return true;
}
//add_filter('buddyforms_user_can_edit', 'contact_form_submission_no_user_can_submit', 999, 1);

function display_comment_recaptcha($form, $form_slug, $post_id) {

	$form->addElement( new Element_HTML('<div class="g-recaptcha" data-sitekey="' . get_option("captcha_site_key") . '"></div>
	<input name="submit" type="submit" value="Submit Comment">'));

	return $form;
}
//add_filter('buddyforms_create_edit_form_button', 'display_comment_recaptcha', 10, 3);