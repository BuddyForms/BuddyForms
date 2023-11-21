<?php


function buddyforms_create_dynamic_post_types() {
	$args = array(
		'post_type' => 'bf-post-types',
		'posts_per_page' => 0,
	);
	$query = new WP_Query($args);
	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			// Create BuddyForms post type
			$labels = array(
				'name' => get_the_title(),
				'singular_name' => get_the_title(),
			);

			$buddyforms_custom_post_type = get_post_meta( get_the_ID(), '_buddyforms_custom_post_type', true );

			$labels = array(
				'name'                  => _x( get_the_title(), 'Post type general name', 'buddyforms' ),
				'singular_name'         => isset($buddyforms_custom_post_type['singular_name']) ? _x( $buddyforms_custom_post_type['labels']['singular_name'], 'Post type singular name', 'buddyforms' ) : '',
			);

			if( isset($buddyforms_custom_post_type['labels']['menu_name']) && ! empty($buddyforms_custom_post_type['labels']['menu_name'])){
				$labels['menu_name'] = _x( $buddyforms_custom_post_type['labels']['menu_name'], 'Admin Menu text', 'buddyforms' );
			}

			$add = array(
				'menu_name'             => _x( 'Recipes', 'Admin Menu text', 'buddyforms' ),
				'name_admin_bar'        => _x( 'Recipe', 'Add New on Toolbar', 'buddyforms' ),
				'add_new'               => __( 'Add New', 'buddyforms' ),
				'add_new_item'          => __( 'Add New buddyforms', 'buddyforms' ),
				'new_item'              => __( 'New buddyforms', 'buddyforms' ),
				'edit_item'             => __( 'Edit buddyforms', 'buddyforms' ),
				'view_item'             => __( 'View buddyforms', 'buddyforms' ),
				'all_items'             => __( 'All buddyformss', 'buddyforms' ),
				'search_items'          => __( 'Search buddyformss', 'buddyforms' ),
				'parent_item_colon'     => __( 'Parent buddyformss:', 'buddyforms' ),
				'not_found'             => __( 'No buddyformss found.', 'buddyforms' ),
				'not_found_in_trash'    => __( 'No buddyformss found in Trash.', 'buddyforms' ),
				'featured_image'        => _x( 'Recipe Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'buddyforms' ),
				'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'buddyforms' ),
				'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'buddyforms' ),
				'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'buddyforms' ),
				'archives'              => _x( 'Recipe archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'buddyforms' ),
				'insert_into_item'      => _x( 'Insert into buddyforms', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'buddyforms' ),
				'uploaded_to_this_item' => _x( 'Uploaded to this buddyforms', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'buddyforms' ),
				'filter_items_list'     => _x( 'Filter buddyformss list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'buddyforms' ),
				'items_list_navigation' => _x( 'Recipes list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'buddyforms' ),
				'items_list'            => _x( 'Recipes list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'buddyforms' ),

			);

			register_post_type(
				get_post_field( 'post_name' ),
				array(
					'labels' => $labels,
					'public' => true,
					'show_ui' => true,
					'_builtin' => false,
					'capability_type' => 'post',
					'hierarchical' => false,
					'rewrite' => false,
					'supports' => array(
						'title',
					),
					'show_in_menu' => true,
					'exclude_from_search' => true,
					'publicly_queryable' => true,
					'menu_icon' => 'dashicons-buddyforms',
				)
			);

		} // end while
	} // end if
	wp_reset_query();
}

add_action('init', 'buddyforms_create_dynamic_post_types');
