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

/**
 * Create the buddyforms post status array.
 * Other Plugins use the filter buddyforms_get_post_status_array to add there post status to the options array
 *
 * @return array
 */
function buddyforms_get_post_status_array() {

	$status_array = array(
		'publish' => __( 'Publish', 'buddyforms' ),
		'pending' => __( 'Pending Review', 'buddyforms' ),
		'draft'   => __( 'Draft', 'buddyforms' ),
		'future'  => __( 'Schedule', 'buddyforms' ),
		'private' => __( 'Privately Publish', 'buddyforms' ),
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
 *
 * @param $form_slug
 * @since 2.3.1
 */
function buddyforms_locate_template( $slug, $form_slug = '' ) {
	global $buddyforms, $bp, $the_lp_query, $current_user, $post_id;

	//Backguard compatibility @sinde 2.3.3.
	if ( empty( $form_slug ) ) {
		global $form_slug;
	}

	// Get the current user so its not needed in the templates
	$current_user = wp_get_current_user();

	// create the plugin template path
	$template_path = BUDDYFORMS_TEMPLATE_PATH . 'buddyforms/' . $slug . '.php';

	// Check if template exist in the child or parent theme and use this path if available
	if ( $template_file = locate_template( "buddyforms/{$slug}.php", false, false ) ) {
		$template_path = $template_file;
	}
	$empty_post_message = __( 'There were no posts found. Create your first post now! ', 'buddyforms' );
	if ( ! empty( $form_slug ) ) {
		if ( ! empty( $buddyforms[ $form_slug ]['empty_submit_list_message_text'] ) ) {
			$empty_post_message = do_shortcode( $buddyforms[ $form_slug ]['empty_submit_list_message_text'] );
		} else {
			$empty_post_message = do_shortcode( buddyforms_default_message_on_empty_submission_list() );
		}
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
 * @return string|boolean
 */
function buddyforms_get_wp_login_form( $form_slug = 'none', $title = '', $args = array() ) {
	global $buddyforms;

	if ( is_admin() ) {
		return false;
	}

	$redirect_url = $label_username = $label_password = $label_remember = $label_log_in = '';

	extract( shortcode_atts( array(
		'redirect_url'   => home_url(),
		'label_username' => __( 'Username or Email Address', 'buddyforms' ),
		'label_password' => __( 'Password', 'buddyforms' ),
		'label_remember' => __( 'Remember Me', 'buddyforms' ),
		'label_log_in'   => __( 'Log In', 'buddyforms' ),
	), $args ) );

	if ( empty( $title ) ) {
		$title = __( 'You need to be logged in to view this page', 'buddyforms' );
	}

	$wp_login_form = '<h3>' . $title . '</h3>';
	$wp_login_form .= wp_login_form(
	        array(
	                'echo' => false,
                    'redirect' => $redirect_url,
                    'id_username' => 'bf_user_name',
                    'id_password' => 'bf_user_pass' ,
                    'label_username' => $label_username,
                    'label_password' => $label_password,
                    'label_remember' => $label_remember,
                    'label_log_in'   => $label_log_in,
            )
    );

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


add_filter( 'login_form_bottom', 'buddyforms_register_link' );
function buddyforms_register_link( $wp_login_form ) {

	$buddyforms_registration_page = get_option( 'buddyforms_registration_page' );
	if ( $buddyforms_registration_page != 'none' ) {
		$permalink = get_permalink( $buddyforms_registration_page );
	} else {
		$permalink = site_url( '/wp-login.php?action=register&redirect_to=' . get_permalink() );
	}

	// new login page
	$wp_login_form .= '<a href="' . $permalink . '">' . __( 'Register', 'buddyforms' ) . '</a> ';

	return $wp_login_form;
}


add_action( 'login_form_bottom', 'buddyforms_add_lost_password_link' );
function buddyforms_add_lost_password_link( $wp_login_form ) {
	return $wp_login_form .= '<a href="' . esc_url( wp_lostpassword_url() ) . '">' . __( 'Lost Password?', 'buddyforms' ) . '</a> ';
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
 *
 * @param bool $echo
 *
 * @since 2.3.1
 *
 * @return string|void
 */
function buddyforms_edit_post_link( $text = null, $before = '', $after = '', $id = 0, $echo = true ) {
	if ( ! $post = get_post( $id ) ) {
		return;
	}

	if ( ! $url = buddyforms_get_edit_post_link( $post->ID ) ) {
		return;
	}

	if ( null === $text ) {
		$text = __( 'Edit This' );
	}

	$link = '<a title="' . __( 'Edit', 'buddyforms' ) . '" class="post-edit-link" href="' . $url . '"><span aria-label="' . __( 'Edit', 'buddyforms' ) . '" class="dashicons dashicons-edit"> </span></a>';

	/**
	 * Filter the post edit link anchor tag.
	 *
	 * @since 2.3.0
	 *
	 * @param string $link Anchor tag for the edit link.
	 * @param int $post_id Post ID.
	 * @param string $text Anchor text.
	 */
	$result = $before . apply_filters( 'edit_post_link', $link, $post->ID, $text ) . $after;

	if ( $echo ) {
		echo $result;
	} else {
		return $result;
	}
}

/**
 * @param $form_slug
 */
function buddyforms_post_entry_actions( $form_slug ) {
	if ( empty( $form_slug ) ) {
		echo '';
		return;
	}
	global $buddyforms, $post;
	if ( ! isset( $buddyforms[ $form_slug ] ) || $buddyforms[ $form_slug ]['attached_page'] == 'none' ) {
		echo '';
		return;
	}
	?>
    <ul class="edit_links">
		<?php
        $is_author = buddyforms_is_author( $post->ID );
        $user_can_all_submission = current_user_can( 'buddyforms_' . $form_slug . '_all' );
		if ( $is_author || $user_can_all_submission && (isset($buddyforms[ $form_slug ]['attached_page']))) {

			$permalink = '';
			if ( ! empty( $buddyforms[ $form_slug ]['attached_page'] ) ) {
				$permalink = get_permalink( $buddyforms[ $form_slug ]['attached_page'] );
				$permalink = apply_filters( 'buddyforms_the_loop_edit_permalink', $permalink, $buddyforms[ $form_slug ]['attached_page'] );
			}
			if ( empty( $permalink ) ) {
				return;
			}

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

			if ( isset( $buddyforms[ $form_slug ]['form_type'] ) && $buddyforms[ $form_slug ]['form_type'] != 'contact' ) {
				if ( current_user_can( 'buddyforms_' . $form_slug . '_edit' ) || current_user_can( 'buddyforms_' . $form_slug . '_all' ) ) {
					echo '<li>';
					if ( isset( $buddyforms[ $form_slug ]['edit_link'] ) && $buddyforms[ $form_slug ]['edit_link'] != 'none' ) {
						echo apply_filters( 'buddyforms_loop_edit_post_link', '<a title="' . __( 'Edit', 'buddyforms' ) . '" id="' . get_the_ID() . '" class="bf_edit_post" href="' . $permalink . 'edit/' . $form_slug . '/' . get_the_ID() . '"><span aria-label="' . __( 'Edit', 'buddyforms' ) . '" class="dashicons dashicons-edit"> </span> ' . __( 'Edit', 'buddyforms' ) . '</a>', get_the_ID() );
					} else {
						echo apply_filters( 'buddyforms_loop_edit_post_link', buddyforms_edit_post_link( '<span aria-label="' . __( 'Edit', 'buddyforms' ) . '" class="dashicons dashicons-edit"> </span> ' . __( 'Edit', 'buddyforms' ), '', '', 0, false), get_the_ID(), $form_slug );
					}
					echo '</li>';
				}
			}
			if ( current_user_can( 'buddyforms_' . $form_slug . '_delete' ) || current_user_can( 'buddyforms_' . $form_slug . '_all' ) ) {
				echo '<li>';
				echo '<a title="' . __( 'Delete', 'buddyforms' ) . '"  id="' . get_the_ID() . '" class="bf_delete_post" href="#"><span aria-label="' . __( 'Delete', 'buddyforms' ) . '" title="' . __( 'Delete', 'buddyforms' ) . '" class="dashicons dashicons-trash"> </span> ' . __( 'Delete', 'buddyforms' ) . '</a></li>';
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

/**
 * Determinate if the current user is the user of the given post
 *
 * @param $post_id
 *
 * @return bool
 */
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
 * @return string
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
 * @return string
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

	buddyforms_go_pro( '<span> </span>', '', array(
		__( 'Priority Support', 'buddyforms' ),
		__( 'More Form Elements', 'buddyforms' ),
		__( 'More Options', 'buddyforms' ),
	), false );
	buddyforms_go_pro( '<span> </span>', __( 'Full Control', 'buddyforms' ), array(
		__( 'Use your form in the backend admin edit screen like ACF', 'buddyforms' ),
		__( 'Control who can create, edit and delete content', 'buddyforms' ),
		__( 'Registration Options', 'buddyforms' ),
		__( 'Disable ajax form submission', 'buddyforms' ),
		__( 'Local Storage', 'buddyforms' ),
		__( 'More Notification Options', 'buddyforms' ),
		__( 'Import - Export Forms', 'buddyforms' ),
	), false );
	buddyforms_go_pro( '<span> </span>', __( 'Permissions Management', 'buddyforms' ), array(
		__( 'Manage User Roles', 'buddyforms' ),
		__( 'Manage Capabilities', 'buddyforms' ),
		__( 'More Validation Options', 'buddyforms' )
	), false );
	buddyforms_go_pro( '<span> </span>', __( 'More Post Options', 'buddyforms' ), array(
		__( 'All Post Types', 'buddyforms' ),
		__( 'Posts Revision', 'buddyforms' ),
		__( 'Comment Status', 'buddyforms' ),
		__( 'Enable Login on the form', 'buddyforms' ),
		__( 'Create an account during submission?', 'buddyforms' ),
		__( 'Featured Image Support', 'buddyforms' )
	), false );
	buddyforms_go_pro( '<span> </span>', __( 'Know Your User', 'buddyforms' ) . '<p><small>' . __( 'Get deep Insights about your Submitter', 'buddyforms' ) . '</small></p>', array(
		__( 'IP Address', 'buddyforms' ),
		__( 'Referer', 'buddyforms' ),
		__( 'Browser', 'buddyforms' ),
		__( 'Platform', 'buddyforms' ),
		__( 'Reports', 'buddyforms' ),
		__( 'User Agent', 'buddyforms' ),
	) );
}

/**
 * Get field by slug
 *
 * @author Sven edited by gfirem
 *
 * @param $form_slug
 * @param $field_slug
 *
 * @return bool|array
 */
function buddyforms_get_form_field_by_slug( $form_slug, $field_slug ) {
	$result_field = wp_cache_get( 'buddyforms_get_field_' . $field_slug . '_in_form_' . $form_slug, 'buddyforms' );
	if ( $result_field === false ) {
		global $buddyforms;
		if ( isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {
			foreach ( $buddyforms[ $form_slug ]['form_fields'] as $field_key => $field ) {
				if ( $field['slug'] == $field_slug ) {
					$result_field = $field;
					wp_cache_set( 'buddyforms_get_field_' . $field_slug . '_in_form_' . $form_slug, $result_field, 'buddyforms' );

					return $result_field;
				}
			}
		}
	}

	return $result_field;
}

/**
 * Return teh array of field belong to the form.
 *
 * @param $form_slug
 *
 * @return bool|array
 */
function buddyforms_get_form_fields( $form_slug ) {
	$result_field = wp_cache_get( 'buddyforms_get_form_fields' . $form_slug, 'buddyforms' );
	if ( $result_field === false ) {
		global $buddyforms;
		if ( empty( $form_slug ) ) {
			return false;
		}
		if ( isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {
			$result_fields = $buddyforms[ $form_slug ]['form_fields'];
			wp_cache_set( 'buddyforms_get_form_fields' . $form_slug, $result_fields, 'buddyforms' );

			return $result_fields;
		}
	}

	return $result_field;
}

/**
 * Check if field type exist in a form
 *
 * @param $form_slug
 * @param $field_type
 *
 * @return bool
 */
function buddyforms_exist_field_type_in_form( $form_slug, $field_type ) {
	$fields = buddyforms_get_form_fields( $form_slug );
	$exist  = false;
	foreach ( $fields as $field ) {
		if ( $field['type'] == $field_type ) {
			$exist = true;
			break;
		}
	}

	return $exist;
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
 * Get a form by slug
 *
 * @param $form_slug
 *
 * @return bool|array
 */
function buddyforms_get_form_by_slug( $form_slug ) {
	$value = wp_cache_get( 'buddyforms_form_by_slug_' . $form_slug, 'buddyforms' );
	if ( $value === false ) {
		global $buddyforms;
		if ( isset( $buddyforms[ $form_slug ] ) ) {
			$value = $buddyforms[ $form_slug ];
			wp_cache_set( 'buddyforms_form_by_slug_' . $form_slug, $value, 'buddyforms' );
		}
	}

	return $value;
}

/**
 * Will return the form slug from post meta or the default. none if no form is attached
 *
 * @author Sven edited by gfirem
 *
 * @param $post_id
 *
 * @return mixed
 */
function buddyforms_get_form_slug_by_post_id( $post_id ) {
	$value = wp_cache_get( 'buddyform_form_slug_' . $post_id, 'buddyforms' );
	if ( $value === false ) {
		$value = get_post_meta( $post_id, '_bf_form_slug', true );

		$buddyforms_posttypes_default = get_option( 'buddyforms_posttypes_default' );

		$post_type = get_post_type( $post_id );

		if ( ! $value && isset( $buddyforms_posttypes_default[ $post_type ] ) || isset( $value ) && $value == 'none' ) {
			$value = $buddyforms_posttypes_default[ $post_type ];
		}
		wp_cache_set( 'buddyform_form_slug_' . $post_id, $value, 'buddyforms' );
	}

	return $value;
}

/**
 * Get the post types for teh created forms
 *
 * @return array
 */
function buddyforms_get_post_types_from_forms() {
	global $buddyforms;
	$result = array();
	if ( ! empty( $buddyforms ) ) {
		foreach ( $buddyforms as $form ) {
			$result[] = $form['post_type'];
		}
		$result = array_unique( $result );
	}

	return $result;
}


function buddyforms_get_post_types() {
	$post_types = array();

	// Generate the Post Type Array 'none' == Contact Form
	$post_types['bf_submissions'] = __( 'none', 'buddyforms' );
	$post_types['post']           = __( 'Post', 'buddyforms' );
	$post_types['page']           = __( 'Page', 'buddyforms' );

	if ( buddyforms_core_fs()->is__premium_only() ) {
		if ( buddyforms_core_fs()->is_plan( 'professional' ) || buddyforms_core_fs()->is_trial() ) {

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


function buddyforms_get_all_pages( $type = 'id', $view = "form_builder" ) {

	// get the page_on_front and exclude it from the query. This page should not get used for the endpoints
	$page_on_front = get_option( 'page_on_front' );
	$exclude       = isset( $page_on_front ) ? $page_on_front : '';

	if ( $view == 'form_builder' ) {
		$buddyforms_registration_page = get_option( 'buddyforms_registration_page' );
		$exclude                      .= isset( $buddyforms_registration_page ) ? $buddyforms_registration_page : '';
	}


	$pages = get_pages( array(
		'sort_order'  => 'asc',
		'sort_column' => 'post_title',
		'parent'      => 0,
		'post_type'   => 'page',
		'post_status' => 'publish',
		'exclude'     => $exclude
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

add_action( 'admin_bar_menu', 'buddyform_admin_bar_shortcut', 60 );
/**
 * Add a short-code to the admin toolbar to edit the form in the current screen
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function buddyform_admin_bar_shortcut( $wp_admin_bar ) {
	if ( is_admin() && is_user_logged_in() ) {
		return;
	}

	global $post, $buddyforms;

	if ( empty( $post->ID ) ) {
		return;
	}
	$form_slug = '';
	global $wp_query;
	if ( ! empty( $wp_query->query_vars['bf_form_slug'] ) ) {
		$form_slug = sanitize_title( $wp_query->query_vars['bf_form_slug'] );
	} else if ( ! empty( $post->post_name ) ) {
		$form_slug = $post->post_name;
	}

	if ( empty( $form_slug ) && is_array( $buddyforms ) && ! array_key_exists( $form_slug, $buddyforms ) ) {
		return;
	}

	if ( ! current_user_can( 'buddyforms_' . $form_slug . '_create' ) ) {
		return;
	}

	$form = get_page_by_path( $form_slug, 'OBJECT', 'buddyforms' );

	$post_url = sprintf( 'post.php?post=%s&action=edit', $form->ID );

	$args = array(
		'id'    => 'buddyforms-admin-edit-form',
		'title' => __( 'Edit BuddyForm', 'buddyforms' ),
		'href'  => admin_url( $post_url ),
		'meta'  => array(
			'data-post_id' => 33,
			'class'        => 'admin-bar dashicons-before dashicons-buddyforms'
		)
	);

	$wp_admin_bar->add_node( $args );
}

add_action( 'buddyforms_form_hero_last', 'buddyforms_form_footer_terms' );
function buddyforms_form_footer_terms( $html ) {

	$buddyforms_gdpr = get_option( 'buddyforms_gdpr' );

	$html .= ' <div class="terms"><p>';
	if ( ! empty( $buddyforms_gdpr['terms_label'] ) ) {
		$html .= '<span id="" class="buddyforms_terms_label">' . $buddyforms_gdpr['terms_label'] . '</span> ';
	}

	if ( isset( $buddyforms_gdpr['terms'] ) && $buddyforms_gdpr['terms'] != 'none' ) {
		$html .= '<a id="" class="" href="' . get_permalink( $buddyforms_gdpr['terms'] ) . '">' . get_the_title( $buddyforms_gdpr['terms'] ) . '</a>';
	}
	$html .= '</p></div>';

	return $html;
}

/**
 * Generate a nonce for certain user. This is used to generate the activation link for other user
 * NOTE: when the nonce is generate for other user the token is an empty string,
 * because the nonce will be validate when the session not exist yet.
 *
 * @param int $action
 * @param int $user_id
 *
 * @return bool|string
 */
function buddyforms_create_nonce( $action = - 1, $user_id = 0 ) {
	$token = '';
	if ( $user_id === 0 ) {
		$user = wp_get_current_user();
		$uid  = (int) $user->ID;
		if ( ! $uid ) {
			/** This filter is documented in wp-includes/pluggable.php */
			$uid = apply_filters( 'nonce_user_logged_out', $uid, $action );
		}
		$token = wp_get_session_token();
	} else {
		$uid = $user_id;
	}
	$i = wp_nonce_tick();

	return substr( wp_hash( $i . '|' . $action . '|' . $uid . '|' . $token, 'nonce' ), - 12, 10 );
}

function buddyforms_form_display_message( $form_slug, $post_id, $source = 'after_submit_message_text' ) {
	global $buddyforms;
	$display_message = buddyforms_default_message_on_create();
	if ( ! empty( $buddyforms[ $form_slug ][ $source ] ) ) {
		$display_message = $buddyforms[ $form_slug ][ $source ];
	} else {
		if ( $source !== 'after_submit_message_text' ) {
			$display_message = buddyforms_default_message_on_update();
		}
	}
	if(!empty($buddyforms[ $form_slug ]['attached_page'])) {
		$permalink       = get_permalink( $buddyforms[ $form_slug ]['attached_page'] );
		$display_message = str_ireplace( '[edit_link]', '<a title="' . __( 'Edit Post', 'buddyforms' ) . '" href="' . $permalink . 'edit/' . $form_slug . '/' . $post_id . '">' . __( 'Continue Editing', 'buddyforms' ) . '</a>', $display_message );
	}
	$display_message = str_ireplace( '[form_singular_name]', $buddyforms[ $form_slug ]['singular_name'], $display_message );
	$display_message = str_ireplace( '[post_title]', get_the_title( $post_id ), $display_message );
	$display_message = str_ireplace( '[post_link]', '<a title="' . __( 'Display Post', 'buddyforms' ) . '" href="' . get_permalink( $post_id ) . '"">' . __( 'Display Post', 'buddyforms' ) . '</a>', $display_message );
	

	return $display_message;
}

function buddyforms_user_fields_array() {
	return array( 'user_login', 'user_email', 'user_first', 'user_last', 'user_pass', 'user_website', 'user_bio', 'country', 'state' );
}

function buddyforms_default_message_on_update() {
	return __( 'Form Updated Successfully.', 'buddyforms' );
}

function buddyforms_default_message_on_empty_submission_list() {
	return __( 'There were no posts found. Create your first post [bf_new_submission_link name="Now"]!', 'buddyforms' );
}

function buddyforms_default_message_on_create() {
	return __( 'Form Submitted Successfully.', 'buddyforms' );
}

add_action( 'wp_ajax_nopriv_handle_dropped_media', 'buddyforms_upload_handle_dropped_media' );
add_action( 'wp_ajax_handle_dropped_media', 'buddyforms_upload_handle_dropped_media' );
function buddyforms_upload_handle_dropped_media() {
	check_ajax_referer( 'fac_drop', 'nonce' );
	status_header( 200 );
	$newupload   = 0;
	if ( ! empty( $_FILES ) ) {
		$files = $_FILES;
		foreach ( $files as $file_id => $file ) {
			$newupload = media_handle_upload( $file_id, 0 );
		}
	}

	echo $newupload;
	die();
}

add_action( 'wp_ajax_nopriv_handle_deleted_media', 'buddyforms_upload_handle_delete_media' );
add_action( 'wp_ajax_handle_deleted_media', 'buddyforms_upload_handle_delete_media' );
function buddyforms_upload_handle_delete_media() {
	check_ajax_referer( 'fac_drop', 'nonce' );
	if ( isset( $_REQUEST['media_id'] ) ) {
		$post_id = absint( $_REQUEST['media_id'] );

		$status = wp_delete_attachment( $post_id, true );

		if ( $status ) {
			echo wp_json_encode( array( 'status' => 'OK' ) );
		} else {
			echo wp_json_encode( array( 'status' => 'FAILED' ) );
		}
	}

	die();
}

/**
 * Check if a file was include into the global php queue
 *
 * @since 2.2.8
 *
 * @author gfirem
 *
 * @param $file_name
 *
 * @return bool
 */
function buddyforms_check_loaded_file( $file_name ) {
	$includes_files = get_included_files();

	return in_array( $file_name, $includes_files );
}



function buddyform_get_role_names() {

	global $wp_roles;

	if ( ! isset( $wp_roles ) )
		$wp_roles = new WP_Roles();

	return $wp_roles->get_names();
}

/**
 * Get a tag inside a shortcode from a given content.
 *
 * @since 2.3.1
 *
 * @param array $shortcodes
 * @param array $targets_tags
 * @param string $content
 *
 * @return string
 */
function buddyforms_get_shortcode_tag( $shortcodes, $targets_tags, $content ) {
	if ( ! is_array( $shortcodes ) || ! is_array( $targets_tags ) ) {
		return '';
	}
	$pattern = get_shortcode_regex();
	$result  = '';

	preg_replace_callback( "/$pattern/s", function ( $tag ) use ( $shortcodes, $targets_tags, &$result ) {
		foreach ( $shortcodes as $shortcode_item ) {
			if ( $shortcode_item === $tag[2] ) {
				$attributes = shortcode_parse_atts( $tag[3] );
				foreach ( $targets_tags as $target_item ) {
					if ( array_key_exists( $target_item, $attributes ) ) {
						$result = $attributes[ $target_item ];

						return $tag[0];
					}
				}
			}
		}

		return $tag[0];
	}, $content );

	return $result;
}

/**
 * Extract the form slug from a html inside the given content reading the inout hidden with the Id `form_slug`
 *
 * @param $content
 *
 * @return string
 */
function buddyforms_get_form_slug_from_html( $content ) {
	if ( ! empty( $content ) ) {
	    try {
	        libxml_use_internal_errors(true);
		    $dom                  = new DOMDocument();
		    $dom->validateOnParse = false;
		    $content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
		    $dom->loadHTML( $content );
		    $form_input_node = $dom->getElementById( 'form_slug' );
		    libxml_use_internal_errors(false);
		    if ( ! empty( $form_input_node ) && $form_input_node instanceof DOMElement ) {
			    return $form_input_node->getAttribute( 'value' );
		    }
	    } catch (Exception $e){

        }
	}

	return '';
}

/**
 * Extract the form slug from a shortcode inside the given content
 *
 * @param $content
 * @param array $shortcodes
 *
 * @return string
 */
function buddyforms_get_form_slug_from_shortcode( $content, $shortcodes = array( 'bf', 'buddyforms_form' ) ) {
	$form_slug = buddyforms_get_shortcode_tag( $shortcodes, array( 'form_slug', 'id' ), $content );

	if ( is_numeric( $form_slug ) ) {
		$form_post = get_post( $form_slug );
		$form_slug = $form_post->post_name;
	}

	return $form_slug;
}

/**
 * Extract the form slug from a shortcode inside the given content, if exist the shortcode or reading the hidden input form_slug from the html
 *
 * @param $content
 * @param array $shortcodes
 *
 * @return string
 */
function buddyforms_get_form_slug_from_content( $content, $shortcodes = array( 'bf', 'buddyforms_form' ) ){
    //Extract from the a shortcode inside the content
    $form_slug = buddyforms_get_shortcode_tag($shortcodes , array( 'form_slug', 'id' ), $content );
    //Extract form the html inside the content, reading the hidden input form_slug
	if ( empty( $form_slug ) ) {
		$form_slug = buddyforms_get_form_slug_from_html( $content );
	}

    if ( is_numeric( $form_slug ) ) {
		$form_post = get_post( $form_slug );
		$form_slug = $form_post->post_name;
	}

	return $form_slug;
}


/**
 * Detext if is gutenberg
 *
 *
 * @return boolean
 */
function buddyforms_is_gutenberg_page() {
	if ( function_exists( 'is_gutenberg_page' ) &&
	     is_gutenberg_page()
	) {
		// The Gutenberg plugin is on.
		return true;
	}

	require_once(ABSPATH . 'wp-admin/includes/screen.php');
	require_once(ABSPATH . 'wp-admin/includes/admin.php');
	$current_screen = get_current_screen();
	if ( method_exists( $current_screen, 'is_block_editor' ) &&
	     $current_screen->is_block_editor()
	) {
		// Gutenberg page on 5+.
		return true;
	}

	return false;
}

/**
 * This function secure the array of options to use in the buddyformsGlobal
 *
 * @param $options
 * @param $form_slug
 * @param $bf_post_id
 *
 * @return mixed
 * @since 2.4.0
 *
 */
function buddyforms_filter_frontend_js_form_options( $options, $form_slug, $bf_post_id ) {
	/**
	 * Let the user change the user granted options to use in the frontend global variable buddyformsGlobal
     *
     * @param array granted keys from the options
	 * @param string The form slug from the global wp_query
	 * @param number The current post id form the wp_query. This can be empty when the form is creating an entry.
     *
	 * @since 2.4.0
	 */
	$granted = apply_filters('buddyforms_frontend_granted_forms_option', array( 'status', 'form_fields' ), $form_slug, $bf_post_id);
	foreach ( $granted as $item ) {
		if ( isset( $options[ $item ] ) ) {
			$result[ $item ] = $options[ $item ];
		}
	}
	
	return $result;
}
