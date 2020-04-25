<?php
/**
 * handle custom page
 * do flush if changing rule, then reload the admin page
 *
 * @package BuddyForms
 * @since 0.3 beta
 */

add_action( 'init', 'buddyforms_attached_page_rewrite_rules' );
/**
 * @param bool $flush_rewrite_rules
 */
function buddyforms_attached_page_rewrite_rules( $flush_rewrite_rules = false ) {
	global $buddyforms;

	if ( ! $buddyforms ) {
		return;
	}

	add_rewrite_tag( 'bf_form_slug', '(.?.+?)' );
	add_rewrite_tag( 'bf_action', '(.?.+?)' );
	add_rewrite_tag( 'bf_parent_post_id', '(:/([0-9]+))?' );
	add_rewrite_tag( 'bf_post_id', '(:/([0-9]+))?' );
	add_rewrite_tag( 'bf_rev_id', '(:/([0-9]+))?' );

	foreach ( $buddyforms as $key => $buddyform ) {
		buddyforms_form_rewrite_rules( $buddyform );
	}
	if ( $flush_rewrite_rules ) {
		flush_rewrite_rules();
	}

	do_action( 'buddyforms_after_attache_page_rewrite_rules', $flush_rewrite_rules );
}

function dfgdgfd( $post_id ) {
	$lang = pll_current_language();
	if ( ! empty( $lang ) ) {
		$lang_array = pll_get_post_translations( $post_id );
		if ( ! empty( $lang_array ) ) {
			if ( isset( $lang_array[ $lang ] ) ) {
				return $lang_array[ $lang ];
			}
		}
	}

	return $post_id;
}

function buddyforms_form_rewrite_rules( $buddyform ) {
	if ( empty( $buddyform ) ) {
		return;
	}

	$structure            = get_option( 'permalink_structure' );
	$is_rewrite_structure = ! empty( $structure );
	$attached_page_id     = 0;
	$page_parameter       = 'pagename';
	if ( ! empty( $structure ) ) {
		if ( ! empty( $buddyform['attached_page_name'] ) ) {
			$attached_page_name = $buddyform['attached_page_name'];
			$attached_page_id   = $attached_page_name;
		} else {
			if ( ! empty( $buddyform['attached_page'] ) && $buddyform['attached_page'] !== 'none' ) {
				$attached_page_id   = $buddyform['attached_page'];
				$attached_page      = WP_Post::get_instance( $buddyform['attached_page'] );
				$attached_page_name = $attached_page->post_name;
			}
		}
	} else {
		$page_parameter     = 'page_id';
		$attached_page_name = $buddyform['attached_page'];
	}

	if ( empty( $attached_page_name ) ) {
		return;
	}

	$bf_actions = array( 'create', 'view', 'edit', 'revision' );

	foreach ( $bf_actions as $item_action ) {
		$bf_internal_rules = buddyforms_get_rewrite_rule( $item_action );
		foreach ( $bf_internal_rules as $bf_internal_rule ) {
			$view_regex = sprintf( $bf_internal_rule['regex'], $attached_page_name, $item_action );
			$view_url   = sprintf( $bf_internal_rule['url'], $page_parameter, $attached_page_name );
			$view_regex = apply_filters( 'buddyforms_rewrite_regex', $view_regex, $bf_internal_rule['regex'], $attached_page_name, $item_action );
			$view_url   = apply_filters( 'buddyforms_rewrite_url', $view_url, $bf_internal_rule['url'], $page_parameter, $attached_page_name );
			add_rewrite_rule( $view_regex, $view_url, 'top' );
			//Add extra rule for each translation of the attached page
			if ( ! empty( $attached_page_id ) ) {
				$lang_array = pll_get_post_translations( $attached_page_id );
				if ( ! empty( $lang_array ) ) {
					foreach ( $lang_array as $lang_code => $attached_page_id_translated ) {
						if ( empty( $attached_page_id_translated ) ) {
							continue;
						}
						if ( ! empty( $structure ) ) {
							$attached_page      = WP_Post::get_instance( $attached_page_id_translated );
							$attached_page_name = $attached_page->post_name;
						} else {
							$page_parameter     = 'page_id';
							$attached_page_name = $attached_page_id_translated;
						}

						$view_regex = sprintf( $bf_internal_rule['regex'], $attached_page_name, $item_action );
						$view_url   = sprintf( $bf_internal_rule['url'], $page_parameter, $attached_page_name );
						add_rewrite_rule( $lang_code . '/' . $view_regex, $view_url, 'top' );
					}
				}
			}
		}
	}
}

