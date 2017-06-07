<?php

//
// Create a array of all available form builder templates
//
function buddyforms_form_builder_register_templates() {

	// Get the templates form demo.buddyforms.com as json string
	$response = wp_remote_get( 'http://demo.buddyforms.com/wp-json/buddyforms/v1/all/' );

	// Decode the json
	$buddyforms = json_decode( $response['body'] );

	$sort = array();
	foreach ($buddyforms as $form_s => $form){
		$sort[$form->form_type][$form_s] = $form;
	}
	// Loop all forms from the demo and create the form templates
	foreach ( $sort as $sort_key => $sort_item ) {
		foreach ( $sort_item as $form_slug => $buddyform ) {
			$desc = '';
			foreach ( $buddyform->form_fields as $form_field ) {
				if ( empty( $desc ) ) {
					$desc .= $form_field->name;
				} else {
					$desc .= ', ' . $form_field->name;
				}

			}
			$buddyforms_templates[$sort_key][ $form_slug ]['title'] = $buddyform->name;
			$buddyforms_templates[$sort_key][ $form_slug ]['url']   = 'http://demo.buddyforms.com/remote/remote-create/' . $form_slug;
			$buddyforms_templates[$sort_key][ $form_slug ]['desc']  = $desc;
			$buddyforms_templates[$sort_key][ $form_slug ]['json']  = json_encode( $buddyform );
		}
	}

	$templates = Array();
	$templates['contact']       = $buddyforms_templates['contact'];
	$templates['registration']  = $buddyforms_templates['registration'];
	$templates['post']          = $buddyforms_templates['post'];

	return apply_filters( 'buddyforms_form_builder_templates', $templates );

}

function buddyforms_form_builder_template_get_dependencies($template){

	$buddyform = json_decode($template['json']);

	$dependencies = 'None';
	$deps = '';

	if( !($buddyform->post_type == 'post' || $buddyform->post_type == 'page' || $buddyform->post_type == 'bf_submissions') ){
		$deps .= 'BuddyForms Professional';
    }
	if ( buddyforms_core_fs()->is__premium_only() ) {
		if ( buddyforms_core_fs()->is_plan( 'professional' ) ) {
			$deps = '';
		}
	}

    if( $buddyform->post_type == 'product' && ! post_type_exists('product' ) ){

	    $deps .= empty( $deps ) ? '' : ', ';
	    $deps .= 'WooCommerce';

    }

	if ( isset( $buddyform->form_fields ) ) : foreach ( $buddyform->form_fields as $field_key => $field) {
		if ($field->slug == '_woocommerce') {

			if ( ! class_exists( 'bf_woo_elem' ) ) {
				$deps .= empty( $deps ) ? '' : ', ';
				$deps .= 'BuddyForms WooElements';
			}

			if ( ! class_exists( 'bf_woo_simple_auction' ) ) {
				$deps .= empty( $deps ) ? '' : ', ';
				$deps .= 'BuddyForms Simple Auction';
			}

			if( $field->product_type_default == 'auction'){
				if (!class_exists('WooCommerce_simple_auction')) {
					$deps .= empty( $deps ) ? '' : ', ';
					$deps .= 'WC Simple Auctions';
				}
            }
		}
	} endif;

    if( ! empty( $deps ) ){
	    $dependencies = $deps;
    }

	return $dependencies;

}

