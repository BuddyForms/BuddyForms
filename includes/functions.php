<?php
/**
 * Add the forms to the admin bar
 *
 * @package BuddyForms
 * @since 0.3 beta
 */
add_action( 'wp_before_admin_bar_render', 'buddyforms_wp_before_admin_bar_render', 1, 2 );
function buddyforms_wp_before_admin_bar_render() {
	global $wp_admin_bar, $buddyforms;

	if ( ! $buddyforms ) {
		return;
	}

	foreach ( $buddyforms as $key => $buddyform ) {

		if ( ! isset( $buddyform['post_type'] ) || $buddyform['post_type'] == 'none' ) {
			continue;
		}

		if ( isset( $buddyform['admin_bar'][0] ) && $buddyform['post_type'] != 'none' && ! empty( $buddyform['attached_page'] ) ) {

			if ( current_user_can( 'buddyforms_' . $key . '_create' ) ) {
				$permalink = get_permalink( $buddyform['attached_page'] );
				$wp_admin_bar->add_menu( array(
					'parent' => 'my-account',
					'id'     => 'my-account-' . $buddyform['slug'],
					'title'  => $buddyform['name'],
					'href'   => $permalink
				) );
				$wp_admin_bar->add_menu( array(
					'parent' => 'my-account-' . $buddyform['slug'],
					'id'     => 'my-account-' . $buddyform['slug'] . '-view',
					'title'  => __( 'View my ', 'buddyforms' ) . $buddyform['name'],
					'href'   => $permalink . '/view/' . $buddyform['slug'] . '/'
				) );
				$wp_admin_bar->add_menu( array(
					'parent' => 'my-account-' . $buddyform['slug'],
					'id'     => 'my-account-' . $buddyform['slug'] . '-new',
					'title'  => __( 'New ', 'buddyforms' ) . $buddyform['singular_name'],
					'href'   => $permalink . 'create/' . $buddyform['slug'] . '/'
				) );
			}
		}
	}
}

// Create the buddyforms post status array.
// Other Plugins use the filter buddyforms_get_post_status_array to add there post status to the options array
/**
 * @param bool $select_condition
 *
 * @return mixed|void
 */
function buddyforms_get_post_status_array() {

	$status_array = array(
		'publish' => __( 'Published', 'buddyforms' ),
		'pending' => __( 'Pending Review', 'buddyforms' ),
		'draft'   => __( 'Draft', 'buddyforms' ),
		'future'  => __( 'Scheduled', 'buddyforms' ),
		'private' => __( 'Privately Published', 'buddyforms' ),
		'trash'   => __( 'Trash', 'buddyforms' ),
	);

	return apply_filters( 'buddyforms_get_post_status_array', $status_array );
}

/**
 * Restricting users to view only media library items they upload.
 *
 * @package BuddyForms
 * @since 0.5 beta
 */
add_action( 'pre_get_posts', 'buddyforms_restrict_media_library' );
/**
 * @param $wp_query_obj
 */
function buddyforms_restrict_media_library( $wp_query_obj ) {
	global $current_user, $pagenow;

	if ( is_super_admin( $current_user->ID ) ) {
		return;
	}

	if ( ! is_a( $current_user, 'WP_User' ) ) {
		return;
	}

	if ( 'admin-ajax.php' != $pagenow || $_REQUEST['action'] != 'query-attachments' ) {
		return;
	}

	if ( ! current_user_can( 'manage_media_library' ) ) {
		$wp_query_obj->set( 'author', $current_user->ID );
	}

	return;
}

/**
 * Check if a subscriber have the needed rights to upload images and add this capabilities if needed.
 *
 * @package BuddyForms
 * @since 0.5 beta
 */
add_action( 'init', 'buddyforms_allow_subscriber_uploads' );
function buddyforms_allow_subscriber_uploads() {

	if ( current_user_can( 'subscriber' ) && ! current_user_can( 'upload_files' ) ) {
		$contributor = get_role( 'subscriber' );

		$contributor->add_cap( 'upload_files' );
	}

}

/**
 * Get the BuddyForms template directory.
 *
 * @package BuddyForms
 * @since 0.1 beta
 *
 * @uses apply_filters()
 * @return string
 */
function buddyforms_get_template_directory() {
	return apply_filters( 'buddyforms_get_template_directory', constant( 'BUDDYFORMS_TEMPLATE_PATH' ) );
}

