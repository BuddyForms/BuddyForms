<?php

function buddyforms_metabox_form_elements($post, $buddyform = '') {
	global $post, $buddyform;

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}

	if(!$buddyform){
		$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true );
	}

	// Generate the form slug from the post name
	$form_slug = $post->post_name;

	// Create the form elements array
	$form_setup   = array();

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

		// Start the form element sortable list
		$form_setup[] = new Element_HTML( '<ul id="sortable_buddyforms_elements" class="sortable sortable_' . $form_slug . '">' );

		// Loop all form elements
		foreach ( $buddyform['form_fields'] as $field_id => $customfield ) {

			// Sanitize the field slug
			if ( isset( $customfield['slug'] ) ) {
				$field_slug = sanitize_title( $customfield['slug'] );
			}

			// If the field slug is empty generate one from the name
			if ( empty( $field_slug ) ) {
				$field_slug = sanitize_title( $customfield['name'] );
			}

			// Make sure we have a field slug and name
			if ( $field_slug != '' && isset( $customfield['name'] ) ) {

				// Create the field arguments array
				$args         = Array(
					'field_id'   => $field_id,
					'field_type' => sanitize_title( $customfield['type'] ),
					'form_slug'  => $form_slug,
					'post_type'  => $buddyform['post_type'],
				);

				// Get the form element html and add it to the form elements array
				$form_setup[] = new Element_HTML( buddyforms_display_form_element( $args ) );
			}
		}
	} else {
		$form_setup[] = new Element_HTML( buddyforms_form_builder_templates() );
	}

	// End the sortable form elements list
	$form_setup[] = new Element_HTML( '</ul>' );

	// Metabox footer for the form elements select
	$form_setup[] = new Element_HTML( '
		<div id="formbuilder-actions-wrap">
			<div id="formbuilder-action-select">
				<select id="bf_add_new_form_element">' . buddyforms_form_builder_form_elements_select() . '</select>
			</div>
			<div id="formbuilder-action-add">
				<span class="formbuilder-spinner spinner"></span>
				<input type="button" name="formbuilder-add-element" id="formbuilder-add-element" class="button button-primary button-large" value="Add">
			</div>
			<div class="clear"></div>
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

//
// Create a list of all available form builder templates
//
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

//
// generate the form builder form elements select options
//
function buddyforms_form_builder_form_elements_select(){
	// Create the form elements array
	$elements_select_options = array(
		__('Basic', 'buddyforms')   => array(
			'text'          => __( 'Text', 'buddyforms' ),
			'textarea'      => __( 'Textarea', 'buddyforms' ),
			'dropdown'      => __( 'Dropdown', 'buddyforms' ),
			'radiobutton'   => __( 'Radiobutton', 'buddyforms' ),
			'checkbox'      => __( 'Checkbox', 'buddyforms' ),
		),
		__('Post Fields', 'buddyforms') => array(
			'title' => array(
				'label'     => __( 'Title', 'buddyforms' ),
				'unique'    => 'unique'
			),
			'content'   => array(
				'label'     => __( 'Content', 'buddyforms' ),
				'unique'    => 'unique'
			),
			'taxonomy'  => array(
				'label'     => __( 'Taxonomy', 'buddyforms' ),
			),
			'comments'  => array(
				'label'     => __( 'Comments', 'buddyforms' ),
				'unique'    => 'unique'
			),
			'status'    => array(
				'label'     => __( 'Post Status', 'buddyforms' ),
				'unique'    => 'unique'
			),
			'featured_image'    => array(
				'label'     => __( 'Featured Image', 'buddyforms' ),
				'unique'    => 'unique'
			),
		),
		__('Contact Fields', 'buddyforms')   => array(
			'text'      => __( 'Name', 'buddyforms' ),
			'link'      => __( 'Link', 'buddyforms' ),
			'mail'      => __( 'Mail', 'buddyforms' ),
		),
		__('Registration Fields', 'buddyforms') => array(
			'login' => array(
				'label'     => __( 'Login Form', 'buddyforms' ),
				'unique'    => 'unique'
			),
			'registration'   => array(
				'label'     => __( 'Registration', 'buddyforms' ),
				'unique'    => 'unique'
			),
			'recaptcha'  => array(
				'label'     => __( 'reCAPTCHA', 'buddyforms' ),
				'unique'    => 'unique'
			),
		),
		__('Extra Fields', 'buddyforms') => array(
			'file'      => __( 'File', 'buddyforms' ),
			'hidden'    => __( 'Hidden', 'buddyforms' ),
			'number'    => __( 'Number', 'buddyforms' ),
			'html'      => __( 'HTML', 'buddyforms' ),
			'date'      => __( 'Date', 'buddyforms' ),
		)
	);

	// Allow others to filter the array
	$elements_select_options = apply_filters( 'buddyforms_add_form_element_to_select', $elements_select_options );

	// Add a default value
	$el_sel_options = '<option value="none">Field Type</option>';

	// Loop The form elements array and add the options to the select box
	if(is_array($elements_select_options)){
		foreach($elements_select_options as $optgroup_label => $optgroup){
			$el_sel_options .= '<optgroup label="' . $optgroup_label . '">';
			foreach($optgroup as $es_val => $es_label){
				if( is_array($es_label) ){
					$el_sel_options .= '<option data-unique="' . $es_label['unique'] . '" value="' . $es_val . '">' . $es_label['label'] . '</option>';
				} else {
					$el_sel_options .= '<option value="' . $es_val . '">' . $es_label . '</option>' ;
				}
			}
			$el_sel_options .= '</optgroup>';
		}
	}

	// Return the options
	return $el_sel_options;
}
