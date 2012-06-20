<?php

class TK_Form_Button extends TK_Form_Element{
	var $extra;
	var $submit;
	var $before_element;
	var $after_element;	
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param array $args Array of [ $id Id, $name Name, $value Value, $submit use submit, $extra Extra checkbox code   ]
	 */
	function tk_form_button( $value, $args = array() ){
		$this->__construct( $value, $args );
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param array $args Array of [ $id Id, $name Name, $value Value, $submit use submit, $extra Extra checkbox code   ]
	 */
	function __construct( $value, $args = array() ){
		$defaults = array(
			'id' => '',
			'name' => '',
			'submit' => true,
			'extra' => '',
			'before_element' => '',
			'after_element' => ''
		);
		
		$args = wp_parse_args($args, $defaults);
		extract( $args , EXTR_SKIP );
		
		$args['name'] = $name;	
		$args['value'] = $value;	
		parent::__construct( $args );
		
		$this->submit = $submit;
		$this->extra = $extra;
		
		$this->before_element = $before_element;
		$this->after_element = $after_element;	
	}
	
	/**
	 * Getting HTML of button
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @return string $html The HTML of button
	 */
	function get_html(){
		$id = '';
		$name = '';
		$value = '';
		$extra = '';
		
		if( $this->id != '' ) $id = ' id="' . $this->id . '"';
		if( $this->name != '' ) $name = ' name="' . $this->name . '"';
		if( $this->value != '' ) $value = ' value="' . $this->value . '"';
		if( $this->extra != '' ) $extra = $this->extra;
		
		$html = $this->before_element;
		if( $this->submit ){
			$html.= '<input type="submit"' . $id . $name . $value . $extra . ' />';
		}else{
			$html.= '<input type="button"' . $id . $name . $value . $extra . ' />';
		}
		$html.= $this->after_element;
		
		return $html;
	}
}

function tk_button( $value, $args = array(), $return_object = FALSE ){
	$button = new TK_Form_Button( $value, $args );
	
	if( TRUE == $return_object ){
		return $button;
	}else{
		return $button->get_html();
	}
}