/**
 * Locate a template
 *
 * @package BuddyForms
 * @since 0.1 beta
 *
 * @param $slug
 */
function buddyforms_locate_template( $slug ) {
	global $buddyforms, $bp, $the_lp_query, $current_user, $form_slug, $post_id;

	// Get the current user so its not needed in the templates
	$current_user = wp_get_current_user();

	// create the plugin template path
	$template_path = BUDDYFORMS_TEMPLATE_PATH . 'buddyforms/' . $slug . '.php';

	// Check if template exist in the child or parent theme and use this path if available
	if ( $template_file = locate_template( "buddyforms/{$slug}.php", false, false ) ) {
		$template_path = $template_file;
	}

	// Do the include
	include( $template_path );

}

// Display the WordPress Login Form
function buddyforms_wp_login_form() {
	// Get The Login Form
	echo buddyforms_get_wp_login_form();
}

// Create the BuddyForms Login Form
/**
 * @return mixed|string|void
 */
function buddyforms_get_wp_login_form( $form_slug = 'none', $title = '' ) {
	global $buddyforms;

	if ( empty( $title ) ) {
		$title = __( 'You need to be logged in to view this page', 'buddyforms' );
	}

	$wp_login_form = '<h3>' . $title . '</h3>';
	$wp_login_form .= wp_login_form( array( 'echo' => false ) );

	if ( $form_slug != 'none' ) {

		if ( $buddyforms[ $form_slug ]['public_submit'] == 'registration_form' && $buddyforms[ $form_slug ]['logged_in_only_reg_form'] != 'none' ) {
			$reg_form_slug = $buddyforms[ $form_slug ]['logged_in_only_reg_form'];

			set_query_var( 'bf_form_slug', $reg_form_slug );

			$wp_login_form = do_shortcode( '[bf form_slug="' . $reg_form_slug . '"]' );
		}
	}

	$wp_login_form = apply_filters( 'buddyforms_wp_login_form', $wp_login_form );

	return $wp_login_form;
}

// Helper Function to get the Get the REQUEST_URI Vars
/**
 * @param $name
 *
 * @return int
 */
function buddyforms_get_url_var( $name ) {
	$strURL  = $_SERVER['REQUEST_URI'];
	$arrVals = explode( "/", $strURL );
	$found   = 0;
	foreach ( $arrVals as $index => $value ) {
		if ( $value == $name ) {
			$found = $index;
		}
	}
	$place = $found + 1;

	return ( $found == 0 ) ? 1 : $arrVals[ $place ];
}


/**
 * Queue some JavaScript code to be output in the footer.
 *
 * @param string $code
 */
function buddyforms_enqueue_js( $code ) {
	global $wc_queued_js;

	if ( empty( $wc_queued_js ) ) {
		$wc_queued_js = '';
	}

	$wc_queued_js .= "\n" . $code . "\n";
}


/**
 * Display edit post link for post.
 *
 * @since 1.0.0
 *
 * @param string $text Optional. Anchor text.
 * @param string $before Optional. Display before edit link.
 * @param string $after Optional. Display after edit link.
 * @param int $id Optional. Post ID.
 */
function buddyforms_edit_post_link( $text = null, $before = '', $after = '', $id = 0 ) {
	if ( ! $post = get_post( $id ) ) {
		return;
	}

	if ( ! $url = buddyforms_get_edit_post_link( $post->ID ) ) {
		return;
	}

	if ( null === $text ) {
		$text = __( 'Edit This' );
	}

	$link = '<a title="' . __( 'Edit', 'buddyforms' ) . '" class="post-edit-link" href="' . $url . '"><span aria-label="' . __( 'Edit', 'buddyforms' ) . '" class="dashicons dashicons-edit"></span></a>';

	/**
	 * Filter the post edit link anchor tag.
	 *
	 * @since 2.3.0
	 *
	 * @param string $link Anchor tag for the edit link.
	 * @param int $post_id Post ID.
	 * @param string $text Anchor text.
	 */
	echo $before . apply_filters( 'edit_post_link', $link, $post->ID, $text ) . $after;
}

/**
 * @param $form_slug
 */
