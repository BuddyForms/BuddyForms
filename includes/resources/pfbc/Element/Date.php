<?php

/**
 * Class Element_Date
 */
class Element_Date extends Element_Textbox {
	/**
	 * @var array
	 */
	protected $_attributes = array(
		"type"    => "date",
		"pattern" => "\d{4}-\d{2}-\d{2}"
	);

	/**
	 * Element_Date constructor.
	 *
	 * @param $label
	 * @param $name
	 * @param array|null $properties
	 */
	public function __construct( $label, $name, array $properties = null ) {
		$this->_attributes["placeholder"] = "YYYY-MM-DD (e.g. " . date( "Y-m-d" ) . ")";
		$this->_attributes["title"]       = $this->_attributes["placeholder"];

		parent::__construct( $label, $name, $properties );
	}

	public function render() {
		$this->validation[] = new Validation_Date ( "Error: The %element% field must match the following date format: " . $this->_attributes["title"] );
		parent::render();
	}
}
