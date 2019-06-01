<?php

/**
 * Class Validation_Required
 */
class Validation_Required extends Validation {
	/**
	 * @var string
	 */
	protected $message = "Error: %element% is a required field.";

	/**
	 * @param $value
	 * @param $element
	 *
	 * @return bool
	 */
	public function isValid( $value, $element ) {
		$valid = false;
		if ( ! is_null( $value ) && ( ( ! is_array( $value ) && $value !== "" ) || ( is_array( $value ) && ! empty( $value ) ) ) ) {
			$valid = true;
		}

		return apply_filters('buddyforms_element_required_validation', $valid, $value, $element );
	}
}
