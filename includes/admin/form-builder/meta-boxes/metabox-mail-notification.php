<?php
function buddyforms_mail_notification_screen() {
	global $post, $buddyform;

	if ( ! $buddyform ) {
		$buddyform = get_post_meta( $post->ID, '_buddyforms_options', true );
	}
	$form_setup = array();

	//$form_setup[] = new Element_HTML( '<a class="button-primary btn btn-primary" href="#" id="mail_notification_add_new">' . __( 'Create New Mail Notification', 'buddyforms' ) . '</a>' );

	$form_setup[] = new Element_HTML( '<h4>' . __( 'Mail Notifications', 'buddyforms' ) . '</h4>
		<p>' . __( 'By default no notification is sent out. Any submission get stored under Submissions. This makes sure you never lose any submission. Of course you can create individual mail notification for the submitter, inform your moderators or sent out a notification to any email address.', 'buddyforms' ) . '</p>
		<a class="button-primary btn btn-primary" href="#" id="mail_notification_add_new">' . __( 'Create New Mail Notification', 'buddyforms' ) . '</a><br><br><br>' );

	buddyforms_display_field_group_table( $form_setup );

	echo '<ul>';
	if ( isset( $buddyform['mail_submissions'] ) ) {
		foreach ( $buddyform['mail_submissions'] as $key => $mail_submission ) {
			buddyforms_mail_notification_form( $key );
		}
	} else {
		echo '<div id="no-trigger-mailcontainer">' . __( 'No Mail Notification Trigger so far.' ) . '</div>';
	}
	echo '<div id="mailcontainer"></div>';
	echo '</ul>';
	echo '<hr>';
}

function buddyforms_post_status_mail_notification_screen() {
	global $post, $buddyform;

	if ( ! $buddyform ) {
		$buddyform = get_post_meta( $post->ID, '_buddyforms_options', true );
	}

	$form_setup = array();

	$shortDesc = '<a class="button-primary btn btn-primary" href="#" id="post_status_mail_notification_add_new">' . __( 'Create New Post Status Change Mail Notification', 'buddyforms' ) . '</a>';
	if ( buddyforms_core_fs()->is_not_paying() && ! buddyforms_core_fs()->is_trial() ) {
		$shortDesc = '<b>' . __( 'Get the Pro version to add Post Status Change Mail Notification', 'buddyforms' ) . '</b>';
	}

	$element = new Element_Select( '<h4>' . __( "Post Status Change Mail Notifications", 'buddyforms' ) . '</h4><p>' . __( 'Forms can send different email notifications triggered by post status changes. For example, automatically notify post authors when their post is published! ', 'buddyforms' ) . '</p>', "buddyforms_notification_trigger", buddyforms_get_post_status_array(), array(
		'class'     => 'post_status_mail_notification_trigger',
		'shortDesc' => $shortDesc,
	) );
	if ( buddyforms_core_fs()->is_not_paying() && ! buddyforms_core_fs()->is_trial() ) {
		$element->setAttribute( 'disabled', 'disabled' );
	}
	$form_setup[] = $element;
	buddyforms_display_field_group_table( $form_setup );
	echo '<ul>';
	if ( isset( $buddyform['mail_notification'] ) ) {
		foreach ( $buddyform['mail_notification'] as $key => $value ) {
			buddyforms_new_post_status_mail_notification_form( $buddyform['mail_notification'][ $key ]['mail_trigger'] );
		}
	} else {
		echo '<div id="no-trigger-post-status-mail-container">' . __( 'No Post Status Mail Notification Trigger so far.' ) . '</div>';
	}
	echo '<div id="post-status-mail-container"></div>';
	echo '</ul>';
	echo '<hr>';

}

/**
 * @param $trigger
 *
 * @return string
 */
function buddyforms_mail_notification_form( $trigger = false ) {
	global $buddyform;

	if ( $trigger == false ) {
		$trigger = substr( md5( time() * rand() ), 0, 10 );
	}

	$shortDesc = sprintf("<br><h4>%s</h4><p>%s</p>", __( 'User Shortcodes', 'buddyforms' ), __( 'You can use any form element slug as shortcode [field_slug].', 'buddyforms' ));

	$form_setup[] = new Element_Hidden( "buddyforms_options[mail_submissions][" . $trigger . "][mail_trigger_id]", $trigger, array( 'class' => 'trigger' . $trigger ) );

	// From Name
	$element      = new Element_Radio( '<b>' . __( 'From Name', 'buddyforms' ) . '</b>', "buddyforms_options[mail_submissions][" . $trigger . "][mail_from_name]", array(
		'user_login'      => __( 'Username', 'buddyforms' ),
		'user_first'      => __( 'First Name', 'buddyforms' ),
		'user_last'       => __( 'Last Name', 'buddyforms' ),
		'user_first_last' => __( 'First and Last Name', 'buddyforms' ),
		'blog_title'      => __( 'Blog Title', 'buddyforms' ),
		'custom'          => __( 'Custom', 'buddyforms' )
	), array(
		'value' => isset( $buddyform['mail_submissions'][ $trigger ]['mail_from_name'] ) ? $buddyform['mail_submissions'][ $trigger ]['mail_from_name'] : 'blog_title',
		'class' => 'mail_from_checkbox bf_mail_from_name_multi_checkbox',
	) );
	$form_setup[] = $element;

	$mail_to_cc   = isset( $buddyform['mail_submissions'][ $trigger ]['mail_from_name'] ) && $buddyform['mail_submissions'][ $trigger ]['mail_from_name'] == 'custom' ? '' : 'hidden';
	$form_setup[] = new Element_Textbox( '<b>' . __( "Custom Mail From Name", 'buddyforms' ) . '</b>', "buddyforms_options[mail_submissions][" . $trigger . "][mail_from_name_custom]", array(
		"class"     => 'mail_from_name_custom ' . $mail_to_cc,
		'value'     => isset( $buddyform['mail_submissions'][ $trigger ]['mail_from_name_custom'] ) ? $buddyform['mail_submissions'][ $trigger ]['mail_from_name_custom'] : '',
		'shortDesc' => __( 'The senders name e.g. John Doe or use any form element slug as shortcode [field_slug]', 'buddyforms' )
	) );


	// From eMail
	$element      = new Element_Radio( '<b>' . __( 'From eMail', 'buddyforms' ) . '</b>', "buddyforms_options[mail_submissions][" . $trigger . "][mail_from]", array(
		'submitter' => __( 'Submitter - User eMail Field', 'buddyforms' ),
		'admin'     => __( 'Admin - eMail from WP General Settings', 'buddyforms' ),
		'custom'    => __( 'Custom', 'buddyforms' )
	), array(
		'value' => isset( $buddyform['mail_submissions'][ $trigger ]['mail_from'] ) ? $buddyform['mail_submissions'][ $trigger ]['mail_from'] : 'submitter',
		'class' => 'mail_from_checkbox bf_mail_from_multi_checkbox'
	) );
	$form_setup[] = $element;

	$mail_to_cc   = isset( $buddyform['mail_submissions'][ $trigger ]['mail_from'] ) && $buddyform['mail_submissions'][ $trigger ]['mail_from'] == 'custom' ? '' : 'hidden';
	$form_setup[] = new Element_Textbox( '<b>' . __( "Custom Mail From Address", 'buddyforms' ) . '</b>', "buddyforms_options[mail_submissions][" . $trigger . "][mail_from_custom]", array(
		"class" => 'mail_from_custom ' . $mail_to_cc,
		'value' => isset( $buddyform['mail_submissions'][ $trigger ]['mail_from_custom'] ) ? $buddyform['mail_submissions'][ $trigger ]['mail_from_custom'] : '',
        'shortDesc' => __( 'You can use any form element slug as shortcode [field_slug]', 'buddyforms' )
	) );

	$element      = new Element_Checkbox( '<b>' . __( 'Sent mail to', 'buddyforms' ) . '</b>', "buddyforms_options[mail_submissions][" . $trigger . "][mail_to]", array(
		'submitter' => __( 'Submitter - User eMail Field', 'buddyforms' ),
		'admin'     => __( 'Admin - eMail from WP General Settings', 'buddyforms' ),
		'cc'        => __( 'CC', 'buddyforms' ),
		'bcc'       => __( 'BCC', 'buddyforms' )
	), array(
		'value' => isset( $buddyform['mail_submissions'][ $trigger ]['mail_to'] ) ? $buddyform['mail_submissions'][ $trigger ]['mail_to'] : 'admin',
		'id'    => 'mail_submissions' . $trigger,
		'class' => 'mail_to_checkbox bf_sent_mail_to_multi_checkbox'
	) );
	$form_setup[] = $element;

	$mail_to_cc = isset( $buddyform['mail_submissions'][ $trigger ]['mail_to'] ) && in_array( 'cc', $buddyform['mail_submissions'][ $trigger ]['mail_to'] ) ? '' : 'hidden';
	$attrs      = array(
		"class"    => 'mail_to_cc_address ' . $mail_to_cc,
		'value'    => isset( $buddyform['mail_submissions'][ $trigger ]['mail_to_cc_address'] ) ? $buddyform['mail_submissions'][ $trigger ]['mail_to_cc_address'] : '',
        'shortDesc' => __( 'Separate addresses by "," and/or use any form element slug as shortcode [field_slug]', 'buddyforms' )
	);
	if ( empty( $mail_to_cc ) ) {
		$attrs['required'] = 1;
	}
	$form_setup[] = new Element_Textbox( '<b>' . __( "CC", 'buddyforms' ) . '</b>', "buddyforms_options[mail_submissions][" . $trigger . "][mail_to_cc_address]", $attrs );

	$mail_to_bcc = isset( $buddyform['mail_submissions'][ $trigger ]['mail_to'] ) && in_array( 'bcc', $buddyform['mail_submissions'][ $trigger ]['mail_to'] ) ? '' : 'hidden';
	$attrs       = array(
		"class" => 'mail_to_bcc_address ' . $mail_to_bcc,
		'value' => isset( $buddyform['mail_submissions'][ $trigger ]['mail_to_bcc_address'] ) ? $buddyform['mail_submissions'][ $trigger ]['mail_to_bcc_address'] : '',
        'shortDesc' => __( 'Separate addresses by "," and/or use any form element slug as shortcode [field_slug]', 'buddyforms' )
	);
	if ( empty( $mail_to_bcc ) ) {
		$attrs['required'] = 1;
	}
	$form_setup[] = new Element_Textbox( '<b>' . __( "BCC", 'buddyforms' ) . '</b>', "buddyforms_options[mail_submissions][" . $trigger . "][mail_to_bcc_address]", $attrs );

	// to eMail
	$form_setup[] = new Element_Textbox( '<b>' . __( "Custom sent mail to", 'buddyforms' ) . '</b>', "buddyforms_options[mail_submissions][" . $trigger . "][mail_to_address]", array(
		"class"     => "bf-mail-field",
		'value'     => isset( $buddyform['mail_submissions'][ $trigger ]['mail_to_address'] ) ? $buddyform['mail_submissions'][ $trigger ]['mail_to_address'] : '',
		'shortDesc' => __( 'Separate addresses by "," and/or use any form element slug as shortcode [field_slug]', 'buddyforms' )
	) );

	// Subject
	$form_setup[] = new Element_Textbox( '<b>' . __( "Subject", 'buddyforms' ) . '</b>', "buddyforms_options[mail_submissions][" . $trigger . "][mail_subject]", array(
		"class"     => "bf-mail-field",
		'value'     => isset( $buddyform['mail_submissions'][ $trigger ]['mail_subject'] ) ? $buddyform['mail_submissions'][ $trigger ]['mail_subject'] : __( 'Form Submission Notification', 'buddyforms' ),
		'required'  => 1,
		'shortDesc' => __( 'Add a default Subject. If you use the "subject" form element the element value will be used, or use a [field_slug]', 'buddyforms' )
	) );

	ob_start();
	$settings = array(
		'textarea_name' => 'buddyforms_options[mail_submissions][' . $trigger . '][mail_body]',
		'wpautop'       => true,
		'media_buttons' => false,
		'tinymce'       => true,
		'quicktags'     => true,
		'textarea_rows' => 18
	);
	wp_editor( isset( $buddyform['mail_submissions'][ $trigger ]['mail_body'] ) ? $buddyform['mail_submissions'][ $trigger ]['mail_body'] : '', "bf_mail_body" . $trigger, $settings );
	$wp_editor    = ob_get_clean();
	$wp_editor    = '<div class="bf_field_group bf_form_content">
	<label for="form_title"><b>' . __( 'eMail Message Content', 'buddyforms' ) . '</b><br>

		<p><strong>'. __( 'Important: ', 'buddyforms' ).'</strong>'. __( 'If you use the "Message" form element you can leave this field empty and the "Message" form element value will be used . If you enter content in here, this content will overwrite the "Message" form element .', 'buddyforms' ).'</p>
		<p>'.__( 'You can add any form element with tags [] e . g . [ message ] will be replaced with the form element "Message" [ form_elements_table ] will add a table of all form elements .', 'buddyforms' ).'</p>
		<p>'. __( 'If no "Message" form element is uses and "no content" is added a table with all form elements will get auto generated .', 'buddyforms' ).'</p>
	</label>
	<div class="bf_inputs bf-texteditor">' . $wp_editor . '</div></div>';
	$form_setup[] = new Element_HTML( $wp_editor . $shortDesc );
	?>
    <li id="trigger<?php echo $trigger ?>" class="bf_trigger_list_item <?php echo $trigger ?>">
        <div class="accordion_fields">
            <div class="accordion-group">
                <div class="accordion-heading-options">
                    <table class="wp-list-table widefat fixed posts">
                        <tbody>
                        <tr>
                            <td class="field_order ui-sortable-handle">
                                <span class="circle">1</span>
                            </td>
                            <td class="field_label">
                                <strong>
                                    <a class="bf_edit_field row-title accordion-toggle collapsed" data-toggle="collapse"
                                       data-parent="#accordion_text" href="#accordion_<?php echo $trigger ?>"
                                       title="Edit this Field"
                                       href="#"><?php echo isset( $buddyform['mail_submissions'][ $trigger ]['mail_subject'] ) ? $buddyform['mail_submissions'][ $trigger ]['mail_subject'] : ''; ?></a>
                                </strong>

                            </td>
                            <td class="field_delete">
                                <span><a class="accordion-toggle collapsed" data-toggle="collapse"
                                         data-parent="#accordion_text" href="#accordion_<?php echo $trigger ?>"
                                         title="<?php _e( 'Edit this Field', 'buddyforms' ) ?>" href="javascript:;"><?php _e( 'Edit', 'buddyforms' ) ?></a> | </span>
                                <span><a class="bf_delete_trigger" id="<?php echo $trigger ?>" title="<?php _e( 'Delete this Field', 'buddyforms' ) ?>"
                                         href="javascript:;"><?php _e( 'Delete', 'buddyforms' ) ?></a></span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div id="accordion_<?php echo $trigger ?>"
                     class="accordion-body <?php if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
					     echo 'in';
				     } ?> collapse">
                    <div class="accordion-inner">
						<?php buddyforms_display_field_group_table( $form_setup, $trigger ) ?>
                    </div>
                </div>
            </div>
        </div>
    </li>

	<?php
	return $trigger;
}