function buddyforms_post_entry_actions( $form_slug ) {
	global $buddyforms, $post;

	if ( $buddyforms[ $form_slug ]['attached_page'] == 'none' ) {
		return;
	}
	?>
    <ul class="edit_links">
		<?php
		if ( buddyforms_is_author( $post->ID ) ) {

			$permalink = get_permalink( $buddyforms[ $form_slug ]['attached_page'] );
			$permalink = apply_filters( 'buddyforms_the_loop_edit_permalink', $permalink, $buddyforms[ $form_slug ]['attached_page'] );

			if ( is_multisite() ) {
				if ( apply_filters( 'buddyforms_enable_multisite', false ) ) {
					if ( isset( $buddyforms[ $form_slug ]['blog_id'] ) ) {

						$current_site = get_current_site();
						$form_blog_id = $buddyforms[ $form_slug ]['blog_id'];


						if ( $current_site->blog_id != $form_blog_id ) {
							$form_site = get_blog_details( $form_blog_id, array( 'blog_id', 'blogname' ) );

							$permalink = str_replace( $form_site->path, $current_site->path, $permalink );
						}
					}
				}
			}

			ob_start();

			$post_form_slug = get_post_meta( $post->ID, '_bf_form_slug', true );

			if ( $post_form_slug ) {
				$form_slug = $post_form_slug;
			}

			if ( current_user_can( 'buddyforms_' . $form_slug . '_edit' ) ) {
				echo '<li>';
				if ( isset( $buddyforms[ $form_slug ]['edit_link'] ) && $buddyforms[ $form_slug ]['edit_link'] != 'none' ) {
					echo apply_filters( 'buddyforms_loop_edit_post_link', '<a title="' . __( 'Edit', 'buddyforms' ) . '" id="' . get_the_ID() . '" class="bf_edit_post" href="' . $permalink . 'edit/' . $form_slug . '/' . get_the_ID() . '"><span aria-label="' . __( 'Edit', 'buddyforms' ) . '" class="dashicons dashicons-edit"></span></a>', get_the_ID() );
				} else {
					echo apply_filters( 'buddyforms_loop_edit_post_link', buddyforms_edit_post_link( '<span aria-label="' . __( 'Edit', 'buddyforms' ) . '" class="dashicons dashicons-edit"></span>' ), get_the_ID(), $form_slug );
				}
				echo '</li>';
			}
			if ( current_user_can( 'buddyforms_' . $form_slug . '_delete' ) ) {
				echo '<li>';
				echo '<a title="Delete"  id="' . get_the_ID() . '" class="bf_delete_post" href="#"><span aria-label="' . __( 'Delete', 'buddyforms' ) . '" title="' . __( 'Delete', 'buddyforms' ) . '" class="dashicons dashicons-trash"></span></a></li>';
				echo '</li>';
			}

			// Add custom actions to the entry
			do_action( 'buddyforms_the_loop_actions', get_the_ID() );

			$meta_tmp = ob_get_clean();

			// Display all actions
			echo apply_filters( 'buddyforms_the_loop_meta_html', $meta_tmp );
		} ?>
    </ul>
	<?php
}

function buddyforms_is_author( $post_id ) {

	$is_author = false;

	if ( get_post_field( 'post_author', $post_id ) == get_current_user_id() ) {
		$is_author = true;
	}

	$form_slug = get_post_field( '_bf_form_slug', $post_id );

	$is_author = apply_filters( 'buddyforms_user_can_edit', $is_author, $form_slug, $post_id );

	return $is_author;
}

/**
 * @param $post_status
 */
function buddyforms_post_status_readable( $post_status ) {
	echo buddyforms_get_post_status_readable( $post_status );
}

/**
 * @param $post_status
 *
 * @return mixed|string|void
 */
function buddyforms_get_post_status_readable( $post_status ) {
	if ( $post_status == 'publish' ) {
		return __( 'Published', 'buddyforms' );
	}

	if ( $post_status == 'draft' ) {
		return __( 'Draft', 'buddyforms' );
	}

	if ( $post_status == 'pending' ) {
		return __( 'Pending Review', 'buddyforms' );
	}

	if ( $post_status == 'future' ) {
		return __( 'Scheduled', 'buddyforms' );
	}

	if ( $post_status == 'awaiting-review' ) {
		return __( 'Awaiting Review', 'buddyforms' );
	}

	if ( $post_status == 'edit-draft' ) {
		return __( 'Edit Draft', 'buddyforms' );
	}

	return apply_filters( 'buddyforms_get_post_status_readable', $post_status );
}

