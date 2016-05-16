<?php

// Generate the jquery validation js
function buddyforms_jquery_validation(){
	global $buddyforms;

	$form_html = '<script type="text/javascript">';
	$form_html .= ' var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";';

	foreach($buddyforms as $form_slug => $form) {

		// make the slug js conform
		$form_slug_js = str_replace( '-', '_', $form_slug );
		// Create the needed Validation JS.

		$form_html .= '
	    jQuery(function() {
	        var validator_' . $form_slug_js . ' = jQuery("#editpost_' . $form_slug . '").submit(function() {
	                if(jQuery(\'textarea\').length > 0) {
	                    // update underlying textarea before submit validation
	                    tinyMCE.triggerSave();
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
	                //jQuery("#editpost_title").prev().css(\'color\',\'red\');
	                label.insertBefore("#editpost_content");
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