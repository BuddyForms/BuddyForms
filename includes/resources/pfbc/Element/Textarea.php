<?php

/**
 * Class Element_Textarea
 */
class Element_Textarea extends Element {
	/**
	 * @var array
	 */
	protected $_attributes = array( "type" => "textarea", "rows" => "5" );

	/**
	 * @var string
	 */
	protected $message = "Error: %element% is a required field.";

	/**
	 * Element_Content constructor.
	 *
	 * @param $label
	 * @param $name
	 * @param $value
	 * @param $field_options
	 */
	public function __construct( $label, $name, $value, array $field_options = null ) {
		global $field_id;

		$properties = array(
			"value"    => $value,
			"field_id" => $field_id
		);
		parent::__construct( $label, $name, $properties, $field_options );
	}

	public function render( $echo = true ) {
		if ( ! empty( $this->_attributes["value"] ) && is_array( $this->_attributes["value"] ) ) {
			$output = '<textarea' . $this->getAttributes( "value" ) . '>';
			if ( ! empty( $this->_attributes["value"]['value'] ) ) {
				$output .= $this->filter( $this->_attributes["value"]['value'] );
			}
			$output .= '</textarea>';
		} elseif ( ! empty( $this->_attributes["value"] ) && is_string( $this->_attributes["value"] ) ) {
			$output = $this->_attributes["value"];
		}

		if ( $echo ) {
			echo $output;

			return '';
		} else {
			return $output;
		}
	}

	public function isValid( $value ) {
		if ( ! empty( $this->field_options ) && ! empty( $this->field_options['required'] ) && $this->field_options['required'][0] === 'required' ) {
			$validation = new Validation_Required( $this->message, $this->field_options );

			$value = $this->getAttribute( 'value' );
			preg_match_all( '/<textarea .*?>(.*?)<\/textarea>/s', $value, $matches );

			$result = $validation->isNotApplicable( $value ) || ! empty( $matches[1][0] );

			if ( ! $result ) {
				$this->_errors[] = str_replace( "%element%", $this->getLabel(), $validation->getMessage() );
			}

			return apply_filters( 'buddyforms_element_textarea_validation', $result, $this );
		} else {
			return true;
		}
	}
}
