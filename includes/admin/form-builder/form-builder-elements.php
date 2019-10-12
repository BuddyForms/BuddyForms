<?php

/**
 * View form fields
 *
 * @param $args
 *
 * @return string
 * @package BuddyForms
 * @since 0.1-beta
 *
 */
function buddyforms_display_form_element( $args ) {
	global $post, $buddyform;

	if ( ! $post && isset( $_POST['post_id'] ) && $_POST['post_id'] != 0 ) {
		$post = get_post( intval( $_POST['post_id'] ) );
	}
	if ( ! $buddyform && ( ! empty( $post->ID ) && $post->ID > 0 ) ) {
		$buddyform = get_post_meta( $post->ID, '_buddyforms_options', true );
	}
	if ( isset( $_POST['fieldtype'] ) ) {
		$field_type = $_POST['fieldtype'];
	}

	if ( isset( $_POST['unique'] ) ) {
		$field_unique = $_POST['unique'];
	}

	$form_slug = ( ! empty( $post->post_name ) ) ? $post->post_name : '';
	$post_type = ( ! empty( $post->post_type ) ) ? $post->post_type : '';

	if ( isset( $field_unique ) && $field_unique == 'unique' ) {
		if ( isset( $buddyform['form_fields'] ) ) {
			foreach ( $buddyform['form_fields'] as $key => $form_field ) {
				if ( $form_field['type'] == $field_type ) {
					return 'unique';
				}
			}
		}
	}

	if ( is_array( $args ) ) {
		extract( $args );
	}

	if ( ! isset( $field_id ) ) {
		$field_id = $mod5 = substr( md5( time() * rand() ), 0, 10 );
	}

	$customfield = isset( $buddyform['form_fields'][ $field_id ] ) ? $buddyform['form_fields'][ $field_id ] : array();
	$form_fields = Array();

	$required                              = isset( $customfield['required'] ) ? $customfield['required'] : 'false';
	$form_fields['validation']['required'] = new Element_Checkbox( '<b>' . __( 'Required', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][required]", array( 'required' => '<b>' . __( 'Make this field a required field', 'buddyforms' ) . '</b>' ), array(
		'value' => $required,
		'id'    => "buddyforms_options[form_fields][" . $field_id . "][required]"
	) );
	if ( buddyforms_core_fs()->is__premium_only() ) {
		if ( buddyforms_core_fs()->is_plan( 'professional' ) || buddyforms_core_fs()->is_trial() ) {
			$metabox_enabled                            = isset( $customfield['metabox_enabled'] ) ? $customfield['metabox_enabled'] : 'false';
			$form_fields['advanced']['metabox_enabled'] = new Element_Checkbox( '<b>' . __( 'Add as admin post meta box to the edit screen', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][metabox_enabled]", array( 'metabox_enabled' => '<b>' . __( 'Add this field to the MetaBox', 'buddyforms' ) . '</b>' ), array(
				'value' => $metabox_enabled,
				'id'    => "buddyforms_options[form_fields][" . $field_id . "][required]"
			) );
		}
	}


	$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : '';
	$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
		'class'    => "use_as_slug",
		'data'     => $field_id,
		'value'    => $name,
		'required' => 1
	) );

	$field_slug                      = isset( $customfield['slug'] ) ? buddyforms_sanitize_slug( $customfield['slug'] ) : $name;
	$form_fields['advanced']['slug'] = new Element_Textbox( '<b>' . __( 'Slug', 'buddyforms' ) . '</b>' . sprintf( '<small>(%s)</small>', __( 'optional', 'buddyforms' ) ), "buddyforms_options[form_fields][" . $field_id . "][slug]", array(
		'shortDesc' => __( 'Underscore before the slug like _name will create a hidden post meta field', 'buddyforms' ),
		'value'     => $field_slug,
		'required'  => 1,
		'data'      => $field_id,
		'class'     => 'slug' . $field_id
	) );

	$description                           = isset( $customfield['description'] ) ? stripslashes( $customfield['description'] ) : '';
	$form_fields['general']['description'] = new Element_Textbox( '<b>' . __( 'Description', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][description]", array( 'value' => $description ) );
	$form_fields['hidden']['type']         = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );

	$validation_error_message                              = isset( $customfield['validation_error_message'] ) ? stripcslashes( $customfield['validation_error_message'] ) : __( 'This field is required.', 'buddyforms' );
	$form_fields['validation']['validation_error_message'] = new Element_Textbox( '<b>' . __( 'Validation Error Message', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_error_message]", array( 'value' => $validation_error_message ) );

	switch ( sanitize_title( $field_type ) ) {

		case 'text':
		    $name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Text', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Name', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'value'    => $name,
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'required' => 1
			) );
			$validation_minlength                              = isset( $customfield['validation_minlength'] ) ? stripcslashes( $customfield['validation_minlength'] ) : 0;
			$form_fields['validation']['validation_minlength'] = new Element_Number( '<b>' . __( 'Validation Min Length', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_minlength]", array( 'value' => $validation_minlength,'min'=>0,'field_id'=>$field_id.'_validation_minlength','onchange'=>'bfValidateRule("'.$field_id.'","min",this,"text")' ) );

            $validation_maxlength                              = isset( $customfield['validation_maxlength'] ) ? stripcslashes( $customfield['validation_maxlength'] ) : 0;
			$form_fields['validation']['validation_maxlength'] = new Element_Number( '<b>' . __( 'Validation Max Length', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_maxlength]", array( 'value' => $validation_maxlength,'min'=>0, 'field_id'=>$field_id.'_validation_maxlength','onchange'=>'bfValidateRule("'.$field_id.'","max",this,"text")' ) );
            break;
		case 'country':
		    $name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Country', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );
			$country_json_list                      = !empty( $customfield['country_list'] ) ? stripcslashes( $customfield['country_list'] ) : wp_json_encode( Element_Country::get_country_list() );
			$form_fields['general']['country_list'] = new Element_Textarea( '<b>' . __( 'Country JSON:', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][country_list]", array(
				'value'     => $country_json_list,
				'style'     => 'resize: both; width:100%',
				'shortDesc' => __( 'This is the list of Countries with code and name. Consider the Code is used to match with the State element when they are link. Provide a valid JSON. Leave empty to reset the list. Read more in the documentation.', 'buddyforms' )
			) );
			$link_with_state                         = isset( $customfield['link_with_state'] ) ? $customfield['link_with_state'] : 'show';
			$form_fields['general']['link_with_state'] = new Element_Checkbox( '<b>' . __( 'Link with State', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][link_with_state]", array( 'link' => '<b>' . __( 'Show the State from the selected Country', 'buddyforms' ) . '</b>' ), array(
				'value'     => $link_with_state,
				'id'        => "buddyforms_options[form_fields][" . $field_id . "][link_with_state]",
				'shortDesc' => __( 'Select this option to show the State from the item selected in the Country list.', 'buddyforms' )
			) );
			break;
		case 'state':
		    $name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'State', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );
			$state_json_list                      = !empty( $customfield['state_list'] ) ? stripcslashes( $customfield['state_list'] ) : wp_json_encode( Element_State::get_state_list() );
			$form_fields['general']['state_list'] = new Element_Textarea( '<b>' . __( 'State JSON:', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][state_list]", array(
				'value'     => $state_json_list,
				'style'     => 'resize: both; width:100%',
				'shortDesc' => __( 'This is the list of State with Country code, State code and name. Provide a valid JSON. Leave empty to reset the list. Read more in the documentation.', 'buddyforms' )
			) );
			break;
		case 'subject':
			unset( $form_fields['advanced']['slug'] );

			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Subject', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );

			$form_fields['hidden']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'subject' );

			$validation_minlength                              = isset( $customfield['validation_minlength'] ) ? stripcslashes( $customfield['validation_minlength'] ) : 0;
			$form_fields['validation']['validation_minlength'] = new Element_Number( '<b>' . __( 'Validation Min Length', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_minlength]", array( 'value' => $validation_minlength ,'min'=>0,'field_id'=>$field_id.'_validation_minlength','onchange'=>'bfValidateRule("'.$field_id.'","min",this,"subject")' ) );

            $validation_maxlength                              = isset( $customfield['validation_maxlength'] ) ? stripcslashes( $customfield['validation_maxlength'] ) : 0;
			$form_fields['validation']['validation_maxlength'] = new Element_Number( '<b>' . __( 'Validation Max Length', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_maxlength]", array( 'value' => $validation_maxlength ,'min'=>0,'field_id'=>$field_id.'_validation_maxlength','onchange'=>'bfValidateRule("'.$field_id.'","max",this,"subject")' ) );
            break;
		case 'message':
			unset( $form_fields['advanced']['slug'] );

			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Message', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );

			$form_fields['hidden']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'message' );

			$validation_minlength                              = isset( $customfield['validation_minlength'] ) ? stripcslashes( $customfield['validation_minlength'] ) : 0;
			$form_fields['validation']['validation_minlength'] = new Element_Number( '<b>' . __( 'Validation Min Length', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_minlength]", array( 'value' => $validation_minlength ,'min'=>0,'field_id'=>$field_id.'_validation_minlength','onchange'=>'bfValidateRule("'.$field_id.'","min",this,"message")' ) );

            $validation_maxlength                              = isset( $customfield['validation_maxlength'] ) ? stripcslashes( $customfield['validation_maxlength'] ) : 0;
			$form_fields['validation']['validation_maxlength'] = new Element_Number( '<b>' . __( 'Validation Max Length', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_maxlength]", array( 'value' => $validation_maxlength ,'min'=>0,'field_id'=>$field_id.'_validation_maxlength','onchange'=>'bfValidateRule("'.$field_id.'","max",this,"message")' ) );
            break;
		case 'user_login':
			unset( $form_fields['advanced']['slug'] );

			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Username', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );

			$hide_if_logged_in                           = isset( $customfield['hide_if_logged_in'] ) ? $customfield['hide_if_logged_in'] : 'show';
			$form_fields['general']['hide_if_logged_in'] = new Element_Checkbox( '<b>' . __( 'Hide Username Form Element for LoggedIn User', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][hide_if_logged_in]", array( 'hide' => '<b>' . __( 'Hide for logged in user', 'buddyforms' ) . '</b>' ), array(
				'value'     => $hide_if_logged_in,
				'id'        => "buddyforms_options[form_fields][" . $field_id . "][hide_if_logged_in]",
				'shortDesc' => __( 'If you want to use this form to allow your users to edit there profile you can hide the password for logged in users to prevent change the password with every update.', 'buddyforms' )
			) );

			$form_fields['hidden']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'user_login' );

			break;
		case 'user_email':
			unset( $form_fields['advanced']['slug'] );

			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'User eMail', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );

			$hide_if_logged_in                           = isset( $customfield['hide_if_logged_in'] ) ? $customfield['hide_if_logged_in'] : 'show';
			$form_fields['general']['hide_if_logged_in'] = new Element_Checkbox( '<b>' . __( 'Hide User eMail Form Element for LoggedIn User', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][hide_if_logged_in]", array( 'hide' => '<b>' . __( 'Hide for logged in user', 'buddyforms' ) . '</b>' ), array(
				'value'     => $hide_if_logged_in,
				'id'        => "buddyforms_options[form_fields][" . $field_id . "][hide_if_logged_in]",
				'shortDesc' => __( 'If you want to use this form to allow your users to edit there profile you can hide the password for logged in users to prevent change the password with every update.', 'buddyforms' )
			) );

			$form_fields['hidden']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'user_email' );
			break;
		case 'user_first':
			unset( $form_fields['advanced']['slug'] );

			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'First Name', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );

			$form_fields['hidden']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'user_first' );
			break;
		case 'user_last':
			unset( $form_fields['advanced']['slug'] );

			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Last Name', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );

			$form_fields['hidden']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'user_last' );
			break;
		case 'user_pass':
			unset( $form_fields['advanced']['slug'] );

			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Password', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'value'    => $name,
				'class'    => "use_as_slug",
				'required' => 1
			) );

			$hide_if_logged_in                           = isset( $customfield['hide_if_logged_in'] ) ? $customfield['hide_if_logged_in'] : 'show';
			$form_fields['general']['hide_if_logged_in'] = new Element_Checkbox( '<b>' . __( 'Hide Password Form Element for LoggedIn User', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][hide_if_logged_in]", array( 'hide' => '<b>' . __( 'Hide for logged in user', 'buddyforms' ) . '</b>' ), array(
				'value'     => $hide_if_logged_in,
				'id'        => "buddyforms_options[form_fields][" . $field_id . "][hide_if_logged_in]",
				'shortDesc' => __( 'If you want to use this form to allow your users to edit there profile you can hide the password for logged in users to prevent change the password with every update.', 'buddyforms' )
			) );

			$form_fields['hidden']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'user_pass' );
			break;
		case 'user_website':
			unset( $form_fields['advanced']['slug'] );

			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Website', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'value'    => $name,
				'class'    => "use_as_slug",
				'required' => 1
			) );

			$form_fields['hidden']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'website' );
			break;
		case 'user_bio':
			unset( $form_fields['advanced']['slug'] );

			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Bio', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'value'    => $name,
				'class'    => "use_as_slug",
				'required' => 1
			) );

			$form_fields['hidden']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'user_bio' );
			break;
		case 'captcha':
			unset( $form_fields['advanced'] );
			unset( $form_fields['validation'] );

			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Captcha', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );

			$captcha_site_key                           = ! empty( $customfield['captcha_site_key'] ) ? $customfield['captcha_site_key'] : '';
			$short_description                          = sprintf( '%s <a target="_blank" href="%s">reCaptcha</a> %s', __( "Sign up for a free", 'buddyforms' ), 'https://www.google.com/recaptcha/', __( 'Keys.', 'buddyforms' ) );
			$form_fields['general']['captcha_site_key'] = new Element_Textbox( '<b>' . __( "Site Key.", 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][captcha_site_key]", array(
				'data'     => $field_id,
				'value'    => $captcha_site_key,
				'required' => 1,
				'shortDesc' => $short_description
			) );
			$captcha_private_key                           = ! empty( $customfield['captcha_private_key'] ) ? $customfield['captcha_private_key'] : '';
			$form_fields['general']['captcha_private_key'] = new Element_Textbox( '<b>' . __( 'Private Key', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][captcha_private_key]", array(
				'data'     => $field_id,
				'value'    => $captcha_private_key,
				'required' => 1
			) );

			$captcha_language                           = ! empty( $customfield['captcha_language'] ) ? $customfield['captcha_language'] : '';
			$form_fields['general']['captcha_language'] = new Element_Textbox( '<b>' . __( 'Language', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][captcha_language]", array(
				'data'     => $field_id,
				'value'    => $captcha_language,
				'required' => 1,
                'shortDesc' => sprintf( '%s <a target="_blank" href="%s">Language Codes</a>', __( "Forces the element to render in a specific language, if empty will try to auto-detects the user's language, check the possibles", 'buddyforms' ), 'https://developers.google.com/recaptcha/docs/language' )
			) );

			$form_fields['general']['captcha_data_theme'] = new Element_Select( '<b>' . __( 'The color theme of the widget', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][captcha_data_theme]",
				array(
					'dark'  => __( 'Dark', 'buddyform' ),
					'light' => __( 'Light', 'buddyform' ),
				), array(
					'value'    => isset( $customfield['captcha_data_theme'] ) ? $customfield['captcha_data_theme'] : 'dark',
					'field_id' => $field_id,
				)
			);

			$form_fields['general']['captcha_data_type'] = new Element_Select( '<b>' . __( 'The type of CAPTCHA to serve', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][captcha_data_type]",
				array(
					'image'  => __( 'Image', 'buddyform' ),
					'audio' => __( 'Audio', 'buddyform' ),
				), array(
					'value'    => isset( $customfield['captcha_data_type'] ) ? $customfield['captcha_data_type'] : 'image',
					'field_id' => $field_id,
				)
			);

			$form_fields['general']['captcha_data_size'] = new Element_Select( '<b>' . __( 'The size of the widget', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][captcha_data_size]",
				array(
					'normal'  => __( 'Normal', 'buddyform' ),
					'compact' => __( 'Compact', 'buddyform' ),
				), array(
					'value'    => isset( $customfield['captcha_data_size'] ) ? $customfield['captcha_data_size'] : 'normal',
					'field_id' => $field_id,
				)
			);

			$form_fields['general']['html'] = new Element_HTML( sprintf('<p><b>%s<b><p>', __( 'reCaptcha is only visible to logged off users . Logged in users not need to get checked.', 'buddyforms') ) );

			$form_fields['hidden']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'captcha' );
			$form_fields['hidden']['type'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );
			break;
		case 'textarea':
            $name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Textarea', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );
			$post_textarea_options                     = isset( $customfield['post_textarea_options'] ) ? $customfield['post_textarea_options'] : 'false';
			$post_textarea_options_array               = array(
				'media_buttons' => 'media_buttons',
				'tinymce'       => 'tinymce',
				'quicktags'     => 'quicktags'
			);
			$form_fields['advanced']['textarea_opt_a'] = new Element_Checkbox( '<b>' . __( 'Turn on wp editor features', 'buddyforms' ) . '</b><br><br>', "buddyforms_options[form_fields][" . $field_id . "][post_textarea_options]", $post_textarea_options_array, array( 'value' => $post_textarea_options ) );

			$validation_minlength                              = isset( $customfield['validation_minlength'] ) ? stripcslashes( $customfield['validation_minlength'] ) : 0;
			$form_fields['validation']['validation_minlength'] = new Element_Number( '<b>' . __( 'Validation Min Length', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_minlength]", array( 'value' => $validation_minlength,'min'=>0 ,'class'=>'textarea-minlength','field_id'=>$field_id.'_validation_minlength','onchange'=>'bfValidateRule("'.$field_id.'","min",this,"textarea")' ) );

			$validation_maxlength                              = isset( $customfield['validation_maxlength'] ) ? stripcslashes( $customfield['validation_maxlength'] ) : 0;
			$form_fields['validation']['validation_maxlength'] = new Element_Number( '<b>' . __( 'Validation Max Length', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_maxlength]", array( 'value' => $validation_maxlength,'min'=>0 ,'class'=>'textarea-maxlength','field_id'=>$field_id.'_validation_maxlength','onchange'=>'bfValidateRule("'.$field_id.'","max",this,"textarea")' ) );


			$hidden                            = isset( $customfield['hidden_field'] ) ? $customfield['hidden_field'] : false;
			$form_fields['advanced']['hidden_field'] = new Element_Checkbox( '<b>' . __( 'Hidden?', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][hidden_field]", array( 'hidden_field' => '<b>' . __( 'Make this field Hidden', 'buddyforms' ) . '</b>' ), array( 'value' => $hidden ) );

			$generate_textarea                            = isset( $customfield['generate_textarea'] ) ? $customfield['generate_textarea'] : '';
			$form_fields['advanced']['generate_textarea'] = new Element_Textbox( '<b>' . __( 'Generate textarea', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][generate_textarea]", array(
				'data'     => $field_id,
				'value'    => $generate_textarea,
				'shortDesc' => __( 'You can use any other field value by using the shortcodes [field_slug]', 'buddyforms' ),
			) );

			$textarea_rows                              = isset( $customfield['textarea_rows'] ) ? stripcslashes( $customfield['textarea_rows'] ) : apply_filters('buddyforms_textarea_default_rows', 3);
			$form_fields['advanced']['textarea_rows'] = new Element_Number( '<b>' . __( 'Amount of rows', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][textarea_rows]", array( 'value' => $textarea_rows ) );

			break;
		case 'post_excerpt':

			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Post Excerpt', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );

			$post_excerpt_options                          = isset( $customfield['post_excerpt_options'] ) ? $customfield['post_excerpt_options'] : 'false';
			$post_excerpt_options_array                    = array(
				'media_buttons' => 'media_buttons',
				'tinymce'       => 'tinymce',
				'quicktags'     => 'quicktags'
			);
			$form_fields['advanced']['post_excerpt_opt_a'] = new Element_Checkbox( '<b>' . __( 'Turn on wp editor features', 'buddyforms' ) . '</b><br><br>', "buddyforms_options[form_fields][" . $field_id . "][post_excerpt_options]", $post_excerpt_options_array, array( 'value' => $post_excerpt_options ) );

			$validation_minlength                              = isset( $customfield['validation_minlength'] ) ? stripcslashes( $customfield['validation_minlength'] ) : 0;
			$form_fields['validation']['validation_minlength'] = new Element_Number( '<b>' . __( 'Validation Min Length', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_minlength]", array( 'value' => $validation_minlength ,'min'=>0,'field_id'=>$field_id.'_validation_minlength','onchange'=>'bfValidateRule("'.$field_id.'","min",this,"post_excerpt")' ) );

            $validation_maxlength                              = isset( $customfield['validation_maxlength'] ) ? stripcslashes( $customfield['validation_maxlength'] ) : 0;
			$form_fields['validation']['validation_maxlength'] = new Element_Number( '<b>' . __( 'Validation Max Length', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_maxlength]", array( 'value' => $validation_maxlength ,'min'=>0,'field_id'=>$field_id.'_validation_maxlength','onchange'=>'bfValidateRule("'.$field_id.'","max",this,"post_excerpt")' ) );

            $hidden                            = isset( $customfield['hidden_field'] ) ? $customfield['hidden_field'] : false;
			$form_fields['advanced']['hidden_field'] = new Element_Checkbox( '<b>' . __( 'Hidden?', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][hidden_field]", array( 'hidden_field' => '<b>' . __( 'Make this field Hidden', 'buddyforms' ) . '</b>' ), array( 'value' => $hidden ) );

			$generate_post_excerpt                            = isset( $customfield['generate_post_excerpt'] ) ? $customfield['generate_post_excerpt'] : '';
			$form_fields['advanced']['generate_post_excerpt'] = new Element_Textarea( '<b>' . __( 'Generate Post Excerpt', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][generate_post_excerpt]", array(
				'value'     => $generate_post_excerpt,
				'shortDesc' => __( 'You can use any other field value by using the shortcodes [field_slug]', 'buddyforms' ),
			) );

			$textarea_rows                              = isset( $customfield['textarea_rows'] ) ? stripcslashes( $customfield['textarea_rows'] ) : apply_filters('buddyforms_post_excerpt_default_rows', 3);
			$form_fields['advanced']['textarea_rows'] = new Element_Number( '<b>' . __( 'Amount of rows', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][textarea_rows]", array( 'value' => $textarea_rows ) );

			unset( $form_fields['advanced']['slug'] );
			$form_fields['hidden']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'post_excerpt' );

			break;
		case 'email':
			unset( $form_fields['advanced']['slug'] );

			$name = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'eMail', 'buddyforms' );;
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );

			$form_fields['hidden']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'email' );
			break;
		case 'phone':
			unset( $form_fields['advanced']['slug'] );

			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Telephone Number', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );

			$form_fields['hidden']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'phone' );
			break;
		case 'number':
		    $name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Number', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );
			$validation_min                              = isset( $customfield['validation_min'] ) ? stripcslashes( $customfield['validation_min'] ) : 0;
			$form_fields['validation']['validation_min'] = new Element_Number( '<b>' . __( 'Validation Min Value', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_min]", array( 'value' => $validation_min,'min'=>0,'field_id'=>$field_id.'_validation_minlength','onchange'=>'bfValidateRule("'.$field_id.'","min",this,"number")' ) );

            $validation_max                              = isset( $customfield['validation_max'] ) ? stripcslashes( $customfield['validation_max'] ) : 0;
			$form_fields['validation']['validation_max'] = new Element_Number( '<b>' . __( 'Validation Max Value', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_max]", array( 'value' => $validation_max ,'min'=>0,'field_id'=>$field_id.'_validation_maxlength','onchange'=>'bfValidateRule("'.$field_id.'","max",this,"number")' ) );
            break;
		case 'dropdown':
		    $name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Dropdown', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );
			$is_frontend_checked                      = isset( $customfield['frontend_reset'] ) ? $customfield['frontend_reset'] : 'false';
			$form_fields['general']['frontend_reset'] = new Element_Checkbox( '<b>' . __( 'Reset', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][frontend_reset]", array( 'frontend_reset' => __( 'Enable Frontend Reset Option', 'buddyforms' ) ), array(
				'value' => $is_frontend_checked,
			) );
			$multiple                                 = isset( $customfield['multiple'] ) ? $customfield['multiple'] : 'false';
			$form_fields['general']['multiple']       = new Element_Checkbox( '<b>' . __( 'Multiple Selection', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][multiple]", array( 'multiple' => '<b>' . __( 'Multiple', 'buddyforms' ) . '</b>' ), array( 'value' => $multiple ) );
			$field_args                               = Array(
				'field_id'  => $field_id,
				'buddyform' => $buddyform
			);
			$form_fields['general']['select_options'] = new Element_HTML( buddyforms_form_element_multiple( $form_fields, $field_args ) );
			break;
		case 'radiobutton':
		    $name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Radio Button', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );
			$field_args                               = Array(
				'field_id'  => $field_id,
				'buddyform' => $buddyform
			);
			$is_frontend_checked                      = isset( $customfield['frontend_reset'] ) ? $customfield['frontend_reset'] : 'false';
			$form_fields['general']['frontend_reset'] = new Element_Checkbox( '<b>' . __( 'Reset', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][frontend_reset]", array( 'frontend_reset' => __( 'Enable Frontend Reset Option', 'buddyforms' ) ), array(
				'value' => $is_frontend_checked,
			) );
			$form_fields['general']['select_options'] = new Element_HTML( buddyforms_form_element_multiple( $form_fields, $field_args ) );
			break;
		case 'checkbox':
			$name                                     = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Checkbox', 'buddyforms' );
			$form_fields['general']['name']           = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );
			$field_args                               = Array(
				'field_id'  => $field_id,
				'buddyform' => $buddyform
			);
			$is_frontend_checked                      = isset( $customfield['frontend_reset'] ) ? $customfield['frontend_reset'] : 'false';
			$form_fields['general']['frontend_reset'] = new Element_Checkbox( '<b>' . __( 'Reset', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][frontend_reset]", array( 'frontend_reset' => __( 'Enable Frontend Reset Option', 'buddyforms' ) ), array(
				'value' => $is_frontend_checked,
			) );
			$form_fields['general']['select_options'] = new Element_HTML( buddyforms_form_element_multiple( $form_fields, $field_args ) );
			break;
        case 'upload':
            $name                           = isset(  $buddyform['form_fields'][$field_id]['name'] ) ? stripcslashes( $buddyform['form_fields'][$field_id]['name'] ) : __( 'Upload', 'buddyforms' );
            $form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
                'value'    => $name,
                'class'    => "use_as_slug",
                'required' => 1
            ) );

	        $file_limit     = isset( $buddyform['form_fields'][ $field_id ]['file_limit'] ) ? stripslashes( $buddyform['form_fields'][ $field_id ]['file_limit'] ) : '1.00';
	        $accepted_files = isset( $buddyform['form_fields'][ $field_id ]['accepted_files'] ) ? $buddyform['form_fields'][ $field_id ]['accepted_files'] : 'jpg|jpeg|jpe';
	        $multiple_files = isset( $buddyform['form_fields'][ $field_id ]['multiple_files'] ) ? $buddyform['form_fields'][ $field_id ]['multiple_files'] : 1;

	        //To keep backward compatibility
	        if ( ! empty( $multiple_files ) && $multiple_files == 0 ) {
		        $multiple_files = 1;
	        }

	        $delete_files   = isset( $buddyform['form_fields'][ $field_id ]['delete_files'] ) ? $buddyform['form_fields'][ $field_id ]['delete_files'][0] : '';

	        $form_fields['general']['upload_file_limts'] = new Element_Number( '<b>' . __( 'Max File Size in MB', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][file_limit]", array(
		        'value' => floatval( $file_limit ),
		        'id'    => 'upload_file_limit' . $field_id,
		        'step'=> '.01'
	        ) );
	        $original_mimes_types                        = get_allowed_mime_types();
	        $sorted_mimes_types                          = array();

	        if( isset( $accepted_files ) && is_array( $accepted_files ) ) {
		        foreach ( $accepted_files as $mime_type ) {
			        if ( array_key_exists( $mime_type, $original_mimes_types ) ) {
				        $sorted_mimes_types[ $mime_type ] = $original_mimes_types[ $mime_type ];
			        }
		        }
	        }

	        asort( $sorted_mimes_types );
	        $preview_mime_value = '';
	        if( isset( $sorted_mimes_types ) && is_array( $sorted_mimes_types ) ) {
		        foreach ( $sorted_mimes_types as $key => $value ) {
			        $preview_mime_value .= $value . ', ';
		        }
            }

	        if( isset( $original_mimes_types ) && is_array( $original_mimes_types ) ) {
		        foreach ( $original_mimes_types as $key => $value ) {
			        if ( ! array_key_exists( $key, $sorted_mimes_types ) ) {
				        $sorted_mimes_types[ $key ] = $original_mimes_types[ $key ];
			        }
		        }
	        }

	        $allowed_mime_types = $sorted_mimes_types;
	        $form_fields['general']['upload_accepted_files']       = new Element_Checkbox( '<b>' . __( 'Allowed File Types', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][accepted_files]", $allowed_mime_types, array(
		        'id' => 'upload_multiple_files' . $field_id,
		        'value' => $accepted_files,
		        'class' => 'upload_accepted_fields_container'
	        ) );
	        $html               = rtrim( trim( $preview_mime_value ), ',' );
	        $form_fields['general']['upload_accepted_files_label'] = new Element_Textarea( '<b>' . __( 'Allowed File Types Resume', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][html]", array( 'value' => $html, 'readonly' => 'readonly' ) );

            $ensure_amount  = isset( $buddyform['form_fields'][ $field_id ]['ensure_amount'] ) ? $buddyform['form_fields'][ $field_id ]['ensure_amount'][0] : '';
            $form_fields['validation']['ensure_amount'] = new Element_Checkbox( '<b>' . __( 'Ensure Amount', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][ensure_amount]", array( 'ensure_amount' => '<b>'.__( 'Files in the field should be equals to Max Number of Files. ', 'buddyforms' ).'</b>' ), array(
                'id' => 'ensure_amount' . $field_id,
                'value' => $ensure_amount
            ) );
            $form_fields['validation']['upload_multiple_files']  = new Element_Number( '<b>' . __( 'Max number of files that will be handled', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][multiple_files]", array(
                'value' => intval( $multiple_files ),
                'min'   =>1,
                'id'    => 'upload_multiple_files' . $field_id,
                'step'=> '1'
            ) );

            $multiple_files_validation_message = isset( $customfield['multiple_files_validation_message'] ) ? stripcslashes( $customfield['multiple_files_validation_message'] ) : __( 'The number of files is greater than allowed.', 'buddyforms' );
            $form_fields['validation']['multiple_files_validation_message'] = new Element_Textbox( '<b>' . __( 'Max files Validation Error Message', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][multiple_files_validation_message]", array( 'value' => $multiple_files_validation_message ) );
            $upload_error_validation_message = isset( $customfield['upload_error_validation_message'] ) ? stripcslashes( $customfield['upload_error_validation_message'] ) : __( 'One or more files have errors, please check.', 'buddyforms' );
            $form_fields['validation']['upload_error_validation_message'] = new Element_Textbox( '<b>' . __( 'Upload Error Validation Message', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][upload_error_validation_message]", array( 'value' => $upload_error_validation_message ) );

	        $element_delete = new Element_Checkbox( '<b>' . __( 'Delete Files', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][delete_files]", array( 'delete' => __( 'Remove Files when Entry is deleted. ', 'buddyforms' ) ), array(
		        'id' => 'upload_delete_files' . $field_id,
		        'value' => $delete_files
	        ) );


	        $form_fields['general']['upload_delete_files']   = $element_delete;

            break;
        case 'date':
	        $name                            = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Date', 'buddyforms' );
	        $form_fields['general']['name']  = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
		        'data'     => $field_id,
		        'class'    => "use_as_slug",
		        'value'    => $name,
		        'required' => 1
	        ) );

            //More formats in https://trentrichardson.com/examples/timepicker
	        $element_date_format                           = isset( $customfield['element_date_format'] ) ? stripcslashes( $customfield['element_date_format'] ) : 'dd/mm/yy';
	        $form_fields['general']['element_date_format'] = new Element_Textbox( '<b>' . __( 'Date Format', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][element_date_format]", array(
		        'value'     => $element_date_format,
		        'shortDesc' => __( 'Read more about the format <a target="_blank" href="https://api.jqueryui.com/datepicker/#utility-formatDate">at.</a>', 'buddyforms' )
	        ) );

            $enable_time                           = isset( $customfield['enable_time'] ) ? $customfield['enable_time'] : false;
	        $form_fields['general']['enable_time'] = new Element_Checkbox( '<b>' . __( 'Enable Time', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][enable_time]", array( 'enable_time' => '<b>' . __( 'Include the Time Picker in the element', 'buddyforms' ) . '</b>' ), array(
		        'value' => $enable_time,
	        ) );
	        $element_time_format                           = isset( $customfield['element_time_format'] ) ? stripcslashes( $customfield['element_time_format'] ) : 'hh:mm tt';
	        $form_fields['general']['element_time_format'] = new Element_Textbox( '<b>' . __( 'Time Format', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][element_time_format]", array(
		        'value'     => $element_time_format,
		        'shortDesc' => __( 'Read more about the format <a target="_blank" href="https://trentrichardson.com/examples/timepicker/#tp-formatting">at.</a>', 'buddyforms' )
	        ) );

            $element_time_step                          = isset( $customfield['element_time_step'] ) ? stripcslashes( $customfield['element_time_step'] ) : 60;
			$form_fields['general']['element_time_step'] = new Element_Number( '<b>' . __( 'Time Step', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][element_time_step]", array(
				'value'     => $element_time_step,
			) );

            break;
        case 'time':
            $name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Time', 'buddyforms' );
            $form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );

            //More formats in https://trentrichardson.com/examples/timepicker/
	        $element_time_format                           = isset( $customfield['element_time_format'] ) ? stripcslashes( $customfield['element_time_format'] ) : 'hh:mm tt';
	        $form_fields['general']['element_time_format'] = new Element_Textbox( '<b>' . __( 'Time Format', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][element_time_format]", array(
		        'value'     => $element_time_format,
		        'shortDesc' => __( 'Read more about the format <a target="_blank" href="https://trentrichardson.com/examples/timepicker/#tp-formatting">at.</a>', 'buddyforms' )
	        ) );


            $element_time_hour_step                          = isset( $customfield['element_time_hour_step'] ) ? stripcslashes( $customfield['element_time_hour_step'] ) : 1;
			$form_fields['general']['element_time_hour_step'] = new Element_Number( '<b>' . __( 'Hour Step', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][element_time_hour_step]", array(
				'value'     => $element_time_hour_step,
			) );

			$element_time_minute_step                          = isset( $customfield['element_time_minute_step'] ) ? stripcslashes( $customfield['element_time_minute_step'] ) : 1;
			$form_fields['general']['element_time_minute_step'] = new Element_Number( '<b>' . __( 'Minute Step', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][element_time_minute_step]", array(
				'value'     => $element_time_minute_step,
			) );

            break;
		case 'post_formats':
		    $name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Post Format', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );
			unset( $form_fields['advanced']['slug'] );
			unset( $form_fields['advanced']['metabox_enabled'] );
			$form_fields['hidden']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'post_formats' );
			$form_fields['hidden']['type'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );

			$post_formats = get_theme_support( 'post-formats' );
			$post_formats = isset( $post_formats[0] ) ? $post_formats[0] : array();
			array_unshift( $post_formats, 'none' );


			$post_formats_default = isset( $customfield['post_formats_default'] ) ? $customfield['post_formats_default'] : false;

			$form_fields['general']['post_formats_default'] = new Element_Select( '<b>' . __( 'Post Formats Default', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][post_formats_default]", $post_formats, array(
				'value'    => $post_formats_default,
				'class'    => 'bf_hide_if_post_type_none',
				'field_id' => $field_id,
				'id'       => 'post_formats_field_id_' . $field_id,
			) );

			$hidden                            = isset( $customfield['hidden_field'] ) ? $customfield['hidden_field'] : false;
			$form_fields['advanced']['hidden_field'] = new Element_Checkbox( '<b>' . __( 'Hidden', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][hidden_field]", array( 'hidden_field' => '<b>' . __( 'Make this field Hidden', 'buddyforms' ) . '</b>' ), array(
				'value' => $hidden,
				'class' => 'bf_hide_if_post_type_none'
			) );

			break;

		case 'taxonomy':
		case 'category':
		case 'tags':
			unset( $form_fields['advanced']['metabox_enabled'] );
            $error_field_type_name = '';

			if(isset( $customfield['name'] )){
			    $name = stripcslashes( $customfield['name'] );
            } else {
				switch ( $field_type ) {
					case 'taxonomy':
						$name = __( 'Taxonomy', 'buddyforms' );
                        $error_field_type_name = __( 'Taxonomies', 'buddyforms' );
						break;
					case 'category':
					    $name = __( 'Category', 'buddyforms' );
                        $error_field_type_name = __( 'Categories', 'buddyforms' );
						break;
					case 'tags':
					    $name = __( 'Tags', 'buddyforms' );
                        $error_field_type_name = __( 'Tags', 'buddyforms' );
						break;
				}
			}

			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'value'    => $name,
				'required' => 1
			) );

            if( sanitize_title( $field_type ) == 'taxonomy' ) {
	            if ( buddyforms_core_fs()->is_not_paying() && ! buddyforms_core_fs()->is_trial() ) {
		            $error                              = '<table style="width:100%;"id="table_row_' . $field_id . '_is_not_paying" class="wp-list-table posts fixed">
                        <td colspan="2">
                            <div class="is_not_paying bf-error"><p>'. __( 'BuddyForms Professional is required to use this form Element . You need to upgrade to the Professional Plan . The Free and Starter Versions does not support Categories tags nad Taxonomies.', 'buddyforms' ).' <a href="edit.php?post_type=buddyforms&amp;page=buddyforms-pricing">'. __( 'Upgrade Now', 'buddyforms' ).'</a></p></div>
                        </td>
                        </table>';
		            $form_fields['general']['disabled'] = new Element_HTML( $error );
		            break;
	            }
            }

			$error = '<table style="width:100%;"id="table_row_' . $field_id . '_taxonomy_error" class="wp-list-table posts fixed bf_hide_if_post_type_none taxonomy_no_post_type">
                        <td colspan="2">
                            <div class="taxonomy_no_post_type bf-error">' . __( 'Please select a post type in the "Form Setup" tab "Create Content" to get the post type taxonomies.', 'buddyforms' ) . '</div>
                        </td>
                        </table>';

			$form_fields['general']['disabled'] = new Element_HTML( $error );


			$taxonomy_objects = get_object_taxonomies( $post_type );

			if ( isset( $taxonomy_objects[0] ) ) {
				$taxonomies = buddyforms_taxonomies( $post_type );
			} else {
				$taxonomies = array( 'category' => 'Categories' );
				if ( isset( $post_type ) ) {
					$error                                             = '<table style="width:100%;"id="table_row_' . $field_id . '_post_type_no_taxonomy_error" class="wp-list-table posts fixed">
                        <td colspan="2">
                            <div class="post_type_no_taxonomy_error bf-error">' . __( 'This Post Type does not have any '.$error_field_type_name.'.', 'buddyforms' ) . '</div>
                        </td>
                        </table>';
					$form_fields['general']['post_type_no_taxonomies'] = new Element_HTML( $error );
				}

			}

			$taxonomy = 'none';
			if ( $field_type == 'tags' ) {
				$taxonomy                          = 'post_tag';
				$form_fields['hidden']['taxonomy'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][taxonomy]", 'post_tag', array( 'id' => 'taxonomy_field_id_' . $field_id ) );
			} elseif ( $field_type == 'category' ) {
				$taxonomy                          = 'category';
				$form_fields['hidden']['taxonomy'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][taxonomy]", 'category', array( 'id' => 'taxonomy_field_id_' . $field_id ) );
			} else {
				if ( isset( $customfield['taxonomy'] ) ) {
					$taxonomy = $customfield['taxonomy'];
				}
				$form_fields['general']['taxonomy'] = new Element_Select( '<b>' . __( 'Taxonomy', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][taxonomy]", $taxonomies, array(
					'value'    => $taxonomy,
					'class'    => 'bf_tax_select bf_hide_if_post_type_none',
					'field_id' => $field_id,
					'id'       => 'taxonomy_field_id_' . $field_id,
				) );

			}

			$taxonomy_default = isset( $customfield['taxonomy_default'] ) ? $customfield['taxonomy_default'] : 'false';
			$taxonomy_order   = isset( $customfield['taxonomy_order'] ) ? $customfield['taxonomy_order'] : 'false';

            if( sanitize_title( $field_type ) == 'taxonomy' ) {
                if ( $customfield['taxonomy'] == 'none' ) {
                    $taxonomy = 'category';
                }
            }

			$wp_dropdown_categories_args = array(
				'hide_empty'    => 0,
				'child_of'      => 0,
				'echo'          => false,
				'selected'      => false,
				'hierarchical'  => 1,
				'id'            => 'taxonomy_default_' . $field_id,
				'name'          => "buddyforms_options[form_fields][" . $field_id . "][taxonomy_default][]",
				'class'         => 'postform bf-select2 tax_default',
				'depth'         => 0,
				'tab_index'     => 0,
				'taxonomy'      => $taxonomy,
				'hide_if_empty' => false,
				'orderby'       => 'SLUG',
				'order'         => $taxonomy_order,
			);

			$dropdown = wp_dropdown_categories( $wp_dropdown_categories_args );

			$dropdown = str_replace( 'id=', 'multiple="multiple" id=', $dropdown );

			if ( is_array( $taxonomy_default ) ) {
				foreach ( $taxonomy_default as $key => $post_term ) {
					$dropdown = str_replace( ' value="' . $post_term . '"', ' value="' . $post_term . '" selected="selected"', $dropdown );
				}
			} else {
				$dropdown = str_replace( ' value="' . $taxonomy_default . '"', ' value="' . $taxonomy_default . '" selected="selected"', $dropdown );
			}

			$dropdown = '<table style="width:100%;"id="table_row_' . $field_id . '_taxonomy_default" class="wp-list-table posts fixed bf_hide_if_post_type_none"><tr><th scope="row">
				<label for="form_title"><b style="margin-left: -10px;">' . __( 'Default Terms', 'buddyforms' ) . '</b></label></th>
				<td><div>' . $dropdown . '<p class="description">' . __( 'You can select a default category', 'buddyforms' ) . '</p></div></td></table>';

			$form_fields['general']['taxonomy_default'] = new Element_HTML( $dropdown );

			$taxonomy_placeholder                           = isset( $customfield['taxonomy_placeholder'] ) ? stripcslashes( $customfield['taxonomy_placeholder'] ) : 'Select an Option';
			$form_fields['general']['taxonomy_placeholder'] = new Element_Textbox( '<b>' . __( 'Taxonomy Placeholder', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][taxonomy_placeholder]", array(
				'data'      => $field_id,
				'value'     => $taxonomy_placeholder,
				'shortDesc' => __( 'You can change the placeholder to something meaningful like Select a Category or what make sense for your taxonomy.', 'buddyforms' )
			) );

			$form_fields['general']['taxonomy_order'] = new Element_Select( '<b>' . __( 'Taxonomy Order', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][taxonomy_order]", array(
				'ASC',
				'DESC'
			), array(
				'value' => $taxonomy_order,
				'class' => 'bf_hide_if_post_type_none'
			) );

			$multiple                           = isset( $customfield['multiple'] ) ? $customfield['multiple'] : 'false';
			$form_fields['general']['multiple'] = new Element_Checkbox( '<b>' . __( 'Multiple Selection', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][multiple]", array( 'multiple' => '<b>' . __( 'Multiple', 'buddyforms' ) . '</b>' ), array(
				'value' => $multiple,
				'class' => 'bf_hide_if_post_type_none'
			) );

			$tmaximumSelectionLength                          = isset( $customfield['maximumSelectionLength'] ) ? stripcslashes( $customfield['maximumSelectionLength'] ) : 0;
			$form_fields['validation']['maximumSelectionLength'] = new Element_Number( '<b>' . __( 'Limit Selections', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][maximumSelectionLength]", array(
				'data'      => $field_id,
				'value'     => $tmaximumSelectionLength,
				'shortDesc' => __( 'Add a number to limit the Selection amount', 'buddyforms' )
			) );

			$taxonomy_include = isset( $customfield['taxonomy_include'] ) ? $customfield['taxonomy_include'] : 'false';

			$wp_dropdown_taxonomy_include_args = array(
				'hide_empty'    => 0,
				'child_of'      => 0,
				'echo'          => false,
				'selected'      => false,
				'hierarchical'  => 1,
				'id'            => 'taxonomy_include' . $field_id,
				'name'          => "buddyforms_options[form_fields][" . $field_id . "][taxonomy_include][]",
				'class'         => 'postform bf-select2 tax_default',
				'depth'         => 0,
				'tab_index'     => 0,
				'taxonomy'      => $taxonomy,
				'hide_if_empty' => false,
				'orderby'       => 'SLUG',
				'order'         => $taxonomy_order,
				'exclude'       => '',
				'include'       => '',
			);

			$dropdown = wp_dropdown_categories( $wp_dropdown_taxonomy_include_args );

			$dropdown = str_replace( 'id=', 'multiple="multiple" id=', $dropdown );

			if ( is_array( $taxonomy_include ) ) {
				foreach ( $taxonomy_include as $key => $post_term ) {
					$dropdown = str_replace( ' value="' . $post_term . '"', ' value="' . $post_term . '" selected="selected"', $dropdown );
				}
			} else {
				$dropdown = str_replace( ' value="' . $taxonomy_include . '"', ' value="' . $taxonomy_include . '" selected="selected"', $dropdown );
			}

			$dropdown = '<table style="width:100%;"id="table_row_' . $field_id . '_taxonomy_include" class="wp-list-table posts fixed bf_hide_if_post_type_none"><tr>
                    <th scope="row">
                        <label for="form_title"><b style="margin-left: -10px;">' . __( 'Include Items', 'buddyforms' ) . '</b></label>
                    </th>
                    <td>
                        <div>' . $dropdown . '
                            <p class="description">' . __( 'You can select multiple items', 'buddyforms' ) . '</p>
                        </div>
                    </td></table>';


			$form_fields['general']['taxonomy_include'] = new Element_HTML( $dropdown );


			$taxonomy_exclude = isset( $customfield['taxonomy_exclude'] ) ? $customfield['taxonomy_exclude'] : 'false';

			$wp_dropdown_taxonomy_exclude_args = array(
				'hide_empty'    => 0,
				'child_of'      => 0,
				'echo'          => false,
				'selected'      => false,
				'hierarchical'  => 1,
				'id'            => 'taxonomy_exclude' . $field_id,
				'name'          => "buddyforms_options[form_fields][" . $field_id . "][taxonomy_exclude][]",
				'class'         => 'postform bf-select2 tax_default',
				'depth'         => 0,
				'tab_index'     => 0,
				'taxonomy'      => $taxonomy,
				'hide_if_empty' => false,
				'orderby'       => 'SLUG',
				'order'         => $taxonomy_order,
			);

			$dropdown = wp_dropdown_categories( $wp_dropdown_taxonomy_exclude_args );

			$dropdown = str_replace( 'id=', 'multiple="multiple" id=', $dropdown );

			if ( is_array( $taxonomy_exclude ) ) {
				foreach ( $taxonomy_exclude as $key => $post_term ) {
					$dropdown = str_replace( ' value="' . $post_term . '"', ' value="' . $post_term . '" selected="selected"', $dropdown );
				}
			} else {
				$dropdown = str_replace( ' value="' . $taxonomy_exclude . '"', ' value="' . $taxonomy_exclude . '" selected="selected"', $dropdown );
			}

			$dropdown = '<table style="width:100%;"id="table_row_' . $field_id . '_taxonomy_exclude" class="wp-list-table posts fixed bf_hide_if_post_type_none"><tr>
                    <th scope="row">
                        <label for="form_title"><b style="margin-left: -10px;">' . __( 'Exclude Items', 'buddyforms' ) . '</b></label>
                    </th>
                    <td>
                        <div>' . $dropdown . '
                            <p class="description">' . __( 'You can select multiple items', 'buddyforms' ) . '</p>
                        </div>
                    </td></table>';


			$form_fields['general']['taxonomy_exclude'] = new Element_HTML( $dropdown );


			$create_new_tax                           = isset( $customfield['create_new_tax'] ) ? $customfield['create_new_tax'] : 'false';
			$form_fields['general']['create_new_tax'] = new Element_Checkbox( '<b>' . __( 'New Taxonomy Item', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][create_new_tax]", array( 'user_can_create_new' => '<b>' . __( 'User can create new', 'buddyforms' ) . '</b>' ), array(
				'value' => $create_new_tax,
				'class' => 'bf_hide_if_post_type_none'
			) );

			$is_ajax                            = isset( $customfield['ajax'] ) ? $customfield['ajax'] : 'false';
			$form_fields['general']['ajax'] = new Element_Checkbox( '<b>' . __( 'Ajax', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][ajax]", array( 'is_ajax' => '<b>' . __( 'Enabled Ajax', 'buddyforms' ) . '</b>' ), array(
				'value' => $is_ajax,
				'data'  => $field_id,
				'class' => 'bf_taxonomy_ajax_ready'
			) );

			$minimum_input_length                         = isset( $customfield['minimumInputLength'] ) ? stripcslashes( $customfield['minimumInputLength'] ) : 0;
			$form_fields['general']['minimumInputLength'] = new Element_Number( '<b>' . __( 'Minimum characters ', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][minimumInputLength]", array(
				'data'      => $field_id,
				'value'     => $minimum_input_length,
				'shortDesc' => __( 'Minimum number of characters required to start a search.', 'buddyforms' ),
				'class'     => 'bf_hide_if_not_ajax_ready'
			) );

			$hidden                            = isset( $customfield['hidden_field'] ) ? $customfield['hidden_field'] : false;
			$form_fields['advanced']['hidden_field'] = new Element_Checkbox( '<b>' . __( 'Hidden', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][hidden_field]", array( 'hidden_field' => '<b>' . __( 'Make this field Hidden', 'buddyforms' ) . '</b>' ), array(
				'value' => $hidden,
				'class' => 'bf_hide_if_post_type_none'
			) );


			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$js = <<<JS
            	<script>

				jQuery(document).ready(function (jQuery) {

					var post_type = jQuery('#form_post_type').val();

					var tax_field_length = jQuery('#taxonomy_field_id_$field_id').children('option').length;

					if(tax_field_length > 1 ){
						// console.log('form_post_type_length link' + tax_field_length);
					} else {
                        if(buddyformsGlobal){
                            jQuery.ajax({
                                type: 'POST',
                                url: buddyformsGlobal.admin_url,
                                data: {
                                    "action": "buddyforms_post_types_taxonomies",
                                    "post_type": post_type
                                },
                                success: function (data) {
                                    // console.log(data);
                                    jQuery('#taxonomy_field_id_$field_id').html(data);
                                    jQuery('#taxonomy_field_id_$field_id').trigger('change');
                                    bf_taxonomy_input( "$field_id" );
    
                                },
                                error: function () {
                                    jQuery('.formbuilder-spinner').removeClass('is-active');
                                    jQuery('<div></div>').dialog({
                                        modal: true,
                                        title: "Info",
                                        open: function() {
                                            var markup = 'Something went wrong ;-(sorry)';
                                            jQuery(this).html(markup);
                                        },
                                        buttons: {
                                            Ok: function() {
                                                jQuery( this ).dialog( "close" );
                                            }
                                        }
                                    });
                                }
                            });
				        }
					}
				});
