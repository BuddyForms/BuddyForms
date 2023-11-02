<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }


function disable_new_posts() {
    // Hide sidebar link
    global $submenu;
    unset($submenu['edit.php?post_type=buddyforms'][10]);
//    unset($submenu['edit.php?post_type=buddyforms&page=buddyforms_welcome_screen'][10]);

    // Hide link on listing page
    if (isset($_GET['post_type']) && $_GET['post_type'] == 'buddyforms') {
        echo '<style type="text/css">
        #favorite-actions, .add-new-h2, .tablenav { display:none; }
        </style>';
    }
}
add_action('admin_menu', 'disable_new_posts');

/**
 * Register custom post type for the templates
 */
function buddyforms_hooks_fields_template_post_type() {
	// Create BuddyForms post type
	$labels = array(
		'name'          => __( 'Template Builder', 'buddyforms' ),
		'singular_name' => __( 'Templates Builder', 'buddyforms' ),
	);

	register_post_type(
		'bf_template',
		array(
			'labels'                       => $labels,
			'public'                       => true,
			'show_ui'                      => true,
			'capability_type'              => 'post',
			'hierarchical'                 => false,
			'show_in_rest'                 => true,
			// 'rewrite'             => true,
								'supports' => array(
									'title',
									'editor',
									'elementor',
								),
			'show_in_menu'                 => 'edit.php?post_type=buddyforms',
			'exclude_from_search'          => true,
			'publicly_queryable'           => true,
			'menu_icon'                    => 'dashicons-buddyforms',
		)
	);
}

add_action( 'init', 'buddyforms_hooks_fields_template_post_type' );

/**
 * Get all template pages
 *
 * @return array
 */
function buddyforms_hooks_fields_get_templates() {
	$args = array(
		'sort_order'  => 'asc',
		'sort_column' => 'post_title',
		'parent'      => - 1,
		'post_type'   => 'bf_template',
		'post_status' => 'private',
	);

	$posts = new WP_Query( $args );

	$all_templates = array( __( '-No override-', 'buddyforms' ) );
	if ( $posts->have_posts() ) {
		foreach ( $posts->posts as $item ) {
			$all_templates[ $item->ID ] = $item->post_title;
		}
	}

	return $all_templates;
}

/**
 * Exclude from the post type in the buddyforms form builder
 *
 * @param $post_type
 *
 * @return mixed
 */
function buddyforms_hooks_fields_exclude_post_type_for_form_builder( $post_type ) {
	unset( $post_type['bf_template'] );

	return $post_type;
}

add_filter( 'buddyforms_form_builder_post_type', 'buddyforms_hooks_fields_exclude_post_type_for_form_builder', 10, 1 );


/**
 * @param $post
 *
 * @return mixed
 */
function buddyforms_hooks_fields_private_template( $post ) {
	if ( $post['post_type'] == 'bf_template' ) {
		$post['post_status'] = 'private';
	}

	return $post;
}

add_filter( 'wp_insert_post_data', 'buddyforms_hooks_fields_private_template' );

/**
 * @param $post_states
 * @param WP_Post     $post
 *
 * @return mixed
 */
function buddyforms_hooks_fields_remove_private_flag( $post_states, $post ) {
	if ( ! empty( $post ) && ! empty( $post_states ) ) {
		if ( ! empty( $post->post_type ) && $post->post_type === 'bf_template' && isset( $post_states['private'] ) ) {
			unset( $post_states['private'] );
		}
	}

	return $post_states;
}

add_filter( 'display_post_states', 'buddyforms_hooks_fields_remove_private_flag', 999, 2 );
