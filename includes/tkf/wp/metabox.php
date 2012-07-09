<?php

class TK_WP_Metabox extends TK_HTML{
	
	var $option_group;
	var $title;
	var $content;
	var $post_type;
	
	
	function tk_wp_metabox( $id, $title, $content, $post_type = 'post' ){
		$this->__construct( $id, $title, $content, $post_type );		
	}
	
	function __construct( $id, $title, $content, $post_type = 'post' ){
		global $tkf_metabox_ids, $tkf_metabox_id;
		
		parent::__construct();
		
		$this->id = $id;
		$this->title = $title;
		$this->post_type = $post_type;
		$this->content = $content;
		
		$tkf_metabox_id = $id;
		
		if( $this->id != '' && !in_array( $this->id, $tkf_metabox_ids ) ):
			$html = apply_filters( 'tk_metabox_before_content_' . $this->id, $html );
			add_action( 'add_meta_boxes', array( $this, 'create' ) );
			array_push( $tkf_metabox_ids, $tkf_metabox_id );
		endif;
	}
	
	function get_html(){
		// Getting HTML
		$tkdb = new TK_Display();
		$html.= $tkdb->get_html( $this->content );
		unset( $tkdb );
		
		return $html;
	}
	
	function create(){
		add_meta_box( $this->id, $this->title, array( $this, 'write_html' ) , $this->post_type );
	}
	
}

function tk_wp_metabox( $id, $title, $content, $post_type = 'post', $return_object = FALSE ){
	$metabox = new TK_WP_Metabox( $id, $title, $content, $post_type );
	
	if( TRUE == $return_object ){
		return $metabox;
	}else{
		return $metabox->get_html();
	}
}