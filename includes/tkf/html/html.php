<?php
class TK_HTML{
	
	var $elements;
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 */
	function tk_html(){
		$this->__construct();
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 */
	function __construct(){
		$this->elements = array();
	}
	
	/**
	 * Adding elements to content
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $element Element which have to be added to content
	 * 
	 */
	function add_element( $element ){
		array_push( $this->elements, $element  );
	}
	
	/**
	 * Getting the content
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @return string $html The whole content
	 * 
	 */
	function get_html(){
		if( count( $this->elements ) > 0 ){
			foreach( $this->elements AS $element ){
				$html.= $element;
			}
		}
		return $html;
	}
	
	/**
	 * Echo the content
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 */
	function write_html(){
		echo $this->get_html();	
	}
}