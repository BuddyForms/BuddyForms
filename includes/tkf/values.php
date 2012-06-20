<?php

class TK_Values{
	var $option_group;
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 */
	function tk_values( $option_group = '' ){
		$this->__construct( $option_group );
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 */
	function __construct( $option_group = ''  ){
		$this->option_group = $option_group;
	}
	
	function get_values(){
		$values = get_option( $this->option_group  . '_values' );
		
		if( $values != '' )
			return (object) $values;
			 
		return FALSE;
	}
	
	function set_values( $values ){
		return update_option( $this->option_group . '_values', $values );
	}
	
	function get_post_values( $postID = FALSE ){
		global $post;
		
		if( $postID != FALSE ){
			return get_post_meta( $postID , $option_group , true );
		}else if( isset( $post ) ){
			return get_post_meta( $post->ID , $option_group , true );
		}else{
			return FALSE;
		}
	}
	
	function get_field_name( $name, $args = array() ){
		global $post, $tk_form_instance_option_group, $tkf_metabox_id;
		
		$defaults = array(
				'option_group' => $tk_form_instance_option_group,
				'multi_index' => ''
			);
			
		$parsed_args = wp_parse_args( $args, $defaults );
		extract( $parsed_args , EXTR_SKIP );
		
		// Getting values from post
		if( $post != '' || isset( $_GET['post_type'] ) || ( $_GET['action'] == 'edit' && ( isset( $_GET['message'] )  || isset( $_GET['post'] ) ) ) ):
			// Getting Post ID
			$post_id =  $post->ID;
			
			if( $post_id == '' )
				$post_id = $_GET['post'];
			
			if( $post_id != '' ):
				
				// Setting up option group id for getting data
				$option_group = $tkf_metabox_id;
	
				// Getting field name
				if( is_array( $multi_index ) ):
					$field_name = $option_group . '[' . $name . ']';
					
					foreach ( $multi_index as $index ) {
						$field_name .= '[' . $index . ']';
					}
				else:
					$field_name = $option_group . '[' . $name . ']';
				endif;
				
			endif;
			
		// Getting values from options
		else:
			// Setting up fieldname
			if( is_array( $multi_index ) ):
				
				$field_name = $option_group . '_values[' . $name . ']';	
				foreach ( $multi_index as $index ) :
					$field_name .= '[' . $index . ']';
				endforeach;
				
			elseif ( $multi_index != '' || is_int($multi_index) ):
				$field_name = $option_group . '_values[' . $name . '][' . $multi_index . ']';	
			else:
				$field_name = $option_group . '_values[' . $name . ']';
			endif;
		endif;
		
		return $field_name;
	}

	function get_value( $name, $args = array() ){
		global $post, $tk_form_instance_option_group, $tkf_metabox_id;
	
		$defaults = array(
				'option_group' => $tk_form_instance_option_group,
				'multi_index' => '',
				'default_value' => ''
			);
			
		$this->name = $name;
		
		// echo 'Name: ' . $this->name .  '<br />';
			
		$parsed_args = wp_parse_args( $args, $defaults );
		extract( $parsed_args , EXTR_SKIP );
		
		add_action( 'save_post', array( $this, 'save_post_fields' ) );
		
		/*
		 * If we are within a post, use post meta data
		 */
		if( $post != '' || isset( $_GET['post_type'] ) || ( $_GET['action'] == 'edit' && ( isset( $_GET['message'] )  || isset( $_GET['post'] ) ) ) ):
			
			// Getting Post ID
			$post_id =  $post->ID;
			
			if( $post_id == '' )
				$post_id = $_GET['post'];
			
			if( $post_id != '' ):
				
				// Setting up option group id for getting data
				$option_group = $tkf_metabox_id;
				
				// Getting data
				$value = get_post_meta( $post_id, $option_group , TRUE );
				
				/*echo '<pre>';
				print_r( $value );
				echo '</pre>';*/
				
				// Getting field value			
				if( $multi_index != '' || is_int($multi_index) ):
					if( is_array( $multi_index ) ):
						// Getting values of multiindex array
						$value = $this->get_multiindex_value( $value[ $option_group ][ $name ], $multi_index );
					else:
						$value = $value[ $option_group ][ $name ][ $multi_index ];
					endif;
				else:
					if( isset( $value[ $option_group ] ) )
						$value = $value[ $option_group ][ $name ];
				endif;
				
			endif;
			
		/*
		 * Getting values from options
		 */
		else:
			$value = get_option( $option_group  . '_values' );
					
			// Setting up value
			if( $multi_index != '' || is_int($multi_index) ):
				if( is_array( $multi_index ) ):
					// Getting values of multiindex array
					$value = $this->get_multiindex_value( $value[ $name ], $multi_index );
				else:
					$value = $value[ $name ][ $multi_index ];
				endif;
			else:
				$value = $value[ $name ];
			endif;
		endif;
		
		// Setting up default value if no value is given
		if( $value == '' )
			$value = $default_value;
		
		return $value;
	}

	function get_multiindex_value( $value, $multi_index, $i = 0 ){
	    if( count( $multi_index ) >  $i ):
			return $this->get_multiindex_value( $value[$multi_index[$i]], $multi_index, ++$i );
		else:
			return $value;
		endif;
	}
	
	function save_post_fields( $post_id ){
		global $tkf_metabox_id;
		
		if ( !wp_is_post_revision( $post_id ) ):
			
			if( isset( $_REQUEST[ $tkf_metabox_id ][ $this->name ] ) ) :
				$post_meta = get_post_meta( $post_id, $tkf_metabox_id, TRUE );
				
				$post_meta[ $tkf_metabox_id ][ $this->name ] = $_REQUEST[ $tkf_metabox_id ][ $this->name ];
								
				update_post_meta( $post_id, $tkf_metabox_id, $post_meta );
			endif;
			
		endif;
	}
}

function tk_encrypt_string( $string ){
	for( $i = 0; $i < strlen( $string ) ; $i++ ){
		$string_encrypted.= chr( $string[$i] );
	}
	return $string_encrypted;
}
function tk_decrypt_string( $string ){
	for( $i=0 ; $i < strlen( $string ); $i++ ){
		$string_decrypted.= ord( $string[$i] );
	}
	return $string_decrypted;
}

function tk_get_values( $option_group ){
	$val = new TK_Values( $option_group );
	return $val->get_values();
}

function tk_get_post_values( $metabox_id ){
	global $post;
	return get_post_meta( $post->ID, $metabox_id, TRUE );
}

function tk_set_values( $option_group, $values ){
	$val = new TK_Values( $option_group );
	return $val->set_values( $values );
}

function tk_get_value( $name, $args = array() ){
	$val = new TK_Values();
	return $val->get_value( $name, $args );
}

function tk_get_field_name( $name, $args = array() ){
	$val = new TK_Values();
	return $val->get_field_name( $name, $args );	
}