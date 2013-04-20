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
		add_action( 'template_redirect', 	array( $this, 'theme_redirect'			),  1, 2 );	
		add_action( 'bp_setup_globals',		array( $this, 'set_globals'				), 12, 1 );
        add_action( 'wp_enqueue_scripts', 	array( $this, 'enqueue_style'			), 10, 1 );
        add_action( 'widgets_init', 		array( $this, 'register_widgets'		), 10, 1 );

        add_filter( 'post_type_link', 		array( $this, 'remove_slug'					), 10, 3 );
		
 	}
	
	/**
	 * Defines cpt4bp_init action
	 *
	 * This action fires on WP's init action and provides a way for the rest of BuddyPress
	 * CPT4BP, as well as other dependent plugins, to hook into the loading process in an
	 * orderly fashion.
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	*/
	
	public function init_hook() {
		do_action( 'cpt4bp_init' );
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
	 * Includes files needed by BuddyPress CPT4BP
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	
	 
	public function includes() {
		require_once( CPT4BP_INCLUDES_PATH .'PFBC/Form.php' 		);	
	    require_once( CPT4BP_INCLUDES_PATH .'helper-functions.php' 		); 
		require_once( CPT4BP_INCLUDES_PATH .'templatetags.php' 		); 
		require_once( CPT4BP_INCLUDES_PATH .'member-extention.php' 		); 
		require_once( CPT4BP_INCLUDES_PATH .'group-control.php' 		); 
	
        require_once( CPT4BP_INCLUDES_PATH .'widgets/widget-apps.php' 		); 
        require_once( CPT4BP_INCLUDES_PATH .'widgets/widget-categories.php' ); 
        require_once( CPT4BP_INCLUDES_PATH .'widgets/widget-groups.php' 	); 
        require_once( CPT4BP_INCLUDES_PATH .'widgets/widget-product.php' 	); 
		
		if( is_admin() ) {
			require_once( CPT4BP_INCLUDES_PATH. 'admin.php' );
		}
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
	 * Loads the textdomain for the plugin
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */	 
	 
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'cpt4bp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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
 	 * Change the slug to groups slug to keep it consistent
	 * 
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta	
	 */
	public function remove_slug( $permalink, $post, $leavename ) {
        global $cpt4bp;
        
		$post_types =  $cpt4bp['selected_post_types'] ;
  
        foreach( $post_types as $post_type ){
             if( $post_type )
                $permalink = str_replace( get_bloginfo('url') .'/'. $post_type , get_bloginfo('url') .'/'.BP_GROUPS_SLUG, $permalink );
        }

		return $permalink;
    }
 
	function filter_template( $template ) {
		
	 	$template = cpt4bp_locate_template('bp/groups-home.php');
		load_template( apply_filters( 'bp_load_template', $template ) );
		exit;
		
	}
	 
	/**
	 * Redirect a post to its group
	 * 
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta	
	 */
	public function theme_redirect() {
	   global $wp_query, $cpt4bp, $bp;
	   
	    $plugindir = dirname( __FILE__ );

		if( $bp->current_component == BP_GROUPS_SLUG && $bp->current_action == 'home'){
			add_action( 'bp_core_pre_load_template', array( $this, 'filter_template' ));	
		}

		//A Specific Custom Post Type redirect to the atached group
		if( in_array( $wp_query->query_vars['post_type'], $cpt4bp['selected_post_types'] ) ) {
    		if( is_singular() ) {
				$link = get_bloginfo('url') .'/'. BP_GROUPS_SLUG .'/'. get_post_meta( $wp_query->post->ID, '_link_to_group', true );
    			wp_redirect( $link, '301' );
    			exit;
    		}
			
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