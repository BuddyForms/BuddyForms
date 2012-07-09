<?php
class CGT_Group_Extension extends BP_Group_Extension {  
 
	var $enable_create_step = true; // If your extension does not need a creation step, set this to false
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
		
		//$this->slug = $bp->bp_cgt->attached_post_type;
		if( $bp->bp_cgt->attached_post_type == 'product'){
	        $this->name = 'Review';
	        $this->nav_item_position = 20;
	    	$this->slug = 'product-review';
		}
		
		add_action( 'bp_after_group_details_admin', array( $this, 'edit_screen'), 1 );
	}
 
	function edit_screen() {
		global $post; ?>
        
        <?php cgt_locate_template('cgt/edit-post.php'); ?>
 
        <?php
    wp_nonce_field( 'groups_edit_save_' . $this->slug );
    }
 
    function edit_screen_save() {
        global $bp;
 
        if ( !isset( $_POST['save'] ) )
            return false;
 
        check_admin_referer( 'groups_edit_save_' . $this->slug );
 
        /* Insert your edit screen save code here */
 
        /* To post an error/success message to the screen, use the following */
    if ( !$success )
        bp_core_add_message( __( 'There was an error saving, please try again', 'buddypress' ), 'error' );
    else
        bp_core_add_message( __( 'Settings saved successfully', 'buddypress' ) );
 
        bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . '/admin/' . $this->slug );
	}
	
	/**
	 * Display or edid a Post
	 * 
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta	
	 */
    function display() {
		global $bp, $wc_query;
		
		//$wc_query = new WP_Query( array('post_type' => $bp->bp_cgt->attached_post_type, 'p' => $bp->bp_cgt->attached_post_id ) ); 

			
		// load the template for display or edit the post
		if ( empty( $bp->action_variables[0] ) ) {
			
	 		if ( $wc_query->have_posts() ) while ( $wc_query->have_posts() ) : $wc_query->the_post(); 
				
			 do_action('woocommerce_groups_single_product_review'); 		

			endwhile;	
	 	} else if ( $bp->action_variables[0] == BP_DOCS_EDIT_SLUG ) {
			cgt_locate_template('cgt/edit-post.php');
	 	} else if ( $bp->action_variables[0] == BP_DOCS_DELETE_SLUG ) {
			cgt_locate_template('cgt/delete-post.php');
	 	}
	 	
    }
 
}
bp_register_group_extension( 'CGT_Group_Extension' );
?>