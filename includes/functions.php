<?php

//add_action( 'wp_ajax_buddyforms_form_ajax', 'buddyforms_form_ajax' );
//add_action( 'wp_ajax_nopriv_buddyforms_form_ajax', 'buddyforms_form_ajax' );
function buddyforms_form_ajax() {
	
	$args = array(
		'posttype' => 'post',
		'the_post' => 0,
		'post_id' => $_POST['post_id']
		);
		
	buddyforms_create_edit_form( $args ); 
	die();
}

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
					
	if (!isset($buddyforms['buddyforms']))
		return;

	$post_type = get_post_type($post);
	
	foreach ($buddyforms['buddyforms'] as $key => $buddyform) {
		if($buddyforms['buddyforms'][$key]['post_type'] != 'none' &&  $buddyforms['buddyforms'][$key]['post_type'] == $post_type)
			$form = $buddyforms['buddyforms'][$key]['slug'];
	}
		
	if (!empty($buddyforms['buddyforms'][$form]['form_fields'])) {
			
		foreach ($buddyforms['buddyforms'][$form]['form_fields'] as $key => $customfield) :
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
				
				
				$meta_tmp = "<p>". $customfield_value ."</p>";
				
				if(is_array($customfield_value))
					$meta_tmp = "<p>". implode(',' , $customfield_value)."</p>";
			
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