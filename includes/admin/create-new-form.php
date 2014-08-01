<?php

function bf_import_export_screen(){ ?>

    <div class="wrap">

        <h2><?php _e('Add New Form', 'buddyforms') ?></h2>

        <?php

        // Get all post types
        $args=array(
            'public' => true,
            'show_ui' => true
        );
        $output = 'names'; // names or objects, note: names is the default
        $operator = 'and'; // 'and' or 'or'
        $post_types = get_post_types($args,$output,$operator);
        $post_types_none['none'] = 'none';
        $post_types = array_merge($post_types_none,$post_types);

        // Form starts
        $form = new Form("buddyforms_form_new");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => $_SERVER['REQUEST_URI'],
            "view" => new View_Inline
        ));

        $form->addElement(new Element_HTML('<div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">'));

    // START CONTENT ,array("required" => 1)

        $form->addElement(new Element_HTML('<div id="titlediv"><div id="titlewrap">'));
            $form->addElement(new Element_Textbox("", "create_new_form_name",array('id' => 'title', 'placeholder' => __('Enter Title here, e.g. Movies', 'buddyforms'),'required' => 1)));
            $form->addElement(new Element_HTML('</div>'));
            $form->addElement(new Element_Textbox("", "create_new_form_singular_name",array('id' => 'create_new_form_singular_name', 'placeholder' => __('Enter Singluar Name here, e.g. Movie', 'buddyforms'), 'required' => 1)));
        $form->addElement(new Element_HTML('</div>'));


        $form->addElement(new Element_HTML('<div class="bf-row"><div class="bf-half-col bf-left"><div class="bf-col-content">
                                            <h4>'.__('Post Type', 'buddyforms').'</h4>
                                            <p class="bf-main-desc">'.__('Select which post type should be created on form submission.', 'buddyforms').'</p>'));
        $form->addElement( new Element_Select("", "create_new_form_post_type", $post_types,array("required" => 1)));

        $args = array(
            'echo' => FALSE,
            'sort_column'  => 'post_title',
            'show_option_none' => __( 'none', 'buddyforms' ),
            'name' => "create_new_form_attached_page",
            'class' => 'postform',
        );

        $form->addElement(new Element_HTML('</div></div><div class="bf-half-col bf-right"><div class="bf-col-content">'));
        $form->addElement(new Element_HTML('<h4>'.__('Attach a page to this form', 'buddyforms').'</h4>
                                            <p class="bf-main-desc">'.__('Select a page for the authors, call it e.g. "My Posts".', 'buddyforms').'</p>'));

        $form->addElement(new Element_HTML(wp_dropdown_pages($args)));

        $form->addElement(new Element_Textbox(__("<br>Or you can create a new Page.<br><i>Enter the title of the new page here: </i>", 'buddyforms'), "create_new_page",array('id' => 'create_new_page', 'placeholder' => __('e.g. My Movies', 'buddyforms'), 'style' => 'width: 400px;')));

        $form->addElement(new Element_HTML('</div></div></div></div>'));

        // END CONTENT

        $form->addElement(new Element_HTML('<div id="postbox-container-1" class="postbox-container">'));
    // START SIDEBAR

    $form->addElement(new Element_HTML('


    <div class="accordion_sidebar" id="accordion_save">
        <div class="accordion-group postbox">
            <div class="accordion-heading"><h5 class="accordion-toggle"><b>'.__('Create New Form', 'buddyforms').'</h5></div>
            <div id="accordion_save" class="accordion-body">
                <div class="accordion-inner">'));
                $form->addElement(new Element_HTML('<input type="button" class="button" onClick="history.go(0)" value="'.__('Cancel', 'buddyforms').'" />'));
                $form->addElement(new Element_Button('button','button',array('class' => 'new_form button-primary', 'name' => 'new_form','value' => __('Create Form', 'buddyforms'), 'style' => 'float: right; text-shadow: none;')));
                $form->addElement(new Element_HTML('</div>
                    </div>
                </div>'));
        $form->addElement(new Element_HTML('
        <div class="accordion-group postbox">
            <div class="accordion-heading"><h5 class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_publish" href="#accordion_publish">'.__('Moderation', 'buddyforms').'</h5></div>
            <div id="accordion_publish" class="accordion-body collapse">
                <div class="accordion-inner">'));
                 $form->addElement( new Element_Select(__("Post status on form submission:", 'buddyforms'), "create_new_form_status", array('publish','pending','draft')));
                 $form->addElement(new Element_HTML('
                </div>
            </div>
        </div>'));
        $form->addElement(new Element_HTML('
        <div class="accordion-group postbox">
            <div class="accordion-heading"><h5 class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_publish" href="#accordion_comments">'.__('Comments', 'buddyforms').'</h5></div>
            <div id="accordion_comments" class="accordion-body collapse">
                <div class="accordion-inner">'));
                $form->addElement( new Element_Select(__("Comment status on form submission:", 'buddyforms'), "create_new_form_comment_status", array('open','closed')));
                $form->addElement(new Element_HTML('
                </div>
            </div>
        </div>'));


    // END SIDEBAR
        $form->addElement(new Element_HTML('</div></div>
            </div>
        </div>'));


        $form->render();
        ?>
	</div>
	
<?php 
}
