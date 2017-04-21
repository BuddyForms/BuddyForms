<?php

add_action( 'buddyforms_form_setup_nav_li_last', 'buddyforms_form_setup_nav_li_layout' );
function buddyforms_form_setup_nav_li_layout() { ?>
	<li class="layout_nav"><a class="layout" href="#layout" data-toggle="tab"><?php _e( 'Layout', 'buddyforms' ); ?></a></li><?php
}

add_action( 'buddyforms_form_setup_tab_pane_last', 'buddyforms_form_setup_tab_pane_layout' );
function buddyforms_form_setup_tab_pane_layout() { ?>
	<div class="tab-pane fade in" id="layout">
	<div class="buddyforms_accordion_layout">
		<?php buddyforms_layout_screen(); ?>
	</div>
	</div><?php
}


function buddyforms_layout_screen($option_name = "buddyforms_options") {
	global $buddyform;

	$form_setup = array();

	if ( ! $buddyform ) {
		$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true );
	}


//	echo '<h4>' . __( 'Define the form Layout', 'buddyforms' ) . '</h4><br>';
	
//	$template = isset( $buddyform['template'] ) ? $buddyform['template'] : 'above';
//	$form_setup[]        = new Element_Select( '<b>' . __( 'Form Template', 'buddyforms' ) . '</b>', "buddyforms_options[template]", array(
//		'defaul'  => __( 'Default', 'buddyforms' ),
//		'inline' => __( 'Inline', 'buddyforms' ),
//		'vertical' => __( 'Vertical', 'buddyforms' ),
//		'bootstrap' => __( 'Bootstrap', 'buddyforms' ),
//		'sidebyside3' => __( 'SideBySide BootStrap 3', 'buddyforms' ),
//		'sidebyside4' => __( 'SideBySide BootStrap 4', 'buddyforms' ),
//	), array(
//		'value'     => $template,
//		'shortDesc' => 'Select the Form Template. You can create a custom Form Template and overwrite it in your theme like normal Page Templates.'
//	) );



	$form_setup[]        = new Element_HTML('<h4>Form Layout</h4>' );

	$layout = isset( $buddyform['layout'] ) ? $buddyform['layout'] : 'block';
	$form_setup[]        = new Element_Radio( '<b>' . __( 'Inline or Block?', 'buddyforms' ) . '</b>', $option_name . "[layout]", array(
		'block'  => __( 'Block', 'buddyforms' ),
		'inline' => __( 'Inline', 'buddyforms' ),
	), array(
		'value'     => $layout,
		'shortDesc' => 'Display Labels inside the form element ( Inline ) or over the element ( Block )'
	) );


	$form_setup[]        = new Element_HTML('<h4>Form Field Design</h4>' );

	$field_padding = isset( $buddyform['field_padding'] ) ? $buddyform['field_padding'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Field Padding', 'buddyforms' ) . '</b>', $option_name . "[field_padding]", array(
		'value'     => $field_padding,
		'shortDesc' => ''
	) );

	$field_background_color = isset( $buddyform['field_background_color'] ) ? $buddyform['field_background_color'] : '';
	$form_setup[]        = new Element_Color('<b>' . __( 'Field Background Color ', 'buddyforms' ) . '</b>', $option_name . "[field_background_color]", array(
		'value'     => $field_background_color,
		'shortDesc' => ''
	) );

	$field_border_color = isset( $buddyform['field_border_color'] ) ? $buddyform['field_border_color'] : '';
	$form_setup[]        = new Element_Color('<b>' . __( 'Field Border Color', 'buddyforms' ) . '</b>', $option_name . "[field_border_color]", array(
		'value'     => $field_border_color,
		'shortDesc' => ''
	) );

	$field_border_width = isset( $buddyform['field_border_width'] ) ? $buddyform['field_border_width'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Field Border Width', 'buddyforms' ) . '</b>', $option_name . "[field_border_width]", array(
		'value'     => $field_border_width,
		'shortDesc' => ''
	) );

	$field_box_shadow = isset( $buddyform['field_box_shadow'] ) ? $buddyform['field_box_shadow'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Field Box Shadow', 'buddyforms' ) . '</b>', $option_name . "[field_box_shadow]", array(
		'value'     => $field_box_shadow,
		'shortDesc' => ''
	) );


	$form_setup[]        = new Element_HTML('<h4>Form Field Typography</h4>' );

 	$field_font_size = isset( $buddyform['field_font_size'] ) ? $buddyform['field_font_size'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Field Font Size', 'buddyforms' ) . '</b>', $option_name . "[field_font_size]", array(
		'value'     => $field_font_size,
		'shortDesc' => ''
	) );

	$field_font_color = isset( $buddyform['field_font_color'] ) ? $buddyform['field_font_color'] : '';
	$form_setup[]        = new Element_Color('<b>' . __( 'Field Font Color', 'buddyforms' ) . '</b>', $option_name . "[field_font_color]", array(
		'value'     => $field_font_color,
		'shortDesc' => ''
	) );

	$field_placeholder_font_color = isset( $buddyform['field_placeholder_font_color'] ) ? $buddyform['field_placeholder_font_color'] : '';
	$form_setup[]        = new Element_Color('<b>' . __( 'Field Placeholder Font Color', 'buddyforms' ) . '</b>', $option_name . "[field_placeholder_font_color]", array(
		'value'     => $field_placeholder_font_color,
		'shortDesc' => ''
	) );

	$field_font_weight = isset( $buddyform['field_font_weight'] ) ? $buddyform['field_font_weight'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Field Font Weight ', 'buddyforms' ) . '</b>', $option_name . "[field_font_weight]", array(
		'value'     => $field_font_weight,
		'shortDesc' => ''
	) );

	$field_font_style = isset( $buddyform['field_font_style'] ) ? $buddyform['field_font_style'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Field Font Style', 'buddyforms' ) . '</b>', $option_name . "[field_font_style]", array(
		'value'     => $field_font_style,
		'shortDesc' => ''
	) );

	$form_setup[]        = new Element_HTML('<h4>Form Field Active/Focused</h4>' );

	$field_active_background_color = isset( $buddyform['field_active_background_color'] ) ? $buddyform['field_active_background_color'] : '';
	$form_setup[]        = new Element_Color('<b>' . __( 'Field Active Background Color', 'buddyforms' ) . '</b>', $option_name . "[field_active_background_color]", array(
		'value'     => $field_active_background_color,
		'shortDesc' => ''
	) );

	$field_active_border_color = isset( $buddyform['field_active_border_color'] ) ? $buddyform['field_active_border_color'] : '';
	$form_setup[]        = new Element_Color('<b>' . __( 'Field Active Border Color', 'buddyforms' ) . '</b>', $option_name . "[field_active_border_color]", array(
		'value'     => $field_active_border_color,
		'shortDesc' => ''
	) );

	$field_active_font_color = isset( $buddyform['field_active_font_color'] ) ? $buddyform['field_active_font_color'] : '';
	$form_setup[]        = new Element_Color('<b>' . __( 'Field Active Font Color', 'buddyforms' ) . '</b>', $option_name . "[field_active_font_color]", array(
		'value'     => $field_active_font_color,
		'shortDesc' => ''
	) );

	$form_setup[]        = new Element_HTML('<h4>Labels</h4>' );

	$labels_layout = isset( $buddyform['labels_layout'] ) ? $buddyform['labels_layout'] : 'block';
	$form_setup[]        = new Element_Radio( '<b>' . __( 'Labels Inline or Block?', 'buddyforms' ) . '</b>', $option_name . "[labels_layout]", array(
		'block'  => __( 'Block', 'buddyforms' ),
		'inline' => __( 'Inline', 'buddyforms' ),
	), array(
		'value'     => $labels_layout,
		'shortDesc' => 'Display Labels inside the form element ( Inline ) or over the element ( Block )'
	) );

	$label_padding = isset( $buddyform['label_padding'] ) ? $buddyform['label_padding'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Label Padding', 'buddyforms' ) . '</b>', $option_name . "[label_padding]", array(
		'value'     => $label_padding,
		'shortDesc' => ''
	) );


	$label_font_size = isset( $buddyform['label_font_size'] ) ? $buddyform['label_font_size'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Label Font Size', 'buddyforms' ) . '</b>', $option_name . "[label_font_size]", array(
		'value'     => $label_font_size,
		'shortDesc' => ''
	) );

	$label_font_color = isset( $buddyform['label_font_color'] ) ? $buddyform['label_font_color'] : '';
	$form_setup[]        = new Element_Color('<b>' . __( 'Label Font Color', 'buddyforms' ) . '</b>', $option_name . "[label_font_color]", array(
		'value'     => $label_font_color,
		'shortDesc' => ''
	) );

	$label_ont_weight = isset( $buddyform['label_ont_weight'] ) ? $buddyform['label_ont_weight'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Label Font Weight ', 'buddyforms' ) . '</b>', $option_name . "[label_ont_weight]", array(
		'value'     => $label_ont_weight,
		'shortDesc' => ''
	) );

	$label_ont_style = isset( $buddyform['label_ont_style'] ) ? $buddyform['label_ont_style'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Label Font Style', 'buddyforms' ) . '</b>', $option_name . "[label_ont_style]", array(
		'value'     => $label_ont_style,
		'shortDesc' => ''
	) );


	$form_setup[]        = new Element_HTML('<h4>Buttons</h4>' );

	$button_class = isset( $buddyform['button_class'] ) ? $buddyform['button_class'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Add a custom class to button', 'buddyforms' ) . '</b>', $option_name . "[button_class]", array(
		'value'     => $button_class,
		'shortDesc' => ''
	) );

	$submit_text = isset( $buddyform['submit_text'] ) ? $buddyform['submit_text'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Default Button Submit Text', 'buddyforms' ) . '</b>', $option_name . "[submit_text]", array(
		'value'     => $submit_text,
		'shortDesc' => ''
	) );

	$button_padding = isset( $buddyform['button_padding'] ) ? $buddyform['button_padding'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Button Padding', 'buddyforms' ) . '</b>', $option_name . "[button_padding]", array(
		'value'     => $button_padding,
		'shortDesc' => ''
	) );

	$button_background_color = isset( $buddyform['button_background_color'] ) ? $buddyform['button_background_color'] : '';
	$form_setup[]        = new Element_Color('<b>' . __( 'Button Background Color', 'buddyforms' ) . '</b>', $option_name . "[button_background_color]", array(
		'value'     => $button_background_color,
		'shortDesc' => ''
	) );

	$button_font_color = isset( $buddyform['button_font_color'] ) ? $buddyform['button_font_color'] : '';
	$form_setup[]        = new Element_Color('<b>' . __( 'Button Font Color', 'buddyforms' ) . '</b>', $option_name . "[button_font_color]", array(
		'value'     => $button_font_color,
		'shortDesc' => ''
	) );

	$button_font_size = isset( $buddyform['button_font_size'] ) ? $buddyform['button_font_size'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Button Font Color', 'buddyforms' ) . '</b>', $option_name . "[button_font_size]", array(
		'value'     => $button_font_size,
		'shortDesc' => ''
	) );

	$button_border_width = isset( $buddyform['button_border_width'] ) ? $buddyform['button_border_width'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Button Border Width', 'buddyforms' ) . '</b>', $option_name . "[button_border_width]", array(
		'value'     => $button_border_width,
		'shortDesc' => ''
	) );

	$button_border_color = isset( $buddyform['button_border_color'] ) ? $buddyform['button_border_color'] : '';
	$form_setup[]        = new Element_Color('<b>' . __( 'Button Border Color', 'buddyforms' ) . '</b>', $option_name . "[button_border_color]", array(
		'value'     => $button_border_color,
		'shortDesc' => ''
	) );

	$button_text_shadow = isset( $buddyform['button_text_shadow'] ) ? $buddyform['button_text_shadow'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Button Text Shadow', 'buddyforms' ) . '</b>', $option_name . "[button_text_shadow]", array(
		'value'     => $button_text_shadow,
		'shortDesc' => ''
	) );

	$button_box_shadow = isset( $buddyform['button_box_shadow'] ) ? $buddyform['button_box_shadow'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Button Box Shadow', 'buddyforms' ) . '</b>', $option_name . "[button_box_shadow]", array(
		'value'     => $button_box_shadow,
		'shortDesc' => ''
	) );

	$form_setup[]        = new Element_HTML('<h4>Hover Status</h4>' );

	$button_background_color_hover = isset( $buddyform['button_background_color_hover'] ) ? $buddyform['button_background_color_hover'] : '';
	$form_setup[]        = new Element_Color('<b>' . __( 'Button Background Color Hover', 'buddyforms' ) . '</b>', $option_name . "[button_background_color_hover]", array(
		'value'     => $button_background_color_hover,
		'shortDesc' => ''
	) );

	$button_font_color_hover = isset( $buddyform['button_font_color_hover'] ) ? $buddyform['button_font_color_hover'] : '';
	$form_setup[]        = new Element_Color('<b>' . __( 'Button Font Color Hover', 'buddyforms' ) . '</b>', $option_name . "[button_font_color_hover]", array(
		'value'     => $button_font_color_hover,
		'shortDesc' => ''
	) );

	$button_border_color_hover = isset( $buddyform['button_border_color_hover'] ) ? $buddyform['button_border_color_hover'] : '';
	$form_setup[]        = new Element_Color('<b>' . __( 'Button Border Color Hover', 'buddyforms' ) . '</b>', $option_name . "[button_border_color_hover]", array(
		'value'     => $button_border_color_hover,
		'shortDesc' => ''
	) );

	$button_text_shadow_hover = isset( $buddyform['button_text_shadow_hover'] ) ? $buddyform['button_text_shadow_hover'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Button Text Shadow Hover', 'buddyforms' ) . '</b>', $option_name . "[button_text_shadow_hover]", array(
		'value'     => $button_text_shadow_hover,
		'shortDesc' => ''
	) );

	$button_box_shadow_hover = isset( $buddyform['button_box_shadow_hover'] ) ? $buddyform['button_box_shadow_hover'] : '';
	$form_setup[]        = new Element_Textbox('<b>' . __( 'Button Box Shadow Hover', 'buddyforms' ) . '</b>', $option_name . "[button_box_shadow_hover]", array(
		'value'     => $button_box_shadow_hover,
		'shortDesc' => ''
	) );

	$form_setup[]        = new Element_HTML('<h4>Custom CSS</h4>' );
	$custom_css = isset( $buddyform['custom_css'] ) ? $buddyform['custom_css'] : '';
	$form_setup[] = new Element_Textarea( '<b>' . __( 'Custom CSS', 'buddyforms' ) . '</b>', $option_name . "[custom_css]", array(
		'rows'  => 3,
		'style' => "width:100%",
		'class' => 'display_message display_form',
		'value' => $custom_css,
		'id'    => 'custom_css',
		'shortDesc' => __( 'Add custom styles to the form', 'buddyforms' )
	) );

	buddyforms_display_field_group_table( $form_setup );
}


