<?php
class CPT4BP
{
	public $post_type_name;
	public $associated_item_tax_name;
	
	/**
	 * Initiate the class
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	
	public function __construct() {
		$this->init_hook();
		$this->load_constants();		
			
		add_action( 'bp_include', 			array( $this, 'includes' 				),  4, 1 );	
        add_action( 'init',   				array( $this, 'load_plugin_textdomain' 	), 10, 1 );
        add_action( 'bp_init', 				array( $this, 'setup_group_extension'	), 10, 1 );
		add_action( 'save_post', 			array( $this, 'create_a_group'			), 10, 2 );
		add_action( 'wp_trash_post',		array( $this, 'delete_a_group'			), 10, 1 );
	    add_action( 'template_redirect', 	array( $this, 'theme_redirect'			),  1, 2 );	
		add_action( 'bp_setup_nav', 		array( $this, 'profile_setup_nav'		), 20, 1 );
        add_action( 'bp_setup_globals',		array( $this, 'set_globals'				), 12, 1 );
        add_action( 'wp_enqueue_scripts', 	array( $this, 'enqueue_style'			), 10, 1 );
        add_action( 'widgets_init', 		array( $this, 'register_widgets'		), 10, 1 );

        add_action( 'after_switch_theme', 	array( $this, 'new_group_type_rewrite_flush' ) );
        
        add_filter( 'post_type_link', 		array( $this, 'remove_slug'					), 10, 3 );
        add_filter( 'post_updated_messages',array( $this, 'group_type_updated_messages' ), 10, 1 );
		add_filter( 'bp_located_template',  array( $this, 'load_template_filter' 		), 10, 2 );
 	}

	/**
	 * Register all available widgets
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	
	public function register_widgets() {
		register_widget( 'CPT4BP_Apps_Widget' 		);
		register_widget( 'CPT4BP_Categories_Widget' );
		register_widget( 'CPT4BP_Groups_Widget' 	);
		register_widget( 'CPT4BP_Product_Widget' 	);
	}
	
	/**
	 * Defines constants needed throughout the plugin.
	 *
	 * These constants can be overridden in bp-custom.php or wp-config.php.
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	
	public function load_constants() {	
		if( !defined( 'CPT4BP_INSTALL_PATH' ) )
			define( 'CPT4BP_INSTALL_PATH', dirname(__FILE__) .'/' );
			
		if( !defined( 'CPT4BP_INCLUDES_PATH' ) )
			define( 'CPT4BP_INCLUDES_PATH', CPT4BP_INSTALL_PATH .'includes/' );
		
		if( !defined( 'CPT4BP_TEMPLATE_PATH' ) )
			define( 'CPT4BP_TEMPLATE_PATH', CPT4BP_INCLUDES_PATH .'templates/' );
					
		if( !defined( 'BP_DOCS_EDIT_SLUG' ) )
			define( 'BP_DOCS_EDIT_SLUG', 'edit' );

		if( !defined( 'BP_DOCS_DELETE_SLUG' ) )
			define( 'BP_DOCS_DELETE_SLUG', 'delete' );			
	}

	/**
	 * Load the styles
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 	unknown
	 */	
	public function enqueue_style(){
     	wp_enqueue_style( 'cpt4bp-style', plugins_url( '/includes/css/cpt4bp.css', __FILE__ ) );
	}	
	     
	
	/**
	 * Includes files needed by BuddyPress CPT4BP
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	
	public function includes() {
		require_once( CPT4BP_INCLUDES_PATH .'PFBC/Form.php' 		);	
	    require_once( CPT4BP_INCLUDES_PATH .'templatetags.php' 		); 
        require_once( CPT4BP_INCLUDES_PATH .'functions.php' 		);
        require_once( CPT4BP_INCLUDES_PATH .'widget-apps.php' 		); 
        require_once( CPT4BP_INCLUDES_PATH .'widget-categories.php' ); 
        require_once( CPT4BP_INCLUDES_PATH .'widget-groups.php' 	); 
        require_once( CPT4BP_INCLUDES_PATH .'widget-product.php' 	); 
		
		if( is_admin() ) {
			require_once( CPT4BP_INCLUDES_PATH. 'admin.php' );
		}
	}

	/**
	 * Load the group extension file
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	
	public function setup_group_extension() {
		require_once(  CPT4BP_INCLUDES_PATH . 'group-extension.php' );
	}
	
	/**
	 * Defines bp_cpt4bp_init action
	 *
	 * This action fires on WP's init action and provides a way for the rest of BuddyPress
	 * CPT4BP, as well as other dependent plugins, to hook into the loading process in an
	 * orderly fashion.
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	*/
	public function init_hook() {
		do_action( 'bp_cpt4bp_init' );
	}
	
