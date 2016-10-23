<?php

//
// Post metabox to display form elements in the admin backend
//
function buddyforms_admin_form_metabox() {
	global $buddyforms, $post;

	// sanity check
	if ( ! ( $post instanceof WP_Post ) ) {
		return;
	}

	if ( $post->post_type == 'buddyforms' ) {
		return;
	}

	$form_slug                    = get_post_meta( $post->ID, '_bf_form_slug', true );
	$buddyforms_posttypes_default = get_option( 'buddyforms_posttypes_default' );

	if ( ! $form_slug ) {
		$form_slug = isset( $buddyforms_posttypes_default[ $post->post_type ] ) ? $buddyforms_posttypes_default[ $post->post_type ] : null;
	}

	if ( empty( $form_slug ) ) {
		return;
	}
	if ( ! isset( $buddyforms[ $form_slug ] ) ) {
		return;
	}

	$form = $buddyforms[ $form_slug ];

	$metabox_enabled = false;
	if ( isset( $form['form_fields'] ) ) {
		foreach ( $form['form_fields'] as $field_key => $field ) {
			if ( isset( $field['metabox_enabled'] ) && $field['metabox_enabled'] ) {
				$metabox_enabled = true;
			}
		}
	}

	if ( $metabox_enabled ) {
		add_meta_box( 'buddyforms_form_' . $form_slug, 'BuddyForms Form: ' . $form['name'], 'buddyforms_metabox_admin_form_metabox', $form['post_type'], 'normal', 'high' );
	}

}

add_action( 'add_meta_boxes', 'buddyforms_admin_form_metabox' );

//
// Metabox content
//
function buddyforms_metabox_admin_form_metabox() {
	global $buddyforms, $post;

	$form_slug = get_post_meta( $post->ID, '_bf_form_slug', true );

	$buddyforms_posttypes_default = get_option( 'buddyforms_posttypes_default' );

	if ( ! $form_slug ) {
		$form_slug = $buddyforms_posttypes_default[ $post->post_type ];
	}

	if ( ! isset( $form_slug ) ) {
		return;
	}
	if ( ! isset( $buddyforms[ $form_slug ] ) ) {
		return;
	}

	session_id( 'buddyforms-memtabox' );

	// Create the form object
	$form = new Form( "metabox_" . $form_slug );

	// Set the form attribute
	$form->configure( array(
		//"prevent" => array("bootstrap", "jQuery", "focus"),
		//"action" => $redirect_to,
		"view"  => new View_Metabox,
		'class' => 'standard-form',
	) );

	$fields = $buddyforms[ $form_slug ]['form_fields'];

	$metabox_fields = array();
	foreach ( $fields as $field_key => $field ) {
		if ( isset( $field['metabox_enabled'] ) ) {
			$metabox_fields[] = $field;
		}
	}

	$args = array(
		'post_type'    => $buddyforms[ $form_slug ]['post_type'],
		'customfields' => $metabox_fields,
		'post_id'      => $post->ID,
		'form_slug'    => $form_slug,
	);

	// if the form has custom field to save as post meta data they get displayed here
	buddyforms_form_elements( $form, $args );

	$form->render();

}

//
// Save the metabox data
//
/**
 * @param $post_id
 */
function buddyforms_metabox_admin_form_metabox_save( $post_id ) {
	global $buddyforms;

	if ( ! is_admin() || defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}

	$form_slug = get_post_meta( $post_id, '_bf_form_slug', true );

	if ( ! isset( $form_slug ) ) {
		return;
	}
	if ( ! isset( $buddyforms[ $form_slug ] ) ) {
		return;
	}

	buddyforms_update_post_meta( $post_id, $buddyforms[ $form_slug ]['form_fields'] );
}

add_action( 'save_post', 'buddyforms_metabox_admin_form_metabox_save' );