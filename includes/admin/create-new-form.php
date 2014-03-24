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

        $form->addElement(new Element_Textbox("Name:", "create_new_form_name",array('id' => 'create_new_form_name', 'placeholder' => 'e.g. Movies')));
        $form->addElement(new Element_Textbox("Singular Name:", "create_new_form_singular_name",array('id' => 'create_new_form_singular_name', 'placeholder' => 'e.g. Movie')));

        $form->addElement(new Element_HTML('<div class="clear"></div><br>'));
        $form->addElement(new Element_Button('button','button',array('class' => 'new_form', 'name' => 'new_form','value' => 'Create Form')));
        /*
                $form->addElement(new Element_HTML('<p><i class="icon-info-sign" style="margin-top:6px;"></i>&nbsp;<small><i>These settings can be overwritten by shortcodes and other plugins! <br>You can define the defaults here. </i></small></p><br />'));
                $form->addElement(new Element_HTML('<div class="innerblock form-type">'));

                if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['form_type']))
                    $form_type = $buddyforms_options['buddyforms'][$buddyform['slug']]['form_type'];

                if(empty($form_type))
                    $form_type = 'post_form';

                $form->addElement( new Element_Radio("<h4>Form Type</h4>", "buddyforms_options[buddyforms][".$buddyform['slug']."][form_type]", array('post_form','mail_form'),array('id' => $buddyform['slug'], 'class' => 'form_type', 'value' => $form_type)));
                $form->addElement(new Element_HTML('</div><div class="clear"></div>'));
                $form->addElement(new Element_HTML('<div class="mail_form_'.$buddyform['slug'].' form_type_settings" >'));
                $form->addElement(new Element_HTML('<p>NOT READY YET<br>I will leave the mail/notification development for later and focus on the logic of form and post control first. After the logic is deaply tested, we will put the same patition into mail and notification.</p>'));
                $email = '';
                if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['email']))
                    $email = $buddyforms_options['buddyforms'][$buddyform['slug']]['email'];

                $form->addElement(new Element_Textbox("Enter your email address:", "buddyforms_options[buddyforms][".$buddyform['slug']."][email]", array('value' => $email)));

                $email_subject = '';
                if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['email_subject']))
                    $email_subject = $buddyforms_options['buddyforms'][$buddyform['slug']]['email_subject'];

                $form->addElement(new Element_Textbox("What should the subject line be?", "buddyforms_options[buddyforms][".$buddyform['slug']."][email_subject]", array('value' => $email_subject)));
                $form->addElement(new Element_HTML('</div>'));
                $form->addElement(new Element_HTML('<div class="post_form_'.$buddyform['slug'].' form_type_settings" >'));
                $form->addElement(new Element_HTML('<div class="buddyforms_accordion_right">'));
                $form->addElement(new Element_HTML('<div class="innerblock featured-image">'));

                $required = 'false';
                if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['featured_image']['required']))
                    $required = $buddyforms_options['buddyforms'][$buddyform['slug']]['featured_image']['required'];

                $form->addElement( new Element_Checkbox("<b>Featured Image</b>","buddyforms_options[buddyforms][".$buddyform['slug']."][featured_image][required]",array('Required'),array('value' => $required)));
                $form->addElement(new Element_HTML('</div>'));
                $form->addElement(new Element_HTML('<div class="innerblock revision">'));

                $revision = 'false';
                if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['revision']))
                    $revision = $buddyforms_options['buddyforms'][$buddyform['slug']]['revision'];

                $form->addElement( new Element_Checkbox("<b>Revision</b><br><i>Enable frontend revison control.</i>","buddyforms_options[buddyforms][".$buddyform['slug']."][revision]",array('Revision'),array('value' => $revision)));

                $admin_bar = 'false';
                if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['admin_bar']))
                    $admin_bar = $buddyforms_options['buddyforms'][$buddyform['slug']]['admin_bar'];

                $form->addElement( new Element_Checkbox("<br><b>Admin Bar</b><br>","buddyforms_options[buddyforms][".$buddyform['slug']."][admin_bar]",array('Add to Admin Bar'),array('value' => $admin_bar)));

                $edit_link = 'false';
                if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['edit_link']))
                    $edit_link = $buddyforms_options['buddyforms'][$buddyform['slug']]['edit_link'];

                $form->addElement( new Element_Checkbox("<br><b>Overwrite Edit-this-entry link?</b><br><i>The link to the backend will be changed<br> to use the frontend editing.</i>","buddyforms_options[buddyforms][".$buddyform['slug']."][edit_link]",array('overwrite'),array('value' => $edit_link)));

                $form->addElement(new Element_HTML('</div>'));
                $form->addElement(new Element_HTML('</div>'));
                $form->addElement(new Element_HTML('<div class="buddyforms_accordion_left">'));

                $status = 'false';
                if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['status']))
                    $status = $buddyforms_options['buddyforms'][$buddyform['slug']]['status'];

                $form->addElement( new Element_Select("Status:", "buddyforms_options[buddyforms][".$buddyform['slug']."][status]", array('publish','pending','draft'),array('value' => $status)));

                $comment_status = 'false';
                if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['comment_status']))
                    $comment_status = $buddyforms_options['buddyforms'][$buddyform['slug']]['comment_status'];

                $form->addElement( new Element_Select("Comment Status:", "buddyforms_options[buddyforms][".$buddyform['slug']."][comment_status]", array('open','closed'),array('value' => $comment_status)));

                $post_type = 'false';
                if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['post_type']))
                    $post_type = $buddyforms_options['buddyforms'][$buddyform['slug']]['post_type'];

                $form->addElement( new Element_Select("Post Type:", "buddyforms_options[buddyforms][".$buddyform['slug']."][post_type]", $post_types,array('value' => $post_type)));

                $attached_page = 'false';
                if(isset($buddyforms_options['buddyforms'][$buddyform['slug']]['attached_page']))
                    $attached_page = $buddyforms_options['buddyforms'][$buddyform['slug']]['attached_page'];

                $args = array(
                    'id' => $key,
                    'echo' => FALSE,
                    'sort_column'  => 'post_title',
                    'show_option_none' => __( 'none', 'buddyforms' ),
                    'name' => "buddyforms_options[buddyforms][".$buddyform['slug']."][attached_page]",
                    'class' => 'postform',
                    'selected' => $attached_page
                );
                $form->addElement(new Element_HTML('<br><br><p><b>Attach page to this form</b></p><i>Select a page for the author, <br>call it e.g. "My Posts".</i><br><br>'));
                $form->addElement(new Element_HTML(wp_dropdown_pages($args)));

                $form->addElement(new Element_HTML('<br>Or you can <a href="'. admin_url( add_query_arg( array( 'post_type' => 'page' ), 'post-new.php' ) ).'" class="btn btn-small">'. __( 'Create A New Page', 'buddypress' ).'</a>'));


                $form->addElement(new Element_HTML('</div>'));

                $form->addElement(new Element_HTML('</div>'));
                $form->addElement(new Element_HTML('<div class="buddyforms_accordion_bottom">'));
                //	$form->addElement(new Element_HTML('<h3><p>Notification settings</p></h3>'));
                $form->addElement(new Element_HTML('</div>'));*/

        $form->render();
        ?>
	</div>
	
<?php 
}
