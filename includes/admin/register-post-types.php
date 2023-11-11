<?php

/**
 * Add the FormBuilder and Form Settings MetaBox to the edit screen
 */
add_action('add_meta_boxes', 'buddyforms_add_meta_boxes');
function buddyforms_add_meta_boxes()
{
	global $post, $buddyform;

	if (!$post || $post->post_type != 'buddyforms') {
		return;
	}

	if (!$buddyform) {
		$buddyform = get_post_meta(get_the_ID(), '_buddyforms_options', true);
	}

	if (buddyforms_core_fs()->is_not_paying() && !buddyforms_core_fs()->is_trial()) {
		add_meta_box('buddyforms_form_go_pro', __('Awesome Premium Features', 'buddyforms'), 'buddyforms_metabox_go_pro', 'buddyforms', 'side', 'low');
	}

	if (is_array($buddyform)) {
		add_meta_box('buddyforms_form_shortcodes', __('Shortcodes', 'buddyforms'), 'buddyforms_metabox_shortcodes', 'buddyforms', 'side', 'low');
	}

	// Add the FormBuilder and the Form Setup Metabox
	add_meta_box('buddyforms_form_elements', __('Form Builder', 'buddyforms'), 'buddyforms_metabox_form_elements', 'buddyforms', 'normal', 'high');
	add_meta_box('buddyforms_form_setup', __('Form Setup', 'buddyforms'), 'buddyforms_metabox_form_setup', 'buddyforms', 'normal', 'high');
	add_meta_box('buddyforms_form_designer', __('Form Designer', 'buddyforms'), 'buddyforms_metabox_form_designer', 'buddyforms', 'normal', 'high');

	// NinjaForms jQuery dialog is different from core so we remove the NinjaForms media buttons on the BuddyForms views
	buddyforms_remove_filters_for_anonymous_class('media_buttons', 'NF_Admin_AddFormModal', 'insert_form_tinymce_buttons', 10);
}

add_filter(
		'get_user_option_meta-box-order_buddyforms',
		function () {
			remove_all_actions('edit_form_advanced');
			remove_all_actions('edit_page_form');
		},
		PHP_INT_MAX
);

/**
 * Add the 'buddyforms_metabox' class to all buddyforms related metaboxes to hide the rest.
 */
add_filter('postbox_classes_buddyforms_buddyforms_form_elements', 'buddyforms_metabox_class');
add_filter('buddyforms_metabox_sidebar', 'buddyforms_metabox_class');
add_filter('postbox_classes_buddyforms_buddyforms_form_setup', 'buddyforms_metabox_class');
add_filter('postbox_classes_buddyforms_buddyforms_form_designer', 'buddyforms_metabox_class');
add_filter('postbox_classes_buddyforms_buddyforms_form_shortcodes', 'buddyforms_metabox_class');
add_filter('postbox_classes_buddyforms_buddyforms_form_go_pro', 'buddyforms_metabox_class');


/**
 * Function we use to add a extra class to all BuddyForms related metaboxes.
 *
 * @param $classes
 *
 * @return array
 */
function buddyforms_metabox_class($classes)
{
	$classes[] = 'buddyforms-metabox';

	return $classes;
}

/**
 * Metabox show if form type is posts
 *
 * @param $classes
 *
 * @return array
 */
function buddyforms_metabox_show_if_form_type_post($classes)
{
	$classes[] = 'buddyforms-metabox-show-if-form-type-post';

	return $classes;
}

/**
 * Metabox show if form type is registration
 *
 * @param $classes
 *
 * @return array
 */
function buddyforms_metabox_show_if_form_type_registration($classes)
{
	$classes[] = 'buddyforms-metabox-show-if-form-type-registration';

	return $classes;
}

/**
 * Metabox show if attached page is selected
 *
 * @param $classes
 *
 * @return array
 */
function buddyforms_metabox_show_if_attached_page($classes)
{
	$classes[] = 'buddyforms-metabox-show-if-attached-page';

	return $classes;
}

/**
 * Metabox show if form post type is not none ( bf_submissions )
 *
 * @param $classes
 *
 * @return array
 */
function buddyforms_metabox_show_if_post_type_none($classes)
{
	$classes[] = 'buddyforms-metabox-show-if-post-type-none';

	return $classes;
}

/**
 * Metabox show if form post type is not none ( bf_submissions )
 *
 * @param $classes
 *
 * @return array
 */
function buddyforms_metabox_hide_if_form_type_register($classes)
{
	$classes[] = 'buddyforms-metabox-hide-if-form-type-register';

	return $classes;
}


/**
 * Hide the form during loading to support the wizard and remove unneded metaboxes before all get displayed.
 */
add_action('post_edit_form_tag', 'buddyforms_post_edit_form_tag');
function buddyforms_post_edit_form_tag()
{
	global $post;

	if ($post->post_type != 'buddyforms') {
		return;
	}
	echo 'class="hidden"';
}

/**
 * Update the post
 *
 * @param $data
 * @param $postarr
 *
 * @return mixed
 */
function buddyforms_wp_insert_post_data($data, $postarr)
{
	if (defined('DOING_AJAX') && DOING_AJAX) {
		return $data;
	}

	if (!empty($data['post_type']) && $data['post_type'] === 'buddyforms' && !empty($_POST['buddyforms_options']) && !empty($_POST['buddyforms_options']['slug'])) {
		$new_slug = sanitize_title(wp_unslash($_POST['buddyforms_options']['slug']));
		if (!empty($data['post_name']) && $data['post_name'] !== $new_slug) {
			$result = buddyforms_update_form_slug($data['post_name'], $new_slug);
			if ($result) {
				$data['post_name'] = $new_slug;
			}
		}
	}

	return $data;
}

add_filter('wp_insert_post_data', 'buddyforms_wp_insert_post_data', 10, 2);

/**
 * Adds a box to the main column on the Post and Page edit screens.
 *
 * @param $post_id
 */
function buddyforms_edit_form_save_meta_box_data($post_id)
{
	if (defined('DOING_AJAX') && DOING_AJAX) {
		return;
	}

	if (!isset($_POST['buddyforms_options'])) {
		return;
	}

	$post = WP_Post::get_instance($post_id);

	if (!isset($post->post_type) || $post->post_type != 'buddyforms') {
		return;
	}

	$buddyform = buddyforms_sanitize('', wp_unslash($_POST['buddyforms_options']));

	// Add post title as form name and post name as form slug.
	$buddyform['name'] = $post->post_title;
	$buddyform['slug'] = $post->post_name;

	// make sure the form fields slug and type is sanitised
	if (isset($buddyform['form_fields']) && is_array($buddyform['form_fields'])) {
		foreach ($buddyform['form_fields'] as $key => $field) {
			$buddyform['form_fields'][$key]['slug'] = buddyforms_sanitize_slug($field['slug']);
			$buddyform['form_fields'][$key]['type'] = sanitize_title($field['type']);
		}
	}

	$buddyform = apply_filters('buddyforms_before_update_form_options', $buddyform, $post_id);

	// Update post meta
	update_post_meta($post_id, '_buddyforms_options', $buddyform);

	$form_type = $buddyform['form_type'];

	// Save the Roles and capabilities.
	if (isset($_POST['buddyforms_roles'])) {
		foreach (get_editable_roles() as $role_name => $role_info) {
			$role = get_role($role_name);
			foreach ($role_info['capabilities'] as $capability => $_) {
				$capability_array = explode('_', $capability);
				if ($capability_array[0] == 'buddyforms') {
					if ($capability_array[1] == $buddyform['slug']) {
						$role->remove_cap($capability);
					}
				}
			}
		}

		foreach (buddyforms_sanitize('', wp_unslash($_POST['buddyforms_roles'])) as $form_role => $capabilities) {
			$role = get_role($form_role);
			foreach ($capabilities as $cap) {
				if (buddyforms_core_fs()->is_not_paying() && ($cap === 'draft' || $cap === 'all' || $cap === 'admin-submission')) {
					continue;
				}
				if ($form_type === 'contact' && ($cap !== 'create' && $cap !== 'all')) {
					continue;
				}
				$cap_slug = 'buddyforms_' . $post->post_name . '_' . $cap;
				$role->add_cap($cap_slug);
			}
		}
	}

	// Regenerate the global $buddyforms.
	// The global$buddyforms is sored in the option table and provides all fors and form fields
	buddyforms_regenerate_global_options();

	// Rewrite the page roles and flash permalink if needed
	buddyforms_attached_page_rewrite_rules(true);

	buddyforms_track('builder-save', array('form-type' => $buddyform['form_type']));

	do_action('buddyforms_after_update_form_options', $buddyform['slug'], $buddyform, $post);
}

