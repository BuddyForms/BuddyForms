<?php

/**
 * Class Element_Captcha
 */
class Element_Captcha extends Element {
	/**
	 * Element_Captcha constructor.
	 *
	 * @param string $label
	 * @param array|null $properties
	 * @param null $customfield
	 */
	public function __construct( $label = "", array $properties = null, $customfield = null ) {
		parent::__construct( $label, "recaptcha_response_field", $properties, $customfield );
	}

	public function isValid( $value ) {
		$this->validation[] = new Validation_Captcha( $this->getAttribute( 'private_key' ) );
		return parent::isValid( $value );
	}


	public function render() {
		echo '<style>#recaptcha_table {table-layout: auto;}</style>';
		echo '<script src="//www.google.com/recaptcha/api.js"></script>';
		echo '<div data-type="' . esc_attr( $this->getAttribute( 'data_type' ) ) . '" data-size="' . esc_attr( $this->getAttribute( 'data_size' ) ) . '" data-theme="' . esc_attr( $this->getAttribute( 'data_theme' ) ) . '" class="g-recaptcha" data-sitekey="' . esc_attr( $this->getAttribute( 'site_key' ) ) . '"></div>';
	}
}
