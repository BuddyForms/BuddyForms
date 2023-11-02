<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }


/**
 * Register the option metabox
 */
function buddyforms_list_all_post_fields_admin_settings_sidebar_metabox() {
	add_meta_box( 'buddyforms_list_all_post_fields', __( 'Display Form Elements on the Single View ', 'buddyforms' ), 'buddyforms_list_all_post_fields_admin_settings_sidebar_metabox_html', 'buddyforms', 'normal', 'low' );
	add_filter( 'postbox_classes_buddyforms_buddyforms_list_all_post_fields', 'buddyforms_metabox_class' );
	add_filter( 'postbox_classes_buddyforms_buddyforms_list_all_post_fields', 'buddyforms_metabox_hide_if_form_type_register' );
	add_filter( 'postbox_classes_buddyforms_buddyforms_list_all_post_fields', 'buddyforms_metabox_show_if_attached_page' );
}

add_filter( 'add_meta_boxes', 'buddyforms_list_all_post_fields_admin_settings_sidebar_metabox' );

/**
 * Form options
 */
function buddyforms_list_all_post_fields_admin_settings_sidebar_metabox_html() {
	global $post;

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}

	$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true );

	$form_setup = array();

	// Add field data as table
	$hook_fields_list_on_single = isset( $buddyform['hook_fields_list_on_single'] ) ? $buddyform['hook_fields_list_on_single'] : '';
	$form_setup[]               = new Element_Checkbox(
		'<b>' . __( 'Add Form Elements as Table', 'buddyforms' ) . '</b>',
		'buddyforms_options[hook_fields_list_on_single]',
		array( 'integrate' => 'Integrate this Form' ),
		array(
			'value'     => $hook_fields_list_on_single,
			'shortDesc' => __(
				'This option will not work if you have a Template page selected.',
				'buddyforms'
			),
		)
	);

	// Add option to select the page to use as template
	// Get all allowed pages
	$form_attached_page = array();
	if ( ! empty( $buddyform['attached_page'] ) ) {
		$form_attached_page[] = $buddyform['attached_page'];
	}
	$all_pages     = buddyforms_hooks_fields_get_templates();
	$attached_page = isset( $buddyform['hook_fields_template_page'] ) ? $buddyform['hook_fields_template_page'] : '';
	$form_setup[]  = new Element_Select(
		'<b>' . __( 'Template page', 'buddyforms' ) . '</b>',
		'buddyforms_options[hook_fields_template_page]',
		$all_pages,
		array(
			'value'     => $attached_page,
			'shortDesc' => sprintf( '%s <a href="https://docs.buddyforms.com/article/641-page-template?utm_source=plugin" target="_blank">%s</a>', __( 'This is a template page to override the output of a single post.', 'buddyforms' ), __( 'Read more in the documentation.', 'buddyforms' ) ),
			'id'        => 'attached_page',
		)
	);

	// Add option to hide the title
	$hide_title   = isset( $buddyform['hook_fields_hide_title'] ) ? $buddyform['hook_fields_hide_title'] : '';
	$form_setup[] = new Element_Checkbox(
		'<b>' . __( 'Hide the title ', 'buddyforms' ) . '</b>',
		'buddyforms_options[hook_fields_hide_title]',
		array( 'yes' => __( 'Disable the post title', 'buddyforms' ) ),
		array(
			'value'     => $hide_title,
			'shortDesc' => __(
				'Use this option if you override the Title with a template shortcode.',
				'buddyforms'
			),
		)
	);

	// Add Show edit link feature
	// Add option to hide the title
	$show_edit_link = isset( $buddyform['hook_fields_show_edit_link'] ) ? $buddyform['hook_fields_show_edit_link'] : '';
	$form_setup[]   = new Element_Checkbox(
		'<b>' . __( 'Show Edit Link ', 'buddyforms' ) . '</b>',
		'buddyforms_options[hook_fields_show_edit_link]',
		array( 'yes' => __( 'Show Edit Link', 'buddyforms' ) ),
		array(
			'value'     => $show_edit_link,
			'shortDesc' => __(
				'Use this option to show a Edit link on the front end',
				'buddyforms'
			),
		)
	);

	buddyforms_display_field_group_table( $form_setup );
}

