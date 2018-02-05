<?php

/**
 * Class Element_Radio
 */
class Element_Radio extends OptionElement {
	/**
	 * @var array
	 */
	protected $_attributes = array( "type" => "radio" );
	/**
	 * @var
	 */
	protected $inline;

	public function render() {
		$labelClass = $this->getAttribute( 'class' );
		if ( ! empty( $this->inline ) ) {
			$labelClass .= "radio-inline";
		}

		$count = 0;
		echo '<div class="radio">';
		foreach ( $this->options as $value => $text ) {
			$value = $this->getOptionValue( $value );

			echo '<label class="', $labelClass . '">';
			echo '<input id="', isset( $this->_attributes["id"] ) ? $this->_attributes["id"] : '', '-', $count, '"', $this->getAttributes( array(
				"id",
				"class",
				"value",
				"checked"
			) ), ' value="', $this->filter( $value ), '"';
			if ( isset( $this->_attributes["value"] ) && $this->_attributes["value"] == $value ) {
				echo ' checked="checked"';
			}
			echo '/> ', $text, ' </label> ';
			++ $count;
			if ( $labelClass != 'radio-inline' ) {
				echo '</div><div class="radio">';
			}
		}
		if ( $this->getAttribute( 'frontend_reset' ) ) {
			echo '<a href="#" class="button bf_reset_multi_input" data-group-name="' . $this->getAttribute( 'name' ) . '">Reset</a>';
		}
		echo '</div>';
	}
}
