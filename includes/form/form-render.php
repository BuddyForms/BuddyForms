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

	if ( ! is_user_logged_in() && $buddyforms[ $form_slug ]['form_type'] != 'registration' && isset( $buddyforms[ $form_slug ]['public_submit'] ) && $buddyforms[ $form_slug ]['public_submit'] != 'public_submit' )  :
		return buddyforms_get_wp_login_form( $form_slug );
	endif;

	$user_can_edit = false;
	if ( empty( $post_id ) && current_user_can( 'buddyforms_' . $form_slug . '_create' ) ) {
		$user_can_edit = true;
	} elseif ( ! empty( $post_id ) && current_user_can( 'buddyforms_' . $form_slug . '_edit' ) ) {
		$user_can_edit = true;
	}

	if ( $buddyforms[ $form_slug ]['form_type'] == 'registration'
	     || isset( $buddyforms[ $form_slug ]['public_submit'] ) && $buddyforms[ $form_slug ]['public_submit'] == 'public_submit'
	) {
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

	$bfdesign = isset( $buddyforms[ $form_slug ]['layout'] ) ? $buddyforms[ $form_slug ]['layout'] : array();


	// Alright, let's set some defaults

	// Labels
	$bfdesign['labels_disable_css']        = isset( $bfdesign['labels_disable_css'] ) ? $bfdesign['labels_disable_css'] : '';
	$bfdesign['labels_layout']             = isset( $bfdesign['labels_layout'] ) ? $bfdesign['labels_layout'] : 'inline';
	$bfdesign['label_font_size']           = isset( $bfdesign['label_font_size'] ) ? $bfdesign['label_font_size'] : '';
	$bfdesign['label_font_color']['style'] = isset( $bfdesign['label_font_color']['style'] ) ? $bfdesign['label_font_color']['style'] : 'auto';
	$bfdesign['label_font_style']          = isset( $bfdesign['label_font_style'] ) ? $bfdesign['label_font_style'] : 'bold';

	// Form Elements
	$bfdesign['other_elements_disable_css']        = isset( $bfdesign['other_elements_disable_css'] ) ? $bfdesign['other_elements_disable_css'] : '';
	$bfdesign['radio_button_alignment'] = isset( $bfdesign['radio_button_alignment'] ) ? $bfdesign['radio_button_alignment'] : 'inline';
	$bfdesign['checkbox_alignment']     = isset( $bfdesign['checkbox_alignment'] ) ? $bfdesign['checkbox_alignment'] : 'inline';

	// Text Fields
	$bfdesign['field_padding']                   = isset( $bfdesign['field_padding'] ) ? $bfdesign['field_padding'] : '15';
	$bfdesign['field_background_color']['style'] = isset( $bfdesign['field_background_color']['style'] ) ? $bfdesign['field_background_color']['style'] : 'auto';
	$bfdesign['field_border_color']['style']     = isset( $bfdesign['field_border_color']['style'] ) ? $bfdesign['field_border_color']['style'] : 'auto';
	$bfdesign['field_border_width']              = isset( $bfdesign['field_border_width'] ) ? $bfdesign['field_border_width'] : '';
	$bfdesign['field_border_radius']             = isset( $bfdesign['field_border_radius'] ) ? $bfdesign['field_border_radius'] : '';
	$bfdesign['field_font_size']                 = isset( $bfdesign['field_font_size'] ) ? $bfdesign['field_font_size'] : '15';
	$bfdesign['field_font_color']['style']       = isset( $bfdesign['field_font_color']['style'] ) ? $bfdesign['field_font_color']['style'] : 'auto';

	// Text Fields :Active
	$bfdesign['field_active_background_color']['style'] = isset( $bfdesign['field_active_background_color']['style'] ) ? $bfdesign['field_active_background_color']['style'] : 'auto';
	$bfdesign['field_active_border_color']['style']     = isset( $bfdesign['field_active_border_color']['style'] ) ? $bfdesign['field_active_border_color']['style'] : 'auto';
	$bfdesign['field_active_font_color']['style']       = isset( $bfdesign['field_active_font_color']['style'] ) ? $bfdesign['field_active_font_color']['style'] : 'auto';
	$bfdesign['field_placeholder_font_color']['style']  = isset( $bfdesign['field_placeholder_font_color']['style'] ) ? $bfdesign['field_placeholder_font_color']['style'] : 'auto';

	// Descriptions
	$bfdesign['desc_font_size']           = isset( $bfdesign['desc_font_size'] ) ? $bfdesign['desc_font_size'] : '';
	$bfdesign['desc_font_color']['style'] = isset( $bfdesign['desc_font_color']['style'] ) ? $bfdesign['desc_font_color']['style'] : 'auto';
	$bfdesign['desc_font_style']          = isset( $bfdesign['desc_font_style'] ) ? $bfdesign['desc_font_style'] : 'italic';

	// Submit Button
	$bfdesign['button_width']                     = isset( $bfdesign['button_width'] ) ? $bfdesign['button_width'] : 'blockmobile';
	$bfdesign['button_size']                      = isset( $bfdesign['button_size'] ) ? $bfdesign['button_size'] : 'large';
	$bfdesign['button_background_color']['style'] = isset( $bfdesign['button_background_color']['style'] ) ? $bfdesign['button_background_color']['style'] : 'auto';
	$bfdesign['button_font_color']['style']       = isset( $bfdesign['button_font_color']['style'] ) ? $bfdesign['button_font_color']['style'] : 'auto';
	$bfdesign['button_border_radius']             = isset( $bfdesign['button_border_radius'] ) ? $bfdesign['button_border_radius'] : '';
	$bfdesign['button_border_width']              = isset( $bfdesign['button_border_width'] ) ? $bfdesign['button_border_width'] : '';
	$bfdesign['button_border_color']['style']     = isset( $bfdesign['button_border_color']['style'] ) ? $bfdesign['button_border_color']['style'] : 'auto';
	$bfdesign['button_alignment']                 = isset( $bfdesign['button_alignment'] ) ? $bfdesign['button_alignment'] : 'left';

	// Submit Button :Active
	$bfdesign['button_background_color_hover']['style'] = isset( $bfdesign['button_background_color_hover']['style'] ) ? $bfdesign['button_background_color_hover']['style'] : 'auto';
	$bfdesign['button_border_color_hover']['style']     = isset( $bfdesign['button_border_color_hover']['style'] ) ? $bfdesign['button_border_color_hover']['style'] : 'auto';
	$bfdesign['button_font_color_hover']['style']       = isset( $bfdesign['button_font_color_hover']['style'] ) ? $bfdesign['button_font_color_hover']['style'] : 'auto';

	// Custom CSS
	$bfdesign['custom_css'] = isset( $bfdesign['custom_css'] ) ? $bfdesign['custom_css'] : '';

	// Extras
	$bfdesign['extras_disable_all_css']        = isset( $bfdesign['extras_disable_all_css'] ) ? $bfdesign['extras_disable_all_css'] : '';


	// only output the whole CSS if the option to disable CSS is unchecked
	if( $bfdesign['extras_disable_all_css'] == '' ) {

		ob_start(); ?>

	    <style>

					<?php
					// only output CSS for labels if the option to disable CSS is unchecked
					if( $bfdesign['labels_disable_css'] == '' ) { ?>
				    /* Design Options - Labels */
				    .the_buddyforms_form .bf_field_group label {
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
		        .the_buddyforms_form .bf-input .radio {
		            display: <?php echo $bfdesign['radio_button_alignment']; ?>;
		        }

		        .the_buddyforms_form .bf-input .checkbox {
		            display: <?php echo $bfdesign['checkbox_alignment']; ?>;
		        }
					<?php } ?>

					<?php
					// only output CSS for form elements if the option to disable CSS is unchecked
					if( $bfdesign['field_disable_css'] == '' ) { ?>
		        /* Design Options - Text Fields */
		        .the_buddyforms_form .bf-input textarea,
		        .the_buddyforms_form .bf-input .form-control {
		            display: block;
		            width: 100%;
		        		<?php
								// Padding
								if( $bfdesign['field_padding'] != '' ) {
									echo 'padding: ' . $bfdesign['field_padding'] . 'px;';
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
								} ?>
		        }

		        /* Design Options - Text Fields Active */
		        .the_buddyforms_form .bf-input textarea:focus,
		        .the_buddyforms_form .bf-input .form-control:focus {
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
							echo '.the_buddyforms_form .bf-input textarea::placeholder,
										.the_buddyforms_form .bf-input .form-control::placeholder {
												color: ' . $bfdesign['field_placeholder_font_color']['color'] . ';
											}';
						} ?>

					<?php } ?>


					<?php
					// only output CSS for descriptions if the option to disable CSS is unchecked
					if( $bfdesign['desc_disable_css'] == '' ) { ?>

		        /* Design Options - Descriptions */
		        .the_buddyforms_form span.help-inline,
		        .the_buddyforms_form span.help-block {
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
					if( $bfdesign['button_disable_css'] == '' ) { ?>
		        /* Design Options - Buttons */
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

		        .the_buddyforms_form form .form-actions {
		            text-align: <?php echo $bfdesign['button_alignment']; ?>;
		        }

		        <?php // Button Width Behaviour -- if always on block
						if( $bfdesign['button_width'] != 'block' ) {
							echo '@media (min-width: 768px) {
											.the_buddyforms_form .form-actions button.bf-submit {
												display: inline;
												width: auto;
											}
										}';
						} ?>

		        /* Design Options - Buttons Hover State */
		        .the_buddyforms_form .form-actions button.bf-submit:hover,
		        .the_buddyforms_form .form-actions button.bf-submit:focus {
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

	        <?php echo $bfdesign['custom_css']; ?>

	    </style>

		<?php
		$layout = ob_get_clean();

		$form_html .= $layout;

	} 


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

	$bf_button_classes = 'bf-submit ' . isset( $bfdesign['button_class'] ) && ! empty( $bfdesign['button_class'] ) ? $bfdesign['button_class'] : '';
	$bf_button_text    = isset( $bfdesign['submit_text'] ) && ! empty( $bfdesign['submit_text'] ) ? $bfdesign['submit_text'] : __( 'Submit', 'buddyforms' );

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
