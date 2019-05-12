<?php

/**
 * Class Element_Textarea
 */
class Element_Textarea extends Element {
	/**
	 * @var array
	 */
	protected $_attributes = array( "rows" => "5" );

	public function render($echo = true) {
		$output = '<textarea'. $this->getAttributes( "value" ). '>';
		if ( ! empty( $this->_attributes["value"] ) ) {
			$output .= $this->filter( $this->_attributes["value"] );
		}
		$output .= '</textarea>';

		if ( $echo ) {
			echo $output;

			return;
		} else {
			return $output;
		}
	}
}