/**
 * @param $trigger
 */
function buddyforms_new_post_status_mail_notification_form( $trigger ) {
	global $post;

	if ( isset( $post->ID ) ) {
		$buddyform = get_post_meta( $post->ID, '_buddyforms_options', true );
	}

	$shortDesc = "
    <br>
    <h4>". __('User Shortcodes', 'buddyforms')."</h4>
    <ul>
        <li><p><b>[user_login] </b>Username</p></li>
        <li><p><b>[user_nicename] </b>". __( 'user_nicename is a url - sanitized version of user_login . For example, if a user’s login is user@example . com, their user_nicename will be userexample - com .', 'buddyforms' )."</p></li>
        <li><p><b>[user_email]</b> ". __( 'user email', 'buddyforms' )."</p></li>
        <li><p><b>[first_name]</b> ". __( 'user first name', 'buddyforms' )."</p></li>
        <li><p><b>[last_name] </b> ". __( 'user last name', 'buddyforms' )."</p></li>
    </ul>
    <h4>". __( 'Published Post Shortcodes', 'buddyforms' )."</h4>
    <ul>
        <li><p><b>[published_post_link_html]</b> ". __( 'the published post link in html', 'buddyforms' )."</p></li>
        <li><p><b>[published_post_link_plain]</b> ". __( 'the published post link in plain', 'buddyforms' )."</p></li>
        <li><p><b>[published_post_title]</b> ". __( 'the published post title', 'buddyforms' )."</p></li>
    </ul>
    <h4>". __( 'Site Shortcodes', 'buddyforms' )."</h4>
    <ul>
        <li><p><b>[site_name]</b> ". __( 'the site name', 'buddyforms' )." </p></li>
        <li><p><b>[site_url]</b> ". __( 'the site url', 'buddyforms' )."</p></li>
        <li><p><b>[site_url_html]</b> ". __( 'the site url in html', 'buddyforms' )."</p></li>
    </ul>
        ";

	$form_setup[] = new Element_Hidden( "buddyforms_options[mail_notification][" . $trigger . "][mail_trigger]", $trigger, array( 'class' => 'trigger' . $trigger ) );


	$form_setup[] = new Element_Textbox( '<b>' . __( "Name", 'buddyforms' ) . '</b>', "buddyforms_options[mail_notification][" . $trigger . "][mail_from_name]", array(
		'value'     => isset( $buddyform['mail_notification'][ $trigger ]['mail_from_name'] ) ? $buddyform['mail_notification'][ $trigger ]['mail_from_name'] : '',
		'required'  => 1,
		'shortDesc' => __( 'The senders name e.g. John Doe or use any form element slug as shortcode [field_slug]', 'buddyforms' )
	) );
	$form_setup[] = new Element_Textbox( '<b>' . __( "Email", 'buddyforms' ) . '</b>', "buddyforms_options[mail_notification][" . $trigger . "][mail_from]", array(
		'value'     => isset( $buddyform['mail_notification'][ $trigger ]['mail_from'] ) ? $buddyform['mail_notification'][ $trigger ]['mail_from'] : '',
		'required'  => 1,
		'shortDesc' => __( 'The senders email e.g. user@domain.com or use any form element slug as shortcode [field_slug]', 'buddyforms' )
	) );

	$form_setup[] = new Element_Checkbox( '<b>' . __( 'Sent mail to', 'buddyforms' ) . '</b>', "buddyforms_options[mail_notification][" . $trigger . "][mail_to]", array(
		'author' => __( 'The Post Author', 'buddyforms' ),
		'admin'  => __( 'Admin E-mail Address from Settings/General', 'buddyforms' )
	), array(
		'value'  => isset( $buddyform['mail_notification'][ $trigger ]['mail_to'] ) ? $buddyform['mail_notification'][ $trigger ]['mail_to'] : '',
		'inline' => 0
	) );
	$form_setup[] = new Element_Textbox( '<b>' . __( "Send Mail To", 'buddyforms' ) . '</b>', "buddyforms_options[mail_notification][" . $trigger . "][mail_to_address]", array(
		"class" => "bf-mail-field",
		'value' => isset( $buddyform['mail_notification'][ $trigger ]['mail_to_address'] ) ? $buddyform['mail_notification'][ $trigger ]['mail_to_address'] : '',
        'shortDesc' => __( 'Separate addresses by "," and/or use any form element slug as shortcode [field_slug]', 'buddyforms' )
	) );

	$form_setup[] = new Element_Textbox( '<b>' . __( 'Subject', 'buddyforms' ) . '</b>', "buddyforms_options[mail_notification][" . $trigger . "][mail_subject]", array(
		"class"    => "bf-mail-field",
		'value'    => isset( $buddyform['mail_notification'][ $trigger ]['mail_subject'] ) ? $buddyform['mail_notification'][ $trigger ]['mail_subject'] : '',
		'shortDesc' => __( 'Add a default Subject. If you use the "subject" form element the element value will be used, or use a [field_slug]', 'buddyforms' ),
		'required' => 1
	) );

	ob_start();
	$settings = array(
		'textarea_name' => 'buddyforms_options[mail_notification][' . $trigger . '][mail_body]',
		'wpautop'       => true,
		'media_buttons' => false,
		'tinymce'       => true,
		'quicktags'     => true,
		'textarea_rows' => 18
	);
	wp_editor( isset( $buddyform['mail_notification'][ $trigger ]['mail_body'] ) ? $buddyform['mail_notification'][ $trigger ]['mail_body'] : '', "bf_mail_body" . $trigger, $settings );
	$wp_editor    = ob_get_clean();
	$wp_editor    = '<div class="bf_field_group bf_form_content"><label><h2>' . __( 'Content', 'buddyforms' ) . '</h2></label><div class="bf_inputs">' . $wp_editor . '</div></div>';
	$form_setup[] = new Element_HTML( $wp_editor . $shortDesc );
	?>

    <li id="trigger<?php echo $trigger ?>" class="bf_trigger_list_item <?php echo $trigger ?>">
        <div class="accordion_fields">
            <div class="accordion-group">
                <div class="accordion-heading-options">
                    <table class="wp-list-table widefat fixed posts">
                        <tbody>
                        <tr>
                            <td class="field_order ui-sortable-handle">
                                <span class="circle">1</span>
                            </td>
                            <td class="field_label">
                                <strong>
                                    <a class="bf_edit_field row-title accordion-toggle collapsed" data-toggle="collapse"
                                       data-parent="#accordion_text" href="#accordion_<?php echo $trigger ?>"
                                       title="Edit this Field" href="#"><?php echo $trigger ?></a>
                                </strong>

                            </td>
                            <td class="field_delete">
                                <span><a class="accordion-toggle collapsed" data-toggle="collapse"
                                         data-parent="#accordion_text" href="#accordion_<?php echo $trigger ?>"
                                         title="<?php _e( 'Edit this Field', 'buddyforms' ) ?>" href="javascript:;"><?php _e( 'Edit', 'buddyforms' ) ?></a> | </span>
                                <span><a class="bf_delete_trigger" id="<?php echo $trigger ?>" title="<?php _e( 'Delete this Field', 'buddyforms' ) ?>"
                                         href="javascript:;"><?php _e( 'Delete', 'buddyforms' ) ?></a></span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div id="accordion_<?php echo $trigger ?>" class="accordion-body collapse">
                    <div class="accordion-inner">
						<?php buddyforms_display_field_group_table( $form_setup, $trigger ) ?>
                    </div>
                </div>
            </div>
        </div>
    </li>

	<?php
}

