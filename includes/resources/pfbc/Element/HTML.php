<?php

/**
 * Class Element_HTML
 */
class Element_HTML extends Element {

	/**
	 * @var array
	 */
	protected $_attributes = array( 'type' => 'html' );

	/**
	 * Element_HTML constructor.
	 *
	 * @param $value
	 * @param string $label
	 * @param string $name
	 * @param array  $field_options
	 */
	public function __construct( $value, $label = '', $name = '', $field_options = array() ) {
		global $field_id;

		$properties = array(
			'value'    => $value,
			'field_id' => $field_id,
		);
		parent::__construct( $label, $name, $properties, $field_options );
	}

	public function render() {
		echo wp_kses( $this->_attributes['value'], buddyforms_wp_kses_allowed_atts() );
	}
}
