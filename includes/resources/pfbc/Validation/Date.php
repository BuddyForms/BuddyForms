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
			// $date = new DateTime( $value ); // todo: validate date and time with custom settings

			return true;
		} catch ( Exception $e ) {
			return false;
		}
	}
}
