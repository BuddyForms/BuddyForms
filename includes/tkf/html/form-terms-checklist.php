<?php

class TK_Form_Terms_Checklist extends TK_Form_Element {

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
    function tk_form_terms_checklist( $postID = 0, $args ) {
	$this->__construct( $postID, $args );
    }

    /**
     * PHP 5 constructor
     *
     * @package Themekraft Framework
     * @since 0.1.0
     * 
     * @param array $args 
     */
    function __construct( $postID = 0, $args ) {
	$this->postID = $postID;

	$defaults = array(
	    // TK_Form_Element Stuff
	    'id' => '',
	    'name' => '',
	    'value' => '',
	    'extra' => '',
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

	parent::__construct( $args );

	$this->extra = $extra;
	$this->checked = $checked;
	$this->before_element = $before_element;
	$this->after_element = $after_element;

	$this->terms_args = array(
		'descendants_and_self' => $descendants_and_self,
		'selected_cats' => $selected_cats,
		'popular_cats' => $popular_cats,
		'walker' => $walker,
		'taxonomy' => $taxonomy,
		'checked_ontop' => $checked_ontop
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
	extract( $this->terms_args );
	$html = $this->before_element;
	$html .= '
<div id="taxonomy-' . $taxonomy . '" class="categorydiv">
<ul id="' . $taxonomy . '-checklist" class="list:' . $taxonomy . ' categorychecklist form-no-clear">
  ' . wp_terms_checklist( $this->postID, array( 'taxonomy' => $taxonomy ) ) . '
</ul>
</div>	    
';
	$html.= $this->after_element;

	return $html;
    }

}

function tk_terms_checklist( $postID = 0, $args, $return_object = FALSE ) {
    $terms_checklist = new TK_Form_Terms_Checklist( $postID, $args );

    if ( TRUE == $return_object ) {
	return $terms_checklist;
    } else {
	return $terms_checklist->get_html();
    }
}