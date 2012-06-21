<?php

class TK_WP_Form extends TK_Form{
	
	var $option_group;
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $option_group The name of the option group
	 * @param string $id The id of the form
	 */
	function tk_wp_form( $id, $option_group ){
		$this->__construct( $id, $option_group );
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $option_group The name of the option group
	 * @param string $id The id of the form
	 */
	function __construct( $id, $option_group ){
		$args['method'] = 'POST';
		$args['action'] = 'options.php';
		$args['name'] = $id;
		
		parent::__construct( $id, $args );
		
		$this->option_group = $option_group; 
	}
	
	/**
	 * Getting the form html
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @return string $html The form content
	 * 
	 */
	function get_html(){
		$html = '<input type="hidden" name="option_page" value="' . esc_attr( $this->option_group ) . '" />';
		$html.= '<input type="hidden" name="action" value="update" />';
		$html.= wp_nonce_field( $this->option_group . '-options', "_wpnonce", true , false ) ;
		
		// Actionhook tk_form_before_content_ + id
		if( $this->id != '' ) $html = apply_filters( 'tk_form_before_content_' . $this->id, $html );
		
		$this->add_element( $html );

		$html = parent::get_html();
		
		return $html;
	}
}
function tk_form( $id, $option_group, $content, $return_object = FALSE ){
	global $tk_form_instance_option_group;
	$tk_form_instance_option_group = $option_group;
	
	$form = new TK_WP_Form( $id, $option_group );
	$form->add_element( $content );
	
	if( TRUE == $return_object ){
		return $form;
	}else{
		return $form->get_html();
	}
}
function tk_form_content( $content ){
  global $tk_form_instance_content;
  $tk_form_instance_content = $content;
}
function tk_form_content_buffer( $buffer ){
  global $tk_form_instance_buffer;
  $tk_form_instance_buffer = $buffer;
}
function tk_form_start( $option_group, $id = '' ){
	global $tk_form_instance_option_group, $tk_form_instance_id;
	$tk_form_instance_option_group = $option_group;
	$tk_form_instance_id = $id;
	
	ob_start("tk_form_content_buffer");
}
function tk_form_end( $output = true ){
	global $tk_form_instance_option_group, $tk_form_instance_id, $tk_form_instance_buffer, $tk_form_instance_content;
	ob_end_flush();	
	
	$form = new TK_WP_Form( $tk_form_instance_option_group, $tk_form_instance_id );
	
	if( $tk_form_instance_content != '' ){
		$form->add_element( $tk_form_instance_content );
	}else{
		$form->add_element( $tk_form_instance_buffer );
	}
	
	if( $output ){
		$form->write_html();
	}else{
		return $form->get_html();
	}
	
	unset( $tk_form_instance );
	unset( $tk_form_instance_buffer );
}
function tk_register_wp_option_group( $option_group ){
	global $post_option_group;
	$post_option_group = $option_group;
	
	add_action( 'save_post', 'tk_save_wp_metabox_option_group' );
	register_setting( $option_group, $option_group . '_values' );
}

function tk_save_wp_metabox_option_group( $post_id ){
	global $post_option_group;
	
//	echo 'POG: ' . $post_option_group;
	
	// verify if this is an auto save routine. 
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	    return;
	
	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( !wp_verify_nonce( $_POST[ $post_option_group . '_nonce' ], 'sp_post_metabox' ) )
	    return;
	 
	// Check permissions
	if ( 'page' == $_POST['post_type'] ) {
	  if ( !current_user_can( 'edit_page', $post_id ) )
	      return;
	}
	else {
	  if ( !current_user_can( 'edit_post', $post_id ) )
	      return;
	}	
	
	update_post_meta( $post_id, $post_option_group, $_POST[ $post_option_group ] );
}