/**
 * @param $post_status
 * @param $form_slug
 */
function buddyforms_post_status_css_class( $post_status, $form_slug ) {
	echo buddyforms_get_post_status_css_class( $post_status, $form_slug );
}

/**
 * @param $post_status
 * @param $form_slug
 *
 * @return mixed|void
 */
function buddyforms_get_post_status_css_class( $post_status, $form_slug ) {

	$post_status_css = $post_status;

	if ( $post_status == 'pending' ) {
		$post_status_css = 'bf-pending';
	}

	return apply_filters( 'buddyforms_post_status_css', $post_status_css, $form_slug );
}

/**
 * Allow to remove method for an hook when, it's a class method used and class don't have global for instanciation !
 *
 * @param string $hook_name
 * @param string $method_name
 * @param int $priority
 *
 * @return bool
 */
function buddyforms_remove_filters_with_method_name( $hook_name = '', $method_name = '', $priority = 0 ) {
	global $wp_filter;

	// Take only filters on right hook name and priority
	if ( ! isset( $wp_filter[ $hook_name ][ $priority ] ) || ! is_array( $wp_filter[ $hook_name ][ $priority ] ) ) {
		return false;
	}

	// Loop on filters registered
	foreach ( (array) $wp_filter[ $hook_name ][ $priority ] as $unique_id => $filter_array ) {
		// Test if filter is an array ! (always for class/method)
		if ( isset( $filter_array['function'] ) && is_array( $filter_array['function'] ) ) {
			// Test if object is a class and method is equal to param !
			if ( is_object( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) && $filter_array['function'][1] == $method_name ) {
				unset( $wp_filter[ $hook_name ][ $priority ][ $unique_id ] );
			}
		}

	}

	return false;
}

/**
 * Allow to remove method for an hook when, it's a class method used and class don't have variable, but you know the class name :)
 *
 * @param string $hook_name
 * @param string $class_name
 * @param string $method_name
 * @param int $priority
 *
 * @return bool
 */
function buddyforms_remove_filters_for_anonymous_class( $hook_name = '', $class_name = '', $method_name = '', $priority = 0 ) {
	global $wp_filter;

	// Take only filters on right hook name and priority
	if ( ! isset( $wp_filter[ $hook_name ][ $priority ] ) || ! is_array( $wp_filter[ $hook_name ][ $priority ] ) ) {
		return false;
	}

	// Loop on filters registered
	foreach ( (array) $wp_filter[ $hook_name ][ $priority ] as $unique_id => $filter_array ) {
		// Test if filter is an array ! (always for class/method)
		if ( isset( $filter_array['function'] ) && is_array( $filter_array['function'] ) ) {
			// Test if object is a class, class and method is equal to param !
			if ( is_object( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) == $class_name && $filter_array['function'][1] == $method_name ) {
				unset( $wp_filter[ $hook_name ][ $priority ][ $unique_id ] );
			}
		}

	}

	return false;
}

/**
 * Get all taxonomies
 *
 * @package BuddyForms
 * @since 0.1-beta
 *
 * @param $post_type
 *
 * @return
 */
function buddyforms_taxonomies( $post_type ) {


	$taxonomies_array = get_object_taxonomies( $post_type, 'objects' );

	$taxonomies['none'] = 'Select a Taxonomy';

	foreach ( $taxonomies_array as $tax_slug => $tax ) {
		$taxonomies[ $tax->name ] = $tax->label;
	}


	return $taxonomies;
}

function buddyforms_metabox_go_pro() {

	buddyforms_go_pro( '<span></span>', '', array(
		'Priority Support',
		'More Form Elements',
		'More Options',
	), false );
	buddyforms_go_pro( '<span></span>', __( 'Full Control', 'buddyforms' ), array(
		'Use your form in the backend admin edit screen like ACF',
		'Control who can create, edit and delete content',
		'Registration Options',
		'Disable ajax form submission',
		'Local Storage',
		'More Notification Options',
		'Import - Export Forms',
	), false );
	buddyforms_go_pro( '<span></span>', __( 'Permissions Management', 'buddyforms' ), array(
		'Manage User Roles',
		'Manage Capabilities',
		'More Validation Options'
	), false );
	buddyforms_go_pro( '<span></span>', __( 'More Post Options', 'buddyforms' ), array(
		'All Post Types',
		'Posts Revision',
		'Comment Status',
		'Enable Login on the form',
		'Create an account during submission?',
		'Featured Image Support'
	), false );
	buddyforms_go_pro( '<span></span>', __( 'Know Your User', 'buddyforms' ) . '<p><small>' . __( 'Get deep Insights about your Submitter', 'buddyforms' ) . '</small></p>', array(
		'IP Address',
		'Referer',
		'Browser',
		'Platform',
		'Reports',
		'User Agent',
	) );
}

