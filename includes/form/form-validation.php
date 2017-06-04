<?php

/*
 * The default validation is already covert. Let us do some extra work and also do the advanced validation server site.
 */

add_filter( 'buddyforms_form_custom_validation', 'buddyforms_server_validation', 2, 2 );
/**
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

			if ( isset( $form_field['validation_min'] ) && $form_field['validation_min'] > 0 ) {
				if ( ! is_numeric( $_POST[ $form_field['slug'] ] ) || $_POST[ $form_field['slug'] ] < $form_field['validation_min'] ) {
					$valid                    = false;
					$validation_error_message = __( 'Please enter a value greater than or equal to ', 'buddyforms' ) . $form_field['validation_min'];
					Form::setError( 'buddyforms_form_' . $form_slug, $validation_error_message );
				}
			}

			if ( isset( $form_field['validation_max'] ) && $form_field['validation_max'] > 0 ) {
				if ( ! is_numeric( $_POST[ $form_field['slug'] ] ) || $_POST[ $form_field['slug'] ] > $form_field['validation_max'] ) {
					$valid                    = false;
					$validation_error_message = __( 'Please enter a value less than or equal to ', 'buddyforms' ) . $form_field['validation_max'];
					Form::setError( 'buddyforms_form_' . $form_slug, $validation_error_message );
				}
			}

			if ( isset( $form_field['validation_minlength'] ) && $form_field['validation_minlength'] > 0 ) {
				if ( strlen( trim( $_POST[ $form_field['slug'] ] ) ) < $form_field['validation_minlength'] ) {
					$valid                    = false;
					$validation_error_message = sprintf( __( 'Please enter at least %d characters.', 'buddyforms' ), $form_field['validation_minlength'] );
					Form::setError( 'buddyforms_form_' . $form_slug, $validation_error_message );
				}
			}

			if ( isset( $form_field['validation_maxlength'] ) && $form_field['validation_maxlength'] > 0 ) {
				if ( strlen( trim( $_POST[ $form_field['slug'] ] ) ) > $form_field['validation_maxlength'] ) {
					$valid                    = false;
					$validation_error_message = sprintf( __( 'Please enter no more than %d characters.', 'buddyforms' ), $form_field['validation_maxlength'] );
					Form::setError( 'buddyforms_form_' . $form_slug, $validation_error_message );
				}
			}

		}

	endif;

	return $valid;
}

/* First Browser Validation
 *
 * Generate the jquery validation js
 * hooked into wp_head in buddyforms/buddyforms.php function buddyform_front_js to make sure it only gets loaded if a form is displayed
 *
 * todo: first I thought its best practice to make the jquery work loaded in the wp_head. Now starting to use more and more react js I ask my self the question, why not add it inline to the form where it is used. I think it could be possible I switch from the global instance. If you read this and have some thoughts. please let me know @svenl77 ;)
 *
 */

function buddyforms_jquery_validation() {
	global $buddyforms;

	if( ! isset( $buddyforms ) || ! is_array( $buddyforms ) ){
		return;
	}

	$form_html = '<script type="text/javascript">';
	$form_html .= ' var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";';

	foreach ( $buddyforms as $form_slug => $form ) {

		// make the slug js conform
		$form_slug_js = str_replace( '-', '_', $form_slug );

		// Create the needed Validation JS.
		$form_html .= '
	    jQuery(function() {
	        var validator_' . $form_slug_js . ' = jQuery("#buddyforms_form_' . $form_slug . '").submit(function() {
	                if(jQuery(\'textarea\').length > 0) {
	                	// if TinyMCE is enabled
	                    if ( "undefined" !== typeof tinyMCE ) {
		                    // update underlying textarea before submit validation
		                    tinyMCE.triggerSave();
		                }
	                }

	        }).validate({
	        ignore: [],
	        rules: {
	        ';

		if ( isset( $form['form_fields'] ) ) :
			foreach ( $form['form_fields'] as $key => $form_field ) {
				if ( isset( $form_field['required'] ) ) {

					$field_slug = str_replace( "-", "", $form_field['slug'] );
					if ( $field_slug ) :
						$form_html .= $field_slug . ': { required: true,';

						if ( isset( $form_field['validation_min'] ) && $form_field['validation_min'] > 0 ) {
							$form_html .= 'min: ' . $form_field['validation_min'] . ',';
						}

						if ( isset( $form_field['validation_max'] ) && $form_field['validation_max'] > 0 ) {
							$form_html .= 'max: ' . $form_field['validation_max'] . ',';
						}

						if ( isset( $form_field['validation_minlength'] ) && $form_field['validation_minlength'] > 0 ) {
							$form_html .= 'minlength: ' . $form_field['validation_minlength'] . ',';
						}

						if ( isset( $form_field['validation_maxlength'] ) && $form_field['validation_maxlength'] > 0 ) {
							$form_html .= 'maxlength: ' . $form_field['validation_maxlength'] . ',';
						}

						//	$form_html .= 'valueNotEquals: "-1",'; ... 	valueNotEquals: "Please select an item!",

						$form_html .= '},
						';
					endif;
				}
			}
		endif;

		$form_html .= '},
	        messages: {
	            ';
		if ( isset( $buddyforms[ $form_slug ]['form_fields'] ) ) : foreach ( $buddyforms[ $form_slug ]['form_fields'] as $key => $form_field ) {
			if ( isset( $form_field['required'] ) ) {

				$validation_error_message = __( 'This field is required.', 'buddyforms' );
				if ( isset( $form_field['validation_error_message'] ) ) {
					$validation_error_message = $form_field['validation_error_message'];
				}

				$field_slug = str_replace( "-", "", $form_field['slug'] );
				if ( $field_slug ) :
					$form_html .= $field_slug . ': {
	                        required: "' . $validation_error_message . '",
	                    },';
				endif;
			}
		}
		endif;
		$form_html .= '},';

		$form_html .= 'errorPlacement: function(label, element) {
	            // position error label after generated textarea
	            if (element.is("textarea")) {
	                label.insertBefore("#buddyforms_form_content");
	            } else if(element.is("input[type=\"radio\"]")) {
	                label.insertBefore(element)
	            } else {
	                label.insertAfter(element)
	            }
	        }
	    });});
	    ';
	}
	$form_html .= '</script>';
	echo $form_html;
}

function buddyforms_sanitize( $type, $value){

	switch( $type ){
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
			$value  = apply_filters( 'buddyforms_sanitize', $value, $type );
			break;

	}
	return $value;
}
