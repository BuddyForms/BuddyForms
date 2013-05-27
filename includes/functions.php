<?php
/**
 * If single and if the post type is selected for buddypress and if there is post meta to display. 
 * hook the post meta to the right places.
 * 
 * This function is an example how you can hook fields into templates in your buddyforms extention
 * of curse you can also use get_post_meta(sanitize_title('name'))
 *
 * @package buddyforms
 * @since 0.2-beta
*/
function buddyforms_form_display_element_frontend(){
	global $buddyforms, $post, $bp;
	
	if(!is_single($post))
		return;
					
	if (!isset($buddyforms['selected_post_types']))
		return;

	$post_type = get_post_type($post);
	
	if (!in_array($post_type, $buddyforms['selected_post_types']))
		return;
		
	if (!empty($buddyforms['bp_post_types'][$post_type]['form_fields'])) {
			
		foreach ($buddyforms['bp_post_types'][$post_type]['form_fields'] as $key => $customfield) :
			if(isset($customfield['slug'])){
				$slug = $customfield['slug'];
			} else {
				$slug = sanitize_title($customfield['name']);
			}
			
			$customfield_value = get_post_meta($post->ID, $slug, true);
			
			if ($customfield_value != '' && $customfield['display'] != 'no') :
				
				$post_meta_tmp = '<div class="post_meta ' . $slug . '">';
				
				if($customfield['display_name'])
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
						 apply_filters('buddyforms_form_element_display_frontend',$customfield,$post_type);
						break;
				}
				
				$post_meta_tmp .= $meta_tmp;
				
				$post_meta_tmp .= '</div>';
				apply_filters('buddyforms_form_element_display_frontend_before_hook',$post_meta_tmp);
				add_action($customfield['display'], create_function('', 'echo "' . addcslashes($post_meta_tmp, '"') . '";'));
			endif;
		endforeach;
	}
}
add_action('wp_head','buddyforms_form_display_element_frontend');

/**
 * Get the buddyforms template directory.
 *
 * @author Sven Lehnert
 * @since 0.1 beta
 *
 * @uses apply_filters()
 * @return string
 */
function buddyforms_get_template_directory() {
	return apply_filters('buddyforms_get_template_directory', constant('BUDDYFORMS_TEMPLATE_PATH'));
}

/**
 * Locate a template
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function buddyforms_locate_template($file) {
	if (locate_template(array($file), false)) {
		locate_template(array($file), true);
	} else {
		include (BUDDYFORMS_TEMPLATE_PATH . $file);
	}
}
?>