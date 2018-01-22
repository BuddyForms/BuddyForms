<?php

/*
 * The default validation is already covert. Let us do some extra work and also do the advanced validation server site.
 */

add_filter( 'buddyforms_form_custom_validation', 'buddyforms_server_validation', 2, 2 );
/**
 *
 * Server Site Validation
 *
 * @param $valid
 * @param $form_slug
 *
 * @return bool
 */
function buddyforms_server_validation( $valid, $form_slug ) {
	global $buddyforms;

	$form = $buddyforms[ $form_slug ];

	if ( isset( $form['form_fields'] ) ) :
		foreach ( $form['form_fields'] as $key => $form_field ) {

			if ( isset( $form_field['validation_min'] ) && $form_field['validation_min'] > 0 && isset( $form_field['validation_max'] ) && $form_field['validation_max'] > 0 ) {
				if ( ! is_numeric( $_POST[ $form_field['slug'] ] ) || ( ( $form_field['validation_min'] === $form_field['validation_max'] ) && $_POST[ $form_field['slug'] ] !== $form_field['validation_min'] ) ) {
					$valid                    = false;
					$validation_error_message = __( 'Please enter a value equal to ', 'buddyforms' ) . $form_field['validation_min'];
					Form::setError( 'buddyforms_form_' . $form_slug, $validation_error_message, $form_field['name'] );
				}
			}

			if ( isset( $form_field['validation_min'] ) && $form_field['validation_min'] > 0 ) {
				if ( ! is_numeric( $_POST[ $form_field['slug'] ] ) || ( ( $form_field['validation_min'] !== $form_field['validation_max'] ) && $_POST[ $form_field['slug'] ] < $form_field['validation_min'] ) ) {
					$valid                    = false;
					$validation_error_message = __( 'Please enter a value greater than or equal to ', 'buddyforms' ) . $form_field['validation_min'];
					Form::setError( 'buddyforms_form_' . $form_slug, $validation_error_message, $form_field['name'] );
				}
			}

			if ( isset( $form_field['validation_max'] ) && $form_field['validation_max'] > 0 ) {
				if ( ! is_numeric( $_POST[ $form_field['slug'] ] ) || ( ( $form_field['validation_min'] !== $form_field['validation_max'] ) && $_POST[ $form_field['slug'] ] > $form_field['validation_max'] ) ) {
					$valid                    = false;
					$validation_error_message = __( 'Please enter a value less than or equal to ', 'buddyforms' ) . $form_field['validation_max'];
					Form::setError( 'buddyforms_form_' . $form_slug, $validation_error_message, $form_field['name'] );
				}
			}

			if ( isset( $form_field['validation_minlength'] ) && $form_field['validation_minlength'] > 0 ) {
				if ( strlen( trim( $_POST[ $form_field['slug'] ] ) ) < $form_field['validation_minlength'] ) {
					$valid                    = false;
					$validation_error_message = sprintf( __( 'Please enter at least %d characters.', 'buddyforms' ), $form_field['validation_minlength'] );
					Form::setError( 'buddyforms_form_' . $form_slug, $validation_error_message, $form_field['name'] );
				}
			}

			if ( isset( $form_field['validation_maxlength'] ) && $form_field['validation_maxlength'] > 0 ) {
				if ( strlen( trim( $_POST[ $form_field['slug'] ] ) ) > $form_field['validation_maxlength'] ) {
					$valid                    = false;
					$validation_error_message = sprintf( __( 'Please enter no more than %d characters.', 'buddyforms' ), $form_field['validation_maxlength'] );
					Form::setError( 'buddyforms_form_' . $form_slug, $validation_error_message, $form_field['name'] );
				}
			}

		}

	endif;

	return $valid;
}

/*
 * Browser Validation - Generate the jquery validation js
 *
 */
function buddyforms_jquery_validation() {
	global $buddyforms;

	if ( ! isset( $buddyforms ) || ! is_array( $buddyforms ) ) {
		return;
	}

	$form_html = '<script type="text/javascript">';
	$form_html .= ' var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";
	';

	foreach ( $buddyforms as $form_slug => $form ) {
		// Create the needed Validation JS.
		$form_html .= '
	    jQuery(function() {
	        jQuery("#buddyforms_form_' . $form_slug . '").submit(function(){}).validate({
	            errorPlacement: function(label, element) {
		            if (element.is("TEXTAREA")) {
		                label.insertAfter(element);
		            } else if(element.is("input[type=\"radio\"]")) {
		                label.insertBefore(element);
		            } else {
		                label.insertAfter(element);
		            }
	            }
	        }); setTimeout(function() {';

		if ( isset( $form['form_fields'] ) ) :
			foreach ( $form['form_fields'] as $key => $form_field ) {
				if ( isset( $form_field['required'] ) ) {

					$form_html .= '
				jQuery("form [name=\'' . $form_field['slug'] . '\']").rules("add", { ';

					$form_html .= 'required: true, ';

					if ( isset( $form_field['validation_min'] ) && $form_field['validation_min'] > 0 ) {
						$form_html .= 'min: ' . $form_field['validation_min'] . ', ';
					}

					if ( isset( $form_field['validation_max'] ) && $form_field['validation_max'] > 0 ) {
						$form_html .= 'max: ' . $form_field['validation_max'] . ', ';
					}

					if ( isset( $form_field['validation_minlength'] ) && $form_field['validation_minlength'] > 0 ) {
						$form_html .= 'minlength: ' . $form_field['validation_minlength'] . ', ';
					}

					if ( isset( $form_field['validation_maxlength'] ) && $form_field['validation_maxlength'] > 0 ) {
						$form_html .= 'maxlength: ' . $form_field['validation_maxlength'] . ', ';
					}

					$validation_error_message = isset( $form_field['validation_error_message'] ) ? $form_field['validation_error_message'] : __( 'This field is required.', 'buddyforms' );
					$form_html                .= ' messages:{ required: "' . $validation_error_message . '" }';
					$form_html                .= '});';
				}
			}
		endif;

		$form_html .= '
		}); }, 0);';

	}
	$form_html .= '
	</script>';
	echo $form_html;
}

function buddyforms_sanitize( $type, $value ) {

	switch ( $type ) {
		case 'subject':
			$value = sanitize_text_field( $value );
			break;
		case 'message':
			$value = esc_textarea( $value );
			break;
		case 'user_login':
			$value = sanitize_user( $value );
			break;
		case 'user_email':
			$value = sanitize_email( $value );
			break;
		case 'user_first':
			$value = sanitize_text_field( $value );
			break;
		case 'user_last':
			$value = sanitize_text_field( $value );
			break;
		case 'user_pass':
			$value = esc_attr( $value );
			break;
		case 'user_website':
			$value = esc_url( $value );
			break;
		case 'user_bio':
			$value = esc_textarea( $value );
			break;
		case 'number':
			$value = is_numeric( $value ) ? $value : 0;
			break;
		case 'title':
			$value = sanitize_title( $value );
			break;
		case 'content':
			$value = esc_textarea( $value );
			break;
		case 'mail':
			$value = sanitize_email( $value );
			break;
		case 'textarea':
			$value = esc_textarea( $value );
			break;
		case 'text':
			$value = sanitize_text_field( $value );
			break;
		case 'link':
			$value = esc_url( $value );
			break;
		default :
			$value = apply_filters( 'buddyforms_sanitize', $value, $type );
			break;

	}

	return $value;
}
