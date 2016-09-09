<?php

/**
 * Add the FormBuilder and Form Settings MetaBox to the edit screen
 */
function buddyforms_add_meta_boxes() {
	global $post, $buddyform;

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}

	if(!$buddyform)
		$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true );

	if(is_array($buddyform)) {
		add_meta_box( 'buddyforms_form_shortcodes', __( "Shortcodes", 'buddyforms' ), 'buddyforms_metabox_shortcodes', 'buddyforms', 'side', 'low' );
	}
//	add_meta_box( 'buddyforms_metabox_sidebar', __( "Form Elements", 'buddyforms' ), 'buddyforms_metabox_sidebar', 'buddyforms', 'side', 'low' );

	// Add the FormBuilder and the Form Setup Metabox
	add_meta_box( 'buddyforms_form_elements', __( "Form Builder", 'buddyforms' ), 'buddyforms_metabox_form_elements', 'buddyforms', 'normal', 'high' );
	add_meta_box( 'buddyforms_form_setup', __( "Form Setup", 'buddyforms' ), 'buddyforms_metabox_form_setup', 'buddyforms', 'normal', 'high' );


	// NinjaForms jQuery dialog is different from core so we remove the NinjaForms media buttons on the BuddyForms views
	bf_remove_filters_for_anonymous_class( 'media_buttons_context', 'NF_Admin_AddFormModal', 'insert_form_tinymce_buttons', 10);

}
add_action( 'add_meta_boxes', 'buddyforms_add_meta_boxes' ,9999);

add_filter( "get_user_option_meta-box-order_buddyforms", function () {
	remove_all_actions( 'edit_form_advanced' );
	remove_all_actions( 'edit_page_form' );
}, PHP_INT_MAX );

// Add the 'buddyforms_metabox' class to all buddyforms related metaboxes to hide the rest.
add_filter('postbox_classes_buddyforms_buddyforms_form_elements','buddyforms_metabox_class');
add_filter('buddyforms_metabox_sidebar','buddyforms_metabox_class');
add_filter('postbox_classes_buddyforms_buddyforms_form_setup','buddyforms_metabox_class');
add_filter('postbox_classes_buddyforms_buddyforms_form_shortcodes','buddyforms_metabox_class');

function buddyforms_metabox_class($classes) {
	$classes[] = 'buddyforms-metabox';
	return $classes;
}

add_action('edit_form_top', 'buddyforms_edit_form_top');
function buddyforms_edit_form_top(){
	echo '<div id="buddyforms-edit-wrap" class="hidden">';
}

add_action('dbx_post_sidebar', 'buddyforms_edit_form_top');
function buddyforms_dbx_post_sidebarp(){
	echo '</div>';
}


/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function buddyforms_edit_form_save_meta_box_data( $post_id ) {
	global $post;

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}

	if ( ! isset( $post->post_type ) || $post->post_type != 'buddyforms' ) {
		return;
	}

	if ( ! isset( $_POST['buddyforms_options'] ) ) {
		return;
	}

	$buddyform = $_POST['buddyforms_options'];

	// Add post title as form name and post name as form slug.
	$buddyform['name'] = $post->post_title;
	$buddyform['slug'] = $post->post_name;

	// make sure the form fields slug and type is sanitised
	if ( isset( $buddyform['form_fields'] ) ) : foreach ( $buddyform['form_fields'] as $key => $field ) {
		$buddyform['form_fields'][ $key ]['slug'] = sanitize_title( $field['slug'] );
		$buddyform['form_fields'][ $key ]['type'] = sanitize_title( $field['type'] );
	} endif;

	// Update post meta
	update_post_meta( $post_id, '_buddyforms_options', $buddyform );

	// Save the Roles and capabilities.
	if ( isset( $_POST['buddyforms_roles'] ) ) {

		foreach ( get_editable_roles() as $role_name => $role_info ):
			$role = get_role( $role_name );
			foreach ( $role_info['capabilities'] as $capability => $_ ):

				$capability_array = explode( '_', $capability );

				if ( $capability_array[0] == 'buddyforms' ) {
					if ( $capability_array[1] == $buddyform['slug'] ) {

						$role->remove_cap( $capability );

					}
				}

			endforeach;
		endforeach;

		foreach ( $_POST['buddyforms_roles'] as $form_role => $capabilities ) {
			foreach ( $capabilities as $key => $capability ) {
				$role = get_role( $key );
				foreach ( $capability as $key_cap => $cap ) {
					$role->add_cap( $cap );
				}
			}

		}

	}

	// Regenerate the global $buddyforms.
	// The global$buddyforms is sored in the option table and provides all fors and form fields
	buddyforms_regenerate_global_options();

	// Rewrite the page roles and flash permalink if needed
	buddyforms_attached_page_rewrite_rules( true );

}
add_action( 'save_post', 'buddyforms_edit_form_save_meta_box_data' );

