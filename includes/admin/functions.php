<?php

/*
 * Freemius helper function to display individual go pro messages for the different arias of the admin ui
 */
/**
 * @param string $h2
 * @param string $h4
 * @param array  $pros
 * @param bool   $link
 */
function buddyforms_go_pro( $h2 = '', $h4 = '', $pros = array(), $link = true ) {
	echo wp_kses( buddyforms_get_go_pro( $h2, $h4, $pros, $link ), buddyforms_wp_kses_allowed_atts() );
}

/**
 * @param string $h2
 * @param string $h4
 * @param array  $pros
 * @param bool   $link
 *
 * @return string
 */
function buddyforms_get_go_pro( $h2 = '', $h4 = '', $pros = array(), $link = true ) {
	if ( buddyforms_core_fs()->is_not_paying() && ! buddyforms_core_fs()->is_trial() ) {

		$tmp  = '<div id="bf-gopro-sidebar">';
		$tmp .= ! empty( $h2 ) ? '<h2>' . $h2 . '</h2>' : '';
		$tmp .= '<div style="padding: 0 12px;">';
		$tmp .= ! empty( $h2 ) ? '<h4>' . $h4 . '</h4>' : '';
		$tmp .= '<ul>';
		foreach ( $pros as $key => $pro ) {
			$tmp .= '<li>' . $pro . '</li>';
		}
		$tmp .= '</ul>';

		if ( $link ) {
			$tmp .= '<a class="buddyforms_get_pro button button-primary" href="' . buddyforms_core_fs()->get_upgrade_url() . '">' . __( 'Upgrade Now!', 'buddyforms' ) . '</a>';
		}

		$tmp .= '</div></div>';

		return $tmp;
	}
}

function buddyforms_version_type() {
	echo wp_kses( buddyforms_get_version_type(), buddyforms_wp_kses_allowed_atts() );
}

/**
 * @return string|void
 */
function buddyforms_get_version_type() {

	// This "if" block will be auto removed from the Free version.
	if ( buddyforms_core_fs()->is__premium_only() ) {
		if ( buddyforms_core_fs()->is_plan( 'starter', true ) ) {
			return '<b>' . __( 'Starter', 'buddyforms' ) . '</b>';
		} elseif ( buddyforms_core_fs()->is_plan( 'professional' ) ) {
			return '<b>' . __( 'Professional', 'buddyforms' ) . '</b>';
		} elseif ( buddyforms_core_fs()->is_plan( 'business' ) ) {
			return '<b>' . __( 'Business', 'buddyforms' ) . '</b>';
		}
	}

	return '<b>' . __( 'Free', 'buddyforms' ) . '</b>';
}

add_filter( 'display_post_states', 'buddyforms_add_label_to_post_list', 10, 2 );
/**
 * Add a label to the post in the list in the backend where the title was auto generated
 *
 * @param $post_states
 * @param $post
 *
 * @return mixed
 */
function buddyforms_add_label_to_post_list( $post_states, $post ) {
	if ( is_admin() && ! empty( $post ) && ! empty( $post->ID ) ) {
		$is_buddyform_post = buddyforms_get_form_slug_by_post_id( $post->ID );
		if ( ! empty( $is_buddyform_post ) ) {
			$title_field = buddyforms_get_form_field_by_slug( $is_buddyform_post, 'buddyforms_form_title' );
			if ( ! empty( $title_field ) && ! empty( $title_field['generate_title'] ) ) {
				$post_states = array( '<span class="bf-auto-generated-title">' . __( 'Generated Title', 'buddyform' ) . '</span>' );
			}
		}
	}

	return $post_states;
}

add_filter( 'the_title', 'buddyforms_strip_html_title_for_entries_in_post_screen', 100, 2 );
/**
 * Change the title in the backend post list
 *
 * @param $title
 * @param $id
 *
 * @return string
 */
