<?php

/*
 * Freemius helper function to display individual go pro messages for the different arias of the admin ui
 */
/**
 * @param string $h2
 * @param string $h4
 * @param array $pros
 * @param bool $link
 */
function buddyforms_go_pro( $h2 = '', $h4 = '', $pros = Array(), $link = true ) {
	echo buddyforms_get_go_pro( $h2, $h4, $pros, $link );
}

/**
 * @param string $h2
 * @param string $h4
 * @param array $pros
 * @param bool $link
 *
 * @return string
 */
function buddyforms_get_go_pro( $h2 = '', $h4 = '', $pros = Array(), $link = true ) {
	if ( buddyforms_core_fs()->is_not_paying() ) {

		$tmp = '<div id="bf-gopro-sidebar">';
		$tmp .= ! empty( $h2 ) ? '<h2>' . $h2 . '</h2>' : '';
		$tmp .= '<div style="padding: 0 12px;">';
		$tmp .= ! empty( $h2 ) ? '<h4>' . $h4 . '</h4>' : '';
		$tmp .= '<ul>';
		foreach ( $pros as $key => $pro ) {
			$tmp .= '<li>' . $pro . '</li>';
		}
		$tmp .= '</ul>';

		if ( $link ) {
			$tmp .= '<a class="buddyforms_get_pro button button-primary" href="' . buddyforms_core_fs()->get_upgrade_url() . '">' . __( "Upgrade Now!", "buddyforms" ) . '</a>';
		}

		$tmp .= '</div></div>';

		return $tmp;
	}
}

function buddyforms_version_type() {
	echo buddyforms_get_version_type();
}

/**
 * @return string|void
 */
function buddyforms_get_version_type() {


	// This "if" block will be auto removed from the Free version.
	if ( buddyforms_core_fs()->is__premium_only() ) {
		if ( buddyforms_core_fs()->is_plan( 'starter', true ) ) {
			return '<b>' . __( 'Starter', 'buddyforms' ) . '</b>';
		} else if ( buddyforms_core_fs()->is_plan( 'professional' ) ) {
			return '<b>' . __( 'Professional', 'buddyforms' ) . '</b>';
		} else if( buddyforms_core_fs()->is_plan( 'business' ) ) {
			return '<b>' . __( 'Business', 'buddyforms' ) . '</b>';
		}
	}

	return '<b>' . __( 'Free', 'buddyforms' ) . '</b>';
}

function buddyforms_get_post_types(){
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
