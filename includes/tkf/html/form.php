<?php

class TK_Form extends TK_HTML{
	
	var $id;
	var $action;
	var $name;
	var $method;
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $action The action of the form
	 * @param array $args Array of [ $method form method, $action form action ]
	 */
	function tk_form( $id, $args ){
		$this->__construct( $action, $args );
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $action The action of the form
	 * @param array $args Array of [ $method form method, $action form action ]
	 */
	function __construct( $id, $args ){
		parent::__construct();
		
		$defaults = array(
			'method' => 'post',
			'action' => esc_url( $_SERVER['REQUEST_URI'] ),
			'name' => $id
		);
		$args = wp_parse_args($args, $defaults);
		extract( $args, EXTR_SKIP );
		
		$this->action = $action;
		$this->method = $method;
		$this->name = $name;
		
		$this->id = $id;
	}
	
	/**
	 * Getting the form html
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @return string $html The form content
	 * 
	 */
	function get_html(){
		// Adding method to the form
		$method_str = ' method="' . $this->method . '"';
		
		$id_str = '';
		$name_str = '';
		
		if( $this->id != '' ) $id_str = ' id="' . $this->id . '"';
		if( $this->name != '' ) $name_str = ' name="' . $this->name . '"';
		
		$html = '<form' . $id_str . $method_str . ' action="' . $this->action . '"' . $name_str . ' enctype="multipart/form-data">';
		
		$html = apply_filters( 'tk_form_start_' . $this->id, $html );
		
		// Adding elements to form
		foreach( $this->elements AS $element ){
			$tkdb = new TK_Display();
			$html.= $tkdb->get_html( $element );
			unset( $tkdb );
		}
		
		$html = apply_filters( 'tk_form_end_' . $this->id, $html );
				
		$html.='</form>';
		
		return $html;
	}
}