add_action('save_post', 'buddyforms_edit_form_save_meta_box_data');

/**
 * @param $new_status
 * @param $old_status
 * @param $post
 */
function buddyforms_transition_post_status_regenerate_global_options($new_status, $old_status, $post)
{

	if ($post->post_type != 'buddyforms') {
		return;
	}

	buddyforms_regenerate_global_options();
	buddyforms_attached_page_rewrite_rules(true);

}

add_action('transition_post_status', 'buddyforms_transition_post_status_regenerate_global_options', 10, 3);

/**
 * The global $buddyforms is stored in the option table and provides all forms and form fields for easy access and to save queries.
 * Its super save as it gets regenerated just if a form gets created or updated. Forms are posts of the post type buddyforms. But in most cases the global buddyforms is used.
 * Its one query form the options table and get cashed super great by WordPress and cashing plugins.
 * I have tested this and have done performance checks and it is super fast. Its based on the theory that a user wil not have hundreds of forms. Let us see if this happends.
 * If a user has 10 forms with 10 fields this will be easy if a user has 100 forms with 10 fields we are save to. If a user has 1000 forms with 10o fields we could run into a server timeout
 * I talked to many users and I have no found a use case of so many forms. most people will have under 10. If I find the form number limit, I wil create a script to limit form creation, or rethink the code.
 * For now I build this plugin for people with up to 100 forms in mind and thy will benefit from less query's and speedy forms.
 */

function buddyforms_regenerate_global_options()
{
	// get all forms and update the global
	$posts = get_posts(
			array(
					'numberposts' => -1,
					'post_type' => 'buddyforms',
					'orderby' => 'menu_order title',
					'order' => 'asc',
					'suppress_filters' => false,
					'post_status' => 'publish',
			)
	);

	$buddyforms_forms = array();

	if ($posts) {
		foreach ($posts as $post) {
			$options = get_post_meta($post->ID, '_buddyforms_options', true);
			if ($options) {
				$options['slug'] = $post->post_name;
				$options['name'] = $post->post_title;
				$buddyforms_forms[$post->post_name] = $options;
			}
		}
	}
	update_option('buddyforms_forms', $buddyforms_forms);
}

/**
 * Register the post type
 */
function buddyforms_register_post_type()
{

	// Create BuddyForms post type
	$labels = array(
			'name' => __('BuddyForms', 'buddyforms'),
			'singular_name' => __('BuddyForm', 'buddyforms'),
			'add_new' => __('Add New', 'buddyforms'),
			'add_new_item' => __('Add New Form', 'buddyforms'),
			'edit_item' => __('Edit Form', 'buddyforms'),
			'new_item' => __('New Form', 'buddyforms'),
			'view_item' => __('View Form', 'buddyforms'),
			'search_items' => __('Search BuddyForms', 'buddyforms'),
			'not_found' => __('No BuddyForm found', 'buddyforms'),
			'not_found_in_trash' => __('No Forms found in Trash', 'buddyforms'),
	);

	register_post_type(
			'buddyforms',
			array(
					'labels' => $labels,
					'public' => false,
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
					'publicly_queryable' => false,
					'menu_icon' => 'dashicons-buddyforms',
			)
	);

	$labels = array(
			'name' => 'Profile Builder',
			'singular_name' => 'Profile Builder',
	);
	register_post_type(
			'Profile Builder',
			array(
					'labels' => $labels,
					'public' => false,
					'show_ui' => true,
					'_builtin' => false,
					'capability_type' => 'post',
					'hierarchical' => false,
					'rewrite' => false,
					'supports' => array(
							'title',
					),
					'show_in_menu' => 'edit.php?post_type=buddyforms',
					'exclude_from_search' => true,
					'publicly_queryable' => false,
					'menu_icon' => 'dashicons-buddyforms',
			)
	);

	$labels = array(
			'name' => 'Profile Builder',
			'singular_name' => 'Profile Builder',
	);
	register_post_type(
			'Profile Builder',
			array(
					'labels' => $labels,
					'public' => false,
					'show_ui' => true,
					'_builtin' => false,
					'capability_type' => 'post',
					'hierarchical' => false,
					'rewrite' => false,
					'supports' => array(
							'title',
					),
					'show_in_menu' => 'edit.php?post_type=buddyforms',
					'exclude_from_search' => true,
					'publicly_queryable' => false,
					'menu_icon' => 'dashicons-buddyforms',
			)
	);

	// Create BuddyForms post type
	$labels = array(
			'name' => __('Post Types', 'buddyforms'),
			'singular_name' => __('Post Type', 'buddyforms'),
			'add_new' => __('Add New', 'buddyforms'),
			'add_new_item' => __('Add New Taxonomy', 'buddyforms'),
			'edit_item' => __('Edit Post Type', 'buddyforms'),
			'new_item' => __('New Post Type', 'buddyforms'),
			'view_item' => __('View Post Type', 'buddyforms'),
			'search_items' => __('Search Post Types', 'buddyforms'),
			'not_found' => __('No Post Type found', 'buddyforms'),
			'not_found_in_trash' => __('No Post Type found in Trash', 'buddyforms'),
	);

	register_post_type(
			'bf-post-types',
			array(
					'labels' => $labels,
					'public' => false,
					'show_ui' => true,
					'_builtin' => false,
					'capability_type' => 'post',
					'hierarchical' => false,
					'rewrite' => false,
					'supports' => array(
							'title',
					),
					'show_in_menu' => 'edit.php?post_type=buddyforms',
					'exclude_from_search' => true,
					'publicly_queryable' => false,
					'menu_icon' => 'dashicons-buddyforms',
			)
	);

	// Create BuddyForms post type
	$labels = array(
			'name' => __('Taxonomies', 'buddyforms'),
			'singular_name' => __('Taxonomy', 'buddyforms'),
			'add_new' => __('Add New', 'buddyforms'),
			'add_new_item' => __('Add New Taxonomy', 'buddyforms'),
			'edit_item' => __('Edit Taxonomy', 'buddyforms'),
			'new_item' => __('New Taxonomy', 'buddyforms'),
			'view_item' => __('View Taxonomy', 'buddyforms'),
			'search_items' => __('Search Taxonomies', 'buddyforms'),
			'not_found' => __('No Taxonomy found', 'buddyforms'),
			'not_found_in_trash' => __('No Taxonomy found in Trash', 'buddyforms'),
	);
	register_post_type(
			'bf-taxonomies',
			array(
					'labels' => $labels,
					'public' => false,
					'show_ui' => true,
					'_builtin' => false,
					'capability_type' => 'post',
					'hierarchical' => false,
					'rewrite' => false,
					'supports' => array(
							'title',
					),
					'show_in_menu' => 'edit.php?post_type=buddyforms',
					'exclude_from_search' => true,
					'publicly_queryable' => false,
					'menu_icon' => 'dashicons-buddyforms',
			)
	);

	// Create BuddyForms post type
	$labels = array(
			'name' => __('Submissions', 'buddyforms'),
			'singular_name' => __('Submissions', 'buddyforms'),
		// 'add_new'            => __( 'Add New', 'buddyforms' ),
		// 'add_new_item'       => __( 'Add New Form', 'buddyforms' ),
		// 'edit_item'          => __( 'Edit Form', 'buddyforms' ),
		// 'new_item'           => __( 'New Form', 'buddyforms' ),
		// 'view_item'          => __( 'View Form', 'buddyforms' ),
		// 'search_items'       => __( 'Search BuddyForms', 'buddyforms' ),
		// 'not_found'          => __( 'No BuddyForm found', 'buddyforms' ),
		// 'not_found_in_trash' => __( 'No Forms found in Trash', 'buddyforms' ),
	);

	register_post_type(
			'bf_submissions',
			array(
					'labels' => $labels,
					'public' => false,
					'show_ui' => true,
					'_builtin' => false,
					'capability_type' => 'posts',
					'hierarchical' => false,
					'rewrite' => false,
					'supports' => false,
				// 'show_in_menu'        => 'edit.php?post_type=buddyforms',
					'show_in_menu' => false,
					'exclude_from_search' => true,
					'publicly_queryable' => false,
					'menu_icon' => 'dashicons-buddyforms',
			)
	);

}

add_action('init', 'buddyforms_register_post_type');


//function buddyforms_post_types_add_custom_box()
//{
//	$screens = ['bf-post-types'];
//	foreach ($screens as $screen) {
//		add_meta_box(
//				'wporg_box_id',                 // Unique ID
//				'Custom Meta Box Title',      // Box title
//				'buddyforms_post_types_custom_box_html',  // Content callback, must be of type callable
//				$screen                            // Post type
//		);
//	}
//}

