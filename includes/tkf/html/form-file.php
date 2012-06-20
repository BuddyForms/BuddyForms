<?php

class TK_Fileuploader{
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $name Name of colorfield
	 * @param array $args Array of [ $id , $extra Extra colorfield code, option_groupOption group to save data, $before_textfield Code before colorfield, $after_textfield Code after colorfield   ]
	 */
	function tk_fileuploader( $name, $args = array() ){
		$this->__construct( $name, $args );
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $name Name of colorfield
	 * @param array $args Array of [ $id , $extra Extra colorfield code, option_groupOption group to save data, $before_textfield Code before colorfield, $after_textfield Code after colorfield   ]
	 */
	function __construct( $name, $args = array() ){
		$defaults = array(
			'id' => substr( md5 ( time() * rand() ), 0, 10 ),
			'extra' => '',
			'before_element' => '',
			'after_element' => '',
		);
		
		$args = wp_parse_args( $args, $defaults );
		extract( $args , EXTR_SKIP );

		$this->name = $name;
		
		$this->extra = $extra;
		
		$this->before_element = $before_element;
		$this->after_element = $after_element;

	}
	
	function get_html(){
		$id = '';
		$name = '';
		$extra = '';
		
		if( $this->id != '' ) $id = ' id="' . $this->id . '"';
		if( $this->name != '' ) $name = ' name="' . $this->name . '"';
		if( $this->extra != '' ) $extra = $this->extra;
		
		$html = $this->before_element;
		$html.= '<input' . $id . $name . $extra . ' type="file" />';
		$html.= $this->after_element;
		
		return $html;
	}
}