<?php

function bf_import_export_screen(){ ?>

    <div class="wrap">
        <h2>Add New Form</h2>
        <?php

    if (isset($_POST["create_new_form_name"])) {
        echo 'jo';
    }

        // Form starts
        $form = new Form("buddyforms_form_new");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => $_SERVER['REQUEST_URI'],
            "view" => new View_Inline
        ));
        $form->addElement(new Element_HTML('
 <div class="accordion-group create-form-box">
    <div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_buddyforms_general_settings_create_form" href="#accordion_buddyforms_general_settings_create_form">Create New Form</p></div>
    <div id="accordion_buddyforms_general_settings_create_form" class="accordion-body collapse">
        <div class="accordion-inner">'));
            $form->addElement(new Element_Textbox("Name:", "create_new_form_name",array('id' => 'create_new_form_name', 'placeholder' => 'e.g. Movies')));
            $form->addElement(new Element_Textbox("Singular Name:", "create_new_form_singular_name",array('id' => 'create_new_form_singular_name', 'placeholder' => 'e.g. Movie')));

            $form->addElement(new Element_HTML('<div class="clear"></div><br>'));
            $form->addElement(new Element_Button('button','button',array('class' => 'new_form', 'name' => 'new_form','value' => 'Create Form')));

            $form->addElement( new Element_HTML('
        </div>
    </div>
</div>'));

        $form->render();
        ?>
	</div>
	
<?php 
}
