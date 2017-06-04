<?php

//
// Create a array of all available form builder templates
//
function buddyforms_form_builder_register_templates() {

	$buddyforms_templates['contact']['title'] = 'Contact Form';
	$buddyforms_templates['contact']['desc']  = 'Setup a simple contact form.';

	$buddyforms_templates['contact2']['title'] = 'Contact Form Konrad';
	$buddyforms_templates['contact2']['desc']  = 'Setup a simple Konrad contact form.';

	$buddyforms_templates['registration']['title'] = 'Registration Form';
	$buddyforms_templates['registration']['desc']  = 'Setup a simple registration form.';

	$buddyforms_templates['post']['title'] = 'Post Form';
	$buddyforms_templates['post']['desc']  = 'Setup a simple post form.';

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
				<p class="bf-tile-desc"><?php echo $template['desc'] ?></p>
				<button id="btn-compile-<?php echo $key ?>" data-template="<?php echo $key ?>"
				        class="bf_wizard_types bf_form_template btn" onclick=""><span
						class="bf-plus">+</span> <?php echo $template['title'] ?></button>
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
			$buddyform = json_decode( '{"form_fields":{"92f6e0cb6b":{"type":"user_first","slug":"user_first","name":"First Name","description":"","validation_error_message":"This field is required."},"8ead289ca0":{"type":"user_last","slug":"user_last","name":"Last Name","description":"","validation_error_message":"This field is required."},"87e0afb2d7":{"type":"user_email","slug":"user_email","name":"eMail","description":"","required":["required"],"validation_error_message":"This field is required."},"210ef7d8a8":{"type":"subject","slug":"subject","name":"Subject","description":"","required":["required"],"validation_error_message":"This field is required.","validation_minlength":"0","validation_maxlength":"0"},"0a256db3cb":{"type":"message","slug":"message","name":"Message","description":"","required":["required"],"validation_error_message":"This field is required.","validation_minlength":"0","validation_maxlength":"0"}},"form_type":"contact","after_submit":"display_message","after_submission_page":"none","after_submission_url":"","after_submit_message_text":"Your Message has been Submitted Successfully","post_type":"bf_submissions","status":"publish","comment_status":"open","singular_name":"","attached_page":"none","edit_link":"all","list_posts_option":"list_all_form","list_posts_style":"list","public_submit":["public_submit"],"public_submit_login":"above","registration":{"activation_page":"none","activation_message_from_subject":"User Account Activation Mail","activation_message_text":"Hi [user_login],\r\n\t\t\tGreat to see you come on board! Just one small step left to make your registration complete.\r\n\t\t\t<br>\r\n\t\t\t<b>Click the link below to activate your account.<\/b>\r\n\t\t\t<br>\r\n\t\t\t[activation_link]\r\n\t\t\t<br><br>\r\n\t\t\t[blog_title]\r\n\t\t","activation_message_from_name":"[blog_title]","activation_message_from_email":"[admin_email]","new_user_role":"subscriber"},"moderation_logic":"default","moderation":{"label_submit":"Submit","label_save":"Save","label_review":"Submit for moderation","label_new_draft":"Create new Draft","label_no_edit":"This Post is waiting for approval and can not be changed until it gets approved"},"name":"ssaSAS","slug":""}', true );
			break;
		case 'contact2' :
			$buddyform = json_decode( '{"form_fields":{"a40912e1a5":{"type":"user_login","slug":"user_login","name":"Username","description":"","required":["required"],"validation_error_message":"This field is required.","custom_class":""},"82abe39ed2":{"type":"user_email","slug":"user_email","name":"eMail","description":"","required":["required"],"validation_error_message":"This field is required.","custom_class":""},"611dc33cb2":{"type":"user_pass","slug":"user_pass","name":"Password","description":"","required":["required"],"validation_error_message":"This field is required.","custom_class":""},"636c12a746":{"type":"text","name":"Shop Name","description":"","validation_error_message":"This field is required.","validation_minlength":"0","validation_maxlength":"0","slug":"pv_shop_name","custom_class":""},"dfc114e960":{"type":"text","name":"PayPal E-mail (required)","description":"","required":["required"],"validation_error_message":"This field is required.","validation_minlength":"0","validation_maxlength":"0","slug":"pv_paypal","custom_class":""},"df44e14ace":{"type":"textarea","name":"Seller Info","description":"","validation_error_message":"This field is required.","validation_minlength":"0","validation_maxlength":"0","slug":"pv_seller_info","custom_class":"","generate_textarea":""},"fce05b6cd3":{"type":"textarea","name":"Shop description","description":"","validation_error_message":"This field is required.","validation_minlength":"0","validation_maxlength":"0","slug":"pv_shop_description","custom_class":"","generate_textarea":""}},"layout":{"cords":{"a40912e1a5":"1","82abe39ed2":"1","611dc33cb2":"1","636c12a746":"1","dfc114e960":"1","df44e14ace":"1","fce05b6cd3":"1"},"labels_layout":"inline","label_font_size":"","label_font_color":{"style":"auto","color":""},"label_font_style":"bold","desc_font_size":"","desc_font_color":{"color":""},"field_padding":"15","field_background_color":{"style":"auto","color":""},"field_border_color":{"style":"auto","color":""},"field_border_width":"","field_border_radius":"","field_font_size":"15","field_font_color":{"style":"auto","color":""},"field_placeholder_font_color":{"style":"auto","color":""},"field_active_background_color":{"style":"auto","color":""},"field_active_border_color":{"style":"auto","color":""},"field_active_font_color":{"style":"auto","color":""},"submit_text":"Submit","button_width":"blockmobile","button_alignment":"left","button_size":"large","button_class":"","button_border_radius":"","button_border_width":"","button_background_color":{"style":"auto","color":""},"button_font_color":{"style":"auto","color":""},"button_border_color":{"style":"auto","color":""},"button_background_color_hover":{"style":"auto","color":""},"button_font_color_hover":{"style":"auto","color":""},"button_border_color_hover":{"style":"auto","color":""},"custom_css":""},"form_type":"registration","after_submit":"display_page","after_submission_url":"","after_submit_message_text":"User Registration Successful! Please check your eMail Inbox and click the activation link to activate your account.","post_type":"bf_submissions","status":"publish","comment_status":"open","singular_name":"","attached_page":"none","edit_link":"all","list_posts_option":"list_all_form","list_posts_style":"list","public_submit":["public_submit"],"public_submit_login":"above","registration":{"activation_message_from_subject":"Vendor Account Activation Mail","activation_message_text":"Hi [user_login],\r\n\r\nGreat to see you come on board! Just one small step left to make your registration complete.\r\n<br>\r\n<b>Click the link below to activate your account.<\/b>\r\n<br>\r\n[activation_link]\r\n<br><br>\r\n[blog_title]","activation_message_from_name":"[blog_title]","activation_message_from_email":"dfg@dfg.fr","new_user_role":"vendor"},"profile_visibility":"any","name":"Auto Draft","slug":""}', true );
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

	// Unset the form fields
	unset( $buddyform['form_fields'] );
	unset( $buddyform['mail_submissions'] );

	// Add the form setup to the json
	$json['form_setup'] = $buddyform;

	echo json_encode( $json );
	die();
}