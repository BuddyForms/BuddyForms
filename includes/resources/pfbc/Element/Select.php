<?php

/**
 * Class Element_Select
 */
class Element_Select extends OptionElement {
	/**
	 * @var array
	 */
	protected $_attributes = array();

	public function render() {
		$this->appendAttribute( 'class', 'c-select' );
		if ( isset( $this->_attributes['value'] ) ) {
			if ( ! is_array( $this->_attributes['value'] ) ) {
				$this->_attributes['value'] = array( $this->_attributes['value'] );
			}
		} else {
			$this->_attributes['value'] = array();
		}

		if ( ! empty( $this->_attributes['multiple'] ) && substr( $this->_attributes['name'], - 2 ) != '[]' ) {
			$this->_attributes['name'] .= '[]';
		}

		ob_start();
		$selected_count = array();
		echo sprintf( '<select %s>', wp_kses( $this->getAttributes( array( 'value', 'selected' ) ), buddyforms_wp_kses_allowed_atts() ) );
		if ( ! empty( $this->field_options ) && ! empty( $this->field_options['empty_option'] ) && $this->field_options['empty_option'] ) {
			echo '<option value=""></option>';
		}
		foreach ( $this->options as $value => $text ) {
			$value            = $this->getOptionValue( $value );
			$current_selected = in_array( $value, $this->_attributes['value'] );
			if ( $current_selected ) {
				$selected_count[] = $current_selected;
			}
			$selected = '';
			if ( empty( $this->_attributes['multiple'] ) ) {
				if ( count( $selected_count ) == 1 ) {
					$selected = selected( $current_selected, true, false );
				}
			} else {
				$selected = selected( $current_selected, true, false );
			}
			$option = sprintf( '<option value="%s" %s>%s</option>', $this->filter( $value ), $selected, $text );
			echo wp_kses( $option, buddyforms_wp_kses_allowed_atts() );
		}
		echo '</select>';
		$content = ob_get_clean();

		echo wp_kses( $content, buddyforms_wp_kses_allowed_atts() );
	}
}
