<?php

use tk\GuzzleHttp\Client;
use tk\GuzzleHttp\Psr7\Request;

add_action( 'wp_ajax_buddyforms_post_types_taxonomies', 'buddyforms_post_types_taxonomies' );
function buddyforms_post_types_taxonomies() {

	if ( ! isset( $_POST['post_type'] ) ) {
		echo 'false';
		die();
	}

	$post_type             = sanitize_text_field( wp_unslash( $_POST['post_type'] ) );
	$buddyforms_taxonomies = buddyforms_taxonomies( $post_type );

	$tmp = '';
	foreach ( $buddyforms_taxonomies as $name => $label ) {
		$tmp .= '<option value="' . $name . '">' . $label . '</option>';
	}

	$allowed = array(
		'option' => array(
			'value'    => array(),
			'selected' => array(),
			'disabled' => array(),
			'class'    => array(),
		),
	);
	echo wp_kses( $tmp, $allowed );
	die();

}

add_action( 'wp_ajax_buddyforms_close_submission_default_page_notification', 'buddyforms_close_submission_default_page_notification' );
/**
 * @return bool
 */
function buddyforms_close_submission_default_page_notification() {
	if ( ! ( is_array( $_POST ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		die();
	}
	if ( ! isset( $_POST['action'] ) || ! isset( $_POST['nonce'] ) || wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'fac_drop' ) === false || $_POST['action'] !== 'buddyforms_close_submission_default_page_notification' ) {
		die();
	}
	update_option( 'close_submission_default_page_notification', 1 );
	die();
}

add_action( 'wp_ajax_buddyforms_update_taxonomy_default', 'buddyforms_update_taxonomy_default' );
function buddyforms_update_taxonomy_default() {

	$allowed = array(
		'option' => array(
			'value'    => array(),
			'selected' => array(),
			'disabled' => array(),
			'class'    => array(),
		),
	);
	if ( ! isset( $_POST['taxonomy'] ) || $_POST['taxonomy'] == 'none' ) {
		$tmp = '<option value="none">' . __( 'First you need to select a Taxonomy to select the Taxonomy defaults', 'buddyforms' ) . '</option>';
		echo wp_kses( $tmp, $allowed );
		die();
	}

	$taxonomy = sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) );

	$args = array(
		'orderby'    => 'name',
		'order'      => 'ASC',
		'hide_empty' => false,
		'fields'     => 'id=>name',
	);

	$terms = get_terms( $taxonomy, $args );

	$tmp = '<option value="none">none</option>';
	foreach ( $terms as $key => $term_name ) {
		$tmp .= '<option value="' . $key . '">' . $term_name . '</option>';
	}
	echo wp_kses( $tmp, $allowed );

	die();

}

add_action( 'wp_ajax_buddyforms_new_page', 'buddyforms_new_page' );
/**
 * Create the holder page to be use as endpoint
 */
function buddyforms_new_page() {

	if ( ! is_admin() ) {
		return;
	}

	// Check if a title is entered
	if ( empty( $_POST['page_name'] ) ) {
		$json['error'] = __( 'Please enter a name', 'buddyforms' );
		echo json_encode( $json );
		die();
	}

	// Create post object
	$new_page = array(
		'post_title'   => sanitize_title( wp_unslash( $_POST['page_name'] ) ),
		'post_content' => '',
		'post_status'  => 'publish',
		'post_type'    => 'page',
	);

	// Insert the post into the database
	$new_page = wp_insert_post( $new_page );

	// Check if page creation worked successfully
	if ( is_wp_error( $new_page ) ) {
		$json['error'] = $new_page;
	} else {
		$json['id']   = $new_page;
		$json['name'] = sanitize_title( wp_unslash( $_POST['page_name'] ) );
	}

	echo json_encode( $json );
	die();

}

add_action( 'wp_ajax_buddyforms_url_builder', 'buddyforms_url_builder' );
function buddyforms_url_builder() {
	global $post;
	if ( ! isset( $_POST['attached_page'] ) || ! isset( $_POST['form_slug'] ) ) {
		return;
	}
	$page_id   = sanitize_key( wp_unslash( $_POST['attached_page'] ) );
	$form_slug = buddyforms_sanitize_slug( wp_unslash( $_POST['form_slug'] ) );
	$post      = get_post( $page_id );

	if ( isset( $post->post_name ) ) {
		$json['permalink'] = get_permalink( $page_id );
		$json['form_slug'] = $form_slug;
		echo json_encode( $json );
		die();
	}
	echo json_encode( 'none' );
	die();
}

/**
 * Ajax callback to process the user satisfaction.
 */
