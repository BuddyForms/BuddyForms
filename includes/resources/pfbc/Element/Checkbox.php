<?php

/**
 * Class Element_Checkbox
 */
class Element_Checkbox extends OptionElement {
	/**
	 * @var array
	 */
	protected $_attributes = array( "type" => "checkbox" );
	/**
	 * @var
	 */
	protected $inline;

	public function render() {
		if ( isset( $this->_attributes["value"] ) ) {
			if ( ! is_array( $this->_attributes["value"] ) ) {
				$this->_attributes["value"] = array( $this->_attributes["value"] );
			}
		} else {
			$this->_attributes["value"] = array();
		}

		if ( substr( $this->_attributes["name"], - 2 ) != "[]" ) {
			$this->_attributes["name"] .= "[]";
		}

		$labelClass = $this->getAttribute( 'class' );
		if ( ! empty( $this->inline ) ) {
			$labelClass .= "checkbox-inline";
		}

		$count = 0;
		echo '<div class="bf-input"><div class="checkbox">';
		foreach ( $this->options as $value => $text ) {
			$value = $this->getOptionValue( $value );

			// get optional ID
			$id = isset( $this->_attributes["id"] ) ? ' id="' . $this->_attributes["id"] . '-' . $count . '"' : '';

			echo '<label class="', $labelClass, '">';
			echo '<input', $id, $this->getAttributes( array(
				"id",
				"class",
				"value",
				"checked"
			) ), ' value="', $this->filter( $value ), '"';
			if ( in_array( $value, $this->_attributes["value"] ) ) {
				echo ' checked="checked"';
			}
			echo '/><span> ', $text, '</span> </label> ';
			++ $count;
			if ( $labelClass != 'checkbox-inline' ) {
				echo '</div><div class="checkbox">';
			}
		}
		echo '</div>';
		if ( $this->getAttribute( 'frontend_reset' ) ) {
			echo '<a href="#" class="button bf_reset_multi_input" data-group-name="' . $this->getAttribute( 'name' ) . '">Reset</a>';
		}
		echo '</div>';
	}
}
