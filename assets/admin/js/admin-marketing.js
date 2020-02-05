jQuery(document).ready(function (jQuery) {
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
});