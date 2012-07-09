<?php

class TK_Jqueryui{
	
	var $jqueryui;
	var $wp_components;
	var $known_components;
	var $enqueued_components;
	var $depencies;
	var $wp_version;
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 */
	function tk_jqueryui(){
		$this->__construct();
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 */
	function __construct(){
		$this->wp_version = $GLOBALS['wp_version'];
		$this->known_components = array();
		$this->wp_components = array();
		$this->enqueued_components = array();
		$this->init_known_jqueryui_components();
		
		$this->register_components();
		
	}
	
	/**
	 * jQueryui loader
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param array $components Array with the component names
	 * @param array $args Array of [ $css Put on false if no css should be included ]
	 */
	function load_jqueryui( $components = array (), $args = array() ){
		if( count( $components ) == 0 ){
			$components = array_merge( $this->known_components, $this->wp_components );
		}
		
		$defaults = array(
			'css' => 'true'
		);
		
		$args = wp_parse_args($args, $defaults);
		extract( $args , EXTR_SKIP );
		
		if( defined( 'BP_VERSION' ) && in_array( 'jquery-ui-accordion', $components ) ){
			wp_deregister_script( 'dtheme-ajax-js' ); // For Buddypress bug on accordion
		}
		
		// loading jQuery core
		wp_enqueue_script( 'jquery-ui' );
		
		if( $css ){
			wp_enqueue_style( 'jquery-ui-css', TKF_URL . '/includes/css/jquery-ui.css' );
			wp_enqueue_style( 'jquery-colorpicker-css', TKF_URL . '/includes/css/colorpicker.css' );
			wp_enqueue_style( 'linedtextarea', TKF_URL . '/includes/css/jquery-linedtextarea.css' );
		    wp_enqueue_style( 'tkf-css', TKF_URL . '/includes/css/tkf.css' );
            wp_enqueue_style( 'jquery-tablednd', TKF_URL . '/includes/css/tablednd.css' );
        	wp_enqueue_style( 'thickbox' );
		}
		
		$jqueryui_url = '';
		
		foreach( $components AS $component ){

			if( isset( $this->jqueryui[ $component ]['url'] ) ){
				$jqueryui_url = $this->jqueryui[ $component ]['url'];
			}
			if( isset( $this->jqueryui[ $component ]['version'] ) ){	
				$jqueryui_version = $this->jqueryui[ $component ]['version'];
			}
		
			
			if( isset( $this->depencies[ $component ] ) ){
				if( count( $this->depencies[ $component ] ) > 0 ){
					foreach( $this->depencies[ $component ] AS $required_component ){
						
						if( in_array( $required_component, $this->known_components) && !in_array( $required_component,  $this->enqueued_components ) ){
							$this->add_enqueued_jqueryui_component( $required_component );
							wp_enqueue_script( $required_component );
						}
					}
				}
			}
			
			if( !in_array( $component,  $this->wp_components ) ){
				wp_register_script( $component, $jqueryui_url, array( 'jquery' ) , $jqueryui_version, true );
			}
						
			if( !in_array( $component,  $this->enqueued_components ) ){
				$this->add_enqueued_jqueryui_component( $component );
				wp_enqueue_script( $component );
			}
		}
	} 
	
	/**
	 * Getting jQueryui component ???
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param array $components Array with the component names
	 * @param array $args Array of [ $css Put on false if no css should be included ]
	 */
	function get_jqueryui_component( $component_name ){
		$url = $this->jqueryui[ $component_name ]['url'];
	}
	
	/**
	 * Adding jQueryui component
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param array $components Array with the component names
	 * @param array $args Array of [ $css Put on false if no css should be included ]
	 */
	function add_jqueryui_component( $component_name, $jqueryui_component_url, $jqueryui_version = '' ){
		// if( !in_array( $component_name, $this->known_components ) ) {
			$this->jqueryui[ $component_name ]['url'] = $jqueryui_component_url;
			
			if( $jqueryui_version != '' ){
				$this->jqueryui[ $component_name ]['version'] = $jqueryui_version;
			}
			
			$this->add_known_jqueryui_component( $component_name );
			
			return TRUE;
	}
	
	function add_depency( $component_name, $components = array() ){
		$this->depencies[ $component_name ] =  $components;
	}
	
	function register_components(){
		$this->add_jqueryui_component( 'jquery-cookies', TKF_URL . '/includes/js/jquery/jquery.cookies.js', '2.2.0' );
		$this->add_jqueryui_component( 'jquery-colorpicker', TKF_URL . '/includes/js/jquery/colorpicker.js', '1.8.16' );
		$this->add_jqueryui_component( 'jquery-fileuploader', TKF_URL . '/includes/js/jquery/fileuploader.js', '1.8.16' );
		$this->add_jqueryui_component( 'jquery-linedtextarea', TKF_URL . '/includes/js/jquery/jquery-linedtextarea.js', '1.3.2' );
		$this->add_jqueryui_component( 'jquery-autogrow-textarea', TKF_URL . '/includes/js/jquery/jquery.elastic.source.js', '1.6.11' );
		$this->add_jqueryui_component( 'jquery-sheepit', TKF_URL . '/includes/js/jquery/jquery.sheepit.js', '1.4' );
        $this->add_jqueryui_component( 'jquery-tablednd', TKF_URL . '/includes/js/jquery/jquery.tablednd.0.7.min.js', '1.7' );
        $this->add_jqueryui_component( 'google-fonts', TKF_URL . '/includes/js/jquery/google-fonts.js', '1.4' );
		
		$this->add_depency( 'jquery-cookies', array( 'jquery' ) );
		$this->add_depency( 'jquery-colorpicker', array( 'jquery-color' ) );
		$this->add_depency( 'jquery-fileuploader', array( 'jquery', 'media-upload', 'thickbox' ) );			
		$this->add_depency( 'jquery-linedtextarea', array( 'jquery' ) );			
		$this->add_depency( 'jquery-autogrow-textarea', array( 'jquery', 'jquery-ui' ) );
	    $this->add_depency( 'jquery-sheepit', array( 'jquery', 'jquery-ui' ) );
        $this->add_depency( 'jquery-tablednd', array( 'jquery', 'jquery-ui' ) );
    	$this->add_depency( 'google-fonts', array( 'jquery', 'jquery-ui' ) );
	}
	
	function init_known_jqueryui_components(){
		$this->add_wp_jqueryui_component( 'jquery' );
		$this->add_wp_jqueryui_component( 'jquery-ui-accordion' );
		$this->add_wp_jqueryui_component( 'jquery-ui-autocomplete' );
		$this->add_wp_jqueryui_component( 'jquery-ui-button' );
		$this->add_wp_jqueryui_component( 'jquery-ui-core' );
		$this->add_wp_jqueryui_component( 'jquery-ui-datepicker' );
		$this->add_wp_jqueryui_component( 'jquery-ui-dialog' );
		$this->add_wp_jqueryui_component( 'jquery-ui-draggable' );
		$this->add_wp_jqueryui_component( 'jquery-ui-droppable' );
		$this->add_wp_jqueryui_component( 'jquery-ui-mouse' );
		$this->add_wp_jqueryui_component( 'jquery-ui-position' );
		$this->add_wp_jqueryui_component( 'jquery-ui-progressbar' );
		$this->add_wp_jqueryui_component( 'jquery-ui-resizable' );
		$this->add_wp_jqueryui_component( 'jquery-ui-selectable' );
		$this->add_wp_jqueryui_component( 'jquery-ui-slider' );
		$this->add_wp_jqueryui_component( 'jquery-ui-sortable' );
		$this->add_wp_jqueryui_component( 'jquery-ui-tabs' );
		$this->add_wp_jqueryui_component( 'jquery-ui-widget' );
		
		$this->add_wp_jqueryui_component( 'media-upload' );
		$this->add_wp_jqueryui_component( 'thickbox' );
	}
	
	function add_known_jqueryui_component( $component_name ){
		array_push( $this->known_components, $component_name );
	}
	
	function add_wp_jqueryui_component( $component_name ){
		array_push( $this->wp_components, $component_name );
		$this->add_known_jqueryui_component( $component_name );
	}
	function add_enqueued_jqueryui_component( $component_name ){
		array_push( $this->enqueued_components, $component_name );
	}	
}

function tk_jqueryui( $components = array() ){
	$tk_jquery_ui = new TK_Jqueryui();
	$tk_jquery_ui->load_jqueryui( $components  );
}

function tk_load_jqueryui(){
	global $tk_jqueryui_components;
	tk_jqueryui( $tk_jqueryui_components );
}