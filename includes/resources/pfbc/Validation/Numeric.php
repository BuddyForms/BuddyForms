<?php

/**
 * Class Validation_Numeric
 */
class Validation_Numeric extends Validation {
	/**
	 * @var string
	 */
	protected $message = "Error: %element% must be numeric.";

	/**
	 * @param $value
	 *
	 * @return bool
	 */
	public function isValid( $value ) {
		if ( $this->isNotApplicable( $value ) || is_numeric( $value ) ) {
			return true;
		}

		return false;
	}
}
