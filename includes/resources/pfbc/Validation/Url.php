<?php

/**
 * Class Validation_Url
 */
class Validation_Url extends Validation {
	/**
	 * @var string
	 */
	protected $message = "Error: %element% must contain a url (e.g. http://www.google.com).";

	/**
	 * @param $value
	 *
	 * @return bool
	 */
	public function isValid( $value ) {
		if ( $this->isNotApplicable( $value ) || filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return true;
		}

		return false;
	}
}
