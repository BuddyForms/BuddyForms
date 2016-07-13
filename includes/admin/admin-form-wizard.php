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
		<?php foreach($buddyforms_wizard_types as $key => $template) { ?>
			<div class="bf-3-tile">
				<h4 class="bf-tile-title"><?php echo $template['title'] ?></h4>
				<p class="bf-tile-desc"><?php echo $template['desc'] ?></p>
				<button id="btn-compile-<?php echo $key ?>" data-type="<?php echo $key ?>" class="bf_wizard_types"><span class="bf-plus">+</span> <?php echo $template['title'] ?></button>
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

	$type = $_POST['type'];
//	echo 'type';

	$allowed_fields['contact'] = array('basic','contact','extra');
	$allowed_fields['registration'] = array('basic','contact','registration','extra');
	$allowed_fields['post'] = array('basic','contact','registration','post','extra');


//	echo $type;
//
//	echo '<pre>';
//	print_r($allowed_fields);
//	echo '</pre>';


	$elements_select_options = bf_form_elements_select_options();


//	echo '<pre>';
//	print_r($elements_select_options);
//	echo '</pre>';

	foreach($allowed_fields[$type] as $key => $t){
		$elements_select_options_new[$t] = $elements_select_options[$t];
	}

//	echo '<pre>';
//	print_r($elements_select_options_new);
//	echo '</pre>';


	// Loop The form elements array and add the options to the select box
	if(is_array($elements_select_options_new)){
		foreach($elements_select_options_new as $optgroup_label => $optgroup){
			$el_links .= '<h5>' . $optgroup['label'] . '</h5>';
			foreach($optgroup['fields'] as $es_val => $es_label){

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