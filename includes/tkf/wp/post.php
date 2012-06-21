<?php

class TK_WP_Post{
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 */
	function tk_wp_post( $id = '' , $args = array() ){
		$this->__construct( $id, $args );
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 */
	function __construct( $id = '' , $args = array() ){
		$defaults = array(
			'title_tag' => 'h3'
		);
		
		$parsed_args = wp_parse_args($args, $defaults);
		extract( $parsed_args , EXTR_SKIP );
		
		parent::__construct();
		
		$this->id = $id;
		$this->title_tag = $title_tag;
	}
	
	function get_html(){
		
	}
}