function buddyforms_new_mail_notification() {

	ob_start();
	$trigger_id   = buddyforms_mail_notification_form();
	$trigger_html = ob_get_clean();

	$json['trigger_id'] = $trigger_id;
	$json['html']       = $trigger_html;

	echo json_encode( $json );
	die();
}

add_action( 'wp_ajax_buddyforms_new_mail_notification', 'buddyforms_new_mail_notification' );


/**
 * @return bool
 */
function buddyforms_new_post_status_mail_notification() {

	$trigger = $_POST['trigger'];

	if ( isset( $trigger, $buddyform['mail_notification'][ $trigger ] ) ) {
		return false;
	}

	buddyforms_new_post_status_mail_notification_form( $trigger );
	die();
}

add_action( 'wp_ajax_buddyforms_new_post_status_mail_notification', 'buddyforms_new_post_status_mail_notification' );


function buddyforms_form_setup_nav_li_notification() { ?>
    <li class="notifications_nav"><a
            href="#notification"
            data-toggle="tab"><?php _e( 'Notifications', 'buddyforms' ); ?></a>
    </li><?php
}

add_action( 'buddyforms_form_setup_nav_li_last', 'buddyforms_form_setup_nav_li_notification' );

function buddyforms_form_setup_tab_pane_notification() { ?>
    <div class="tab-pane fade in" id="notification">
    <div class="buddyforms_accordion_notification">
        <div class="hidden bf-hidden"><?php wp_editor( 'dummy', 'dummy' ); ?></div>

		<?php buddyforms_mail_notification_screen() ?>

        <div class="bf_show_if_f_type_post bf_hide_if_post_type_none">
			<?php buddyforms_post_status_mail_notification_screen() ?>
        </div>


    </div>
    </div><?php
}

add_action( 'buddyforms_form_setup_tab_pane_last', 'buddyforms_form_setup_tab_pane_notification' );
