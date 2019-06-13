<?php

/**
 * Class ErrorView_Standard
 */
class ErrorView_Standard extends ErrorView {
	public function render() {
		$global_error = ErrorHandler::get_instance();
		if ( $global_error->get_global_error()->has_errors() ) {
			$all_errors = $global_error->get_global_error()->errors;
			$size   = sizeof( $all_errors );
			$errors = implode( "</li><li>", $all_errors );

			ob_start();

			// create the plugin template path
			$template_path = BUDDYFORMS_TEMPLATE_PATH . 'buddyforms/bf-error-container.php';

			// Check if template exist in the child or parent theme and use this path if available
			if ( $template_file = locate_template( "buddyforms/bf-error-container.php", false, false ) ) {
				$template_path = $template_file;
			}

			// Do the include
			include $template_path;

			echo ob_get_clean();
		}
	}

	public function renderAjaxErrorResponse() {
		$global_error = ErrorHandler::get_instance();
		if ( $global_error->get_global_error()->has_errors() ) {
			header( "Content-type: application/json" );
			echo wp_json_encode( array( "errors" => $global_error->get_global_error()->errors) );
			die;
		}
	}

	public function renderCSS() {

	}
}
