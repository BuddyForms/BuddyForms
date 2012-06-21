<?php

class TK_Jqueryui_Autocomplete extends TK_WP_Form_Textfield{
	
	var $autocomplete_values;
	
	function tk_jqueryui_autocomplete( $name, $args = array() ){
		$this->__construct( $name, $args );
	}
	
	function __construct( $name, $args = array() ){
		parent::__construct( $name, $args );
		$this->autocomplete_values = array();
		$this->delete_values = array();
	}
	
	function add_autocomplete_value( $value ){
		array_push( $this->autocomplete_values, $value ); 
	}
	
	
	function delete_autocomplete_value( $value ){
		$delete_keys = array_keys( $this->autocomplete_values, $value );
		foreach( $delete_keys AS $key ){
			unset( $this->autocomplete_values[ $key ] );
		}
	}
	
	function get_html(){
		
		$this->merge_autocomplete_elements();
		
		$html = parent::get_html();
		
		$autocomplete_values = array();
		
		foreach( $this->autocomplete_values AS $key => $value )
			array_push( $autocomplete_values, '"' .  $value . '"' );

		$values = implode( ',', $autocomplete_values );
		
		$html .= '
			<script type="text/javascript">
			  	jQuery(document).ready(function($){
				  	$("#' . $this->id . '").autocomplete({ source: [' . $values . '] });
			  	});
	  		</script>
	  	';	
		
	  	return $html;
	  
	}
	
	function merge_autocomplete_elements(){
		global $tk_autocomplete_elements;
		
		if( is_array( $tk_autocomplete_elements[ $this->id ] ) ){
			
			foreach( $tk_autocomplete_elements[ $this->id ] AS $element ){
				
				if( $element[ 'action' ] == 'add' )
					$this->add_autocomplete_value( $element['value'] );
				
				if( $element[ 'action' ] == 'delete' )
					$this->delete_autocomplete_value( $element['value'] );
							
				
			}
		}
	}
}

function tk_jqueryui_autocomplete( $name, $values, $args, $return_object = false ){
	$autocomplete = new TK_Jqueryui_Autocomplete( $name, $args );
	
	foreach ( $values AS $value ){
		$autocomplete->add_autocomplete_value( $value[0] );
	}
	
	if( TRUE == $return_object ){
		return $autocomplete;
	}else{
		return $autocomplete->get_html();
	}
}

function tk_autocomplete_add_value( $autocomplete_id, $value ){
	global $tk_autocomplete_elements;
	
	if( !is_array( $tk_autocomplete_elements[ $autocomplete_id ] ) )
		$tk_autocomplete_elements[ $autocomplete_id ] = array();
		
	array_push( $tk_autocomplete_elements[ $autocomplete_id ], array( 'action' => 'add' , 'value' => $value ) );
}

function tk_autocomplete_delete_value( $autocomplete_id, $value ){
	global $tk_autocomplete_elements;
	
	if( !is_array( $tk_autocomplete_elements[ $autocomplete_id ] ) )
		$tk_autocomplete_elements[ $autocomplete_id ] = array();
		
	array_push( $tk_autocomplete_elements[ $autocomplete_id ], array( 'action' => 'delete' , 'value' => $value ) );	
}
