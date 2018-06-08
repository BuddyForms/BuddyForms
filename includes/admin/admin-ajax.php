<?php

/*
 * Get the post type taxonomies to load the new created form element select
 *
 *
 */
add_action('wp_ajax_nopriv_handle_dropped_media', 'BMP_handle_dropped_media');
add_action( 'wp_ajax_handle_dropped_media', 'BMP_handle_dropped_media' );
function BMP_handle_dropped_media() {
	check_ajax_referer( 'fac_drop', 'nonce' );
	status_header( 200 );
	$upload_dir  = wp_upload_dir();
	$upload_path = $upload_dir['path'] . DIRECTORY_SEPARATOR;
	$num_files   = count( $_FILES['file']['tmp_name'] );
	$newupload = 0;
	if ( ! empty( $_FILES ) ) {
		$files = $_FILES;
		foreach ( $files as $file_id => $file ) {
			$newupload = media_handle_upload( $file_id, 0 );
		}
	}
	
	echo $newupload;
	die();
}

add_action( 'wp_ajax_nopriv_handle_deleted_media', 'BMP_handle_delete_media' );
add_action( 'wp_ajax_handle_deleted_media', 'BMP_handle_delete_media' );

function BMP_handle_delete_media() {
	check_ajax_referer( 'fac_drop', 'nonce' );
	if ( isset( $_REQUEST['media_id'] ) ) {
		$post_id = absint( $_REQUEST['media_id'] );
		
		$status = wp_delete_attachment( $post_id, true );
		
		if ( $status ) {
			echo wp_json_encode( array( 'status' => 'OK' ) );
		} else {
			echo wp_json_encode( array( 'status' => 'FAILED' ) );
		}
	}
	
	die();
}
add_action( 'wp_ajax_buddyforms_post_types_taxonomies', 'buddyforms_post_types_taxonomies' );
function buddyforms_post_types_taxonomies() {

	if ( ! isset( $_POST['post_type'] ) ) {
		echo 'false';
		die();
	}

	$post_type             = $_POST['post_type'];
	$buddyforms_taxonomies = buddyforms_taxonomies( $post_type );

	$tmp = '';
	foreach ( $buddyforms_taxonomies as $name => $label ) {
		$tmp .= '<option value="' . $name . '">' . $label . '</option>';
	}

	echo $tmp;
	die();

}

add_action( 'wp_ajax_buddyforms_update_taxonomy_default', 'buddyforms_update_taxonomy_default' );
function buddyforms_update_taxonomy_default() {

	if ( ! isset( $_POST['taxonomy'] ) || $_POST['taxonomy'] == 'none' ) {
		$tmp = '<option value="none">First you need to select a Taxonomy to select the Taxonomy defaults</option>';
		echo $tmp;
		die();
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

add_action( 'wp_ajax_buddyforms_new_page', 'buddyforms_new_page' );
function buddyforms_new_page() {

	if ( ! is_admin() ) {
		return;
	}

	// Check if a title is entered
	if ( empty( $_POST['page_name'] ) ) {
		$json['error'] = 'Please enter a name';
		echo json_encode( $json );
		die();
	}

	// Create post object
	$new_page = array(
		'post_title'   => wp_strip_all_tags( $_POST['page_name'] ),
		'post_content' => '',
		'post_status'  => 'publish',
		'post_type'    => 'page'
	);

	// Insert the post into the database
	$new_page = wp_insert_post( $new_page );

	// Check if page creation worked successfully
	if ( is_wp_error( $new_page ) ) {
		$json['error'] = $new_page;
	} else {
		$json['id']   = $new_page;
		$json['name'] = wp_strip_all_tags( $_POST['page_name'] );
	}

	echo json_encode( $json );
	die();

}

add_action( 'wp_ajax_buddyforms_url_builder', 'buddyforms_url_builder' );
function buddyforms_url_builder() {
	global $post;
	$page_id   = $_POST['attached_page'];
	$form_slug = $_POST['form_slug'];
	$post      = get_post( $page_id );

	if ( isset( $post->post_name ) ) {
		$json['permalink'] = get_permalink( $page_id );
		$json['form_slug'] = $form_slug;
		echo json_encode( $json );
		die();
	}
	echo json_encode( 'none' );
	die();


}