function buddyform_get_action_rewrite_rule( $action, $attached_page_id, $post_id, $form_slug, $revision_id = 0, $arguments = array() ) {
	if ( ! empty( $attached_page_id ) && $attached_page_id !== 'none' ) {
		$structure              = get_option( 'permalink_structure' );
		$page_parameter         = ! empty( $structure ) ? 'pagename' : 'page_id';
		$is_rewrite_structure   = ! empty( $structure );
		$rewrite_rules_internal = buddyforms_get_rewrite_rule( $action );

		$attached_page_permalink = get_permalink( $attached_page_id );
		$attached_page_permalink = rtrim( $attached_page_permalink, '/' );

		$match_rewrite_rule = false;
		foreach ( $rewrite_rules_internal as $rewrite_rule ) {
			$match_rewrite_rule = array(
				'action' => $rewrite_rule['action'],
				'regex'  => $rewrite_rule['rewrite_regex'],
				'url'    => $rewrite_rule['rewrite_url'],
			);
			if ( $is_rewrite_structure ) {
				$match_rewrite_rule['regex'] = preg_replace( '/%s/', $attached_page_permalink, $match_rewrite_rule['regex'], 1 );
			} else {
				$match_rewrite_rule['url'] = str_replace( '?%s=', sprintf( '?%s=', $page_parameter ), $match_rewrite_rule['url'] );
			}
		}
		if ( ! empty( $match_rewrite_rule ) ) {
			$base = home_url();
			if ( $is_rewrite_structure ) {
				switch ( $action ) {
					case 'create':
						if ( ! empty( $post_id ) ) {
							$attached_page      = WP_Post::get_instance( $post_id );
							$attached_page_name = $attached_page->post_name;
							if ( empty( $arguments ) ) {
								$arguments = array( $action, $attached_page_name );
							}
						} else {
							if ( empty( $arguments ) ) {
								$arguments = array( $action, $form_slug );
							}
						}
						break;
					case 'edit':
						if ( empty( $arguments ) ) {
							$arguments = array( $action, $form_slug, $post_id );
						}
						break;
					case 'view':
						if ( empty( $arguments ) ) {
							$arguments = array( $action, $form_slug );
						}
						break;
					case 'revision':
						if ( empty( $arguments ) ) {
							$arguments = array( $action, $form_slug, $post_id, $revision_id );
						}
						break;
				}

				$result_url = vsprintf( $match_rewrite_rule['regex'], $arguments );
			} else {// No rewrite
				switch ( $action ) {
					case 'create':
						if ( ! empty( $post_id ) ) {
							$attached_page      = WP_Post::get_instance( $post_id );
							$attached_page_name = $attached_page->post_name;
							if ( empty( $arguments ) ) {
								$arguments = array( $attached_page_id, $form_slug, $attached_page_name );
							}
						} else {
							if ( empty( $arguments ) ) {
								$arguments = array( $attached_page_id, $form_slug );
							}
						}
						break;
					case 'edit':
						if ( empty( $arguments ) ) {
							$arguments = array( $attached_page_id, $form_slug, $post_id );
						}
						break;
					case 'view':
						if ( empty( $arguments ) ) {
							$arguments = array( $attached_page_id, $form_slug );
						}
						break;
					case 'revision':
						if ( empty( $arguments ) ) {
							$arguments = array( $attached_page_id, $form_slug, $post_id, $revision_id );
						}
						break;
				}

				$result_url = vsprintf( $base . $match_rewrite_rule['url'], $arguments );
			}

			return apply_filters( 'buddyforms_action_rewrite_rule', $result_url, $action, $attached_page_id, $post_id, $form_slug, $revision_id );
		}
	}

	return '';
}

