<?php
class CPT4BP_GroupControl {

	/**
	 * Initiate the class
	 *
	 * @package CPT4BP
	 * @since 0.1-beta
	 */
	public function __construct() {
		add_action('save_post', array($this, 'create_a_group'), 10, 2);
		add_action('wp_trash_post', array($this, 'delete_a_group'), 10, 1);
	}

	/**
	 * Creates a group if a group associated post is created
	 *
	 * @package CPT4BP
	 * @since 0.1-beta
	 */
	public function create_a_group($post_ID, $post) {
		global $bp, $cpt4bp;

		if (!isset($cpt4bp['selected_post_types']))
			return;

		if (!isset($cpt4bp['bp_post_types'][$post->post_type]['groups']['attache']))
			return;

		if (!class_exists('BP_Groups_Group'))
			return;

		// make sure we get the correct data
		if ($post->post_type == 'revision')
			$post = get_post($post->post_parent);

		if (in_array($post->post_type, $cpt4bp['selected_post_types'])) {
			$post_group_id = get_post_meta($post->ID, '_post_group_id', true);

			$new_group = new BP_Groups_Group();

			if (!empty($post_group_id))
				$new_group->id = $post_group_id;

			$new_group->creator_id = $post->post_author;
			$new_group->admins = $post->post_author;
			$new_group->name = $post->post_title;
			$new_group->slug = $post->post_name;
			$new_group->description = $post->post_content;

			if ($post->post_status == 'draft')
				$new_group->status = 'hidden';
			elseif ($post->post_status == 'publish')
				$new_group->status = 'public';

			$new_group->is_invitation_only = 1;
			$new_group->enable_forum = 0;
			$new_group->date_created = current_time('mysql');
			$new_group->total_member_count = 1;
			$new_group->save();

			update_post_meta($post->ID, '_post_group_id', $new_group->id);
			update_post_meta($post->ID, '_link_to_group', $new_group->slug);

			groups_update_groupmeta($new_group->id, 'total_member_count', 1);
			groups_update_groupmeta($new_group->id, 'last_activity', time());
			groups_update_groupmeta($new_group->id, 'theme', 'buddypress');
			groups_update_groupmeta($new_group->id, 'stylesheet', 'buddypress');
			groups_update_groupmeta($new_group->id, 'group_post_id', $post->ID);
			groups_update_groupmeta($new_group->id, 'group_type', $post->post_type);

			self::add_member_to_group($new_group->id, $post->post_author);
		}
	}

	/**
	 * Deletes a group if a group associated post is deleted
	 *
	 * @package CPT4BP
	 * @since 0.1-beta
	 */
	public function delete_a_group($post_id) {
		global $cpt4bp;
		$post = get_post($post_id);

		if (in_array($post->post_type, $cpt4bp['selected_post_types'])) {
			$post_group_id = get_post_meta($post->ID, '_post_group_id', true);

			if (!empty($post_group_id))
				groups_delete_group($post_group_id);
		}
	}

	/**
	 * Add member to group as admin
	 * credidts go to boon georges. This function is coppyed from the group management plugin.
	 *
	 * @package CPT4BP
	 * @since 0.1-beta
	 */
	public static function add_member_to_group($group_id, $user_id = false) {
		global $bp;

		if (!$user_id)
			$user_id = $bp->loggedin_user->id;

		/* Check if the user has an outstanding invite, is so delete it. */
		if (groups_check_user_has_invite($user_id, $group_id))
			groups_delete_invite($user_id, $group_id);

		/* Check if the user has an outstanding request, is so delete it. */
		if (groups_check_for_membership_request($user_id, $group_id))
			groups_delete_membership_request($user_id, $group_id);

		/* User is already a member, just return true */
		if (groups_is_user_member($user_id, $group_id))
			return true;

		if (!$bp->groups->current_group)
			$bp->groups->current_group = new BP_Groups_Group($group_id);

		$new_member = new BP_Groups_Member;
		$new_member->group_id = $group_id;
		$new_member->user_id = $user_id;
		$new_member->inviter_id = 0;
		$new_member->is_admin = 1;
		$new_member->user_title = '';
		$new_member->date_modified = gmdate("Y-m-d H:i:s");
		$new_member->is_confirmed = 1;

		if (!$new_member->save())
			return false;

		/* Record this in activity streams */
		groups_record_activity(array('user_id' => $user_id, 'action' => apply_filters('groups_activity_joined_group', sprintf(__('%s joined the group %s', 'cpt4bp'), bp_core_get_userlink($user_id), '<a href="' . bp_get_group_permalink($bp->groups->current_group) . '">' . esc_html($bp->groups->current_group->name) . '</a>')), 'type' => 'joined_group', 'item_id' => $group_id));

		/* Modify group meta */
		groups_update_groupmeta($group_id, 'total_member_count', (int) groups_get_groupmeta($group_id, 'total_member_count') + 1);
		groups_update_groupmeta($group_id, 'last_activity', gmdate("Y-m-d H:i:s"));

		do_action('groups_join_group', $group_id, $user_id);

		return true;
	}

}
add_action('cpt4bp_init', new CPT4BP_GroupControl());

function cpt4bp_delete_a_group($post_id) {
	CPT4BP_GroupControl::delete_a_group($post_id);
}
?>