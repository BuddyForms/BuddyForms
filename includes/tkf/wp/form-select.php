<?php 

class TK_WP_Form_Select extends TK_Form_Select{
	
	var $option_group;
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $name Name of select field
	 * @param array $args Array of [ $id , $extra Extra select field code   ]
	 */
	function tk_wp_form_select( $name, $args = array() ){
		$this->__construct( $name, $args );
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $name Name of select field
	 * @param array $args Array of [ $id , $extra Extra select field code, $option_group Name of optiongroup where select field have to be saved ]
	 */
	function __construct( $name, $args = array() ){
		global $post, $tk_form_instance_option_group;
		
		$defaults = array(
			'id' => '',
			'extra' => '',
			'default_value' => '',
			'size' => '',
			'multiselect' => FALSE,
			'option_group' => $tk_form_instance_option_group,
			'before_element' => '',
			'after_element' => ''
		);
		
		$parsed_args = wp_parse_args( $args, $defaults);
		extract( $parsed_args , EXTR_SKIP );
		
		$field_name = tk_get_field_name( $name, array( 'option_group' => $option_group, 'multi_index' => $multi_index ) );
		$value = tk_get_value( $name, array( 'option_group' => $option_group, 'multi_index' => $multi_index, 'default_value' => $default_value ) );
		
		$parsed_args['name'] = $field_name;
		$parsed_args['value'] = $value;
		
		parent::__construct( $parsed_args );

	}			
}

function tk_form_select( $name, $options, $args = array(), $return_object = FALSE ){
	$select = new TK_WP_Form_Select( $name, $args );
	
	if( is_array( $options ) ){
		foreach ( $options AS $option ){
			if( !is_array( $option) ){
				$select->add_option( $option );
			}else{
				if( !isset( $option['id'] ) )
					$option['id'] = '';
					
				if( !isset( $option['name'] ) )
					$option['name'] = '';
				
				if( !isset( $option['option_name'] ) )
					$option['option_name']  = '';
				
				if( !isset( $option['extra'] ) )
					$option['extra']  = '';
				
				if( !isset( $option['value'] ) )
					$option['value']  = '';
				
				$args = array(
					'id'=> $option['id'],
					'option_name' => $option['option_name'],
					'extra' => $option['extra']
				);
				$select->add_option( $option['value'], $args );
			}
		}
	}
	
	if( TRUE == $return_object ){
		return $select;
	}else{
		return $select->get_html();
	}
}