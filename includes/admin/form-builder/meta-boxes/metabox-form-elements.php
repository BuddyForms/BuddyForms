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
		<h5>You can add form fields from the sidebar or choose a pre-configured form template:</h5>

		<div class="bf-3-tile">
			<h4 class="bf-tile-title">Contact Form</h4>
			<p class="bf-tile-desc">Setup a simple contact form.</p>
			<button id="btn-compile-contact" data-template="contact" class="bf_form_template btn" onclick=""><span class="bf-plus">+</span> Contact Form</button>
		</div>

		<div class="bf-3-tile">
			<h4 class="bf-tile-title">Registration Form</h4>
			<p class="bf-tile-desc">Setup a simple registration form.</p>
			<button id="btn-compile-register" data-template="register" class="xmar bf_form_template btn" onclick=""><span class="bf-plus">+</span> Registration Form</button>
		</div>

		<div class="bf-3-tile">
			<h4 class="bf-tile-title">Post Form</h4>
			<p class="bf-tile-desc">Setup a simple post form.</p>
			<button id="btn-compile-posts" data-template="create" class="xmar bf_form_template btn" onclick=""><span class="bf-plus">+</span> Post Form</button>
		</div>

	</div>

	<?php

	$tmp = ob_get_clean();

	return $tmp;
}
