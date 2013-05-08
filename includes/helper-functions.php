<?php
 /**
 * this function is a bit tricky and needs some fixing.
 * I have not find a way to overwrite the group home and use the new template system.
 * If someone can have a look into this one would be greate!
 *
 * @author svenl77
 * @since 0.1
 *
 * @uses apply_filters()
 * @return string
 */

function cpt4bp_groups_load_template_filter($found_template, $templates) {
	global $bp;

	if ($bp->current_component == BP_GROUPS_SLUG && $bp->current_action == 'home') {
		$templates = cpt4bp_locate_template('cpt4bp/bp/groups-home.php');
		exit ;
	}

	return apply_filters('cpt4bp_load_template_filter', $found_template);
}

/**
 * Get the CPT4BP template directory.
 *
 * @author Sven Lehnert
 * @since 0.1 beta
 *
 * @uses apply_filters()
 * @return string
 */
function cpt4bp_get_template_directory() {
	return apply_filters('cpt4bp_get_template_directory', constant('CPT4BP_TEMPLATE_PATH'));
}

/** TEMPLATE LOADER ************************************************/

/**
 * CPT4BP template loader.
 *
 * This function sets up CPT4BP to use custom templates.
 *
 * If a template does not exist in the current theme, we will use our own
 * bundled templates.
 *
 * We're doing two things here:
 *  1) Support the older template format for themes that are using them
 *     for backwards-compatibility (the template passed in
 *     {@link bp_core_load_template()}).
 *  2) Route older template names to use our new template locations and
 *     format.
 *
 * View the inline doc for more details.
 *
 * @since 1.0
 */
function cpt4bp_load_template_filter($found_template, $templates) {
	global $bp;

	if ($bp->current_action == 'create' || $bp->current_action == 'my-posts') {

		if (empty($found_template)) {
			// register our theme compat directory
			//
			// this tells BP to look for templates in our plugin directory last
			// when the template isn't found in the parent / child theme
			bp_register_template_stack('cpt4bp_get_template_directory', 14);

			// locate_template() will attempt to find the plugins.php template in the
			// child and parent theme and return the located template when found
			//
			// plugins.php is the preferred template to use, since all we'd need to do is
			// inject our content into BP
			//
			// note: this is only really relevant for bp-default themes as theme compat
			// will kick in on its own when this template isn't found
			$found_template = locate_template('members/single/plugins.php', false, false);

			// add our hook to inject content into BP
			
			if ($bp->current_action == 'my-posts') {
				add_action('bp_template_content', create_function('', "
				bp_get_template_part( 'cpt4bp/bp/members-post-display' );
			"));
			} elseif ($bp->current_action == 'create') {
				add_action('bp_template_content', create_function('', "
				bp_get_template_part( 'cpt4bp/bp/members-post-create' );
			"));
			}
		}
	}

	return apply_filters('cpt4bp_load_template_filter', $found_template);
}

add_filter('bp_located_template', 'cpt4bp_load_template_filter', 10, 2);

/**
 * Delete a product post
 *
 * @package CPT4BP
 * @since 0.1-beta
 */
function cpt4bp_delete_product_post($group_id) {
	$groups_post_id = groups_get_groupmeta($group_id, 'group_post_id');

	wp_delete_post($groups_post_id);
}

add_action('groups_before_delete_group', 'cpt4bp_delete_product_post');

/**
 * Update a product post
 *
 * @package CPT4BP
 * @since 0.1-beta
 */
function cpt4bp_group_header_fields_save($group_id) {
	$groups_post_id = groups_get_groupmeta($group_id, 'group_post_id');
	$posttype = groups_get_groupmeta($group_id, 'group_type');

	$my_post = array('ID' => $groups_post_id, 'post_title' => $_POST['group-name'], 'post_content' => $_POST['group-desc']);

	// update the new post
	$post_id = wp_update_post($my_post);
}
add_action('groups_group_details_edited', 'cpt4bp_group_header_fields_save');

/**
 * Locate a template
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function cpt4bp_locate_template($file) {
	if (locate_template(array($file), false)) {
		locate_template(array($file), true);
	} else {
		include (CPT4BP_TEMPLATE_PATH . $file);
	}
}
?>