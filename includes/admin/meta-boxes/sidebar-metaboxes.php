

$form->addElement(new Element_HTML('<div id="postbox-container-1" class="postbox-container"><div class="subcontainer tab-pane fade in" id="subcon' . $buddyform['slug'] . '">'));

        $form->addElement(new Element_HTML('
        <div class="accordion_sidebar" id="accordion_' . $buddyform['slug'] . '">
            <div class="accordion-group postbox">
                <div class="accordion-heading"><p class="accordion-toggle">' . __('Save Form Settings', 'buddyforms') . '</p></div>
                <div id="accordion_' . $buddyform['slug'] . '_save" class="accordion-body">
                    <div class="accordion-inner">'));

                        $form->addElement(new Element_HTML('<a class="button" href="' . get_admin_url() . 'admin.php?page=buddyforms_options_page">' . __('Cancel', 'buddyforms') . '</a>'));

                        $form->addElement(new Element_Button('button', 'button', array('id' => $buddyform['slug'], 'class' => 'button dele_form', 'name' => 'dele_form', 'value' => __('Delete', 'buddyforms'))));

                        $form->addElement(new Element_HTML('<button type="submit" class="button-primary bf-save-form" style="float: right" name="action" value="Save">Save</button>'));
                        //$form->addElement(new Element_Button('submit', 'submit', array('action' => 'action', 'id' => 'submited', 'name' => 'form_save_action', 'value' => __('Save', 'buddyforms'), 'class' => 'button-primary', 'style' => 'float: right;')));

                        $form->addElement(new Element_HTML('
                    </div>
                </div>
            </div>'));

            $form->addElement(new Element_HTML('

            <div class="accordion-group postbox">
                <div class="accordion-heading"><p class="accordion-toggle">' . __('Mail Notification', 'buddyforms') . '</p></div>
                <div id="accordion_' . $buddyform['slug'] . '_save" class="accordion-body">
                    <div class="accordion-inner">

                        <a style="margin-bottom: 5px;" class="button" href="' . get_admin_url() . 'admin.php?page=bf_mail_notification&form_slug=' . $buddyform['slug'] . '"> ' . __('Mail Notification', 'buddyforms') . '</a>
                        <a style="margin-bottom: 5px;" class="button" href="' . get_admin_url() . 'admin.php?page=bf_manage_form_roles_and_capabilities&form_slug=' . $buddyform['slug'] . '"> ' . __('Roles and Capabilities', 'buddyforms') . '</a>
                        '));

                        $form->addElement(new Element_HTML('
                    </div>
                </div>
            </div>'));

            $form->addElement(new Element_HTML('
            <div class="accordion-group postbox">
                <div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_' . $buddyform['slug'] . '" href="#accordion_' . $buddyform['slug'] . '_fields"> ' . __('Form Elements', 'buddyforms') . '</p></div>
                <div id="accordion_' . $buddyform['slug'] . '_fields" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div>
                            <h5>' . __('Classic Fields', 'buddyforms') . '</h5>
                            <p><a href="Text/' . $buddyform['slug'] . '" class="action">' . __('Text', 'buddyforms') . '</a></p>
                            <p><a href="Textarea/' . $buddyform['slug'] . '" class="action">' . __('Textarea', 'buddyforms') . '</a></p>
                            <p><a href="Link/' . $buddyform['slug'] . '" class="action">' . __('Link', 'buddyforms') . '</a></p>
                            <p><a href="Mail/' . $buddyform['slug'] . '" class="action">' . __('Mail', 'buddyforms') . '</a></p>
                            <p><a href="Dropdown/' . $buddyform['slug'] . '" class="action">' . __('Dropdown', 'buddyforms') . '</a></p>
                            <p><a href="Radiobutton/' . $buddyform['slug'] . '" class="action">' . __('Radiobutton', 'buddyforms') . '</a></p>
                            <p><a href="Checkbox/' . $buddyform['slug'] . '" class="action">' . __('Checkbox', 'buddyforms') . '</a></p>
                            <h5>Post Fields</h5>
                            <p><a href="Content/' . $buddyform['slug'] . '/unique" class="action">' . __('Content', 'buddyforms') . '</a></p>
                            <p><a href="Taxonomy/' . $buddyform['slug'] . '" class="action">' . __('Taxonomy', 'buddyforms') . '</a></p>
                            <p><a href="Comments/' . $buddyform['slug'] . '/unique" class="action">' . __('Comments', 'buddyforms') . '</a></p>
                            <p><a href="Status/' . $buddyform['slug'] . '/unique" class="action">' . __('Post Status', 'buddyforms') . '</a></p>
                            <p><a href="Featured_Image/' . $buddyform['slug'] . '/unique" class="action">' . __('Featured Image', 'buddyforms') . '</a></p>

                            <h5>Extras</h5>
                            <p><a href="File/' . $buddyform['slug'] . '" class="action">' . __('File', 'buddyforms') . '</a></p>
                            <p><a href="Hidden/' . $buddyform['slug'] . '" class="action">' . __('Hidden', 'buddyforms') . '</a></p>
                            <p><a href="Number/' . $buddyform['slug'] . '" class="action">' . __('Number', 'buddyforms') . '</a></p>
                            <p><a href="HTML/' . $buddyform['slug'] . '" class="action">' . __('HTML', 'buddyforms') . '</a></p>
                            <p><a href="Date/' . $buddyform['slug'] . '" class="action">' . __('Date', 'buddyforms') . '</a></p>

                            '));

                            $form = apply_filters('buddyforms_add_form_element_to_sidebar', $form, $buddyform['slug']);

                            $form->addElement(new Element_HTML('
                        </div>
                    </div>
                </div>
                '));

                apply_filters('buddyforms_admin_settings_sidebar_metabox', $form, $buddyform['slug']);
