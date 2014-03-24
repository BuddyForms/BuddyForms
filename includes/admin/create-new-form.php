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
            $form->addElement(new Element_Textbox("", "create_new_form_name",array('id' => 'title', 'placeholder' => 'Enter Title here, e.g. Movies')));
            $form->addElement(new Element_HTML('</div>'));
            $form->addElement(new Element_Textbox("", "create_new_form_singular_name",array('id' => 'create_new_form_singular_name', 'placeholder' => 'Enter Singluar Name here, e.g. Movie')));
        $form->addElement(new Element_HTML('</div>'));


        $form->addElement(new Element_HTML('<div class="bf-row"><div class="bf-half-col bf-left"><div class="bf-col-content">
                                            <h4>Post Type</h4>
                                            <p class="bf-main-desc">Select which post type should be created on form submission.</p>'));
        $form->addElement( new Element_Select("", "create_new_form_post_type", $post_types));

        $args = array(
            'echo' => FALSE,
            'sort_column'  => 'post_title',
            'show_option_none' => __( 'none', 'buddyforms' ),
            'name' => "create_new_form_attached_page",
            'class' => 'postform',
        );

        $form->addElement(new Element_HTML('</div></div><div class="bf-half-col bf-right"><div class="bf-col-content">'));
        $form->addElement(new Element_HTML('<h4>Attach a page to this form</h4>
                                            <p class="bf-main-desc">Select a page for the authors, call it e.g. "My Posts".</p>'));

        $form->addElement(new Element_HTML(wp_dropdown_pages($args)));

        $form->addElement(new Element_HTML('<br><br><p><i>Or you can </i><a href="'. admin_url( add_query_arg( array( 'post_type' => 'page' ), 'post-new.php' ) ).'" class="button">'. __( 'Create A New Page', 'buddypress' ).'</a></p>'));

        $form->addElement(new Element_HTML('</div></div></div></div>'));

        // END CONTENT

        $form->addElement(new Element_HTML('<div id="postbox-container-1" class="postbox-container">'));
    // START SIDEBAR

    $form->addElement(new Element_HTML('


    <div class="accordion_sidebar" id="accordion_save">
        <div class="accordion-group postbox">
            <div class="accordion-heading"><h5 class="accordion-toggle"><b>Create New Form</h5></div>
            <div id="accordion_save" class="accordion-body">
                <div class="accordion-inner">'));
                $form->addElement(new Element_HTML('<input type="button" class="button" onClick="history.go(0)" value="Cancel" />'));
                $form->addElement(new Element_Button('button','button',array('class' => 'new_form button-primary', 'name' => 'new_form','value' => 'Create Form', 'style' => 'float: right;')));
                $form->addElement(new Element_HTML('</div>
                    </div>
                </div>'));
        $form->addElement(new Element_HTML('
        <div class="accordion-group postbox">
            <div class="accordion-heading"><h5 class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_publish" href="#accordion_publish">Moderation</h5></div>
            <div id="accordion_publish" class="accordion-body collapse">
                <div class="accordion-inner">'));
                 $form->addElement( new Element_Select("Post status on form submission:", "create_new_form_status", array('publish','pending','draft')));
                 $form->addElement(new Element_HTML('
                </div>
            </div>
        </div>'));
        $form->addElement(new Element_HTML('
        <div class="accordion-group postbox">
            <div class="accordion-heading"><h5 class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_publish" href="#accordion_comments">Comments</h5></div>
            <div id="accordion_comments" class="accordion-body collapse">
                <div class="accordion-inner">'));
                $form->addElement( new Element_Select("Comment status on form submission:", "create_new_form_comment_status", array('open','closed')));
                $form->addElement(new Element_HTML('
                </div>
            </div>
        </div>'));

        $form->addElement(new Element_HTML('
        <div class="accordion-group postbox">
            <div class="accordion-heading"><h5 class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_featured_images" href="#accordion_featured_images">Featured Images</h5></div>
            <div id="accordion_featured_images" class="accordion-body collapse">
                <div class="accordion-inner">'));
                $form->addElement( new Element_Checkbox("<label>Make Featured Images Required?</label>","create_new_form_featured_image_required",array('Mark as required')));
                $form->addElement(new Element_HTML('
                </div>
            </div>
        </div>'));

        $form->addElement(new Element_HTML('
        <div class="accordion-group postbox">
            <div class="accordion-heading"><h5 class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_revision" href="#accordion_revision">Revision</h5></div>
            <div id="accordion_revision" class="accordion-body collapse">
                <div class="accordion-inner">'));
        $form->addElement( new Element_Checkbox("<label>Enable frontend revison control?</label>","create_new_form_revision",array('Enable revision')));
        $form->addElement(new Element_HTML('
                </div>
            </div>
        </div>'));

        $form->addElement(new Element_HTML('
        <div class="accordion-group postbox">
            <div class="accordion-heading"><h5 class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_admin_bar" href="#accordion_admin_bar">Admin Bar</h5></div>
            <div id="accordion_admin_bar" class="accordion-body collapse">
                <div class="accordion-inner">'));
        $form->addElement( new Element_Checkbox("<label>Add menu items to admin bar?</label>","create_new_form_admin_bar",array('Add to admin bar')));
        $form->addElement(new Element_HTML('
                </div>
            </div>
        </div>'));

        $form->addElement(new Element_HTML('
        <div class="accordion-group postbox">
            <div class="accordion-heading"><h5 class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_edit_link" href="#accordion_edit_link">Edit Link</h5></div>
            <div id="accordion_edit_link" class="accordion-body collapse">
                <div class="accordion-inner">'));
        $form->addElement( new Element_Checkbox("<label><b>Overwrite Edit-this-entry Link?</b><br>The link to the backend will be changed to use the frontend editing.</label>","create_new_form_edit_link",array('Overwrite')));
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
