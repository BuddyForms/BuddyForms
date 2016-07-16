<?php

//
// Create a list of all available form builder templates
//
function buddyforms_form_builder_wizard_types(){

	$buddyforms_wizard_types['contact']['title'] = 'Contact Form';
	$buddyforms_wizard_types['contact']['desc'] = 'Setup a contact form in 4 easy steps.';

	$buddyforms_wizard_types['registration']['title'] = 'Registration Form';
	$buddyforms_wizard_types['registration']['desc'] = 'Create a custom sign up form in 6 easy steps.';

	$buddyforms_wizard_types['post']['title'] = 'Post Form';
	$buddyforms_wizard_types['post']['desc'] = 'Let your users submit content to your site.';

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
					$el_links .= '<a href="#" class="bf_add_element_action button" data-unique="' . $es_label['unique'] . '" data-fieldtype="' . $es_val . '">' . $es_label['label'] . '</a> ';
				} else {
					$el_links .= '<a href="#" class="bf_add_element_action button" data-fieldtype="' . $es_val . '">' . $es_label . '</a> ' ;
				}

			}
		}
	}

	echo $el_links;
	die();
}
add_action( 'wp_ajax_buddyforms_form_builder_wizard_elements', 'buddyforms_form_builder_wizard_elements' );


function buddyforms_form_builder_wizard_save(){

	if ( isset( $_POST['FormData'] ) ) {
		parse_str( $_POST['FormData'], $formdata );
		$_POST = $formdata;
	}

	$formdata['post_status'] = 'publish';
	$formdata['ID'] = $formdata['post_ID'];

	$form = wp_insert_post($formdata);

	if ( ! isset( $formdata['buddyforms_options'] ) ) {
		return;
	}

	$post = get_post($form);
	$buddyform = $formdata['buddyforms_options'];

	// Add post title as form name and post name as form slug.
	$buddyform['name'] = $post->post_title;
	$buddyform['slug'] = $post->post_name;

	// make sure the form fields slug and type is sanitised
	if ( isset( $buddyform['form_fields'] ) ) : foreach ( $buddyform['form_fields'] as $key => $field ) {
		$buddyform['form_fields'][ $key ]['slug'] = sanitize_title( $field['slug'] );
		$buddyform['form_fields'][ $key ]['type'] = sanitize_title( $field['type'] );
	} endif;

	// Update post meta
	update_post_meta( $form, '_buddyforms_options', $buddyform );

	// Save the Roles and capabilities.
	if ( isset( $formdata['buddyforms_roles'] ) ) {

		foreach ( get_editable_roles() as $role_name => $role_info ):
			$role = get_role( $role_name );
			foreach ( $role_info['capabilities'] as $capability => $_ ):

				$capability_array = explode( '_', $capability );

				if ( $capability_array[0] == 'buddyforms' ) {
					if ( $capability_array[1] == $buddyform['slug'] ) {

						$role->remove_cap( $capability );

					}
				}

			endforeach;
		endforeach;

		foreach ( $formdata['buddyforms_roles'] as $form_role => $capabilities ) {
			foreach ( $capabilities as $key => $capability ) {
				$role = get_role( $key );
				foreach ( $capability as $key_cap => $cap ) {
					$role->add_cap( $cap );
				}
			}

		}

	}

	// Regenerate the global $buddyforms.
	// The global$buddyforms is sored in the option table and provides all forms and form fields
	buddyforms_regenerate_global_options();

	// Rewrite the page roles and flash permalink if needed
	buddyforms_attached_page_rewrite_rules( true );


	$url =  admin_url().'post.php?post=' . $form. '&action=edit&wizard=done';
	echo $url;
	die();

}
add_action( 'wp_ajax_buddyforms_form_builder_wizard_save', 'buddyforms_form_builder_wizard_save' );







function buddyforms_wizard_done(){
	global $post;

	echo '<pre>';
	print_r($_POST);
	echo '</pre>';

	if(!isset($_GET['wizard'])){
		return;
	}

	$type =  get_post_type();

	switch ($type){
		case "buddyforms":
			$url=  admin_url().'post.php?post=' . $post->ID . '&action=edit&wizard=done';
			wp_redirect($url);
			exit;
			break;
	}
}
//add_action('save_post','buddyforms_wizard_done');
