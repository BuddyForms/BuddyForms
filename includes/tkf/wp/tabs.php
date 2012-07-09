<?php

class TK_Jqueryui_Tabs extends TK_HTML{
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 */
	function tk_jqueryui_tabs( $id = '' ){
		$this->__construct( $id );
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 */
	function __construct( $id = '' ){
		parent::__construct();
		$this->id = $id;
	}
	
	/**
	 * Adding tab
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $id Id of the tab
	 * @param string $title Title of the tab
	 * @param string $content Content which appears in the tab
	 * 
	 */
	function add_tab( $id = '', $title = '', $content = '' ){
		$element = array( 'id'=> $id, 'title' => $title, 'content' => $content );
		$this->add_element( $element );
	}
	
	/**
	 * Getting the tabs html
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @return string $html The tabs as html
	 * 
	 */
	function get_html(){
		global $tk_hidden_elements;
		
		// Creating elements
		if( !in_array( $this->id, $tk_hidden_elements ) ){
			if( $this->id == '' ){
				$id = md5( rand() );
			}else{
				$id = $this->id;
			}
			
			$html = '<script type="text/javascript">
			jQuery(document).ready(function($){
				var cookieName = "stickyTabs_' . $id . '";
				$( ".' . $id . '" ).tabs({
					selected: ( $.cookies.get( cookieName ) || 0 ),
					show: function(event, ui) {
						$.cookies.set( cookieName, $( ".' . $id . '" ).tabs( "option", "selected" ) );
					}
				});
			});
	   		</script>';
			
			
			$html.= '<div id="' . $id . '" class="' . $id . '">';
			
			$html.= '<ul>';
			
			if( $this->id != '' ) $html = apply_filters( 'tk_wp_jqueryui_tabs_before_tabs_' . $id, $html );
			
			// Creting navigation elements
			foreach( $this->elements AS $element ){
					// Show tab
					if( !in_array( $element['id'], $tk_hidden_elements ) ){
						if( $element['id'] == '' ){	$element_id = md5( $element['title'] ); }else{	$element_id = $element['id']; }
									
						if( $element['id'] != '' ) $html = apply_filters( 'tk_wp_jqueryui_tabs_tabs_before_li_' . $element['id'], $html );
						$html.= '<li><a href="#' . $element_id . '" >';
						if( $element['id'] != '' ) $html = apply_filters( 'tk_wp_jqueryui_tabs_tabs_title_before_' .$element['id'], $html );
					
						if( is_object( $element['title'] ) ){
							 $html.= $element['title']->get_html();
						}else{
							 $html.= $element['title'];
						}
						
						if( $element['id'] != '' ) $html = apply_filters( 'tk_wp_jqueryui_tabs_tabs_title_after_' . $element['id'], $html );
						$html.= '</a></li>';
						if( $element['id'] != '' ) $html = apply_filters( 'tk_wp_jqueryui_tabs_tabs_after_li_' . $element['id'], $html );
					}
			}
			
			if( $this->id != '' ) $html = apply_filters( 'tk_wp_jqueryui_tabs_after_tabs_' . $id, $html );
			
			$html.= '</ul>';
			
			// Creting content elements
			foreach( $this->elements AS $element ){
				// Show tab content
				if( !in_array( $element['id'], $tk_hidden_elements ) ){
					if( $element['id'] == '' ){	$element_id = md5( $element['title'] ); }else{	$element_id = $element['id']; }
					
					$html.= '<div id="' . $element_id . '" >';
					if( $element['id'] != '' ) $html = apply_filters( 'tk_wp_jqueryui_tabs_before_content_' . $element['id'], $html );
					
					$tkdb = new TK_Display();
					$html.= $tkdb->get_html( $element['content'] );
					unset( $tkdb );
					
					if( $element['id'] != '' ) $html = apply_filters( 'tk_wp_jqueryui_tabs_after_content_' . $element['id'], $html );
					$html.= '</div>';
					
				// Hide tab content
				}else{
					$tkdb = new TK_Display();
					$html.= $tkdb->get_html( $element['content'], TRUE );
					unset( $tkdb );
				}
			}
			
			$html.= '</div>';
			
			return $html;
			
		// Hiding elements
		}else{
			foreach( $this->elements AS $element ){
				$tkdb = new TK_Display();
				$html.= $tkdb->get_html( $element['content'], TRUE );
				unset( $tkdb );
			}
		}
	}
}
function tk_tabs( $id = '', $elements = array(), $return_object = FALSE ){	
	$tabs = new	TK_Jqueryui_Tabs( $id );	
	
	foreach ( $elements AS $element ){
		$tabs->add_tab( $element['id'], $element['title'], $element['content'] );
	}

	if( TRUE == $return_object ){
		return $tabs;
	}else{
		return $tabs->get_html();
	}
}

?>