function buddyforms_strip_html_title_for_entries_in_post_screen( $title, $id = null ) {
	if ( is_admin() && ! empty( $id ) ) {
		$is_buddyform_post = buddyforms_get_form_slug_by_post_id( $id );
		if ( ! empty( $is_buddyform_post ) && $is_buddyform_post !== 'none' ) {
			$title = wp_strip_all_tags( html_entity_decode( $title ), true );
		}
	}

	return $title;
}

/**
 * Update the metas of a form to match the new form slug
 *
 * @param $old_slug
 * @param $new_slug
 *
 * @return bool
 * @since 2.4.6
 */
function buddyforms_update_form_slug( $old_slug, $new_slug ) {
	if ( empty( $new_slug ) || empty( $old_slug ) ) {
		return false;
	}
	global $wpdb;

	$count = $wpdb->get_var( $wpdb->prepare( "select count(meta_id) from {$wpdb->postmeta} where meta_value = %s", $old_slug ) );

	if ( empty( $count ) ) {
		return true;
	}

	$result = $wpdb->update( $wpdb->postmeta, array( 'meta_value' => $new_slug ), array( 'meta_value' => $old_slug ) );

	return ! empty( $result );
}

/**
 * Convert an array of shortcodes into a html
 *
 * @param $shortcodes_array
 * @param $target_element
 *
 * @return string
 * @author gfirem
 *
 * @since 2.5.10
 */
function buddyforms_get_shortcode_string( $shortcodes_array, $target_element ) {
	$all_shortcodes = array();
	foreach ( $shortcodes_array as $index => $short ) {
		$action_html              = sprintf( '<a href="" class="buddyforms-shortcodes-action" data-short="%s" data-target="%s">%s</a>', $short, $target_element, $short );
		$all_shortcodes[ $index ] = $action_html;
	}

	return '<div class="buddyforms-shortcodes-container">' . implode( ', ', $all_shortcodes ) . '</div>';
}

/**
 * Return array with the default shortcodes to use in the helper
 *
 * @param $form_slug
 * @param $element_name
 *
 * @return array
 * @author gfirem
 * @since 2.5.10
 */
function buddyforms_available_shortcodes( $form_slug, $element_name ) {
	return apply_filters(
		'buddyforms_available_shortcodes',
		array(
			'[user_login]',
			'[user_nicename]',
			'[first_name]',
			'[last_name]',
			'[published_post_link_plain]',
			'[published_post_link_html]',
			'[published_post_title]',
			'[site_name]',
			'[site_url]',
			'[site_url_html]',
		),
		$form_slug,
		$element_name
	);
}

/**
 * Return array of the fields type to not include in the list of shortcodes in the helper
 *
 * @param $form_slug
 * @param $element_name
 *
 * @return array
 * @author gfirem
 * @since 2.5.10
 */
function buddyforms_unauthorized_shortcodes_field_type( $form_slug, $element_name ) {
	return apply_filters( 'buddyforms_unauthorized_shortcodes_field_type', array(), $form_slug, $element_name );
}

/**
 * Clean up external TinyMCE plugins
 * to avoid errors on BuddyForms pages.
 */
add_filter( 'mce_external_plugins', 'buddyforms_remove_mce_external_plugins', 999 );
function buddyforms_remove_mce_external_plugins( $plugins ) {
	return ( is_admin() && get_post_type() !== 'buddyforms' ) ? $plugins : array();
}

add_filter( 'display_post_states', 'buddyforms_add_display_post_states', 999, 2 );
function buddyforms_add_display_post_states( $post_states, $post ) {

	if ( $post->ID === (int) get_option( 'buddyforms_preview_page' ) ) {
		$post_states['buddyforms-preview-page'] = __( 'BuddyForms Preview Page', 'woocommerce' );
	}

	if ( $post->ID === (int) get_option( 'buddyforms_submissions_page' ) ) {
		$post_states['buddyforms-submissions-page'] = __( 'BuddyForms Submissions Page', 'woocommerce' );
	}

	return $post_states;
}
