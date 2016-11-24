<?php

/**
 * Class MaxLength
 * @package PFBC\Validation
 */
class MaxLength extends Validation {
	/**
	 * @var
	 */
	protected $message;
	/**
	 * @var
	 */
	protected $limit;

	/**
	 * MaxLength constructor.
	 *
	 * @param $limit
	 * @param string $message
	 */
	public function __construct( $limit, $message = "" ) {
		$this->limit = $limit;
		if ( empty( $message ) ) {
			$message = "%element% is limited to " . $limit . " characters.";
		}
		parent::__construct( $message );
	}

	/**
	 * @param $value
	 *
	 * @return bool
	 */
	public function isValid( $value ) {
		if ( $this->isNotApplicable( $value ) || strlen( $value ) <= $this->limit ) {
			return true;
		}

		return false;
	}
}
