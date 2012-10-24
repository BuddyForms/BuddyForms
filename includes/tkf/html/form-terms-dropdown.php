<?php

class TK_Form_Terms_Dropdown extends TK_Form_Element {

    var $extra;
    var $before_element;
    var $after_element;
    var $terms_args;
    var $postID;

    /**
     * PHP 4 constructor
     *
     * @package Themekraft Framework
     * @since 0.1.0
     * 
     * @param array $args 
     */
    function tk_form_terms_checklist( $args ) {
	$this->__construct( $args );
    }

    /**
     * PHP 5 constructor
     *
     * @package Themekraft Framework
     * @since 0.1.0
     * 
     * @param array $args 
     */
    function __construct( $args ) {
	$defaults = array(
	    // TK_Form_Element Stuff
	    'id' => '',
	    'value' => '',
	    'extra' => '',
	    'before_element' => '',
	    'after_element' => '',
	    'multiple' => '',
	    'selected_cats' => false,
	    // wp_dropdown_categories Stuff
	    'hide_empty' => 0,
	    'id' => 0,
	    'child_of' => 0,
	    'echo' => FALSE,
	    'selected' => false,
	    'hierarchical' => 1,
	    'name' => '',
	    'class' => 'postform',
	    'depth' => 0,
	    'tab_index' => 0,
	    'taxonomy' => 'category',
	    'hide_if_empty' => FALSE,
	    'orderby' => 'id',
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	parent::__construct( $args );

	$this->extra = $extra;
	$this->value = $value;
	$this->before_element = $before_element;
	$this->after_element = $after_element;
	$this->multiple = $multiple;
	$this->selected_cats = $selected_cats;
	
	$this->terms_args = array(
	    'hide_empty' => $hide_empty,
	    'id' => $id,
	    'child_of' => $child_of,
	    'echo' => $echo,
	    'selected' => $selected,
	    'hierarchical' => $hierarchical,
	    'name' => $name,
	    'class' => $class,
	    'depth' => $depth,
	    'tab_index' => $tab_index,
	    'taxonomy' => $taxonomy,
	    'hide_if_empty' => $hide_if_empty,
	    'orderby' => $orderby,
	);
    }

    /**
     * Getting HTML of checkbox
     *
     * @package Themekraft Framework
     * @since 0.1.0
     * 
     * @return string $html The HTML of checkbox
     */
    function get_html() {
	/**
	 * @todo fix the orderby='name'  problem. only $taxonomy="category" is ordered correctly by name. Other taxnonomies arent?! 
	 */
	$dropdown = wp_dropdown_categories( $this->terms_args );

	if ( $this->multiple ) {
	    $dropdown = str_replace( 'id=', 'multiple="multiple" id=', $dropdown );
	}
	if ( is_array( $this->selected_cats ) ) {
	    foreach ( $this->selected_cats as $value ) {
		$dropdown = str_replace( ' value="' . $value . '"', ' value="' . $value . '" selected="selected"', $dropdown );
	    }
	}

	$html = $this->before_element . $dropdown . $this->after_element;

	return $html;
    }

}

function tk_terms_dropdown( $args, $return_object = FALSE ) {
    $terms_dropdown = new TK_Form_Terms_Dropdown( $args );

    if ( TRUE == $return_object ) {
	return $terms_dropdown;
    } else {
	return $terms_dropdown->get_html();
    }
}