//add_action( 'add_meta_boxes', 'buddyforms_post_types_add_custom_box' );

add_action('edit_form_advanced', 'buddyforms_edit_form_after_title');

function buddyforms_edit_form_after_title($post)
{

//	echo '<pre>';
//	print_r($post);
//	echo '</pre>';
//
//
//	echo $post->post_type;
	if ($post->post_type == 'bf-post-types') {
		buddyforms_post_types_custom_box_html($post);
	}
}

function buddyforms_custom_post_type_save_postdata($post_id)
{
	if (array_key_exists('buddyforms_custom_post_type', $_POST)) {
		update_post_meta(
				$post_id,
				'_buddyforms_custom_post_type',
				$_POST['buddyforms_custom_post_type']
		);
	}
}

add_action('save_post', 'buddyforms_custom_post_type_save_postdata');


function buddyforms_post_types_custom_box_html($post)
{
	$buddyforms_custom_post_type = get_post_meta($post->ID, '_buddyforms_custom_post_type', true);

//	echo '<pre>';
//	print_r($buddyforms_custom_post_type);
//	echo '</pre>';

	?>
	<div id="poststuff">



			<style>
				.entry {
					width: 350px;
					height: 220px;
					background-color: #f0f0f0;
					border: 1px solid #ccc;
					margin: 10px;
					display: inline-block;
					cursor: pointer;
					transition: transform 0.2s;
					overflow: hidden;
					background-size: cover;
					background-position: center;
				}

				.selected {
					border: 2px solid green;
				}

				.deselected {
					opacity: 0.5; /* Adjust the opacity for deselected entries */
				}

				.entry.directory {
					background: #f0f0f0 url('none');
				}

				.entry.blog {
					background: #f0f0f0 url('none');
				}

				.entry.pages {
					background: #f0f0f0 url('none');
				}

				.entry.hidden {
					background: #f0f0f0 url('none');
				}

				.entry:hover {
					transform: scale(1.05);
				}

				.description {
					padding: 10px;
					background-color: #fff;
					text-align: left;
					height: 100%;
				}

				.entry p {
					font-size: 20px;
				}

				.entry span.dashicons {
					font-size: 50px;
					text-align: left;
					float: left;
					padding: 10px;
				}
			</style>
		<h1>Quick Select your Use Case</h1>

		<div class="entry directory dashicons" data-usecase="directory" onclick="selectEntry(this)">
			<span class="dashicons dashicons-list-view"></span>
			<p>Directory with Filter and Search</p>
			<div class="description">Advanced directory with a search and filters to make it easily filterable by any form element. It's suitable for real estate listings, company directories, geo directories and various other types of directories.</div>
		</div>

		<div class="entry blog dashicons" data-usecase="blog" onclick="selectEntry(this)">
			<span class="dashicons dashicons-admin-post"></span>
			<p>Blog or Magazine Style</p>
			<div class="description">This use case is perfect for creating a blog or magazine-style website with an archive of posts. It provides a chronological display of your articles for readers to explore.</div>
		</div>

		<div class="entry pages dashicons" data-usecase="pages" onclick="selectEntry(this)">
			<span class="dashicons dashicons-admin-page"></span>
			<p>WordPress Pages Style Hierarchy</p>
			<div class="description">Create a hierarchy of pages in a WordPress-style structure, with parent pages and child pages. This use case is ideal for organizing and presenting content in a structured manner.</div>
		</div>

		<div class="entry hidden dashicons" data-usecase="hidden" onclick="selectEntry(this)">
			<span class="dashicons dashicons-hidden"></span>
			<p>Hidden Public Post Type</p>
			<div class="description">This use case allows you to create a hidden post type that is publicly accessible but doesn't have an archive. The post exists only through its URL, making it useful for unique, standalone content.</div>
		</div>

		<script>
			function selectEntry(entry) {
				// Remove 'selected' class from all entries
				const entries = document.querySelectorAll('.entry');
				entries.forEach(e => e.classList.remove('selected'));

				// Add 'selected' class to the clicked entry
				entry.classList.add('selected');

				// Remove 'deselected' class from all entries
				entries.forEach(e => e.classList.remove('deselected'));

				// Add 'deselected' class to all entries except the clicked entry
				entries.forEach(e => {
					if (e !== entry) {
						e.classList.add('deselected');
					}
				});
			}
		</script>











		<h1>Advanced Settings </h1>

		<div id="buddyforms_panel_pt_advanced_settings" class="buddyforms-section buddyforms-settings postbox closed">
			<div class="postbox-header">
				<h2 class="hndle ui-sortable-handle">
					<span>General Settings</span>
				</h2>
				<div class="handle-actions hide-if-no-js">
					<button type="button" class="handlediv" aria-expanded="false">
						<span class="screen-reader-text">Toggle panel: General settings</span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				</div>
			</div>
			<div class="inside">
				<div class="main">
					<table class="form-table buddyforms-table">
						<tbody>

			<tr>
				<th scope="row">
					<label for="name">Post Type Slug</label> <span class="required">*</span>
					<p id="slugchanged" class="hidemessage">Slug has changed<span
								class="dashicons dashicons-warning"></span></p>
					<p id="slugexists" class="hidemessage">Slug already exists<span
								class="dashicons dashicons-warning"></span></p>
				</th>
				<td><input type="text" id="name" name="buddyforms_custom_post_type[name]"
						   value="<?php echo isset($buddyforms_custom_post_type['name']) ? $buddyforms_custom_post_type['name'] : '' ?>"
						   maxlength="20"
						   aria-required="true" required="true"><br>
					<p class="buddyforms-field-description description">The post type name/slug. Used for
						various queries for post type content.</p>
					<p class="buddyforms-slug-details">Slugs may only contain lowercase alphanumeric
						characters, dashes, and underscores.</p>
					<p>DO NOT EDIT the post type slug unless also planning to migrate posts. Changing the
						slug registers a new post type entry.</p>
					<div class="buddyforms-spacer"><input type="checkbox" id="update_post_types"
														  name="update_post_types[]"
														  value="update_post_types"><label
								for="update_post_types">Migrate posts to newly renamed post
							type?</label><br></div>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="label">Plural Label</label> <span class="required">*</span></th>
				<td><input type="text" id="label" name="buddyforms_custom_post_type[label_plural]"
						   value="<?php echo isset($buddyforms_custom_post_type['label_plural']) ? $buddyforms_custom_post_type['label_plural'] : ''; ?>"
						   aria-required="true"
						   required="true" placeholder="(e.g. Movies)"><span class="visuallyhidden">(e.g. Movies)</span><br>
					<p class="buddyforms-field-description description">Used for the post type admin menu
						item.</p></td>
			</tr>
			<tr>
				<th scope="row"><label for="singular_label">Singular Label</label> <span
							class="required">*</span></th>
				<td><input type="text" id="singular_label"
						   name="buddyforms_custom_post_type[singular_label]"
						   value="<?php echo isset($buddyforms_custom_post_type['label_singular']) ? $buddyforms_custom_post_type['label_singular'] : ''; ?>"
						   aria-required="true" required="true" placeholder="(e.g. Movie)"><span
							class="visuallyhidden">(e.g. Movie)</span><br>
					<p class="buddyforms-field-description description">Used when a singular label is
						needed.</p></td>
			</tr>
			<tr>
				<th scope="row">Supports<p>Add support for various available post editor features on the
						right. A checked value means the post type feature is supported.</p>
					<p>Use the "None" option to explicitly set "supports" to false.</p>
					<p>Featured images and Post Formats need theme support added, to be used.</p>
					<p>
						<a href="https://developer.wordpress.org/reference/functions/add_theme_support/#post-thumbnails"
						   target="_blank" rel="noopener">Theme support for featured images</a><br><a
								href="https://wordpress.org/support/article/post-formats/" target="_blank"
								rel="noopener">Theme support for post formats</a></p></th>
				<td>
					<fieldset tabindex="0">
						<legend class="screen-reader-text">Post type options</legend>
						<input type="checkbox" id="title" name="cpt_supports[]" value="title"
							   checked="checked"><label for="title">Title</label><br><input type="checkbox"
																							id="editor"
																							name="cpt_supports[]"
																							value="editor"
																							checked="checked"><label
								for="editor">Editor</label><br><input type="checkbox" id="thumbnail"
																	  name="cpt_supports[]"
																	  value="thumbnail"
																	  checked="checked"><label
								for="thumbnail">Featured Image</label><br><input type="checkbox"
																				 id="excerpts"
																				 name="cpt_supports[]"
																				 value="excerpt"><label
								for="excerpts">Excerpt</label><br><input type="checkbox" id="trackbacks"
																		 name="cpt_supports[]"
																		 value="trackbacks"><label
								for="trackbacks">Trackbacks</label><br><input type="checkbox"
																			  id="custom-fields"
																			  name="cpt_supports[]"
																			  value="custom-fields"><label
								for="custom-fields">Custom Fields</label><br><input type="checkbox"
																					id="comments"
																					name="cpt_supports[]"
																					value="comments"><label
								for="comments">Comments</label><br><input type="checkbox" id="revisions"
																		  name="cpt_supports[]"
																		  value="revisions"><label
								for="revisions">Revisions</label><br><input type="checkbox" id="author"
																			name="cpt_supports[]"
																			value="author"><label
								for="author">Author</label><br><input type="checkbox" id="page-attributes"
																	  name="cpt_supports[]"
																	  value="page-attributes"><label
								for="page-attributes">Page Attributes</label><br><input type="checkbox"
																						id="post-formats"
																						name="cpt_supports[]"
																						value="post-formats"><label
								for="post-formats">Post Formats</label><br><input type="checkbox" id="none"
																				  name="cpt_supports[]"
																				  value="none"><label
								for="none">None</label><br></fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="custom_supports">Custom "Supports"</label>
					<p>Use this input to register custom "supports" values, separated by commas. Learn about
						this at <a
								href="http://docs.pluginize.com/article/28-third-party-support-upon-registration"
								target="_blank" rel="noopener">Custom "Supports"</a></p></th>
				<td><input type="text" id="custom_supports"
						   name="buddyforms_custom_post_type[custom_supports]" value=""
						   aria-required="false"><br>
					<p class="buddyforms-field-description description">Provide custom support slugs
						here.</p></td>
			</tr>
			<tr>
				<th scope="row">Taxonomies<p>Add support for available registered taxonomies.</p></th>
				<td>
					<fieldset tabindex="0">
						<legend class="screen-reader-text">Taxonomy options</legend>
						<input type="checkbox" id="category" name="cpt_addon_taxes[]"
							   value="category"><label for="category">Categories (WP Core)</label><br><input
								type="checkbox" id="post_tag" name="cpt_addon_taxes[]"
								value="post_tag"><label for="post_tag">Tags (WP Core)</label><br></fieldset>
				</td>
			</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="buddyforms_panel_pt_basic_settings" class="buddyforms-section postbox closed">
			<div class="postbox-header">
				<h2 class="hndle ui-sortable-handle">
					<span>Directories and Archive</span>
				</h2>
				<div class="handle-actions hide-if-no-js">
					<button type="button" class="handlediv" aria-expanded="false">
						<span class="screen-reader-text">Toggle panel: Basic settings</span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				</div>
			</div>
			<div class="inside">
				<div class="main">
					<table class="form-table buddyforms-table">
						<tbody>
						<tr>
							<th scope="row"><label for="has_archive">Has Archive</label>
								<p>If left blank, the archive slug will default to the post type slug.</p></th>
							<td><select id="has_archive" name="buddyforms_custom_post_type[has_archive]">
									<option value="0" selected="selected">False</option>
									<option value="1">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: false) Whether or not the
									post type will have a post type archive URL.</p><br><input type="text"
																							   id="has_archive_string"
																							   name="buddyforms_custom_post_type[has_archive_string]"
																							   value=""
																							   aria-required="false"
																							   placeholder="Slug to be used for archive URL."><span
										class="visuallyhidden">Slug to be used for archive URL.</span></td>
						</tr>
						<tr>
							<th scope="row"><label for="exclude_from_search">Exclude From Search</label></th>
							<td><select id="exclude_from_search"
										name="buddyforms_custom_post_type[exclude_from_search]">
									<option value="0" selected="selected">False</option>
									<option value="1">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: false) Whether or not to
									exclude posts with this post type from front end search results. This also excludes
									from taxonomy term archives.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="hierarchical">Hierarchical</label></th>
							<td><select id="hierarchical" name="buddyforms_custom_post_type[hierarchical]">
									<option value="0" selected="selected">False</option>
									<option value="1">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: false) Whether or not the
									post type can have parent-child relationships. At least one published content item
									is needed in order to select a parent.</p></td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>


		<div id="buddyforms_panel_pt_basic_settings" class="buddyforms-section postbox closed">
			<div class="postbox-header">
				<h2 class="hndle ui-sortable-handle">
					<span>Capabilities and Permission</span>
				</h2>
				<div class="handle-actions hide-if-no-js">
					<button type="button" class="handlediv" aria-expanded="false">
						<span class="screen-reader-text">Toggle panel: Basic settings</span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				</div>
			</div>
			<div class="inside">
				<div class="main">
					<table class="form-table buddyforms-table">
						<tbody>
						<tr>
							<th scope="row"><label for="capability_type">Capability Type</label></th>
							<td><input type="text" id="capability_type"
									   name="buddyforms_custom_post_type[capability_type]" value="post"
									   aria-required="false"><br>
								<p class="buddyforms-field-description description">The post type to use for checking
									read, edit, and delete capabilities. A comma-separated second value can be used for
									plural version.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="public">Public</label></th>
							<td><select id="public" name="buddyforms_custom_post_type[public]">
									<option value="0">False</option>
									<option value="1" selected="selected">True</option>
								</select>
								<p class="buddyforms-field-description description">(Custom Post Type UI default: true)
									Whether or not posts of this type should be shown in the admin UI and is publicly
									queryable.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="publicly_queryable">Publicly Queryable</label></th>
							<td><select id="publicly_queryable" name="buddyforms_custom_post_type[publicly_queryable]">
									<option value="0">False</option>
									<option value="1" selected="selected">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: true) Whether or not
									queries can be performed on the front end as part of parse_request()</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="show_ui">Show UI</label></th>
							<td><select id="show_ui" name="buddyforms_custom_post_type[show_ui]">
									<option value="0">False</option>
									<option value="1" selected="selected">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: true) Whether or not to
									generate a default UI for managing this post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="show_in_nav_menus">Show in Nav Menus</label></th>
							<td><select id="show_in_nav_menus" name="buddyforms_custom_post_type[show_in_nav_menus]">
									<option value="0">False</option>
									<option value="1" selected="selected">True</option>
								</select>
								<p class="buddyforms-field-description description">(Custom Post Type UI default: true)
									Whether or not this post type is available for selection in navigation menus.</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="delete_with_user">Delete with user</label></th>
							<td><select id="delete_with_user" name="buddyforms_custom_post_type[delete_with_user]">
									<option value="0" selected="selected">False</option>
									<option value="1">True</option>
								</select>
								<p class="buddyforms-field-description description">(buddyforms default: false) Whether
									to delete posts of this type when deleting a user.</p></td>
						</tr>

						</tbody>
					</table>
				</div>
			</div>
		</div>


		<div id="buddyforms_panel_pt_basic_settings" class="buddyforms-section postbox closed">
			<div class="postbox-header">
				<h2 class="hndle ui-sortable-handle">
					<span>Menu</span>
				</h2>
				<div class="handle-actions hide-if-no-js">
					<button type="button" class="handlediv" aria-expanded="false">
						<span class="screen-reader-text">Toggle panel: Basic settings</span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				</div>
			</div>
			<div class="inside">
				<div class="main">
					<table class="form-table buddyforms-table">
						<tbody>

						<tr>
							<th scope="row"><label for="menu_position">Menu Position</label>
								<p>See
									<a href="https://developer.wordpress.org/reference/functions/register_post_type/#menu_position"
									   target="_blank" rel="noopener">Available options</a> in the "menu_position"
									section. Range of 5-100</p></th>
							<td><input type="text" id="menu_position" name="buddyforms_custom_post_type[menu_position]"
									   value="" aria-required="false"><br>
								<p class="buddyforms-field-description description">The position in the menu order the
									post type should appear. show_in_menu must be true.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="show_in_menu">Show in Menu</label>
								<p>"Show UI" must be "true". If an existing top level page such as "tools.php" is
									indicated for second input, post type will be sub menu of that.</p></th>
							<td><select id="show_in_menu" name="buddyforms_custom_post_type[show_in_menu]">
									<option value="0">False</option>
									<option value="1" selected="selected">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: true) Whether or not to
									show the post type in the admin menu and where to show that menu.</p><br><input
										type="text" id="show_in_menu_string"
										name="buddyforms_custom_post_type[show_in_menu_string]" value=""
										aria-required="false"><br>
								<p class="buddyforms-field-description description">The top-level admin menu page file
									name for which the post type should be in the sub menu of.</p></td>
						</tr>
						<tr>
							<th scope="row">
								<div id="menu_icon_preview"></div>
								<label for="menu_icon">Menu Icon</label></th>
							<td><input type="text" id="menu_icon" name="buddyforms_custom_post_type[menu_icon]" value=""
									   aria-required="false" placeholder="(Full URL for icon or Dashicon class)"><span
										class="visuallyhidden">(Full URL for icon or Dashicon class)</span><br>
								<p class="buddyforms-field-description description">Image URL or <a
											href="https://developer.wordpress.org/resource/dashicons/" target="_blank"
											rel="noopener">Dashicon class name</a> to use for icon. Custom image should
									be 20px by 20px.</p>
								<div class="buddyforms-spacer"><input id="buddyforms_choose_dashicon"
																	  class="button dashicons-picker" type="button"
																	  value="Choose dashicon">
									<div class="buddyforms-spacer"><input id="buddyforms_choose_icon" class="button "
																		  type="button" value="Choose image icon"></div>
								</div>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="buddyforms_panel_pt_basic_settings" class="buddyforms-section postbox closed">
			<div class="postbox-header">
				<h2 class="hndle ui-sortable-handle">
					<span>Rest API</span>
				</h2>
				<div class="handle-actions hide-if-no-js">
					<button type="button" class="handlediv" aria-expanded="false">
						<span class="screen-reader-text">Toggle panel: Basic settings</span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				</div>
			</div>
			<div class="inside">
				<div class="main">
					<table class="form-table buddyforms-table">
						<tbody>


						<tr>
							<th scope="row"><label for="show_in_rest">Show in REST API</label></th>
							<td><select id="show_in_rest" name="buddyforms_custom_post_type[show_in_rest]">
									<option value="0">False</option>
									<option value="1" selected="selected">True</option>
								</select>
								<p class="buddyforms-field-description description">(Custom Post Type UI default: true)
									Whether or not to show this post type data in the WP REST API.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="rest_base">REST API base slug</label></th>
							<td><input type="text" id="rest_base" name="buddyforms_custom_post_type[rest_base]" value=""
									   aria-required="false" placeholder="Slug to use in REST API URLs."><span
										class="visuallyhidden">Slug to use in REST API URLs.</span></td>
						</tr>
						<tr>
							<th scope="row"><label for="rest_controller_class">REST API controller class</label></th>
							<td><input type="text" id="rest_controller_class"
									   name="buddyforms_custom_post_type[rest_controller_class]" value=""
									   aria-required="false"
									   placeholder="(default: WP_REST_Posts_Controller) Custom controller to use instead of WP_REST_Posts_Controller."><span
										class="visuallyhidden">(default: WP_REST_Posts_Controller) Custom controller to use instead of WP_REST_Posts_Controller.</span>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="rest_namespace">REST API namespace</label></th>
							<td><input type="text" id="rest_namespace"
									   name="buddyforms_custom_post_type[rest_namespace]" value="" aria-required="false"
									   placeholder="(default: wp/v2) To change the namespace URL of REST API route."><span
										class="visuallyhidden">(default: wp/v2) To change the namespace URL of REST API route.</span>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="buddyforms_panel_pt_advanced_settings" class="buddyforms-section buddyforms-settings postbox closed">
			<div class="postbox-header">
				<h2 class="hndle ui-sortable-handle">
					<span>Advanced Settings</span>
				</h2>
				<div class="handle-actions hide-if-no-js">
					<button type="button" class="handlediv" aria-expanded="false">
						<span class="screen-reader-text">Toggle panel: Basic settings</span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				</div>
			</div>
			<div class="inside">
				<div class="main">
					<table class="form-table buddyforms-table">
						<tbody>

						<tr>
							<th scope="row"><label for="can_export">Can Export</label></th>
							<td><select id="can_export" name="buddyforms_custom_post_type[can_export]">
									<option value="0" selected="selected">False</option>
									<option value="1">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: false) Can this post_type
									be exported.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="rewrite">Rewrite</label></th>
							<td><select id="rewrite" name="buddyforms_custom_post_type[rewrite]">
									<option value="0">False</option>
									<option value="1" selected="selected">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: true) Whether or not
									WordPress should use rewrites for this post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="rewrite_slug">Custom Rewrite Slug</label></th>
							<td><input type="text" id="rewrite_slug" name="buddyforms_custom_post_type[rewrite_slug]"
									   value="" aria-required="false" placeholder="(default: post type slug)"><span
										class="visuallyhidden">(default: post type slug)</span><br>
								<p class="buddyforms-field-description description">Custom post type slug to use instead
									of the default.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="rewrite_withfront">With Front</label></th>
							<td><select id="rewrite_withfront" name="buddyforms_custom_post_type[rewrite_withfront]">
									<option value="0">False</option>
									<option value="1" selected="selected">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: true) Should the permalink
									structure be prepended with the front base. (example: if your permalink structure is
									/blog/, then your links will be: false-&gt;/news/, true-&gt;/blog/news/).</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="query_var">Query Var</label></th>
							<td><select id="query_var" name="buddyforms_custom_post_type[query_var]">
									<option value="0">False</option>
									<option value="1" selected="selected">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: true) Sets the query_var
									key for this post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="query_var_slug">Custom Query Var Slug</label></th>
							<td><input type="text" id="query_var_slug"
									   name="buddyforms_custom_post_type[query_var_slug]" value="" aria-required="false"
									   placeholder="(default: post type slug) Query var needs to be true to use."><span
										class="visuallyhidden">(default: post type slug) Query var needs to be true to use.</span><br>
								<p class="buddyforms-field-description description">Custom query var slug to use instead
									of the default.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="register_meta_box_cb">Metabox callback</label></th>
							<td><input type="text" id="register_meta_box_cb"
									   name="buddyforms_custom_post_type[register_meta_box_cb]" value=""
									   aria-required="false"><br>
								<p class="buddyforms-field-description description">Provide a callback function that
									sets up the meta boxes for the edit form. Do `remove_meta_box()` and
									`add_meta_box()` calls in the callback. Default null.</p></td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="buddyforms_panel_pt_additional_labels" class="buddyforms-section buddyforms-labels postbox closed">
			<div class="postbox-header">
				<h2 class="hndle ui-sortable-handle">
					<span>Labels</span>
				</h2>
				<div class="handle-actions hide-if-no-js">
					<button type="button" class="handlediv" aria-expanded="false">
						<span class="screen-reader-text">Toggle panel: Basic settings</span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				</div>
			</div>
			<div class="inside">
				<div class="main">
					<table class="form-table buddyforms-table">
						<tbody>
						<tr>
							<th scope="row"><label for="description">Post Type Description</label></th>
							<td><textarea id="description" name="buddyforms_custom_post_type[description]" rows="4"
										  cols="40"></textarea><br>
								<p class="buddyforms-field-description description">Perhaps describe what your custom
									post type is used for?</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="menu_name">Menu Name</label></th>
							<td><input type="text" id="menu_name" name="buddyforms_custom_post_type[menu_name]"
									   value="<?php echo isset($buddyforms_custom_post_type['menu_name']) ? $buddyforms_custom_post_type['menu_name'] : ''; ?>"
									   aria-required="false" placeholder="(e.g. My Movies)" data-label="My item"
									   data-plurality="plural"><span class="visuallyhidden">(e.g. My Movies)</span><br>
								<p class="buddyforms-field-description description">Custom admin menu name for your
									custom post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="all_items">All Items</label></th>
							<td><input type="text" id="all_items" name="buddyforms_custom_post_type[label_all_items]"
									   value="<?php echo isset($buddyforms_custom_post_type['label_add_new']) ? $buddyforms_custom_post_type['label_all_items'] : ''; ?>"
									   aria-required="false" placeholder="(e.g. All Movies)" data-label="All item"
									   data-plurality="plural"><span class="visuallyhidden">(e.g. All Movies)</span><br>
								<p class="buddyforms-field-description description">Used in the post type admin
									submenu.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="add_new">Add New</label></th>
							<td><input type="text" id="add_new" name="buddyforms_custom_post_type[add_new]"
									   value="<?php echo isset($buddyforms_custom_post_type['label_add_new']) ? $buddyforms_custom_post_type['label_add_new'] : ''; ?>"
									   aria-required="false" placeholder="(e.g. Add New)" data-label="Add new"
									   data-plurality="plural"><span class="visuallyhidden">(e.g. Add New)</span><br>
								<p class="buddyforms-field-description description">Used in the post type admin
									submenu.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="add_new_item">Add New Item</label></th>
							<td>
								<input type="text" id="add_new_item" name="buddyforms_custom_post_type[add_new_item]"
									   value=""
									   aria-required="false" placeholder="(e.g. Add New Movie)"
									   data-label="Add new item" data-plurality="singular"><span class="visuallyhidden">(e.g. Add New Movie)</span><br>
								<p class="buddyforms-field-description description">Used at the top of the post editor
									screen for a new post type post.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="edit_item">Edit Item</label></th>
							<td><input type="text" id="edit_item" name="buddyforms_custom_post_type[edit_item]" value=""
									   aria-required="false" placeholder="(e.g. Edit Movie)" data-label="Edit item"
									   data-plurality="singular"><span
										class="visuallyhidden">(e.g. Edit Movie)</span><br>
								<p class="buddyforms-field-description description">Used at the top of the post editor
									screen for an existing post type post.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="new_item">New Item</label></th>
							<td><input type="text" id="new_item" name="buddyforms_custom_post_type[new_item]" value=""
									   aria-required="false" placeholder="(e.g. New Movie)" data-label="New item"
									   data-plurality="singular"><span
										class="visuallyhidden">(e.g. New Movie)</span><br>
								<p class="buddyforms-field-description description">Post type label. Used in the admin
									menu for displaying post types.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="view_item">View Item</label></th>
							<td><input type="text" id="view_item" name="buddyforms_custom_post_type[view_item]" value=""
									   aria-required="false" placeholder="(e.g. View Movie)" data-label="View item"
									   data-plurality="singular"><span
										class="visuallyhidden">(e.g. View Movie)</span><br>
								<p class="buddyforms-field-description description">Used in the admin bar when viewing
									editor screen for a published post in the post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="view_items">View Items</label></th>
							<td><input type="text" id="view_items" name="buddyforms_custom_post_type[view_items]"
									   value=""
									   aria-required="false" placeholder="(e.g. View Movies)" data-label="View item"
									   data-plurality="plural"><span
										class="visuallyhidden">(e.g. View Movies)</span><br>
								<p class="buddyforms-field-description description">Used in the admin bar when viewing
									editor screen for a published post in the post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="search_items">Search Item</label></th>
							<td><input type="text" id="search_items" name="buddyforms_custom_post_type[search_items]"
									   value=""
									   aria-required="false" placeholder="(e.g. Search Movies)" data-label="Search item"
									   data-plurality="plural"><span
										class="visuallyhidden">(e.g. Search Movies)</span><br>
								<p class="buddyforms-field-description description">Used as the text for the search
									button on post type list screen.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="not_found">Not Found</label></th>
							<td><input type="text" id="not_found" name="buddyforms_custom_post_type[not_found]" value=""
									   aria-required="false" placeholder="(e.g. No Movies found)"
									   data-label="No item found" data-plurality="plural"><span class="visuallyhidden">(e.g. No Movies found)</span><br>
								<p class="buddyforms-field-description description">Used when there are no posts to
									display on the post type list screen.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="not_found_in_trash">Not Found in Trash</label></th>
							<td><input type="text" id="not_found_in_trash"
									   name="buddyforms_custom_post_type[not_found_in_trash]"
									   value="" aria-required="false" placeholder="(e.g. No Movies found in Trash)"
									   data-label="No item found in trash" data-plurality="plural"><span
										class="visuallyhidden">(e.g. No Movies found in Trash)</span><br>
								<p class="buddyforms-field-description description">Used when there are no posts to
									display on the post type list trash screen.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="parent">Parent</label></th>
							<td><input type="text" id="parent" name="buddyforms_custom_post_type[parent]" value=""
									   aria-required="false"
									   placeholder="(e.g. Parent Movie:)" data-label="Parent item:"
									   data-plurality="singular"><span
										class="visuallyhidden">(e.g. Parent Movie:)</span><br>
								<p class="buddyforms-field-description description">Used for hierarchical types that
									need a colon.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="featured_image">Featured Image</label></th>
							<td><input type="text" id="featured_image"
									   name="buddyforms_custom_post_type[featured_image]" value=""
									   aria-required="false" placeholder="(e.g. Featured image for this movie)"
									   data-label="Featured image for this item" data-plurality="singular"><span
										class="visuallyhidden">(e.g. Featured image for this movie)</span><br>
								<p class="buddyforms-field-description description">Used as the "Featured Image" phrase
									for the post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="set_featured_image">Set Featured Image</label></th>
							<td><input type="text" id="set_featured_image"
									   name="buddyforms_custom_post_type[set_featured_image]"
									   value="" aria-required="false"
									   placeholder="(e.g. Set featured image for this movie)"
									   data-label="Set featured image for this item" data-plurality="singular"><span
										class="visuallyhidden">(e.g. Set featured image for this movie)</span><br>
								<p class="buddyforms-field-description description">Used as the "Set featured image"
									phrase for the post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="remove_featured_image">Remove Featured Image</label></th>
							<td><input type="text" id="remove_featured_image"
									   name="buddyforms_custom_post_type[remove_featured_image]"
									   value="" aria-required="false"
									   placeholder="(e.g. Remove featured image for this movie)"
									   data-label="Remove featured image for this item" data-plurality="singular"><span
										class="visuallyhidden">(e.g. Remove featured image for this movie)</span><br>
								<p class="buddyforms-field-description description">Used as the "Remove featured image"
									phrase for the post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="use_featured_image">Use Featured Image</label></th>
							<td><input type="text" id="use_featured_image"
									   name="buddyforms_custom_post_type[use_featured_image]"
									   value="" aria-required="false"
									   placeholder="(e.g. Use as featured image for this movie)"
									   data-label="Use as featured image for this item" data-plurality="singular"><span
										class="visuallyhidden">(e.g. Use as featured image for this movie)</span><br>
								<p class="buddyforms-field-description description">Used as the "Use as featured image"
									phrase for the post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="archives">Archives</label></th>
							<td><input type="text" id="archives" name="buddyforms_custom_post_type[archives]" value=""
									   aria-required="false" placeholder="(e.g. Movie archives)"
									   data-label="item archives" data-plurality="singular"><span
										class="visuallyhidden">(e.g. Movie archives)</span><br>
								<p class="buddyforms-field-description description">Post type archive label used in nav
									menus.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="insert_into_item">Insert into item</label></th>
							<td><input type="text" id="insert_into_item"
									   name="buddyforms_custom_post_type[insert_into_item]" value=""
									   aria-required="false" placeholder="(e.g. Insert into movie)"
									   data-label="Insert into item" data-plurality="singular"><span
										class="visuallyhidden">(e.g. Insert into movie)</span><br>
								<p class="buddyforms-field-description description">Used as the "Insert into post" or
									"Insert into page" phrase for the post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="uploaded_to_this_item">Uploaded to this Item</label></th>
							<td><input type="text" id="uploaded_to_this_item"
									   name="buddyforms_custom_post_type[uploaded_to_this_item]"
									   value="" aria-required="false" placeholder="(e.g. Uploaded to this movie)"
									   data-label="Upload to this item" data-plurality="singular"><span
										class="visuallyhidden">(e.g. Uploaded to this movie)</span><br>
								<p class="buddyforms-field-description description">Used as the "Uploaded to this post"
									or "Uploaded to this page" phrase for the post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="filter_items_list">Filter Items List</label></th>
							<td><input type="text" id="filter_items_list"
									   name="buddyforms_custom_post_type[filter_items_list]" value=""
									   aria-required="false" placeholder="(e.g. Filter movies list)"
									   data-label="Filter item list" data-plurality="plural"><span
										class="visuallyhidden">(e.g. Filter movies list)</span><br>
								<p class="buddyforms-field-description description">Screen reader text for the filter
									links heading on the post type listing screen.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="items_list_navigation">Items List Navigation</label></th>
							<td><input type="text" id="items_list_navigation"
									   name="buddyforms_custom_post_type[items_list_navigation]"
									   value="" aria-required="false" placeholder="(e.g. Movies list navigation)"
									   data-label="item list navigation" data-plurality="plural"><span
										class="visuallyhidden">(e.g. Movies list navigation)</span><br>
								<p class="buddyforms-field-description description">Screen reader text for the
									pagination heading on the post type listing screen.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="items_list">Items List</label></th>
							<td><input type="text" id="items_list" name="buddyforms_custom_post_type[items_list]"
									   value=""
									   aria-required="false" placeholder="(e.g. Movies list)" data-label="item list"
									   data-plurality="plural"><span
										class="visuallyhidden">(e.g. Movies list)</span><br>
								<p class="buddyforms-field-description description">Screen reader text for the items
									list heading on the post type listing screen.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="attributes">Attributes</label></th>
							<td><input type="text" id="attributes" name="buddyforms_custom_post_type[attributes]"
									   value=""
									   aria-required="false" placeholder="(e.g. Movies Attributes)"
									   data-label="item attributes" data-plurality="plural"><span
										class="visuallyhidden">(e.g. Movies Attributes)</span><br>
								<p class="buddyforms-field-description description">Used for the title of the post
									attributes meta box.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="name_admin_bar">"New" menu in admin bar</label></th>
							<td><input type="text" id="name_admin_bar"
									   name="buddyforms_custom_post_type[name_admin_bar]" value=""
									   aria-required="false" placeholder="(e.g. Movie)" data-label="item"
									   data-plurality="singular"><span class="visuallyhidden">(e.g. Movie)</span><br>
								<p class="buddyforms-field-description description">Used in New in Admin menu bar.
									Default "singular name" label.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="item_published">Item Published</label></th>
							<td><input type="text" id="item_published"
									   name="buddyforms_custom_post_type[item_published]" value=""
									   aria-required="false" placeholder="(e.g. Movie published)"
									   data-label="item published" data-plurality="singular"><span
										class="visuallyhidden">(e.g. Movie published)</span><br>
								<p class="buddyforms-field-description description">Used in the editor notice after
									publishing a post. Default "Post published." / "Page published."</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="item_published_privately">Item Published Privately</label></th>
							<td><input type="text" id="item_published_privately"
									   name="buddyforms_custom_post_type[item_published_privately]" value=""
									   aria-required="false"
									   placeholder="(e.g. Movie published privately.)"
									   data-label="item published privately." data-plurality="singular"><span
										class="visuallyhidden">(e.g. Movie published privately.)</span><br>
								<p class="buddyforms-field-description description">Used in the editor notice after
									publishing a private post. Default "Post published privately." / "Page published
									privately."</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="item_reverted_to_draft">Item Reverted To Draft</label></th>
							<td><input type="text" id="item_reverted_to_draft"
									   name="buddyforms_custom_post_type[item_reverted_to_draft]"
									   value="" aria-required="false" placeholder="(e.g. Movie reverted to draft)"
									   data-label="item reverted to draft." data-plurality="singular"><span
										class="visuallyhidden">(e.g. Movie reverted to draft)</span><br>
								<p class="buddyforms-field-description description">Used in the editor notice after
									reverting a post to draft. Default "Post reverted to draft." / "Page reverted to
									draft."</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="item_scheduled">Item Scheduled</label></th>
							<td><input type="text" id="item_scheduled"
									   name="buddyforms_custom_post_type[item_scheduled]" value=""
									   aria-required="false" placeholder="(e.g. Movie scheduled)"
									   data-label="item scheduled" data-plurality="singular"><span
										class="visuallyhidden">(e.g. Movie scheduled)</span><br>
								<p class="buddyforms-field-description description">Used in the editor notice after
									scheduling a post to be published at a later date. Default "Post scheduled." / "Page
									scheduled."</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="item_updated">Item Updated</label></th>
							<td><input type="text" id="item_updated" name="buddyforms_custom_post_type[item_updated]"
									   value=""
									   aria-required="false" placeholder="(e.g. Movie updated)"
									   data-label="item updated." data-plurality="singular"><span
										class="visuallyhidden">(e.g. Movie updated)</span><br>
								<p class="buddyforms-field-description description">Used in the editor notice after
									updating a post. Default "Post updated." / "Page updated."</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="enter_title_here">Add Title</label></th>
							<td><input type="text" id="enter_title_here"
									   name="buddyforms_custom_post_type[enter_title_here]" value=""
									   aria-required="false" placeholder="(e.g. Add Movie)" data-label="Add item"
									   data-plurality="singular"><span
										class="visuallyhidden">(e.g. Add Movie)</span><br>
								<p class="buddyforms-field-description description">Placeholder text in the "title"
									input when creating a post. Not exportable.</p></td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php
}


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
							'public' => false,
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
							'publicly_queryable' => false,
							'menu_icon' => 'dashicons-buddyforms',
					)
			);

		} // end while
	} // end if
	wp_reset_query();
}

