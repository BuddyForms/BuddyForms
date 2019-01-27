<?php
/*
Plugin Name: PHP Block
Description: A sample PHP rendered block, showing how to convert a shortcode to a block.
Author: Gary Pendergast
Version: 0.1
Author URI: https://buddyforms.net/
License: GPLv2+
*/

/*
 * Here's a little sample plugin that shows how to easily convert an existing shortcode
 * to be a server-side rendered block. This lets you get your existing plugin functionality
 * running in the block editor as quickly as possible, you can always go back later and
 * improve the UX.
 *
 * In this case, we have an imaginary shortcode, [php_block], which accepts one argument, 'foo'.
 * This shortcode would be used like so:
 *
 * [php_block foo=abcde]
 *
 * Because the block editor uses the same function signature when doing server-side rendering, we
 * can reuse our entire shortcode logic when creating the block.
 */


/**
 * Register our block and shortcode.
 */
function php_block_init() {
	global $buddyforms;

	// Register block editor BuddyForms script.
	wp_register_script(
		'bf-embed-form',
		plugins_url( 'shortcodes-to-blocks.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' )
	);

	//
	// Localize the BuddyForms script with all needed data
	//

	// All Forms as slug and label
	$forms = array();
	foreach( $buddyforms as $form_slug => $form ){
		$forms[$form_slug] = $form['name'];
	}
	wp_localize_script( 'bf-embed-form', 'buddyforms_forms', $forms );

	// All WordPress User Roles
	$roles = buddyform_get_role_names();
	wp_localize_script( 'bf-embed-form', 'buddyforms_roles', $roles );

	// All WordPress Users
	$users = array();
	$users_object = get_users( array( 'fields' => array( 'ID', 'display_name' ) ) );
	foreach ( $users_object as $user ) {
		$users[$user->ID] = esc_html( $user->display_name );
	}
	wp_localize_script( 'bf-embed-form', 'buddyforms_users', $users );

	// Register our block, and explicitly define the attributes we accept.
	register_block_type( 'buddyforms/bf-embed-form', array(
		'attributes'      => array(
			'bf_form_slug' => array(
				'type' => 'string',
			)
		),
		'editor_script'   => 'bf-embed-form', // The script name we gave in the wp_register_script() call.
		'render_callback' => 'buddyforms_block_render_form',
	) );

	// Register our block, and explicitly define the attributes we accept.
	register_block_type( 'buddyforms/bf-navigation', array(
		'attributes'      => array(
			'bf_form_slug' => array(
				'type' => 'string',
			)
		),
		'editor_script'   => 'bf-embed-form', // The script name we gave in the wp_register_script() call.
		'render_callback' => 'buddyforms_block_navigation',
	) );

	// Register our block, and explicitly define the attributes we accept.
	register_block_type( 'buddyforms/bf-list-submissions', array(
		'attributes'      => array(
			'bf_form_slug' => array(
				'type' => 'string',
			),
			'bf_rights' => array(
				'type' => 'string',
			),
			'bf_author' => array(
				'type' => 'string',
			),
			'bf_meta_key' => array(
				'type' => 'string',
			),
			'bf_list_posts_style' => array(
				'type' => 'string',
			),
		),
		'editor_script'   => 'bf-list-submissions', // The script name we gave in the wp_register_script() call.
		'render_callback' => 'buddyforms_block_list_submissions',
	) );
}

add_action( 'init', 'php_block_init' );
function buddyforms_block_render_form( $attributes ) {
	global $buddyforms;

	if( isset($attributes['bf_form_slug']) && isset($buddyforms[$attributes['bf_form_slug']])){
		return buddyforms_create_edit_form_shortcode( array( 'form_slug' => $attributes['bf_form_slug'] ) );
	} else {
		return '<p>' . __( 'Please Select a Form in the Block Settings Sitebar', 'buddyforms') . '</p>';
	}
}

function buddyforms_block_navigation( $attributes ) {
	global $buddyforms;

	if( isset($attributes['bf_form_slug']) && isset($buddyforms[$attributes['bf_form_slug']])){
		return buddyforms_create_edit_form_shortcode( array( 'form_slug' => $attributes['bf_form_slug'] ) );
	} else {
		return '<p>' . __( 'Please Select a Form in the Block Settings Sitebar', 'buddyforms') . '</p>';
	}
}

function buddyforms_block_list_submissions( $attributes ) {
	global $buddyforms;

//	print_r($attributes);

	if( isset($attributes['bf_form_slug']) && isset($buddyforms[$attributes['bf_form_slug']])){
		$list_style = empty( $attributes['bf_list_posts_style'] ) ? 'list' : $attributes['bf_list_posts_style'];
		return buddyforms_the_loop_shortcode( array( 'form_slug' => $attributes['bf_form_slug'], 'list_posts_style' => $list_style ) );
	} else {
		return '<p>' . __( 'Please Select a Form in the Block Settings Sitebar', 'buddyforms') . '</p>';
	}

//	return '<p>Laver ' . print_r( $attributes, true ) . '</p>'.  $attributes['bf_form_slug'];
}