// Get field by slug
function buddyforms_get_form_field_by_slug( $form_slug, $slug ) {
	global $buddyforms;

	if ( isset( $buddyforms[ $form_slug ]['form_fields'] ) ) : foreach ( $buddyforms[ $form_slug ]['form_fields'] as $field_key => $field ) {
		if ( $field['slug'] == $slug ) {
			return $field;
		}
	} endif;

	return false;
}

//
// Add Placeholder support top the wp editor
//
add_filter( 'mce_external_plugins', 'buddyforms_add_mce_placeholder_plugin' );
function buddyforms_add_mce_placeholder_plugin( $plugins ) {

	if ( is_admin() ) {
		return $plugins;
	}

	$plugins['placeholder'] = BUDDYFORMS_PLUGIN_URL . 'assets/resources/wp-tinymce-placeholder/mce.placeholder.js';

	return $plugins;
}

/**
 * Add garlic support to the wp editor for local save the content of the textarea
 *
 * @param $initArray
 *
 * @return mixed
 */
function buddyforms_tinymce_setup_function( $initArray ) {
	$initArray['setup'] = 'function(editor) {
                editor.on("change keyup", function(e){
                    editor.save(); 
                    jQuery(editor.getElement()).trigger(\'change\');
                });
            }';

	return $initArray;
}

/**
 *
 * Will return the form slug from post meta or the default. none if no form is attached
 *
 * @param $post_id
 *
 * @return mixed
 */
function buddyforms_get_form_slug_by_post_id( $post_id ) {

	$value = get_post_meta( $post_id, '_bf_form_slug', true );

	$buddyforms_posttypes_default = get_option( 'buddyforms_posttypes_default' );

	$post_type = get_post_type( $post_id );

	if ( ! $value && isset( $buddyforms_posttypes_default[ $post_type ] ) || isset( $value ) && $value == 'none' ) {
		$value = $buddyforms_posttypes_default[ $post_type ];
	}

	return $value;
}


function buddyforms_get_post_types() {
	$post_types = array();

	// Generate the Post Type Array 'none' == Contact Form
	$post_types['bf_submissions'] = 'none';
	$post_types['post']           = 'Post';
	$post_types['page']           = 'Page';

	if ( buddyforms_core_fs()->is__premium_only() ) {
		if ( buddyforms_core_fs()->is_plan( 'professional' ) ) {

			// Get all post types
			$post_types = get_post_types( array( 'show_ui' => true ), 'names', 'and' );

			// Generate the Post Type Array 'none' == Contact Form
			$post_types['bf_submissions'] = 'none';

			$post_types = buddyforms_sort_array_by_Array( $post_types, array( 'bf_submissions' ) );

			// Remove the 'buddyforms' post type from the post type array
			unset( $post_types['buddyforms'] );

			$post_types = apply_filters( 'buddyforms_form_builder_post_type', $post_types );

		}
	}

	return $post_types;
}


function buddyforms_get_all_pages( $type = 'id' ) {

	// get the page_on_front and exclude it from the query. This page should not get used for the endpoints
	$page_on_front = get_option( 'page_on_front' );

	$pages = get_pages( array(
		'sort_order'  => 'asc',
		'sort_column' => 'post_title',
		'parent'      => 0,
		'post_type'   => 'page',
		'post_status' => 'publish',
		'exclude'     => isset( $page_on_front ) ? $page_on_front : 0
	) );


	$all_pages         = Array();
	$all_pages['none'] = __( 'Select a Page', 'buddyforms' );

	if ( $type == 'id' ) {
		// Generate the pages array by id
		foreach ( $pages as $page ) {
			$all_pages[ $page->ID ] = $page->post_title;
		}
	}


	if ( $type == 'name' ) {
		foreach ( $pages as $page ) {
			$all_pages[ $page->post_name ] = $page->post_title;
		}

	}

	return $all_pages;
}