add_action('init', 'buddyforms_create_dynamic_post_types');

function menue_icon_admin_head_css()
{
	BuddyFormsAssets::load_tk_font_icons();
}

add_action('admin_head', 'menue_icon_admin_head_css');

/**
 * Adds a box to the main column on the Post and Page edit screens.
 *
 * @param $messages
 *
 * @return bool
 */
function buddyforms_form_updated_messages($messages)
{
	global $post, $post_ID;

	if ($post->post_type != 'buddyforms') {
		return false;
	}

	$messages['buddyforms'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __('Form updated.', 'buddyforms'),
			2 => __('Custom field updated.', 'buddyforms'),
			3 => __('Custom field deleted.', 'buddyforms'),
			4 => __('Form updated.', 'buddyforms'),
		/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf(__('Form restored to revision from %s', 'buddyforms'), wp_post_revision_title((int)$_GET['revision'], false)) : false,
			6 => __('Form published.', 'buddyforms'),
			7 => __('Form saved.'),
			8 => __('Form submitted.', 'buddyforms'),
			9 => sprintf(__('Form scheduled for: <strong>%1$s</strong>.'), date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date))),
			10 => __('Form draft updated.', 'buddyforms'),
	);

	return $messages;
}

add_filter('post_updated_messages', 'buddyforms_form_updated_messages', 999);

