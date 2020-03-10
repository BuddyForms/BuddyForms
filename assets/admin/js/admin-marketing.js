jQuery(document).ready(function (jQuery) {
    //Popup for the themekraft bundle insisde the addons page
    var addonsContainer = jQuery('#fs_addons');
    var targetContainer = jQuery('#buddyforms_form_elements');
    if (((addonsContainer && addonsContainer.length > 0) || (targetContainer && targetContainer.length > 0)) && buddyformsMarketingHandler && buddyformsGlobal) {
        targetContainer.cornerpopup({
            variant: 10,
            slide: 1,
            slideTop: 1,
            escClose: 1,
            bgcolor: "#fff",
            bordercolor: "#efefef",
            textcolor: "#181818",
            btntextcolor: "#fff",
            content: buddyformsMarketingHandler.content,
        });
        if (addonsContainer && addonsContainer.length > 0) {
            jQuery('div#corner-popup').addClass('buddyforms-marketing-container buddyforms-marketing-bundle-container');
        }
    }
    //Popup for the viral share in the list of forms
    var formsList = jQuery('.type-buddyforms');
    if ((formsList && formsList.length >= 3) && buddyformsMarketingHandler && buddyformsGlobal && buddyformsMarketingHandler.content) {
        targetContainer.cornerpopup({
            variant: 10,
            slide: 1,
            slideTop: 1,
            escClose: 1,
            bgcolor: "#fff",
            bordercolor: "#efefef",
            textcolor: "#181818",
            btntextcolor: "#fff",
            afterPopup: function () {
                if (confirm('Close for ever?')) {
                    //hide for ever
                    jQuery.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: buddyformsGlobal.admin_url,
                        data: {
                            'action': 'buddyforms_marketing_form_list_coupon_for_free_close',
                            'nonce': buddyformsGlobal.ajaxnonce,
                        }
                    });
                }
            },
            content: buddyformsMarketingHandler.content,
        });
        jQuery('div#corner-popup').addClass('buddyforms-marketing-container buddyforms-marketing-bundle-container');
    }
});