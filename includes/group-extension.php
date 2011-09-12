<?php
class CGT_Group_Extension extends BP_Group_Extension {  
 
	var $enable_create_step = false; // If your extension does not need a creation step, set this to false
	var $enable_nav_item = true; // If your extension does not need a navigation item, set this to false
	var $enable_edit_item = false; // If your extension does not need an edit screen, set this to false

	/**
	 * Extends the group and register the nav item and add groupmeta to the $bp global
	 * 
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta	
	 */
    function cgt_group_extension() {
    	global $bp;
    
    	// Add the displayed groupmeta for group type and post id to the $bp global
    	if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group();
	    	$bp->bp_cgt->attached_post_id = groups_get_groupmeta( bp_get_group_id(), 'group_post_id' );
			$bp->bp_cgt->attached_post_type = groups_get_groupmeta( bp_get_group_id(), 'group_type' );
		endwhile; endif;
	    
		// Check if the Group extention nav title has bean overwriten in the admin settings for this group type
		if( get_option($bp->bp_cgt->attached_post_type.'_name') != '' ){
			$this->name = get_option($bp->bp_cgt->attached_post_type.'_name');
        } else {
			$this->name = $bp->bp_cgt->attached_post_type;
        }
		
		$this->slug = $bp->bp_cgt->attached_post_type;
        $this->nav_item_position = 20;
    }
 

	/**
	 * Display or edid a Post
	 * 
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta	
	 */
    function display() {
		global $bp;
	
		// Get the group attached post
		$post = query_posts( array('post_type' => $bp->bp_cgt->attached_post_type, 'p' => $bp->bp_cgt->attached_post_id ));
			
		// load the template for display or edit the post
		if ( empty( $bp->action_variables[0] ) ) {
			cgt_locate_template('cgt/single-post.php');
			
	 	} else if ( $bp->action_variables[0] == BP_DOCS_EDIT_SLUG ) {
			cgt_locate_template('cgt/edit-post.php');
	 	} else if ( $bp->action_variables[0] == BP_DOCS_DELETE_SLUG ) {
			cgt_locate_template('cgt/delete-post.php');
	 	}
	 	
    }
 
}
bp_register_group_extension( 'CGT_Group_Extension' );
?>