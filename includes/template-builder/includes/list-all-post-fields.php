<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }


/**
 * Define who the form data will be show in a single post view.
 *
 * @param $content
 *
 * @return string
 */
function buddyforms_list_all_post_fields( $content ) {
	global $buddyforms, $post;

	if ( ! is_single() ) {
		return $content;
	}

	$form_slug = get_post_meta( $post->ID, '_bf_form_slug', true );

	if ( ! $form_slug ) {
		return $content;
	}
	if ( isset( $buddyforms[ $form_slug ]['hook_fields_show_edit_link'] ) ) {

		$edit_link = '<p><a class="post-edit-link" href="' . get_edit_post_link( $post->ID ) . '">' . __( 'Edit', 'buddyforms' ) . ' <span class="screen-reader-text">prueba nueva</span></a></p>';

		$content = $edit_link . $content;
	}

	if ( empty( $buddyforms ) || empty( $buddyforms[ $form_slug ] ) ) {
		return $content;
	}

	$add_table_content        = ( ! empty( $buddyforms[ $form_slug ]['hook_fields_list_on_single'] ) ) ? $buddyforms[ $form_slug ]['hook_fields_list_on_single'] : '';
	$post_template_id         = ( ! empty( $buddyforms[ $form_slug ]['hook_fields_template_page'] ) ) ? (int) $buddyforms[ $form_slug ]['hook_fields_template_page'] : 'none';
	$is_post_template_enabled = ( ! empty( $post_template_id ) && $post_template_id !== 'none' );

	$hide_title = ( ! empty( $buddyforms[ $form_slug ]['hook_fields_hide_title'] ) ) ? $buddyforms[ $form_slug ]['hook_fields_hide_title'] : '';
	if ( ! isset( $buddyforms[ $form_slug ]['hook_fields_list_on_single'] ) ) {
		return $content;
	}

	wp_enqueue_style( 'bf-hook-fields', plugins_url( 'assets/bf-hook-fields.css', __FILE__ ) );

	remove_filter( 'the_content', 'buddyforms_list_all_post_fields', 999 );

	$exist_title_in_post = buddyforms_exist_field_type_in_form( $form_slug, 'title' );
	if ( ! empty( $hide_title ) && $exist_title_in_post ) {
		echo '<style>.entry-header{display: none;}</style>';
	}
	if ( $is_post_template_enabled ) {
		if ( class_exists( 'Elementor\Plugin' ) ) {
			$template_content = Elementor\Plugin::instance()->frontend->get_builder_content( $post_template_id, true );
		}

		if ( empty( $template_content ) ) {
			$template_content = get_the_content( null, false, $post_template_id );
			$template_content = apply_filters( 'the_content', $template_content );
			$template_content = str_replace( ']]>', ']]&gt;', $template_content );
		}
		$template_content = buddyforms_get_field_value_from_string( $template_content, $post->ID, $form_slug, true );
		if ( ! empty( $template_content ) ) {
			$content = $template_content;
		}
	} elseif ( $add_table_content ) {

		$striped_c   = 0;
		$new_content = '<table rules="all" class="bf-hook-field-container" cellpadding="10">';
		if ( isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {
			foreach ( $buddyforms[ $form_slug ]['form_fields'] as $key => $field ) {

				if ( in_array( $field['slug'], buddyforms_get_exclude_field_slugs() ) || $field['slug'] == 'buddyforms_form_content' || $field['slug'] == 'buddyforms_form_title' || $field['slug'] == 'featured_image' || $field['type'] == 'hidden' ) {
					continue;
				}

				$field = buddyforms_get_field_with_meta( $form_slug, $post->ID, $field['slug'] );

				$field_value = ! empty( $field['value'] ) ? $field['value'] : apply_filters( 'buddyforms_field_shortcode_empty_value', '', $field, $form_slug, $post->ID, $field['slug'] );

				$striped = ( $striped_c ++ % 2 == 1 ) ? "style='background: #eee;'" : '';

				if ( isset( $field['slug'] ) ) {
					if ( $field['type'] === 'upload' || $field['type'] === 'file' ) {
						$upload_field_val = get_post_meta( $post->ID, $field['slug'], true );
						$media_items      = explode( ',', $upload_field_val );
						$result           = '';
						foreach ( $media_items as $attachment_item ) {
							if ( ! empty( $attachment_item ) ) {
								$attachment_full_url      = wp_get_attachment_url( $attachment_item );
								$file_mime_type           = get_post_mime_type( $attachment_item );
								$file_type                = explode( '/', $file_mime_type )[0];
								$file_type_extension      = explode( '/', $file_mime_type )[1];
								$default_thumbnail        = plugin_dir_url( __FILE__ ) . '/assets/images/multimedia.png';
								$attachment_thumbnail_url = wp_get_attachment_thumb_url( $attachment_item ) === false ? $default_thumbnail : wp_get_attachment_thumb_url( $attachment_item );

								$result .= "<a class='image-placeholder' href='" . $attachment_full_url . "' target='_blank'>";

								switch ( $file_type ) {

									case 'video':
										$result .= "<video width='150' height='150' controls> <source src='" . $attachment_full_url . "' >  </video>";
										break;
									case 'image':
										$result .= "<img src='" . $attachment_thumbnail_url . "' />";
										break;
									case 'audio':
										$result .= "<audio width='150' height='150' controls> <source src='" . $attachment_full_url . "' ></audio>";
										break;

									default:
									case 'application':
										if ( $file_type_extension == 'pdf' ) {
											$pdf_thumbnail = plugin_dir_url( __FILE__ ) . '/assets/images/pdf.png';
											$result       .= "<img src='" . $pdf_thumbnail . "' />";
										} elseif ( $file_type_extension == 'zip' || $file_type_extension == 'x-gzip' || $file_type_extension == 'rar' || $file_type_extension == 'x-7z-compressed' ) {
											$compressed_thumbnail = plugin_dir_url( __FILE__ ) . '/assets/images/compressed.png';
											$result              .= "<img src='" . $compressed_thumbnail . "' />";

										} else {
											$result .= "<img src='" . $default_thumbnail . "' />";
										}

										break;
								}

								$result .= '</a>';

							}
						}
						$new_content .= '<tr ' . $striped . '><td><strong>' . $field['name'] . '</strong> </td><td><div>' . trim( $result ) . '</div></td></tr>';
					} else {
						$new_content .= '<tr ' . $striped . '><td><strong>' . $field['name'] . '</strong> </td><td>' . $field_value . '</td></tr>';
					}
				}
			}
		}

		// Table end
		$new_content .= '</table>';
		$content     .= $new_content;
	}

	add_filter( 'the_content', 'buddyforms_list_all_post_fields', 999, 1 );

	// Let us return the form elements table
	return $content;
}

add_filter( 'the_content', 'buddyforms_list_all_post_fields', 999, 1 );

/**
 * Define how to display each field
 */
function buddyforms_form_display_element_frontend() {
	global $buddyforms, $post, $bf_hooked;

	if ( is_admin() ) {
		return;
	}

	if ( ! isset( $post->ID ) ) {
		return;
	}

	if ( $bf_hooked ) {
		return;
	}

	$form_slug = get_post_meta( $post->ID, '_bf_form_slug', true );

	if ( ! isset( $form_slug ) ) {
		return;
	}

	if ( ! isset( $buddyforms[ $form_slug ] ) ) {
		return;
	}

	if ( ! isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {
		return;
	}

	wp_enqueue_style( 'bf-hook-fields', plugins_url( 'assets/bf-hook-fields.css', __FILE__ ) );

	$before_the_title   = false;
	$after_the_title    = false;
	$before_the_content = false;
	$after_the_content  = false;
	$allowed            = array(
		'a'      => array(
			'href'  => array(),
			'title' => array(),
			'class' => array(),
			'id'    => array(),
			'data'  => array(),
			'rel'   => array(),
		),
		'br'     => array(),
		'em'     => array(),
		'ul'     => array(
			'class' => array(),
			'id'    => array(),
		),
		'ol'     => array(
			'class' => array(),
		),
		'li'     => array(
			'class' => array(),
			'id'    => array(),
		),
		'strong' => array(),
		'div'    => array(
			'class' => array(),
			'id'    => array(),
			'data'  => array(),
			'style' => array(),
		),
		'span'   => array(
			'class' => array(),
			'id'    => array(),
			'style' => array(),
		),
		'img'    => array(
			'alt'    => array(),
			'class'  => array(),
			'id'     => array(),
			'height' => array(),
			'src'    => array(),
			'width'  => array(),
		),
		'select' => array(
			'id'    => array(),
			'class' => array(),
			'name'  => array(),
		),
		'option' => array(
			'value'    => array(),
			'class'    => array(),
			'id'       => array(),
			'selected' => array(),
		),
	);
	foreach ( $buddyforms[ $form_slug ]['form_fields'] as $field_id => $customfield ) {

		if( isset( $customfield['slug'] ) ) {

			if ( ! empty( $customfield['slug'] ) && ( ( isset( $customfield['hook'] ) && ! empty( $customfield['hook'] ) ) || is_single() ) ) {

				$field             = buddyforms_get_field_with_meta( $form_slug, $post->ID, $customfield['slug'] );
				$customfield_value = ! empty( $field['value'] ) ? $field['value'] : apply_filters( 'buddyforms_field_shortcode_empty_value', '', $field, $form_slug, $post->ID, $field['slug'] );

				if ( ! empty( $customfield_value ) ) {
					$post_meta_tmp = '<div class="post_meta bf-hook-field ' . $customfield['slug'] . '">';

					if ( isset( $customfield['display_name'] ) ) {
						$post_meta_tmp .= '<p style="width:100%;">' . $customfield['name'] . ':</p>';
					}

					if ( $field['type'] === 'upload' || $field['type'] === 'file' ) {
						$upload_field_val = get_post_meta( $post->ID, $field['slug'], true );
						$media_items      = explode( ',', $upload_field_val );
						$result           = array();
						$thumbnail_size   = 'thumbnail';

						if ( isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['thumbnail_size'] ) ) {
							$thumbnail_size = $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['thumbnail_size'];
						}

						foreach ( $media_items as $attachment_item ) {
							if ( ! empty( $attachment_item ) ) {
								$attachment_full_url      = wp_get_attachment_url( $attachment_item );
								$attachment_thumbnail_url = wp_get_attachment_image_src( $attachment_item, $thumbnail_size );
								$file_mime_type           = get_post_mime_type( $attachment_item );
								$file_type                = explode( '/', $file_mime_type )[0];
								$file_type_extension      = explode( '/', $file_mime_type )[1];

								$registered_sizes = wp_get_registered_image_subsizes();
								$media_size_width = isset( $registered_sizes[ $thumbnail_size ]['width'] ) ? $registered_sizes[ $thumbnail_size ]['width'] : 150;

								$default_img  = plugin_dir_url( __FILE__ ) . '/assets/images/multimedia.png';
								$media_output = "<a href='" . $attachment_full_url . "' target='_blank'>";

								switch ( $file_type ) {

									case 'video':
										$media_output .= '<video width=' . $media_size_width . " controls> <source src='" . $attachment_full_url . "' ></video>";
										break;

									case 'image':
										$media_output .= "<img src='" . $attachment_thumbnail_url[0] . "' />";
										break;
									case 'audio':
										$media_style   = "style='width: " . $media_size_width . "px;'";
										$media_output .= "<span class='image-placeholder' " . $media_style . '>';
										$media_output .= "<audio  controls style='width:80%'> <source src='" . $attachment_full_url . "' ></audio>";
										$media_output .= '<p>' . basename( get_attached_file( $attachment_item ) ) . '</p>';
										$media_output .= '</span>';

										break;

									default:
									case 'application':
										$media_style   = "style='width: " . $media_size_width . "px;'";
										$media_output .= "<span class='image-placeholder' " . $media_style . '>';

										if ( $file_type_extension === 'pdf' ) {
											$pdf_thumbnail = plugin_dir_url( __FILE__ ) . '/assets/images/pdf.png';
											$media_output .= "<img src='" . $pdf_thumbnail . "' />";
										} elseif ( $file_type_extension === 'zip' || $file_type_extension == 'x-gzip' || $file_type_extension == 'rar' || $file_type_extension == 'x-7z-compressed' ) {
											$compressed_thumbnail = plugin_dir_url( __FILE__ ) . '/assets/images/compressed.png';
											$media_output        .= "<img src='" . $compressed_thumbnail . "' />";
										} else {
											$media_output .= "<img src='" . $default_img . "' />";
										}

										$media_output .= '<p>' . basename( get_attached_file( $attachment_item ) ) . '</p>';
										$media_output .= '</span>';

										break;
								}

								$media_output .= '</a>';
								$result[]      = $media_output;
							}
						}

						$meta_tmp = implode( '', $result );

					} else {
						if ( is_array( $customfield_value ) ) {
							$meta_tmp = '<p>' . implode( ',', $customfield_value ) . '</p>';
						} else {
							$meta_tmp = '<p>' . $customfield_value . '</p>';
						}
					}

					if ( $meta_tmp ) {
						$post_meta_tmp .= apply_filters( 'buddyforms_form_element_display_frontend', $meta_tmp, $customfield );
					}

					$post_meta_tmp .= '</div>';

					$post_meta_tmp = apply_filters( 'buddyforms_form_element_display_frontend_before_hook', $post_meta_tmp );

					if ( isset( $customfield['hook'] ) && ! empty( $customfield['hook'] ) ) {
						add_action(
							$customfield['hook'],
							function () use ( $post_meta_tmp ) {
								echo wp_kses( addcslashes( $post_meta_tmp, '"' ), $allowed );
							}
						);
					}

					if ( is_single() && isset( $customfield['display'] ) ) {
						switch ( $customfield['display'] ) {
							case 'before_the_title':
								$before_the_title .= $post_meta_tmp;
								break;
							case 'after_the_title':
								$after_the_title .= $post_meta_tmp;
								break;
							case 'before_the_content':
								$before_the_content .= $post_meta_tmp;
								break;
							case 'after_the_content':
								$after_the_content .= $post_meta_tmp;
								break;
						}
					}
				}
			}
		}

	}

	if ( is_single() ) {

		if ( $before_the_title ) {
			add_filter(
				'the_title',
				function ( $content, $id ) use ( $before_the_title ) {
					if ( is_single() && $id == get_the_ID() ) {
						return $before_the_title . $content;
					}

					return $content;
				},
				9999,
				2
			);
		}

		if ( $after_the_title ) {
			add_filter(
				'the_title',
				function ( $content, $id ) use ( $after_the_title ) {
					if ( is_single() && $id == get_the_ID() ) {
						return $content . $after_the_title;
					}

					return $content;
				},
				9999,
				2
			);
		}

		if ( $before_the_content ) {
			add_filter(
				'the_content',
				function ( $content ) use ( $before_the_content ) {
					return $before_the_content . $content;
				},
				9999
			);
		}

		if ( $after_the_content ) {

			add_filter(
				'the_content',
				function ( $content ) use ( $after_the_content ) {
					return $content . $after_the_content;
				},
				9999
			);
		}
	}
	$bf_hooked = true;

}

add_action( 'the_post', 'buddyforms_form_display_element_frontend' );

add_shortcode( 'bfsinglefield', 'bf_hooks_single_field' );
function bf_hooks_single_field( $atts ) {
	global $buddyforms, $post;
	if ( ! isset( $atts['form-slug'] ) || ! isset( $atts['field-slug'] ) ) {
		return;
	}
	if( ! is_object( $post ) ){
		return;
	}
	$form_to_check = get_post_meta( $post->ID, '_bf_form_slug', true );
	$form_slug     = $atts['form-slug'];
	$form          = $buddyforms[ $form_slug ];
	if ( empty( $form ) ) {
		return;
	}
	if ( ! isset( $form['form_fields'] ) ) {
		return;
	}
	if ( ( $atts['form-slug'] != $form_to_check ) && isset( $post ) ) {
		return;
	}
	$selected_field = '';
	foreach ( $form['form_fields'] as $field ) {
		if ( $field['slug'] == $atts['field-slug'] ) {
			$selected_field = $field['slug'];
			break;
		}
	}

	$current_post_type = get_post_type();
	if ( empty( $current_post_type ) ) {
		return '<p>Field <b>"' . $atts['field-slug'] . '"</b> value.</p>';
	}
	$field_data = buddyforms_get_field_with_meta( $form_slug, $post->ID, $selected_field, $full_string = false, $html = true );
	if ( ! isset( $field_data['value'] ) ) {
		return '<b>Sorry, this field was not found in the selected form.</b>';
	}
	$field_value = '<p>' . $field_data['value'] . '</p>';
	return $field_value;
}

