<?php

function buddyforms_metabox_form_elements($post, $buddyform = '') {
	global $post, $buddyform;

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}

	if(!$buddyform){
		$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true );
	}

//    echo '<pre>';
//    print_r($buddyform);
//    echo '</pre>';

	$form_slug = $post->post_name;

	$form_setup   = array();
	$form_setup[] = new Element_HTML( '<div id="buddyforms_forms_builder_' . $form_slug . '" class="buddyforms_forms_builder">' );


	if ( isset( $buddyform['form_fields'] ) ) {
		$form_setup[] = new Element_HTML( '
	        <div class="fields_header">
	            <table class="wp-list-table widefat fixed posts">
	                <thead>
	                    <tr>
	                        <th class="field_order">Field Order</th>
	                        <th class="field_label">Field Label</th>
	                        <th class="field_name">Field Slug</th>
	                        <th class="field_type">Field Type</th>
	                        <th class="field_type">Action</th>
	                    </tr>
	                </thead>
	            </table>
	        </div>
	    ' );
	}

	$form_setup[] = new Element_HTML( '<ul id="sortable_buddyforms_elements" class="sortable sortable_' . $form_slug . '">' );

	if ( isset( $buddyform['form_fields'] ) ) {

		foreach ( $buddyform['form_fields'] as $field_id => $customfield ) {

			if ( isset( $customfield['slug'] ) ) {
				$field_slug = sanitize_title( $customfield['slug'] );
			}

			if ( empty( $field_slug ) ) {
				$field_slug = sanitize_title( $customfield['name'] );
			}

			if ( $field_slug != '' && isset( $customfield['name'] ) ) {
				$args         = Array(
					'field_id'   => $field_id,
					'field_type' => sanitize_title( $customfield['type'] ),
					'form_slug'  => $form_slug,
					'post_type'  => $buddyform['post_type'],
				);
				$form_setup[] = new Element_HTML( buddyforms_display_form_element( $args ) );
			}
		}
	} else {
		$form_setup[] = new Element_HTML( buddyforms_form_builder_templates() );
	}

	$form_setup[] = new Element_HTML( '</ul>' );
	$form_setup[] = new Element_HTML( '</div>' );

	foreach ( $form_setup as $key => $field ) {
		echo $field->getLabel();
		echo $field->getShortDesc();
		echo $field->render();
	}
}

function buddyforms_form_builder_templates(){

	ob_start();

	?>
	<div class="buddyforms_template">
		<h5>Add form fields from the sidebar or use a template. Just click one of the buttons to load all fields at once.</h5>

		<button id="btn-compile" data-template="contact" class="bf_form_template btn btn-block btn-lg btn-outline" onclick="">Contact Form</button>
		<button id="btn-compile" data-template="register" class="bf_form_template btn btn-block btn-lg btn-outline" onclick="">Registration Form</button>
		<button id="btn-compile" data-template="create" class="bf_form_template btn btn-block btn-lg btn-outline" onclick="">Submit Post Form</button>
	</div>

	<?php

	$tmp = ob_get_clean();

	return $tmp;
}