function buddyforms_transition_post_status_regenerate_global_options( $new_status, $old_status, $post ) {

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}

	buddyforms_regenerate_global_options();
	buddyforms_attached_page_rewrite_rules( true );

}
add_action( 'transition_post_status', 'buddyforms_transition_post_status_regenerate_global_options', 10, 3 );

function buddyforms_regenerate_global_options() {
	// get all forms and update the global
	$posts = get_posts( array(
		'numberposts'      => - 1,
		'post_type'        => 'buddyforms',
		'orderby'          => 'menu_order title',
		'order'            => 'asc',
		'suppress_filters' => false,
		'post_status'      => 'publish'
	) );

	$buddyforms_forms = Array();

	if ( $posts ) {
		foreach ( $posts as $post ) {
			$options = get_post_meta( $post->ID, '_buddyforms_options', true );
			if ( $options ) {
				$buddyforms_forms[ $post->post_name ] = $options;
			}
		}
	}
	update_option( 'buddyforms_forms', $buddyforms_forms );
}

/**
 * Register the post type
 */
function buddyforms_register_post_type() {

	// Create BuddyForms post type
	$labels = array(
		'name'               => __( 'BuddyForms', 'buddyforms' ),
		'singular_name'      => __( 'BuddyForm', 'buddyforms' ),
		'add_new'            => __( 'Add New', 'buddyforms' ),
		'add_new_item'       => __( 'Add New Form', 'buddyforms' ),
		'edit_item'          => __( 'Edit Form', 'buddyforms' ),
		'new_item'           => __( 'New Form', 'buddyforms' ),
		'view_item'          => __( 'View Form', 'buddyforms' ),
		'search_items'       => __( 'Search BuddyForms', 'buddyforms' ),
		'not_found'          => __( 'No BuddyForm found', 'buddyforms' ),
		'not_found_in_trash' => __( 'No Forms found in Trash', 'buddyforms' ),
	);

	register_post_type( 'buddyforms', array(
		'labels'              => $labels,
		'public'              => true,
		'show_ui'             => true,
		'_builtin'            => false,
		'capability_type'     => 'post',
		'hierarchical'        => false,
		'rewrite'             => false,
		'supports'            => array(
			'title'
		),
		'show_in_menu'        => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'menu_icon'           => 'dashicons-buddyforms',
	) );

	// Create BuddyForms post type
	$labels = array(
		'name'               => __( 'Submissions', 'buddyforms' ),
		'singular_name'      => __( 'Submissions', 'buddyforms' ),
//		'add_new'            => __( 'Add New', 'buddyforms' ),
//		'add_new_item'       => __( 'Add New Form', 'buddyforms' ),
//		'edit_item'          => __( 'Edit Form', 'buddyforms' ),
//		'new_item'           => __( 'New Form', 'buddyforms' ),
//		'view_item'          => __( 'View Form', 'buddyforms' ),
//		'search_items'       => __( 'Search BuddyForms', 'buddyforms' ),
//		'not_found'          => __( 'No BuddyForm found', 'buddyforms' ),
//		'not_found_in_trash' => __( 'No Forms found in Trash', 'buddyforms' ),
	);

	register_post_type( 'buddyforms_submissions', array(
		'labels'              => $labels,
		'public'              => false,
		'show_ui'             => true,
		'_builtin'            => false,
		'capability_type'     => 'posts',
		'hierarchical'        => false,
		'rewrite'             => false,
		'supports'            => false,
		//'show_in_menu'        => 'edit.php?post_type=buddyforms',
		'show_in_menu'        => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'menu_icon'           => 'dashicons-buddyforms',
	) );

}

add_action( 'init', 'buddyforms_register_post_type' );

function menue_icon_admin_head_css() { ?>
	<style>

		.wp-menu-image.dashicons-before.dashicons-buddyforms:before {
			content: "\e000";
			font-family: 'icomoon';
			font-size: 27px;
			padding: 0;
			padding-right: 10px;
		}

	</style>

<?php }

