<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }


if ( ! defined( 'ABSPATH' ) ) {
	exit; }

/**
 * Create Blocks from Shortcodes
 *
 * @since 2.3.1
 */
function buddyforms_hook_fields_shortcodes_to_block_init() {
	global $buddyforms, $pagenow;

	if ( empty( $buddyforms ) || ! is_array( $buddyforms ) ) {
		return;
	}

	if ( $pagenow == 'post-new.php' ) {
		return;
	}
	// Register block editor BuddyForms script.
	wp_register_script(
		'bf-embed-hook-field',
		plugins_url( 'shortcodes-to-blocks.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' )
	);

	//
	// Localize the BuddyForms script with all needed data
	//

	// All Forms as slug and label
	$forms        = array();
	$forms_fields = array();
	foreach ( $buddyforms as $form_slug => $form ) {
		$forms[ $form_slug ] = $form['name'];
		foreach ( $form['form_fields'] as $key => $field ) {
			$forms_fields[ $form_slug ][ $key ] = $field['slug'];
		}
	}

	$args = array(
		'numberposts' => 5,
		'post_type'   => 'bf_template',
		'post_status' => 'private',
	);
	$templates = get_posts( $args );

	$templates_array = array();
	foreach ( $templates as $key => $template ) {
		$templates_array[ $template->ID ] = $template->post_title;
	}

	wp_localize_script( 'bf-embed-hook-field', 'buddyforms_forms', $forms );
	wp_localize_script( 'bf-embed-hook-field', 'buddyforms_forms_fields', $forms_fields );
	wp_localize_script( 'bf-embed-hook-field', 'buddyforms_templates', $templates_array );

	//
	// Embed a form
	//
	register_block_type(
		'buddyformshooks/bf-insert-form-field-value',
		array(
			'attributes'      => array(
				'bf_form_slug'  => array(
					'type' => 'string',
				),
				'bf_form_field' => array(
					'type' => 'string',
				),
			),
			'editor_script'   => 'bf-embed-hook-field',
			'render_callback' => 'buddyforms_hook_fields_block_render_form',
		)
	);
}
add_action( 'init', 'buddyforms_hook_fields_shortcodes_to_block_init', 9999, 0 );

/**
 * Render a Form
 *
 * @since 2.3.1
 */
function buddyforms_hook_fields_block_render_form( $attributes ) {
	global $buddyforms, $post;

	if ( isset( $attributes['bf_form_slug'] ) && isset( $buddyforms[ $attributes['bf_form_slug'] ] ) ) {
		$tmp = '<p>' . __( 'Please select a form element in the block settings sidebar!', 'buddyforms' ) . '</p>';
		if ( isset( $attributes['bf_form_field'] ) ) {
				$tmp = do_shortcode( '[bfsinglefield form-slug="' . $attributes['bf_form_slug'] . '" field-slug="' . $attributes['bf_form_field'] . '"]' );
		}
//		if ( empty( $tmp ) ){
//			$tmp = $attributes->ID; // get_the_content( null, false, $post->ID );
//		}
		return $tmp;
	} else {
		return '<p>' . __( 'Please select a form in the block settings sidebar!', 'buddyforms' ) . '</p>';
	}
}
