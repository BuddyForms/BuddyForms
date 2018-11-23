<?php

// Shortcode to add the form everywhere easily ;) the form is located in form.php
add_shortcode( 'buddyforms_form', 'buddyforms_create_edit_form_shortcode' );
add_shortcode( 'bf', 'buddyforms_create_edit_form_shortcode' );
/**
 * @param $args
 *
 * @return string
 */
function buddyforms_create_edit_form_shortcode( $args ) {
	$post_type = $the_post = $post_id = $revision_id = $form_slug = $slug = $id = '';

	extract( shortcode_atts( array(
		'post_type'   => '',
		'the_post'    => 0,
		'post_id'     => '',
		'revision_id' => false,
		'form_slug'   => '',
		'slug'        => '',
		'id'          => '',
	), $args ) );

	if ( empty( $form_slug ) ) {
		$form_slug = $slug;
	}

	// If multisite is enabled make sure we switch back to the current blog to get the correct form
	if ( buddyforms_is_multisite() ) {
		restore_current_blog();
	}
	// if id is used we need to get the post_name
	if ( empty( $form_slug ) && ! empty( $id ) ) {
		$post = get_post( $id );

		if ( ! isset( $post->post_name ) ) {
			return false;
		}
		$form_slug = $post->post_name;
	}

	// Ok we have the form. let us switch back to the form blog id
	buddyforms_switch_to_form_blog( $form_slug );


	// add the form slug to the args array to render the form
	$args['form_slug'] = $form_slug;

	// unset slug and id they are not supported from the buddyforms_create_edit_form function.
	unset( $args['slug'] );
	unset( $args['id'] );

	ob_start();
	buddyforms_create_edit_form( $args );
	$create_edit_form = ob_get_contents();
	ob_clean();

	return $create_edit_form;
}


/**
 * Shortcode to display author posts of a specific post type
 *
 * @package BuddyForms
 * @since 0.3 beta
 *
 * @param $args
 */
function buddyforms_the_loop( $args ) {
	global $the_lp_query, $buddyforms, $form_slug, $paged;

	$caller = $author = $post_type = $form_slug = $id = $post_parent = $query_option = $user_logged_in_only = $meta_key = $meta_value = '';

	// Enable other plugins to manipulate the arguments used for query the posts
	$args = apply_filters( 'buddyforms_the_loop_args', $args );

	extract( shortcode_atts( array(
		'author'              => '',
		'post_type'           => '',
		'form_slug'           => '',
		'id'                  => '',
		'caller'              => $caller,
		'post_parent'         => 0,
		'query_option'        => $query_option,
		'user_logged_in_only' => 'logged_in_only',
		'meta_key'            => '',
		'meta_value'          => ''
	), $args ) );

	if ( $user_logged_in_only == 'logged_in_only' && ! is_user_logged_in() ) {
        buddyforms_wp_login_form();
        return;
    }

	// if multisite is enabled switch to the form blog id
	buddyforms_switch_to_form_blog( $form_slug );

	if ( empty( $form_slug ) && ! empty( $id ) ) {
		$post      = get_post( $id );
		$form_slug = $post->post_name;
	}
	$args['form_slug'] = $form_slug;
	unset( $args['id'] );

	$query_option = isset( $buddyforms[ $form_slug ]['list_posts_option'] ) ? $buddyforms[ $form_slug ]['list_posts_option'] : '';

	if ( empty( $post_type ) ) {
		$post_type = $buddyforms[ $form_slug ]['post_type'];
	}

	$list_posts_style  = isset( $buddyforms[ $form_slug ]['list_posts_style'] ) ? $buddyforms[ $form_slug ]['list_posts_style'] : 'list';

	if ( empty( $author ) ) {
		$author = get_current_user_id();
	}

	$the_author_id = apply_filters( 'buddyforms_the_loop_author_id', $author, $form_slug );

	$post_status = array( 'publish', 'pending', 'draft', 'future' );

	if ( ! $the_author_id ) {
		$post_status = array( 'publish' );
	}

	$paged = buddyforms_get_url_var( 'page' );

	switch ( $query_option ) {
		case 'list_all_published_posts':
			$query_args = array(
				'post_type'      => $post_type,
				'post_parent'    => $post_parent,
				'form_slug'      => $form_slug,
				'post_status'    => 'publish',
				'posts_per_page' => apply_filters( 'buddyforms_user_posts_query_args_posts_per_page', 10 ),
				'paged'          => $paged,
			);
			break;
		case 'list_all_published_posts_by_meta_key':
			$query_args = array(
				'post_type'      => $post_type,
				'post_parent'    => $post_parent,
				'form_slug'      => $form_slug,
				'post_status'    => 'publish',
				'posts_per_page' => apply_filters( 'buddyforms_user_posts_query_args_posts_per_page', 10 ),
				'paged'          => $paged,
				'meta_key'       => $meta_key,
				'meta_value'     => $meta_value
			);
			break;
		case 'list_all':
			$query_args = array(
				'post_type'      => $post_type,
				'post_parent'    => $post_parent,
				'form_slug'      => $form_slug,
				'post_status'    => $post_status,
				'posts_per_page' => apply_filters( 'buddyforms_user_posts_query_args_posts_per_page', 10 ),
				'paged'          => $paged,
			);
			break;
		default:
			$query_args = array(
				'post_type'      => $post_type,
				'post_parent'    => $post_parent,
				'form_slug'      => $form_slug,
				'post_status'    => $post_status,
				'posts_per_page' => apply_filters( 'buddyforms_user_posts_query_args_posts_per_page', 10 ),
				'paged'          => $paged,
				'meta_key'       => '_bf_form_slug',
				'meta_value'     => $form_slug
			);
			break;

	}

	if( $caller !== 'buddyforms_list_all' ) {
		if ( ! current_user_can( 'buddyforms_' . $form_slug . '_all' ) ) {
			$query_args['author'] = $the_author_id;
		}
	}

	// New
	$query_args = apply_filters( 'buddyforms_user_posts_query_args', $query_args );
	// Deprecated
	$query_args = apply_filters( 'buddyforms_post_to_display_args', $query_args );


	do_action( 'buddyforms_the_loop_start', $query_args );

	$the_lp_query = new WP_Query( $query_args );
	$the_lp_query = apply_filters( 'buddyforms_the_lp_query', $the_lp_query );


	$form_slug = $the_lp_query->query_vars['form_slug'];

	if ( $list_posts_style == 'table' ) {
		buddyforms_locate_template( 'the-table' );
	} elseif ( $list_posts_style == 'list' ) {
		buddyforms_locate_template( 'the-loop' );
	} else {
		buddyforms_locate_template( $list_posts_style );
	}

	// Support for wp_pagenavi
	if ( function_exists( 'wp_pagenavi' ) ) {
		wp_pagenavi( array( 'query' => $the_lp_query ) );
	}
	wp_reset_postdata();

	do_action( 'buddyforms_the_loop_end', $query_args );

	// If multisite is enabled we should restore now to the current blog.
	if ( buddyforms_is_multisite() ) {
		restore_current_blog();
	}
}

