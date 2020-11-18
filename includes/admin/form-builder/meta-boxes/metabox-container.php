<?php
function buddyforms_metabox_form_editor(){
	global $post, $buddyform;

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}

	if ( ! $buddyform ) {
		$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true );
	}

	// Generate the form slug from the post name
	$form_slug = ( isset( $post->post_name ) ) ? $post->post_name : '';

	include BUDDYFORMS_ADMIN_VIEW.'editor/editor-container.php';
}
