<?php

/**
 * Class Element_Captcha
 */
class Element_Captcha extends Element {
	/**
	 * @var string
	 */
	protected $privateKey = "6LcazwoAAAAAAD-auqUl-4txAK3Ky5jc5N3OXN0_";
	/**
	 * @var string
	 */
	protected $publicKey = "6LcazwoAAAAAADamFkwqj5KN1Gla7l4fpMMbdZfi";

	/**
	 * Element_Captcha constructor.
	 *
	 * @param string $label
	 * @param array|null $properties
	 */
	public function __construct( $label = "", array $properties = null ) {
		parent::__construct( $label, "recaptcha_response_field", $properties );
	}

	public function render() {
		echo '<script type="text/javascript">var RecaptchaOptions = {theme : \'blackglass\'};</script><style>#recaptcha_table {table-layout: auto;}</style>';
		$this->validation[] = new Validation_Captcha( $this->privateKey );
		require_once( dirname( __FILE__ ) . "/../Resources/recaptchalib.php" );
		echo recaptcha_get_html( $this->publicKey );
	}
}