function buddyforms_get_rewrite_rule( $action = '' ) {
	$rules = array(
		array(
			'action'        => 'create',
			'regex'         => "%s/%s/([^/]+)/([^/]+)/?",
			'rewrite_regex' => "%s/%s/%s/%s/",
			'url'           => "index.php?%s=%s&bf_action=create&bf_form_slug=\$matches[1]&bf_parent_post_id=\$matches[2]",
			'rewrite_url'   => "?%s=%s&bf_action=create&bf_form_slug=%s&bf_parent_post_id=%s",
		),
		array(
			'action'        => 'create',
			'regex'         => "%s/%s/([^/]+)/?",
			'rewrite_regex' => "%s/%s/%s/",
			'url'           => "index.php?%s=%s&bf_action=create&bf_form_slug=\$matches[1]",
			'rewrite_url'   => "?%s=%s&bf_action=create&bf_form_slug=%s",
		),
		array(
			'action'        => 'edit',
			'regex'         => "%s/%s/([^/]+)/([^/]+)/?",
			'rewrite_regex' => "%s/%s/%s/%s/",
			'url'           => "index.php?%s=%s&bf_action=edit&bf_form_slug=\$matches[1]&bf_post_id=\$matches[2]",
			'rewrite_url'   => "?%s=%s&bf_action=edit&bf_form_slug=%s&bf_post_id=%s",
		),
		array(
			'action'        => 'view',
			'regex'         => "%s/%s/([^/]+)/?",
			'rewrite_regex' => "%s/%s/%s/",
			'url'           => "index.php?%s=%s&bf_action=view&bf_form_slug=\$matches[1]",
			'rewrite_url'   => "?%s=%s&bf_action=view&bf_form_slug=%s",
		),
		array(
			'action'        => 'revision',
			'regex'         => "%s/%s/([^/]+)/([^/]+)/([^/]+)/?",
			'rewrite_regex' => "%s/%s/%s/%s/%s/",
			'url'           => "index.php?%s=%s&bf_action=revision&bf_form_slug=\$matches[1]&bf_post_id=\$matches[2]&bf_rev_id=\$matches[3]",
			'rewrite_url'   => "?%s=%s&bf_action=revision&bf_form_slug=%s&bf_post_id=%s&bf_rev_id=%s",
		),
	);

	$rules = apply_filters( 'buddyforms_rewrite_rules', $rules );

	if ( empty( $action ) ) {
		return $rules;
	} else {
		$action_rules = array();
		foreach ( $rules as $item_action ) {
			if ( $item_action['action'] === $action ) {
				$action_rules[] = $item_action;
			}
		}

		return $action_rules;
	}
}

/**
 * add the query vars
 *
 * @package BuddyForms
 * @since 0.3 beta
 */
add_filter( 'query_vars', 'buddyforms_attached_page_query_vars' );
/**
 * @param $query_vars
 *
 * @return array
 */
function buddyforms_attached_page_query_vars( $query_vars ) {

	$query_vars[] = 'bf_action';
	$query_vars[] = 'bf_form_slug';
	$query_vars[] = 'bf_post_id';
	$query_vars[] = 'bf_parent_post_id';
	$query_vars[] = 'bf_rev_id';
	$query_vars[] = 'form_slug';
	$query_vars[] = 'post_id';
	$query_vars[] = '_wpnonce';

	return $query_vars;
}

