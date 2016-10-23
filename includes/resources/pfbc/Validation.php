<?php

/**
 * Class Validation
 */
abstract class Validation extends Base {
	/**
	 * @var string
	 */
	protected $message = "%element% is invalid.";

	/**
	 * Validation constructor.
	 *
	 * @param string $message
	 */
	public function __construct( $message = "" ) {
		if ( ! empty( $message ) ) {
			$this->message = $message;
		}
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @param $value
	 *
	 * @return bool
	 */
	public function isNotApplicable( $value ) {
		if ( is_null( $value ) || is_array( $value ) || $value === "" ) {
			return true;
		}

		return false;
	}

	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	public abstract function isValid( $value );
}
