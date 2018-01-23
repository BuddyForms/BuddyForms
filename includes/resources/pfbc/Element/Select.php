<?php

/**
 * Class Element_Select
 */
class Element_Select extends OptionElement {
	/**
	 * @var array
	 */
	protected $_attributes = array();

	public function jQueryDocumentReady() {
		echo 'jQuery("select[field_id=\''+$this->_attributes["field_id"]+'\']").select2({
	       placeholder: "Select an option",
	       tags: true,
	       allowClear: true,
	       tokenSeparators: [\',\', \' \']
	    });
	    ';
	}

	public function render() {
		$this->appendAttribute( 'class', 'c-select' );
		if ( isset( $this->_attributes["value"] ) ) {
			if ( ! is_array( $this->_attributes["value"] ) ) {
				$this->_attributes["value"] = array( $this->_attributes["value"] );
			}
		} else {
			$this->_attributes["value"] = array();
		}

		if ( ! empty( $this->_attributes["multiple"] ) && substr( $this->_attributes["name"], - 2 ) != "[]" ) {
			$this->_attributes["name"] .= "[]";
		}

		echo '<select', $this->getAttributes( array( "value", "selected" ) ), '>';
		$selected = false;
		foreach ( $this->options as $value => $text ) {
			$value = $this->getOptionValue( $value );
			echo '<option value="', $this->filter( $value ), '"';
			if ( in_array( $value, $this->_attributes["value"] ) ) {
				if ( $selected && empty ( $this->_attributes["multiple"] ) ) {
					continue;
				}
				echo ' selected="selected"';
				$selected = true;
			}
			echo '>', $text, '</option>';
		}
		echo '</select>';
	}
}
