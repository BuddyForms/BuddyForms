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
	 *
	 * @return bool
	 */
	public function isValid( $value ) {
		try {
			$d = DateTime::createFromFormat( $this->field_options['element_save_format'], $value );

			return $d && $d->format( $this->field_options['element_save_format'] ) === $value;
		} catch ( Exception $e ) {
			return false;
		}
	}
}
