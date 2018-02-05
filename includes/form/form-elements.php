<?php

/**
 * @param $form
 * @param $args
 */
function buddyforms_form_elements( $form, $args ) {
	global $buddyforms, $field_id;

	extract( $args );

	if ( ! isset( $customfields ) ) {
		return;
	}

	foreach ( $customfields as $field_id => $customfield ) :

		if ( isset( $customfield['slug'] ) ) {
			$slug = sanitize_title( $customfield['slug'] );
		}

		if ( empty( $slug ) ) {
			$slug = sanitize_title( $customfield['name'] );
		}

		if ( $slug != '' ) :

			$customfield_val = '';
			if ( isset( $_POST[ $slug ] ) ) {
				$customfield_val = $_POST[ $slug ];
			} elseif ( is_user_logged_in() ) {

				if ( $buddyforms[ $form_slug ]['form_type'] == 'registration' ) {

					if ( is_admin() ) {
						$bf_registration_user_id = get_post_meta( $post_id, '_bf_registration_user_id', true );
						$current_user            = get_userdata( $bf_registration_user_id );
					} else {
						$current_user = get_userdata( get_current_user_id() );
					}

					if ( ! $current_user ) {
						continue;
					}
					$customfield_val = get_user_meta( $current_user->ID, $slug, true );

				} else {
					$customfield_val = get_post_meta( $post_id, $slug, true );
				}

			}

			if ( empty( $customfield_val ) && isset( $customfield['default'] ) ) {
				$customfield_val = $customfield['default'];
			}

			$name = '';
			if ( isset( $customfield['name'] ) ) {
				$name = stripcslashes( $customfield['name'] );
			}

			$name = apply_filters( 'buddyforms_form_field_name', $name, $post_id );

			$description = '';
			if ( isset( $customfield['description'] ) ) {
				$description = stripcslashes( $customfield['description'] );
			}

			$description = apply_filters( 'buddyforms_form_field_description', $description, $post_id );

			$element_attr = array(
				'id'        => str_replace( "-", "", $slug ),
				'value'     => $customfield_val,
				'class'     => 'settings-input',
				'shortDesc' => $description,
				'field_id'  => $field_id
//				"view" => "Inline"
			);

			if ( isset( $customfield['required'] ) ) {
				$element_attr = array_merge( $element_attr, array( 'required' => true ) );
			}

			if ( isset( $customfield['custom_class'] ) ) {
				$element_attr['class'] = $element_attr['class'] . ' ' . $customfield['custom_class'];
			}

			if ( isset( $customfield['type'] ) ) {

				switch ( sanitize_title( $customfield['type'] ) ) {

					case 'subject':
						$form->addElement( new Element_Textbox( $name, $slug, $element_attr ) );
						break;

					case 'country':
						$form->addElement( new Element_Country( $name, $slug, $element_attr ) );
						break;

					case 'state':
						$form->addElement( new Element_State( $name, $slug, $element_attr ) );
						break;

					case 'message':
						$form->addElement( new Element_Textarea( $name, $slug, $element_attr ) );
						break;

					case 'user_login':
						if ( $buddyforms[ $form_slug ]['form_type'] == 'registration' && is_user_logged_in() ) {
							break;
						}
						$form->addElement( new Element_Textbox( $name, $slug, $element_attr ) );
						break;

					case 'user_email':
						if ( $buddyforms[ $form_slug ]['form_type'] == 'registration' && is_user_logged_in() ) {
							$element_attr['value'] = $current_user->user_email;
						}
						$form->addElement( new Element_Email( $name, $slug, $element_attr ) );
						break;

					case 'user_first':
						if ( $buddyforms[ $form_slug ]['form_type'] == 'registration' && is_user_logged_in() ) {
							$element_attr['value'] = $current_user->user_firstname;
						}
						$form->addElement( new Element_Textbox( $name, $slug, $element_attr ) );
						break;

					case 'user_last':
						if ( $buddyforms[ $form_slug ]['form_type'] == 'registration' && is_user_logged_in() ) {
							$element_attr['value'] = $current_user->user_lastname;
						}
						$form->addElement( new Element_Textbox( $name, $slug, $element_attr ) );
						break;

					case 'user_pass':
						if ( ! isset( $customfield['hide_if_logged_in'] ) && ! is_admin() ) {
							$form->addElement( new Element_Password( $name, $slug, $element_attr ) );
							$element_attr['id'] = $element_attr['id'] . '2';
							$form->addElement( new Element_Password( $name . ' Confirm', $slug . '_confirm', $element_attr ) );
						}
						break;

					case 'user_website':
						if ( $buddyforms[ $form_slug ]['form_type'] == 'registration' && is_user_logged_in() ) {
							$element_attr['value'] = $current_user->user_url;
						}
						$form->addElement( new Element_Url( $name, $slug, $element_attr ) );
						break;

					case 'user_bio':
						if ( $buddyforms[ $form_slug ]['form_type'] == 'registration' && is_user_logged_in() ) {
							$element_attr['value'] = $current_user->user_description;
						}
						$form->addElement( new Element_Textarea( $name, $slug, $element_attr ) );
						break;

					case 'number':
						$form->addElement( new Element_Number( $name, $slug, $element_attr ) );
						break;

					case 'html':
						$form->addElement( new Element_HTML( $customfield['html'] ) );
						break;

					case 'date':
						$form->addElement( new Element_Date( $name, $slug, $element_attr ) );
						break;

					case 'title':
						$post_title = '';
						if ( isset( $_POST['buddyforms_form_title'] ) ) {
							$post_title = stripslashes( $_POST['buddyforms_form_title'] );
						} elseif ( isset( $the_post->post_title ) ) {
							$post_title = $the_post->post_title;
						}
						if ( isset( $customfield['hidden'] ) ) {
							$form->addElement( new Element_Hidden( 'buddyforms_form_title', $post_title ) );
						} else {

							$element_attr = array(
								'id'        => 'buddyforms_form_title',
								'value'     => $post_title,
								'shortDesc' => $description
							);
							if ( isset( $customfield['required'] ) ) {
								$element_attr = array_merge( $element_attr, array( 'required' => true ) );
							}

							$form->addElement( new Element_Textbox( $name, "buddyforms_form_title", $element_attr ) );
						}
						break;

					case 'content':
						remove_filter( 'the_content', 'do_shortcode', 11 );
						add_filter( 'tiny_mce_before_init', 'buddyforms_tinymce_setup_function' );
						$buddyforms_form_content_val = false;
						if ( isset( $_POST['buddyforms_form_content'] ) ) {
							$buddyforms_form_content_val = stripslashes( $_POST['buddyforms_form_content'] );
						} else {
							if ( ! empty( $the_post->post_content ) ) {
								$buddyforms_form_content_val = $the_post->post_content;
							}
						}

						if ( isset( $customfield['hidden'] ) ) {
							$form->addElement( new Element_Hidden( 'buddyforms_form_content', $buddyforms_form_content_val ) );
						} else {

							ob_start();
							$settings = array(
								'wpautop'       => true,
								'media_buttons' => isset( $customfield['post_content_options'] ) ? in_array( 'media_buttons', $customfield['post_content_options'] ) ? false : true : true,
								'tinymce'       => isset( $customfield['post_content_options'] ) ? in_array( 'tinymce', $customfield['post_content_options'] ) ? false : true : true,
								'quicktags'     => isset( $customfield['post_content_options'] ) ? in_array( 'quicktags', $customfield['post_content_options'] ) ? false : true : true,
								'textarea_rows' => 18,
								'textarea_name' => 'buddyforms_form_content',
								'editor_class'  => 'textInMce',
							);

							if ( isset( $post_id ) ) {
								wp_editor( $buddyforms_form_content_val, 'buddyforms_form_content', $settings );
							} else {
								$content = false;
								$post    = 0; // todo: Not sure $post = 0 is needed.
								wp_editor( $content, 'buddyforms_form_content', $settings );
							}
							$wp_editor = ob_get_contents();
							ob_clean();

							$required = '';
							if ( isset( $customfield['required'] ) ) {
								$wp_editor = str_replace( '<textarea', '<textarea required="required"', $wp_editor );
								$required  = '<span class="required">* </span>';
							}

							$labels_layout = isset( $buddyforms[ $form_slug ]['layout']['labels_layout'] ) ? $buddyforms[ $form_slug ]['layout']['labels_layout'] : 'inline';

							$wp_editor_label = '';
							if ( $labels_layout == 'inline' ) {
								if ( isset( $customfield['required'] ) ) {
									$required = '* ';
								}
								$wp_editor = preg_replace( '/<textarea/', "<textarea placeholder=\"" . $required . $name . "\"", $wp_editor );
							} else {
								$wp_editor_label = '<label for="buddyforms_form_content">' . $required . $name . '</label>';
							}

							//						echo '<div id="buddyforms_form_content_val" style="display: none">' . $buddyforms_form_content_val . '</div>';


							if ( isset( $buddyforms[ $form_slug ]['layout']['desc_position'] ) && $buddyforms[ $form_slug ]['layout']['desc_position'] == 'above_field' ) {
								$wp_editor = '<div class="bf_field_group bf_form_content">' . $wp_editor_label . '<span class="help-inline">' . $description . '</span><div class="bf_inputs bf-input">' . $wp_editor . '</div></div>';
							} else {
								$wp_editor = '<div class="bf_field_group bf_form_content">' . $wp_editor_label . '<div class="bf_inputs bf-input">' . $wp_editor . '</div><span class="help-inline">' . $description . '</span></div>';
							}

							$wp_editor = apply_filters( 'buddyforms_wp_editor', $wp_editor, $post_id );

							$form->addElement( new Element_HTML( $wp_editor ) );
						}
						break;

					case 'email' :
						$form->addElement( new Element_Email( $name, $slug, $element_attr ) );
						break;

					case 'phone' :
						$form->addElement( new Element_Phone( $name, $slug, $element_attr ) );
						break;

					case 'radiobutton' :
						if ( isset( $customfield['options'] ) && is_array( $customfield['options'] ) ) {

							$options = Array();
							foreach ( $customfield['options'] as $key => $option ) {
								$options[ $option['value'] ] = $option['label'];
							}
							$element = new Element_Radio( $name, $slug, $options, $element_attr );
							$element->setAttribute( 'frontend_reset', ! empty( $customfield['frontend_reset'][0] ) );
							$form->addElement( $element );

						}
						break;

					case 'checkbox' :

						if ( isset( $customfield['options'] ) && is_array( $customfield['options'] ) ) {

							$options = Array();
							foreach ( $customfield['options'] as $key => $option ) {
								$options[ $option['value'] ] = $option['label'];
							}
							$element = new Element_Checkbox( $name, $slug, $options, $element_attr );
							$element->setAttribute( 'frontend_reset', ! empty( $customfield['frontend_reset'][0] ) );
							$form->addElement( $element );

						}
						break;

					case 'dropdown' :

						if ( isset( $customfield['options'] ) && is_array( $customfield['options'] ) ) {

							$options = Array();
							foreach ( $customfield['options'] as $key => $option ) {
								$options[ $option['value'] ] = $option['label'];
							}

							if ( ! empty( $customfield['frontend_reset'][0] ) ) {
								$element_attr['data-reset'] = 'true';
							}

							$element_attr['class'] = $element_attr['class'] . ' bf-select2';
							$element               = new Element_Select( $name, $slug, $options, $element_attr );

							if ( isset( $customfield['multiple'] ) && is_array( $customfield['multiple'] ) ) {
								$element->setAttribute( 'multiple', 'multiple' );
							}

							$form->addElement( $element );
						}
						break;

					case 'comments' :
						if ( isset( $the_post ) ) {
							$element_attr['value'] = $the_post->comment_status;
						}

						$form->addElement( new Element_Select( $name, 'comment_status', array(
							'open',
							'closed'
						), $element_attr ) );
						break;

					case 'status' :

						if ( isset( $customfield['post_status'] ) && is_array( $customfield['post_status'] ) ) {
							if ( in_array( 'pending', $customfield['post_status'] ) ) {
								$post_status['pending'] = __( 'Pending Review', 'buddyforms' );
							}

							if ( in_array( 'publish', $customfield['post_status'] ) ) {
								$post_status['publish'] = __( 'Published', 'buddyforms' );
							}

							if ( in_array( 'draft', $customfield['post_status'] ) ) {
								$post_status['draft'] = __( 'Draft', 'buddyforms' );
							}

							if ( in_array( 'future', $customfield['post_status'] ) && empty( $customfield_val ) || in_array( 'future', $customfield['post_status'] ) && get_post_status( $post_id ) == 'future' ) {
								$post_status['future'] = __( 'Scheduled', 'buddyforms' );
							}

							if ( in_array( 'private', $customfield['post_status'] ) ) {
								$post_status['private'] = __( 'Privately Published', 'buddyforms' );
							}

							if ( in_array( 'private', $customfield['post_status'] ) ) {
								$post_status['trash'] = __( 'Trash', 'buddyforms' );
							}

							$customfield_val = isset( $the_post->post_status ) ? $the_post->post_status : '';

							if ( isset( $_POST['status'] ) ) {
								$customfield_val = $_POST['status'];
							}

							$form->addElement( new Element_Select( $name, 'status', $post_status, $element_attr ) );

							if ( isset( $_POST[ $slug ] ) ) {
								$schedule_val = $_POST['schedule'];
							} else {
								$schedule_val = get_post_meta( $post_id, 'schedule', true );
							}

							$element_attr['class']       = $element_attr['class'] . ' bf_datetime bf_datetime_wrap';
							$element_attr['id']          = $element_attr['id'] . '_bf_datetime';
							$element_attr['placeholder'] = __( 'Schedule Time', 'buddyforms' );
							$form->addElement( new Element_Textbox( '', 'schedule', $element_attr ) );

						}
						break;
					case 'textarea' :
						add_filter( 'tiny_mce_before_init', 'buddyforms_tinymce_setup_function' );

						ob_start();
						$settings = array(
							'wpautop'       => false,
							'media_buttons' => isset( $customfield['post_textarea_options'] ) ? in_array( 'media_buttons', $customfield['post_textarea_options'] ) ? true : false : false,
							'tinymce'       => isset( $customfield['post_textarea_options'] ) ? in_array( 'tinymce', $customfield['post_textarea_options'] ) ? true : false : false,
							'quicktags'     => isset( $customfield['post_textarea_options'] ) ? in_array( 'quicktags', $customfield['post_textarea_options'] ) ? true : false : false,
							'textarea_rows' => 18,
							'textarea_name' => $slug,
							'editor_class'  => 'textInMce',
						);

						wp_editor( stripslashes( $customfield_val ), $slug, $settings );
						$wp_editor = ob_get_contents();
						ob_clean();

						$wp_editor = str_replace( '<textarea', '<textarea name="' . $slug . '"', $wp_editor );

						$required = '';
						if ( isset( $customfield['required'] ) ) {
							$wp_editor = str_replace( '<textarea', '<textarea required="required"', $wp_editor );
							$required  = '<span class="required">* </span>';
						}

						$labels_layout = isset( $buddyforms[ $form_slug ]['layout']['labels_layout'] ) ? $buddyforms[ $form_slug ]['layout']['labels_layout'] : 'inline';

						$wp_editor_label = '';
						if ( $labels_layout == 'inline' ) {
							if ( isset( $customfield['required'] ) ) {
								$required = '* ';
							}
							$wp_editor = preg_replace( '/<textarea/', "<textarea placeholder=\"" . $required . $name . "\"", $wp_editor );
						} else {
							$wp_editor_label = '<label for="buddyforms_form_"' . $name . '>' . $required . $name . '</label>';
						}

						if ( isset( $customfield['hidden'] ) ) {
							$form->addElement( new Element_Hidden( $name, $customfield_val ) );
						} else {
							if ( isset( $buddyforms[ $form_slug ]['layout']['desc_position'] ) && $buddyforms[ $form_slug ]['layout']['desc_position'] == 'above_field' ) {
								$wp_editor = '<div class="bf_field_group bf_form_content">' . $wp_editor_label . '<span class="help-inline">' . $description . '</span><div class="bf_inputs bf-input">' . $wp_editor . '</div></div>';
							} else {
								$wp_editor = '<div class="bf_field_group bf_form_content">' . $wp_editor_label . '<div class="bf_inputs bf-input">' . $wp_editor . '</div><span class="help-inline">' . $description . '</span></div>';
							}

							$form->addElement( new Element_HTML( $wp_editor ) );
						}
						break;
					case 'post_excerpt':
						add_filter( 'tiny_mce_before_init', 'buddyforms_tinymce_setup_function' );

						ob_start();
						$settings = array(
							'wpautop'       => false,
							'media_buttons' => isset( $customfield['post_excerpt_options'] ) ? in_array( 'media_buttons', $customfield['post_excerpt_options'] ) ? true : false : false,
							'tinymce'       => isset( $customfield['post_excerpt_options'] ) ? in_array( 'tinymce', $customfield['post_excerpt_options'] ) ? true : false : false,
							'quicktags'     => isset( $customfield['post_excerpt_options'] ) ? in_array( 'quicktags', $customfield['post_excerpt_options'] ) ? true : false : false,
							'textarea_rows' => 18,
							'textarea_name' => $slug,
							'editor_class'  => 'textInMce',
						);

						wp_editor( stripslashes( $customfield_val ), $slug, $settings );
						$wp_editor = ob_get_contents();
						ob_clean();


						$wp_editor = str_replace( '<textarea', '<textarea name="' . $slug . '"', $wp_editor );

						$required = '';
						if ( isset( $customfield['required'] ) ) {
							$wp_editor = str_replace( '<textarea', '<textarea required="required"', $wp_editor );
							$required  = '<span class="required">* </span>';
						}

						$labels_layout = isset( $buddyforms[ $form_slug ]['layout']['labels_layout'] ) ? $buddyforms[ $form_slug ]['layout']['labels_layout'] : 'inline';

						$wp_editor_label = '';
						if ( $labels_layout == 'inline' ) {
							if ( isset( $customfield['required'] ) ) {
								$required = '* ';
							}
							$wp_editor = preg_replace( '/<textarea/', "<textarea placeholder=\"" . $required . $name . "\"", $wp_editor );
						} else {
							$wp_editor_label = '<label for="buddyforms_form_"' . $name . '>' . $required . $name . '</label>';
						}

						if ( isset( $customfield['hidden'] ) ) {
							$form->addElement( new Element_Hidden( $name, $customfield_val ) );
						} else {
							if ( isset( $buddyforms[ $form_slug ]['layout']['desc_position'] ) && $buddyforms[ $form_slug ]['layout']['desc_position'] == 'above_field' ) {
								$wp_editor = '<div class="bf_field_group bf_form_content">' . $wp_editor_label . '<span class="help-inline">' . $description . '</span><div class="bf_inputs bf-input">' . $wp_editor . '</div></div>';
							} else {
								$wp_editor = '<div class="bf_field_group bf_form_content">' . $wp_editor_label . '<div class="bf_inputs bf-input">' . $wp_editor . '</div><span class="help-inline">' . $description . '</span></div>';
							}

							$form->addElement( new Element_HTML( $wp_editor ) );
						}
						break;
					case 'hidden' :
						$form->addElement( new Element_Hidden( $name, $customfield['value'] ) );
						break;

					case 'text' :
						$form->addElement( new Element_Textbox( $name, $slug, $element_attr ) );
						break;

					case 'range' :
						$form->addElement( new Element_Range( $name, $slug, $element_attr ) );
						break;

					case 'captcha' :
						if ( ! is_user_logged_in() ) {
							$form->addElement( new Element_Captcha( "Captcha", $attributes = null ) );
						}
						break;

					case 'link' :
						$form->addElement( new Element_Url( $name, $slug, $element_attr ) );
						break;

					case 'featured-image':
					case 'featured_image':

						$attachment_ids = get_post_thumbnail_id( $post_id );
						$attachments    = array_filter( explode( ',', $attachment_ids ) );

						$str = '<div id="bf_files_container_' . $slug . '" class="bf_files_container"><ul class="bf_files">';

						if ( $attachments ) {
							foreach ( $attachments as $attachment_id ) {

								$attachment_metadat = get_post( $attachment_id );

								$str .= '<li class="image bf-image" data-attachment_id="' . esc_attr( $attachment_id ) . '">

                                    <div class="bf_attachment_li">
                                    <div class="bf_attachment_img">
                                    ' . wp_get_attachment_image( $attachment_id, array( 64, 64 ), true ) . '
                                    </div><div class="bf_attachment_meta">
                                    <p><b>' . __( 'Name: ', 'buddyforms' ) . '</b>' . $attachment_metadat->post_name . '<p>
                                    <p><b>' . __( 'Type: ', 'buddyforms' ) . '</b>' . $attachment_metadat->post_mime_type . '<p>

                                    <p>
                                    <a href="#" class="delete tips" data-slug="' . $slug . '" data-tip="' . __( 'Delete image', 'buddyforms' ) . '">' . __( 'Delete', 'buddyforms' ) . '</a>
                                    <a href="' . wp_get_attachment_url( $attachment_id ) . '" target="_blank" class="view" data-tip="' . __( 'View', 'buddyforms' ) . '">' . __( 'View', 'buddyforms' ) . '</a>
                                    </p>
                                    </div></div>

                                </li>';
							}
						}

						$str .= '</ul>';


						$labels_layout = isset( $buddyforms[ $form_slug ]['layout']['labels_layout'] ) ? $buddyforms[ $form_slug ]['layout']['labels_layout'] : 'inline';

						$name_inline = isset( $customfield['button_label'] ) ? $customfield['button_label'] : __( 'Add Image', 'buddyforms' );
						if ( isset( $customfield['required'] ) && $labels_layout == 'inline' ) {
							$name_inline = '* ' . $name;
						}

						$str .= '<span class="bf_add_files hide-if-no-js">';
						$str .= '<a class="button btn btn-primary" href="#" data-slug="' . $slug . '" data-type="image/jpeg,image/gif,image/png,image/bmp,image/tiff,image/x-icon" data-multiple="false" data-choose="' . __( 'Add ', 'buddyforms' ) . '" data-update="' . __( 'Add ', 'buddyforms' ) . '" data-delete="' . __( 'Delete ', 'buddyforms' ) . '" data-text="' . __( 'Delete', 'buddyforms' ) . '">' . $name_inline . '</a>';
						$str .= '</span>';

						$str .= '</div><span class="help-inline">';
						$str .= $description;
						$str .= '</span>';

						$fimage_element = '<div class="bf_field_group">';
						if ( $labels_layout != 'inline' ) {
							$fimage_element .= '<label for="_' . $slug . '">';

							if ( isset( $customfield['required'] ) ) {
								$fimage_element .= '<span class="required">* </span>';
							}

							$fimage_element .= $name . '</label>';
						}

						$fimage_element .= '<div class="bf_inputs bf-input">
                            ' . $str . '
                            </div></div>
                        ';

						$form->addElement( new Element_HTML( $fimage_element ) );

						// always add slug
						$featured_image_params = array( 'id' => $slug );

						// add "required" if needed
						if ( isset( $customfield['required'] ) ) {
							$featured_image_params['required'] = 'required';
						}

						$form->addElement( new Element_Hidden( 'featured_image', $customfield_val, $featured_image_params ) );


						break;
					case 'file':

						$attachment_ids = $customfield_val;

						$str = '<div id="bf_files_container_' . $slug . '" class="bf_files_container"><ul class="bf_files">';

						$attachments = array_filter( explode( ',', $attachment_ids ) );

						if ( $attachments ) {
							foreach ( $attachments as $attachment_id ) {

								$attachment_metadat = get_post( $attachment_id );

								$str .= '<li class="image bf_image" data-attachment_id="' . esc_attr( $attachment_id ) . '">

                                    <div class="bf_attachment_li">
                                    <div class="bf_attachment_img">
                                    ' . wp_get_attachment_image( $attachment_id, array( 64, 64 ), true ) . '
                                    </div><div class="bf_attachment_meta">
                                    <p><b>' . __( 'Name: ', 'buddyforms' ) . '</b>' . $attachment_metadat->post_title . '<p>
                                    <p><b>' . __( 'Type: ', 'buddyforms' ) . '</b>' . $attachment_metadat->post_mime_type . '<p>

                                    <p>
                                    <a href="#" class="delete tips" data-slug="' . $slug . '" data-tip="' . __( 'Delete image', 'buddyforms' ) . '">' . __( 'Delete', 'buddyforms' ) . '</a>
                                    <a href="' . wp_get_attachment_url( $attachment_id ) . '" target="_blank" class="view" data-tip="' . __( 'View', 'buddyforms' ) . '">' . __( 'View', 'buddyforms' ) . '</a>
                                    </p>
                                    </div></div>

                                </li>';
							}
						}

						$str .= '</ul>';

						$str .= '<span class="bf_add_files hide-if-no-js">';


						$library_types = $allowed_types = '';
						if ( isset( $customfield['data_types'] ) ) {

							$data_types_array   = Array();
							$allowed_mime_types = get_allowed_mime_types();

							foreach ( $customfield['data_types'] as $key => $value ) {
								$data_types_array[ $value ] = $allowed_mime_types[ $value ];
							}

							$library_types = implode( ",", $data_types_array );
							$library_types = 'data-library_type="' . $library_types . '"';

							$allowed_types = implode( ",", $customfield['data_types'] );
							$allowed_types = 'data-allowed_type="' . $allowed_types . '"';

						}

						$data_multiple = 'data-multiple="false"';
						if ( isset( $customfield['validation_multiple'] ) ) {
							$data_multiple = 'data-multiple="true"';
						}

						$name_inline = __( 'Attache File', 'buddyforms' );
						if ( isset( $customfield['required'] ) && $labels_layout == 'inline' ) {
							$name_inline = '* ' . $name;
						}

						$str .= '<a href="#" class="button btn btn-primary" data-slug="' . $slug . '" ' . $data_multiple . ' ' . $allowed_types . ' ' . $library_types . 'data-choose="' . __( 'Add into', 'buddyforms' ) . '" data-update="' . __( 'Add ', 'buddyforms' ) . $name . '" data-delete="' . __( 'Delete ', 'buddyforms' ) . '" data-text="' . __( 'Delete', 'buddyforms' ) . '">' . $name_inline . '</a>';
						$str .= '</span>';

						$str .= '</div><span class="help-inline">';
						$str .= $description;
						$str .= '</span>';

						$file_element = '<div class="bf_field_group">';
						if ( $labels_layout != 'inline' ) {
							$file_element .= '<label for="_' . $slug . '">';

							if ( isset( $customfield['required'] ) ) {
								$file_element .= '<span class="required">* </span>';
							}

							$file_element .= $name . '</label>';
						}

						$file_element .= '<div class="bf_inputs bf-input">
                            ' . $str . '
                            </div></div>
                        ';
						$form->addElement( new Element_HTML( $file_element ) );

						$form->addElement( new Element_Hidden( $slug, $customfield_val, array( 'id' => $slug ) ) );


						break;
					case 'post_formats' :

						$post_formats = get_theme_support( 'post-formats' );
						$post_formats = isset( $post_formats[0] ) ? $post_formats[0] : array();
						array_unshift( $post_formats, 'Select a Post Format' );

						if ( empty( $element_attr['value'] ) ) {
							$element_attr['value'] = $customfield['post_formats_default'];
						}

						if ( isset( $customfield['hidden'] ) ) {
							$form->addElement( new Element_Hidden( $slug, $customfield['post_formats_default'] ) );
						} else {
							$form->addElement( new Element_Select( $name, $slug, $post_formats, $element_attr ) );
						}

						break;
					case 'taxonomy' :
					case 'category' :
					case 'tags' :

						if ( ! isset( $customfield['taxonomy'] ) ) {
							break;
						}
						if ( $customfield['taxonomy'] == 'none' ) {

							if ( $customfield['type'] == 'tags' ) {
								$customfield['taxonomy'] = 'post_tag';
							} elseif ( $customfield['type'] == 'category' ) {
								$customfield['taxonomy'] = 'category';
							} else {
								break;
							}

						}

						$args = array(
							'hide_empty'    => 0,
							'id'            => $field_id,
							'child_of'      => 0,
							'echo'          => false,
							'selected'      => false,
							'hierarchical'  => 1,
							'name'          => $slug . '[]',
							'class'         => 'postform bf-select2-' . $field_id,
							'depth'         => 0,
							'tab_index'     => 0,
							'hide_if_empty' => false,
							'orderby'       => 'SLUG',
							'taxonomy'      => isset( $customfield['taxonomy'] ) && $customfield['taxonomy'] != 'none' ? $customfield['taxonomy'] : '',
							'order'         => $customfield['taxonomy_order'],
							'exclude'       => isset( $customfield['taxonomy_exclude'] ) ? $customfield['taxonomy_exclude'] : '',
							'include'       => isset( $customfield['taxonomy_include'] ) ? $customfield['taxonomy_include'] : '',
						);

						$placeholder = isset( $customfield['taxonomy_placeholder'] ) ? $customfield['taxonomy_placeholder'] : 'Select an option';
						if ( ! isset( $customfield['multiple'] ) ) {
							$args = array_merge( $args, Array( 'show_option_none' => $placeholder ) );
						}

						if ( isset( $customfield['multiple'] ) ) {
							$args = array_merge( $args, Array( 'multiple' => $customfield['multiple'] ) );
						}

						$args     = apply_filters( 'buddyforms_wp_dropdown_categories_args', $args, $post_id );
						$dropdown = wp_dropdown_categories( $args );

						if ( isset( $customfield['multiple'] ) && is_array( $customfield['multiple'] ) ) {
							$dropdown = str_replace( 'id=', 'multiple="multiple" id=', $dropdown );
						}

						if ( isset( $customfield['required'] ) && is_array( $customfield['required'] ) ) {
							$dropdown = str_replace( 'id=', 'required id=', $dropdown );
						}

						$dropdown = str_replace( 'id=', 'data-placeholder="' . $placeholder . '" id=', $dropdown );
						$dropdown = str_replace( 'id=', 'style="width:100%;" id=', $dropdown );

						if ( isset( $customfield['taxonomy'] ) ) {
							$the_post_terms = get_the_terms( $post_id, $customfield['taxonomy'] );
						}

						if ( isset( $the_post_terms ) && is_array( $the_post_terms ) ) {
							foreach ( $the_post_terms as $key => $post_term ) {
								$dropdown = str_replace( ' value="' . $post_term->term_id . '"', ' value="' . $post_term->term_id . '" selected="selected"', $dropdown );
							}
						} else {
							if ( isset( $customfield['taxonomy_default'] ) ) {
								foreach ( $customfield['taxonomy_default'] as $key => $tax ) {
									$dropdown = str_replace( ' value="' . $customfield['taxonomy_default'][ $key ] . '"', ' value="' . $tax . '" selected="selected"', $dropdown );
								}
							}
						}

						$required = '';
						if ( isset( $customfield['required'] ) && is_array( $customfield['required'] ) ) {
							$required = '<span class="required">* </span>';
						}

						$tags                   = isset( $customfield['create_new_tax'] ) ? 'tags: true,' : '';
						$maximumSelectionLength = isset( $customfield['maximumSelectionLength'] ) ? 'maximumSelectionLength: ' . $customfield['maximumSelectionLength'] . ',' : '';

						$dropdown = '
						<script>
							jQuery(document).ready(function () {
							    jQuery(".bf-select2-' . $field_id . '").select2({
//							            minimumResultsForSearch: -1,
										' . $maximumSelectionLength . '
										    placeholder: function(){
										        jQuery(this).data("placeholder");
										    },
                                     allowClear: true,
							        ' . $tags . '
							        tokenSeparators: [\',\']
							    });
						    });
						</script>
						<div class="bf_field_group">
	                        <label for="editpost-element-' . $field_id . '">
	                            ' . $required . $name . '
	                        </label>
	                        <div class="bf_inputs bf-input">' . $dropdown . '</div>
		                	<span class="help-inline">' . $description . '</span>
		                </div>';

						if ( isset( $customfield['hidden'] ) ) {
							if ( isset( $customfield['taxonomy_default'] ) ) {
								foreach ( $customfield['taxonomy_default'] as $key => $tax ) {
									$form->addElement( new Element_Hidden( $slug . '[' . $key . ']', $tax ) );
								}
							}
						} else {
							$form->addElement( new Element_HTML( $dropdown ) );
						}

						break;

					default:

						$form_args = Array(
							'field_id'        => $field_id,
							'post_id'         => $post_id,
							'post_parent'     => $post_parent,
							'form_slug'       => $form_slug,
							'customfield'     => $customfield,
							'customfield_val' => $customfield_val
						);

						// hook to add your form element
						$form = apply_filters( 'buddyforms_create_edit_form_display_element', $form, $form_args );

						break;

				}
			}

		endif;
	endforeach;
}
