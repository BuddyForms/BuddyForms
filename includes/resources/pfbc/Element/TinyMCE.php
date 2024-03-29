<?php

/**
 * Class Element_TinyMCE
 */
class Element_TinyMCE extends Element_Textarea {
	/**
	 * @var
	 */
	protected $basic;

	public function render() {
		echo '<textarea', wp_kses( $this->getAttributes( array( 'value', 'required' ) ), buddyforms_wp_kses_allowed_atts() ), '>';
		if ( ! empty( $this->_attributes['value'] ) ) {
			echo wp_kses( $this->_attributes['value'], buddyforms_wp_kses_allowed_atts() );
		}
		echo '</textarea>';
	}

	function renderJS() {
		$id     = $this->_form->getAttribute( 'id' );
		$formID = '#' . $id . ' #' . $this->_attributes['id'];
		echo 'tinymce.init({selector: "', esc_js( $formID ), '", width: "100%"';
		/*
				if(!empty($this->basic))
					echo ', theme: "simple"';
				else
					echo ', theme: "advanced", theme_advanced_resizing: true';
		*/
		echo '});';

		$ajax = $this->_form->getAjax();
		if ( ! empty( $ajax ) ) {
			echo 'jQuery("#$id").on("submit", function() { tinyMCE.triggerSave(); });';
		}
	}

	/**
	 * @return array
	 */
	function getJSFiles() {
		return array(
			'//tinymce.cachefly.net/4.2/tinymce.min.js',
		);
	}
}