add_shortcode( 'buddyforms_the_loop', 'buddyforms_the_loop_shortcode' );
add_shortcode( 'buddyforms_list_all', 'buddyforms_list_all_shortcode' );
add_shortcode( 'bf_user_posts_list', 'bf_user_posts_list_shortcode' );
add_shortcode( 'bf_posts_list', 'bf_posts_list_shortcode' );

//
// buddyforms_the_loop_shortcode
//
function buddyforms_list_all_shortcode( $args ) {
	ob_start();
	$args['caller'] = 'buddyforms_list_all';
	buddyforms_the_loop( $args );
	$tmp = ob_get_clean();

	return $tmp;
}

function bf_user_posts_list_shortcode( $args ) {
	ob_start();
	$args['caller'] = 'bf_user_posts_list';
	buddyforms_the_loop( $args );
	$tmp = ob_get_clean();

	return $tmp;
}

function bf_posts_list_shortcode( $args ) {
	ob_start();
	$args['caller'] = 'bf_posts_list';
	buddyforms_the_loop( $args );
	$tmp = ob_get_clean();

	return $tmp;
}

function buddyforms_the_loop_shortcode( $args ) {
	ob_start();
	$args['caller'] = 'buddyforms_the_loop';
	buddyforms_the_loop( $args );
	$tmp = ob_get_clean();

	return $tmp;
}


//
// BuddyForms Shortcode Buttons
//
add_shortcode( 'buddyforms_nav', 'buddyforms_nav' );
add_shortcode( 'bf_nav', 'buddyforms_nav' );
/**
 * @param $args
 *
 * @return mixed|string
 */
function buddyforms_nav( $args ) {
	$form_slug = $separator = $label_add = $label_view = '';
	extract( shortcode_atts( array(
		'form_slug'  => '',
		'separator'  => ' | ',
		'label_add'  => 'Add New',
		'label_view' => 'View',
	), $args ) );

	$tmp = buddyforms_button_view_posts( $args );
	$tmp .= $separator;
	$tmp .= buddyforms_button_add_new( $args );

	return $tmp;
}

add_shortcode( 'buddyforms_button_view_posts', 'buddyforms_button_view_posts' );
add_shortcode( 'bf_link_to_user_posts', 'buddyforms_button_view_posts' );
/**
 * @param $args
 *
 * @return mixed
 */
