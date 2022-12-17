<?php

/**
 * Class Element_HTML
 */
class Element_InlineHTML extends Element {

	/**
	 * @var array
	 */
	protected $_attributes = array( 'type' => 'html' );

	/**
	 * Element_HTML constructor.
	 *
	 * @param $value
	 */
	public function __construct( $value ) {
		global $field_id;

		$properties = array(
			'value'    => $value,
			'field_id' => $field_id,
		);
		parent::__construct( '', '', $properties );
	}

	public function render() {
		echo wp_kses( $this->_attributes['value'], buddyforms_wp_kses_allowed_atts() );
	}
}
