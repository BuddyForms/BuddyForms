<?php

/**
 * Class Validation_Date
 */
class Validation_Date extends Validation {
	/**
	 * @var string
	 */
	protected $message = "Error: %element% must contain a valid date.";

	/**
	 * @param $value
	 * @param $element
	 *
	 * @return bool
	 */
	public function isValid( $value, $element ) {
		try {
			$d = DateTime::createFromFormat( $this->field_options['element_save_format'], $value );

			$result = $d && $d->format( $this->field_options['element_save_format'] ) === $value;

			return apply_filters('buddyforms_element_date_validation', $result, $element );
		} catch ( Exception $e ) {
			return false;
		}
	}
}