function buddyforms_user_satisfaction_ajax() {
	try {
		if ( ! ( is_array( $_POST ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			wp_send_json_error();
		}
		if ( ! isset( $_POST['action'] ) || ! isset( $_POST['nonce'] ) ) {
			wp_send_json_error();
		}
		if ( ! wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'fac_drop' ) ) {
			wp_send_json_error();
		}

		if ( ! isset( $_POST['user_satisfaction_key'] ) || empty( $_POST['user_satisfaction_value'] ) ) {
			wp_send_json_error();
		}

		$us_key   = sanitize_text_field( wp_unslash( $_POST['user_satisfaction_key'] ) );
		$us_value = sanitize_textarea_field( wp_unslash( $_POST['user_satisfaction_value'] ) );

		switch ( $us_key ) {
			case 'satisfaction_recommendation':
				if ( ! isset( $us_value ) || empty( $us_value ) ) {
					wp_send_json_error();
				}
				buddyforms_track(
					'$experiment_started',
					array(
						'Experiment name' => 'User Satisfaction',
						'Variant name'    => 'v1',
						'action'          => 'satisfaction-rate',
						'rate'            => intval( $us_value ),
					)
				);
				update_option( 'buddyforms_user_satisfaction_sent', 1 );

				wp_send_json( '' );
				break;
			case 'satisfaction_comments':
				if ( isset( $us_value ) && ! empty( $us_value ) ) {
					buddyforms_track(
						'$experiment_started',
						array(
							'Experiment name' => 'User Satisfaction',
							'Variant name'    => 'v1',
							'action'          => 'satisfaction-comment',
							'comment'         => $us_value,
						)
					);
				}

				wp_send_json( '' );
				break;
			default:
				wp_send_json_error();
				break;
		}
	} catch ( Exception $ex ) {
		wp_send_json_error( $ex->getMessage() );
	}
}

add_action( 'wp_ajax_buddyforms_user_satisfaction_ajax', 'buddyforms_user_satisfaction_ajax' );

/**
 * Ajax callback to close for ever or close one time the marketing popups
 */
function buddyforms_marketing_hide_for_ever_close() {
	try {
		if ( ! ( is_array( $_POST ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			die();
		}
		if ( ! isset( $_POST['action'] ) || ! isset( $_POST['nonce'] ) ) {
			die();
		}
		if ( ! wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'fac_drop' ) ) {
			die();
		}

		if ( ! empty( $_POST['popup_key'] ) ) {
			$key     = sanitize_text_field( wp_unslash( $_POST['popup_key'] ) );
			$options = get_option( 'buddyforms_marketing_hide_for_ever_close' );
			if ( ! empty( $options ) && is_array( $options ) ) {
				if ( empty( $options[ $key ] ) ) {
					$options[ $key ] = true;
				}
			} else {
				$options = array( $key => true );
			}
			update_option( 'buddyforms_marketing_hide_for_ever_close', $options );
		}

		wp_send_json( '' );
	} catch ( Exception $ex ) {
		BuddyForms::error_log( $ex->getMessage() );
	}
	die();
}

add_action( 'wp_ajax_buddyforms_marketing_hide_for_ever_close', 'buddyforms_marketing_hide_for_ever_close' );

/**
 * Ajax callback for the user reset permission related to Marketing in the setting page
 */
function buddyforms_marketing_reset_permissions() {
	try {
		if ( ! ( is_array( $_POST ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			die();
		}
		if ( ! isset( $_POST['action'] ) || ! isset( $_POST['nonce'] ) ) {
			die();
		}
		if ( ! wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'fac_drop' ) ) {
			die();
		}

		$result1 = delete_option( 'buddyforms_marketing_hide_for_ever_close' );
		$result2 = delete_option( 'buddyforms_user_satisfaction_sent' );
		$result  = $result1 && $result2;

		wp_send_json( $result );
	} catch ( Exception $ex ) {
		BuddyForms::error_log( $ex->getMessage() );
	}
	die();
}

add_action( 'wp_ajax_buddyforms_marketing_reset_permissions', 'buddyforms_marketing_reset_permissions' );

function buddyforms_custom_form_template_tracking() {
	if ( ! ( is_array( $_POST ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		die();
	}
	if ( ! isset( $_POST['action'] ) || ! isset( $_POST['nonce'] ) ) {
		die();
	}
	if ( ! wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'fac_drop' ) ) {
		die();
	}
	buddyforms_track(
		'selected-form-template',
		array(
			'template' => 'custom',
			'type'     => 'custom',
		)
	);
}

add_action( 'wp_ajax_buddyforms_custom_form_template', 'buddyforms_custom_form_template_tracking' );
