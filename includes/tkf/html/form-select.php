<?php

class TK_Form_select extends TK_Form_element{
	
	var $extra;
	var $elements;
	var $size;
	var $before_element;
	var $after_element;
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param array $args Array of [ $id Id, $name Name, $value Value, $extra Extra selectfield code ]
	 */
	function tk_form_select( $args ){
		$this->__construct( $args );
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param array $args Array of [ $id Id, $name Name, $value Value, $extra Extra selectfield code ]
	 */
	function __construct( $args ){
		$defaults = array(
			'id' => '',
			'name' => '',
			'value' => '',
			'onchange' => '',
			'size' => '',
			'multiselect' => FALSE,
			'extra' => '',
			'elements' => ''
		);
		
		$args = wp_parse_args($args, $defaults);
		extract( $args , EXTR_SKIP );
		
		parent::__construct( $args );
		
		$this->size = $size;	
		$this->multi_select = $multi_select;
		$this->onchange = $onchange;
		
		if( $this->onchange != '' ) $this->str_onchange = '  onchange="' . $this->onchange . '"';
		if( $this->size != '' ) $this->str_size = ' size="' . $this->size . '"';
	}
	
	/**
	 * Adds an option to the select field
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $option The option to show in list
	 * @param array $args Array of [ $value Value, $extra Extra option code ]
	 */
	function add_option( $value, $args = array() ){
		$defaults = array(
			'id' => '',
			'option_name' => '',
			'extra' => ''
		);
		
		$parsed_args = wp_parse_args( $args, $defaults );
		extract( $parsed_args , EXTR_SKIP );
		
		$this->elements[ $value ] = array( 'id' => $id, 'value'=> $value, 'option_name' => $option_name, 'extra' => $extra );
	}
	
	/**
	 * Getting HTML of select box
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @return string $html The HTML of select box
	 */
	function get_html(){
		global $tk_hidden_elements;
		
		// Merging values
		$this->merge_option_elements();
		
		if( $this->multiselect ):
			if( $this->name != '' ) $name_string = ' name="' . $this->name . '[]"';
			$multiselect_string = ' multiple="multiple"';
		else:
			if( $this->name != '' ) $name_string = ' name="' . $this->name . '"';
		endif;
		
		$html = $this->before_element;

		$html.= '<select' . $this->str_id . $this->str_name . $this->str_size . $this->str_onchange  . $this->str_css_classes . $multiselect_string . $this->extra . '>';
		
		// Adding options
		$options = '';

		if( count( $this->elements ) > 0 ){
			
			foreach( $this->elements AS $value => $element ){
				
				if( !in_array( $element['id'], $tk_hidden_elements ) ):
					
					/*
					echo '<pre>';
					print_r( $element );
					echo '</pre>';
					 */
					
					$option_name = $element['option_name'];
					$value_string = ' value="' . $value . '"';
					$extra_string = $element['extra'];
					
					if( $option_name == '' )
						$option_name = $value;
					
					if( is_array( $this->value ) ):
						// If value is from a multiselect box
						if( in_array( $value, $this->value ) ):
							$options .=  '<option' . $value_string . ' selected' . $extra_string . '>' . $option_name . '</option>';
						else:
							$options .=  '<option' . $value_string . $extra_string . '>' . $option_name . '</option>';
						endif;					
						
						
					else:
						// Standard value
						if( $this->value == $value && $value != '' ):
							$options .=  '<option' . $value_string . ' selected' . $extra_string . '>' . $option_name . '</option>';
						else:
							$options .=  '<option' . $value_string . $extra_string . '>' . $option_name . '</option>';
						endif;
						
					endif;
				endif;
				// No else because is only option in it
			}

		}
		
		$options = apply_filters( 'tk_select_options_' . $this->id, $options, $this->id );

		$html.= $options . '</select>';
		$html.= $this->after_element;
		
		return $html;
	}

	function merge_option_elements(){
		
		global $tk_select_option_elements;
		
		if( !isset( $tk_select_option_elements[ $this->id ]  ) )
			return false;
		
		if( is_array( $tk_select_option_elements[ $this->id ] ) ){
			
			foreach( $tk_select_option_elements[ $this->id ] AS $element ){
				
				if( $element[ 'action' ] == 'add' )
					$this->elements[ $element[ 'value' ] ] = array( 'option_name' => $element[ 'option_name' ], 'extra' => $element[ 'extra' ] );
				
				if( $element[ 'action' ] == 'delete' )
					unset ( $this->elements[ $element[ 'value' ]  ] );
					
			}
		}

	}
	// $element = array( 'option_name' => $option_name, 'extra' => $extra );
}

function tk_select_add_option( $select_id, $value, $option_name = '', $extra = '' ){
	global $tk_select_option_elements;
	
	if( $option_name == '' )
		$option_name = $value;
	
	if( !is_array( $tk_select_option_elements[ $select_id ] ) )
		$tk_select_option_elements[ $select_id ] = array();
		
	array_push( $tk_select_option_elements[ $select_id ], array( 'action' => 'add' , 'value' => $value, 'option_name' => $option_name, 'extra' => $extra ) );
}

function tk_select_delete_option( $select_id, $value ){
	global $tk_select_option_elements;
	
	if( !is_array( $tk_select_option_elements[ $select_id ] ) )
		$tk_select_option_elements[ $select_id ] = array();
		
	array_push( $tk_select_option_elements[ $select_id ], array( 'action' => 'delete' , 'value' => $value ) );
}

function tk_select( $args, $return_object = FALSE ){
    $select = new TK_Form_select( $args );

    if( TRUE == $return_object ){
        return $select;
    }else{
        return $select->get_html();
    }   
}