/**
 * Add option inside each field
 *
 * @param $form_fields
 * @param $field_type
 * @param $field_id
 *
 * @return mixed
 */
function buddyforms_hook_options_into_formfields( $form_fields, $field_type, $field_id ) {
	global $post;

	if ( empty( $post ) || empty( $post->ID ) ) {
		return $form_fields;
	}

	$buddyform = get_post_meta( $post->ID, '_buddyforms_options', true );

	if ( empty( $buddyform ) ) {
		return $form_fields;
	}

	$hooks = array( 'no', 'before_the_title', 'after_the_title', 'before_the_content', 'after_the_content' );
	$hooks = apply_filters( 'buddyforms_hook_field_form_element_position', $hooks );

	$form_fields['hooks']['html_display'] = new Element_HTML( '<div class="bf_element_display">' );

	$display = 'false';
	if ( isset( $buddyform['form_fields'][ $field_id ]['display'] ) ) {
		$display = $buddyform['form_fields'][ $field_id ]['display'];
	}

	$form_fields['hooks']['display'] = new Element_Select(
		'Display where?',
		'buddyforms_options[form_fields][' . $field_id . '][display]',
		$hooks,
		array(
			'value'     => $display,
			'shortDesc' => __( 'This only works for the single view.', 'buddyforms' ),
		)
	);

	$hook = '';
	if ( isset( $buddyform['form_fields'][ $field_id ]['hook'] ) ) {
		$hook = $buddyform['form_fields'][ $field_id ]['hook'];
	}

	$form_fields['hooks']['hook'] = new Element_Textbox(
		'Add <b>hook</b> name',
		'buddyforms_options[form_fields][' . $field_id . '][hook]',
		array(
			'value'     => $hook,
			'shortDesc' => __( 'This option give the ability to place the output of the field to other action. It works global.', 'buddyforms' ),
		)
	);

	$display_name = 'false';
	if ( isset( $buddyform['form_fields'][ $field_id ]['display_name'] ) ) {
		$display_name = $buddyform['form_fields'][ $field_id ]['display_name'];
	}
	$form_fields['hooks']['display_name'] = new Element_Checkbox(
		'Display the label?',
		'buddyforms_options[form_fields][' . $field_id . '][display_name]',
		array( '' => __( 'Show the Field Label with the Field value.', 'buddyforms' ) ),
		array(
			'value' => $display_name,
			'id'    => 'buddyforms_options[form_fields][' . $field_id . '][display_name]',
		)
	);

	if ( $field_type === 'upload' || $field_type === 'file' ) {

		$sizes = wp_get_registered_image_subsizes();
		$sizes = array_keys( $sizes );

		$selected_size = false;
		if ( isset( $buddyform['form_fields'][ $field_id ]['thumbnail_size'] ) ) {
			$selected_size = $buddyform['form_fields'][ $field_id ]['thumbnail_size'];
		}

		$form_fields['hooks']['thumbnail_size'] = new Element_Select(
			'Thumbnail size',
			'buddyforms_options[form_fields][' . $field_id . '][thumbnail_size]',
			$sizes,
			array(
				'value'     => $selected_size,
				'shortDesc' => __( 'This option give the ability to control the size for uploaded images, Eg: thumbnail, medium, large.', 'buddyforms' ),
			)
		);

	}

	$form_fields['hooks']['html_display_end'] = new Element_HTML( '</div>' );

	return $form_fields;
}

add_filter( 'buddyforms_formbuilder_fields_options', 'buddyforms_hook_options_into_formfields', 2, 3 );
