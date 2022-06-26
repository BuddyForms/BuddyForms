<?php

/**
 * Class Element_jQueryUIDate
 */
class Element_jQueryUIDate extends Element_Textbox {
	/**
	 * @var array
	 */
	protected $_attributes = array(
		'type'         => 'text',
		'autocomplete' => 'off',
	);
	/**
	 * @var
	 */
	protected $jQueryOptions;

	/**
	 * @return array
	 */
	public function getCSSFiles() {
		return array(
		// $this->_form->getPrefix() . "://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/smoothness/jquery-ui.min.css"
		);
	}

	public function jQueryDocumentReady() {
		parent::jQueryDocumentReady();
		echo 'jQuery("#', esc_js( $this->_attributes['id'] ), '").datepicker(', esc_js( $this->jQueryOptions() ), ');';
	}

	public function render() {
		$this->validation[] = new Validation_Date();
		parent::render();
	}
}
