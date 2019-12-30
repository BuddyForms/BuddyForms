jQuery(document).ready(function (jQuery) {
    var targetContainer = jQuery('#buddyforms_form_elements');
    if (targetContainer && targetContainer.length > 0 && buddyformsMarketingHandler && buddyformsGlobal) {
        targetContainer.cornerpopup({
            variant: 10,
            slide: 1,
            slideTop: 1,
            escClose: 1,
            colors: "#048914",
            bgcolor: "#fff",
            bordercolor: "#efefef",
            textcolor: "#181818",
            iconcolor: "#108923",
            btncolor: "#891e12",
            btntextcolor: "#fff",
            content: buddyformsMarketingHandler.content
        });
    }
});