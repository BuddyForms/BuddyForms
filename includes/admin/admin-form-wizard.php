<?php

//
// Create a list of all available form builder templates
//
function buddyforms_form_builder_wizard_types(){

	$buddyforms_wizard_types['contact']['title'] = 'Contact Form';
	$buddyforms_wizard_types['contact']['desc'] = 'Setup a contact form in 4 easy steps.';

	$buddyforms_wizard_types['registration']['title'] = 'Registration Form';
	$buddyforms_wizard_types['registration']['desc'] = 'Setup a registration form.';

	$buddyforms_wizard_types['post']['title'] = 'Post Form';
	$buddyforms_wizard_types['post']['desc'] = 'Setup a post form in 6 easy steps..';

	$buddyforms_wizard_types = apply_filters('buddyforms_wizard_types', $buddyforms_wizard_types);

	ob_start();

	?>
	<div class="buddyforms_wizard_types">
		<h5>Choose a Form Type</h5>

		<?php foreach($buddyforms_wizard_types as $key => $template) { ?>
			<div class="bf-3-tile">
				<h4 class="bf-tile-title"><?php echo $template['title'] ?></h4>
				<p class="bf-tile-desc"><?php echo $template['desc'] ?></p>
				<button id="btn-compile-<?php echo $key ?>" data-type="<?php echo $key ?>" class="bf_wizard_types" ><span class="bf-plus">+</span> <?php echo $template['title'] ?></button>
			</div>
		<?php } ?>

	</div>

	<?php

	$tmp = ob_get_clean();

	echo $tmp;
	die();
}
add_action( 'wp_ajax_buddyforms_form_builder_wizard_types', 'buddyforms_form_builder_wizard_types' );



function buddyforms_form_builder_wizard_elements() {


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

//	echo '<pre>';
//	print_r($elements_select_options);
//	echo '</pre>';


	// Loop The form elements array and add the options to the select box
	if(is_array($elements_select_options)){
		foreach($elements_select_options as $optgroup_label => $optgroup){
			$el_links .= '<h5>' . $optgroup_label . '</h5>';
			foreach($optgroup as $es_val => $es_label){

				if( is_array($es_label) ){
					$el_links .= '<a href="#" class="bf_add_element_action" data-unique="' . $es_label['unique'] . '" data-fieldtype="' . $es_val . '">' . $es_label['label'] . '</a> ';
				} else {
					$el_links .= '<a href="#" class="bf_add_element_action" data-fieldtype="' . $es_val . '">' . $es_label . '</a> ' ;
				}

			}
		}
	}

	echo $el_links;



	echo $tmp;
	die();
}
add_action( 'wp_ajax_buddyforms_form_builder_wizard_elements', 'buddyforms_form_builder_wizard_elements' );