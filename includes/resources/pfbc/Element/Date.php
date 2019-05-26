<?php

/**
 * Class Element_Date
 */
class Element_Date extends Element_Textbox {
	/**
	 * Element_Date constructor.
	 *
	 * @param $label
	 * @param $name
	 * @param array|null $properties
	 */
	public function __construct( $label, $name, array $properties = null ) {
		$properties["class"] .= ' bf_datetimepicker ' . $properties["class"];

		parent::__construct( $label, $name, $properties );
	}

	public function render() {
		$this->validation[] = new Validation_Date ( "Error: The %element% field must match the following date format: " . $this->_attributes["title"] );
		parent::render();
	}
}
