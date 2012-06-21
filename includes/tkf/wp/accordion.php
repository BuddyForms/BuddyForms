<?php

class TK_Jqueryui_Accordion extends TK_HTML{
	
	var $id;
	var $title_tag;
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 */
	function tk_jqueryui_accordion( $id = '' , $args = array() ){
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
		
		$args = wp_parse_args($args, $defaults);
		extract( $args , EXTR_SKIP );
		
		parent::__construct();
		
		$this->id = $id;
		$this->title_tag = $title_tag;
	}
	
	/**
	 * Adding section to accordion
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $id Id of the section
	 * @param string $title Title of the section
	 * @param array $args Array of [ $extra_title Extra title code, $extra_content Extra content code ]
	 */
	function add_section( $id, $title, $content, $args = array() ){
		$defaults = array(
			'css_class' => '',
			'style' => '',
			'extra_title' => '',
			'extra_content' => '',
			
		);
		
		$args = wp_parse_args($args, $defaults);
		extract( $args , EXTR_SKIP );
		
		$element = array( 'id'=> $id, 'title' => $title, 'extra_title' => $extra_title,  'content' => $content, 'css_class'=> $css_class, 'style'=> $style, 'extra_content' => $extra_content );
		$this->add_element( $element );
		
	}

	/**
	 * Getting the accordion html
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @return string $html The accordion as html
	 * 
	 */
	function get_html( $hide_element = FALSE ){
		global $tk_hidden_elements;
		
		// Creating elements
		if( !in_array( $this->id, $tk_hidden_elements ) && !$hide_element ){
			
			if( $this->id == '' ){
				$id = md5( rand() );
			}else{
				$id = $this->id;
			}
		
			$html = '<script type="text/javascript">
			jQuery(document).ready(function($){
					
				var cookieName = "stickyAccordion_' . $id . '";
				
				$( ".' . $id . '" ).accordion({
					header: "' . $this->title_tag . '", 
					autoHeight: false, 
					collapsible:true,
					active: ( $.cookies.get( cookieName ) || 0 ),
					change: function( e, ui )
					{
						$.cookies.set( cookieName, $( this ).find( "' . $this->title_tag . '" ).index ( ui.newHeader[0] ) );
					}
				});
					
			});
	   		</script>';
			
			if( $this->id != '' ) $html = apply_filters( 'tk_jqueryui_accordion_before_' . $this->id , $html );
			
			$html.= '<div class="' . $id . '">';
			
			foreach( $this->elements AS $element ){
				
				if( !in_array( $element['id'], $tk_hidden_elements ) ){
					if( $element['id'] == '' ){	$element_id = md5( $element['title'] ); }else{	$element_id = $element['id']; }
					
					$html.= '<' . $this->title_tag . ' ' . $element['extra_title']  . ' class="'.$element['css_class'].'" style="'.$element['style'].'"><a href="#">';
					
					if( is_object( $element['title'] ) ){
						 $html.= $element['title']->get_html();
					}else{
						 $html.= $element['title'];
					}
					
					$html.= '</a></' . $this->title_tag . '>';
					$html.= '<div id="' . $element['id'] . '"' . $element['extra_content']  . ' class="'.$element['css_class'].'">';
					
					if( $this->id != '' ) $html = apply_filters( 'tk_jqueryui_accordion_content_section_before_' . $this->id , $html );
					if( $element['id'] != '' ) $html = apply_filters( 'tk_jqueryui_accordion_content_section_before_' . $element['id'], $html );
				
					$tkdb = new TK_Display();
					$html.= $tkdb->get_html( $element['content'] );
					unset( $tkdb );
					
					
					if( $this->id != '' ) $html = apply_filters( 'tk_jqueryui_accordion_content_section_after_' . $this->id , $html );
					if( $element['id'] != '' ) $html = apply_filters( 'tk_jqueryui_accordion_content_section_after_' . $element['id'], $html );
		
					$html.= '</div>';
					
				}else{
					$tkdb = new TK_Display();
					$html.= $tkdb->get_html( $element['content'], TRUE );
					unset( $tkdb );
				}
			}
			
			$html.= '</div>';
			
		// Hiding elements
		}else{
			foreach( $this->elements AS $element ){
				$tkdb = new TK_Display();
				$html.= $tkdb->get_html( $element['content'], TRUE );
				unset( $tkdb );
			}
		}
		
		if( $this->id != '' ) $html = apply_filters( 'tk_jqueryui_accordion_after_' . $this->id , $html );
		
		return $html;
	}

	function get_xml(){
		return get_object_vars( $this );
	}
	
}
function tk_accordion( $id, $elements, $return_object = FALSE ){
	$accordion = new TK_Jqueryui_Accordion( $id );
	
	foreach ( $elements AS $element ){
		
		if( !isset( $element['extra_title'] ) )
			$element['extra_title'] = '';
		
		if( !isset( $element['extra_content'] ) )
			$element['extra_content'] = '';
		
		$args = array(
			'css_class' => $element['css_class'],
			'style' => $element['style'],
			'extra_title' => $element['extra_title'],
			'extra_content' => $element['extra_content']
		);
		
		$accordion->add_section( $element['id'], $element['title'], $element['content'], $args );
	}
	
	if( TRUE == $return_object ){
		return $accordion;
	}else{
		return $accordion->get_html();
	}	
}