/**
 * Adds a box to the main column on the Post and Page edit screens.
 *
 * @param $columns
 *
 * @return
 */
function set_custom_edit_buddyforms_columns($columns)
{
	unset($columns['date']);
	// $columns['slug']               = __( 'Slug', 'buddyforms' );
	$columns['attached_post_type'] = __('Form Type', 'buddyforms');
	$columns['attached_page'] = __('Logged In User Access', 'buddyforms');
	$columns['shortcode'] = __('Shortcode', 'buddyforms');

	return $columns;
}

add_filter('manage_buddyforms_posts_columns', 'set_custom_edit_buddyforms_columns', 10, 1);

/**
 * Adds a box to the main column on the Post and Page edit screens.
 *
 * @param $column
 * @param $post_id
 */
function custom_buddyforms_column($column, $post_id)
{

	$post = get_post($post_id);
	$buddyform = get_post_meta($post_id, '_buddyforms_options', true);

	switch ($column) {
		case 'slug':
			echo wp_kses($post->post_name, buddyforms_wp_kses_allowed_atts());
			break;
		case 'attached_post_type':
			if (!isset($buddyform['form_type'])) {
				$post_type_html = '<p>' . __('Contact Form', 'buddyforms') . '</p>';
			} elseif ($buddyform['form_type'] == 'contact') {
				$post_type_html = '<p>' . __('Contact Form', 'buddyforms') . '</p>';
			} elseif ($buddyform['form_type'] == 'post') {
				$post_type_html = '<p>' . __('Post Submissions', 'buddyforms') . ' <br> ' . __('Post Type: ', 'buddyforms') . $buddyform['post_type'] . '</p>';
			} elseif ($buddyform['form_type'] == 'registration') {
				$post_type_html = '<p>' . __('Registration Form', 'buddyforms') . '</p>';
			}

			echo wp_kses($post_type_html, buddyforms_wp_kses_allowed_atts());
			break;
		case 'attached_page':
			if (isset($buddyform['attached_page']) && empty($buddyform['attached_page'])) {
				$attached_page = '<p style="color: red;">' . __('No Page Attached', 'buddyforms') . '</p>';
			} elseif (isset($buddyform['attached_page']) && $attached_page_title = get_the_title($buddyform['attached_page'])) {
				$attached_page = '<p>' . __('On', 'buddyforms') . '</p>';// . '<br>' . $attached_page_title . '</p>';
			} else {
				$attached_page = 'Off';
			}

			echo wp_kses($attached_page, buddyforms_wp_kses_allowed_atts());

			if ($attached_page != 'Off') {
				$attached_page_permalink = isset($buddyform['attached_page']) ? get_permalink($buddyform['attached_page']) : ''; ?>
				<div class="row-actions">
					<span class="view-form">
						<a target="_blank"
						   href="<?php echo esc_attr($attached_page_permalink) . 'create/' . esc_attr($post->post_name); ?>"><?php esc_html_e('View Form', 'buddyforms'); ?></a> |
					</span>
					<span class="view-entryies">
						<a target="_blank"
						   href="<?php echo esc_attr($attached_page_permalink) . 'view/' . esc_attr($post->post_name); ?>"><?php esc_html_e('View Entries', 'buddyforms'); ?></a>
					</span>

				</div>
				<?php
			}
			break;
		case 'shortcode':
			echo '[bf form_slug="' . esc_html($post->post_name) . '"]';
			break;
	}
}

