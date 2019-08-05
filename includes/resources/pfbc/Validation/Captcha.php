<?php

/**
 * Class Validation_Captcha
 */
class Validation_Captcha extends Validation {
	/**
	 * @var string
	 */
	protected $message = "Error: The reCATPCHA response provided was incorrect.  Please try again.";
	/**
	 * @var string
	 */
	protected $privateKey;

	/**
	 * Validation_Captcha constructor.
	 *
	 * @param string $privateKey
	 * @param string $message
	 */
	public function __construct( $privateKey, $message = "" ) {
		$this->privateKey = $privateKey;
		if ( ! empty( $message ) ) {
			$this->message = $message;
		}
	}

	/**
	 * @param $value
	 *
	 * @return bool
	 */
	public function isValid( $value, $element ) {
		$captcha = sanitize_text_field( $_POST["g-recaptcha-response"] );
		$resp    = $this->validate_google_captcha( $captcha, $this->privateKey );
		$result  = ! empty( $resp['success'] ) && boolval( $resp['success'] ) === true;

		return apply_filters( 'buddyforms_element_captcha_validation', $result, $element );
	}

	public function validate_google_captcha( $captcha, $secret ) {
		return json_decode( file_get_contents( "https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR'] ), true );
	}
}
