<?php

/**
 * Class Validation_Email
 */
class Validation_Email extends Validation {
	/**
	 * @var string
	 */
	protected $message = "Error: %element% must contain an email address.";

	/**
	 * @param $value
	 *
	 * @return bool
	 */
	public function isValid( $value ) {
		if ( $this->isNotApplicable( $value ) || filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
			return true;
		}

		return false;
	}
}
