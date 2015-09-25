<?php
function bf_manage_form_roles_and_capabilities_screen(){


    global $post;

    $buddyform = get_post_meta($post->ID, '_buddyforms_options', true);

    if($post->post_name == ''){ ?>
        <div class="bf-roles-main-desc" >
            <div class="bf-col-content">
                <h2>You need to save the form before you can setup the Form Capabilities</h2>
                Roles and Capabilities are not stored as post meta. We use the form slug to identified capabilitys
            </div>
        </div>
    <?php
    } else {

        $form_slug = $post->post_name;

        $form_setup = array();

        $form_setup[] = new Element_HTML('
        <div class="bf-roles-main-desc" >
            <div class="bf-col-content"> ');

        $form_setup[] = new Element_HTML('
            <p>'.__('In WordPress we have user roles and capabilities to manage the user rights. You can decide which user is allowed to create, edit and delete posts by checking the needed capabilities for the different user roles.', 'buddyforms').'</p>
            <p>'.__('If you want to create new user roles and manage all available capabilities I recommend you to install the Members plugin.', 'buddyforms').'</p>
            <p>'.__('Here you can manage all BuddyForms capabilities for all available user roles of your wp install.', 'buddyforms').'</p><br>
            <p><b>'.__('Check/Uncheck capabilities to allow/disallow users to create, edit and/or delete posts of this form', 'buddyforms').'</b></p><p><a href="#" class="checkall">'.__('Uncheck all','buddyforms').'</a></p>
        ');

        $form_setup[] = new Element_HTML('
           </div>
        </div>');

        foreach (get_editable_roles() as $role_name => $role_info):

            $form_setup[] = new Element_HTML('
                <div class="bf-half-col bf-left" >
            <div class="bf-col-content" style="min-height:50px;"> ');


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

            $form_setup[] = new Element_Checkbox('<b>' . $role_name . '</b>', 'buddyforms_roles['.$form_slug.'][' . $role_name . ']', $default_roles, array('value' => $form_user_role, 'inline' => 1));
            $form_setup[] = new Element_HTML('<br><br>');
            $form_setup[] = new Element_HTML('</div>');
        endforeach;


        foreach($form_setup as $key => $field){
            echo '<div class="buddyforms_field_label">' . $field->getLabel() . '</div>';
            echo '<div class="buddyforms_field_description">' . $field->getShortDesc() . '</div>';
            echo '<div class="buddyforms_form_field">' . $field->render() . '</div>';
        }

        }

}