function buddyforms_button_view_posts( $args ) {
	global $buddyforms;
	$form_slug = $label_view = '';
	extract( shortcode_atts( array(
		'form_slug'  => '',
		'label_view' => 'View',
	), $args ) );

	$button = '<a class="button" href="/' . get_post( $buddyforms[ $form_slug ]['attached_page'] )->post_name . '/view/' . $form_slug . '/"> ' . __( $label_view, 'buddyforms' ) . ' </a>';

	return apply_filters( 'buddyforms_button_view_posts', $button, $args );

}

add_shortcode( 'buddyforms_button_add_new', 'buddyforms_button_add_new' );
add_shortcode( 'bf_link_to_form', 'buddyforms_button_add_new' );
/**
 * @param $args
 *
 * @return mixed
 */
function buddyforms_button_add_new( $args ) {
	global $buddyforms;
	$form_slug = $label_add = '';
	extract( shortcode_atts( array(
		'form_slug' => '',
		'label_add' => 'Add New',
	), $args ) );


	$button = '<a class="button" href="/' . get_post( $buddyforms[ $form_slug ]['attached_page'] )->post_name . '/create/' . $form_slug . '/"> ' . __( $label_add, 'buddyforms' ) . '</a>';

	return apply_filters( 'buddyforms_button_add_new', $button, $args );

}

add_shortcode( 'bf_login_form', 'buddyforms_view_login_form' );
function buddyforms_view_login_form( $args ) {
	global $wp;

	if ( is_admin() ) {
		return false;
	}
	$form_slug = $redirect_url = $title = $label_username = $label_password = $label_remember = $label_log_in = '';
	$current_url = home_url( add_query_arg( array(), $wp->request ) );

	extract( shortcode_atts( array(
		'form_slug'      => 'none',
		'redirect_url'   => $current_url,
		'title'          => 'Login',
		'label_username' => __( 'Username or Email Address' ),
		'label_password' => __( 'Password' ),
		'label_remember' => __( 'Remember Me' ),
		'label_log_in'   => __( 'Log In' ),
	), $args ) );

	if ( is_user_logged_in() ) {
		$tmp = '<a href="' . wp_logout_url( $current_url ) . '">' . __( 'Logout', 'buddyforms' ) . '</a>';
	} else {
		$tmp = buddyforms_get_wp_login_form( $form_slug, $title, $args );
	}

	return $tmp;
}


// password reset form
function buddyforms_reset_password_form($args) {
	$redirect_url = '';
	extract( shortcode_atts( array(
		'redirect_url' => '',
	), $args ) );


	if(is_user_logged_in()) {

		$bf_pw_redirect_url = get_user_meta( get_current_user_id(),'bf_pw_redirect_url', true );

		if($bf_pw_redirect_url){
			$redirect_url = $bf_pw_redirect_url;
		}

		return buddyforms_change_password_form( $redirect_url );
	} else {

		$buddyforms_registration_form = get_option( 'buddyforms_registration_form' );
		return buddyforms_get_wp_login_form( $buddyforms_registration_form, __('You need to login to change your password.') );
	}
}
add_shortcode('buddyforms_reset_password', 'buddyforms_reset_password_form');

/**
 * This shortcode will return the link to create a new post if the attached page option are enabled
 *
 * @param $args
 *
 * @return string
 */
function buddyforms_create_submission_link_shortcode( $args ) {
	global $buddyforms, $form_slug;

	$default_link = '';
	if ( ! empty( $form_slug ) ) {
		$attached_page     = isset( $buddyforms[ $form_slug ]['attached_page'] ) ? $buddyforms[ $form_slug ]['attached_page'] : 'false';
		$siteurl           = get_bloginfo( 'wpurl' );
		$attached_page_url = get_permalink( $attached_page );

		if ( ! empty( $attached_page_url ) ) {
			$default_link = $attached_page_url . "create/" . $form_slug;
		} else {
			$default_link = $siteurl . '/' . $attached_page . '/create/' . $form_slug;
		}
	}
	$arguments = shortcode_atts( array(
		'name'   => __( 'Now', 'buddyforms' ),
		'link'   => $default_link,
		'target' => '_blank',
	), $args );

	$target = '';
	if ( ! empty( $arguments['target'] ) && 'false' !== $arguments['target'] ) {
		$target = sprintf( ' target="%s" ', $arguments['target'] );
	}
	if ( ! empty( $arguments['link'] ) ) {
		return sprintf( '<a href="%s" %s >%s</a>', $arguments['link'], $target, $arguments['name'] );
	} else {
		return $arguments['name'];
	}
}

add_shortcode( 'bf_new_submission_link', 'buddyforms_create_submission_link_shortcode' );