add_action( 'admin_head', 'menue_icon_admin_head_css' );

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function buddyforms_form_updated_messages( $messages ) {
	global $post, $post_ID;

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}

	$messages['buddyforms'] = array(
		0  => '', // Unused. Messages start at index 1.
		1  => __( 'Form updated.', 'buddyforms' ),
		2  => __( 'Custom field updated.', 'buddyforms' ),
		3  => __( 'Custom field deleted.', 'buddyforms' ),
		4  => __( 'Form updated.', 'buddyforms' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Form restored to revision from %s' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6  => sprintf( __( 'Form published. <a href="%s">View Form</a>' ), esc_url( get_permalink( $post_ID ) ) ),
		7  => __( 'Form saved.' ),
		8  => sprintf( __( 'Form submitted. <a target="_blank" href="%s">Preview Form</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		9  => sprintf( __( 'Form scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Form</a>' ),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
		10 => sprintf( __( 'Form draft updated. <a target="_blank" href="%s">Preview Form</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
	);

	return $messages;
}

add_filter( 'post_updated_messages', 'buddyforms_form_updated_messages', 999 );

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function set_custom_edit_buddyforms_columns( $columns ) {
	unset( $columns['date'] );
	//$columns['slug']               = __( 'Slug', 'buddyforms' );
	$columns['attached_post_type'] = __( 'Form Type', 'buddyforms' );
	$columns['attached_page']      = __( 'Logged In User Access', 'buddyforms' );
	$columns['shortcode']          = __( 'Shortcode', 'buddyforms' );

	return $columns;
}

add_filter( 'manage_buddyforms_posts_columns', 'set_custom_edit_buddyforms_columns', 10, 1 );

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function custom_buddyforms_column( $column, $post_id ) {

	$post      = get_post( $post_id );
	$buddyform = get_post_meta( $post_id, '_buddyforms_options', true );

	switch ( $column ) {
		case 'slug' :
			echo $post->post_name;
			break;
		case 'attached_post_type' :

			if ( !isset( $buddyform['form_type'] ) ){
				$post_type_html = '<p>' . __( 'Contact Form', 'buddyforms' ) . '</p>';
			} elseif($buddyform['form_type']  == 'contact' ) {
				$post_type_html = '<p>' . __( 'Contact Form', 'buddyforms' ) . '</p>';
			} elseif($buddyform['form_type']  == 'post' ) {
				$post_type_html = '<p>' . __( 'Post Submissions', 'buddyforms' ) . ' <br> ' . __( 'Post Type: ', 'buddyforms' ) . $buddyform['post_type'] . '</p>';
			} elseif( $buddyform['form_type']  == 'registration' ) {
				$post_type_html = '<p>' . __( 'Registration Form', 'buddyforms' ) . '</p>';
			}

			echo $post_type_html;
			break;
		case 'attached_page' :
			if ( isset( $buddyform['attached_page'] ) && empty( $buddyform['attached_page'] ) ) {
				$attached_page = '<p style="color: red;">No Page Attached</p>';
			} elseif ( isset( $buddyform['attached_page'] ) && $attached_page_title = get_the_title( $buddyform['attached_page'] ) ) {
				$attached_page = '<p>' . __( 'On', 'buddyforms' ). '</p>';// . '<br>' . $attached_page_title . '</p>';
			} else {
				$attached_page = 'Off';
			}

			echo $attached_page;

			if($attached_page != 'Off') {
				$attached_page_permalink = isset( $buddyform['attached_page'] ) ? get_permalink( $buddyform['attached_page'] ) : '';?>
				<div class="row-actions">
					<span class="view-form">
						<a target="_blank" href="<?php echo $attached_page_permalink . 'create/' . $post->post_name ?>">View Form</a> |
					</span>
					<span class="view-entryies">
						<a target="_blank" href="<?php echo $attached_page_permalink . 'view/' . $post->post_name ?>">View Entries</a>
					</span>

				</div>
				<?php
			}
			break;
		case 'shortcode':
			echo '[bf form_slug="' . $post->post_name . '"]';
			break;
	}
}
add_action( 'manage_buddyforms_posts_custom_column', 'custom_buddyforms_column', 10, 2 );

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function buddyforms_hide_publishing_actions() {
	global $post;

	if ( get_post_type( $post ) == 'buddyforms' ) { ?>
		<style type="text/css">
			.misc-pub-visibility,
			.misc-pub-curtime,
			.misc-pub-post-status{
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
		</style>

		<script>
			jQuery(document).ready(function (jQuery) {
				//jQuery('#screen-meta-links').hide();
				jQuery('body').find('h1:first').css('line-height', '58px');
				jQuery('body').find('h1:first').css('margin-top', '20px');
				jQuery('body').find('h1:first').css('font-size', '30px');
				//jQuery('body').find('h1:first').addClass('tk-icon-buddyforms');
				jQuery('body').find('h1:first').html('<div style="font-size: 52px; margin-top: -5px; float: left; margin-right: 15px;" class="tk-icon-buddyforms"></div> ' +
					'BuddyForms ' +
					'<a href="post-new.php?post_type=buddyforms" class="page-title-action">Add New</a>' +
					'<small style="line-height: 1; margin-top: -10px; margin-right: -15px; color: #888; font-size: 13px; padding-top: 23px; float:right;">Version <?php echo BUDDYFORMS_VERSION ?></small>'
				);
				jQuery('h1').show();
			});

		</script>


		<?php
	}
}
add_action( 'admin_head-edit.php', 'buddyforms_hide_publishing_actions' );
add_action( 'admin_head-post.php', 'buddyforms_hide_publishing_actions' );
add_action( 'admin_head-post-new.php', 'buddyforms_hide_publishing_actions' );

//
// Add new Actions Buttons to the publish metabox
//
function buddyforms_add_button_to_submit_box() {
	global $post;

	if ( get_post_type( $post ) != 'buddyforms' )
		return;

	$buddyform               = get_post_meta( $post->ID, '_buddyforms_options', true );
	$attached_page_permalink = isset( $buddyform['attached_page'] ) ? get_permalink( $buddyform['attached_page'] ) : '';

	$base = home_url();

	$preview_page_id = get_option( 'buddyforms_preview_page', true );
	?>
	<div id="buddyforms-actions" class="misc-pub-section">
		<?php if(isset($post->post_name) && $post->post_name != '') { ?>
			<a class="button button-large bf_button_action" target="_blank" href="<?php echo $base ?>/?page_id=<?php echo $preview_page_id ?>&preview=true&form_slug=<?php echo $post->post_name ?>"><?php _e( 'Preview Form', 'buddyforms' ) ?></a>
		<?php } ?>
		<?php if( isset($buddyform['attached_page']) && isset($buddyform['post_type']) && $buddyform['attached_page'] != 'none'){ ?>
			<div id="frontend-actions">
				<label for="button">Frontend</label>
				<?php echo '<a class="button button-large bf_button_action" href="' . $attached_page_permalink . 'view/' . $post->post_name . '/" target="_new">' . __( 'Your Submissions', 'buddyforms' ) . '</a>
                <a class="button button-large bf_button_action" href="' . $attached_page_permalink . 'create/' . $post->post_name . '/" target="_new">' . __( 'The Form', 'buddyforms' ) . '</a>'; ?>
			</div>
		<?php } if(isset($post->post_name) && $post->post_name != '') { ?>
			<div id="admin-actions">
				<label for="button">Admin</label>
				<?php echo '<a class="button button-large bf_button_action" href="edit.php?post_type=buddyforms&page=buddyforms_submissions&form_slug='.$post->post_name.'">' . __( 'Submissions', 'buddyforms' ) . '</a>'; ?>
			</div>
		<?php } ?>

		<div class="clear"></div>
	</div>

<?php

}
add_action( 'post_submitbox_misc_actions', 'buddyforms_add_button_to_submit_box' );

// remove the slugdiv metabox from buddyforms post edit screen
function buddyforms_remove_slugdiv() {
	remove_meta_box( 'slugdiv', 'buddyforms', 'normal' );
}
add_action( 'admin_menu', 'buddyforms_remove_slugdiv' );

// Add the actions to list table
function buddyforms_add_action_buttons($actions, $post){

	if(get_post_type() === 'buddyforms'){
		$url = add_query_arg(
			array(
				'post_id' => $post->ID,
				'my_action' => 'export_form',
			)
		);

		unset($actions['inline hide-if-no-js']);

		$base = home_url();

		$preview_page_id = get_option( 'buddyforms_preview_page', true );

		$actions['export']       = '<a href="' . esc_url( $url ) . '">Export</a>';
		$actions['submissions']  = '<a href="?post_type=buddyforms&page=buddyforms_submissions&form_slug=' . $post->post_name . '">' . __("View Submissions", "buddyforms") . '</a>';
		$actions['preview_link'] = '<a target="_blank" href="' . $base . '/?page_id=' . $preview_page_id  . '&preview=true&form_slug=' . $post->post_name . '">' . __( 'Preview Form', 'buddyforms' ) . '</a>';

	}
	return $actions;
}
add_filter( 'post_row_actions', 'buddyforms_add_action_buttons', 10, 2 );


function buddyforms_export_form(){
	if ( isset( $_REQUEST['my_action'] ) && 'export_form' == $_REQUEST['my_action']  ) {

		$buddyform_options = get_post_meta( $_REQUEST['post_id'], '_buddyforms_options', true );

		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename="BuddyFormsExport.json"');
		echo json_encode($buddyform_options);
		exit;
	}
}
add_action( 'admin_init', 'buddyforms_export_form' );
