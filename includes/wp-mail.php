<?php








//add_action('publish_post',    'buddyforms_send_email_notification');

add_action('buddyforms_after_save_post',    'buddyforms_send_email_notification', 10, 1);
function buddyforms_send_email_notification($post_ID){
    global $buddyforms;

    echo $post_ID;

    $bf_form_slug = get_post_meta($post_ID, '_bf_form_slug', true );

    if(empty($bf_form_slug))
        return;

    $pub_post = get_post($post_ID);
    $author_id=$pub_post->post_author;
    $post_title=$pub_post->post_title;
    $postperma=get_permalink( $post_ID );
    $user_info = get_userdata($author_id);

    $usernameauth=$user_info->user_login;
    $user_nicename=$user_info->user_nicename;
    $user_email=$user_info->user_email;
    $first_name=$user_info->user_firstname;
    $last_name=$user_info->user_lastname;

    $blog_title = get_bloginfo('name');
    $siteurl=get_bloginfo('wpurl');
    $siteurlhtml="<a href='$siteurl' target='_blank' >$siteurl</a>";



    $publish_post_notification_settings = $buddyforms['buddyforms'][$bf_form_slug];

    $subject=$publish_post_notification_settings['mail_subject'];
    $from_name=$publish_post_notification_settings['mail_from_name'];
    $from_email=$publish_post_notification_settings['mail_from'];
    $emailBody=$publish_post_notification_settings['mail_body'];
    $emailBody=stripslashes($emailBody);
    $emailBody=str_replace('[username]',$usernameauth,$emailBody);
    $emailBody=str_replace('[user_login]',$usernameauth,$emailBody);
    $emailBody=str_replace('[user_nicename]',$user_nicename,$emailBody);
    $emailBody=str_replace('[user_email]',$user_email,$emailBody);
    $emailBody=str_replace('[first_name]',$first_name,$emailBody);
    $emailBody=str_replace('[last_name]',$last_name,$emailBody);

    $emailBody=str_replace('[published_post_link_plain]',$postperma,$emailBody);

    $postlinkhtml="<a href='$postperma' target='_blank'>$postperma</a>";

    $emailBody=str_replace('[published_post_link_html]',$postlinkhtml,$emailBody);

    $emailBody=str_replace('[published_post_title]',$post_title,$emailBody);
    $emailBody=str_replace('[site_name]',$blog_title,$emailBody);
    $emailBody=str_replace('[site_url]',$siteurl,$emailBody);
    $emailBody=str_replace('[site_url_html]',$siteurlhtml,$emailBody);

    $emailBody=stripslashes(htmlspecialchars_decode($emailBody));

    $mailheaders .= "MIME-Version: 1.0\n";
    $mailheaders .= "X-Priority: 1\n";
    $mailheaders .= "Content-Type: text/html; charset=\"UTF-8\"\n";
    $mailheaders .= "Content-Transfer-Encoding: 7bit\n\n";
    $mailheaders .= "From: $from_name <$from_email>" . "\r\n";
    $message='<html><head></head><body>'.$emailBody.'</body></html>';

    echo $user_email.'<br>';
    echo $message.'<br>';
    echo $subject.'<br>';
    echo $mailheaders.'<br>';

    wp_mail($user_email, $subject, $message, $mailheaders);


}
?>