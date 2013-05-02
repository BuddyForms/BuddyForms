<?php
if ( class_exists( 'BP_Group_Extension' ) ) :
class CPT4BP_Group_Extension extends BP_Group_Extension
{  
	public $enable_create_step = true;
	public $enable_nav_item 	= false;
	public $enable_edit_item 	= true;

	/**
	 * Extends the group and register the nav item and add groupmeta to the $bp global
	 * 
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta	
	 */
    public function __construct() {
    	global $bp, $cpt4bp;
			
		/**
		 * @TODO Is this supposed to loop through everything and constantly replace the parameters?
		 */ 
    	if( bp_has_groups() ) :
	    	while( bp_groups() ) : bp_the_group();
				$attached_post_id 	= groups_get_groupmeta( bp_get_group_id(), 'group_post_id' );
				$attached_post_type = groups_get_groupmeta( bp_get_group_id(), 'group_type' );
			endwhile; 
		endif;
		// echo '<pre>';
		// print_r($cpt4bp);
		// echo '</pre>';
		// echo $cpt4bp['bp_post_types'][$attached_post_type]['groups']['display_post'];
	   switch ($cpt4bp['bp_post_types'][$attached_post_type]['groups']['display_post']) {

			case 'before group activity post form':
			   		add_action('bp_before_group_activity_post_form', array( $this, 'display_post'), 1 );
				break;
			case 'before group activity content':
					add_action('bp_before_group_activity_content', array( $this, 'display_post'), 1 );
				break;
			case 'after group activity content':
			   		add_action('bp_after_group_activity_content', array( $this, 'display_post'), 1 );
				break;
		    case 'create a new tab':
					 $this->enable_nav_item 	= true;
			   break;
			case 'replace home new tab activity':
				add_filter( 'bp_located_template', 'cpt4bp_groups_load_template_filter', 10, 2 );
				$this->add_activity_tab();
				break;

	   }

				$this->name					= $cpt4bp['bp_post_types'][$attached_post_type]['singular_name'];
			    $this->nav_item_position 	= 20;
		    	$this->slug 				= $cpt4bp['bp_post_types'][$attached_post_type]['slug'];
		//add_action( 'bp_after_group_details_admin', array( $this, 'edit_screen'), 1 );
		
		
	}
	
	 function display_post() {
			cpt4bp_locate_template('cpt4bp/single-post.php');
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

		cpt4bp_locate_template('cpt4bp/single-post.php');

	} 
	
	/**
	 * Add an activity tab
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	 
	public function add_activity_tab() {
		global $bp;
	 
		if( bp_is_group() ) {
			bp_core_new_subnav_item( 
				array( 
					'name' 				=> 'Activity', 
					'slug' 				=> 'activity', 
					'parent_slug' 		=> $bp->groups->current_group->slug, 
					'parent_url' 		=> bp_get_group_permalink( $bp->groups->current_group ), 
					'position' 			=> 11, 
					'item_css_id' 		=> 'nav-activity',
					'screen_function' 	=> create_function( '', "bp_core_load_template( apply_filters( 'groups_template_group_home', 'groups/single/home' ) );" ),
					'user_has_access' 	=> 1
				) 
			);
	 
			if( bp_is_current_action( 'activity' ) ) {
				add_action( 'bp_template_content_header', create_function( '', 'echo "'. esc_attr( 'Activity' ) .'";' ) );
				add_action( 'bp_template_title', 		  create_function( '', 'echo "'. esc_attr( 'Activity' ) .'";' ) );
			}
		}
	}
	
}
bp_register_group_extension( 'CPT4BP_Group_Extension' );
endif;