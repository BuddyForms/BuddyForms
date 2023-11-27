<?php

// for testing the rewrite args
flush_rewrite_rules();

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
//			$labels = array(
//				'name' => get_the_title(),
//				'singular_name' => get_the_title(),
//			);

			$buddyforms_custom_post_type = get_post_meta( get_the_ID(), '_buddyforms_custom_post_type', true );


			// Labels

			$labels = array(
				'name'                  => _x( get_the_title(), 'Post type general name', 'buddyforms' ),
				'singular_name'         => isset($buddyforms_custom_post_type['singular_name']) ? _x( $buddyforms_custom_post_type['labels']['singular_name'], 'Post type singular name', 'buddyforms' ) : '',
			);

			if( isset( $buddyforms_custom_post_type['labels']['menu_name'] ) && ! empty($buddyforms_custom_post_type['labels']['menu_name'])){
				$labels['menu_name'] = _x( $buddyforms_custom_post_type['labels']['menu_name'], 'Admin Menu text', 'buddyforms' );
			}

			if( isset( $buddyforms_custom_post_type['labels']['add_new'] ) && ! empty($buddyforms_custom_post_type['labels']['add_new'])){
				$labels['add_new'] = _x( $buddyforms_custom_post_type['labels']['add_new'], 'Admin Menu text', 'buddyforms' );
			}

			if (isset($buddyforms_custom_post_type['labels']['name_admin_bar']) && !empty($buddyforms_custom_post_type['labels']['name_admin_bar'])) {
				$labels['name_admin_bar'] = _x($buddyforms_custom_post_type['labels']['name_admin_bar'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['add_new_item']) && !empty($buddyforms_custom_post_type['labels']['add_new_item'])) {
				$labels['add_new_item'] = _x($buddyforms_custom_post_type['labels']['add_new_item'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['new_item']) && !empty($buddyforms_custom_post_type['labels']['new_item'])) {
				$labels['new_item'] = _x($buddyforms_custom_post_type['labels']['new_item'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['edit_item']) && !empty($buddyforms_custom_post_type['labels']['edit_item'])) {
				$labels['edit_item'] = _x($buddyforms_custom_post_type['labels']['edit_item'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['view_item']) && !empty($buddyforms_custom_post_type['labels']['view_item'])) {
				$labels['view_item'] = _x($buddyforms_custom_post_type['labels']['view_item'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['view_items']) && !empty($buddyforms_custom_post_type['labels']['view_items'])) {
				$labels['view_items'] = _x($buddyforms_custom_post_type['labels']['view_items'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['all_items']) && !empty($buddyforms_custom_post_type['labels']['all_items'])) {
				$labels['all_items'] = _x($buddyforms_custom_post_type['labels']['all_items'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['search_items']) && !empty($buddyforms_custom_post_type['labels']['search_items'])) {
				$labels['search_items'] = _x($buddyforms_custom_post_type['labels']['search_items'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['parent']) && !empty($buddyforms_custom_post_type['labels']['parent'])) {
				$labels['parent_item_colon'] = _x($buddyforms_custom_post_type['labels']['parent'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['not_found']) && !empty($buddyforms_custom_post_type['labels']['not_found'])) {
				$labels['not_found'] = _x($buddyforms_custom_post_type['labels']['not_found'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['not_found_in_trash']) && !empty($buddyforms_custom_post_type['labels']['not_found_in_trash'])) {
				$labels['not_found_in_trash'] = _x($buddyforms_custom_post_type['labels']['not_found_in_trash'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['featured_image']) && !empty($buddyforms_custom_post_type['labels']['featured_image'])) {
				$labels['featured_image'] = _x($buddyforms_custom_post_type['labels']['featured_image'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['set_featured_image']) && !empty($buddyforms_custom_post_type['labels']['set_featured_image'])) {
				$labels['set_featured_image'] = _x($buddyforms_custom_post_type['labels']['set_featured_image'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['remove_featured_image']) && !empty($buddyforms_custom_post_type['labels']['remove_featured_image'])) {
				$labels['remove_featured_image'] = _x($buddyforms_custom_post_type['labels']['remove_featured_image'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['use_featured_image']) && !empty($buddyforms_custom_post_type['labels']['use_featured_image'])) {
				$labels['use_featured_image'] = _x($buddyforms_custom_post_type['labels']['use_featured_image'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['archives']) && !empty($buddyforms_custom_post_type['labels']['archives'])) {
				$labels['archives'] = _x($buddyforms_custom_post_type['labels']['archives'], 'Admin Menu text', 'buddyforms');
			}
			
			if (isset($buddyforms_custom_post_type['labels']['insert_into_item']) && !empty($buddyforms_custom_post_type['labels']['insert_into_item'])) {
				$labels['insert_into_item'] = _x($buddyforms_custom_post_type['labels']['insert_into_item'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['uploaded_to_this_item']) && !empty($buddyforms_custom_post_type['labels']['uploaded_to_this_item'])) {
				$labels['uploaded_to_this_item'] = _x($buddyforms_custom_post_type['labels']['uploaded_to_this_item'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['filter_items_list']) && !empty($buddyforms_custom_post_type['labels']['filter_items_list'])) {
				$labels['filter_items_list'] = _x($buddyforms_custom_post_type['labels']['filter_items_list'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['items_list_navigation']) && !empty($buddyforms_custom_post_type['labels']['items_list_navigation'])) {
				$labels['items_list_navigation'] = _x($buddyforms_custom_post_type['labels']['items_list_navigation'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['items_list']) && !empty($buddyforms_custom_post_type['labels']['items_list'])) {
				$labels['items_list'] = _x($buddyforms_custom_post_type['labels']['items_list'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['attributes']) && !empty($buddyforms_custom_post_type['labels']['attributes'])) {
				$labels['attributes'] = _x($buddyforms_custom_post_type['labels']['attributes'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['item_published']) && !empty($buddyforms_custom_post_type['labels']['item_published'])) {
				$labels['item_published'] = _x($buddyforms_custom_post_type['labels']['item_published'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['item_published_privately']) && !empty($buddyforms_custom_post_type['labels']['item_published_privately'])) {
				$labels['item_published_privately'] = _x($buddyforms_custom_post_type['labels']['item_published_privately'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['item_reverted_to_draft']) && !empty($buddyforms_custom_post_type['labels']['item_reverted_to_draft'])) {
				$labels['item_reverted_to_draft'] = _x($buddyforms_custom_post_type['labels']['item_reverted_to_draft'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['item_scheduled']) && !empty($buddyforms_custom_post_type['labels']['item_scheduled'])) {
				$labels['item_scheduled'] = _x($buddyforms_custom_post_type['labels']['item_scheduled'], 'Admin Menu text', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['labels']['item_updated']) && !empty($buddyforms_custom_post_type['labels']['item_updated'])) {
				$labels['item_updated'] = _x($buddyforms_custom_post_type['labels']['item_updated'], 'Admin Menu text', 'buddyforms');
			}

			// description

			$description = '';

			if( isset( $buddyforms_custom_post_type['labels']['description'] ) && ! empty($buddyforms_custom_post_type['labels']['description'])){
				$description = _x( $buddyforms_custom_post_type['labels']['description'], 'Custom post Archiv Descriptiom', 'buddyforms' );
			}

			// public

			$public = false;

			if (isset($buddyforms_custom_post_type['public']) && !empty($buddyforms_custom_post_type['public'])) {
				$public = $buddyforms_custom_post_type['public'];
			}

			// hierarchical

			$hierarchical = false;

			if (isset($buddyforms_custom_post_type['hierarchical']) && !empty($buddyforms_custom_post_type['hierarchical'])) {
				$hierarchical = $buddyforms_custom_post_type['hierarchical'];
			}

			// exclude_from_search

			$exclude_from_search = false;

			if (isset($buddyforms_custom_post_type['exclude_from_search']) && !empty($buddyforms_custom_post_type['exclude_from_search'])) {
				$exclude_from_search = $buddyforms_custom_post_type['exclude_from_search'];
			}

			// publicly_queryable

			$publicly_queryable = false;

			if (isset($buddyforms_custom_post_type['publicly_queryable']) && !empty($buddyforms_custom_post_type['publicly_queryable'])) {
				$publicly_queryable = $buddyforms_custom_post_type['publicly_queryable'];
			}

			// show_ui

			$show_ui = false;

			if (isset($buddyforms_custom_post_type['show_ui']) && !empty($buddyforms_custom_post_type['show_ui'])) {
				$show_ui = $buddyforms_custom_post_type['show_ui'];
			}

			// show_in_menu

			$show_in_menu = false;

			if (isset($buddyforms_custom_post_type['show_in_menu']) && !empty($buddyforms_custom_post_type['show_in_menu'])) {
				$show_in_menu = $buddyforms_custom_post_type['show_in_menu'];
			}

			// show_in_menu_string

			$show_in_menu_string = '';	

			if (isset($buddyforms_custom_post_type['show_in_menu_string']) && !empty($buddyforms_custom_post_type['show_in_menu_string'])) {
				$show_in_menu_string = _x($buddyforms_custom_post_type['show_in_menu_string'], 'Admin Menu text', 'buddyforms');
			}

			// show_in_nav_menus

			$show_in_nav_menus = false;

			if (isset($buddyforms_custom_post_type['show_in_nav_menus']) && !empty($buddyforms_custom_post_type['show_in_nav_menus'])) {
				$show_in_nav_menus = $buddyforms_custom_post_type['show_in_nav_menus'];
			}

			// // show_in_rest

			$show_in_rest = false;

			if (isset($buddyforms_custom_post_type['show_in_rest']) && !empty($buddyforms_custom_post_type['show_in_rest'])) {
				$show_in_rest = $buddyforms_custom_post_type['show_in_rest'];
			}

			// rest_base

			// $rest_base = '';

			// if (isset($buddyforms_custom_post_type['rest_base']) && !empty($buddyforms_custom_post_type['rest_base'])) {
			// 	$rest_base = _x($buddyforms_custom_post_type['rest_base'], 'Custom Post Title', 'buddyforms');
			// }

			// // rest_controller_class

			// $rest_controller_class = '';

			// if (isset($buddyforms_custom_post_type['rest_controller_class']) && !empty($buddyforms_custom_post_type['rest_controller_class'])) {
			// 	$rest_controller_class = _x($buddyforms_custom_post_type['rest_controller_class'], 'Custom Post Title', 'buddyforms');
			// }

			// // rest_namespace
			
			// $rest_namespace = '';

			// if (isset($buddyforms_custom_post_type['rest_namespace']) && !empty($buddyforms_custom_post_type['rest_namespace'])) {
			// 	$rest_namespace = _x($buddyforms_custom_post_type['rest_namespace'], 'Custom Post Title', 'buddyforms');
			// }
			
			// menu_position

			$menu_position = null;

			if (isset($buddyforms_custom_post_type['menu_position']) && !empty($buddyforms_custom_post_type['menu_position'])) {
				$menu_position = _x($buddyforms_custom_post_type['menu_position'], 'Custom Post Menu Position', 'buddyforms');
			}

			// menu_icon

			// working but has to be the correct size and maybe source of insecure connection if not https:// ...

			$menu_icon = 'dashicons-buddyforms';

			if (isset($buddyforms_custom_post_type['menu_icon']) && !empty($buddyforms_custom_post_type['menu_icon'])) {
				$menu_icon = $buddyforms_custom_post_type['menu_icon'];
			}
			
			// rewrite

			$rewrite = false;

			if (isset($buddyforms_custom_post_type['rewrite']) && !empty($buddyforms_custom_post_type['rewrite']) && ($buddyforms_custom_post_type['rewrite'] === true)) {
				
				$rewrite = [];

				if (isset($buddyforms_custom_post_type['rewrite_withfront']) && !empty($buddyforms_custom_post_type['rewrite_withfront'])) {
					$rewrite['with_front'] = _x($buddyforms_custom_post_type['rewrite_withfront'], 'Post Type Structure with Front', 'buddyforms');
				}

				if (isset($buddyforms_custom_post_type['rewrite_slug']) && !empty($buddyforms_custom_post_type['rewrite_slug'])) {
					$rewrite['slug'] = _x($buddyforms_custom_post_type['rewrite_slug'], 'Post Type Slug', 'buddyforms');
				}
			}
			
			// query_var

			$query_var = false;

			if (isset($buddyforms_custom_post_type['query_var']) && !empty($buddyforms_custom_post_type['query_var'])) {
				$query_var = $buddyforms_custom_post_type['query_var'];
			}

			// can_export

			$can_export = false;

			if (isset($buddyforms_custom_post_type['can_export']) && !empty($buddyforms_custom_post_type['can_export'])) {
				$can_export = $buddyforms_custom_post_type['can_export'];
			}

			// delete_with_user

			$delete_with_user = false;

			if (isset($buddyforms_custom_post_type['delete_with_user']) && !empty($buddyforms_custom_post_type['delete_with_user'])) {
				$delete_with_user = $buddyforms_custom_post_type['delete_with_user'];
			}

			// supports

			// Comment Has to be translatabe? I guess no?

			$supports = array(
				'title' => false,
				'editor' => false,
				'author' => false,
				'excerpt' => false,
				'trackbacks' => false,
				'custom-fields' => false,
				'comments' => false,
				'revisions' => false,
				'page-attributes' => false,
				'thumbnail' => false,
				'post-formats' => false,
			);

			if (isset($buddyforms_custom_post_type['buddyforms_custom_post_type']['supports']['none']) && !empty($buddyforms_custom_post_type['supports']['none'])) {
				$supports['none'] = _x($buddyforms_custom_post_type['supports']['none'], 'Custom Post Post Formats', 'buddyforms');				
			}

			if (isset($buddyforms_custom_post_type['supports']['title']) && !empty($buddyforms_custom_post_type['supports']['title']) && !array_key_exists('none', $buddyforms_custom_post_type['supports'])) {
				$supports['title'] = _x($buddyforms_custom_post_type['supports']['title'], 'Custom Post Title', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['supports']['editor']) && !empty($buddyforms_custom_post_type['supports']['editor']) && !array_key_exists('none', $buddyforms_custom_post_type['supports']))  {
				$supports['editor'] = _x($buddyforms_custom_post_type['supports']['editor'], 'Custom Post Editor', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['supports']['thumbnail']) && !empty($buddyforms_custom_post_type['supports']['thumbnail']) && !array_key_exists('none', $buddyforms_custom_post_type['supports'])) {
				$supports['thumbnail'] = _x($buddyforms_custom_post_type['supports']['thumbnail'], 'Custom Post Featured Image', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['supports']['excerpts']) && !empty($buddyforms_custom_post_type['supports']['excerpts']) && !array_key_exists('none', $buddyforms_custom_post_type['supports'])) {
				$supports['excerpts'] = _x($buddyforms_custom_post_type['supports']['excerpts'], 'Custom Post Excerpt', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['supports']['trackbacks']) && !empty($buddyforms_custom_post_type['supports']['trackbacks']) && !array_key_exists('none', $buddyforms_custom_post_type['supports'])) {
				$supports['trackbacks'] = _x($buddyforms_custom_post_type['supports']['trackbacks'], 'Custom Post Trackbacks', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['supports']['custom-fields']) && !empty($buddyforms_custom_post_type['supports']['custom-fields']) && !array_key_exists('none', $buddyforms_custom_post_type['supports'])) {
				$supports['custom-fields'] = _x($buddyforms_custom_post_type['supports']['custom-fields'], 'Custom Post Custom Fields', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['supports']['comments']) && !empty($buddyforms_custom_post_type['supports']['comments']) && !array_key_exists('none', $buddyforms_custom_post_type['supports'])) {
				$supports['comments'] = _x($buddyforms_custom_post_type['supports']['comments'], 'Custom Post Comments', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['supports']['revisions']) && !empty($buddyforms_custom_post_type['supports']['revisions']) && !array_key_exists('none', $buddyforms_custom_post_type['supports'])) {
				$supports['revisions'] = _x($buddyforms_custom_post_type['supports']['revisions'], 'Custom Post Revisions', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['supports']['author']) && !empty($buddyforms_custom_post_type['supports']['author']) && !array_key_exists('none', $buddyforms_custom_post_type['supports'])) {
				$supports['author'] = _x($buddyforms_custom_post_type['supports']['author'], 'Custom Post Author', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['supports']['page-attributes']) && !empty($buddyforms_custom_post_type['supports']['page-attributes']) && !array_key_exists('none', $buddyforms_custom_post_type['supports'])) {
				$supports['page-attributes'] = _x($buddyforms_custom_post_type['supports']['page-attributes'], 'Custom Post Page Attributes', 'buddyforms');
			}

			if (isset($buddyforms_custom_post_type['supports']['post-formats']) && !empty($buddyforms_custom_post_type['supports']['post-formats']) && !array_key_exists('none', $buddyforms_custom_post_type['supports'])) {
				$supports['post-formats'] = _x($buddyforms_custom_post_type['supports']['post-formats'], 'Custom Post Post Formats', 'buddyforms');
			}


			// taxonomies

			$taxonomies = array();

			if (isset($buddyforms_custom_post_type['taxonomies']['category']) && !empty($buddyforms_custom_post_type['taxonomies']['category'])) {
				array_push($taxonomies, _x($buddyforms_custom_post_type['taxonomies']['category'], 'Custom Post Category', 'buddyforms'));
			}

			if (isset($buddyforms_custom_post_type['taxonomies']['post_tag']) && !empty($buddyforms_custom_post_type['taxonomies']['post_tag'])) {
				array_push($taxonomies, _x($buddyforms_custom_post_type['taxonomies']['post_tag'], 'Custom Post Tag', 'buddyforms'));
			}

			if (isset($buddyforms_custom_post_type['taxonomies']['post_format']) && !empty($buddyforms_custom_post_type['taxonomies']['post_format'])) {
				array_push($taxonomies, _x($buddyforms_custom_post_type['taxonomies']['post_format'], 'Custom Post Format', 'buddyforms'));
			}

			// capability type

			$capability_type = array('post');

			if (isset($buddyforms_custom_post_type['capability_type']) && !empty($buddyforms_custom_post_type['capability_type'])) {
				$capability_type = _x($buddyforms_custom_post_type['capability_type'], 'Custom Post Archive', 'buddyforms');
			}
			
			// has archive

			$has_archive = false;

			if (isset($buddyforms_custom_post_type['has_archive']) && !empty($buddyforms_custom_post_type['has_archive'])) {
				$has_archive = $buddyforms_custom_post_type['has_archive'];
			}

			// missing from Wordpress Buddyforms interface [custom_supports, has_archive_slug, register_meta_box_cb, query_var_slug, show_in_menu_string ]
		
			register_post_type(
				get_post_field( 'post_name' ),
				array(
					'labels' => $labels,
					'description' => $description,
					'public' => $public,
					'hierarchical' => $hierarchical,
					'exclude_from_search' => $exclude_from_search,
					// 'publicly_queryable' => $publicly_queryable,
					'show_ui' => $show_ui,
					'show_in_menu' => true, // otherwise doesn't show up
					// 'menu_position' => $menu_position,
					'show_in_nav_menus' => $show_in_nav_menus,
					'menu_icon' => $menu_icon,
					'capability_type' => $capability_type,
					'has_archive' => $has_archive,
					'show_in_rest' => $show_in_rest,
					// 'rest_base' => $rest_base,
					// 'rest_namespace' => $rest_namespace,
					// 'rest_controller_class' => $rest_controller_class,
					'_builtin' => false,
					// 'rewrite' => $rewrite,
					'query_var' => $query_var,
					'can_export' => $can_export,
					'delete_with_user' => $delete_with_user,
					'supports' => $supports,
					'taxonomies' => $taxonomies,
				)
			);

			

		} // end while
	} // end if
	wp_reset_query();
}

add_action('init', 'buddyforms_create_dynamic_post_types');
