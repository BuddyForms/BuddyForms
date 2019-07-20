<?php

/**
 * Class Element_Url
 */
class Element_Url extends Element_Textbox {
	/**
	 * @var array
	 */
	protected $_attributes = array( 'type' => 'text', 'class' => 'bf-user-website', 'data-rule-user-website' => 'true' );

	public function render() {
		$this->_attributes['data-rule-user-website'] = ( ! empty( $this->field_options ) && ! empty( $this->field_options['required'] ) && $this->field_options['required'][0] === 'required' );

		parent::render();
	}
}