JS;

				$js .= <<<JS

				bf_taxonomy_input( "$field_id" );
				from_setup_post_type();
JS;

				$js                           .= '</script>';
				$form_fields['general']['js'] = new Element_HTML( $js );
			}
			break;
		case 'hidden':
			unset( $form_fields );
			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Hidden', 'buddyforms' );
			$form_fields['hidden']['name']   = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][name]", $field_slug );
			$form_fields['advanced']['slug'] = new Element_Textbox( '<b>' . __( 'Slug', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][slug]", array(
				'required' => true,
				'data'      => $field_id,
				'value'    => $field_slug,
			) );
			$form_fields['hidden']['type']   = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );

			$value                           = isset( $customfield['value'] ) ? $customfield['value'] : '';
			$form_fields['general']['value'] = new Element_Textbox( '<b>' . __( 'Value:', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][value]", array( 'value' => $value ) );
			break;
		case 'comments':
            unset( $form_fields['validation'] );
            unset( $form_fields['advanced']['slug'] );
            unset( $form_fields['advanced']['metabox_enabled'] );
			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Comments', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'value'    => $name,
				'required' => 1
			) );
			$form_fields['hidden']['slug']  = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'comments' );
			$form_fields['hidden']['type']  = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );
			$form_fields['general']['html'] = new Element_HTML( __( "This Post Field allows users to override the global comments settings so they can open and close comments as they wish.", 'buddyforms' ) );
			break;
		case 'title':
			unset( $form_fields['general']['required'] );
			unset( $form_fields['advanced']['slug'] );
			unset( $form_fields['advanced']['metabox_enabled'] );

			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Title', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Name', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'value'    => $name,
				'required' => 1
			) );
			$form_fields['hidden']['slug']  = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'buddyforms_form_title' );
			$form_fields['hidden']['type']  = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );

			$validation_minlength                              = isset( $customfield['validation_minlength'] ) ? stripcslashes( $customfield['validation_minlength'] ) : 0;
			$form_fields['validation']['validation_minlength'] = new Element_Number( '<b>' . __( 'Validation Min Length', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_minlength]", array( 'value' => $validation_minlength,'min'=>0,'field_id'=>$field_id.'_validation_minlength','onchange'=>'bfValidateRule("'.$field_id.'","min",this,"title")' ) );

			$validation_maxlength                              = isset( $customfield['validation_maxlength'] ) ? stripcslashes( $customfield['validation_maxlength'] ) : '';
			$form_fields['validation']['validation_maxlength'] = new Element_Number( '<b>' . __( 'Validation Max Length', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_maxlength]", array( 'value' => $validation_maxlength,'min'=>0,'field_id'=>$field_id.'_validation_maxlength','onchange'=>'bfValidateRule("'.$field_id.'","max",this,"title")' ) );

			$hidden                            = isset( $customfield['hidden_field'] ) ? $customfield['hidden_field'] : false;
			$form_fields['advanced']['hidden_field'] = new Element_Checkbox( '<b>' . __( 'Hidden?', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][hidden_field]", array( 'hidden_field' => '<b>' . __( 'Make this field Hidden', 'buddyforms' ) . '</b>' ),
				array(
					'value'     => $hidden,
					'shortDesc' => __( 'If you want to generate the title you can set the title to hidden. If the title is visible and a title is entered the entered tiltle is stronger than the generated title. If you want to make sure the generated title is used hide the title field', 'buddyforms' )
				) );

			$generate_title                            = isset( $customfield['generate_title'] ) ? $customfield['generate_title'] : '';
			$form_fields['advanced']['generate_title'] = new Element_Textbox( '<b>' . __( 'Generate Title', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][generate_title]", array(
				'value'     => $generate_title,
				'shortDesc' => __( 'You can use any other field value by using the shortcodes [field_slug]', 'buddyforms' ),
			) );

			break;
		case 'content':
			unset( $form_fields['advanced']['metabox_enabled'] );
			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Content', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Name', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'value'    => $name,
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'required' => 1
			) );

			$post_content_options                     = isset( $customfield['post_content_options'] ) ? $customfield['post_content_options'] : 'false';
			$post_content_options_array               = array(
				'media_buttons' => 'media_buttons',
				'tinymce'       => 'tinymce',
				'quicktags'     => 'quicktags'
			);
			$form_fields['advanced']['content_opt_a'] = new Element_Checkbox( '<b>' . __( 'Turn off wp editor features', 'buddyforms' ) . '</b><br><br>', "buddyforms_options[form_fields][" . $field_id . "][post_content_options]", $post_content_options_array, array( 'value' => $post_content_options ) );

			unset( $form_fields['advanced']['slug'] );
			$form_fields['hidden']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'buddyforms_form_content' );
			$form_fields['hidden']['type'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );

			$validation_minlength                              = isset( $customfield['validation_minlength'] ) ? stripcslashes( $customfield['validation_minlength'] ) : 0;
			$form_fields['validation']['validation_minlength'] = new Element_Number( '<b>' . __( 'Validation Min Length', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_minlength]", array( 'value' => $validation_minlength,'min'=>0,'field_id'=>$field_id.'_validation_minlength','onchange'=>'bfValidateRule("'.$field_id.'","min",this,"content")' ) );

			$validation_maxlength                              = isset( $customfield['validation_maxlength'] ) ? stripcslashes( $customfield['validation_maxlength'] ) : 0;
			$form_fields['validation']['validation_maxlength'] = new Element_Number( '<b>' . __( 'Validation Max Length', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_maxlength]", array( 'value' => $validation_maxlength ,'min'=>0,'field_id'=>$field_id.'_validation_maxlength','onchange'=>'bfValidateRule("'.$field_id.'","max",this,"content")' ) );

			$hidden                            = isset( $customfield['hidden_field'] ) ? $customfield['hidden_field'] : false;
			$form_fields['advanced']['hidden_field'] = new Element_Checkbox( '<b>' . __( 'Hidden?', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][hidden_field]", array( 'hidden_field' => '<b>' . __( 'Make this field Hidden', 'buddyforms' ) . '</b>' ), array( 'value' => $hidden ) );

			$textarea_rows                              = isset( $customfield['textarea_rows'] ) ? stripcslashes( $customfield['textarea_rows'] ) : apply_filters('buddyforms_post_content_default_rows', 18);
			$form_fields['advanced']['textarea_rows'] = new Element_Number( '<b>' . __( 'Amount of rows', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][textarea_rows]", array( 'value' => $textarea_rows ) );

			$generate_content                            = isset( $customfield['generate_content'] ) ? $customfield['generate_content'] : '';
			$form_fields['advanced']['generate_content'] = new Element_Textarea( '<b>' . __( 'Generate Content', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][generate_content]", array(
				'value'     => $generate_content,
				'shortDesc' => __( 'You can use any other field value by using the shortcodes [field_slug]', 'buddyforms' ),
			) );

			break;
		case 'status':
			unset( $form_fields );
			// $required = isset($customfield['required']) ? $customfield['required'] : 'false';
			// $form_fields['general']['required']   = new Element_Checkbox('<b>' . __('Required', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][required]", array('required' => '<b>' . __('Required', 'buddyforms') . '</b>'), array('value' => $required, 'id' => "buddyforms_options[form_fields][" . $field_id . "][required]"));

			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Status', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Name', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'value'    => $name,
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'required' => 1
			) );
			$form_fields['hidden']['slug']  = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'post_status' );

			$post_status                           = isset( $customfield['post_status'] ) ? $customfield['post_status'] : 'post_status';
			$form_fields['general']['post_status'] = new Element_Checkbox( '<b>' . __( 'Select the post status you want to make available in the frontend form', 'buddyforms' ) . '</b><br><br>', "buddyforms_options[form_fields][" . $field_id . "][post_status]", buddyforms_get_post_status_array(), array(
				'value'     => $post_status,
				'id'        => "buddyforms_options[form_fields][" . $field_id . "][post_status]",
				'shortDesc' => __( "This Post Field allows users to override this form’s Status setting (find the setting above in the Form Settings bock).", 'buddyforms' )
			) );
			$form_fields['hidden']['type']         = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );
			break;
		case 'featured_image':
			//unset( $form_fields );
            $max_file_size     = isset( $buddyform['form_fields'][ $field_id ]['max_file_size'] ) ?  $buddyform['form_fields'][ $field_id ]['max_file_size']  : '1';
            $form_fields['general']['max_file_size'] = new Element_Number( '<b>' . __( 'Max File Size in MB', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][max_file_size]", array(
                'value' => floatval( $max_file_size ),
                'id'    => 'max_file_size' . $field_id,
                'step'=> '1'
            ) );

			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Featured Image', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Name', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'value'    => $name,
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'required' => 1
			) );

			$button_label                           = isset( $customfield['button_label'] ) ? stripcslashes( $customfield['button_label'] ) : __( 'Add Image', 'buddyforms' );
			$form_fields['general']['button_label'] = new Element_Textbox( '<b>' . __( 'Button Label', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][button_label]", array(
				'value' => $button_label,
			) );

			$form_fields['hidden']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'featured_image' );

			$description                           = isset( $customfield['description'] ) ? stripcslashes( $customfield['description'] ) : '';
			$form_fields['general']['description'] = new Element_Textbox( '<b>' . __( 'Description:', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][description]", array( 'value' => $description ) );
			$form_fields['hidden']['type']         = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );

            $field_slug                      = isset( $customfield['slug'] ) ? buddyforms_sanitize_slug( $customfield['slug'] ) : 'featured_image';
            $form_fields['advanced']['slug'] = new Element_Textbox( '<b>' . __( 'Slug', 'buddyforms' ) . '</b> <small>(optional)</small>', "buddyforms_options[form_fields][" . $field_id . "][slug]", array(
                'shortDesc' => __( 'Underscore before the slug like _name will create a hidden post meta field', 'buddyforms' ),
                'value'     => $field_slug,
                'data'      => $field_id,
                'required'  => 1,
                'class'     => 'slug' . $field_id
            ) );

            $upload_error_validation_message = isset( $customfield['upload_error_validation_message'] ) ? stripcslashes( $customfield['upload_error_validation_message'] ) : __( 'One or more files have errors, please check.', 'buddyforms' );
            $form_fields['validation']['upload_error_validation_message'] = new Element_Textbox( '<b>' . __( 'Upload Error Validation Message', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][upload_error_validation_message]", array( 'value' => $upload_error_validation_message ) );


			break;
		case 'file':
            $name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'File', 'buddyforms' );
            $form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Name', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'value'    => $name,
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'required' => 1
			) );
			$validation_multiple                            = isset( $customfield['validation_multiple'] ) ? $customfield['validation_multiple'] : 0;
			$form_fields['advanced']['validation_multiple'] = new Element_Checkbox( '<b>' . __( 'Only one file or multiple?', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_multiple]", array( 'multiple' => '<b>' . __( 'Allow multiple file upload', 'buddyforms' ) . '</b>' ), array( 'value' => $validation_multiple ) );

			$allowed_mime_types = get_allowed_mime_types();

			$data_types                            = isset( $customfield['data_types'] ) ? $customfield['data_types'] : '';
			$form_fields['advanced']['data_types'] = new Element_Checkbox( '<b>' . __( 'Select allowed file Types', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][data_types]", $allowed_mime_types, array( 'value' => $data_types ) );
			break;
		case 'html':
			unset( $form_fields );
			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'HTML', 'buddyforms' );
			$html                                  = isset( $customfield['html'] ) ? stripcslashes( $customfield['html'] ) : '';
			$form_fields['general']['description'] = new Element_Textarea( '<b>' . __( 'HTML:', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][html]", array( 'value' => $html ) );
			$form_fields['hidden']['name']         = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][name]", 'HTML' );
			$form_fields['hidden']['slug']         = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'html' );
			$form_fields['hidden']['type']         = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );
			break;
		case 'gdpr':
			unset( $form_fields );
			$name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'GDPR Agreement', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Name', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'value'    => $name,
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'required' => 1
			) );

			$description                           = isset( $customfield['description'] ) ? stripcslashes( $customfield['description'] ) : __( 'Please agree to our privacy police', 'buddyforms' );
			$form_fields['general']['description'] = new Element_Textbox( '<b>' . __( 'Description:', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][description]", array( 'value' => $description ) );


			$form_fields['hidden']['slug']         = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'gdpr-agreement' );
			$form_fields['hidden']['type']         = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );

			$field_args                           = Array(
                'field_id'  => $field_id,
                'buddyform' => $buddyform
            );
            $form_fields['general']['select_options'] = new Element_HTML( buddyforms_form_element_gdpr( $form_fields, $field_args ) );
            break;
        case 'form_actions':
            unset($form_fields);
            $name = __( 'Form Actions', 'buddyforms' );
            $field_slug = 'form_actions';
            $form_fields['general']['name'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][name]", $name );
            $form_fields['hidden']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", $field_slug );
			$form_fields['hidden']['type'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );

	        $is_draft_enabled                         = isset( $customfield['enable_draft'] ) ? $customfield['enable_draft'] : 'false';
	        $form_fields['general']['enable_draft']   = new Element_Checkbox( '<b>' . __( 'Draft Button', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][enable_draft]", array( 'enable_draft' => '<b>' . __( 'Check to enable', 'buddyforms' ) . '</b>' ), array(
		        'value' => $is_draft_enabled,
		        'id'    => "buddyforms_options[form_fields][" . $field_id . "][enable_draft]"
	        ) );
	        $is_publish_enabled                       = isset( $customfield['enable_publish'] ) ? $customfield['enable_publish'] : 'false';
	        $form_fields['general']['enable_publish'] = new Element_Checkbox( '<b>' . __( 'Publish Button', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][enable_publish]", array( 'enable_publish' => '<b>' . __( 'Check to enable', 'buddyforms' ) . '</b>' ), array(
		        'value' => $is_publish_enabled,
		        'id'    => "buddyforms_options[form_fields][" . $field_id . "][enable_publish]"
	        ) );
            break;
        case 'price':
            $name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Price', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Name', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'value'    => $name,
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'required' => 1
			) );
            $form_fields = Element_Price::builder_element_options($form_fields, $form_slug, $field_type, $field_id, $buddyform );
            break;
        case 'range':
            $name                           = isset( $customfield['name'] ) ? stripcslashes( $customfield['name'] ) : __( 'Range', 'buddyforms' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Name', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'value'    => $name,
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'required' => 1
			) );
        break;
		default:
			$form_fields = apply_filters( 'buddyforms_form_element_add_field', $form_fields, $form_slug, $field_type, $field_id );
			break;
	}

	$custom_class                            = isset( $customfield['custom_class'] ) ? stripcslashes( $customfield['custom_class'] ) : '';
	$form_fields['advanced']['custom_class'] = new Element_Textbox( '<b>' . __( 'Add custom class to the form element', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][custom_class]", array( 'value' => $custom_class ) );

	$form_fields = apply_filters( 'buddyforms_formbuilder_fields_options', $form_fields, $field_type, $field_id, $form_slug, $customfield );


	if ( is_array( $form_fields ) ) {
		$form_fields = buddyforms_sort_array_by_Array( $form_fields, array( 'general', 'validation', 'advanced' ) );
	}

	ob_start(); ?>
    <li id="field_<?php echo $field_id ?>"
    class="bf_list_item <?php echo $field_id ?> bf_<?php echo sanitize_title( $field_type ) ?>" data-field_id="<?php echo $field_id ?> ">

    <input id="this_field_id" type="hidden" value="<?php echo $field_id ?>">

    <div style="display:none;" class="hidden">
		<?php if ( isset( $form_fields['hidden'] ) ) {
			foreach ( $form_fields['hidden'] as $key => $form_field ) {
				$form_field->render();
			}
		} ?>
    </div>

    <div class="accordion_fields">
        <div class="accordion-group">
            <div class="accordion-heading-options">
                <table class="wp-list-table widefat fixed posts">
                    <tbody>
                    <tr>
                        <td class="field_order ui-sortable-handle">
                            <span class="circle">0</span>
                        </td>
                        <td class="field_label">
                            <strong>
                                <a class="bf_edit_field row-title accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_text" href="#accordion_<?php echo $field_type . '_' . $field_id; ?>" title="Edit this Field" href="javascript:;">
										<?php
                                        echo $name;
										if ( ! empty( $customfield ) && ! empty( $customfield['required'] ) && $customfield['required'][0] === 'required' ) {
											if ( is_subclass_of( $form_field, 'Base' ) ) {
												$form_field->renderRequired( true );
											}
										} ?></a>
                            </strong>

                        </td>
                        <td class="field_name">
                            <div class="tooltip">
                                <span class="field_name_text bf-ready-to-copy"><?php echo $field_slug ?></span>
                                <span class="tooltip-container"><?php _e('Copy to clipboard', 'buddyforms')?></span>
                            </div>
                        </td>
                        <td class="field_type"><?php echo $field_type ?></td>
                        <td class="field_delete">
                                <span><a class="accordion-toggle collapsed" data-toggle="collapse"
                                         data-parent="#accordion_text"
                                         href="#accordion_<?php echo $field_type . '_' . $field_id; ?>"
                                         title="<?php _e( 'Edit this Field', 'buddyforms' ) ?>" href="javascript:;"><?php _e( 'Edit', 'buddyforms' ) ?></a> | </span>
                            <span><a class="bf_delete_field" id="<?php echo $field_id ?>" title="<?php _e( 'Delete this Field', 'buddyforms' ) ?>"
                                     href="#"><?php _e( 'Delete', 'buddyforms' ) ?></a></span>
                        </td>

						<?php $layout = isset( $buddyform['layout']['cords'][ $field_id ] ) ? $buddyform['layout']['cords'][ $field_id ] : '1'; ?>

                        <td class="field_layout">
                            <select class="" name="buddyforms_options[layout][cords][<?php echo $field_id ?>]">
                                <option <?php selected( $layout, '1' ); ?> value="1"><?php _e( 'Full Width', 'buddyforms' ) ?></option>
                                <option <?php selected( $layout, '2' ); ?> value="2">1/2</option>
                                <option <?php selected( $layout, '3' ); ?> value="3">1/3</option>
                                <option <?php selected( $layout, '4' ); ?> value="4">1/4</option>
                                <option <?php selected( $layout, '5' ); ?> value="5">2/3</option>
                                <option <?php selected( $layout, '6' ); ?> value="6">3/4</option>
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div id="accordion_<?php echo $field_type . '_' . $field_id; ?>" class="accordion-body collapse">
                <div class="accordion-inner">
                    <div class="tabs-<?php echo $field_type . '-' . $field_id ?> tabbable buddyform-tabs-left ">
                        <ul id="bf_field_group<?php echo $field_type . '-' . $field_id ?>"
                            class="nav buddyform-nav-tabs buddyform-nav-pills">
							<?php
							$i = 0;
							foreach ( $form_fields as $key => $form_field ) {

								if ( $key == 'hidden' ) {
									continue;
								}

								$class_active = '';
								if ( $i == 0 ) {
									$class_active = 'active';
								}

								?>
                            <li class="<?php echo $class_active ?>"><a
                                        href="#<?php echo $key . '-' . $field_type . '-' . $field_id ?>"
                                        data-toggle="tab"><?php echo str_replace( '-', ' ', ucfirst( $key ) ) ?></a>
                                </li><?php
								$i ++;
							}
							?>
                        </ul>
                        <div id="bf_field_group_content<?php echo $field_type . '-' . $field_id ?>"
                             class="tab-content">
							<?php
							$i = 0;
							foreach ( $form_fields as $key => $form_field ) {

								if ( $key == 'hidden' ) {
									continue;
								}

								$class_active = '';
								if ( $i == 0 ) {
									$class_active = 'active';
								}
								?>
                                <div class="tab-pane fade in <?php echo $class_active ?>"
                                     id="<?php echo $key . '-' . $field_type . '-' . $field_id ?>">
                                    <div class="buddyforms_accordion_general">
										<?php buddyforms_display_field_group_table( $form_field, $field_id ) ?>
                                    </div>
                                </div>
								<?php
								$i ++;
							}
							if ( ! is_array( $form_field ) ) {
								_e( 'Please Save the form once for the form element to work.', 'buddyforms' );
							}
							?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </li>
	<?php
	$field_html = ob_get_contents();
	ob_end_clean();

	if ( is_array( $args ) ) {
		return $field_html;
	} else {
		echo $field_html;
		die();
	}


}

