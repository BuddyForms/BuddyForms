<?php
/**
 * @package WordPress
 * @subpackage BuddyForms
 * @author ThemeKraft Dev Team
 * @copyright 2018
 * @link http://www.themekraft.com
 * @license http://www.apache.org/licenses/
 */

/**
 * Class BfAdminNotices
 *
 * Handle the notices inside the form builder
 */
class BfAdminNotices {
	public function __construct() {
		add_action('post_submitbox_start', array($this, 'buddyforms_notice'));
	}

	public function buddyforms_notice() {
		global $post, $buddyform;

		// Get the current screen
		$screen = get_current_screen();

		if ( ! ( $screen->parent_base == 'edit' && isset( $_GET['action'] ) ) ) {
			return;
		}

		if ( $post->post_type != 'buddyforms' ) {
			return;
		}

		if ( ! $buddyform ) {
			$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true );
		}

		if ( ! is_array( $buddyform ) ) {
			return;
		}

		switch ($buddyform['form_type']){
			case 'post':
				$this->validate_post_form($buddyform);
				break;
			case 'registration':
				$this->validate_registration_form($buddyform);
				break;
		}
	}

	public function validate_registration_form( $buddyform ) {
		$messages = array();

		$users_can_register = get_site_option( 'users_can_register' );

		if ( empty( $users_can_register ) ) {
			$messages[] = __( 'Registration is disabled on your site. Please enable registration if you like to use this form for registration purpose. You can still use it to update existing Users. <a href="/wp-admin/options-general.php">Set</a> registration to Anyone can register.', 'buddyforms' );
		}

		$this->show_form_notices( $messages );
	}

	public function validate_post_form($buddyform) {
		//
		// OK let us start with the form validation
		//
		$messages = array();
		if ( ! isset( $buddyform['post_type'] ) || isset( $buddyform['post_type'] ) && $buddyform['post_type'] == 'bf_submissions' ) {
			$messages[] = __( 'No Post Type Selected. Please select a post type', 'buddyforms' );
		}
		if ( isset( $buddyform['post_type'] ) ) {

			$post_types = buddyforms_get_post_types();

			if ( ! isset( $post_types[ $buddyform['post_type'] ] ) ) {
				$messages['pro'] = 'BuddyForms Professional is required to use this Form. You need to upgrade to the Professional Plan. The Free and Starter Versions does not support Custom Post Types <a href="edit.php?post_type=buddyforms&page=buddyforms-pricing">Go Pro Now</a>';
			}
			if ( buddyforms_core_fs()->is__premium_only() ) {
				if ( buddyforms_core_fs()->is_plan( 'professional' ) || buddyforms_core_fs()->is_trial() ) {
					if ( ! in_array( $buddyform['post_type'], $post_types ) ) {
						$messages[] = __( 'The Selected Post Type does not exist', 'buddyforms' );
					}
				}
			}

		}

		if ( isset( $buddyform['form_fields'] ) ) {
			foreach ( $buddyform['form_fields'] as $field_key => $field ) {
				if ( $field['type'] == 'taxonomy'
				     || $field['type'] == 'category'
				     || $field['type'] == 'tags'
				     || $field['type'] == 'featured_image'
				) {
					$messages['pro'] = 'BuddyForms Professional is required to use this Form. You need to upgrade to the Professional Plan. The Free and Starter Versions does not support the required Form Elements <a href="edit.php?post_type=buddyforms&page=buddyforms-pricing">Go Pro Now</a>';
				}
			}
		}

		if ( buddyforms_core_fs()->is__premium_only() ) {
			if ( buddyforms_core_fs()->is_plan( 'professional' ) || buddyforms_core_fs()->is_trial() ) {
				unset( $messages['pro'] );
			}
		}

		$messages = apply_filters( 'buddyforms_broken_form_error_messages', $messages );

		$this->show_form_notices($messages);
	}

	public function show_form_notices( $messages ) {
		if ( ! empty( $messages ) ) {
			include 'view/admin-notices.php';
		}
	}
}