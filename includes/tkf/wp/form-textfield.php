<?php

class TK_WP_Form_Textfield extends TK_Form_Textfield{
	
	var $option_group;
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $name Name of textfield
	 * @param array $args Array of [ $id , $value,  $extra Extra textfield code   ]
	 */
	function tk_wp_form_textfield( $name, $args = array() ){
		$this->__construct( $name, $args );
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $name Name of textfield
	 * @param array $args Array of [ $id,  $extra Extra textfield code, $option_group Name of optiongroup where textfield have to be saved   ]
	 */
	function __construct( $name, $args = array() ){
		global $post, $tk_form_instance_option_group;
		
		$defaults = array(
			'id' => '',
			'extra' => '',
			'default_value' => '',
			'option_group' => $tk_form_instance_option_group,
			'multi_index' => '',
			'before_element' => '',
			'after_element' => ''
		);
		
		$parsed_args = wp_parse_args( $args, $defaults );
		extract( $parsed_args , EXTR_SKIP );
		
		$field_name = tk_get_field_name( $name, array( 'option_group' => $option_group, 'multi_index' => $multi_index ) );
		$value = tk_get_value( $name, array( 'option_group' => $option_group, 'multi_index' => $multi_index, 'default_value' => $default_value ) );
		
		$args['name'] = $field_name;
		$args['value'] = $value;

		parent::__construct( $args );
	}

	function get_html(){
		$this->field_name_set = TRUE;
		return parent::get_html();
	}

}

function tk_form_textfield( $name, $args = array(), $return_object = FALSE ){
	$textfield = new TK_WP_Form_Textfield( $name, $args );
		
	if( TRUE == $return_object ){
		return $textfield;
	}else{
		return $textfield->get_html();
	}
}