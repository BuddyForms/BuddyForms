<?php
class BP_CGT {
	var $post_type_name;
	var $associated_item_tax_name;
	
	/**
	 * PHP 4 constructor
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */
	function BP_cgt() {
		$this->__construct();
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	
	function __construct() {
		global $bp;
					
		// Add Activity Tab for the activity stream, since we’re displacing it
		//add_action( 'bp_actions',	array( $this, 'add_activity_tab' ), 8 );
	
		// Load textdomain
		add_action( 'init',   array( $this, 'load_plugin_textdomain' ) );
       
        add_action( 'init', array( $this, 'add_firmen' ) );
        
		// Includes necessary files
		add_action('save_post', array( &$this, 'create_a_group'), 10, 2 );
		
		// Includes necessary files
		add_action('trash_post', array( &$this,'delete_a_group'), 10, 2 );
		
		// Includes necessary files
		add_action( 'init', array( &$this,'register_post_type'), 2 );
         
        add_action( 'after_switch_theme', 'new_group_type_rewrite_flush' );
        
        add_filter( 'post_updated_messages',array( $this,  'group_type_updated_messages' ));
              
		// Includes necessary files
		add_action( 'init', array( &$this,'register_taxonomy'), 1, 2 );
		
		// Includes necessary files
	    add_action("template_redirect", array( &$this,'theme_redirect'),1 , 2 );	
		
		// Load predefined constants first thing
		add_action( 'bp_cgt_init', 	array( $this, 'load_constants' ), 2 );
	
		// Includes necessary files
		add_action( 'bp_cgt_init', 	array( $this, 'includes' ), 4 );	
		
		add_action( 'bp_setup_nav', array( $this, 'profile_setup_nav'), 10 );
        
        add_filter( 'post_type_link', array( $this, 'remove_slug'), 10, 3 );
        
        // Let plugins know that BP Docs has started loading
		$this->init_hook();
		
		$this->framework_init();
        

         add_filter( 'plugins_loaded', array( $this, 'framework_init'), 10, 3 );
         add_action( 'after_setup_theme', array( $this, 'set_globals'), 12 );
  $this->enqueue_style();
 	}

function enqueue_style(){
        $plugindir = dirname( __FILE__ );
      
     wp_register_style( 'cgt-style', plugins_url('/includes/css/cgt.css', __FILE__) );
        wp_enqueue_style( 'cgt-style' );
    
}
        

    function set_globals(){
        global $bp, $cgt ;
        
        $cgt = tk_get_values( 'cgt-config' );
        
        foreach($cgt->new_post_types_slug as $key => $post_type_slug){
            if($post_type_slug != '')
                $post_types_slug[$post_type_slug] = $post_type_slug;
        }
        $cgt->new_post_types_slug = $post_types_slug;
       
       
        
        //$cgt->custom_field_slug = $custom_field_slug;
 
        $cgt->post_types = array_merge($cgt->existing_post_types, $post_types_slug);
    
        foreach ($cgt->post_types as $post_type) {
            foreach($cgt->custom_field_slug[$post_type] as $key => $field_slug){
                if($field_slug == '')
                    unset($cgt->custom_field_slug[$post_type][$key]);
            }
        }
       
    } 

    function framework_init(){
        
        // Registering the form where the data have to be saved
        $args['forms'] = array( 'cgt-config' );
        //$args['text_domain'] = 'cgt_text_domain';
        
        //require_once( 'loader.php' );
        
        tk_framework($args); 
         
    }
 
	// Set the profil menu navigation
	function profile_setup_nav() {
	    global $bp, $cgt;
        
		if ( bp_has_groups('user_id='.bp_displayed_user_id()) ) : 
			
			while ( bp_groups() ) : bp_the_group(); 
		
			 	$group_type = groups_get_groupmeta( bp_get_group_id(), 'group_type' );
				
				$post_count[$group_type] ++ ;
		
			endwhile;endif;
		
		$position = 20;
 
        foreach($cgt->post_types as $post_type) {
			$position ++;
			bp_core_new_nav_item( array( 
		 		'name' => sprintf(__( $cgt->new_group_types[$post_type]['name'].' <span>%d</span>', 'cgt' ), $post_count[$post_type]),
	            'slug' => $post_type, 
	            'position' => $position,
	            'screen_function' => create_function('',"bp_core_load_template( 'members_post_loop' );"),
			) );
            bp_core_new_subnav_item( 'subnav'.$post_type, 'subnav'.$post_type, __('new ', 'cgt').$cgt->new_group_types[$post_type]['name'], 'create', 'members_post_sub_menue', 'apps_sub_nav', true, false  );
			bp_core_new_subnav_item( 
            array( 
                'name' => sprintf(__(' Add %s', 'cgt' ), $cgt->new_group_types[$post_type]['name']),
                'slug' => 'create', 
                'parent_slug' => $post_type, 
                'parent_url' => $bp->loggedin_user->domain.$post_type.'/', 
                'item_css_id' => 'apps_sub_nav',
                'screen_function' => create_function('',"bp_core_load_template( 'members_post_create' );"),
                'user_has_access' => bp_is_my_profile()
            ) 
        );
		}		
 
	     bp_core_remove_nav_item('groups'); //needs to become an admin option
		 	
	}

	// Load a page template for your custom item. You'll need to have an item-one-template.php and item-two-template.php in your theme root.
	function members_post_create() {
	      
        do_shortcode('[create_group_type_form]'); 
                    
    }   
	
	function members_post_loop() {
	     // bp_core_load_template( 'members_post_loop_firmen' );
		  load_sub_template( array( BP_CGT_TEMPLATE_PATH.'/bp/members_post_loop.php' ) );
	}	
    
	function load_sub_template( $template ) {
		if ( $located_template = apply_filters( 'bp_located_template', locate_template( $template , false ), $template ) )	
			load_template( apply_filters( 'bp_load_template', $located_template ) );
	}

	function add_activity_tab() {
		global $bp;
	 
		if(bp_is_group()) {
			bp_core_new_subnav_item( 
				array( 
					'name' => 'Activity', 
					'slug' => 'activity', 
					'parent_slug' => $bp->groups->current_group->slug, 
					'parent_url' => bp_get_group_permalink( $bp->groups->current_group ), 
					'position' => 11, 
					'item_css_id' => 'nav-activity',
					'screen_function' => create_function('',"bp_core_load_template( apply_filters( 'groups_template_group_home', 'groups/single/home' ) );"),
					'user_has_access' => 1
				) 
			);
	 
			if ( bp_is_current_action( 'activity' ) ) {
				add_action( 'bp_template_content_header', create_function( '', 'echo "' . esc_attr( 'Activity' ) . '";' ) );
				add_action( 'bp_template_title', create_function( '', 'echo "' . esc_attr( 'Activity' ) . '";' ) );
			}
		}
	}
	
	/**
	 * Defines bp_cgt_init action
	 *
	 * This action fires on WP's init action and provides a way for the rest of BuddyPress
	 * CGT, as well as other dependent plugins, to hook into the loading process in an
	 * orderly fashion.
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	*/
	function init_hook() {
		do_action( 'bp_cgt_init' );
	}
	
	/**
	 * Defines constants needed throughout the plugin.
	 *
	 * These constants can be overridden in bp-custom.php or wp-config.php.
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	
	function load_constants() {
	
		// Define the default's
				
		if ( !defined( 'BP_CGT_INSTALL_PATH' ) )
			define( 'BP_CGT_INSTALL_PATH', dirname(__FILE__) . '/' );
			
		if ( !defined( 'BP_CGT_INCLUDES_PATH' ) )
			define( 'BP_CGT_INCLUDES_PATH', BP_CGT_INSTALL_PATH . 'includes/' );
		
		if ( !defined( 'BP_CGT_TEMPLATE_PATH' ) )
			define( 'BP_CGT_TEMPLATE_PATH', BP_CGT_INCLUDES_PATH . 'templates/' );
					
		if ( !defined( 'BP_DOCS_EDIT_SLUG' ) )
			define( 'BP_DOCS_EDIT_SLUG', 'edit' );

		if ( !defined( 'BP_DOCS_DELETE_SLUG' ) )
			define( 'BP_DOCS_DELETE_SLUG', 'delete' );
			
	}
	
	/**
	 * Includes files needed by BuddyPress CGT
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	*/	
	function includes() {

		require_once(  BP_CGT_INCLUDES_PATH . 'group-extension.php' );	
	    require_once(  BP_CGT_INCLUDES_PATH . 'templatetags.php' ); 
        require_once(  BP_CGT_INCLUDES_PATH . 'widgets.php' ); 
        require_once(  BP_CGT_INCLUDES_PATH . 'tkf/loader.php' );
    	
		if ( is_admin() ) {
			require_once(  BP_CGT_INCLUDES_PATH. 'admin.php' );
		}
	}
	
	/**
	 * Loads the textdomain for the plugin
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	 
	function load_plugin_textdomain() {
		load_plugin_textdomain( 'bp-cgt', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Creates a group if a group associated post is created   
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	 
	function create_a_group($post_ID, $post) {
		global $flag, $wpdb, $bp, $cgt;
		
           $post = query_posts(array( 'post_type' => $post->post_type, 'p' => $post->ID));
	       $post = $post[0];
			
	 	if( in_array( $post->post_type, $cgt->existing_post_types ) || in_array( $post->post_type, $cgt->new_post_types_slug ) ){
	        
	     	$post_group_id = get_post_meta($post->ID,"_post_group_id", true);
            
           //  $group =  new BP_Groups_Group( $post_group_id );
           
                $new_group = new BP_Groups_Group();
		        if($post_group_id != 0){
		         	$new_group->id = $post_group_id;
		         }
		        $new_group->creator_id = $post->post_author;
	            $new_group->admins = $post->post_author;
	            $new_group->name = 	$post->post_title;
	            $new_group->slug = $post->post_name;
	            $new_group->description = $post->post_content;
	            if($post->post_status == 'draft'){
		        	$new_group->status = 'hidden';
	            }
	            if($post->post_status == 'publish'){
	
	            		$new_group->status = 'public';
		        	
	            }
	            
	            $new_group->is_invitation_only = 1;
		        $new_group->enable_forum = 0;
		        $new_group->date_created = current_time('mysql');
		        $new_group->total_member_count = 1;
			    $new_group -> save(); //this does the database insert
					
			update_post_meta($post->ID,"_post_group_id",$new_group->id);
			update_post_meta($post->ID,"_link_to_group",$new_group->slug);
				
	        groups_update_groupmeta( $new_group->id, 'total_member_count', 1 );
	        groups_update_groupmeta( $new_group->id, 'last_activity', time() );
		    groups_update_groupmeta( $new_group->id, 'theme', 'buddypress' );
	   		groups_update_groupmeta( $new_group->id, 'stylesheet', 'buddypress' );
			groups_update_groupmeta( $new_group->id, 'group_post_id', $post->ID );
			groups_update_groupmeta( $new_group->id, 'group_type', $post->post_type );
			
			BP_CGT::add_member_to_group($new_group->id, $post->post_author );
			
	 	}
	   
	 }

	/**
	 * Deletes a group if a group associated post is deleted   
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	 
	function delete_a_group() {
		global $flag, $post, $bp, $wpdb, $cgt;
		
		$post = query_posts(array( 'post_type' => $post->post_type, 'p' => $post->ID));
		$post = $post[0];
		
	 	if( in_array( $post->post_type, $cgt->existing_post_types ) || in_array( $post->post_type, $cgt->new_post_types_slug)) {
	 	
	     	$post_group_id = get_post_meta($post->ID,"_post_group_id", true);
	     	
	            $new_group = new BP_Groups_Group();
	            
	            $new_group->id = $post_group_id;    
	                  
		 	    $new_group -> delete(); //this does the database insert
	
		 	  $wpdb->query( $wpdb->prepare( "DELETE FROM " . $bp->groups->table_name_groupmeta . " WHERE group_id = %d", $new_group->id ) );
		 	    
		 }
	   
	 }
	  

	/**
	 * Add member to group as admin
	 * credidts go to boon georges. This function is coppyed from the group management plugin.   
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	  
	function add_member_to_group( $group_id, $user_id = false ) {
		global $bp;
	
		if ( !$user_id )
			$user_id = $bp->loggedin_user->id;
	
		/* Check if the user has an outstanding invite, is so delete it. */
		if ( groups_check_user_has_invite( $user_id, $group_id ) )
			groups_delete_invite( $user_id, $group_id );
	
		/* Check if the user has an outstanding request, is so delete it. */
		if ( groups_check_for_membership_request( $user_id, $group_id ) )
			groups_delete_membership_request( $user_id, $group_id );
	
		/* User is already a member, just return true */
		if ( groups_is_user_member( $user_id, $group_id ) )
			return true;
	
		if ( !$bp->groups->current_group )
			$bp->groups->current_group = new BP_Groups_Group( $group_id );
	
		$new_member = new BP_Groups_Member;
		$new_member->group_id = $group_id;
		$new_member->user_id = $user_id;
		$new_member->inviter_id = 0;
		$new_member->is_admin = 1;
		$new_member->user_title = '';
		$new_member->date_modified = gmdate( "Y-m-d H:i:s" );
		$new_member->is_confirmed = 1;
	
		if ( !$new_member->save() )
			return false;
	
		/* Record this in activity streams */
		groups_record_activity( array(
			'user_id' => $user_id,
			'action' => apply_filters( 'groups_activity_joined_group', sprintf( __( '%s joined the group %s', 'bp-group-management'), bp_core_get_userlink( $user_id ), '<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . '">' . esc_html( $bp->groups->current_group->name ) . '</a>' ) ),
			'type' => 'joined_group',
			'item_id' => $group_id
		) );
	
		/* Modify group meta */
		groups_update_groupmeta( $group_id, 'total_member_count', (int) groups_get_groupmeta( $group_id, 'total_member_count') + 1 );
		groups_update_groupmeta( $group_id, 'last_activity', gmdate( "Y-m-d H:i:s" ) );
	
		do_action( 'groups_join_group', $group_id, $user_id );
	
		return true;
	}
	 
	 /**
 	 * Registers BuddyPress CGT post type
	 * 
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta	
	 */
	function register_post_type() {
		global $bp, $cgt;
        
		foreach ($cgt->new_post_types_slug as $post_type) :
             if($post_type != '') {
                 $labels = array(
                    'name' => _x($cgt->new_group_types[$post_type][name], 'post type general name'),
                    'singular_name' => _x($cgt->new_group_types[$post_type][singular_name], 'post type singular name'),
                    'add_new' => _x('Add New', strtolower($cgt->new_group_types[$post_type][singular_name])),
                    'add_new_item' => __('Add New '. $cgt->new_group_types[$post_type][singular_name]),
                    'edit_item' => __('Edit '.$cgt->new_group_types[$post_type][singular_name]),
                    'new_item' => __('New '.$cgt->new_group_types[$post_type][singular_name]),
                    'all_items' => __('All '. $cgt->new_group_types[$post_type][name]),
                    'view_item' => __('View '. $cgt->new_group_types[$post_type][name]),
                    'search_items' => __('Search '. $cgt->new_group_types[$post_type][name]),
                    'not_found' =>  __('No '.$cgt->new_group_types[$post_type][name].' found'),
                    'not_found_in_trash' => __('No '.strtolower($cgt->new_group_types[$post_type][name]).' found in Trash'), 
                    'parent_item_colon' => '',
                    'menu_name' => $cgt->new_group_types[$post_type][name]
                
                  );
                  
                   $args = array(
                    'labels' => $labels,
                    'public' => true,
                    'publicly_queryable' => true,
                    'show_ui' => true, 
                    'show_in_menu' => true, 
                    'query_var' => true,
                    'rewrite' => true,
                    'capability_type' => 'post',
                    'has_archive' => true, 
                    'hierarchical' => false,
                    'menu_position' => null,
                    'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
                  );            
                 register_post_type($post_type, $args);
             }
		endforeach; 
	  
	}

    function new_group_type_rewrite_flush() {
        flush_rewrite_rules();
    }

    //add filter to ensure the text is displayed when user updates
    
    function group_type_updated_messages( $messages ) {
      global $post, $post_ID, $cgt;
      
      foreach ($cgt->new_post_types_slug as $post_type) :
        
        $messages[$post_type] = array(
        0 => '', // Unused. Messages start at index 1.
        1 => sprintf( __('%s updated. <a href="%s">View %s</a>'),$cgt->new_group_types[$post_type][singular_name], esc_url( get_permalink($post_ID) ),strtolower($cgt->new_group_types[$post_type][singular_name]) ),
        2 => __('Custom field updated.'),
        3 => __('Custom field deleted.'),
        4 => sprintf( __('%s updated'), $cgt->new_group_types[$post_type][singular_name] ),
        /* translators: %s: date and time of the revision */
        5 => isset($_GET['revision']) ? sprintf( __('%s restored to revision from %s'), $cgt->new_group_types[$post_type][singular_name], wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6 => sprintf( __('%s published. <a href="%s">View %s</a>'),$cgt->new_group_types[$post_type][singular_name], esc_url( get_permalink($post_ID) ), strtolower($cgt->new_group_types[$post_type][singular_name]) ),
        7 => sprintf( __('%s saved'), $cgt->new_group_types[$post_type][singular_name] ),
        8 => sprintf( __('%s submitted. <a target="_blank" href="%s">Preview %s</a>'), $cgt->new_group_types[$post_type][singular_name],  esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ), strtolower($cgt->new_group_types[$post_type][singular_name]) ),
        9 => sprintf( __('%s scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview %s</a>'),
          // translators: Publish box date format, see http://php.net/date
          date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
        10 => sprintf( __('%s draft updated. <a target="_blank" href="%s">Preview %s</a>'), $cgt->new_group_types[$post_type][singular_name], esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ), strtolower($cgt->new_group_types[$post_type][singular_name]) ),
      );    
      endforeach;
      return $messages;
    }

	 /**
 	 * 
	 * Registers BuddyPress CGT taxonomies
	 * 
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta	
	 */
	function register_taxonomy() {
		global $bp, $cgt;	
    foreach ($cgt->new_post_types_slug as $post_type) :
     
    
	 $labels_group_cat = array(
	    'name' => sprintf( __('%s Categories'), $cgt->new_group_types[$post_type][name] ),
	    'singular_name' => sprintf( __('%s Category'), $cgt->new_group_types[$post_type][singular_name] ),
	  ); 
	
	  $labels_group_tags = array(
	    'name' => sprintf( __('%s Tags'), $cgt->new_group_types[$post_type][name] ),
        'singular_name' => sprintf( __('%s Tag'), $cgt->new_group_types[$post_type][singular_name] ),
      ); 
      	
	  register_taxonomy($post_type.'_category',$post_type,array(
        'hierarchical' => true,
        'labels' => $labels_group_cat,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => $post_type.'_category' ),
      ));
      
      register_taxonomy($post_type.'_tag',$post_type,array(
	    'hierarchical' => false,
	    'labels' => $labels_group_tags,
        'show_ui' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array( 'slug' => $post_type.'_tag' ),
	  ));
      endforeach;
      
                        
   foreach($cgt->post_types as $post_type) :
      
      if($cgt->custom_field_attach_group[$post_type]){
       foreach($cgt->custom_field_attach_group[$post_type] as $key => $attached_group ){
             $labels_group_groups = array(
            'name' => sprintf( __('%s Categories'), $cgt->custom_field_name[$post_type][$key] ),
            'singular_name' => sprintf( __('%s Category'), $cgt->custom_field_name[$post_type][$key] ),
         ); 
        
        register_taxonomy($post_type . '_attached_' . $attached_group, $post_type,array(
            'hierarchical' => true,
            'labels' => $labels_group_groups,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => $post_type . '_attached_' . $attached_group ),
            'show_in_nav_menus' => false,
          ));
         
         
        }   
      }
     
   endforeach;
      	
   }

