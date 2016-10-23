<?php

/**
 * Class Element_Url
 */
class Element_Url extends Element_Textbox {
	/**
	 * @var array
	 */
	protected $_attributes = array( "type" => "url" );

	public function render() {
		$this->validation[] = new Validation_Url;
		parent::render();
	}
}
