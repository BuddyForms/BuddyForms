<?php
/** @var $bfdesign array Form design option */
/** @var $form_slug string Form slug */
/** @var $is_registration_form bool Determinate if the current form is a registration form */
/** @var $need_registration_form bool Determinate if the current form need a registration form include */
$css_form_id    = 'buddyforms_form_' . $form_slug;
$css_form_class = 'buddyforms-' . $form_slug;
?>
<style type="text/css" <?php echo apply_filters( 'buddyforms_add_form_style_attributes', '', $css_form_id ); ?>>
	<?php
			// only output CSS for labels if the option to disable CSS is unchecked
	if( $bfdesign['labels_disable_css'] == '' ) { ?>
	/* Design Options - Labels */
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group label {
		margin-right: 10px !important;
	<?php
		// Font Size
		if( $bfdesign['label_font_size'] != '' ) {
			echo 'font-size: ' . $bfdesign['label_font_size'] . 'px !important;';
		}
		// Font Color
		if( $bfdesign['label_font_color']['style'] == 'color' ) {
			echo 'color: ' . $bfdesign['label_font_color']['color'] . ' !important;';
		}
		// Font Weight
		if( $bfdesign['label_font_style'] == 'bolditalic' || $bfdesign['label_font_style'] == 'bold' ) {
			echo 'font-weight: bold !important;';
		} else {
			echo 'font-weight: normal !important;';
		}
		// Font Style
		if ( $bfdesign['label_font_style'] == 'bolditalic' || $bfdesign['label_font_style'] == 'italic' ) {
			echo 'font-style: italic !important;';
		} else {
			echo 'font-style: normal !important;';
		} ?>;
	}

	<?php } ?>

	<?php
	// only output CSS for these form elements if the option to disable CSS is unchecked
	if( $bfdesign['other_elements_disable_css'] == '' ) { ?>
	/* Design Options - Form Elements */
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .radio {
		display: <?php echo esc_attr( $bfdesign['radio_button_alignment']); ?>;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .checkbox {
		display: <?php echo esc_attr( $bfdesign['checkbox_alignment']); ?>;
	}

	<?php } ?>

	<?php
	// only output CSS for form elements if the option to disable CSS is unchecked
	if( empty($bfdesign['field_disable_css']) ) { ?>
	/* Design Options - Text Fields */
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input input[type="range"] {
		width: 100% !important;
		padding-left: 0 !important;
		padding-right: 0 !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input textarea,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .form-control {
		display: block !important;
		width: 100% !important;

	<?php
			// Padding
			if( !empty($bfdesign['field_padding']) ) {
				echo 'padding: ' . $bfdesign['field_padding'] . 'px !important;';
			} else {
				echo 'padding-left: 5px !important;';
			}
			// Background Color
			if( $bfdesign['field_background_color']['style'] == 'color' ) {
				echo 'background-color: ' . $bfdesign['field_background_color']['color'] . ' !important;';
			} elseif( $bfdesign['field_background_color']['style'] == 'transparent') {
				echo 'background-color: transparent !important;';
			}
			// Border Color
			if( $bfdesign['field_border_color']['style'] == 'color') {
				echo 'border-color: ' . $bfdesign['field_border_color']['color'] . ' !important;';
			} elseif( $bfdesign['field_border_color']['style'] == 'transparent') {
				echo 'border-color: transparent !important;';
			}
			// Border Width
			if( $bfdesign['field_border_width'] != '' ) {
				echo 'border-width: ' . $bfdesign['field_border_width'] . 'px !important; border-style: solid !important;';
			}
			// Border Radius
			if( $bfdesign['field_border_radius'] != '' ) {
				echo 'border-radius: ' . $bfdesign['field_border_radius'] . 'px !important;';
			}
			// Font Size
			if( $bfdesign['field_font_size'] != '' ) {
				echo 'font-size: ' . $bfdesign['field_font_size'] . 'px !important;';
			}
			// Font Color
			if( $bfdesign['field_font_color']['style'] == 'color' ) {
				echo 'color: ' . $bfdesign['field_font_color']['color'] . ' !important;';
			} ?> min-height: 40px;
	}

	/* Design Options - Text Fields Active */
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input textarea:focus,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .form-control:focus {
	<?php
			// Background Color
			if( $bfdesign['field_active_background_color']['style'] == 'color' ) {
				echo 'background-color: ' . $bfdesign['field_active_background_color']['color'] . ' !important;';
			} elseif( $bfdesign['field_active_background_color']['style'] == 'transparent' ) {
				echo 'background-color: transparent !important;';
			}
			// Border Color
			if( $bfdesign['field_active_border_color']['style'] == 'color' ) {
				echo 'border-color: ' . $bfdesign['field_active_border_color']['color'] . ' !important;';
			} elseif( $bfdesign['field_active_border_color']['style'] == 'transparent') {
				echo 'border-color: transparent !important;';
			}
			// Font Color
			if( $bfdesign['field_active_font_color']['style'] == 'color' ) {
				echo 'color: ' . $bfdesign['field_active_font_color']['color'] . ' !important;';
			} ?>
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .form-control:disabled {
		background: rgba(255, 255, 255, 0.5) !important;
		box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04) !important;
		color: rgba(51, 51, 51, 0.5) !important;
		cursor: not-allowed !important;
	}

	<?php // Placeholder Font Color
	if( $bfdesign['field_placeholder_font_color']['style'] == 'color' ) {
		echo '.the_buddyforms_form form#'.esc_attr($css_form_id).' .bf-input textarea::placeholder, .the_buddyforms_form form#'.esc_attr($css_form_id).' .bf-input .form-control::placeholder { color: ' . $bfdesign['field_placeholder_font_color']['color'] . ' !important; }';
	} ?>

	<?php } ?>


	<?php
	// only output CSS for descriptions if the option to disable CSS is unchecked
	if( empty($bfdesign['desc_disable_css']) ) { ?>

	/* Design Options - Descriptions */
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> span.help-inline,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> span.help-block {
		padding: 5px 0 !important;
	<?php
			// Font Size
			if( $bfdesign['desc_font_size'] != '' ) {
				echo 'font-size: ' . $bfdesign['desc_font_size'] . 'px !important;';
			}
			// Font Color
			if( $bfdesign['desc_font_color']['style'] == 'color' ) {
				echo 'color: ' . $bfdesign['desc_font_color']['color'] . ' !important;';
			}
			// Font Style
			if( $bfdesign['desc_font_style'] == 'italic' ) 	{
				echo 'font-style: italic !important;';
			} else {
				echo 'font-style: normal !important;';
			} ?>;
	}

	<?php } ?>


	<?php
	// only output CSS for buttons if the option to disable CSS is unchecked
	if( empty($bfdesign['button_disable_css']) ) { ?>
	/* Design Options - Buttons */
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .form-actions button.bf-submit, .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .form-actions button.bf-draft {
		margin-bottom: 10px !important;
	<?php
			// Button Width
			if( $bfdesign['button_width'] != 'inline' ) {
				echo 'display: block; width: 100% !important;'; }
			else {
				echo 'display: inline; width: auto !important;';
			}
			// Button Size
			if( $bfdesign['button_size'] == 'large' ) {
				echo 'padding: 12px 25px; font-size: 17px !important;';
			}
			if( $bfdesign['button_size'] == 'xlarge' ) {
				echo 'padding: 15px 32px; font-size: 19px !important;';
			}
			// Background Color
			if( $bfdesign['button_background_color']['style'] == 'color' ) {
				echo 'background-color: ' . $bfdesign['button_background_color']['color'] . ' !important;';
			} elseif( $bfdesign['button_background_color']['style'] == 'transparent' ) {
				echo 'background-color: transparent !important;';
			}
			// Font Color
			if( $bfdesign['button_font_color']['style'] == 'color' ) {
				echo 'color: ' . $bfdesign['button_font_color']['color'] . ' !important;';
			}
			// Border Radius
			if( $bfdesign['button_border_radius'] != '' ) {
				echo 'border-radius: ' . $bfdesign['button_border_radius'] . 'px !important;';
			}
			// Border Width
			if( $bfdesign['button_border_width'] != '' ) {
				echo 'border-width: ' . $bfdesign['button_border_width'] . 'px !important; border-style: solid !important;';
			}
			// Border Color
			if( $bfdesign['button_border_color']['style'] == 'color' ) {
				echo 'border-color: ' . $bfdesign['button_border_color']['color'] . ' !important;';
			} elseif( $bfdesign['button_border_color']['style'] == 'transparent' ) {
				echo 'border-color: transparent !important;';
			} ?>
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .form-actions {
		text-align: <?php echo esc_attr( $bfdesign['button_alignment']); ?>;
	}

	/*Button Width Behaviour -- if always on block*/
	<?php if( $bfdesign['button_width'] != 'block' ) : ?>
	@media (min-width: 768px) {
		.<?php echo esc_attr($css_form_class) ?> .form-actions button.bf-submit, .<?php echo esc_attr($css_form_class) ?> .form-actions button.bf-draft {
			display: inline !important;
			width: auto !important;
		}
	}

	<?php endif; ?>

	/* Design Options - Buttons Hover State */
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .form-actions button.bf-submit:hover,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .form-actions button.bf-draft:hover,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .form-actions button.bf-submit:focus,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .form-actions button.bf-draft:focus {
	<?php
		 // Background Color
		 if( $bfdesign['button_background_color_hover']['style'] == 'color' ) {
			 echo 'background-color: ' . $bfdesign['button_background_color_hover']['color'] . ' !important;';
		 } elseif( $bfdesign['button_background_color_hover']['style'] == 'transparent' ) {
			 echo 'background-color: transparent !important;';
		 }
		 // Font Color
		 if( $bfdesign['button_font_color_hover']['style'] == 'color' ) {
			 echo 'color: ' . $bfdesign['button_font_color_hover']['color'] . ' !important;';
		 }
		 // Border Color
		 if( $bfdesign['button_border_color_hover']['style'] == 'color') {
			 echo 'border-color: ' . $bfdesign['button_border_color_hover']['color'] . ' !important;';
		 } elseif( $bfdesign['button_border_color_hover']['style'] == 'transparent' ) {
			 echo 'border-color: transparent !important;';
		 } ?>
	}

	<?php } ?>

	<?php echo esc_attr( $bfdesign['custom_css']); ?>
	/* The BuddyForms Form */

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> {
		margin-top: 20px !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> {
		margin: 0 -15px !important;
	}

	.bf_field_group {
		margin: 15px 0 !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group .bf_inputs {
		margin: 0 !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group .bf_inputs .wp-editor-container table tr {
		background: transparent !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> label {
		display: block !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> span.help-inline,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> span.help-block {
		display: block !important;
		font-size: 80% !important;
		font-style: italic !important;
		font-weight: normal !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .revision {
		overflow: auto !important;
		overflow-x: hidden !important;
		margin: 40px 0 20px 0 !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> ul.post-revisions {
		list-style: none outside none !important;
		margin: 10px 0 !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> ul.post-revisions li {
		margin: 5px 0 !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> ul.post-revisions li img {
		margin-right: 10px !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .post-revisions li {
		float: left !important;
		padding: 5px !important;
		width: 100% !important;
	}

	#loginform input.input {
		max-width: 300px !important;
	}

	#loginform input.input,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .standard-form textarea,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .standard-form input[type=url],
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .standard-form input[type=link],
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .standard-form input[type=text],
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .standard-form input[type=email],
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .standard-form input[type=password] {
		width: 100% !important;
		background: #fff !important;
		border: 1px solid #ccc !important;
		-moz-border-radius: 3px !important;
		-webkit-border-radius: 3px !important;
		border-radius: 3px !important;
		color: inherit !important;
		font: inherit !important;
		font-size: 14px !important;
		padding: 6px !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-xs-12 {
		position: relative !important;
		min-height: 1px !important;
		padding-left: 15px !important;
		padding-right: 15px !important;
		float: left !important;
		width: 100% !important;
		box-sizing: border-box !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-9 {
		position: relative !important;
		min-height: 1px !important;
		padding-left: 15px !important;
		padding-right: 15px !important;
		float: left !important;
		width: 100% !important;
		box-sizing: border-box !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-8 {
		position: relative !important;
		min-height: 1px !important;
		padding-left: 15px !important;
		padding-right: 15px !important;
		float: left !important;
		width: 100% !important;
		box-sizing: border-box !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-6 {
		position: relative !important;
		min-height: 1px !important;
		padding-left: 15px !important;
		padding-right: 15px !important;
		float: left !important;
		width: 100% !important;
		box-sizing: border-box !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-4 {
		position: relative !important;
		min-height: 1px !important;
		padding-left: 15px !important;
		padding-right: 15px !important;
		float: left !important;
		width: 100% !important;
		box-sizing: border-box !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-3 {
		position: relative !important;
		min-height: 1px !important;
		padding-left: 15px !important;
		padding-right: 15px !important;
		float: left !important;
		width: 100% !important;
		box-sizing: border-box !important;
	}

	@media (min-width: 992px) {
		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-9 {
			width: 75% !important;
		}

		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-8 {
			width: 66.66% !important;
		}

		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-6 {
			width: 50% !important;
		}

		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-4 {
			width: 33.33% !important;
		}

		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-3 {
			width: 25% !important;
		}

		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-12.bf-start-row,
		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-9.bf-start-row,
		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-8.bf-start-row,
		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-6.bf-start-row,
		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-4.bf-start-row,
		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-3.bf-start-row {
			clear: both !important;
		}
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> #insert-media-button {
		padding: 1px 7px 1px 5px !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> #wp-buddyforms_form_content-editor-tools .wp-switch-editor {
		height: auto !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> #wp-buddyforms_form_content-editor-container.error {
		border: 1px solid red !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> #wp-buddyforms_form_content-editor-container {
		border: 1px solid rgba(0, 0, 0, 0.2) !important;

	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> #wp-buddyforms_form_content-editor-container iframe {
		width: 99% !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group .dropzone .dz-message {
		text-align: left !important;
	}

	/* --- Form Errors --- */
	html body .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group input.error,
	html body .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group select.error,
	html body .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group button.error,
	html body .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group textarea.error {
		border: 1px solid red !important;
	}

	html body .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group div.dropzone.dz-clickable.error {
		border: 1px solid red !important;
	}

	html body .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group.a textarea.error {
		border: 1px solid red !important;
	}

	html body .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group label.error {
		color: red !important;
	}

	html body .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group .checkbox label label.error {
		color: red !important;
		font-weight: bold !important;
	}

	html body .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group span.select2-selection.select2-selection--multiple.error,
	html body .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group span.select2-selection.select2-selection--single.error {
		border: 1px solid red !important;
	}

	/* --- Form Errors --- */

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-12.bf-start-row,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-9.bf-start-row,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-8.bf-start-row,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-6.bf-start-row,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-4.bf-start-row,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-3.bf-start-row {
		clear: both !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input textarea, .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input #comments.form-control {
		display: block !important;
		width: 100% !important;
		min-height: 40px !important;
		font-size: 15px !important;
		float: unset !important;
	}

	#content .buddypress-wrap .the_buddyforms_form .standard-form input[type="search"] {
		background: #fff !important;
		border: unset !important;
	}

	#content .buddypress-wrap .the_buddyforms_form .standard-form li.select2-selection__choice {
		padding: 0 5px !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?>.bf-input .select2-selection,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-selection {
		width: 100% !important;
		float: unset !important;
		height: auto !important;
		font-size: 15px !important;
		appearance: none !important;
		min-height: unset !important;
		box-sizing: content-box !important;
		background-color: #fafafa !important;
		<?php
			if(!empty($bfdesign['field_padding'])) {
				echo 'padding: ' . $bfdesign['field_padding'] . 'px; !important';
				echo 'min-height: calc(52px  - ' . $bfdesign['field_padding'] * 2 . 'px) !important;';
				echo 'width: calc(100% - ' . $bfdesign['field_padding'] * 2 . 'px) !important;';
			}
		?>
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-selection .select2-selection__arrow:before {
		content: "" !important;
		width: 100% !important;
		display: block !important;
		height: 100% !important;
		line-height: 35px !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-container--default .select2-selection--single .select2-selection__arrow {
		height: 100% !important;
		position: absolute !important;
		top: 1px !important;
		right: 0 !important;
		width: 20px !important;
		background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23007CB2%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E') !important;
		background-repeat: no-repeat, repeat !important;
		background-position: right .7em top 50%, 0 0 !important;
		background-size: .65em auto, 100% !important;
		<?php
			if(!empty($bfdesign['field_padding'])) {
				echo 'right: ' . $bfdesign['field_padding'] . 'px !important;';
			}
		?>
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-container--default .select2-selection--single .select2-selection__arrow b {
		display: none !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-container--default .select2-selection--multiple .select2-selection__choice {
		background-color: #e4e4e4 !important;
		border: 1px solid #aaa !important;
		border-radius: 4px !important;
		cursor: default !important;
		float: left !important;
		margin-right: 5px !important;
		margin-top: 5px !important;
		padding: 0 5px !important;
		height: 24px !important;
		list-style: none !important;
		line-height: unset !important;
	}
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-container--default .select2-search {
		list-style: none !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group.acf-field .select2-selection--multiple .select2-selection__rendered li {
		line-height: 30px !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group .select2-selection--multiple .select2-selection__rendered li {
		height: auto !important;
		line-height: unset !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-selection .select2-selection__clear {
		right: 0 !important;
		top: 1px !important;
		margin-left: 5px !important;
		font-size: 1rem !important;
		<?php
			if(!empty($bfdesign['field_padding'])) {
				echo 'right: ' . ($bfdesign['field_padding'] + 25) . 'px !important;';
			}
		?>
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-container--default .select2-selection.select2-selection--multiple .select2-selection__choice {
		color: #444 !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-container--default .select2-selection.select2-selection--multiple .select2-selection__choice__remove {
		color: #666666 !important;
		cursor: pointer !important;
		font-weight: bold !important;
		margin-right: 5px !important;
		display: inline-block !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-container--default .select2-selection--multiple .select2-selection__clear {
		float: right !important;
		margin-top: 0 !important;
		font-size: 1em !important;
		cursor: pointer !important;
		font-weight: bold !important;
		line-height: 50px !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-selection .select2-selection__placeholder {
		color: #666666 !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-selection.select2-selection--multiple .select2-selection__rendered {
		padding: 0 !important;
		width: 100% !important;
		height: auto !important;
		overflow-y: auto !important;
		margin-bottom: 0 !important;
		box-sizing: content-box !important;
		position: relative !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-selection .select2-selection__rendered {
		padding: 0 !important;
		height: auto !important;
		position: static !important;
		line-height: unset !important;
		<?php
			if(!empty($bfdesign['field_padding'])) {
				//echo 'right: ' . $bfdesign['field_padding'] . 'px;';
			}
		?>
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-container--default .select2-selection .select2-search--inline .select2-search__field {
		color: #666666 !important;
		line-height: unset !important;
		background-color: #fafafa !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-container--default .select2-selection .select2-search--inline .select2-search__field::-webkit-input-placeholder { /* Edge */
		color: #666666 !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-container--default .select2-selection .select2-search--inline .select2-search__field:-ms-input-placeholder { /* Internet Explorer 10-11 */
		color: #666666 !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-container--default .select2-selection .select2-search--inline .select2-search__field::placeholder {
		color: #666666 !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input select.form-control {
		display: block !important;
		margin: 0 !important;
		padding: 5px !important;
		max-width: 100% !important;
		appearance: none !important;
		border-radius: 4px !important;
		-moz-appearance: none !important;
		border: 1px solid #aaa !important;
		-webkit-appearance: none !important;
		width: calc(100% - 5px*2) !important;
		background-color: #fafafa !important;
		background-size: .65em auto, 100% !important;
		box-sizing: content-box !important;
		background-repeat: no-repeat, repeat !important;
		background-position: right .7em top 50%, 0 0 !important;
		background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23007CB2%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E') !important;
		<?php
			if (!empty($bfdesign['field_padding'])) {
				echo 'padding: ' . $bfdesign['field_padding'] . 'px !important;';
				echo 'width: calc(100% - ' . ($bfdesign['field_padding'] * 2) . 'px) !important;';
				echo 'background-position: right ' . $bfdesign['field_padding'] . 'px top 50%, 0 0 !important;';
			}
		?>
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input select.form-control::-ms-expand {
		display: none !important;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group .radio > label, .bf_field_group .radio > label > input[type='radio'] {
		cursor: pointer !important;
	}

	/* Avoid red style over the elements comming from BuddyPress */
	.buddypress-wrap .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?>.standard-form input[required]:invalid,
	.buddypress-wrap .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?>.standard-form textarea[required]:invalid {
		border-color: #d6d6d6 !important;
	}

	/* Fix to avoid BP override the width of the MCE editor on the text tab */
	.buddypress-wrap .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?>.standard-form .wp-editor-wrap input:not(.small) {
		width: initial !important;
	}

	/* Fix to avoid BP override the width of text inputs */
	#buddypress .the_buddyforms_form .standard-form input[type="text"] {
		width: 100% !important;
	}

	#content .the_buddyforms_form form.<?php echo esc_attr($css_form_class) ?> fieldset {
		border: 1px solid #d6d6d6 !important;
		padding: 0 !important;
		width: 100% !important;
		max-width: 100% !important;
		min-width: 100% !important;
		margin-top: 0.5em !important;
		margin-bottom: 0.5em !important;
	}

	/* Solution to avoid the select2 dropdown not left behind the popups */
	span.select2-dropdown.buddyforms-dropdown {
		z-index: 200000 !important;
	}
</style>