<?php

function cpt4bp_form_element_single_hooks($form_element_hooks,$post_type,$field_id){
		array_push($form_element_hooks,
			'bp_before_blog_single_post',
			'bp_after_blog_single_post'
		);

	return $form_element_hooks;
}
add_filter('form_element_hooks','cpt4bp_form_element_single_hooks',1,3);

function cpt4bp_form_display_element_frontend(){
	global $cpt4bp, $post, $bp;
	
	if(!is_single($post))
		return;
					
	if (!isset($cpt4bp['selected_post_types']))
		return;

	$post_type = get_post_type($post);
	
	if (!in_array($post_type, $cpt4bp['selected_post_types']))
		return;
		
	if (!empty($cpt4bp['bp_post_types'][$post_type]['form_fields'])) {
		foreach ($cpt4bp['bp_post_types'][$post_type]['form_fields'] as $key => $customfield) :
			$customfield_value = get_post_meta($post->ID, sanitize_title($customfield['name']), true);
			if ($customfield_value != '' && $customfield['display'] != 'no') :
				$post_meta_tmp = '<div class="post_meta ' . sanitize_title($customfield['name']) . '">';
				$post_meta_tmp .= '<lable>' . $customfield['name'] . '</lable>';
				
				$meta_tmp = $meta_tmp = "<p>". $customfield_value ."</p>";
				
				switch ($customfield['type']) {
					case 'Taxonomy':
						$meta_tmp = "<p>". get_the_term_list( $post->ID, $customfield['taxonomy'] )."</p>";
						break;
					case 'Link':
						$meta_tmp = "<p><a href='" . $customfield_value . "' " . $customfield['name'] . ">" . $customfield_value . " </a></p>";
						break;
					default:
						 apply_filters('cpt4bp_form_element_display_frontend',$customfield,$post_type);
						break;
				}
				
				$post_meta_tmp .= $meta_tmp;
				
				$post_meta_tmp .= '</div>';
				apply_filters('cpt4bp_form_element_display_frontend_before_hook',$post_meta_tmp);
				add_action($customfield['display'], create_function('', 'echo "' . addcslashes($post_meta_tmp, '"') . '";'));
			endif;
		endforeach;
	}
}
add_action('wp_head','cpt4bp_form_display_element_frontend');

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
				bp_get_template_part( 'cpt4bp/members/members-post-display' );
			"));
			} elseif ($bp->current_action == 'create') {
				add_action('bp_template_content', create_function('', "
				bp_get_template_part( 'cpt4bp/members/members-post-create' );
			"));
			}
		}
	}

	return apply_filters('cpt4bp_load_template_filter', $found_template);
}

add_filter('bp_located_template', 'cpt4bp_load_template_filter', 10, 2);


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