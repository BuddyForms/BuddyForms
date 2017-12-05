<?php

// Hooks near the bottom of profile page (if current user)
add_action( 'show_user_profile', 'custom_user_profile_fields' );

// Hooks near the bottom of the profile page (if not current user)
add_action( 'edit_user_profile', 'custom_user_profile_fields' );

// @param WP_User $user
function custom_user_profile_fields( $user ) {

	global $buddyforms;

	if ( isset( $buddyforms ) ) {
		foreach ( $buddyforms as $form_slug => $buddyform ) {
			if ( $buddyform['form_type'] == 'registration' && isset( $buddyform['form_fields'] ) ) {

				$form_setup = array();
				echo '<h2>' . $buddyform['name'] . '</h2>';
				foreach ( $buddyform['form_fields'] as $key => $user_meta ) {

					if ( substr( $user_meta['type'], 0, 5 ) != 'user_' ) {

						$name = $user_meta['name'];
						$slug = $user_meta['slug'];

						$element_attr = array(
							'value' => esc_attr( get_the_author_meta( $user_meta['slug'], $user->ID ) )
						);


						switch ( sanitize_title( $user_meta['type'] ) ) {

							case 'subject':
								$form_setup[] = new Element_Textbox( $name, $slug, $element_attr );
								break;

							case 'message':
								$form_setup[] = new Element_Textarea( $name, $slug, $element_attr );
								break;

							case 'number':
								$form_setup[] = new Element_Number( $name, $slug, $element_attr );
								break;

							case 'html':
								$form_setup[] = new Element_HTML( $user_meta['html'] );
								break;

							case 'date':
								$form_setup[] = new Element_Date( $name, $slug, $element_attr );
								break;

							case 'mail' :
								$form_setup[] = new Element_Email( $name, $slug, $element_attr );
								break;

							case 'radiobutton' :
								if ( isset( $user_meta['options'] ) && is_array( $user_meta['options'] ) ) {

									$options = Array();
									foreach ( $user_meta['options'] as $key => $option ) {
										$options[ $option['value'] ] = $option['label'];
									}
									$element = new Element_Radio( $name, $slug, $options, $element_attr );

									$form_setup[] = $element;

								}
								break;

							case 'checkbox' :

								if ( isset( $user_meta['options'] ) && is_array( $user_meta['options'] ) ) {

									$options = Array();
									foreach ( $user_meta['options'] as $key => $option ) {
										$options[ $option['value'] ] = $option['label'];
									}
									$element = new Element_Checkbox( $name, $slug, $options, $element_attr );

									$form_setup[] = $element;

								}
								break;

							case 'dropdown' :

								if ( isset( $user_meta['options'] ) && is_array( $user_meta['options'] ) ) {

									$options = Array();
									foreach ( $user_meta['options'] as $key => $option ) {
										$options[ $option['value'] ] = $option['label'];
									}

									$element_attr['class'] = $element_attr['class'] . ' bf-select2';
									$element               = new Element_Select( $name, $slug, $options, $element_attr );

									if ( isset( $user_meta['multiple'] ) && is_array( $user_meta['multiple'] ) ) {
										$element->setAttribute( 'multiple', 'multiple' );
									}

									$form_setup[] = $element;
								}
								break;

							case 'textarea' :
								$form_setup[] = new Element_Textarea( $name, $slug, $element_attr );
								break;

							case 'text' :
								$form_setup[] = new Element_Textbox( $name, $slug, $element_attr );
								break;

							case 'link' :
								$form_setup[] = new Element_Url( $name, $slug, $element_attr );
								break;

						}

					}
				}
				buddyforms_display_field_group_table( $form_setup );
			}
		}
	}

}


// Hook is used to save custom fields that have been added to the WordPress profile page (if current user)
add_action( 'personal_options_update', 'update_extra_profile_fields' );

// Hook is used to save custom fields that have been added to the WordPress profile page (if not current user)
add_action( 'edit_user_profile_update', 'update_extra_profile_fields' );

function update_extra_profile_fields( $user_id ) {
	global $buddyforms;

	if ( current_user_can( 'edit_user', $user_id ) ) {
		if ( isset( $buddyforms ) ) {
			foreach ( $buddyforms as $form_slug => $buddyform ) {
				if ( $buddyform['form_type'] == 'registration' && isset( $buddyform['form_fields'] ) ) {
					foreach ( $buddyform['form_fields'] as $key => $user_meta ) {
						// Check if the form element type starts with user_ as prefix. user_ is reserved by WordPress and handled separably
						if ( substr( $user_meta['type'], 0, 5 ) != 'user_' ) {

							$slug = $user_meta['slug'];

							$value = isset( $_POST[ $slug ] ) ? $_POST[ $slug ] : '';

							update_user_meta( $user_id, $slug, buddyforms_sanitize( $user_meta['type'], $value ) );

						}

					}
				}
			}
		}
	}
}
