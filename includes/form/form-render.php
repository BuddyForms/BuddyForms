<?php

/**
 * @param $args
 *
 * @return mixed|string|void
 */
function buddyforms_form_html( $args ) {
	global $buddyforms, $bf_form_error, $bf_submit_button, $post_id, $form_slug;

	// First check if any form error exist
	if ( ! empty( $bf_form_error ) ) {
		echo '<div class="bf-alert error">' . $bf_form_error . '</div>';

		return $args;
	}

	// Extract the form args
	extract( shortcode_atts( array(
		'post_type'    => '',
		'the_post'     => 0,
		'customfields' => false,
		'post_id'      => false,
		'revision_id'  => false,
		'post_parent'  => 0,
		'redirect_to'  => esc_url( $_SERVER['REQUEST_URI'] ),
		'form_slug'    => '',
		'form_notice'  => '',
	), $args ) );

	if ( ! is_user_logged_in() && ! isset( $buddyforms[ $form_slug ]['public_submit'] ) ) :
		return buddyforms_get_wp_login_form();
	endif;

	$user_can_edit = false;
	if ( empty( $post_id ) && current_user_can( 'buddyforms_' . $form_slug . '_create' ) ) {
		$user_can_edit = true;
	} elseif ( ! empty( $post_id ) && current_user_can( 'buddyforms_' . $form_slug . '_edit' ) ) {
		$user_can_edit = true;
	}

	if ( isset( $buddyforms[ $form_slug ]['public_submit'] ) && $buddyforms[ $form_slug ]['public_submit'][0] == 'public_submit' ) {
		$user_can_edit = true;
	}

	$user_can_edit = apply_filters( 'buddyforms_user_can_edit', $user_can_edit, $form_slug, $post_id );

	if ( $user_can_edit == false ) {

		$error_message = apply_filters( 'buddyforms_user_can_edit_error_message', __( 'You do not have the required user role to use this form', 'buddyforms' ) );

		return '<div class="bf-alert error">' . $error_message . '</div>';
	}

	// Form HTML Start. The Form is rendered as last step.
	$form_html = '<div id="buddyforms_form_hero_' . $form_slug . '" class="the_buddyforms_form ' . apply_filters( 'buddyforms_form_hero_classes', '' ) . '" >';

	// Hook above the form inside the BuddyForms form div
	$form_html = apply_filters( 'buddyforms_form_hero_top', $form_html, $form_slug );
	$form_html .= ! is_user_logged_in() && isset( $buddyforms[ $form_slug ]['public_submit_login'] ) && $buddyforms[ $form_slug ]['public_submit_login'] == 'above' ? buddyforms_get_login_form_template() : '';

	$notice_class = apply_filters( 'buddyforms_form_notice_class', $form_notice != '' ? 'bf-alert success' : '', $form_slug );

	$form_html .= '<div class="' . $notice_class . '" id="form_message_' . $form_slug . '">' . $form_notice . '</div>';
	$form_html .= '<div class="form_wrapper">';

	$bfdesign = $buddyforms[$form_slug]['layout'];





	ob_start();?>

	<style>

	/* BuddyForms Options - Labels */
	.the_buddyforms_form .bf_field_group label {
	    margin-right: 10px;
			font-size: <?php echo $bfdesign['label_font_size']; ?>px;
			color: <?php echo $bfdesign['label_font_color']; ?>;
			font-weight: <?php if( $bfdesign['label_font_style'] == 'bolditalic' || $bfdesign['label_font_style'] == 'bold' ) { echo 'bold'; } else { echo 'normal'; } ?>;
			font-style:  <?php if( $bfdesign['label_font_style'] == 'bolditalic' || $bfdesign['label_font_style'] == 'italic' ) 	{ echo 'italic'; } else { echo 'normal'; } ?>;
	}

	/* BuddyForms Options - Form Fields & Form Field Text */
	.the_buddyforms_form .bf-input textarea,
	.the_buddyforms_form .bf-input .form-control {
	    display: block;
	    width: 100%;
			padding: <?php echo $bfdesign['field_padding']; ?>px;
			background: <?php echo $bfdesign['field_background_color']; ?>;
			border-color: <?php echo $bfdesign['field_border_color']; ?>;
			border-width: <?php echo $bfdesign['field_border_width']; ?>px;
			font-size: <?php echo $bfdesign['field_font_size']; ?>px;
			color: <?php echo $bfdesign['field_font_color']; ?>;
	}

	/* BuddyForms Options - Form Fields Active */
	.the_buddyforms_form .bf-input textarea:focus,
	.the_buddyforms_form .bf-input .form-control:focus {
			background: <?php echo $bfdesign['field_active_background_color']; ?>;
			border-color: <?php echo $bfdesign['field_active_border_color']; ?>;
			color: <?php echo $bfdesign['field_active_font_color']; ?>;
	}

	/* BuddyForms Options - Buttons */
	.the_buddyforms_form .form-actions button.bf-submit {
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
		// Button Background Color
		if( $bfdesign['button_background_color'] != '' ) {
			echo 'background-color: ' . $bfdesign['button_background_color'] . ';';
		}
		// Button Font Color
		if( $bfdesign['button_font_color'] != '' ) {
			echo 'color: ' . $bfdesign['button_font_color'] . ';';
		}
		// Button Border Radius
		if( $bfdesign['button_border_radius'] != '' ) {
			echo 'border-radius: ' . $bfdesign['button_border_radius'] . 'px;';
		}
		// Button Border Width
		if( $bfdesign['button_border_width'] != '' ) {
			echo 'border-width: ' . $bfdesign['button_border_width'] . 'px; border-style: solid;';
		}
		// Button Border Color
		if( $bfdesign['button_border_color'] != '' ) {
			echo 'border-color: ' . $bfdesign['button_border_color'];
		}
		?>
	}

	<?php if( $bfdesign['button_width'] != 'block' ) {
		echo '@media (min-width: 768px) {
						.the_buddyforms_form .form-actions button.bf-submit {
							display: inline;
							width: auto;
						}
					}';
		} ?>

	/* BuddyForms Options - Buttons Hover State */
	.the_buddyforms_form .form-actions button.bf-submit:hover,
	.the_buddyforms_form .form-actions button.bf-submit:focus {
		<?php
		// Button Background Color
		if( $bfdesign['button_background_color_hover'] != '' ) {
			echo 'background-color: ' . $bfdesign['button_background_color_hover'] . ';';
		}
		// Button Font Color
		if( $bfdesign['button_font_color_hover'] != '' ) {
			echo 'color: ' . $bfdesign['button_font_color_hover'] . ';';
		}
		// Button Border Color
		if( $bfdesign['button_border_color_hover'] != '' ) {
			echo 'border-color: ' . $bfdesign['button_border_color_hover'];
		}
		?>
	}

	/* BuddyForms Options - Other */
	.the_buddyforms_form .bf-input .radio {
	    display: <?php echo $bfdesign['radio_button_alignment']; ?>;
	}
	.the_buddyforms_form .bf-input .checkbox {
	    display: <?php echo $bfdesign['checkbox_alignment']; ?>;
	}



	</style>


	<?php
	$layout = ob_get_clean();

	$form_html .= $layout;


	// Create the form object
	$form = new Form( "buddyforms_form_" . $form_slug );

	$buddyforms_frontend_form_template_name = apply_filters( 'buddyforms_frontend_form_template', 'View_Frontend' );

	$form_class = 'standard-form';

	if ( ! isset( $buddyforms[ $form_slug ]['local_storage'] ) ) {
		$form_class = ' bf-garlic';
	}

	// Set the form attribute
	$form->configure( array(
		"prevent" => array( "bootstrap", "jQuery", "focus" ),
		"action"  => $redirect_to,
		"view"    => new $buddyforms_frontend_form_template_name(),
		'class'   => apply_filters( 'buddyforms_form_class', $form_class ),
		'ajax'    => ! isset( $buddyforms[ $form_slug ]['bf_ajax'] ) ? 'buddyforms_ajax_process_edit_post' : false,
		'method'  => 'post'
	) );

	$form->addElement( new Element_HTML( do_action( 'template_notices' ) ) );
	$form->addElement( new Element_HTML( wp_nonce_field( 'buddyforms_form_nonce', '_wpnonce', true, false ) ) );

	$form->addElement( new Element_Hidden( "redirect_to", $redirect_to ) );
	$form->addElement( new Element_Hidden( "post_id", $post_id ) );
	$form->addElement( new Element_Hidden( "revision_id", $revision_id ) );
	$form->addElement( new Element_Hidden( "post_parent", $post_parent ) );
	$form->addElement( new Element_Hidden( "form_slug", $form_slug ) );
	$form->addElement( new Element_Hidden( "bf_post_type", $post_type ) );
	$form->addElement( new Element_Hidden( "status", 'draft', array( 'id' => "status" ) ) );

	if ( isset( $buddyforms[ $form_slug ]['bf_ajax'] ) ) {
		$form->addElement( new Element_Hidden( "ajax", 'off' ) );
	}

	// if the form has custom field to save as post meta data they get displayed here
	buddyforms_form_elements( $form, $args );


	$form->addElement( new Element_Hidden( "bf_submitted", 'true', array( 'value' => 'true', 'id' => "submitted" ) ) );

	$bf_button_classes 	= 'bf-submit ' . $bfdesign['button_class'];
	$bf_button_text			= $bfdesign['submit_text'];

	$bf_submit_button = new Element_Button( $bf_button_text, 'submit', array(
		'id'    => $form_slug,
		'class' => $bf_button_classes,
		'name'  => 'submitted'
	) );
	$form             = apply_filters( 'buddyforms_create_edit_form_button', $form, $form_slug, $post_id );

	if ( $bf_submit_button ) {
		$form->addElement( $bf_submit_button );
	}

	$form = apply_filters( 'buddyforms_form_before_render', $form, $args );

	// That's it! render the form!
	ob_start();
	$form->render();
	$form_html .= ob_get_contents();
	ob_clean();

	$form_html .= '<div class="bf_modal"></div></div>';

	// If Form Revision is enabled Display the revision posts under the form
	if ( isset( $buddyforms[ $form_slug ]['revision'] ) && $post_id != 0 ) {
		ob_start();
		buddyforms_wp_list_post_revisions( $post_id );
		$form_html .= ob_get_contents();
		ob_clean();
	}

	// Hook under the form inside the BuddyForms form div
	$form_html = apply_filters( 'buddyforms_form_hero_last', $form_html, $form_slug );
	$form_html .= ! is_user_logged_in() && isset( $buddyforms[ $form_slug ]['public_submit_login'] ) && $buddyforms[ $form_slug ]['public_submit_login'] == 'under' ? buddyforms_get_login_form_template() : '';

	if ( buddyforms_core_fs()->is_not_paying() ) {
		$form_html .= '<div style="text-align: right; opacity: 0.4; font-size: 12px; margin: 30px 0 0;" clss="branding">Proudly brought to you by <a href="https://themekraft.com/buddyforms/" target="_blank" rel="nofollow">BuddyForms</a></div>';
	}

	$form_html .= '</div>'; // the_buddyforms_form end

	return $form_html;
}

/**
 * @return string
 */
function buddyforms_get_login_form_template() {

	ob_start();
	buddyforms_locate_template( 'login-form' );
	$login_form = ob_get_clean();

	return $login_form;

}
