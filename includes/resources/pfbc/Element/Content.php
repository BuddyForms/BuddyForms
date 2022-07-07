<?php

/**
 * Class Element_Content
 */
class Element_Content extends Element {

	/**
	 * @var array
	 */
	protected $_attributes = array( 'type' => 'content' );

	/**
	 * @var string
	 */
	protected $message = 'Error: %element% is a required field.';

	/**
	 * Element_Content constructor.
	 *
	 * @param $label
	 * @param $name
	 * @param $value
	 * @param $field_options
	 */
	public function __construct( $label, $name, $value, $field_options ) {
		global $field_id;

		$properties = array(
			'value'    => $value,
			'field_id' => $field_id,
		);

		if ( ! empty( $field_options ) && ! empty( $field_options['required'] ) && $field_options['required'][0] === 'required' ) {
			$this->setValidation( new Validation_Required( $this->message, $field_options ) );
		}

		parent::__construct( $label, $name, $properties, $field_options );
	}

	public function render() {
		wp_enqueue_style( 'wp_editor_css', includes_url( '/css/editor.css' ) );
		echo $this->_attributes['value'];
	}
}
