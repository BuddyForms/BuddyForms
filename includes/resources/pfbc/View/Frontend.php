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
					echo '<div class="form-actions">';
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
		if ( $element instanceof Element_Hidden || $element instanceof Element_HTML ) {
			$element->render();

			return;
		}
		if ( ! $element instanceof Element_Radio && ! $element instanceof Element_Checkbox && ! $element instanceof Element_File ) {
			$element->appendAttribute( "class", "form-control" );
		}

		if ( $this->noLabel ) {
			$label = $element->getLabel();
			$element->setAttribute( "placeholder", $label );
			$element->setLabel( "" );
		}

		echo '<div class="bf_field_group elem-' . $element->getAttribute( "id" ) . '"> ', $this->renderLabel( $element );
		echo '<div class="bf-input">';
		echo $element->render(), $this->renderDescriptions( $element );
		echo "</div></div> ";
	}

	/**
	 * @param Element $element
	 */
	protected function renderLabel( Element $element ) {
		$label = $element->getLabel();
		if ( empty ( $label ) ) {
			$label = '';
		}
		echo ' <label for="', $element->getAttribute( "id" ), '">';
		if ( $element->isRequired() ) {
			echo '<span class="required">* </span> ';
		}
		echo $label, '</label> ';
	}
}
