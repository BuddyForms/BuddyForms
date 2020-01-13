jQuery(document).ready(function (jQuery) {
    var targetContainer = jQuery('#buddyforms_form_elements');
    if (targetContainer && targetContainer.length > 0 && buddyformsMarketingHandler && buddyformsGlobal) {
        targetContainer.cornerpopup({
            variant: 10,
            slide: 1,
            slideTop: 1,
            escClose: 1,
            colors: "#2ba7a7",
            bgcolor: "#fff",
            bordercolor: "#efefef",
            textcolor: "#181818",
            iconcolor: "#2ba7a7",
            btncolor: "#f4a141",
            btntextcolor: "#fff",
            content: buddyformsMarketingHandler.content
        });
    }
});