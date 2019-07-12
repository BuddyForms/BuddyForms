<?php

/**
 * Class Element_Date
 */
class Element_Date extends Element_Textbox {
	/**
	 * Element_Date constructor.
	 *
	 * @param $label
	 * @param $name
	 * @param $field_options
	 * @param array|null $properties
	 */
	public function __construct( $label, $name, $field_options, array $properties = null ) {
		if ( ! empty( $properties['class'] ) ) {
			$properties['class'] .= ' bf_datetimepicker ';
		} else {
			$properties['class'] = ' bf_datetimepicker ';
		}

		$show_label = isset( $field_options['is_inline'] ) && isset( $field_options['is_inline'][0] ) && $field_options['is_inline'][0] === 'is_inline';
		if ( $show_label ) {
			$properties['label'] = $label;
		}

		parent::__construct( $label, $name, $properties, $field_options );
	}

	public function render() {
		wp_enqueue_script( 'buddyforms-datetimepicker', BUDDYFORMS_ASSETS . 'resources/datetimepicker/jquery.datetimepicker.full.min.js', array( 'jquery' ), BUDDYFORMS_VERSION );
		wp_enqueue_style( 'buddyforms-datetimepicker', BUDDYFORMS_ASSETS . 'resources/datetimepicker/jquery.datetimepicker.min.css' );

		$expected_format = ! empty( $this->field_options['element_save_format'] ) ? $this->field_options['element_save_format'] : '';

		if ( ! empty( $expected_format ) ) {
			$this->validation[] = new Validation_Date ( "Error: The %element% field must match the following date format: " . ! empty( $expected_format ) ? $expected_format : '', $this->field_options );
		}
		parent::render();
	}
}