    function add_firmen(){
      global $cgt;
      if ( bp_has_groups('type=alphabetical') ) : while ( bp_groups() ) : bp_the_group(); 
            if ( bp_get_group_status() == ('public' || 'private')) { 
                if(groups_get_groupmeta( bp_get_group_id(), 'group_type' ) != ''){
                    
                       foreach($cgt->post_types as $post_type) :
      
                          if($cgt->custom_field_attach_group[$post_type]){
                           foreach($cgt->custom_field_attach_group[$post_type] as $key => $attached_group ){
                               if(groups_get_groupmeta( bp_get_group_id(), 'group_type' ) != $post_type) {
                                wp_set_object_terms( bp_get_group_id(), bp_get_group_name(), $post_type . '_attached_' . $attached_group);
                               }
                          
                            }   
                          }
                         
                       endforeach;
                    
                 }
            }
        endwhile; /* endif; */ 
        endif;
}

    // change the shop slug to groups slug to ceep it consitent 
    function remove_slug($permalink, $post, $leavename) {
        global $bp ,$cgt;
        
        $post_types = array_merge($cgt->existing_post_types_slug, $cgt->new_post_types_slug);
  
         foreach($post_types as $post_type){
             if($post_type)
                $permalink = str_replace(get_bloginfo('url') . '/'.$post_type , get_bloginfo('url') . '/groups', $permalink);
        }
       return $permalink;
    }
 
