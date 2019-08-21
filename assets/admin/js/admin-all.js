jQuery(document).ready(function (jQuery) {



    jQuery(document.body).on('click', '#submission_default_page :button', function () {

        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            url: php_vars.admin_url,
            data: {"action": "buddyforms_close_submission_default_page_notification"},
            success: function (data) {

                //console.log(data);
            }
        })
    });
})