<?php

/**
 * Class Element_Captcha
 */
class Element_Captcha extends Element {
	/**
	 * Element_Captcha constructor.
	 *
	 * @param string     $label
	 * @param array|null $properties
	 */
	public function __construct( $label = "", array $properties = null ) {
		parent::__construct( $label, "recaptcha_response_field", $properties );
	}
	
	public function render() {
		echo '<style>#recaptcha_table {table-layout: auto;}</style>';
		$this->validation[] = new Validation_Captcha( $this->getAttribute( 'private_key' ) );
		echo '<script src="//www.google.com/recaptcha/api.js"></script>';
		echo '<div data-type="' . esc_attr( $this->getAttribute( 'data_type' ) ) . '" data-size="' . esc_attr( $this->getAttribute( 'data_size' ) ) . '" data-theme="' . esc_attr( $this->getAttribute( 'data_theme' ) ) . '" class="g-recaptcha" data-sitekey="' . esc_attr( $this->getAttribute( 'site_key' ) ) . '"></div>';
	}
}
