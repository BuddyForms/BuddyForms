<?php

/**
 * Class View_Vertical
 */
class View_Vertical extends FormView {
	/**
	 * @var int
	 */
	private $sharedCount = 0;

	/**
	 * @param $element
	 */
	public function renderElement( $element ) {
		if ( $element instanceof Element_Hidden || $element instanceof Element_HTML || $element instanceof Element_Button ) {
			$element->render();

			return;
		}
		if ( ! $element instanceof Element_Radio && ! $element instanceof Element_Checkbox && ! $element instanceof Element_File ) {
			$element->appendAttribute( "class", "form-control" );
		}

		if ( $this->sharedCount == 0 ) {
			$rowClass = $element->getShared() ? 'row' : '';
			echo '<div class="' . $rowClass . ' bf_field_group elem-' . $element->getAttribute( "id" ) . '"> ', $this->renderLabel( $element );
		}

		if ( $element->getShared() ) {
			$colSize           = $element->getShared();
			$this->sharedCount += $colSize[ strlen( $colSize ) - 1 ];
			echo " <div class='$colSize'> ";
		}

		$element->setAttribute( 'placeholder', $element->getLabel() );
		echo $element->render(), $this->renderDescriptions( $element );
		if ( $element->getShared() ) {
			echo " </div> ";
		}

		if ( $this->sharedCount == 0 || $this->sharedCount == 12 ) {
			$this->sharedCount = 0;
			echo " </div> ";
		}
	}

	/**
	 * @param Element $element
	 */
	protected function renderLabel( Element $element ) {
	}
}
