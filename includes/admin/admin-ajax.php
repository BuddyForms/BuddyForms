<?php
/**
 * Ajax call back function to add a form element
 *
 * @package BuddyForms
 * @since 0.1-beta
 */
function buddyforms_add_form() {
	global $buddyforms;

	if ( ! is_array( $buddyforms ) ) {
		$buddyforms = Array();
	}

	if ( empty( $_POST['create_new_form_name'] ) ) {
		return;
	}
	if ( empty( $_POST['create_new_form_singular_name'] ) ) {
		return;
	}
	if ( empty( $_POST['create_new_form_attached_page'] ) && empty( $_POST['create_new_page'] ) ) {
		return;
	}
	if ( empty( $_POST['create_new_form_post_type'] ) ) {
		return;
	}

	if ( ! empty( $_POST['create_new_page'] ) ) {
		// Create post object
		$mew_post = array(
			'post_title'   => wp_strip_all_tags( $_POST['create_new_page'] ),
			'post_content' => '',
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'page'
		);

		// Insert the post into the database
		$_POST['create_new_form_attached_page'] = wp_insert_post( $mew_post );
	}

	$bf_forms_args = array(
		'post_title'  => $_POST['create_new_form_name'],
		'post_type'   => 'buddyforms',
		'post_status' => 'publish',
	);

	// Insert the new form
	$post_id  = wp_insert_post( $bf_forms_args, true );
	$the_post = get_post( $post_id );

	$options = Array(
		'slug'          => $the_post->post_name,
		'id'            => $the_post->ID,
		'name'          => $_POST['create_new_form_name'],
		'singular_name' => $_POST['create_new_form_singular_name'],
		'attached_page' => $_POST['create_new_form_attached_page'],
		'post_type'     => $_POST['create_new_form_post_type'],
	);

	if ( ! empty( $_POST['create_new_form_status'] ) ) {
		$options = array_merge( $options, Array( 'status' => $_POST['create_new_form_status'] ) );
	}

	if ( ! empty( $_POST['create_new_form_comment_status'] ) ) {
		$options = array_merge( $options, Array( 'comment_status' => $_POST['create_new_form_comment_status'] ) );
	}

	$field_id = $mod5 = substr( md5( time() * rand() ), 0, 10 );

	$options['form_fields'][ $field_id ]['name'] = 'Title';
	$options['form_fields'][ $field_id ]['slug'] = 'editpost_title';
	$options['form_fields'][ $field_id ]['type'] = 'Title';

	$field_id = $mod5 = substr( md5( time() * rand() ), 0, 10 );

	$options['form_fields'][ $field_id ]['name'] = 'Content';
	$options['form_fields'][ $field_id ]['slug'] = 'editpost_content';
	$options['form_fields'][ $field_id ]['type'] = 'Content';


	update_post_meta( $post_id, '_buddyforms_options', $options );

	if ( $post_id ) {
		buddyforms_attached_page_rewrite_rules( true );
		echo sanitize_title( $_POST['create_new_form_name'] );
	} else {
		echo 'Error Saving the Form';
	}

	die();

}

add_action( 'wp_ajax_buddyforms_add_form', 'buddyforms_add_form' );

/**
 * Get all taxonomies
 *
 * @package BuddyForms
 * @since 0.1-beta
 */
function buddyforms_taxonomies( $buddyform ) {

	$post_type = $buddyform['post_type'];

	$taxonomies = get_object_taxonomies( $post_type );

	return $taxonomies;
}

function buddyforms_update_taxonomy_default() {

	if ( ! isset( $_POST['taxonomy'] ) ) {
		echo 'false';
	}

	$taxonomy = $_POST['taxonomy'];

	$args = array(
		'orderby'    => 'name',
		'order'      => 'ASC',
		'hide_empty' => false,
		'fields'     => 'id=>name',
	);

	$terms = get_terms( $taxonomy, $args );

	$tmp = '<option value="none">none</option>';
	foreach ( $terms as $key => $term_name ) {
		$tmp .= '<option value="' . $key . '">' . $term_name . '</option>';
	}

	echo $tmp;

	die();

}
add_action( 'wp_ajax_buddyforms_update_taxonomy_default', 'buddyforms_update_taxonomy_default' );

function buddyforms_form_template(){
	global $post, $buddyform;


	$post->post_type = 'buddyforms';


	switch($_POST['template']){
		case 'contact' :
			$buddyform =  json_decode('{"name":"Contact Us","slug":"contact-us","after_submit":"display_posts_list","after_submit_message_text":"The [form_singular_name] [post_title] has been successfully updated!<br>1. [post_link]<br>2. [edit_link]","singular_name":"kann weg","post_type":"bf_submissions","attached_page":"2","status":"publish","comment_status":"open","edit_link":"all","list_posts_option":"list_all_form","list_posts_style":"table","form_fields":{"bef0fd43e6":{"name":"Name","slug":"name","description":"","type":"text","validation_error_message":"This field is required.","validation_minlength":"0","validation_maxlength":"0","custom_class":""},"2391bf0a50":{"name":"Mail","slug":"mail","description":"","type":"mail","validation_error_message":"This field is required.","custom_class":""},"544d105558":{"name":"Website","slug":"website","description":"","type":"link","validation_error_message":"This field is required.","metabox_enabled":["metabox_enabled"],"custom_class":""},"5bf35b20e3":{"name":"Subject","slug":"editpost_title","description":"","type":"title","validation_error_message":"This field is required.","validation_minlength":"0","validation_maxlength":"","custom_class":""},"a6aa43b61d":{"name":"Content","slug":"editpost_content","description":"","type":"content","validation_error_message":"This field is required.","validation_minlength":"0","validation_maxlength":"0","custom_class":""}},"moderation_logic":"default","moderation":{"label_submit":"Submit","label_save":"Save","label_review":"Submit for moderation","label_new_draft":"Create new Draft","label_no_edit":"This Post is waiting for approval and can not be changed until it gets approved"}}', true);
			break;
		case 'create' :
			$buddyform =  json_decode('{"form_fields":{"5bf35b20e3":{"name":"Subject","description":"","type":"title","slug":"editpost_title","validation_error_message":"This field is required.","validation_minlength":"0","validation_maxlength":"","custom_class":""},"a6aa43b61d":{"name":"Content","description":"","type":"content","slug":"editpost_content","validation_error_message":"This field is required.","validation_minlength":"0","validation_maxlength":"0","custom_class":""}},"name":"Post","slug":"post","after_submit":"display_message","after_submit_message_text":"The [form_singular_name] [post_title] has been successfully updated!<br>1. [post_link]<br>2. [edit_link]","post_type":"post","status":"publish","comment_status":"open","edit_link":"all","singular_name":"","attached_page":"2","list_posts_option":"list_all_form","list_posts_style":"list"}', true);
			break;
	}

	ob_start();
		buddyforms_metabox_form_elements($post, $buddyform);
	$tmp = ob_get_clean();

	$json['html'] = $tmp;
	unset($buddyform['form_fields']);
	$json['form_setup'] = $buddyform;


	echo json_encode( $json );

	die();

}
add_action( 'wp_ajax_buddyforms_form_template', 'buddyforms_form_template' );
