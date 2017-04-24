<?php

/**
 * Class Element_Color
 */
class Element_Color extends OptionElement {
	/**
	 * @var array
	 */
	protected $_attributes = array( "type" => "color" );

	public function render() {

//		if ( isset( $this->_attributes["value"] ) ) {
//			if ( ! is_array( $this->_attributes["value"] ) ) {
//				$this->_attributes["value"] = array( $this->_attributes["value"] );
//			}
//		} else {
//			$this->_attributes["value"] = array();
//		}

//		if ( substr( $this->_attributes["name"], - 2 ) != "[]" ) {
//			$this->_attributes["name"] .= "[]";
//		}




		print_r($this); // $this->_attributes["value"];


		$this->_attributes["name"] = $this->_attributes["name"] . '[color]';
		$this->_attributes["pattern"] = "#[a-g0-9]{6}";
		$this->_attributes["title"]   = "6-digit hexidecimal color (e.g. #000000)";
		$this->validation[]           = new Validation_RegExp( "/" . $this->_attributes["pattern"] . "/", "Error: The %element% field must contain a " . $this->_attributes["title"] );
		parent::render();

		$style = str_replace( '[color]', '[style]', $this->_attributes["name"] );

		echo '
		<p style="display: inline-block; font-size: 11px; line-height: 2.5;">
		<input id="" type="radio" name="' . $style . '" value="auto"> Auto <br>
		<input id="" type="radio" name="' . $style . '" value="transparent"> Transparent
		</p>';





	}
}
