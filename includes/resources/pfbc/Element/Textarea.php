<?php

/**
 * Class Element_Textarea
 */
class Element_Textarea extends Element {
	/**
	 * @var array
	 */
	protected $_attributes = array( "rows" => "5" );

	public function render() {
		echo '<textarea', $this->getAttributes( "value" ), '>';
		if ( ! empty( $this->_attributes["value"] ) ) {
			echo $this->filter( $this->_attributes["value"] );
		}
		echo '</textarea>';
	}
}
