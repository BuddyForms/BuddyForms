<?php

class TK_Form_Element{
	
	var $id;
	var $name;
	var $value;
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param array $args Array of [ $id Id, $name Name, $value Value ]
	 */
	function tk_form_element( $args ){
		$this->__construct( $args );
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param array $args Array of [ $id Id, $name Name, $value Value ]
	 */
	function __construct( $args ){
		
		$defaults = array(
			'id' => '',
			'name' => '',
			'value' => ''
		);
		extract( wp_parse_args($args, $defaults), EXTR_SKIP );
		
		$this->id = $id;
		$this->name = $name;
		$this->value = $value;
	}
	
	/**
	 * Getting content ( empty function )
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 */
	function get_html(){
	}
	
	/**
	 * Echo content
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 */
	function write_html(){
		echo $this->get_html();		
	}
}