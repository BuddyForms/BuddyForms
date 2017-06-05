<?php

//
// Create a array of all available form builder templates
//
function buddyforms_form_builder_register_templates() {

	$buddyforms_templates['contact']['title'] = 'Contact Form';
	$buddyforms_templates['contact']['desc']  = 'Setup a simple contact form.';

	$buddyforms_templates['registration']['title'] = 'Registration Form';
	$buddyforms_templates['registration']['desc']  = 'Setup a simple registration form.';

	$buddyforms_templates['post']['title'] = 'Post Form';
	$buddyforms_templates['post']['desc']  = 'Setup a simple post form.';

	$buddyforms_templates['contact_mini']['title'] = 'Contact Form Mini';
	$buddyforms_templates['contact_mini']['desc']  = 'A minimal contact form.';

	$buddyforms_templates['enquiry']['title'] = 'Enquiry Form';
	$buddyforms_templates['enquiry']['desc']  = 'Setup a simple enquiry form.';


	return apply_filters( 'buddyforms_form_builder_templates', $buddyforms_templates );

}

//
// Template HTML Loop the array of all available form builder templates
//
function buddyforms_form_builder_templates() {

	$buddyforms_templates = buddyforms_form_builder_register_templates();

	ob_start();

	?>
	<div class="buddyforms_template">
		<h5>Choose a pre-configured form template or start a new one:</h5>

		<?php foreach ( $buddyforms_templates as $key => $template ) { ?>
			<div class="bf-3-tile">
				<h4 class="bf-tile-title"><?php echo $template['title'] ?></h4>
				<div class="bf-tile-desc">
					<p><?php echo $template['desc'] ?></p>
					<p><a href="#" class="button bf-preview"><span class="dashicons dashicons-visibility"></span> Preview</a></p>
				</div>
				<button id="btn-compile-<?php echo $key ?>" data-template="<?php echo $key ?>"
				        class="bf_wizard_types bf_form_template btn btn-primary btn-50" onclick=""><span
						class="dashicons dashicons-plus"></span> <?php echo $template['title'] ?></button>
				<!-- <a href="#" class="button bf-preview btn-50"><span class="dashicons dashicons-visibility"></span> Preview</a> -->
			</div>
		<?php } ?>

	</div>

	<?php

	$tmp = ob_get_clean();

	return $tmp;
}

