<?php
class CPT4BP_Members
{

	/**
	 * Initiate the class
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	
	public function __construct() {
		add_action( 'bp_setup_nav', 		array( $this, 'profile_setup_nav'		), 20, 1 );
		add_filter( 'bp_located_template',  array( $this, 'load_template_filter' 	), 10, 2 );
	}
	
	/**
	 * Setup profile navigation
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	 
	public function profile_setup_nav() {
	    global $cpt4bp, $bp;
		
		session_start();
		
		$post_count = array();
        
		// count up the groups for a user, sorted by group type
		if( bp_has_groups( array( 'user_id' => bp_displayed_user_id() ) ) ) :			
			while( bp_groups() ) : bp_the_group();		
			 	$group_type = groups_get_groupmeta( bp_get_group_id(), 'group_type' );
						
				if( ! isset( $post_count[$group_type] ) )
					$post_count[$group_type] = 0;
				
				$post_count[$group_type]++;		
			endwhile;
		endif;
		
		$position = 20; 
		
		if(empty($cpt4bp[selected_post_types]))
			return;
		
        foreach( $cpt4bp[selected_post_types] as $post_type ) {
			$position ++;
			
			$count = isset( $post_count[$post_type] ) ? $post_count[$post_type] : 0;
			
			bp_core_new_nav_item( array( 
		 		'name' 				=> sprintf( '%s <span>%d</span>', $cpt4bp['bp_post_types'][$post_type]['name'], $count ),
	            'slug' 				=> $post_type, 
	            'position' 			=> $position,
	            'screen_function' 	=> array( $this, 'cpt4bp_screen_settings' )
			) );
			
			bp_core_new_subnav_item( array( 
                'name' 				=> sprintf(__(' Add %s', 'cpt4bp' ), $cpt4bp['bp_post_types'][$post_type]['name']),
                'slug' 				=> 'create', 
                'parent_slug' 		=> $post_type, 
                'parent_url' 		=> trailingslashit( bp_loggedin_user_domain() . $post_type ),
                'item_css_id' 		=> 'apps_sub_nav',
                'screen_function' 	=> array( $this, 'load_members_post_create' ),
                'user_has_access'	=> bp_is_my_profile()
	        ) );

		}
		
		//bp_core_remove_nav_item( 'groups' );	
	}


	/**
	 * Show the post create form
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.2-beta
	 */	 
	public function cpt4bp_screen_settings() {
		global $current_user, $bp;

		if($_GET[post_id]){
			bp_core_load_template( 'cpt4bp/bp/members-post-create' );
		}
		if($_GET[delete]){
			get_currentuserinfo();	
			$the_post = get_post( $_GET[delete] );
			
			if ($the_post->post_author != $current_user->ID){
				echo '<div class="error alert">You are not allowed to delete this entry! What are you doing here?</div>';
				return;	
			}
		
			cpt4bp_delete_a_group( $_GET[delete] );
			wp_delete_post( $_GET[delete] );

		}
		bp_core_load_template( 'cpt4bp/bp/members-post-display' );
	
	}

	/**
	 * Show the post create form
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.2-beta
	 */	 
	public function load_members_post_create() {
		bp_core_load_template( 'cpt4bp/bp/members-post-create' );
	}

	/**
	 * Look for the templates in the proper places
	 * 
	 * @package BuddyPress Custom Group Types
	 * @since 0.2-beta
	 */
	public function load_template_filter( $found_template, $templates ) {

		if( empty( $found_template ) ) :
			foreach( (array)$templates as $template ) {
				if( file_exists( STYLESHEETPATH . '/' . $template ) )
					$filtered_template = STYLESHEETPATH . '/' . $template;
						
				else
					$filtered_template =  CPT4BP_TEMPLATE_PATH . $template;
			}
			
			if( file_exists( $filtered_template ) ) :	
				return apply_filters( 'cpt4bp_load_template_filter', $filtered_template );
			else :
				return '';
			endif;
		else :
			return $found_template;
		endif;
	}

}
add_action('cpt4bp_init',new CPT4BP_Members())
?>