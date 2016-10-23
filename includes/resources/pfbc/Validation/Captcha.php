<?php

/**
 * Class Validation_Captcha
 */
class Validation_Captcha extends Validation {
	/**
	 * @var string
	 */
	protected $message = "Error: The reCATPCHA response provided was incorrect.  Please re-try.";
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
	public function isValid( $value ) {
		require_once( dirname( __FILE__ ) . "/../Resources/recaptchalib.php" );
		$resp = recaptcha_check_answer( $this->privateKey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"] );
		if ( $resp->is_valid ) {
			return true;
		} else {
			return false;
		}
	}
}