add_action( 'wp_ajax_buddyforms_display_form_element', 'buddyforms_display_form_element' );
/**
 * @param $form_fields
 * @param $args
 *
 * @return string
 */
function buddyforms_form_element_multiple( $form_fields, $args ) {

	$field_id  = '';
	$buddyform = '';
	extract( $args );

	if ( ! isset( $buddyform['form_fields'][ $field_id ]['options'] ) && isset( $buddyform['form_fields'][ $field_id ]['value'] ) ) {
		foreach ( $buddyform['form_fields'][ $field_id ]['value'] as $key => $value ) {
			$buddyform['form_fields'][ $field_id ]['options'][ $key ]['label'] = $value;
			$buddyform['form_fields'][ $field_id ]['options'][ $key ]['value'] = $value;
		}
	}

	$field_options = !empty($buddyform['form_fields'][ $field_id ]['options'])? $buddyform['form_fields'][ $field_id ]['options']: array();
	$field_type = !empty($buddyform['form_fields'][ $field_id ]['type'])?$buddyform['form_fields'][ $field_id ]['type']: '';
	$count = 1;
	$default_option = !empty( $buddyform['form_fields'][ $field_id ]['default'] ) ? $buddyform['form_fields'][ $field_id ]['default'] : '' ;

	ob_start();

	require BUDDYFORMS_ADMIN_VIEW.'buddyforms-form-element-multiple.php';

	$tmp = ob_get_clean();

	return $tmp;
}
function buddyforms_form_element_gdpr( $form_fields, $args ) {

	$field_id  = '';
	$buddyform = '';
	extract( $args );

	if ( ! empty( $field_id ) && ! empty( $buddyform ) ) {
		$field_options  = isset( $buddyform['form_fields'][ $field_id ]['options'] ) ? $buddyform['form_fields'][ $field_id ]['options'] : '';
		$field_type     = $buddyform['form_fields'][ $field_id ]['type'];
		$count          = 1;
		$default_option = isset( $buddyform['form_fields'][ $field_id ]['default'] ) ? $buddyform['form_fields'][ $field_id ]['default'] : '';

		ob_start();

		require BUDDYFORMS_ADMIN_VIEW . 'buddyforms-form-gdpr-element-multiple.php';

		$tmp = ob_get_clean();

		return $tmp;
	} else {
		return '';
	}
}

