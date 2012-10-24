<?php

class TK_WP_Form_Terms_Checklist extends TK_Form_Terms_Checklist {

    var $option_group;

    /**
     * PHP 4 constructor
     *
     * @package Themekraft Framework
     * @since 0.1.0
     * 
     * @param string $name Name of terms checklist
     * @param array $args 
     */
    function tk_wp_form_terms_checklist( $name, $postID = 0, $args = array( ) ) {
	$this->__construct( $name, $args );
    }

    /**
     * PHP 5 constructor
     *
     * @package Themekraft Framework
     * @since 0.1.0
     * 
     * @param string $name Name of terms checklist
     * @param array $args 
     */
    function __construct( $name, $postID = 0, $args = array( ) ) {
	global $tk_hidden_elements, $post, $tk_form_instance_option_group;

	$defaults = array(
	    'id' => '',
	    'value' => '',
	    'extra' => '',
	    'option_group' => $tk_form_instance_option_group,
	    'multi_index' => '',
	    'before_element' => '',
	    'after_element' => '',
	    // wp_terms_checklist Stuff
	    'descendants_and_self' => 0,
	    'selected_cats' => false,
	    'popular_cats' => false,
	    'walker' => null,
	    'taxonomy' => 'category',
	    'checked_ontop' => true
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	$field_name = tk_get_field_name( $name, array( 'option_group' => $option_group, 'multi_index' => $multi_index ) );

	$args[ 'name' ] = $field_name;

	parent::__construct( $args );
    }

}

function tk_form_terms_checklist( $name, $postID = 0, $args = array( ), $return = 'echo' ) {
   $terms_checklist = new TK_WP_Form_Terms_Checklist( $name, $postID, $args );
   return tk_element_return( $terms_checklist, $return );
}