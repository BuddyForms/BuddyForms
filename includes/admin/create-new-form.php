<?php

function bf_import_export_screen(){ ?>

    <div class="wrap">
        <h2>Add New Form</h2>

        <?php

        global $bp, $buddyforms;

        // Get all needed values
        BuddyForms::set_globals();
        $buddyforms_options = $buddyforms; //get_option('buddyforms_options');

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

    // START CONTENT

        $form->addElement(new Element_HTML('<div id="titlediv"><div id="titlewrap">'));
            $form->addElement(new Element_Textbox("Name:", "create_new_form_name",array('id' => 'title', 'placeholder' => 'e.g. Movies')));
            $form->addElement(new Element_Textbox("Singular Name:", "create_new_form_singular_name",array('id' => 'create_new_form_singular_name', 'placeholder' => 'e.g. Movie')));
        $form->addElement(new Element_HTML('</div></div>'));

        $form->addElement( new Element_Select("Post Type:", "create_new_form_post_type", $post_types));

        $args = array(
            'echo' => FALSE,
            'sort_column'  => 'post_title',
            'show_option_none' => __( 'none', 'buddyforms' ),
            'name' => "create_new_form_attached_page",
            'class' => 'postform',
        );
        $form->addElement(new Element_HTML('<br><br><p><b>Attach page to this form</b></p><i>Select a page for the author, <br>call it e.g. "My Posts".</i><br><br>'));
        $form->addElement(new Element_HTML(wp_dropdown_pages($args)));

        $form->addElement(new Element_HTML('<br>Or you can <a href="'. admin_url( add_query_arg( array( 'post_type' => 'page' ), 'post-new.php' ) ).'" class="btn btn-small">'. __( 'Create A New Page', 'buddypress' ).'</a>'));

        // END CONTENT

        $form->addElement(new Element_HTML('</div>
                <div id="postbox-container-1" class="postbox-container">'));
    // START SIDEBAR

    $form->addElement(new Element_HTML('
    <div class="accordion_sidebar" id="accordion_save">
        <div class="accordion-group postbox">
            <div class="accordion-heading"><p class="accordion-toggle">Create New Form</p></div>
            <div id="accordion_save" class="accordion-body">
                <div class="accordion-inner">'));
                $form->addElement(new Element_Button('button','button',array('class' => 'new_form button-primary', 'name' => 'new_form','value' => 'Create Form')));
                $form->addElement(new Element_HTML('<input type="button" class="button" onClick="history.go(0)" value="Cancel">
                </div>
            </div>
        </div>'));
        $form->addElement(new Element_HTML('
        <div class="accordion-group postbox">
            <div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_publish" href="#accordion_publish">Publish</p></div>
            <div id="accordion_publish" class="accordion-body collapse">
                <div class="accordion-inner">'));
                 $form->addElement( new Element_Select("Status:", "create_new_form_status", array('publish','pending','draft')));
                 $form->addElement(new Element_HTML('
                </div>
            </div>
        </div>'));
        $form->addElement(new Element_HTML('
        <div class="accordion-group postbox">
            <div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_publish" href="#accordion_comments">Comments</p></div>
            <div id="accordion_comments" class="accordion-body collapse">
                <div class="accordion-inner">'));
                $form->addElement( new Element_Select("Comment Status:", "create_new_form_comment_status", array('open','closed')));
                $form->addElement(new Element_HTML('
                </div>
            </div>
        </div>'));

        $form->addElement( new Element_Checkbox("<b>Featured Image</b>","create_new_form_featured_image_required",array('Required')));
        $form->addElement( new Element_Checkbox("<b>Revision</b><br><i>Enable frontend revison control.</i>","create_new_form_revision",array('Revision')));
        $form->addElement( new Element_Checkbox("<br><b>Admin Bar</b><br>","create_new_form_admin_bar",array('Add to Admin Bar')));
        $form->addElement( new Element_Checkbox("<br><b>Overwrite Edit-this-entry link?</b><br><i>The link to the backend will be changed<br> to use the frontend editing.</i>","create_new_form_edit_link",array('overwrite')));

    // END SIDEBAR
        $form->addElement(new Element_HTML('</div></div>
            </div>
        </div>'));


        $form->render();
        ?>
	</div>
	
<?php 
}