	/**
	 * Loads the textdomain for the plugin
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	 
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'cpt4bp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Setup all globals
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	 
    public function set_globals(){
        global $cpt4bp;
        
        $cpt4bp = get_option('cpt4bp_options');
		
			
		// echo '<pre>';
		// print_r($cpt4bp);
		// echo '</pre>';
		
		if(empty($cpt4bp['bp_post_types']))
			return;
		
		foreach ($cpt4bp['bp_post_types'] as $key => $value) {
			
			$post_type_object = get_post_type_object( $key );
			
			// echo '<pre>';
			// print_r($post_type_object);
			// echo '</pre>';
		
			if(empty($cpt4bp['bp_post_types'][$key][name]))
				$cpt4bp['bp_post_types'][$key][name] = $post_type_object->labels->name;
			
			if(empty($cpt4bp['bp_post_types'][$key][name]))
				$cpt4bp['bp_post_types'][$key][name] = $key;
		
			if(empty($cpt4bp['bp_post_types'][$key][slug]))
				$cpt4bp['bp_post_types'][$key][slug] = $key;
		}
		
		// echo '<pre>';
		// print_r($cpt4bp);
		// echo '</pre>';
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
			bp_core_load_template( 'bp/members_post_create' );
		}
		if($_GET[delete]){
			get_currentuserinfo();	
			$the_post = get_post( $_GET[delete] );
			
			if ($the_post->post_author != $current_user->ID){
				echo '<div class="error alert">You are not allowed to delete this entry! What are you doing here?</div>';
				return;	
			}
		
			$this->delete_a_group( $_GET[delete] );
			wp_delete_post( $_GET[delete] );

		}
		bp_core_load_template( 'bp/members_post_loop' );
	
	}

	/**
	 * Show the post create form
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.2-beta
	 */	 
	public function load_members_post_create() {
		bp_core_load_template( 'bp/members_post_create' );
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

	/**
	 * Creates a group if a group associated post is created   
	 *
	 * @package Custom Post Types for BuddyPress
	 * @since 0.1-beta
	 */	 
	public function create_a_group( $post_ID, $post ) {
		global $bp, $cpt4bp;
		// echo '<pre>';
		// print_r($cpt4bp);
		// echo '</pre>';
		// make sure we get the correct data
		if( $post->post_type == 'revision' )
			$post = get_post( $post->post_parent );		
			
	 	if( in_array( $post->post_type, $cpt4bp['selected_post_types'] ) ){	        
	     	$post_group_id = get_post_meta( $post->ID, '_post_group_id', true );
            
			$new_group = new BP_Groups_Group();
			
		    if( ! empty( $post_group_id ) )
		      	$new_group->id = $post_group_id;
			
		    $new_group->creator_id 	= $post->post_author;
	        $new_group->admins 		= $post->post_author;
	        $new_group->name 		= $post->post_title;
	        $new_group->slug 		= $post->post_name;
	        $new_group->description = $post->post_content;
			
	        if( $post->post_status == 'draft' )
		      	$new_group->status = 'hidden';

			elseif( $post->post_status == 'publish' )
	       		$new_group->status = 'public';
	            
	        $new_group->is_invitation_only 	= 1;
		    $new_group->enable_forum 		= 0;
		    $new_group->date_created 		= current_time( 'mysql' );
		    $new_group->total_member_count 	= 1;
			$new_group->save();
					
			update_post_meta( $post->ID, '_post_group_id', $new_group->id   );
			update_post_meta( $post->ID, '_link_to_group', $new_group->slug );
				
	        groups_update_groupmeta( $new_group->id, 'total_member_count', 	1 				 );
	        groups_update_groupmeta( $new_group->id, 'last_activity', 		time() 			 );
		    groups_update_groupmeta( $new_group->id, 'theme', 				'buddypress' 	 );
	   		groups_update_groupmeta( $new_group->id, 'stylesheet', 			'buddypress'  	 );
			groups_update_groupmeta( $new_group->id, 'group_post_id', 		$post->ID 		 );
			groups_update_groupmeta( $new_group->id, 'group_type', 			$post->post_type );
			
			echo bp_core_avatar_handle_upload($_FILES['async-upload'],'groups_avatar_upload_dir');
			//	require_once( ABSPATH . '/wp-admin/includes/file.php' );
			// wp_handle_upload( $_FILES['async-upload'], array( 'action'=> 'bp_avatar_upload' ));
			self::add_member_to_group( $new_group->id, $post->post_author );			
	 	}	   
	 }

	/**
	 * Deletes a group if a group associated post is deleted   
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	 
	public function delete_a_group( $post_id ) {
		global $cpt4bp;
		$post = get_post( $post_id );
		
		if( in_array( $post->post_type, $cpt4bp['selected_post_types'] ) ) {	 
	     	$post_group_id = get_post_meta( $post->ID, '_post_group_id', true );
			
			if( ! empty( $post_group_id ) )
				groups_delete_group( $post_group_id );
		 }
	 }	  

	/**
	 * Add member to group as admin
	 * credidts go to boon georges. This function is coppyed from the group management plugin.   
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	  
	public static function add_member_to_group( $group_id, $user_id = false ) {
		global $bp;
	
		if( ! $user_id )
			$user_id = $bp->loggedin_user->id;
	
		/* Check if the user has an outstanding invite, is so delete it. */
		if( groups_check_user_has_invite( $user_id, $group_id ) )
			groups_delete_invite( $user_id, $group_id );
	
		/* Check if the user has an outstanding request, is so delete it. */
		if( groups_check_for_membership_request( $user_id, $group_id ) )
			groups_delete_membership_request( $user_id, $group_id );
	
		/* User is already a member, just return true */
		if( groups_is_user_member( $user_id, $group_id ) )
			return true;
	
		if( ! $bp->groups->current_group )
			$bp->groups->current_group = new BP_Groups_Group( $group_id );
	
		$new_member = new BP_Groups_Member;
		$new_member->group_id 		= $group_id;
		$new_member->user_id 		= $user_id;
		$new_member->inviter_id 	= 0;
		$new_member->is_admin 		= 1;
		$new_member->user_title 	= '';
		$new_member->date_modified 	= gmdate( "Y-m-d H:i:s" );
		$new_member->is_confirmed 	= 1;
	
		if( ! $new_member->save() )
			return false;
	
		/* Record this in activity streams */
		groups_record_activity( array(
			'user_id' 	=> $user_id,
			'action' 	=> apply_filters( 'groups_activity_joined_group', sprintf( __( '%s joined the group %s', 'cpt4bp' ), bp_core_get_userlink( $user_id ), '<a href="'. bp_get_group_permalink( $bp->groups->current_group ) .'">'. esc_html( $bp->groups->current_group->name ) .'</a>' ) ),
			'type' 		=> 'joined_group',
			'item_id' 	=> $group_id
		) );
	
		/* Modify group meta */
		groups_update_groupmeta( $group_id, 'total_member_count', (int) groups_get_groupmeta( $group_id, 'total_member_count') + 1 );
		groups_update_groupmeta( $group_id, 'last_activity', gmdate( "Y-m-d H:i:s" ) );
	
		do_action( 'groups_join_group', $group_id, $user_id );
	
		return true;
	}
	 

