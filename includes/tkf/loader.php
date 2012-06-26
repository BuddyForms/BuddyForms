<?php
/*
 * Loader script for the themekraft framework
 * Just include this script to load the framework
 */

// If Framework in same version is not existing
if( !function_exists( 'tkf_init_010' ) ){
	global $tkf_version;
	
	$this_tkf_version = '0.1.0';
	
	// Initialize function of this version which have to be have hooked
	function tkf_init_010(){
		require_once( 'core.php' );
	}
	
	// If there is already a framework started
	if( $tkf_version != '' ){
		
		// If started framework version is older than this version, remove action from init actionhook
		if( version_compare( $tkf_version, $this_tkf_version, '<' ) ){
			$function_name = 'tkf_init_' . str_replace( '.', '', $tkf_version );
			
			// Removing functions from init actionhook 
			if( has_action( 'after_setup_theme', $function_name ) ){
				remove_action( $tag, $function_name );
			}
			
			// Add own action to actionhook
			$tkf_version = $this_tkf_version;
			add_action( 'after_setup_theme', 'tkf_init_' . str_replace( '.', '', $this_tkf_version ), 1 );
		}
	}else{
		// Add own action to actionhook
		$tkf_version = $this_tkf_version;
		add_action( 'after_setup_theme', 'tkf_init_' . str_replace( '.', '', $this_tkf_version ), 1 );
	}
	
	function tk_framework( $args = array()  ){
		global $tkf_text_domain, $tkf_text_domain_path, $tkf_text_domain_strings, $tkf_create_textfiles, $tk_hidden_elements, $tk_autocomplete_elements, $tkf_metabox_ids, $tkf_hide_class, $tkf_hide_class_options;
		
		$tk_hidden_elements = array();
		$tk_select_option_elements = array();
		$tk_autocomplete_elements = array();
		$tkf_metabox_ids = array();
		$tkf_hide_class = array();
		$tkf_hide_class_options = array();
		
		$defaults = array(
			'jqueryui_components' => array( 'jquery-cookies', 'jquery-fileuploader', 'jquery-ui-tabs', 'jquery-ui-accordion', 'jquery-colorpicker', 'jquery-ui-autocomplete', 'jquery-linedtextarea', 'jquery-autogrow-textarea', 'jquery-sheepit', 'jquery-tablednd', 'google-fonts' ),
			'forms' => array(),
			'text_domain' => '',
			'text_domain_path' => '/lang'
		);
	
		$args = wp_parse_args($args, $defaults);
		extract( $args , EXTR_SKIP );
		
		
		if( count( $forms ) > 0  ){
			global $tk_option_groups;
			if( !is_array( $tk_option_groups ) )
				$tk_option_groups = array();
			$tk_option_groups = array_merge( $tk_option_groups, $forms );
		}
		
		if( count( $jqueryui_components ) > 0  ){
			global $tk_jqueryui_components;
			$tk_jqueryui_components = $jqueryui_components;
		}
		
		$tkf_create_textfiles = FALSE;
		
		if( $text_domain != '' ){
			$tkf_text_domain = $text_domain;
			$tkf_text_domain_strings = array();
			
			if( $text_domain_path != '' ){
				$tkf_text_domain_path = $text_domain_path;
				load_plugin_textdomain( $text_domain, false, dirname( plugin_basename( __FILE__ ) ) . $text_domain_path );
			}
		}
		
		add_action( 'admin_init', 'tk_register_option_groups' ); // should not be here
		
		add_action( 'after_setup_theme', 'tk_load_framework', 1 );
		
		add_action( 'admin_head', 'tk_load_jqueryui', 10 );
		
	}
	
	function tk_register_option_groups(){
		global $tk_option_groups;
		
		if( count( $tk_option_groups ) > 0){
			foreach( $tk_option_groups AS $option_group ){
				tk_register_wp_option_group( $option_group );
			}
		}
	}
}