add_action('manage_buddyforms_posts_custom_column', 'custom_buddyforms_column', 10, 2);

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function buddyforms_hide_publishing_actions()
{
	global $post;

	if (get_post_type($post) == 'buddyforms') {
		?>
		<style type="text/css">
			.misc-pub-visibility,
			.misc-pub-curtime,
			.misc-pub-post-status {
				display: none;
			}

			h1 {
				display: none;
			}

			.metabox-prefs label {
				/* float: right; */
				/* margin-top: 57px; */
				width: 100%;
			}

			/* Sven Quick Fix ToDo: Konrad please check it;) */
			.wrap .wp-heading-inline + .page-title-action {
				display: none;
			}

			@media screen and (max-width: 790px) {
				#buddyforms_version {
					display: none;
				}
			}

		</style>
		<?php
		if (get_post_type($post) == 'buddyforms' && !isset($_GET['wizard']) || isset($_GET['wizard']) && $_GET['wizard'] != 'done' || (isset($_GET['post_type']) && $_GET['post_type'] == 'buddyforms')) {
			?>
			<script>
				jQuery(document).ready(function (jQuery) {
					jQuery('body').find('h1:first').css('line-height', '58px');
					jQuery('body').find('h1:first').css('margin-top', '20px');
					jQuery('body').find('h1:first').css('font-size', '30px');
					jQuery('body').find('h1:first').css('width', '100%');
					<?php
					$tmp = '<div id="buddyforms-adminhead-wizard" style="font-size: 52px; margin-top: -5px; float: left; margin-right: 15px;" class="tk-icon-buddyforms"></div> BuddyForms';
					if (!isset($_GET['wizard'])) {
						$tmp .= ' <a href="post-new.php?post_type=buddyforms" class="page-title-action">' . __('Add New', 'buddyforms') . '</a> <a class="page-title-action" href="edit.php?post_type=buddyforms&page=buddyforms_settings&tab=import" id="btn-open">' . __('Import', 'buddyforms') . '</a> <a class="page-title-action" href="https://docs.buddyforms.com/" target="_blank" id="btn-open">' . __('Documentation', 'buddyforms') . '</a> <a href="edit.php?post_type=buddyforms&page=buddyforms-contact" class="page-title-action" id="btn-open">' . __('Contact Us', 'buddyforms') . '</a>';
					}
					echo "var h1 = jQuery('body').find('h1:first');";

					echo "h1.html('" . wp_kses($tmp, buddyforms_wp_kses_allowed_atts()) . "');";

					$tmp = '<small id="buddyforms_version" style="line-height: 1; margin-top: -10px; color: #888; font-size: 13px; padding-top: 30px; float:right;">' . buddyforms_get_version_type() . ' ' . __('Version', 'buddyforms') . ' ' . BUDDYFORMS_VERSION . '</small>';
					echo "h1.append('" . wp_kses($tmp, buddyforms_wp_kses_allowed_atts()) . "');";

					// echo "jQuery('" . $tmp . "').insertAfter(h1);";
					?>
					jQuery('h1').show();
				});
			</script>
			<?php
		} else {
			?>
			<script>
				jQuery(document).ready(function (jQuery) {
					jQuery('body').find('h1:first').remove();
				});
			</script>
			<?php
		}
	}
}

