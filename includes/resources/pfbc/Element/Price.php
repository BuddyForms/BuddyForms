<?php

/**
 * Class Element_Date
 */
class Element_Price extends Element_Textbox {
	public function render() {
		if ( ! empty( $this->field_options ) ) {
			$this->_attributes["class"] .= ' bf_woo_price ';
		}
		//include the asset
		wp_enqueue_script( 'jquery.priceformat', BUDDYFORMS_PLUGIN_URL . 'assets/resources/jquery.priceformat.min.js', array( 'jquery' ), BUDDYFORMS_VERSION );

		parent::render();
	}

	public static function builder_element_options( $form_fields, $form_slug, $field_type, $field_id, $buddyform ) {

		return $form_fields;
	}
}
