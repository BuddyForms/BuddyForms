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
		add_action('bp_located_template', array($this, 'buddyforms_load_template_filter'), 10, 2);
		
	}
	
	/**
	 * get the user posts count
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	*/
	function get_user_posts_count($user_id, $args) {
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

			$count = $this->get_user_posts_count($user_ID, array('post_type' => $post_type));

			bp_core_new_nav_item(array('name' => sprintf('%s <span>%d</span>', $buddyforms['bp_post_types'][$post_type]['name'], $count), 'slug' => $post_type, 'position' => $position, 'screen_function' => array($this, 'buddyforms_screen_settings')));

			bp_core_new_subnav_item(array('name' => sprintf(__(' Add %s', 'buddyforms'), $buddyforms['bp_post_types'][$post_type]['singular_name']), 'slug' => 'create', 'parent_slug' => $post_type, 'parent_url' => trailingslashit(bp_loggedin_user_domain() . $post_type), 'item_css_id' => 'apps_sub_nav', 'screen_function' => array($this, 'load_members_post_create'), 'user_has_access' => bp_is_my_profile()));

		}

		//bp_core_remove_nav_item( 'groups' ); // @TODO here needs to come one global option to turn Groups nav on off
	}

	/**
	 * Display the posts or the edit screen
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
		wp_enqueue_style('members-profil-css', plugins_url('css/members-profil.css', __FILE__));
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
	
	/**
	 * buddyforms template loader.
	 * 
	 * this function I copied from the buddypress.org website and modifired it for my needs 
	 *
	 * This function sets up buddyforms to use custom templates.
	 *
	 * If a template does not exist in the current theme, we will use our own
	 * bundled templates.
	 *
	 * We're doing two things here:
	 *  1) Support the older template format for themes that are using them
	 *     for backwards-compatibility (the template passed in
	 *     {@link bp_core_load_template()}).
	 *  2) Route older template names to use our new template locations and
	 *     format.
	 *
	 * View the inline doc for more details.
	 *
	 * @since 1.0
	 */
	function buddyforms_load_template_filter($found_template, $templates) {
	global $bp;
	if ($bp->current_action == 'create' || $bp->current_action == 'my-posts') {
	
			if (empty($found_template)) {
				// register our theme compat directory
				//
				// this tells BP to look for templates in our plugin directory last
				// when the template isn't found in the parent / child theme
				bp_register_template_stack('buddyforms_get_template_directory', 14);
	
				// locate_template() will attempt to find the plugins.php template in the
				// child and parent theme and return the located template when found
				//
				// plugins.php is the preferred template to use, since all we'd need to do is
				// inject our content into BP
				//
				// note: this is only really relevant for bp-default themes as theme compat
				// will kick in on its own when this template isn't found
				$found_template = locate_template('members/single/plugins.php', false, false);
	
				// add our hook to inject content into BP
				
				if ($bp->current_action == 'my-posts') {
					add_action('bp_template_content', create_function('', "
					bp_get_template_part( 'buddyforms/members/members-post-display' );
				"));
				} elseif ($bp->current_action == 'create') {
					add_action('bp_template_content', create_function('', "
					bp_get_template_part( 'buddyforms/members/members-post-create' );
				"));
				}
			}
		}
	
		return apply_filters('buddyforms_load_template_filter', $found_template);
	}
}

add_action('buddyforms_init', new BuddyForms_Members());
?>