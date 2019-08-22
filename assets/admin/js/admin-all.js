jQuery(document).ready(function (jQuery) {
    //Remove submission default page notification
    jQuery(document.body).on('click', '#buddyforms_submission_default_page button.notice-dismiss', function () {
        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            url: buddyformsGlobal.admin_url,
            data: {"action": "buddyforms_close_submission_default_page_notification", "nonce":buddyformsGlobal.ajaxnonce},
        })
    });
});