	/**
 	 * Flush rewrite rules
	 * 
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta	
	 */
    public function new_group_type_rewrite_flush() {
        flush_rewrite_rules();
    }

	/**
 	 * Adjust backend messages
	 * 
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta	
	 */
    public function group_type_updated_messages( $messages ) {
		global $post, $post_ID, $cpt4bp;
      
		foreach( (array) $cpt4bp->new_post_type_slugs as $post_type ) :        
			$messages[$post_type] = array(
		        0 => '', // Unused. Messages start at index 1.
		        1 => sprintf( __('%s updated. <a href="%s">View %s</a>'), $cpt4bp->bp_post_types[$post_type]['singular_name'], esc_url( get_permalink($post_ID) ),strtolower($cpt4bp->bp_post_types[$post_type]['singular_name']) ),
		        2 => __('Custom field updated.'),
		        3 => __('Custom field deleted.'),
		        4 => sprintf( __('%s updated'), $cpt4bp->bp_post_types[$post_type][singular_name] ),
		        /* translators: %s: date and time of the revision */
		        5 => isset($_GET['revision']) ? sprintf( __('%s restored to revision from %s'), $cpt4bp->bp_post_types[$post_type]['singular_name'], wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		        6 => sprintf( __('%s published. <a href="%s">View %s</a>'),$cpt4bp->bp_post_types[$post_type]['singular_name'], esc_url( get_permalink($post_ID) ), strtolower($cpt4bp->bp_post_types[$post_type]['singular_name']) ),
		        7 => sprintf( __('%s saved'), $cpt4bp->bp_post_types[$post_type][singular_name] ),
		        8 => sprintf( __('%s submitted. <a target="_blank" href="%s">Preview %s</a>'), $cpt4bp->bp_post_types[$post_type]['singular_name'],  esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ), strtolower($cpt4bp->bp_post_types[$post_type]['singular_name']) ),
		        9 => sprintf( __('%s scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview %s</a>'),
		          // translators: Publish box date format, see http://php.net/date
		          date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		        10 => sprintf( __('%s draft updated. <a target="_blank" href="%s">Preview %s</a>'), $cpt4bp->bp_post_types[$post_type]['singular_name'], esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ), strtolower($cpt4bp->bp_post_types[$post_type]['singular_name']) ),
			);    
		endforeach;
		
		return $messages;
    }

