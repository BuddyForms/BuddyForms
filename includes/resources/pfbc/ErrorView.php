<?php

/**
 * Class ErrorView
 */
abstract class ErrorView extends Base {
	/**
	 * @var
	 */
	protected $_form;

	/**
	 * ErrorView constructor.
	 *
	 * @param array|null $properties
	 */
	public function __construct( array $properties = null ) {
		$this->configure( $properties );
	}

	public abstract function applyAjaxErrorResponse();

	public function clear() {
		echo 'jQuery("#', $this->_form->getAttribute( "id" ), ' .alert-error").remove();';
	}

	public abstract function render();

	public abstract function renderAjaxErrorResponse();

	public function renderCSS() {
	}

	/**
	 * @param Form $form
	 */
	public function _setForm( Form $form ) {
		$this->_form = $form;
	}
}
