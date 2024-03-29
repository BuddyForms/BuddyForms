<?php

/**
 * Class View_SideBySide4
 */
class View_SideBySide4 extends FormView {
	/**
	 * @var int
	 */
	private $sharedCount = 0;

	/**
	 * @param Element $element
	 */
	public function renderElement( $element ) {
		$colSize                   = 'col-xs-12 col-md-8';
		$element->bootstrapVersion = 4;

		if ( $element instanceof Element_Hidden || $element instanceof Element_HTML || $element instanceof Element_Button ) {
			$element->render();

			return;
		}
		if ( ! $element instanceof Element_Radio && ! $element instanceof Element_Checkbox && ! $element instanceof Element_File ) {
			$element->appendAttribute( 'class', 'form-control' );
		}

		$attr_error = $element->getAttribute( 'error' );
		$opt_error  = $element->getOption( 'error' );
		if ( ! empty( $attr_error ) || ! empty( $opt_error ) ) {
			$element->appendAttribute( 'class', 'error' );
		}

		if ( $this->noLabel ) {
			$label = $element->getLabel();
			$element->setAttribute( 'placeholder', $label );
			$element->setLabel( '' );
		}

		if ( $this->sharedCount == 0 ) {
			echo '<div class="row form-group elem-' . esc_attr( $element->getAttribute( 'id' ) ) . '"> ', wp_kses( $this->renderLabel( $element ), buddyforms_wp_kses_allowed_atts() );
		}

		if ( $element->getShared() ) {
			$colSize            = $element->getShared();
			$this->sharedCount += $colSize[ strlen( $colSize ) - 1 ];
		}

		echo wp_kses( " <div class='$colSize'> ", buddyforms_wp_kses_allowed_atts() );
		echo wp_kses( $element->render(), $this->renderDescriptions( $element ), buddyforms_wp_kses_allowed_atts() );
		echo ' </div> ';

		if ( $this->sharedCount == 0 || $this->sharedCount == 8 ) {
			$this->sharedCount = 0;
			echo ' </div> ';
		}
	}

	/**
	 * @param Element $element
	 */
	protected function renderLabel( Element $element ) {
		if ( $this->noLabel ) {
			echo ' ';

			return;
		}

		$label = $element->getLabel();

		// TODO improve required flag position
		if ( ! $this->noLabel && $element->isRequired() ) {
			$label = $label . $this->renderRequired();
		}

		echo wp_kses( sprintf( '<label class="text-right-lg col-xs-12 col-md-4 form-control-label" for="%s">%s</label>', $element->getAttribute( 'id' ), $label ), buddyforms_wp_kses_allowed_atts() );
	}

	public function renderCSS() {
		parent::renderCSS();
		echo '@media (min-width: 760px) { .text-right-lg { text-align: right !important; }}';
	}
}