/**
 * @param $form_fields
 * @param string $field_id
 * @param string $striped
 */
function buddyforms_display_field_group_table( $form_fields, $field_id = 'global', $striped = 'striped' ) {
	?>
    <table class="wp-list-table widefat posts fixed <?php echo $striped ?>">
        <tbody>
		<?php
		if ( isset( $form_fields ) ) {
			foreach ( $form_fields as $key => $field ) {

				$type     = $field->getAttribute( 'type' );
				$class    = $field->getAttribute( 'class' );
				$disabled = $field->getAttribute( 'disabled' );
				$classes  = empty( $class ) ? '' : $class . ' ';
				$classes  .= empty( $disabled ) ? '' : 'bf-' . $disabled . ' ';

				switch ( $type ) {
					case 'html':
						echo '<tr id="table_row_' . $field_id . '_' . $key . '" class="' . $classes . '"><td colspan="2">';
						$field->render();
						echo '</td></tr>';
						break;
					case 'hidden':
						$field->render();
						break;
					default :
						?>
                        <tr id="table_row_<?php echo $field_id ?>_<?php echo $key ?>" class="<?php echo $classes ?>">
                            <th scope="row">
                                <label for="form_title"><?php echo $field->getLabel() ?></label>
                            </th>
                            <td>
								<?php echo $field->render() ?>
                                <p class="description"><?php echo $field->getShortDesc() ?></p>
                            </td>
                        </tr>
						<?php
						break;
				}
			}
		}
		?>
        </tbody>
    </table>
	<?php
}

/**
 * @param array $array
 * @param array $orderArray
 *
 * @return array
 */
function buddyforms_sort_array_by_Array( Array $array, Array $orderArray ) {
	$ordered = array();
	foreach ( $orderArray as $key ) {
		if ( array_key_exists( $key, $array ) ) {
			$ordered[ $key ] = $array[ $key ];
			unset( $array[ $key ] );
		}
	}

	return $ordered + $array;
}
