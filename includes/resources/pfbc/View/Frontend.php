<?php

/**
 * Class View_Frontend
 */
class View_Frontend extends FormView {
	/**
	 * @var string
	 */
	protected $class = "form-inline";

	/**
	 * @param null $onlyElement
	 */
	public function render( $onlyElement = null ) {
		global $buddyforms, $form_slug;


		$field_id     = $this->_form->getAttribute( "field_id" );
		$layout_style = buddyforms_layout_style( $field_id );

		if ( $this->class ) {
			$this->_form->appendAttribute( "class", $this->class );
		}

		$this->_form->getErrorView()->render();
		echo '<form ', $this->_form->getAttributes(), "><!--csrftoken--><fieldset> ";
		if ( $onlyElement && $onlyElement == 'open' ) {
			return;
		}

		$elements     = $this->_form->getElements();
		$elementSize  = sizeof( $elements );
		$elementCount = 0;
		for ( $e = 0; $e < $elementSize; ++ $e ) {
			$element = $elements[ $e ];

			if ( $element instanceof Element_Button ) {
				if ( $e == 0 || ! $elements[ ( $e - 1 ) ] instanceof Element_Button ) {
					echo '<div class="form-actions ' . $layout_style . '">';
				} else {
					echo ' ';
				}
				$element->render();
				if ( ( $e + 1 ) == $elementSize || ! $elements[ ( $e + 1 ) ] instanceof Element_Button ) {
					echo '</div>';
				}

			} else {
				$this->renderElement( $element );
			}

			++ $elementCount;
		}

		$this->renderFormClose();
	}

	/**
	 * @param $element
	 */
	public function renderElement( $element ) {
		global $form_slug, $buddyforms;

		$field_id = $element->getAttribute( "field_id" );

		$layout_style = buddyforms_layout_style( $field_id );

		if ( $element instanceof Element_Hidden ) {
			$element->render();

			return;
		}

		if ( $element instanceof Element_INLINE_HTML ) {
			$element->render();

			return;
		}

		echo '<div class="' . $layout_style . '">';

		if ( $element instanceof Element_HTML ) {
			$element->render();
			echo "</div>";

			return;
		}


		if ( ! $element instanceof Element_Radio && ! $element instanceof Element_Checkbox && ! $element instanceof Element_File ) {

			$element->appendAttribute( "class", "form-control" );

			if ( isset( $buddyforms[ $form_slug ]['layout']['labels_layout'] ) && $buddyforms[ $form_slug ]['layout']['labels_layout'] == 'inline' ) {

				if ( empty( $label ) ) {
					$label = $element->getLabel();
				}

				if ( $element->isRequired() ) {
					$label = $label . ' *';
				}

				$element->setAttribute( "placeholder", $label );
				$element->setLabel( "" );
			}
		}

		echo '<div class="bf_field_group elem-' . $element->getAttribute( "id" ) . '"> ', $this->renderLabel( $element ), '<div class="bf-input">';
		if ( isset( $buddyforms[ $form_slug ]['layout']['desc_position'] ) && $buddyforms[ $form_slug ]['layout']['desc_position'] == 'above_field' ) {
			echo $this->renderDescriptions( $element );
			echo $element->render();
		} else {
			echo $element->render();
			echo $this->renderDescriptions( $element );
		}
		echo "</div></div></div>";
	}

	/**
	 * @param Element $element
	 */
	protected function renderLabel( Element $element ) {
		global $form_slug, $buddyforms;

		$label = $element->getLabel();
		if ( empty ( $label ) ) {
			$label = '';
		}
		echo ' <label for="', $element->getAttribute( "id" ), '">';

		echo $label;

		if ( isset( $buddyforms[ $form_slug ]['layout']['labels_layout'] ) && $buddyforms[ $form_slug ]['layout']['labels_layout'] != 'inline' ) {

			if ( $element->isRequired() ) {
				echo '<span class="required"> *</span> ';
			}
		}
		echo '</label> ';
	}
}


function buddyforms_layout_style( $field_id ) {
	global $buddyforms, $form_slug;

	$layout_style = isset( $buddyforms[ $form_slug ]['layout']['cords'][ $field_id ] ) ? $buddyforms[ $form_slug ]['layout']['cords'][ $field_id ] : '1';

	switch ( $layout_style ) {
		case '1' :
			$layout_style = 'col-xs-12';
			break;
		case '2' :
			$layout_style = 'col-xs-12 col-md-6';
			break;
		case '3' :
			$layout_style = 'col-xs-12 col-md-4';
			break;
		case '4' :
			$layout_style = 'col-xs-12 col-md-3';
			break;
		case '5' :
			$layout_style = 'col-xs-12 col-md-8';
			break;
		case '6' :
			$layout_style = 'col-xs-12 col-md-9';
			break;
		default:
			$layout_style = 'col-xs-12';
			break;
	}

	return apply_filters( 'buddyforms_layout_style', $layout_style, $field_id );
}
