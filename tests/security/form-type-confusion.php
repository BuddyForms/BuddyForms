<?php
/**
 * Run on a disposable site with BuddyForms active:
 * wp eval-file tests/security/form-type-confusion.php
 * Sends real submissions through buddyforms_process_submission() and real saves through save_post,
 * each case in its own process, then checks what reached the database.
 */
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit;
}

if ( ! empty( $args ) ) {
	$case = json_decode( base64_decode( $args[0] ), true );
	wp_set_current_user( $case['user'] );
	if ( ! empty( $case['admin'] ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
		require_once ABSPATH . 'wp-admin/includes/screen.php';
		$GLOBALS['current_screen'] = WP_Screen::get( 'post' );
		// Admin-only files BuddyForms loads when is_admin() at boot; form-metabox.php needs the professional plan.
		require_once BUDDYFORMS_INCLUDES_PATH . 'admin/functions.php';
		require_once BUDDYFORMS_INCLUDES_PATH . 'admin/form-metabox.php';
	}
	$_SERVER['REQUEST_METHOD'] = 'POST';
	$_SERVER['REQUEST_URI']    = '/';
	$_POST                     = $case['post'];
	foreach ( (array) $case['nonces'] as $field => $action ) {
		$_POST[ $field ] = wp_create_nonce( $action );
	}
	if ( ! empty( $case['blocked_caps'] ) ) {
		add_filter( 'buddyforms_registration_blocked_capabilities', function () use ( $case ) {
			return $case['blocked_caps'];
		} );
	}
	add_filter( 'pre_wp_mail', '__return_true' );
	if ( 'submit' === $case['run'] ) {
		$result = buddyforms_process_submission( $_POST );
		echo wp_json_encode( array( 'hasError' => ! empty( $result['hasError'] ) ) );
	} else {
		$result = wp_update_post( array( 'ID' => $case['post_id'], 'post_title' => get_the_title( $case['post_id'] ) ), true );
		echo wp_json_encode( array( 'updated' => ! is_wp_error( $result ) ) );
	}
	return;
}

$users = $posts = array();
$passed = $failed = 0;
$previous_registration = get_option( 'users_can_register' );
$marker = 'bftc' . wp_rand( 100000, 999999 );
$fields = array(
	'title' => array( 'type' => 'title', 'slug' => 'buddyforms_form_title', 'name' => 'Title' ),
	'note'  => array( 'type' => 'text', 'slug' => 'bf_note', 'name' => 'Note', 'metabox_enabled' => array( 'metabox_enabled' ) ),
);
$form_options = array(
	'public'    => array( 'form_type' => 'post', 'post_type' => 'post', 'public_submit' => 'public_submit' ),
	'reg_admin' => array( 'form_type' => 'registration', 'post_type' => 'bf_submissions', 'registration' => array( 'new_user_role' => 'administrator' ) ),
	'reg_sub'   => array( 'form_type' => 'registration', 'post_type' => 'bf_submissions', 'registration' => array( 'new_user_role' => 'subscriber' ) ),
	'account'   => array( 'form_type' => 'post', 'post_type' => 'post', 'public_submit' => 'public_submit', 'public_submit_create_account' => array( 'public_submit_create_account' ), 'registration' => array( 'new_user_role' => 'administrator' ) ),
);
$forms_of_marker = function () use ( $marker ) {
	return get_posts( array( 'post_type' => 'buddyforms', 'post_status' => 'any', 'numberposts' => -1, 's' => $marker, 'fields' => 'ids' ) );
};
try {
	update_option( 'users_can_register', 1 );
	foreach ( array( 'administrator', 'subscriber' ) as $role ) {
		$id = wp_insert_user( array( 'user_login' => 'bftc-' . $role . '-' . $marker, 'user_email' => $role . '-' . $marker . '@example.com', 'user_pass' => wp_generate_password(), 'role' => $role ) );
		if ( is_wp_error( $id ) ) {
			throw new Exception( 'Unable to create user fixture' );
		}
		$users[ $role ] = $id;
	}
	$slugs = array();
	foreach ( $form_options as $name => $options ) {
		$id = wp_insert_post( array( 'post_type' => 'buddyforms', 'post_status' => 'publish', 'post_title' => 'BF fixture ' . $name . ' ' . wp_generate_uuid4(), 'post_author' => $users['administrator'] ), true );
		if ( is_wp_error( $id ) ) {
			throw new Exception( 'Unable to create form fixture' );
		}
		$posts[ $name ] = $id;
		$slugs[ $name ] = get_post_field( 'post_name', $id );
		update_post_meta( $id, '_buddyforms_options', array_merge( array( 'name' => $name, 'slug' => $slugs[ $name ], 'singular_name' => $name, 'status' => 'publish', 'comment_status' => 'closed', 'form_fields' => $fields ), $options ) );
	}
	// What an administrator's "Add New Form" leaves behind until it is saved.
	$posts['auto_draft'] = wp_insert_post( array( 'post_type' => 'buddyforms', 'post_status' => 'auto-draft', 'post_title' => 'Auto Draft', 'post_author' => $users['administrator'] ) );
	// Rendering a form creates an auto-draft of its post type; real submissions always carry its ID.
	foreach ( array( 'public' => 'post', 'account' => 'post', 'reg_admin' => 'bf_submissions', 'reg_sub' => 'bf_submissions', 'filtered' => 'bf_submissions' ) as $name => $type ) {
		$posts[ 'draft_' . $name ] = wp_insert_post( array( 'post_type' => $type, 'post_status' => 'auto-draft', 'post_title' => 'Auto Draft' ) );
	}
	$posts['published'] = wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'BF published ' . $marker, 'post_author' => $users['administrator'] ) );
	$posts['entry']      = wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'BF entry ' . $marker, 'post_author' => $users['administrator'] ) );
	update_post_meta( $posts['entry'], '_bf_form_slug', $slugs['public'] );
	buddyforms_regenerate_global_options();

	$injected = array( 'form_type' => 'registration', 'post_type' => 'post', 'status' => 'publish', 'registration' => array( 'new_user_role' => 'administrator' ), 'form_fields' => $fields, 'singular_name' => 'injected' );
	$submission = array( 'form_slug' => $slugs['public'], 'post_id' => $posts['draft_public'], 'form_action' => 'create', 'bf_post_type' => 'post', 'bf_hweb' => '', 'redirect_to' => '/', 'buddyforms_form_title' => 'BF public ' . $marker );
	$registration = function ( $slug, $who, $draft ) use ( $marker ) {
		$pass = wp_generate_password( 16, false );
		return array( 'form_slug' => $slug, 'post_id' => $draft, 'form_action' => 'create', 'bf_hweb' => '', 'redirect_to' => '/', 'user_login' => 'bftc' . $who . $marker, 'user_email' => 'bftc-' . $who . '-' . $marker . '@example.com', 'buddyforms_user_pass' => $pass, 'buddyforms_user_pass_confirm' => $pass );
	};
	$builder_post = function ( $name, $options ) use ( $posts, $slugs ) {
		return array( 'post_ID' => $posts[ $name ], 'buddyforms_options' => array_merge( get_post_meta( $posts[ $name ], '_buddyforms_options', true ), array( 'slug' => $slugs[ $name ] ), $options ) );
	};
	$form_nonce = array( '_wpnonce' => 'buddyforms_form_nonce' );

	$cases = array(
		'guest submission with bf_post_type=buddyforms creates no form' => array(
			'case'  => array( 'run' => 'submit', 'user' => 0, 'nonces' => $form_nonce, 'post' => array_merge( $submission, array( 'post_id' => $posts['published'], 'bf_post_type' => 'buddyforms', 'post_type' => 'buddyforms', 'buddyforms_form_title' => 'BF injected ' . $marker, 'buddyforms_options' => $injected ) ) ),
			'check' => function () use ( $forms_of_marker ) {
				return array() === $forms_of_marker();
			},
		),
		'guest submission cannot take over a buddyforms auto-draft' => array(
			'case'  => array( 'run' => 'submit', 'user' => 0, 'nonces' => $form_nonce, 'post' => array_merge( $submission, array( 'post_id' => $posts['auto_draft'], 'bf_post_type' => 'buddyforms', 'buddyforms_form_title' => 'BF auto-draft ' . $marker, 'buddyforms_options' => $injected ) ) ),
			'check' => function () use ( $posts ) {
				return 'auto-draft' === get_post_status( $posts['auto_draft'] ) && '' === get_post_meta( $posts['auto_draft'], '_buddyforms_options', true );
			},
		),
		'guest submission to the public form still creates its entry' => array(
			'case'  => array( 'run' => 'submit', 'user' => 0, 'nonces' => $form_nonce, 'post' => $submission ),
			'check' => function () use ( $posts, $marker ) {
				return 'publish' === get_post_status( $posts['draft_public'] ) && 'BF public ' . $marker === get_the_title( $posts['draft_public'] );
			},
		),
		'guest cannot register through a form with the administrator role' => array(
			'case'  => array( 'run' => 'submit', 'user' => 0, 'nonces' => $form_nonce, 'post' => $registration( $slugs['reg_admin'], 'admin', $posts['draft_reg_admin'] ) ),
			'check' => function () use ( $marker ) {
				return false === get_user_by( 'email', 'bftc-admin-' . $marker . '@example.com' );
			},
		),
		'guest cannot get the administrator role by creating an account with a post' => array(
			'case'  => array( 'run' => 'submit', 'user' => 0, 'nonces' => $form_nonce, 'post' => array_merge( $registration( $slugs['account'], 'account', $posts['draft_account'] ), array( 'bf_post_type' => 'post', 'buddyforms_form_title' => 'BF account ' . $marker ) ) ),
			'check' => function () use ( $marker ) {
				return false === get_user_by( 'email', 'bftc-account-' . $marker . '@example.com' );
			},
		),
		'guest can register through a form with the subscriber role' => array(
			'case'  => array( 'run' => 'submit', 'user' => 0, 'nonces' => $form_nonce, 'post' => $registration( $slugs['reg_sub'], 'sub', $posts['draft_reg_sub'] ) ),
			'check' => function () use ( $marker ) {
				$user = get_user_by( 'email', 'bftc-sub-' . $marker . '@example.com' );
				return $user && array( 'subscriber' ) === array_values( $user->roles );
			},
		),
		'the blocked capabilities filter can refuse more roles' => array(
			'case'  => array( 'run' => 'submit', 'user' => 0, 'nonces' => $form_nonce, 'blocked_caps' => array( 'read' ), 'post' => $registration( $slugs['reg_sub'], 'filtered', $posts['draft_filtered'] ) ),
			'check' => function () use ( $marker ) {
				return false === get_user_by( 'email', 'bftc-filtered-' . $marker . '@example.com' );
			},
		),
		'builder save without its nonce changes nothing' => array(
			'case'  => array( 'run' => 'save', 'user' => $users['administrator'], 'admin' => true, 'nonces' => array(), 'post_id' => $posts['public'], 'post' => $builder_post( 'public', array( 'form_type' => 'registration', 'singular_name' => 'changed', 'slug' => 'bftc-renamed-' . $marker ) ) ),
			'check' => function () use ( $posts, $slugs ) {
				$options = get_post_meta( $posts['public'], '_buddyforms_options', true );
				return 'post' === $options['form_type'] && 'public' === $options['singular_name'] && $slugs['public'] === get_post_field( 'post_name', $posts['public'] );
			},
		),
		'builder save by a subscriber with a valid nonce changes nothing' => array(
			'case'  => array( 'run' => 'save', 'user' => $users['subscriber'], 'admin' => true, 'nonces' => array( 'buddyforms_form_builder_nonce' => 'buddyforms_form_builder_save_' . $posts['public'] ), 'post_id' => $posts['public'], 'post' => $builder_post( 'public', array( 'form_type' => 'registration', 'singular_name' => 'changed' ) ) ),
			'check' => function () use ( $posts ) {
				$options = get_post_meta( $posts['public'], '_buddyforms_options', true );
				return 'post' === $options['form_type'] && 'public' === $options['singular_name'];
			},
		),
		'builder save with a nonce for another form changes nothing' => array(
			'case'  => array( 'run' => 'save', 'user' => $users['administrator'], 'admin' => true, 'nonces' => array( 'buddyforms_form_builder_nonce' => 'buddyforms_form_builder_save_' . $posts['reg_sub'] ), 'post_id' => $posts['public'], 'post' => $builder_post( 'public', array( 'form_type' => 'registration', 'singular_name' => 'changed' ) ) ),
			'check' => function () use ( $posts ) {
				$options = get_post_meta( $posts['public'], '_buddyforms_options', true );
				return 'post' === $options['form_type'] && 'public' === $options['singular_name'];
			},
		),
		'builder save outside wp-admin keeps the protected settings' => array(
			'case'  => array( 'run' => 'save', 'user' => $users['administrator'], 'nonces' => array( 'buddyforms_form_builder_nonce' => 'buddyforms_form_builder_save_' . $posts['public'] ), 'post_id' => $posts['public'], 'post' => $builder_post( 'public', array( 'form_type' => 'registration', 'public_submit' => '', 'registration' => array( 'new_user_role' => 'administrator' ), 'singular_name' => 'front' ) ) ),
			'check' => function () use ( $posts ) {
				$options = get_post_meta( $posts['public'], '_buddyforms_options', true );
				return 'post' === $options['form_type'] && 'public_submit' === $options['public_submit'] && ! isset( $options['registration'] ) && 'front' === $options['singular_name'];
			},
		),
		'builder save by an administrator in wp-admin saves the form' => array(
			'case'  => array( 'run' => 'save', 'user' => $users['administrator'], 'admin' => true, 'nonces' => array( 'buddyforms_form_builder_nonce' => 'buddyforms_form_builder_save_' . $posts['reg_sub'] ), 'post_id' => $posts['reg_sub'], 'post' => $builder_post( 'reg_sub', array( 'form_type' => 'post', 'singular_name' => 'saved' ) ) ),
			'check' => function () use ( $posts ) {
				$options = get_post_meta( $posts['reg_sub'], '_buddyforms_options', true );
				return 'post' === $options['form_type'] && 'saved' === $options['singular_name'];
			},
		),
		'entry meta box save without its nonce changes nothing' => array(
			'case'  => array( 'run' => 'save', 'user' => $users['administrator'], 'admin' => true, 'nonces' => array(), 'post_id' => $posts['entry'], 'post' => array( 'bf_note' => 'no nonce' ) ),
			'check' => function () use ( $posts ) {
				return '' === get_post_meta( $posts['entry'], 'bf_note', true );
			},
		),
		'entry meta box save by a subscriber with a valid nonce changes nothing' => array(
			'case'  => array( 'run' => 'save', 'user' => $users['subscriber'], 'admin' => true, 'nonces' => array( 'buddyforms_admin_form_metabox_nonce' => 'buddyforms_admin_form_metabox_' . $posts['entry'] ), 'post_id' => $posts['entry'], 'post' => array( 'bf_note' => 'subscriber' ) ),
			'check' => function () use ( $posts ) {
				return '' === get_post_meta( $posts['entry'], 'bf_note', true );
			},
		),
		'entry meta box save by an administrator saves the field' => array(
			'case'  => array( 'run' => 'save', 'user' => $users['administrator'], 'admin' => true, 'nonces' => array( 'buddyforms_admin_form_metabox_nonce' => 'buddyforms_admin_form_metabox_' . $posts['entry'] ), 'post_id' => $posts['entry'], 'post' => array( 'bf_note' => 'saved' ) ),
			'check' => function () use ( $posts ) {
				return 'saved' === get_post_meta( $posts['entry'], 'bf_note', true );
			},
		),
	);
	foreach ( $cases as $name => $test ) {
		$result = WP_CLI::launch_self( 'eval-file', array( __FILE__, base64_encode( wp_json_encode( $test['case'] ) ) ), array(), false, true );
		wp_cache_flush();
		$ok = 0 === $result->return_code && $test['check']();
		$ok ? ++$passed : ++$failed;
		WP_CLI::line( ( $ok ? 'PASS ' : 'FAIL ' ) . $name );
		if ( 0 !== $result->return_code ) {
			WP_CLI::line( '     ' . trim( $result->stderr ) );
		}
	}
} finally {
	foreach ( array_merge( $posts, $forms_of_marker() ) as $id ) {
		wp_delete_post( $id, true );
	}
	foreach ( get_posts( array( 'post_type' => array( 'post', 'bf_submissions' ), 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids', 'meta_query' => array( array( 'key' => '_bf_form_slug', 'value' => array_values( isset( $slugs ) ? $slugs : array() ), 'compare' => 'IN' ) ) ) ) as $id ) {
		wp_delete_post( $id, true );
	}
	require_once ABSPATH . 'wp-admin/includes/user.php';
	foreach ( get_users( array( 'search' => '*' . $marker . '*', 'fields' => 'ID' ) ) as $id ) {
		wp_delete_user( $id );
	}
	foreach ( $users as $id ) {
		wp_delete_user( $id );
	}
	update_option( 'users_can_register', $previous_registration );
	buddyforms_regenerate_global_options();
}
WP_CLI::line( sprintf( 'Results: %d passed, %d failed', $passed, $failed ) );
if ( $failed ) {
	throw new Exception( 'Form type confusion regression failed' );
}
