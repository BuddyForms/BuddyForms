<?php

/**
 * Class Element_Sort
 */
class Element_Sort extends OptionElement {
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

	/**
	 * @return array
	 */
	public function getJSFiles() {
		return array(
		//	$this->_form->getPrefix() . '://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js',
		);
	}

	public function jQueryDocumentReady() {
		echo 'jQuery("#', esc_js( $this->_attributes['id'] ), '").sortable(', esc_js( $this->jQueryOptions() ), ');';
		echo 'jQuery("#', esc_js( $this->_attributes['id'] ), '").disableSelection();';
	}

	public function render() {
		if ( substr( $this->_attributes['name'], - 2 ) != '[]' ) {
			$this->_attributes['name'] .= '[]';
		}

		echo '<ul id="', esc_attr( $this->_attributes['id'] ), '">';
		foreach ( $this->options as $value => $text ) {
			$value = $this->getOptionValue( $value );
			echo '<li class="ui-state-default"><input type="hidden" name="', esc_attr( $this->_attributes['name'] ), '" value="', esc_attr( $value ), '"/>', wp_kses( $text, buddyforms_wp_kses_allowed_atts() ), '</li>';
		}
		echo '</ul>';
	}

	public function renderCSS() {
		echo '#', wp_kses( $this->_attributes['id'], buddyforms_wp_kses_allowed_atts() ), ' { list-style-type: none; margin: 0; padding: 0; cursor: pointer; max-width: 400px; }';
		echo '#', wp_kses( $this->_attributes['id'], buddyforms_wp_kses_allowed_atts() ), ' li { margin: 0.25em 0; padding: 0.5em; font-size: 1em; }';
	}
}