add_action('admin_head-edit.php', 'buddyforms_hide_publishing_actions');
add_action('admin_head-post.php', 'buddyforms_hide_publishing_actions');
add_action('admin_head-post-new.php', 'buddyforms_hide_publishing_actions');

//
// Add new Actions Buttons to the publish metabox
//
function buddyforms_add_button_to_submit_box()
{
	global $post;

	if (get_post_type($post) != 'buddyforms') {
		return;
	}

	$buddyform = get_post_meta($post->ID, '_buddyforms_options', true);
	$attached_page_permalink = isset($buddyform['attached_page']) ? get_permalink($buddyform['attached_page']) : '';

	$base = home_url();

	$preview_page_id = get_option('buddyforms_preview_page', true);
	?>
	<div id="buddyforms-actions" class="misc-pub-section">
		<?php if (isset($post->post_name) && $post->post_name != '') { ?>
			<div id="frontend-actions">
				<a class="button button-large bf_button_action" target="_blank"
				   href="<?php echo esc_attr($base); ?>/?page_id=<?php echo esc_attr($preview_page_id); ?>&preview=true&form_slug=<?php echo esc_attr($post->post_name); ?>"><span
							class="dashicons dashicons-visibility"></span> <?php esc_html_e('Preview Form', 'buddyforms'); ?>
				</a>
			</div>
		<?php } ?>
		<?php if (isset($buddyform['attached_page']) && isset($buddyform['post_type']) && $buddyform['attached_page'] != 'none') { ?>
			<div class="bf-tile actions">
				<div id="frontend-actions">
					<label for="button"><?php esc_html_e('Frontend', 'buddyforms'); ?></label>
					<?php
					$preview_form_url = $attached_page_permalink . 'create/' . $post->post_name . '/';
					$url_request = curl_init($preview_form_url);
					curl_setopt($url_request, CURLOPT_RETURNTRANSFER, true);
					$httpCode = curl_getinfo($url_request, CURLINFO_HTTP_CODE);
					if ($httpCode == 404) {
						flush_rewrite_rules();
					}
					curl_close($url_request);
					?>
					<?php
					echo '<a class="button button-large bf_button_action" href="' . esc_attr($attached_page_permalink) . 'view/' . esc_attr($post->post_name) . '/" target="_new"><span class="dashicons dashicons-admin-page"></span> ' . esc_html__('Your Submissions', 'buddyforms') . '</a>
                <a class="button button-large bf_button_action" href="' . esc_attr($attached_page_permalink) . 'create/' . esc_attr($post->post_name) . '/" target="_new"><span class="dashicons dashicons-feedback"></span>    ' . esc_html__('The Form', 'buddyforms') . '</a>';
					?>
				</div>
			</div>
			<?php
		}
		if (isset($post->post_name) && $post->post_name != '') {
			?>
			<div class="bf-tile actions">
				<div id="admin-actions">
					<label for="button"><?php esc_html_e('Admin', 'buddyforms'); ?></label>
					<?php echo '<a class="button button-large bf_button_action" href="edit.php?post_type=buddyforms&page=buddyforms_submissions&form_slug=' . esc_attr($post->post_name) . '"><span class="dashicons dashicons-email"></span> ' . esc_html__('Submissions', 'buddyforms') . '</a>'; ?>
				</div>
			</div>
		<?php } ?>

		<div class="clear"></div>
	</div>

	<?php

}

