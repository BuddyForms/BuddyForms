<?php
function bf_manage_form_roles_and_capabilities_screen(){ ?>

    <div class="wrap">

        <?php include('admin-credits.php');

        if (isset($_GET['form_slug'])) {

            global $buddyforms;

            $form_slug = $_GET['form_slug'];

            if (isset($_POST['buddyforms_roles_submit'])) {

                foreach (get_editable_roles() as $role_name => $role_info):
                    $role = get_role( $role_name );
                    foreach ($role_info['capabilities'] as $capability => $_):

                        $capability_array = explode('_', $capability);

                        if($capability_array[0] == 'buddyforms'){
                            if($capability_array[1] == $form_slug){

                                $role->remove_cap( $capability );

                            }
                        }

                    endforeach;

                endforeach;

                if(isset($_POST['buddyforms_roles'])){
                    foreach($_POST['buddyforms_roles'][$form_slug] as $form_role => $capabilities){

                        $role = get_role( $form_role );
                        foreach($capabilities as $capability){
                            $role->add_cap( $capability );
                        }

                    }
                    echo "<div id=\"settings_updated\" class=\"updated\"> <p><strong>" . __('Capabilities updated', 'buddyforms') . ".</strong></p></div>";
                }

            }


            $form = new Form("buddyforms_notification_trigger");
            $form->configure(array(
                "prevent" => array("bootstrap", "jQuery"),
                "action" => $_SERVER['REQUEST_URI'],
                "view" => new View_Inline
            ));


            $form->addElement(new Element_HTML('
            <div id="poststuff">
                <div id="post-body" class="bf-mail-columns metabox-holder columns-2">


                    <div id="post-body-content">'));

                            $form->addElement(new Element_HTML('
                                <div class="bf-roles-main-desc" >
                                    <div class="bf-col-content"> '));
                                        $form->addElement(new Element_HTML('<h2>' . __(' Roles and Capabilities Settings for "', 'buddyforms') . $buddyforms['buddyforms'][$form_slug]['name'] . '"</h2><br>
                                        <p>In WordPress we have user roles and capabilities to manage the user rights. You can decide how to create, edit and delete posts by checking the needed capabilities for the different user roles.</p>
                                        <p>If you want to create new user roles and manage all available capabilities I recommend you to install the Members plugin.</p>
                                        <p>Here you can manage all BuddyForms capabilities for all available user roles of your wp install.</p><br>
                                        <p><b>Check/Uncheck all capabilities to allow/disallow users to create edit and delete posts of this form</b><a href="#" class="checkall"> Check all</a></p>
                            '));

                            $form->addElement(new Element_HTML('</div></div>'));

                            foreach (get_editable_roles() as $role_name => $role_info):

                                $form->addElement(new Element_HTML('
                                    <div class="bf-half-col bf-left" >
                                <div class="bf-col-content" style="min-height:50px;"> '));


                                $default_roles['buddyforms_' . $form_slug . '_create'] =  'buddyforms_' . $form_slug . '_create';
                                $default_roles['buddyforms_' . $form_slug . '_edit']   =  'buddyforms_' . $form_slug . '_edit';
                                $default_roles['buddyforms_' . $form_slug . '_delete'] =  'buddyforms_' . $form_slug . '_delete';

                                $form_user_role = array();

                                foreach ($role_info['capabilities'] as $capability => $_):

                                    $capability_array = explode('_', $capability);

                                    if($capability_array[0] == 'buddyforms'){
                                        if($capability_array[1] == $form_slug){

                                            $form_user_role[$capability] = $capability;
                                            $default_roles[$capability] = $capability;

                                        }
                                    }

                                endforeach;

                                $form->addElement(new Element_Checkbox('<b>' . $role_name . '</b>', 'buddyforms_roles['.$form_slug.'][' . $role_name . ']', $default_roles, array('value' => $form_user_role, 'inline' => 1)));
                                $form->addElement(new Element_HTML('<br><br>'));
                                $form->addElement(new Element_HTML('</div></div>'));
                            endforeach;







            $form->addElement(new Element_HTML('

                        </div>

                        <div id="postbox-container-1" class="postbox-container">
                            <div class="accordion_sidebar" id="accordion_save">
                                <div class="accordion-group postbox">
                                    <div class="accordion-heading"><h5 class="accordion-toggle"><b>Save Roles and Capabilities</b></h5></div>
                                    <b>
                                        <div id="accordion_save" class="accordion-body">
                                            <div class="accordion-inner">'));

            $form->addElement(new Element_Hidden('buddyforms_roles_submit', 'buddyforms_roles_submit'));
            $form->addElement(new Element_Button());
            $form->addElement(new Element_HTML('
                                            </div>
                                        </div>
                                    </b>
                                </div>
                            </div>
                            <div class="accordion_sidebar" id="accordion_save">
                                <div class="accordion-group postbox">
                                    <div class="accordion-heading"><h5 class="accordion-toggle"><b>Form Builder</b></h5></div>
                                    <b>
                                        <div id="accordion_save" class="accordion-body">
                                            <div class="accordion-inner">
                                               <a class="button" href="' . get_admin_url() . 'admin.php?page=buddyforms_options_page#subcon' . $form_slug . '">Jump into the Form Builder</a>
                                            </div>
                                        </div>
                                    </b>
                                </div>
                            </div>
                        </div>
            '));
            $form->addElement(new Element_HTML('</div></div>'));


            $form->render();



        } else {

            echo '<h2>No Mail Notification found</h2><div id="mailcontainer"></div>';

        }

?> </div> <?php
}
?>