	/**
	 * Redirect a post to its group
	 * 
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta	
	 */
	function theme_redirect() {
	   global $wp, $wp_query, $bp, $post, $cgt;
	    $plugindir = dirname( __FILE__ );

		//A Specific Custom Post Type redirect to the atached group
		if (in_array( $wp->query_vars["post_type"], $cgt->existing_post_types ) || in_array( $wp->query_vars["post_type"], $cgt->new_post_types_slug )) {
    		if ( is_singular()) {
    			    $link = get_bloginfo('url').'/'.BP_GROUPS_SLUG.'/'.get_post_meta( $wp_query->post->ID, '_link_to_group', true );
    				if ( !$link )
    					return;
    			$redirect_type = '301';	
    			wp_redirect( $link, $redirect_type );
    			exit;
    		} else {
    		  
    		    foreach ($cgt->new_post_types_slug as $post_type) :
 
                    if($wp->query_vars["post_type"] == $post_type){
                        $templatefilename = 'page-'.$post_type.'.php';
                    if ( file_exists(STYLESHEETPATH . '/' . $templatefilename)) {
                        $return_template = STYLESHEETPATH . '/' . $templatefilename;
                    } else if ( file_exists(TEMPLATEPATH . '/' . $templatefilename) ) {
                        $return_template = TEMPLATEPATH . '/' . $templatefilename;
                    } else {
                        //$return_template = get_template_part( 'archive' );
                        $return_template = $plugindir . '/includes/templates/wp/taxonomy.php';
                    }     
                   BP_CGT::do_theme_redirect($return_template);  
                    }
 
            
                endforeach;
            
    		}
         // A custom Taxonomy Page	
	    } else {
    	    foreach ($cgt->new_post_types_slug as $post_type) :
              if ($wp->query_vars[$post_type.'_category']) {
                $templatefilename = 'taxonomy-'.$post_type.'_category.php';
                    
                if ( file_exists(STYLESHEETPATH . '/' . $templatefilename)) {
                    $return_template = STYLESHEETPATH . '/' . $templatefilename;
                } else if ( file_exists(TEMPLATEPATH . '/' . $templatefilename) ) {
                    $return_template = TEMPLATEPATH . '/' . $templatefilename;
                } else {
                    //  $return_template =  get_template_part( 'archive' );
                    $return_template = $plugindir . '/includes/templates/wp/taxonomy.php';
                }
                BP_CGT::do_theme_redirect($return_template);
        
              }        
            endforeach;
       }
   }

	function do_theme_redirect($url) {
	    global $post, $wp_query;
	    if (have_posts()) {
	        include($url);
	        die();
	    } else {
	        $wp_query->is_404 = true;
	    }
	}	
	
}
?>