//
// Template HTML Loop the array of all available form builder templates
//
function buddyforms_form_builder_templates() {

	$buddyforms_templates = buddyforms_form_builder_register_templates();


	ob_start();

	?>
    <div class="buddyforms_template buddyforms_wizard_types">
        <h5>Choose a pre-configured form template or start a new one:</h5>

		<?php add_thickbox(); ?>

		<?php foreach ( $buddyforms_templates as $sort_key => $sort_item ) { ?>

            <h2><?php echo strtoupper($sort_key) ?> FORMS</h2>

            <?php foreach ( $sort_item as $key => $template ) {

                $dependencies = buddyforms_form_builder_template_get_dependencies( $template );

                $disabled = $dependencies != 'None' ? 'disabled' : '';

                ?>
                <div class="bf-3-tile bf-tile <?php if( $dependencies != 'None' ){ echo 'disabled '; } ?>">
                    <h4 class="bf-tile-title"><?php echo $template['title'] ?></h4>
                    <div class="xbf-col-50 bf-tile-desc-wrap">
                        <p class="bf-tile-desc"><?php echo wp_trim_words( $template['desc'], 15 ); ?></p>
                    </div>
                    <div class="bf-tile-preview-wrap">
                        <p><a href="#TB_inline?width=600&height=550&inlineId=template-<?php echo $key ?>"
                              data-src="<?php echo $template['url'] ?>" data-key="<?php echo $key ?>"
                              title="<?php echo $template['title'] ?>" class="thickbox button bf-preview"><span
                                        class="dashicons dashicons-visibility"></span> Preview</a></p>
                    </div>
										<?php if( $dependencies != 'None' ){ ?>
												<p class="bf-tile-dependencies">Dependencies: <?php echo $dependencies ?></p>
										<?php } else { ?>
		                    <button <?php echo $disabled ?> id="btn-compile-<?php echo $key ?>" data-type="<?php echo $key ?>"
		                            data-template="<?php echo $key ?>"
		                            class="bf_wizard_types bf_form_template btn btn-primary btn-50" onclick="">
		                        <!-- <span class="dashicons dashicons-plus"></span>  -->
		                        Use This Template
		                        <?php // echo $template['title'] ?>
		                    </button>
										<?php } ?>
                    <!-- <a href="#TB_inline?width=600&height=550&inlineId=template---><?php //echo $key ?><!--" title="-->
                    <?php //echo $template['title'] ?><!--" class="thickbox button  btn-primary btn-50"><span class="dashicons dashicons-visibility"></span> Preview</a>-->
                    <div id="template-<?php echo $key ?>" style="display:none;">
                        <div class="bf-tile-desc-wrap">
                            <p class="bf-tile-desc"><?php echo $template['desc'] ?></p>
                        </div>
                        <iframe id="iframe-<?php echo $key ?>" width="100%" height="800px" scrolling="yes" frameborder="0"
                                style="background: transparent; height: 639px;"></iframe>
                        <button <?php echo $disabled ?> id="btn-compile-<?php echo $key ?>" data-type="<?php echo $key ?>"
                                data-template="<?php echo $key ?>"
                                class="bf_wizard_types bf_form_template btn btn-primary btn-50" onclick="">
                            <!-- <span class="dashicons dashicons-plus"></span>  -->
                            Use This Template
                        </button>
                    </div>

                </div>
            <?php }
        }?>

    </div>

	<?php

	$tmp = ob_get_clean();

	return $tmp;
}

//
// json string of the form export top generate the Form from template
//
add_action( 'wp_ajax_buddyforms_form_template', 'buddyforms_form_template' );
function buddyforms_form_template() {
	global $post, $buddyform;


	$post->post_type = 'buddyforms';

	$buddyforms_templates = buddyforms_form_builder_register_templates();

	$forms = Array();
	foreach( $buddyforms_templates as $type => $form_temps){
        foreach($form_temps as $forms_slug => $form ){
	        $forms[$forms_slug] = $form;
        }
    }

    $buddyforms_templates = $forms;

	$buddyform = $buddyforms_templates[ $_POST['template'] ];

	$buddyform = json_decode( $buddyform['json'], true );

	ob_start();
	buddyforms_metabox_form_elements( $post, $buddyform );
	$formbuilder = ob_get_clean();

	// Add the form elements to the form builder
	$json['formbuilder'] = $formbuilder;

	ob_start();

	?>
    <div class="buddyforms_accordion_notification">
        <div class="hidden bf-hidden"><?php wp_editor( 'dummy', 'dummy' ); ?></div>

		<?php buddyforms_mail_notification_screen() ?>

        <div class="bf_show_if_f_type_post bf_hide_if_post_type_none">
			<?php buddyforms_post_status_mail_notification_screen() ?>
        </div>
    </div>
	<?php
	$mail_notification = ob_get_clean();

	$json['mail_notification'] = $mail_notification;

	// Unset the form fields
	unset( $buddyform['form_fields'] );
	unset( $buddyform['mail_submissions'] );

	// Add the form setup to the json
	$json['form_setup'] = $buddyform;

	echo json_encode( $json );
	die();
}
