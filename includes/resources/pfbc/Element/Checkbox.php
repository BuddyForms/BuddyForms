<?php

/**
 * Class Element_Checkbox
 */
class Element_Checkbox extends OptionElement {
	/**
	 * @var array
	 */
	protected $_attributes = array( 'type' => 'checkbox' );
	/**
	 * @var
	 */
	protected $inline;

	public function render() {
		if ( isset( $this->_attributes['value'] ) ) {
			if ( ! is_array( $this->_attributes['value'] ) ) {
				$this->_attributes['value'] = array( $this->_attributes['value'] );
			}
		} else {
			$this->_attributes['value'] = array();
		}

		if ( substr( $this->_attributes['name'], - 2 ) != '[]' ) {
			$this->_attributes['name'] .= '[]';
		}

		$labelClass = $this->getAttribute( 'class' );
		if ( ! empty( $this->inline ) ) {
			$labelClass .= ' checkbox-inline';
		}

		$count = 0;
		echo '<div class="checkbox">';
		foreach ( $this->options as $value => $text ) {
			$value = $this->getOptionValue( $value );

			// get optional ID
			$id = isset( $this->_attributes['id'] ) ? ' id="' . $this->_attributes['id'] . '-' . $count . '"' : '';

			echo '<label class="' , esc_attr( $labelClass ) , ' ' , esc_attr( $value ) , '">';

			echo '<input', wp_kses( $id, buddyforms_wp_kses_allowed_atts() ), wp_kses( $this->getAttributes(
				array(
					'id',
					'class',
					'value',
					'checked',
				)
				), buddyforms_wp_kses_allowed_atts() ), ' value="', esc_attr( $this->filter( $value ) ), '"';
			if ( in_array( $value, $this->_attributes['value'] ) ) {
				echo ' checked="checked"';
			}
			echo '/><span> ', wp_kses( $text, buddyforms_wp_kses_allowed_atts() ), '</span> </label> ';
			++ $count;
			if ( $labelClass != 'checkbox-inline' ) {
				echo '</div><div class="checkbox">';
			}
		}
		echo '';
		if ( $this->getAttribute( 'frontend_reset' ) ) {
			echo '<a href="#" class="button bf_reset_multi_input" data-group-name="' . esc_attr( $this->getAttribute( 'name' ) ) . '">' . esc_html__( 'Reset', 'buddyforms' ) . '</a>';
		}
		echo '</div>';
	}
}