/**
 * rewrite the url of the edit-this-post link in the frontend
 *
 * @package BuddyForms
 * @since 0.3 beta
 */
add_filter( 'get_edit_post_link', 'buddyforms_my_edit_post_link', 1, 3 );
/**
 * @param $url
 * @param $post_ID
 *
 * @return string
 */
function buddyforms_my_edit_post_link( $url, $post_ID ) {
	global $buddyforms, $current_user;

	if ( is_admin() ) {
		return $url;
	}

	if ( ! isset( $buddyforms ) ) {
		return $url;
	}

	$the_post         = get_post( $post_ID );
	$post_type        = get_post_type( $the_post );
	$form_slug        = get_post_meta( $post_ID, '_bf_form_slug', true );
	$posttype_default = get_option( 'buddyforms_posttypes_default' );

	if ( ! $form_slug && isset( $posttype_default[ $post_type ] ) || $form_slug == 'none' && isset( $posttype_default[ $post_type ] ) ) {
		$form_slug = $posttype_default[ $post_type ];
	}

	if ( $form_slug == 'none' ) {
		return $url;
	}

	$the_author_id = apply_filters( 'buddyforms_the_author_id', $the_post->post_author, $form_slug, $post_ID );

	if ( $the_author_id != $current_user->ID ) {
		return $url;
	}

	if ( isset( $buddyforms[ $form_slug ]['edit_link'] ) && $buddyforms[ $form_slug ]['edit_link'] == 'none' ) {
		return $url;
	}

	if ( isset( $buddyforms[ $form_slug ]['edit_link'] ) && $buddyforms[ $form_slug ]['edit_link'] == 'my-posts-list' ) {
		return $url;
	}

	if ( isset( $buddyforms[ $form_slug ] ) && $buddyforms[ $form_slug ]['post_type'] == $post_type && ! empty( $buddyforms[ $form_slug ]['attached_page'] ) ) {

		$permalink = get_permalink( $buddyforms[ $form_slug ]['attached_page'] );
		$url       = $permalink . 'edit/' . $form_slug . '/' . $post_ID;

		return $url;
	}

	return $url;
}

/**
 * Retrieve edit posts link for post.
 *
 * Can be used within the WordPress loop or outside of it. Can be used with
 * pages, posts, attachments, and revisions.
 *
 * @param int $id Optional. Post ID.
 * @param string $context Optional, defaults to display. How to write the '&', defaults to '&amp;'.
 *
 * @return string The edit post link for the given post.
 * @since 2.3.0
 *
 */
function buddyforms_get_edit_post_link( $id = 0, $context = 'display' ) {
	if ( ! $post = get_post( $id ) ) {
		return $id;
	}

	if ( 'revision' === $post->post_type ) {
		$action = '';
	} elseif ( 'display' == $context ) {
		$action = '&amp;action=edit';
	} else {
		$action = '&action=edit';
	}

	$post_type_object = get_post_type_object( $post->post_type );
	if ( ! $post_type_object ) {
		return $id;
	}


	/**
	 * Filter the post edit link.
	 *
	 * @param string $link The edit link.
	 * @param int $post_id Post ID.
	 * @param string $context The link context. If set to 'display' then ampersands
	 *                        are encoded.
	 *
	 * @since 2.3.0
	 *
	 */
	return apply_filters( 'get_edit_post_link', admin_url( sprintf( $post_type_object->_edit_link . $action, $post->ID ) ), $post->ID, $context );
}

/**
 * Redirect Registration Page
 */
function buddyforms_registration_page_redirect() {
	global $pagenow;

	if ( ! isset( $_GET['action'] ) ) {
		return;
	}

	if ( ( strtolower( $pagenow ) == 'wp-login.php' ) && ( strtolower( $_GET['action'] ) == 'register' ) ) {

		$buddyforms_registration_page = get_option( 'buddyforms_registration_page' );

		if ( $buddyforms_registration_page != 'none' ) {
			$permalink = get_permalink( $buddyforms_registration_page );
			wp_redirect( $permalink );
		}

	}
}

