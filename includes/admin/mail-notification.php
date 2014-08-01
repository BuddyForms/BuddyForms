<?php
function bf_mail_notification_screen(){
    ?>

    <div class="wrap">

        <?php // include('admin-credits.php'); ?>

        <?php

        if (isset($_GET['form_slug'])) {

            global $buddyforms;

            $form_slug = $_GET['form_slug'];

            if (isset($_POST['buddyforms_options'])) {

                $buddyforms_new = array_merge_recursive($buddyforms, $_POST['buddyforms_options']);

                $update_option = update_option("buddyforms_options", $buddyforms_new);

                if ($update_option)
                    echo "<div id=\"settings_updated\" class=\"updated\"> <p><strong>" . __('Settings saved', 'buddyforms') . ".</strong></p></div>";
            }

            $form = new Form("buddyforms_notification_trigger");
            $form->configure(array(
                "prevent" => array("bootstrap", "jQuery"),
                "action" => $_SERVER['REQUEST_URI'],
                "view" => new View_Inline
            ));

            $form->addElement(new Element_HTML('<h2>' . $buddyforms['buddyforms'][$form_slug]['name'] . __(' Mail Notification Settings', 'buddyforms') . '<i><a target="_blank" href="' . get_admin_url() . 'admin.php?page=buddyforms_options_page#' . $form_slug . '"> Manage this Form</a></i></h2><br>'));

            $form->addElement(new Element_HTML('<div id="poststuff"><div class="bf-row">'));


                        $form->addElement(new Element_HTML('<div class="bf-col-content">

                         <br>
                        '));


                        $form->addElement(new Element_HTML('Every form can have different mail notification depance on the post status change.
                        You can create mail notification for each individual post status. Pleas see the select box and chuse the post status you want to ceata mail notification trigger for.<br><br>'));
                        $form->addElement(new Element_Select(__("Create new Notification Trigger for", 'buddyforms'), "buddyforms_notification_trigger", array('none' => 'select condition', 'first-submission' => 'first submission', 'status-change' => 'status change', 'publish' => 'publish', 'pending' => 'pending', 'draft' => 'draft', 'delete' => 'delete'), array('class' => 'buddyforms_notification_trigger', 'shortDesc' => '')));
                        $form->addElement(new Element_HTML('<a class="button-primary btn btn-primary" href="#" id="btnAdd"> Create Trigger</a></div>'));

                        $form->render();


                        if (isset($buddyforms['mail_notification'][$form_slug])) {
                            echo '<ul class="nav nav-tabs" id="tabs">';
                            $index = 1;
                            foreach ($buddyforms['mail_notification'][$form_slug] as $key => $value) {

                                if ($index == 1) {
                                    echo '<li class="active" ><a data-toggle="tab" href="#tab' . $index . '">' . $key . '</a></li>';
                                } else {
                                    echo '<li><a data-toggle="tab" href="#tab' . $index . '">' . $key . '</a></li>';
                                }


                                $index++;
                            }
                            echo '</ul> <div class="tab-content">';
                            $index = 1;
                            foreach ($buddyforms['mail_notification'][$form_slug] as $key => $value) {


                                ob_start();
                                buddyforms_new_notification_trigger_form($form_slug, $buddyforms['mail_notification'][$form_slug][$key]['mail_trigger']);
                                $trigger_form = ob_get_contents();
                                ob_clean();


                                if ($index == 1) {
                                    echo '<div class="tab-pane active" id="tab' . $index . '">' . $trigger_form . '</div>';
                                } else {
                                    echo '<div class="tab-pane" id="tab' . $index . '">' . $trigger_form . '</div>';
                                }


                                $index++;
                            }
                            echo '</div>';
                        } else {
                            echo '<h2>No Mail Notification Trigger Created so far</h2><div id="mailcontainer"></div>';
                        }

                    } else {

                        echo 'no form selected';
                    }


                    ?>



    </div>         </div>

<!--        <div id="postbox-container-1" class="postbox-container">
            <div class="accordion_sidebar" id="accordion_save">
                <div class="accordion-group postbox">
                    <div class="accordion-heading"><h5 class="accordion-toggle"><b>Manage this Form</b></h5></div>
                    <div id="accordion_save" class="accordion-body">
                        <div class="accordion-inner">'));
                            <a target="_blank" href="' . get_admin_url() . 'admin.php?page=buddyforms_options_page#' . $form_slug . '"> Manage this Form</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>-->


    </div>

<?php
}


function buddyforms_new_notification_trigger_form($form_slug, $trigger, $href = FALSE){
    global $buddyforms;

    session_start();
    $form = new Form("buddyforms_mail_notifications");

    if (!isset($href))
        $href = $_SERVER['REQUEST_URI'];


    $form->configure(array(
        "prevent" => array("bootstrap", "jQuery"),
        "action" => $href,
        "view" => new View_Inline
    ));

    $shortDesc = "
                    you can use [username] , [user_login] , [user_nicename] , [user_email] , [first_name] , [last_name] ,[published_post_link_html] , [published_post_link_plain] , [published_post_title] , [site_name] , [site_url],[site_url_html] place holder into email body
                    you can use [username] , [user_login] , [user_nicename] , [user_email] , [first_name] , [last_name] ,[published_post_link_html] , [published_post_link_plain] , [published_post_title] , [site_name] , [site_url],[site_url_html] place holder into email body";


    $form->addElement(new Element_Hidden("buddyforms_options[mail_notification][" . $form_slug . "][" . $trigger . "][mail_trigger]", $trigger));


    $form->addElement(new Element_Textbox(__("Subject:", 'buddyforms'), "buddyforms_options[mail_notification][" . $form_slug . "][" . $trigger . "][mail_subject]", array('value' => $buddyforms['mail_notification'][$form_slug][$trigger]['mail_subject'], 'required' => 1)));
    $form->addElement(new Element_Textbox(__("Email From Name:", 'buddyforms'), "buddyforms_options[mail_notification][" . $form_slug . "][" . $trigger . "][mail_from_name]", array('value' => $buddyforms['mail_notification'][$form_slug][$trigger]['mail_from_name'], 'required' => 1)));
    $form->addElement(new Element_Email(__("Email From:", 'buddyforms'), "buddyforms_options[mail_notification][" . $form_slug . "][" . $trigger . "][mail_from]", array('value' => $buddyforms['mail_notification'][$form_slug][$trigger]['mail_from'], 'required' => 1)));
    $form->addElement(new Element_Textarea(__("Email Body:", 'buddyforms'), "buddyforms_options[mail_notification][" . $form_slug . "][" . $trigger . "][mail_body]", array("class" => "span9", 'value' => $buddyforms['mail_notification'][$form_slug][$trigger]['mail_body'], 'required' => 1, shortDesc => $shortDesc)));

    $form->addElement(new Element_HTML('<br><br><br><br>'));
    $form->addElement(new Element_Button());



    $form->render();

}


function buddyforms_new_mail_notification()
{

    global $buddyforms;

    $form_slug = $_POST['form_slug'];
    $trigger = $_POST['trigger'];
    $href = $_POST['href'];

    if (isset($trigger, $buddyforms['mail_notification'][$form_slug][$trigger]))
        return false;

    buddyforms_new_notification_trigger_form($form_slug, $trigger, $href);
    die();
}

add_action('wp_ajax_buddyforms_new_mail_notification', 'buddyforms_new_mail_notification');
add_action('wp_ajax_nopriv_buddyforms_new_mail_notification', 'buddyforms_new_mail_notification');
?>