//
// json string of the form export top generate the Form from template
//
add_action( 'wp_ajax_buddyforms_form_template', 'buddyforms_form_template' );
function buddyforms_form_template() {
	global $post, $buddyform;


	$post->post_type = 'buddyforms';


	switch ( $_POST['template'] ) {
		case 'contact' :
			$buddyform = json_decode( '{"form_fields":{"92f6e0cb6b":{"type":"user_first","slug":"user_first","name":"First Name","description":"","validation_error_message":"This field is required.","custom_class":""},"8ead289ca0":{"type":"user_last","slug":"user_last","name":"Last Name","description":"","validation_error_message":"This field is required.","custom_class":""},"87e0afb2d7":{"type":"user_email","slug":"user_email","name":"Email","description":"","required":["required"],"validation_error_message":"This field is required.","custom_class":""},"210ef7d8a8":{"type":"subject","slug":"subject","name":"Subject","description":"","required":["required"],"validation_error_message":"This field is required.","validation_minlength":"0","validation_maxlength":"0","custom_class":""},"0a256db3cb":{"type":"message","slug":"message","name":"Message","description":"","required":["required"],"validation_error_message":"This field is required.","validation_minlength":"0","validation_maxlength":"0","custom_class":""}},"layout":{"cords":{"92f6e0cb6b":"2","8ead289ca0":"2","87e0afb2d7":"1","210ef7d8a8":"1","0a256db3cb":"1"},"labels_layout":"inline","label_font_size":"","label_font_color":{"color":""},"label_font_style":"bold","desc_font_size":"","desc_font_color":{"color":""},"radio_button_alignment":"inline-block","checkbox_alignment":"inline-block","field_padding":"","field_background_color":{"color":""},"field_border_color":{"color":""},"field_border_width":"","field_border_radius":"","field_font_size":"","field_font_color":{"color":""},"field_placeholder_font_color":{"color":""},"field_active_background_color":{"color":""},"field_active_border_color":{"color":""},"field_active_font_color":{"color":""},"submit_text":"SEND","button_width":"blockmobile","button_alignment":"left","button_size":"xlarge","button_class":"button btn btn-primary","button_border_radius":"","button_border_width":"","button_background_color":{"color":""},"button_font_color":{"color":""},"button_border_color":{"color":""},"button_background_color_hover":{"color":""},"button_font_color_hover":{"color":""},"button_border_color_hover":{"color":""},"custom_css":""},"form_type":"contact","after_submit":"display_message","after_submission_page":"none","after_submission_url":"","after_submit_message_text":"Your message has been submitted successfully.","post_type":"bf_submissions","status":"publish","comment_status":"open","singular_name":"","attached_page":"none","edit_link":"all","list_posts_option":"list_all_form","list_posts_style":"list","mail_submissions":{"830e6d7716":{"mail_trigger_id":"830e6d7716","mail_from_name":"user_first","mail_from_name_custom":"","mail_from":"submitter","mail_from_custom":"","mail_to_address":"","mail_to":["submitter","admin"],"mail_to_cc_address":"","mail_to_bcc_address":"","mail_subject":"You Got Mail From Your Contact Form","mail_body":""}},"public_submit":["public_submit"],"public_submit_login":"above","registration":{"activation_page":"none","activation_message_from_subject":"User Account Activation Mail","activation_message_text":"Hi [user_login],\r\n\t\t\tGreat to see you come on board! Just one small step left to make your registration complete.\r\n\t\t\t<br>\r\n\t\t\t<b>Click the link below to activate your account.<\/b>\r\n\t\t\t<br>\r\n\t\t\t[activation_link]\r\n\t\t\t<br><br>\r\n\t\t\t[blog_title]\r\n\t\t","activation_message_from_name":"[blog_title]","activation_message_from_email":"[admin_email]","new_user_role":"subscriber"},"name":"Auto Draft","slug":""}', true );
			break;
		case 'contact_mini' :
			$buddyform = json_decode( '{"form_fields":{"92f6e0cb6b":{"type":"user_first","slug":"user_first","name":"Your Name","description":"","required":["required"],"validation_error_message":"This field is required.","custom_class":""},"87e0afb2d7":{"type":"user_email","slug":"user_email","name":"Your Email","description":"","required":["required"],"validation_error_message":"This field is required.","custom_class":""},"0a256db3cb":{"type":"message","slug":"message","name":"Your Message","description":"","required":["required"],"validation_error_message":"This field is required.","validation_minlength":"0","validation_maxlength":"0","custom_class":""}},"layout":{"cords":{"92f6e0cb6b":"1","87e0afb2d7":"1","0a256db3cb":"1"},"labels_layout":"inline","label_font_size":"","label_font_color":{"color":""},"label_font_style":"bold","desc_font_size":"","desc_font_color":{"color":""},"radio_button_alignment":"inline-block","checkbox_alignment":"inline-block","field_padding":"","field_background_color":{"color":""},"field_border_color":{"color":""},"field_border_width":"","field_border_radius":"","field_font_size":"","field_font_color":{"color":""},"field_placeholder_font_color":{"color":""},"field_active_background_color":{"color":""},"field_active_border_color":{"color":""},"field_active_font_color":{"color":""},"submit_text":"SEND","button_width":"blockmobile","button_alignment":"left","button_size":"xlarge","button_class":"button btn btn-primary","button_border_radius":"","button_border_width":"","button_background_color":{"color":""},"button_font_color":{"color":""},"button_border_color":{"color":""},"button_background_color_hover":{"color":""},"button_font_color_hover":{"color":""},"button_border_color_hover":{"color":""},"custom_css":""},"form_type":"contact","after_submit":"display_message","after_submission_page":"none","after_submission_url":"","after_submit_message_text":"Thanks! Your message is on the way! ","post_type":"bf_submissions","status":"publish","comment_status":"open","singular_name":"","attached_page":"none","edit_link":"all","list_posts_option":"list_all_form","list_posts_style":"list","mail_submissions":{"e8818ef3a1":{"mail_trigger_id":"e8818ef3a1","mail_from_name":"user_first","mail_from_name_custom":"","mail_from":"submitter","mail_from_custom":"","mail_to_address":"","mail_to":["submitter","admin"],"mail_to_cc_address":"","mail_to_bcc_address":"","mail_subject":"Contact Form Submission","mail_body":""}},"public_submit":["public_submit"],"public_submit_login":"above","registration":{"activation_page":"none","activation_message_from_subject":"User Account Activation Mail","activation_message_text":"Hi [user_login],\r\n\t\t\tGreat to see you come on board! Just one small step left to make your registration complete.\r\n\t\t\t<br>\r\n\t\t\t<b>Click the link below to activate your account.<\/b>\r\n\t\t\t<br>\r\n\t\t\t[activation_link]\r\n\t\t\t<br><br>\r\n\t\t\t[blog_title]\r\n\t\t","activation_message_from_name":"[blog_title]","activation_message_from_email":"[admin_email]","new_user_role":"subscriber"},"name":"Contact Mini","slug":"contact-mini"}', true );
			break;
    case 'enquiry' :
        $buddyform = json_decode( '{"form_fields":{"92f6e0cb6b":{"type":"user_first","slug":"user_first","name":"Your Name","description":"","required":["required"],"validation_error_message":"This field is required.","custom_class":""},"87e0afb2d7":{"type":"user_email","slug":"user_email","name":"Your Email","description":"","required":["required"],"validation_error_message":"This field is required.","custom_class":""},"0a256db3cb":{"type":"message","slug":"message","name":"What Do You Need? ","description":"","required":["required"],"validation_error_message":"This field is required.","validation_minlength":"0","validation_maxlength":"0","custom_class":""}},"layout":{"cords":{"92f6e0cb6b":"1","87e0afb2d7":"1","0a256db3cb":"1"},"labels_layout":"inline","label_font_size":"","label_font_color":{"color":""},"label_font_style":"bold","desc_font_size":"","desc_font_color":{"color":""},"field_padding":"","field_background_color":{"color":""},"field_border_color":{"color":""},"field_border_width":"","field_border_radius":"","field_font_size":"","field_font_color":{"color":""},"field_placeholder_font_color":{"color":""},"field_active_background_color":{"color":""},"field_active_border_color":{"color":""},"field_active_font_color":{"color":""},"submit_text":"SEND","button_width":"blockmobile","button_alignment":"left","button_size":"xlarge","button_class":"button btn btn-primary","button_border_radius":"","button_border_width":"","button_background_color":{"color":""},"button_font_color":{"color":""},"button_border_color":{"color":""},"button_background_color_hover":{"color":""},"button_font_color_hover":{"color":""},"button_border_color_hover":{"color":""},"custom_css":""},"form_type":"contact","after_submit":"display_message","after_submission_page":"none","after_submission_url":"","after_submit_message_text":"Thanks! Your message is on the way! ","post_type":"bf_submissions","status":"publish","comment_status":"open","singular_name":"","attached_page":"none","edit_link":"all","list_posts_option":"list_all_form","list_posts_style":"list","mail_submissions":{"e8818ef3a1":{"mail_trigger_id":"e8818ef3a1","mail_from_name":"user_first","mail_from_name_custom":"","mail_from":"submitter","mail_from_custom":"","mail_to_address":"","mail_to":["submitter","admin"],"mail_to_cc_address":"","mail_to_bcc_address":"","mail_subject":"Enquiry Form Submission","mail_body":""}},"public_submit":["public_submit"],"public_submit_login":"above","registration":{"activation_page":"none","activation_message_from_subject":"User Account Activation Mail","activation_message_text":"Hi [user_login],\r\n\t\t\tGreat to see you come on board! Just one small step left to make your registration complete.\r\n\t\t\t<br>\r\n\t\t\t<b>Click the link below to activate your account.<\/b>\r\n\t\t\t<br>\r\n\t\t\t[activation_link]\r\n\t\t\t<br><br>\r\n\t\t\t[blog_title]\r\n\t\t","activation_message_from_name":"[blog_title]","activation_message_from_email":"[admin_email]","new_user_role":"subscriber"},"name":"Enquiry","slug":"enquiry"}', true );
        break;
		case 'registration' :
			$buddyform = json_decode( '{"form_fields":{"a40912e1a5":{"type":"user_login","slug":"user_login","name":"Username","description":"","required":["required"],"validation_error_message":"This field is required."},"82abe39ed2":{"type":"user_email","slug":"user_email","name":"eMail","description":"","required":["required"],"validation_error_message":"This field is required."},"611dc33cb2":{"type":"user_pass","slug":"user_pass","name":"Password","description":"","required":["required"],"validation_error_message":"This field is required."}},"form_type":"registration","after_submit":"display_message","after_submission_page":"none","after_submission_url":"","after_submit_message_text":"User Registration Successful! Please check your eMail Inbox and click the activation link to activate your account.","post_type":"bf_submissions","status":"publish","comment_status":"open","singular_name":"","attached_page":"none","edit_link":"all","list_posts_option":"list_all_form","list_posts_style":"list","public_submit":["public_submit"],"public_submit_login":"above","registration":{"activation_page":"none","activation_message_from_subject":"User Account Activation Mail","activation_message_text":"Hi [user_login],\r\n\r\nGreat to see you come on board! Just one small step left to make your registration complete.\r\n<br>\r\n<b>Click the link below to activate your account.<\/b>\r\n<br>\r\n[activation_link]\r\n<br><br>\r\n[blog_title]","activation_message_from_name":"[blog_title]","activation_message_from_email":"dfg@dfg.fr","new_user_role":"author"},"moderation_logic":"default","moderation":{"label_submit":"Submit","label_save":"Save","label_review":"Submit for moderation","label_new_draft":"Create new Draft","label_no_edit":"This Post is waiting for approval and can not be changed until it gets approved"},"name":"Auto Draft","slug":""}', true );
			break;
		case 'post' :
			$buddyform = json_decode( '{"form_fields":{"51836a88da":{"type":"title","slug":"buddyforms_form_title","name":"Title","description":"","validation_error_message":"This field is required.","validation_minlength":"0","validation_maxlength":"","custom_class":""},"27ff0af6c6":{"type":"content","slug":"buddyforms_form_content","name":"Content","description":"","validation_error_message":"This field is required.","validation_minlength":"0","validation_maxlength":"0","custom_class":""}},"form_type":"post","after_submit":"display_message","after_submission_page":"none","after_submission_url":"","after_submit_message_text":"Your Message has been Submitted Successfully","post_type":"post","status":"publish","comment_status":"open","singular_name":"","attached_page":"none","edit_link":"all","list_posts_option":"list_all_form","list_posts_style":"list","mail_submissions":{"03aa1b8b80":{"mail_trigger_id":"03aa1b8b80","mail_from_name":"eMail Notification","mail_to_address":"","mail_from":"mail@sven-lehnert.de","mail_to_cc_address":"","mail_to_bcc_address":"","mail_subject":"Form Submission Notification","mail_body":""}},"public_submit":["public_submit"],"public_submit_login":"above","registration":{"activation_page":"none","activation_message_from_subject":"User Account Activation Mail","activation_message_text":"Hi [user_login],\r\n\t\t\tGreat to see you come on board! Just one small step left to make your registration complete.\r\n\t\t\t<br>\r\n\t\t\t<b>Click the link below to activate your account.<\/b>\r\n\t\t\t<br>\r\n\t\t\t[activation_link]\r\n\t\t\t<br><br>\r\n\t\t\t[blog_title]\r\n\t\t","activation_message_from_name":"[blog_title]","activation_message_from_email":"[admin_email]","new_user_role":"subscriber"},"name":"Posts","slug":""}', true );
			break;
		default :
			$buddyform = json_decode( apply_filters( 'buddyforms_form_builder_templates_json', $buddyform ), true );
			break;
	}

	ob_start();
	buddyforms_metabox_form_elements( $post, $buddyform );
	$formbuilder = ob_get_clean();

	// Add the form elements to the form builder
	$json['formbuilder'] = $formbuilder;


	ob_start();

	?>
    <div class="buddyforms_accordion_notification">
        <div class="hidden bf-hidden"><?php wp_editor( 'dummy', 'dummy' ); ?></div>


		<?php buddyforms_mail_notification_screen() ?>

        <div class="bf_show_if_f_type_post bf_hide_if_post_type_none">
			<?php buddyforms_post_status_mail_notification_screen() ?>
        </div>


    </div>
    <?php
	$mail_notification = ob_get_clean();

	$json['mail_notification'] = $mail_notification;

	// Unset the form fields
	unset( $buddyform['form_fields'] );
	unset( $buddyform['mail_submissions'] );

	// Add the form setup to the json
	$json['form_setup'] = $buddyform;

	echo json_encode( $json );
	die();
}
