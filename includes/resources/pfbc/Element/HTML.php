<?php

/**
 * Class Element_HTML
 */
class Element_HTML extends Element {

	/**
	 * @var array
	 */
	protected $_attributes = array( "type" => "html" );

	/**
	 * Element_HTML constructor.
	 *
	 * @param $value
	 */
	public function __construct( $value ) {
		$properties = array(
			"value" => $value,
			"field_id"  => 'asd'
		);
		parent::__construct( "", "", $properties );
	}

	public function render() {

//		print_r($this->_attributes);
		echo $this->_attributes["field_id"];
		echo $this->_attributes["value"];
	}
}
