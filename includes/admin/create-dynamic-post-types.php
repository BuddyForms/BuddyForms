<?php


function buddyforms_create_dynamic_post_types()
{
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
			register_post_type(
				get_the_title(),
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
