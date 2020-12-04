<?php
/**
 * Editor Main meta-box container
 */
function buddyforms_metabox_form_editor($post){
	include BUDDYFORMS_ADMIN_VIEW.'editor/editor-container.php';
}

/**
 * Editor Form Elements
 *
 * @param $post
 * @param bool $buddyform
 */
function buddyforms_form_editor_elements($post, $buddyform = false){
	global $post, $buddyform;

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}

	// Generate the form slug from the post name
	$form_slug = ( isset( $post->post_name ) ) ? $post->post_name : '';

	if ( ! $buddyform ) {
		$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true );
	}

	// Create the form elements array
	$form_setup = array();

	// Start the form builder #buddyforms_forms_builder
	$form_setup[] = new Element_HTML( '<div id="buddyforms_forms_builder" class="buddyforms_forms_builder">' );

	// check if form elements exist for this form
	if ( isset( $buddyform['form_fields'] ) ) {

		// Create the table head to display the form elements
		$form_setup[] = new Element_HTML( '
	        <div class="fields_header">
	            <table class="wp-list-table widefat fixed posts">
	                <thead>
	                    <tr>
	                        <th class="field_order">' . __( 'Order', 'buddyforms' ) . '</th>
	                        <th class="field_label">' . __( 'Label', 'buddyforms' ) . '</th>
	                        <th class="field_name">' . __( 'Slug', 'buddyforms' ) . '</th>
	                        <th class="field_type">' . __( 'Type', 'buddyforms' ) . '</th>
	                    </tr>
	                </thead>
	            </table>
	        </div>
	    ' );
	}
	// Start the form element sortable list
	$form_setup[] = new Element_HTML( '<ul id="sortable_buddyforms_elements" class="sortable sortable_' . $form_slug . '">' );

	$has_fields = false;
	if ( ! empty( $buddyform['form_fields'] ) && is_array( $buddyform['form_fields'] ) ) {

		// Loop all form elements
		foreach ( $buddyform['form_fields'] as $field_id => $customfield ) {

			// Sanitize the field slug
			if ( isset( $customfield['slug'] ) ) {
				$field_slug = buddyforms_sanitize_slug( $customfield['slug'] );
			}

			// If the field slug is empty generate one from the name
			if ( empty( $field_slug ) ) {
				$field_slug = buddyforms_sanitize_slug( $customfield['name'] );
			}

			// Make sure we have a field slug and name
			if ( $field_slug != '' && isset( $customfield['name'] ) ) {

				// Create the field arguments array
				$args = Array(
					'field_id'   => $field_id,
					'field_type' => sanitize_title( $customfield['type'] ),
					'form_slug'  => $form_slug,
					'post_type'  => $buddyform['post_type'],
				);

				// Get the form element html and add it to the form elements array
				$form_setup[] = new Element_HTML( buddyforms_display_form_element( $args ) );
			}
		}
		$has_fields = true;
	} else {
		$form_setup[] = new Element_HTML( buddyforms_form_builder_templates() );
	}

	// End the sortable form elements list
	$form_setup[] = new Element_HTML( '</ul>' );

	$select_a_template_button = ( ! $has_fields ) ? '<input type="button" name="formbuilder-show-templates" id="formbuilder-show-templates" class="button button-primary button-large" value="' . __( 'Select a Template', 'buddyforms' ) . '">' : '';
	// Metabox footer for the form elements select
	$form_setup[] = new Element_HTML( '
		<div id="formbuilder-actions-wrap">
			<div class="formbuilder-actions-select-wrap">
				<div id="formbuilder-action-templates">
					' . $select_a_template_button . '
				</div>
				<div id="formbuilder-action-add">
					<span class="formbuilder-spinner spinner"></span>
					<input type="button" name="formbuilder-add-element" id="formbuilder-add-element" class="button button-primary button-large" value="' . __( '+ Add Field', 'buddyforms' ) . '">
				</div>
				<div id="formbuilder-action-select">
					<select id="bf_add_new_form_element">' . buddyforms_form_builder_form_elements_select() . '</select>
				</div>
			</div>
		</div>
		<div id="formbuilder-action-select-modal" class="hidden">
				<select id="bf_add_new_form_element_modal">' . buddyforms_form_builder_form_elements_select() . '</select>
		</div>
	' );

	// End #buddyforms_forms_builder wrapper
	$form_setup[] = new Element_HTML( '</div>' );

	foreach ( $form_setup as $key => $field ) {
		echo $field->getLabel();
		echo $field->getShortDesc();
		echo $field->render();
	}
}
