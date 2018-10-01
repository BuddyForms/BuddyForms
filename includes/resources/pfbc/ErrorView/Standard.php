<?php

/**
 * Class ErrorView_Standard
 */
class ErrorView_Standard extends ErrorView {
	public function applyAjaxErrorResponse() {
		$id                    = $this->_form->getAttribute( "id" );
		$error_string_start    = __( 'The following', 'buddyforms' );
		$error_string_singular = __( 'error was', 'buddyforms' );
		$error_string_plural   = __( 'errors were', 'buddyforms' );
		$error_string_end      = __( 'found: ', 'buddyforms' );
		echo <<<JS
        var errorSize = response.errors.length;
        if(errorSize == 1)
            var errorFormat = "$error_string_singular";
        else
            var errorFormat = errorSize + " $error_string_plural";

        jQuery('.bf-alert').remove();
        var errorHTML = '<div class="bf-alert error"><strong class="alert-heading">$error_string_start ' + errorFormat + ' $error_string_end</strong><ul>';
        for(e = 0; e < errorSize; ++e)
            errorHTML += '<li>' + response.errors[e] + '</li>';
        errorHTML += '</ul></div>';
        jQuery("#$id").prepend(errorHTML);
JS;

	}

	public function render() {
		$errors = $this->_form->getErrors();
		if ( ! empty( $errors ) ) {
			$size   = sizeof( $errors );
			$errors = implode( "</li><li>", $errors );

			$error_heading_text = _n( 'The following error was found:', 'The following errors were found:', $size, 'buddyforms' );

			echo <<<HTML
            <div class="bf-alert error">
                <strong class="alert-heading">$error_heading_text</strong>
                <ul><li>$errors</li></ul>
            </div>
HTML;
		}
	}

	public function renderAjaxErrorResponse() {
		$errors = $this->_form->getErrors();
		if ( ! empty( $errors ) ) {
			header( "Content-type: application/json" );
			echo json_encode( array( "errors" => $errors ) );
			die;
		}
	}

	/**
	 * @param $errors
	 *
	 * @return array
	 */
	private function parse( $errors ) {

		$list = array();
		if ( ! empty( $errors ) ) {
			$keys    = array_keys( $errors );
			$keySize = sizeof( $keys );
			for ( $k = 0; $k < $keySize; ++ $k ) {
				$list = array_merge( $list, $errors[ $keys[ $k ] ] );
			}
		}

		return $list;
	}
}
