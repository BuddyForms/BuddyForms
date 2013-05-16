<?php
class BuddyForms_Members {

	/**
	* Initiate the class
	*
	* @package buddyforms
	* @since 0.1-beta
	*/
	public function __construct() {
		add_action('bp_setup_nav', array($this, 'profile_setup_nav'), 20, 1);
	}

	function custom_get_user_posts_count($user_id, $args) {
		$args['author'] = $user;
		$args['fields'] = 'ids';
		$ps = get_posts($args);
		return count($ps);
	}

	/**
	* Setup profile navigation
	*
	* @package buddyforms
	* @since 0.1-beta
	*/
	public function profile_setup_nav() {
		global $buddyforms, $bp;

		get_currentuserinfo();

		session_start();

		$position = 20;

		if (empty($buddyforms[selected_post_types]))
			return;

		foreach ($buddyforms[selected_post_types] as $post_type) {
			$position++;

			$count = $this->custom_get_user_posts_count($user_ID, array('post_type' => $post_type));

			bp_core_new_nav_item(array('name' => sprintf('%s <span>%d</span>', $buddyforms['bp_post_types'][$post_type]['name'], $count), 'slug' => $post_type, 'position' => $position, 'screen_function' => array($this, 'buddyforms_screen_settings')));

			bp_core_new_subnav_item(array('name' => sprintf(__(' Add %s', 'buddyforms'), $buddyforms['bp_post_types'][$post_type]['singular_name']), 'slug' => 'create', 'parent_slug' => $post_type, 'parent_url' => trailingslashit(bp_loggedin_user_domain() . $post_type), 'item_css_id' => 'apps_sub_nav', 'screen_function' => array($this, 'load_members_post_create'), 'user_has_access' => bp_is_my_profile()));

		}

		//bp_core_remove_nav_item( 'groups' ); // @TODO here needs to come one global option to turn Groups nav on off
	}

	/**
	* Show the post create form
	*
	* @package buddyforms
	* @since 0.2-beta
	*/
	public function buddyforms_screen_settings() {
		global $current_user, $bp;

		if ($_GET[post_id]) {
			$bp->current_action = 'create';
			bp_core_load_template('buddyforms/members/members-post-create');
			return;
		}
		if ($_GET[delete]) {
			$bp->current_action = 'create';
			get_currentuserinfo();
			$the_post = get_post($_GET[delete]);

			if ($the_post->post_author != $current_user->ID) {
				echo '<div class="error alert">You are not allowed to delete this entry! What are you doing here?</div>';
				return;
			}
			
			do_action('buddyforms_delete_post',$_GET[delete]);
			
			wp_delete_post($_GET[delete]);

		}
		$bp->current_action = 'my-posts';
		bp_core_load_template('buddyforms/members/members-post-display');

	}

	/**
	* Show the post create form
	*
	* @package buddyforms
	* @since 0.2-beta
	*/
	public function load_members_post_create() {
		bp_core_load_template('buddyforms/members/members-post-create');
	}

}

add_action('buddyforms_init', new BuddyForms_Members());
?>