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
	global $buddyforms_templates;


	$buddyforms_templates['contact']['title'] = 'Contact Form';
	$buddyforms_templates['contact']['desc'] = 'Setup a simple contact form.';

	$buddyforms_templates['registration']['title'] = 'Registration Form';
	$buddyforms_templates['registration']['desc'] = 'Setup a simple registration form.';

	$buddyforms_templates['post']['title'] = 'Post Form';
	$buddyforms_templates['post']['desc'] = 'Setup a simple post form.';

	$buddyforms_templates = apply_filters('buddyforms_templates', $buddyforms_templates);

	ob_start();

	?>
	<div class="buddyforms_template">
		<h5>You can add form fields from the sidebar or choose a pre-configured form template:</h5>

		<?php foreach($buddyforms_templates as $key => $template) { ?>
		<div class="bf-3-tile">
			<h4 class="bf-tile-title"><?php echo $template['title'] ?></h4>
			<p class="bf-tile-desc"><?php echo $template['desc'] ?></p>
			<button id="btn-compile-<?php echo $key ?>" data-template="<?php echo $key ?>" class="bf_form_template btn" onclick=""><span class="bf-plus">+</span> <?php echo $template['title'] ?></button>
		</div>
		<?php } ?>

	</div>

	<?php

	$tmp = ob_get_clean();

	return $tmp;
}
