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
		$captcha_attributes = array();
		$language_code      = $this->getOption( 'captcha_language' );
		if ( ! empty( $language_code ) ) {
			$captcha_attributes['hl'] = $language_code;
		}
		$captcha_attributes_string = build_query( $captcha_attributes );
		if ( ! empty( $captcha_attributes_string ) ) {
			$captcha_attributes_string = '?' . apply_filters( 'buddyforms_captcha_js_source_parameter', $captcha_attributes_string, $this );
		}
		echo '<style>#recaptcha_table {table-layout: auto;}</style>';
		echo sprintf( '<script src="//www.google.com/recaptcha/api.js%s"></script>', $captcha_attributes_string );
		echo '<div data-type="' . esc_attr( $this->getAttribute( 'data_type' ) ) . '" data-size="' . esc_attr( $this->getAttribute( 'data_size' ) ) . '" data-theme="' . esc_attr( $this->getAttribute( 'data_theme' ) ) . '" class="g-recaptcha" data-sitekey="' . esc_attr( $this->getAttribute( 'site_key' ) ) . '"></div>';
	}
}
