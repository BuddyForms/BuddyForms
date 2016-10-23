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
		$properties = array( "value" => $value );
		parent::__construct( "", "", $properties );
	}

	public function render() {
		echo $this->_attributes["value"];
	}
}