add_filter( 'init', 'buddyforms_registration_page_redirect' );

/**
 * Redirect after login
 *
 * @param $redirect_to
 * @param $request
 * @param $user
 *
 * @return mixed|void
 */
function buddyforms_login_redirect( $redirect_to, $request, $user ) {
	global $pagenow;

	if ( ( strtolower( $pagenow ) == 'wp-login.php' ) ) {
		// Look for 'redirect_to'
		if ( isset( $_REQUEST['redirect_to'] ) && is_string( $_REQUEST['redirect_to'] ) && isset( $_REQUEST['log'] ) ) {
			if ( ! empty( $_REQUEST['form_slug'] ) && $_REQUEST['form_slug'] !== 'none' ) {
				$form_slug = buddyforms_sanitize_slug( $_REQUEST['form_slug'] );
				global $buddyforms;
				if ( ! empty( $buddyforms ) && isset( $buddyforms[ $form_slug ] ) && $buddyforms[ $form_slug ]['form_type'] === 'registration' ) {
					$redirect_url = apply_filters( 'buddyforms_login_form_redirect_url', $_REQUEST['redirect_to'] );
				}
			} else {
				$redirect_url = apply_filters( 'buddyforms_login_form_redirect_url', $_REQUEST['redirect_to'] );
			}

			if ( ! empty( $redirect_url ) ) {
				$redirect_to = $redirect_url;
			}

		}
	}

	return $redirect_to;
}

add_filter( 'login_redirect', 'buddyforms_login_redirect', 99999, 3 );

/**
 * Function to control how is processed the registration page
 *
 * @param $content
 *
 * @return mixed|string|void
 */
function buddyforms_registration_page_content( $content ) {
	global $post;

	if ( empty( $post ) || empty( $post->post_name ) ) {
		return $content;
	}

	$page_id = buddyforms_get_ID_by_page_name( $post->post_name );

	if ( empty( $page_id ) ) {
		return $content;
	}

	$buddyforms_registration_page = get_option( 'buddyforms_registration_page' );
	$buddyforms_registration_form = get_option( 'buddyforms_registration_form' );

	if ( empty( $buddyforms_registration_page ) || empty( $buddyforms_registration_form ) ) {
		return $content;
	}

	$bp_get_signup_slug = false;
	if ( function_exists( 'bp_get_signup_slug' ) ) {
		$bp_get_signup_slug = bp_get_signup_slug();
	}

	if ( ( $page_id == $buddyforms_registration_page || ( $bp_get_signup_slug !== false && $post->post_name == $bp_get_signup_slug ) ) && $buddyforms_registration_form != 'none' ) {
		if ( $buddyforms_registration_form == 'page' ) {
			$regpage = get_post( $buddyforms_registration_page );
			if ( ! empty( $regpage ) ) {
				// Remove the filter to make sure it not end up in a infinity loop
				remove_filter( 'the_content', 'buddyforms_registration_page_content', 99999 );

				$content = apply_filters( 'the_content', $regpage->post_content );
				$content = str_replace( ']]>', ']]&gt;', $content );

				// Rebuild the removed filters
				add_filter( 'the_content', 'buddyforms_registration_page_content', 99999 );

			}
		} else {
			$content = do_shortcode( '[bf form_slug="' . $buddyforms_registration_form . '"]' );
		}

		//Direct include of the assets with the new content because the normal flow not detect this new form to include the assets
		BuddyFormsAssets::front_js_css( $content, $buddyforms_registration_form );
		BuddyFormsAssets::load_tk_font_icons();
	}

	return $content;
}

add_filter( 'the_content', 'buddyforms_registration_page_content', 99999 );

function buddyforms_get_ID_by_page_name( $page_name ) {
	global $wpdb;
	$page_name_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s", $page_name ) );

	return $page_name_id;
}