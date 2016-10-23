<?php

/**
 * Class Element_Email
 */
class Element_Email extends Element_Textbox {
	/**
	 * @var array
	 */
	protected $_attributes = array( "type" => "email" );

	public function render() {
		$this->validation[] = new Validation_Email;
		parent::render();
	}
}
