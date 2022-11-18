<?php

/**
 * Class Element_Checksort
 */
class Element_Checksort extends Element_Sort {
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

		$labelClass = $this->_attributes['type'];
		if ( ! empty( $this->inline ) ) {
			$labelClass .= '-inline';
		}

		$count    = 0;
		$existing = '';

		foreach ( $this->options as $value => $text ) {
			$value = $this->getOptionValue( $value );
			if ( ! empty( $this->inline ) && $count > 0 ) {
				echo ' ';
			}
			echo '<label class="', esc_attr( $labelClass ), '"><input id="', esc_attr( $this->_attributes['id'] ), '-', esc_attr( $count ), '"', wp_kses(
				$this->getAttributes(
					array(
						'id',
						'class',
						'value',
						'checked',
						'name',
						'onclick',
						'required',
					)
				),
				buddyforms_wp_kses_allowed_atts()
			), ' value="', esc_attr( $this->filter( $value ) ), '"';
			if ( in_array( $value, $this->_attributes['value'] ) ) {
				echo ' checked="checked"';
			}
			echo ' onclick="updateChecksort(this, \'', esc_js(
				str_replace(
					array( '"', "'" ),
					array(
						'&quot;',
						"\'",
					),
					$text
				)
			), '\');"/>', wp_kses( $text, buddyforms_wp_kses_allowed_atts() ), '</label>';

			if ( in_array( $value, $this->_attributes['value'] ) ) {
				$existing .= '<li id="' . $this->_attributes['id'] . '-sort-' . $count . '" class="ui-state-default"><input type="hidden" name="' . $this->_attributes['name'] . '" value="' . $value . '"/>' . $text . '</li>';
			}

			++ $count;
		}

		echo '<ul id="', esc_attr( $this->_attributes['id'] ), '">', wp_kses( $existing, buddyforms_wp_kses_allowed_atts() ), '</ul>';
	}

	function renderJS() {
		echo <<<JS
if(typeof updateChecksort != "function") {		
	function updateChecksort(element, text) {
		var position = element.id.lastIndexOf("-");
		var id = element.id.substr(0, position);
		var index = element.id.substr(position + 1);
		if(element.checked) {
			jQuery("#" + id).append('<li id="' + id + '-sort-' + index + '" class="ui-state-default"><input type="hidden" name="{$this->_attributes["name"]}" value="' + element.value + '"/>' + text + '</li>');
		}	
		else
			jQuery("#" + id + "-sort-" + index).remove();
	}
}
JS;
	}
}
