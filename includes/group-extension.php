<?php
class CPT4BP_Group_Extension extends BP_Group_Extension
{  
	public $enable_create_step = true;
	public $enable_nav_item 	= true;
	public $enable_edit_item 	= true;

	/**
	 * Extends the group and register the nav item and add groupmeta to the $bp global
	 * 
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta	
	 */
    public function __construct() {
    	global $bp;
		
		if( ! is_object( $bp->bp_cpt4bp ) )
			$bp->bp_cpt4bp = new stdClass;
		   	

		/**
		 * @TODO Is this supposed to loop through everything and constantly replace the parameters?
		 */ 
    	if( bp_has_groups() ) :
	    	while( bp_groups() ) : bp_the_group();
		    	$bp->bp_cpt4bp->attached_post_id 	= groups_get_groupmeta( bp_get_group_id(), 'group_post_id' );
				$bp->bp_cpt4bp->attached_post_type = groups_get_groupmeta( bp_get_group_id(), 'group_type' );
			endwhile; 
		endif;
	    
		
		// Check if the Group extention nav title has bean overwriten in the admin settings for this group type
		$name = get_option( $bp->bp_cpt4bp->attached_post_type .'_name' );
		if( ! empty( $name ) ) {
			$this->name = $name;
        } else {
			$this->name = $bp->bp_cpt4bp->attached_post_type;
        }
		
		//$this->slug = $bp->bp_cpt4bp->attached_post_type;
		if( $bp->bp_cpt4bp->attached_post_type == 'product' ){
	        $this->name 				= 'Review';
	        $this->nav_item_position 	= 20;
	    	$this->slug 				= 'product-review';
		}
		
		add_action( 'bp_after_group_details_admin', array( $this, 'edit_screen'), 1 );
	}
 
	/**
	 * Display the edit screen
	 * 
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta	
	 */
	public function edit_screen() {
		global $post;
        
        cpt4bp_locate_template( 'cpt4bp/edit-post.php' );
 
    	wp_nonce_field( 'groups_edit_save_'. $this->slug );
    }
 
	/**
	 * Save action
	 * 
	 * @TODO	Figure out what this is supposed to do
	 * @package BuddyPress Custom Group Types
	 * @since 	0.1-beta	
	 */
    public function edit_screen_save() {
        global $bp;
 
        if( ! isset( $_POST['save'] ) )
            return false;
 
        check_admin_referer( 'groups_edit_save_' . $this->slug );
 
	    if( ! $success )
        	bp_core_add_message( __( 'There was an error saving, please try again', 'buddypress' ), 'error' );
	    else
	        bp_core_add_message( __( 'Settings saved successfully', 'buddypress' ) );
 
        bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . '/admin/' . $this->slug );
	}
	
	/**
	 * Display or edit a Post
	 * 
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta	
	 */
    public function display() {
		global $bp, $wc_query;
		
		$wc_query = new WP_Query( array(
			'post_type' => $bp->bp_cpt4bp->attached_post_type, 
			'p' 		=> $bp->bp_cpt4bp->attached_post_id
		) );
			
		// load the template for display or edit the post
		if( empty( $bp->action_variables[0] ) ) {			
	 		if( $wc_query->have_posts() ) :
	 			while ( $wc_query->have_posts() ) : $wc_query->the_post(); 
				
			 		do_action( 'woocommerce_groups_single_product_review' ); 		
				
				endwhile;
			endif;
				
	 	} elseif( $bp->action_variables[0] == BP_DOCS_EDIT_SLUG ) {
			cpt4bp_locate_template('cpt4bp/edit-post.php');
			
	 	} elseif( $bp->action_variables[0] == BP_DOCS_DELETE_SLUG ) {
			cpt4bp_locate_template('cpt4bp/delete-post.php');
	 	} else {
	 		cpt4bp_locate_template('cpt4bp/single-post.php');
	 	}	 	
    } 
}
bp_register_group_extension( 'CPT4BP_Group_Extension' );