add_action('post_submitbox_misc_actions', 'buddyforms_add_button_to_submit_box');


function buddyforms_add_go_pro_metabox()
{

}

// remove the slugdiv metabox from buddyforms post edit screen
function buddyforms_remove_slugdiv()
{
	remove_meta_box('slugdiv', 'buddyforms', 'normal');
}

add_action('admin_menu', 'buddyforms_remove_slugdiv');

// Add the actions to list table
/**
 * @param $actions
 * @param $post
 *
 * @return mixed
 */
function buddyforms_add_action_buttons($actions, $post)
{

	if (get_post_type() === 'buddyforms') {

		$url = add_query_arg(
				array(
						'post_id' => $post->ID,
						'my_action' => 'export_form',
				)
		);

		unset($actions['inline hide-if-no-js']);

		$base = home_url();

		$preview_page_id = get_option('buddyforms_preview_page', true);

		$actions['export'] = '<a href="' . esc_url($url) . '">' . __('Export', 'buddyforms') . '</a>';
		$actions['submissions'] = '<a href="?post_type=buddyforms&page=buddyforms_submissions&form_slug=' . $post->post_name . '">' . __('View Submissions', 'buddyforms') . '</a>';
		$actions['preview_link'] = '<a target="_blank" href="' . $base . '/?page_id=' . $preview_page_id . '&preview=true&form_slug=' . $post->post_name . '">' . __('Preview Form', 'buddyforms') . '</a>';

	}

	return $actions;
}

add_filter('post_row_actions', 'buddyforms_add_action_buttons', 10, 2);


function buddyforms_export_form()
{
	if (isset($_REQUEST['my_action']) && 'export_form' == $_REQUEST['my_action']) {

		$buddyform_options = get_post_meta(filter_var(wp_unslash($_REQUEST['post_id']), FILTER_VALIDATE_INT), '_buddyforms_options', true);

		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename="BuddyFormsExport.json"');
		echo json_encode($buddyform_options);
		exit;
	}
}

add_action('admin_init', 'buddyforms_export_form');

add_filter('hidden_meta_boxes', 'custom_hidden_meta_boxes');
function custom_hidden_meta_boxes($hidden)
{
	global $post;

	if (get_post_type($post) != 'buddyforms') {
		return $hidden;
	}

	$hidden = array();

	return $hidden;
}
