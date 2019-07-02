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
	if ( buddyforms_core_fs()->is_not_paying() && ! buddyforms_core_fs()->is_trial() ) {

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
		} else if ( buddyforms_core_fs()->is_plan( 'business' ) ) {
			return '<b>' . __( 'Business', 'buddyforms' ) . '</b>';
		}
	}

	return '<b>' . __( 'Free', 'buddyforms' ) . '</b>';
}

add_action( 'admin_notices', 'buddyforms_rating_admin_notice' );
function buddyforms_rating_admin_notice() {

	if ( defined( 'BUDDYFORMS_PRO_VERSION' ) ) {
		return;
	}

	$user_id = get_current_user_id();
	if ( ! get_user_meta( $user_id, 'buddyforms_rating_admin_notice_dismissed' ) ) {
		?>
		<div class="notice notice-success is-dismissible">
			<h4 style="margin-top: 20px;">Hey, you just updated to the <?php echo BUDDYFORMS_VERSION ?> version of BuddyForms – that’s awesome!</h4>
			<p style="line-height: 2.2; font-size: 13px;">Could you please do me a BIG favor and give it a 5-star rating
				on WordPress? Just to help us spread the word and boost our motivation.
			<p>
			<p style="margin: 20px 0;">
				<a class="button xbutton-primary"
				   style="font-size: 15px; padding: 8px 20px; height: auto; line-height: 1;"
				   href="?buddyforms_rating_admin_notice_dismissed">Dismiss</a>
				<a class="button button-primary"
				   style="font-size: 15px; padding: 8px 20px; height: auto; line-height: 1; box-shadow: none; text-shadow: none; background: #46b450; color: #fff; border: 1px solid rgba(0,0,0,0.1);"
				   href="https://wordpress.org/support/plugin/buddyforms/reviews/" target="_blank">Review Now</a>
			</p>
		</div>
		<?php
	}

}

add_action( 'admin_init', 'buddyforms_rating_admin_notice_dismissed' );
function buddyforms_rating_admin_notice_dismissed() {
	$user_id = get_current_user_id();
	if ( isset( $_GET['buddyforms_rating_admin_notice_dismissed'] ) ) {
		add_user_meta( $user_id, 'buddyforms_rating_admin_notice_dismissed', 'true', true );
	}
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
		if ( ! empty( $is_buddyform_post ) ) {
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
 *
 */
function buddyforms_update_form_slug( $old_slug, $new_slug ) {
	if ( empty( $new_slug ) || empty( $old_slug ) ) {
		return false;
	}
	global $wpdb;

	$count = $wpdb->get_var($wpdb->prepare("select count(meta_id) from {$wpdb->postmeta} where meta_value = %s", $old_slug));

	if ( empty( $count ) ) {
		return true;
	}

	$result = $wpdb->update( $wpdb->postmeta, array( 'meta_value' => $new_slug ), array( 'meta_value' => $old_slug ) );

	return ! empty( $result );
}