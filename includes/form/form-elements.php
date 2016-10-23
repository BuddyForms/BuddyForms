<?php

function buddyforms_form_elements( $form, $args ) {

	extract($args);

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

			if ( isset( $_POST[ $slug ] ) ) {
				$customfield_val = $_POST[ $slug ];
			} else {
				$customfield_val = get_post_meta( $post_id, $slug, true );
			}

			if ( empty( $customfield_val ) && isset( $customfield['default'] ) ) {
				$customfield_val = $customfield['default'];
			}

			$name = '';
			if ( isset( $customfield['name'] ) ) {
				$name = stripcslashes( $customfield['name'] );
			}
			$description = '';
			if ( isset( $customfield['description'] ) ) {
				$description = stripcslashes( $customfield['description'] );
			}

			$element_attr = array(
				'id'        => str_replace( "-", "", $slug ),
				'value'     => $customfield_val,
				'class'     => 'settings-input',
				'shortDesc' => $description,
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

					case 'message':
						$form->addElement( new Element_Textarea( $name, $slug, $element_attr ) );
						break;

					case 'user_login':
						 $form->addElement( new Element_Textbox( $name, $slug, $element_attr ) );
						break;

					case 'user_email':
						$form->addElement( new Element_Email( $name, $slug, $element_attr ) );
						break;

					case 'user_first':
						$form->addElement( new Element_Textbox( $name, $slug, $element_attr ) );
						break;

					case 'user_last':
						$form->addElement( new Element_Textbox( $name, $slug, $element_attr ) );
						break;

					case 'user_pass':
						$form->addElement( new Element_Password( $name, $slug, $element_attr ) );
						$element_attr['id'] = $element_attr['id'] . '2';
						$form->addElement( new Element_Password( $name . ' Confirm', $slug  . '_confirm', $element_attr ) );
						break;

					case 'user_website':
						$form->addElement( new Element_Url( $name, $slug, $element_attr ) );
						break;

					case 'user_bio':
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
						} elseif( isset( $the_post->post_title ) ) {
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
						add_filter( 'tiny_mce_before_init', 'buddyforms_tinymce_setup_function' );
						$buddyforms_form_content_val = false;
						if ( isset( $_POST['buddyforms_form_content'] ) ) {
							$buddyforms_form_content_val = stripslashes( $_POST['buddyforms_form_content'] );
						} else {
							if ( ! empty( $the_post->post_content ) ) {
								$buddyforms_form_content_val = $the_post->post_content;
							}
						}

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
							$post    = 0;
							wp_editor( $content, 'buddyforms_form_content', $settings );
						}
						$wp_editor = ob_get_contents();
						ob_clean();

						$required = '';
						if ( isset( $customfield['required'] ) ) {
							$wp_editor = str_replace( '<textarea', '<textarea required="required"', $wp_editor );
							$required  = '<span class="required">* </span>';
						}


						echo '<div id="buddyforms_form_content_val" style="display: none">' . $buddyforms_form_content_val . '</div>';

						if ( isset( $customfield['hidden'] ) ) {
							$form->addElement( new Element_Hidden( 'buddyforms_form_content', $buddyforms_form_content_val ) );
						} else {
							$wp_editor = '<div class="bf_field_group bf_form_content"><label for="buddyforms_form_content">' . $required . $name . '</label><div class="bf_inputs bf-input">' . $wp_editor . '</div><span class="help-inline">' . $description . '</span></div>';
							$form->addElement( new Element_HTML( $wp_editor ) );
						}
						break;

					case 'mail' :
						$form->addElement( new Element_Email( $name, $slug, $element_attr ) );
						break;

					case 'radiobutton' :
						if ( isset( $customfield['options'] ) && is_array( $customfield['options'] ) ) {

							$options = Array();
							foreach ( $customfield['options'] as $key => $option ) {
								$options[ $option['value'] ] = $option['label'];
							}
							$element = new Element_Radio( $name, $slug, $options, $element_attr );

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

							$form->addElement( $element );

						}
						break;

					case 'dropdown' :

						if ( isset( $customfield['options'] ) && is_array( $customfield['options'] ) ) {

							$options = Array();
							foreach ( $customfield['options'] as $key => $option ) {
								$options[ $option['value'] ] = $option['label'];
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
							$customfield['value'] = $the_post->comment_status;
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


							$customfield_val = isset($the_post->post_status) ? $the_post->post_status : '' ;

							if ( isset( $_POST['status'] ) ) {
								$customfield_val = $_POST['status'];
							}

							$form->addElement( new Element_Select( $name, 'status', $post_status, $element_attr ) );

							if ( isset( $_POST[ $slug ] ) ) {
								$schedule_val = $_POST['schedule'];
							} else {
								$schedule_val = get_post_meta( $post_id, 'schedule', true );
							}

							$element_attr['class'] = $element_attr['class'] . ' bf_datetime';

							$element_attr['id'] = $element_attr['id'] . '_bf_datetime';

							$form->addElement( new Element_HTML( '<div class="bf_datetime_wrap">' ) );
							$form->addElement( new Element_Textbox( 'Schedule Time', 'schedule', $element_attr ) );
							//$form->addElement(new Element_Date('Schedule Time', 'schedule', $element_attr));
							$form->addElement( new Element_HTML( '</div>' ) );
						}
						break;
					case 'textarea' :
						$form->addElement( new Element_Textarea( $name, $slug, $element_attr ) );
						break;

					case 'hidden' :
						$form->addElement( new Element_Hidden( $name, $customfield['value'] ) );
						break;

					case 'text' :
						$form->addElement( new Element_Textbox( $name, $slug, $element_attr ) );
						break;

					case 'captcha' :
						if(!is_user_logged_in()){
							$form->addElement( new Element_Captcha("Captcha", $attributes = null) );
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

								$str .= '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">

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

						$str .= '<span class="bf_add_files hide-if-no-js">';
						$str .= '<a class="button btn btn-primary" href="#" data-slug="' . $slug . '" data-type="image/jpeg,image/gif,image/png,image/bmp,image/tiff,image/x-icon" data-multiple="false" data-choose="' . __( 'Add ', 'buddyforms' ) . $name . '" data-update="' . __( 'Add ', 'buddyforms' ) . $name . '" data-delete="' . __( 'Delete ', 'buddyforms' ) . $name . '" data-text="' . __( 'Delete', 'buddyforms' ) . '">' . __( 'Add ', 'buddyforms' ) . $name . '</a>';
						$str .= '</span>';

						$str .= '</div><span class="help-inline">';
						$str .= $description;
						$str .= '</span>';

						$form->addElement( new Element_HTML( '
                        <div class="bf_field_group">
                            <label for="_' . $slug . '">' ) );

						if ( isset( $customfield['required'] ) ) {
							$form->addElement( new Element_HTML( '<span class="required">* </span>' ) );
						}

						$form->addElement( new Element_HTML( $name . '</label>' ) );
						$form->addElement( new Element_HTML( '<div class="bf_inputs bf-input">
                            ' . $str . '
                            </div>
                        ' ) );

						// always add slug
						$featured_image_params = array( 'id' => $slug );

						// add "required" if needed
						if ( isset( $customfield['required'] ) ) {
							$featured_image_params['required'] = 'required';
						}

						$form->addElement( new Element_Hidden( 'featured_image', $customfield_val, $featured_image_params ) );
						$form->addElement( new Element_HTML( '</div>' ) );

						break;
					case 'file':

						$attachment_ids = $customfield_val;

						$str = '<div id="bf_files_container_' . $slug . '" class="bf_files_container"><ul class="bf_files">';

						$attachments = array_filter( explode( ',', $attachment_ids ) );

						if ( $attachments ) {
							foreach ( $attachments as $attachment_id ) {

								$attachment_metadat = get_post( $attachment_id );

								$str .= '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">

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

						$str .= '<a href="#" data-slug="' . $slug . '" ' . $data_multiple . ' ' . $allowed_types . ' ' . $library_types . 'data-choose="' . __( 'Add into', 'buddyforms' ) . $name . '" data-update="' . __( 'Add ', 'buddyforms' ) . $name . '" data-delete="' . __( 'Delete ', 'buddyforms' ) . $name . '" data-text="' . __( 'Delete', 'buddyforms' ) . '">' . __( 'Attache File', 'buddyforms' ) . '</a>';
						$str .= '</span>';

						$str .= '</div><span class="help-inline">';
						$str .= $description;
						$str .= '</span>';

						$form->addElement( new Element_HTML( '
                        <div class="bf_field_group">
                            <label for="_' . $slug . '">' ) );

						if ( isset( $customfield['required'] ) ) {
							$form->addElement( new Element_HTML( '<span class="required">* </span>' ) );
						}

						$form->addElement( new Element_HTML( $name . '</label>' ) );
						$form->addElement( new Element_HTML( '<div class="bf_inputs bf-input">
                            ' . $str . '
                            </div>
                        ' ) );
						$form->addElement( new Element_Hidden( $slug, $customfield_val, array( 'id' => $slug ) ) );
						$form->addElement( new Element_HTML( '</div>' ) );

						break;
					case 'taxonomy' :

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
							'taxonomy'      => $customfield['taxonomy'],
							'order'         => $customfield['taxonomy_order'],
							'exclude'            => isset( $customfield['taxonomy_exclude'] ) ? $customfield['taxonomy_exclude'] : '',
							'include'            => isset( $customfield['taxonomy_include'] ) ? $customfield['taxonomy_include'] : '',
						);

						if ( isset( $customfield['show_option_none'] ) && ! isset( $customfield['multiple'] ) ) {
							$args = array_merge( $args, Array( 'show_option_none' => 'Nothing Selected' ) );
						}

						if ( isset( $customfield['multiple'] ) ) {
							$args = array_merge( $args, Array( 'multiple' => $customfield['multiple'] ) );
						}

						$dropdown = wp_dropdown_categories( $args );

						if ( isset( $customfield['multiple'] ) && is_array( $customfield['multiple'] ) ) {
							$dropdown = str_replace( 'id=', 'multiple="multiple" id=', $dropdown );
						}

						if ( isset( $customfield['required'] ) && is_array( $customfield['required'] ) ) {
							$dropdown = str_replace( 'id=', 'required id=', $dropdown );
						}

						$dropdown = str_replace( 'id=', 'style="width:100%;" id=', $dropdown );

						if( isset( $customfield['taxonomy'] ) ){
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

						$tags = isset($customfield['create_new_tax']) ? 'tags: true,' : '';

						$dropdown = '
						<script>
							jQuery(document).ready(function () {
							    jQuery(".bf-select2-' . $field_id . '").select2({
							        placeholder: "Select an option",
							        '.$tags.'
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
//							'post_parent'     => $post_parent,
							'form_slug'       => $form_slug,
							'customfield'     => $customfield,
							'customfield_val' => $customfield_val
						);

						// hook to add your form element
						apply_filters( 'buddyforms_create_edit_form_display_element', $form, $form_args );

						break;

				}
			}
		endif;
	endforeach;
}

function buddyforms_tinymce_setup_function( $initArray ) {
	$initArray['setup'] = 'function(editor) {
                editor.on("change keyup", function(e){
                    console.log(\'saving\');
                    //tinyMCE.triggerSave(); // updates all instances
                    editor.save(); // updates this instance\'s textarea
                    jQuery(editor.getElement()).trigger(\'change\'); // for garlic to detect change
                });
            }';

	return $initArray;
}