	/**
 	 * Change the slug to groups slug to keep it consistent
	 * 
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta	
	 */
	public function remove_slug( $permalink, $post, $leavename ) {
        global $cpt4bp;
        
        $post_types = array_merge( (array) $cpt4bp->existing_post_type_slugs, (array) $cpt4bp->new_post_type_slugs );
  
        foreach( $post_types as $post_type ){
             if( $post_type )
                $permalink = str_replace( get_bloginfo('url') .'/'. $post_type , get_bloginfo('url') .'/groups', $permalink );
        }

		return $permalink;
    }
 
	/**
	 * Redirect a post to its group
	 * 
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta	
	 */
	public function theme_redirect() {
	   global $wp_query, $bp, $post, $cpt4bp;
	    $plugindir = dirname( __FILE__ );

		//A Specific Custom Post Type redirect to the atached group
		if( in_array( $wp_query->query_vars['post_type'], $cpt4bp['selected_post_types'] ) ) {
    		if( is_singular() ) {
				$link = get_bloginfo('url') .'/'. BP_GROUPS_SLUG .'/'. get_post_meta( $wp_query->post->ID, '_link_to_group', true );

    			wp_redirect( $link, '301' );
    			exit;
    		} else {
    		    foreach( $cpt4bp['selected_post_types'] as $post_type ) :
					$templatefilename = '';
					 
                    if( $wp_query->query_vars['post_type'] == $post_type ){
                        $templatefilename = 'page-'. $post_type .'.php';
						
	                    if( file_exists( STYLESHEETPATH .'/'. $templatefilename ) ) {
	                        $return_template = STYLESHEETPATH .'/'. $templatefilename;
							
	                    } elseif( file_exists( TEMPLATEPATH .'/'. $templatefilename ) ) {
	                        $return_template = TEMPLATEPATH .'/'. $templatefilename;
							
	                    } else {
	                       $return_template = $plugindir .'/includes/templates/wp/taxonomy.php';
	                    }
						
	                   	self::do_theme_redirect( $return_template );  
					}
                endforeach;
    		}

        // A custom Taxonomy Page	
	    } else {
    	    foreach( $cpt4bp['selected_post_types'] as $post_type ) :
				$templatefilename = '';
              	if( isset( $wp_query->query_vars[$post_type .'_category'] ) ) {
	                $templatefilename = 'taxonomy-'.$post_type.'_category.php';
	                    
                    if( file_exists( STYLESHEETPATH .'/'. $templatefilename ) ) {
                        $return_template = STYLESHEETPATH .'/'. $templatefilename;
							
                    } elseif( file_exists( TEMPLATEPATH .'/'. $templatefilename ) ) {
                        $return_template = TEMPLATEPATH .'/'. $templatefilename;
							
                    } else {
                       $return_template = $plugindir .'/includes/templates/wp/taxonomy.php';
                    }
					
                   	self::do_theme_redirect( $return_template );  
              	}        
            endforeach;
       	}
	}

	/**
	 * Perform the redirect
	 * 
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta	
	 */
	public static function do_theme_redirect( $url ) {
	    global $wp_query;
		if( have_posts() ) {
	        include( $url );
	        die();
	    } else {
	        $wp_query->is_404 = true;
	    }
	}	
}