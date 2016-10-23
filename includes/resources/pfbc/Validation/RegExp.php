<?php

/**
 * Class Validation_RegExp
 */
class Validation_RegExp extends Validation {
	/**
	 * @var string
	 */
	protected $message = "Error: %element% contains invalid characters.";
	/**
	 * @var string
	 */
	protected $pattern;

	/**
	 * Validation_RegExp constructor.
	 *
	 * @param string $pattern
	 * @param string $message
	 */
	public function __construct( $pattern, $message = "" ) {
		$this->pattern = $pattern;
		parent::__construct( $message );
	}

	/**
	 * @param $value
	 *
	 * @return bool
	 */
	public function isValid( $value ) {
		if ( $this->isNotApplicable( $value ) || preg_match( $this->pattern, $value ) ) {
			return true;
		}

		return false;
	}
}
