<?php

function buddyforms_form_setup_nav_li_registration() { ?>
    <li class="registrations_nav"><a
            href="#registration"
            data-toggle="tab"><?php _e( 'Registration', 'buddyforms' ); ?></a>
    </li><?php
}

add_action( 'buddyforms_form_setup_nav_li_last', 'buddyforms_form_setup_nav_li_registration', 50 );

function buddyforms_form_setup_tab_pane_registration() { ?>
    <div class="tab-pane fade in" id="registration">
    <div class="buddyforms_accordion_registration">
		<?php buddyforms_registration_screen() ?>
    </div>
    </div><?php
}

add_action( 'buddyforms_form_setup_tab_pane_last', 'buddyforms_form_setup_tab_pane_registration' );

function buddyforms_registration_screen() {
	global $post, $buddyform;

	$form_setup = array();

	if ( ! $buddyform ) {
		$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true );
	}

	echo '<h4>' . __( 'Registration Options', 'buddyforms' ) . '</h4><br>';

	$generate_password = isset( $buddyform['registration']['generate_password'] ) ? $buddyform['registration']['generate_password'] : '';
	$element           = new Element_Checkbox( '<b>' . __( 'Generate Password', 'buddyforms' ) . '</b>', "buddyforms_options[registration][generate_password]", array( 'yes' => __( 'Auto generate the password.', 'buddyforms' ) ), array(
		'value'     => $generate_password,
		'shortDesc' => 'If generate password is enabled the password field is not required and can be removed from the form. How ever if the password field exist and a passowrd was entered the password from the password field is used instad of the auto generated password.'
	) );
	if ( buddyforms_core_fs()->is_not_paying() ) {
		$element->setAttribute( 'disabled', 'disabled' );
	}
	$form_setup[] = $element;

	// Generate Username ?
	$public_submit_username_from_email = ! isset( $buddyform['public_submit_username_from_email'] ) ? '' : 'public_submit_username_from_email';
	$element                           = new Element_Checkbox( '<b>' . __( 'Automatically generate username from eMail', 'buddyforms' ) . '</b>', "buddyforms_options[public_submit_username_from_email]", array( 'public_submit_username_from_email' => __( 'Generate Username from eMail', 'buddyforms' ) ), array(
		'value'     => $public_submit_username_from_email,
		'shortDesc' => 'This option only works with the eMail Form Element added to the Form. Please make sure you have the User eMail form element added to the form.'
	) );
	if ( buddyforms_core_fs()->is_not_paying() ) {
		$element->setAttribute( 'disabled', 'disabled' );
	}
	$form_setup[] = $element;

	// Get all Pages
	$pages = get_pages( array(
		'sort_order'  => 'asc',
		'sort_column' => 'post_title',
		'parent'      => 0,
		'post_type'   => 'page',
		'post_status' => 'publish'
	) );

	// Generate the pages Array
	$all_pages             = Array();
	$all_pages['referrer'] = 'Select a Page';
	$all_pages['referrer'] = 'Referrer';
	$all_pages['home']     = 'Homepage';
	foreach ( $pages as $page ) {
		$all_pages[ $page->ID ] = $page->post_title;
	}

	$activation_page = isset( $buddyform['registration']['activation_page'] ) ? $buddyform['registration']['activation_page'] : 'none';

	// Activation Page
	$form_setup[] = new Element_Select( '<b>' . __( "Activation Page", 'buddyforms' ) . '</b>', "buddyforms_options[registration][activation_page]", $all_pages, array(
		'value'     => $activation_page,
		'shortDesc' => __( 'Select the page the user should land on if he clicks the activation link in the activation email.', 'buddyforms' ),
		'class'     => '',
	) );

	// activation_message_from_subject
	$activation_message_from_subject = isset( $buddyform['registration']['activation_message_from_subject'] ) ? $buddyform['registration']['activation_message_from_subject'] : 'User Account Activation Mail';
	$form_setup[]                    = new Element_Textbox( '<b>' . __( "Activation Message Subject", 'buddyforms' ) . '</b>', "buddyforms_options[registration][activation_message_from_subject]", array(
		'value'     => $activation_message_from_subject,
		'shortDesc' => __( '', 'buddyforms' ),
		'class'     => '',
	) );
	// activation_message_text
	$activation_message_text = isset( $buddyform['registration']['activation_message_text'] )
		? $buddyform['registration']['activation_message_text']
		: 'Hi [user_login],
			Great to see you come on board! Just one small step left to make your registration complete.
			<br>
			<b>Click the link below to activate your account.</b>
			<br>
			[activation_link]
			<br><br>
			[blog_title]
		';
	$form_setup[]            = new Element_Textarea( '<b>' . __( "Activation Message Text", 'buddyforms' ) . '</b>', "buddyforms_options[registration][activation_message_text]", array(
		'value'     => $activation_message_text,
		'shortDesc' => __( '', 'buddyforms' ),
		'class'     => '',
		'style'     => 'width: 100%; display: inline-block;'
	) );
	// activation_message_from_name
	$activation_message_from_name = isset( $buddyform['registration']['activation_message_from_name'] ) ? $buddyform['registration']['activation_message_from_name'] : '[blog_title]';
	$form_setup[]                 = new Element_Textbox( '<b>' . __( "Activation From Name", 'buddyforms' ) . '</b>', "buddyforms_options[registration][activation_message_from_name]", array(
		'value'     => $activation_message_from_name,
		'shortDesc' => __( '', 'buddyforms' ),
		'class'     => '',
	) );
	// activation_message_from_email
	$activation_message_from_email = isset( $buddyform['registration']['activation_message_from_email'] ) ? $buddyform['registration']['activation_message_from_email'] : '[admin_email]';
	$form_setup[]                  = new Element_Textbox( '<b>' . __( "Activation From eMail", 'buddyforms' ) . '</b>', "buddyforms_options[registration][activation_message_from_email]", array(
		'value'     => $activation_message_from_email,
		'shortDesc' => __( 'You can set the "From Email Address" to [admin_email] to use the admin Email from the general WordPress settings', 'buddyforms' ),
		'class'     => '',
	) );

//	$auto_loggin = '';
//	if ( isset( $buddyform['registration']['auto_loggin'] ) ) {
//		$auto_loggin = $buddyform['registration']['auto_loggin'];
//	}
//	$form_setup[] = new Element_Checkbox( '<b>' . __( 'Auto Loggin', 'buddyforms' ) . '</b>', "buddyforms_options[registration][auto_loggin]", array( 'yes' => __( 'Auto loggin the user.', 'buddyforms' ) ), array( 'value' => $auto_loggin, 'shortDesc' => 'Make sure you have the recharter form element...' ) );

	$new_user_role = isset( $buddyform['registration']['new_user_role'] ) ? $buddyform['registration']['new_user_role'] : 'subscriber';
	$roles_select  = array();

	foreach ( get_editable_roles() as $role_name => $role_info ) {
		$roles_select[ $role_name ] = $role_name;
	}

	// User Role
	$form_setup[] = new Element_Select( '<b>' . __( "New User Role", 'buddyforms' ) . '</b>', "buddyforms_options[registration][new_user_role]", $roles_select, array(
		'value'     => $new_user_role,
		'shortDesc' => __( 'Select the User Role the user should have after successful registration', 'buddyforms' ),
		'class'     => ''
	) );

	buddyforms_display_field_group_table( $form_setup );
}