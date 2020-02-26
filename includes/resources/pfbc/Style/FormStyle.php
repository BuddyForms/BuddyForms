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
		margin-right: 10px;
	<?php
		// Font Size
		if( $bfdesign['label_font_size'] != '' ) {
			echo 'font-size: ' . $bfdesign['label_font_size'] . 'px;';
		}
		// Font Color
		if( $bfdesign['label_font_color']['style'] == 'color' ) {
			echo 'color: ' . $bfdesign['label_font_color']['color'] . ';';
		}
		// Font Weight
		if( $bfdesign['label_font_style'] == 'bolditalic' || $bfdesign['label_font_style'] == 'bold' ) {
			echo 'font-weight: bold;';
		} else {
			echo 'font-weight: normal;';
		}
		// Font Style
		if( $bfdesign['label_font_style'] == 'bolditalic' || $bfdesign['label_font_style'] == 'italic' ) 	{
			echo 'font-style: italic;';
		} else {
			echo 'font-style: normal;';
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
		width: 95%;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input textarea,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .form-control {
		display: block;
		width: 100%;

	<?php
			// Padding
			if( !empty($bfdesign['field_padding']) ) {
				echo 'padding: ' . $bfdesign['field_padding'] . 'px;';
			} else {
				echo 'padding-left: 5px;';
			}
			// Background Color
			if( $bfdesign['field_background_color']['style'] == 'color' ) {
				echo 'background-color: ' . $bfdesign['field_background_color']['color'] . ';';
			} elseif( $bfdesign['field_background_color']['style'] == 'transparent' ) {
				echo 'background-color: transparent;';
			}
			// Border Color
			if( $bfdesign['field_border_color']['style'] == 'color' ) {
				echo 'border-color: ' . $bfdesign['field_border_color']['color'] . ';';
			} elseif( $bfdesign['field_border_color']['style'] == 'transparent' ) {
				echo 'border-color: transparent;';
			}
			// Border Width
			if( $bfdesign['field_border_width'] != '' ) {
				echo 'border-width: ' . $bfdesign['field_border_width'] . 'px; border-style: solid;';
			}
			// Border Radius
			if( $bfdesign['field_border_radius'] != '' ) {
				echo 'border-radius: ' . $bfdesign['field_border_radius'] . 'px;';
			}
			// Font Size
			if( $bfdesign['field_font_size'] != '' ) {
				echo 'font-size: ' . $bfdesign['field_font_size'] . 'px;';
			}
			// Font Color
			if( $bfdesign['field_font_color']['style'] == 'color' ) {
				echo 'color: ' . $bfdesign['field_font_color']['color'] . ';';
			} ?> min-height: 40px;
	}

	/* Design Options - Text Fields Active */
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input textarea:focus,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .form-control:focus {
	<?php
			// Background Color
			if( $bfdesign['field_active_background_color']['style'] == 'color' ) {
				echo 'background-color: ' . $bfdesign['field_active_background_color']['color'] . ';';
			} elseif( $bfdesign['field_active_background_color']['style'] == 'transparent' ) {
				echo 'background-color: transparent;';
			}
			// Border Color
			if( $bfdesign['field_active_border_color']['style'] == 'color' ) {
				echo 'border-color: ' . $bfdesign['field_active_border_color']['color'] . ';';
			} elseif( $bfdesign['field_active_border_color']['style'] == 'transparent' ) {
				echo 'border-color: transparent;';
			}
			// Font Color
			if( $bfdesign['field_active_font_color']['style'] == 'color' ) {
				echo 'color: ' . $bfdesign['field_active_font_color']['color'] . ';';
			} ?>
	}

	<?php // Placeholder Font Color
	if( $bfdesign['field_placeholder_font_color']['style'] == 'color' ) {
		echo '.the_buddyforms_form form#'.esc_attr($css_form_id).' .bf-input textarea::placeholder, .the_buddyforms_form form#'.esc_attr($css_form_id).' .bf-input .form-control::placeholder { color: ' . $bfdesign['field_placeholder_font_color']['color'] . '; }';
	} ?>

	<?php } ?>


	<?php
	// only output CSS for descriptions if the option to disable CSS is unchecked
	if( empty($bfdesign['desc_disable_css']) ) { ?>

	/* Design Options - Descriptions */
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> span.help-inline,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> span.help-block {
		padding: 5px 0;
	<?php
			// Font Size
			if( $bfdesign['desc_font_size'] != '' ) {
				echo 'font-size: ' . $bfdesign['desc_font_size'] . 'px;';
			}
			// Font Color
			if( $bfdesign['desc_font_color']['style'] == 'color' ) {
				echo 'color: ' . $bfdesign['desc_font_color']['color'] . ';';
			}
			// Font Style
			if( $bfdesign['desc_font_style'] == 'italic' ) 	{
				echo 'font-style: italic;';
			} else {
				echo 'font-style: normal;';
			} ?>;
	}

	<?php } ?>


	<?php
	// only output CSS for buttons if the option to disable CSS is unchecked
	if( empty($bfdesign['button_disable_css']) ) { ?>
	/* Design Options - Buttons */
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .form-actions button.bf-submit, .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .form-actions button.bf-draft {
		margin-bottom: 10px;
	<?php
			// Button Width
			if( $bfdesign['button_width'] != 'inline' ) {
				echo 'display: block; width: 100%;'; }
			else {
				echo 'display: inline; width: auto;';
			}
			// Button Size
			if( $bfdesign['button_size'] == 'large' ) {
				echo 'padding: 12px 25px; font-size: 17px;';
			}
			if( $bfdesign['button_size'] == 'xlarge' ) {
				echo 'padding: 15px 32px; font-size: 19px;';
			}
			// Background Color
			if( $bfdesign['button_background_color']['style'] == 'color' ) {
				echo 'background-color: ' . $bfdesign['button_background_color']['color'] . ';';
			} elseif( $bfdesign['button_background_color']['style'] == 'transparent' ) {
				echo 'background-color: transparent;';
			}
			// Font Color
			if( $bfdesign['button_font_color']['style'] == 'color' ) {
				echo 'color: ' . $bfdesign['button_font_color']['color'] . ';';
			}
			// Border Radius
			if( $bfdesign['button_border_radius'] != '' ) {
				echo 'border-radius: ' . $bfdesign['button_border_radius'] . 'px;';
			}
			// Border Width
			if( $bfdesign['button_border_width'] != '' ) {
				echo 'border-width: ' . $bfdesign['button_border_width'] . 'px; border-style: solid;';
			}
			// Border Color
			if( $bfdesign['button_border_color']['style'] == 'color' ) {
				echo 'border-color: ' . $bfdesign['button_border_color']['color'] . ';';
			} elseif( $bfdesign['button_border_color']['style'] == 'transparent' ) {
				echo 'border-color: transparent;';
			} ?>
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .form-actions {
		text-align: <?php echo esc_attr( $bfdesign['button_alignment']); ?>;
	}

	/*Button Width Behaviour -- if always on block*/
	<?php if( $bfdesign['button_width'] != 'block' ) : ?>
	@media (min-width: 768px) {
		.<?php echo esc_attr($css_form_class) ?> .form-actions button.bf-submit, .<?php echo esc_attr($css_form_class) ?> .form-actions button.bf-draft {
			display: inline;
			width: auto;
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
			 echo 'background-color: ' . $bfdesign['button_background_color_hover']['color'] . ';';
		 } elseif( $bfdesign['button_background_color_hover']['style'] == 'transparent' ) {
			 echo 'background-color: transparent;';
		 }
		 // Font Color
		 if( $bfdesign['button_font_color_hover']['style'] == 'color' ) {
			 echo 'color: ' . $bfdesign['button_font_color_hover']['color'] . ';';
		 }
		 // Border Color
		 if( $bfdesign['button_border_color_hover']['style'] == 'color' ) {
			 echo 'border-color: ' . $bfdesign['button_border_color_hover']['color'] . ';';
		 } elseif( $bfdesign['button_border_color_hover']['style'] == 'transparent' ) {
			 echo 'border-color: transparent;';
		 } ?>
	}

	<?php } ?>

	<?php echo esc_attr( $bfdesign['custom_css']); ?>
	/* The BuddyForms Form */

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> {
		margin-top: 20px;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> {
		margin: 0 -15px;
	}

	.bf_field_group {
		margin: 15px 0;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group .bf_inputs {
		margin: 0;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group .bf_inputs .wp-editor-container table tr {
		background: transparent;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> label {
		display: block;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> span.help-inline,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> span.help-block {
		display: block;
		font-size: 80%;
		font-style: italic;
		font-weight: normal;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .revision {
		overflow: auto;
		overflow-x: hidden;
		margin: 40px 0 20px 0;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> ul.post-revisions {
		list-style: none outside none;
		margin: 10px 0;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> ul.post-revisions li {
		margin: 5px 0;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> ul.post-revisions li img {
		margin-right: 10px;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .post-revisions li {
		float: left;
		padding: 5px;
		width: 100%;
	}

	#loginform input.input {
		max-width: 300px;
	}

	#loginform input.input,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .standard-form textarea,
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .standard-form input[type=url],
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .standard-form input[type=link],
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .standard-form input[type=text],
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .standard-form input[type=email],
	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .standard-form input[type=password] {
		width: 100%;
		background: #fff;
		border: 1px solid #ccc;
		-moz-border-radius: 3px;
		-webkit-border-radius: 3px;
		border-radius: 3px;
		color: inherit;
		font: inherit;
		font-size: 14px;
		padding: 6px;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-xs-12 {
		position: relative;
		min-height: 1px;
		padding-left: 15px;
		padding-right: 15px;
		float: left;
		width: 100%;
		box-sizing: border-box;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-9 {
		position: relative;
		min-height: 1px;
		padding-left: 15px;
		padding-right: 15px;
		float: left;
		width: 100%;
		box-sizing: border-box;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-8 {
		position: relative;
		min-height: 1px;
		padding-left: 15px;
		padding-right: 15px;
		float: left;
		width: 100%;
		box-sizing: border-box;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-6 {
		position: relative;
		min-height: 1px;
		padding-left: 15px;
		padding-right: 15px;
		float: left;
		width: 100%;
		box-sizing: border-box;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-4 {
		position: relative;
		min-height: 1px;
		padding-left: 15px;
		padding-right: 15px;
		float: left;
		width: 100%;
		box-sizing: border-box;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-3 {
		position: relative;
		min-height: 1px;
		padding-left: 15px;
		padding-right: 15px;
		float: left;
		width: 100%;
		box-sizing: border-box;
	}

	@media (min-width: 992px) {
		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-9 {
			width: 75%;
		}

		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-8 {
			width: 66.66%;
		}

		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-6 {
			width: 50%;
		}

		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-4 {
			width: 33.33%;
		}

		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-3 {
			width: 25%;
		}

		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-12.bf-start-row,
		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-9.bf-start-row,
		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-8.bf-start-row,
		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-6.bf-start-row,
		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-4.bf-start-row,
		.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> fieldset .col-md-3.bf-start-row {
			clear: both;
		}
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> #insert-media-button {
		padding: 1px 7px 1px 5px;
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
		text-align: left;
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
		color: red;
	}

	html body .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group .checkbox label label.error {
		color: red;
		font-weight: bold;
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
		clear: both;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input textarea, .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input #comments.form-control {
		display: block;
		width: 100%;
		min-height: 40px;
		font-size: 15px;
		float: unset;
	}

	#content .buddypress-wrap .the_buddyforms_form .standard-form input[type="search"] {
		background: #fff;
		border: unset;
	}

	#content .buddypress-wrap .the_buddyforms_form .standard-form li.select2-selection__choice {
	<?php
		if( !empty($bfdesign['field_padding']) ) {
			$padding = intval($bfdesign['field_padding'])/3;
			echo 'padding: ' . $padding . 'px !important;';
		} else {
			echo 'padding-left: 5px;';
		}
	?>
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-selection {
		min-height: 40px;
		font-size: 15px;
		float: unset;
		width: 100%;
		background-color: #fafafa !important;
		padding: 25px;
		appearance: none;

	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-selection .select2-selection__arrow:before {
		content: "" !important;
		width: 100% !important;
		display: block;
		height: 100%;
		line-height: 35px;

	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-container--default .select2-selection--single .select2-selection__arrow {
		height: 100%;
		position: absolute;
		top: 1px;
		right: 0;
		width: 20px;
		background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23007CB2%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E');
		background-repeat: no-repeat, repeat;
		background-position: right .7em top 50%, 0 0;
		background-size: .65em auto, 100%;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-container--default .select2-selection--single .select2-selection__arrow b {
		display: none;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-container--default .select2-selection--multiple .select2-selection__choice {
		background-color: #e4e4e4;
		border: 1px solid #aaa;
		border-radius: 4px;
		cursor: default;
		float: left;
		margin-right: 5px;
		margin-top: 5px;
		padding: 0 5px;
		height: 30px;
		line-height: 30px;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-selection .select2-selection__clear {
		right: 0;
		top: 1px;
		margin-left: 3px;
		font-size: 1rem;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-container--default .select2-selection--multiple .select2-selection__clear {
		cursor: pointer;
		float: right;
		font-weight: bold;
		margin-top: 5px;
		margin-right: 5px;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-selection .select2-selection__placeholder {
		color: #666666;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-selection.select2-selection--multiple .select2-selection__rendered {
		width: 97%;
		overflow-y: auto;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input .select2-selection .select2-selection__rendered {
		height: 40px;
		position: absolute;
		top: 5px;
		left: 1em;
		line-height: 40px;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input select.form-control {
		display: block;
		width: 100%;
		max-width: 100%;
		box-sizing: border-box;
		margin: 0;
		-moz-appearance: none;
		-webkit-appearance: none;
		border: 1px solid #aaa;
		border-radius: 4px;
		appearance: none;
		background-color: #fafafa;
		background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23007CB2%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E');
		background-repeat: no-repeat, repeat;
		background-position: right .7em top 50%, 0 0;
		background-size: .65em auto, 100%;
	<?php
	if( !empty($bfdesign['field_padding'])) {
		echo 'padding: ' . $bfdesign['field_padding'] . 'px;';
	} else {
		echo 'padding-left: 5px;';
	}
	?>
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf-input select.form-control::-ms-expand {
		display: none;
	}

	.the_buddyforms_form .<?php echo esc_attr($css_form_class) ?> .bf_field_group .radio > label, .bf_field_group .radio > label > input[type='radio'] {
		cursor: pointer;
	}

	/* Avoid red style over the elements comming from BuddyPress */
	.buddypress-wrap .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?>.standard-form input[required]:invalid,
	.buddypress-wrap .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?>.standard-form textarea[required]:invalid {
		border-color: #d6d6d6;
	}

	/* Fix to avoid BP override the width of the MCE editor on the text tab */
	.buddypress-wrap .the_buddyforms_form .<?php echo esc_attr($css_form_class) ?>.standard-form .wp-editor-wrap input:not(.small) {
		width: initial;
	}

	/* Fix to avoid BP override the width of text inputs */
	#buddypress .the_buddyforms_form .standard-form input[type="text"] {
		width: 100%;
	}

	#content .the_buddyforms_form form.<?php echo esc_attr($css_form_class) ?> fieldset {
		border: 1px solid #d6d6d6;
		padding: 0;
		width: 100%;
		max-width: 100%;
		min-width: 100%;
		margin-top: 0.5em;
		margin-bottom: 0.5em;
	}

	/* Solution to avoid the select2 dropdown not left behind the popups */
	span.select2-dropdown.buddyforms-dropdown {
		z-index: 200000;
	}
</style>