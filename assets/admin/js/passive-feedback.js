function tkFeedbackPassive() {
    const loadSVG = 'data:image/svg+xml;base64,PCEtLSBCeSBTYW0gSGVyYmVydCAoQHNoZXJiKSwgZm9yIGV2ZXJ5b25lLiBNb3JlIEAgaHR0cDovL2dvby5nbC83QUp6YkwgLS0+Cjxzdmcgd2lkdGg9IjM4IiBoZWlnaHQ9IjM4IiB2aWV3Qm94PSIwIDAgMzggMzgiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgc3Ryb2tlPSIjMDAwIj4KICAgIDxnIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMSAxKSIgc3Ryb2tlLXdpZHRoPSIyIj4KICAgICAgICAgICAgPGNpcmNsZSBzdHJva2Utb3BhY2l0eT0iLjUiIGN4PSIxOCIgY3k9IjE4IiByPSIxOCIvPgogICAgICAgICAgICA8cGF0aCBkPSJNMzYgMThjMC05Ljk0LTguMDYtMTgtMTgtMTgiPgogICAgICAgICAgICAgICAgPGFuaW1hdGVUcmFuc2Zvcm0KICAgICAgICAgICAgICAgICAgICBhdHRyaWJ1dGVOYW1lPSJ0cmFuc2Zvcm0iCiAgICAgICAgICAgICAgICAgICAgdHlwZT0icm90YXRlIgogICAgICAgICAgICAgICAgICAgIGZyb209IjAgMTggMTgiCiAgICAgICAgICAgICAgICAgICAgdG89IjM2MCAxOCAxOCIKICAgICAgICAgICAgICAgICAgICBkdXI9IjFzIgogICAgICAgICAgICAgICAgICAgIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIi8+CiAgICAgICAgICAgIDwvcGF0aD4KICAgICAgICA8L2c+CiAgICA8L2c+Cjwvc3ZnPg==';

    async function takeScreenshot() {
        const screenshot = await html2canvas(document.body, {
            width: jQuery('html').width(),
            height: jQuery('html').height(),
            x: jQuery('html').scrollLeft(),
            y: jQuery('html').scrollTop()
        });

        return {
            canvas: screenshot,
            url: screenshot.toDataURL()
        };
    }

    function runEvent() {
        const tkFeedback = jQuery('.tk-feedback');
        const tkFeedbackHTML = jQuery('.tk-feedback')[0].outerHTML;

        jQuery(tkFeedback).remove();
        jQuery('#wpcontent').append(tkFeedbackHTML);
        jQuery('.tk-feedback').removeAttr('hidden');

        jQuery('#tk-feedback-screenshot img').attr('src', loadSVG);

        jQuery(document).on('click', '[data-tk-feedback-action]', function (e) {
            e.preventDefault();
            const feedbackAction = jQuery(this)[0].dataset.tkFeedbackAction.split(':');

            if (!feedbackAction) {
                return false;
            }

            switch (feedbackAction[0]) {
                case 'dialog': {
                    if (feedbackAction[1] == 0) {
                        jQuery('#tk-feedback-text').val('');
                        jQuery('html').css('overflow-y', 'auto');
                        jQuery('#tk-feedback-screenshot img').attr('src', loadSVG);
                    } else {
                        jQuery('html').css('overflow-y', 'hidden');
                    }

                    jQuery('#tk-feedback-alert').attr('data-state', '');
                    jQuery('.tk-feedback-backend-dialog').attr('data-open', feedbackAction[1]);
                    break;
                }
                case 'submit': {
                    const feedbackText = jQuery('#tk-feedback-text').val();
                    const feedbackScreen = jQuery('#tk-feedback-screenshot img').attr('src');

                    if (feedbackText != '') {
                        jQuery('#tk-feedback-alert').attr('data-state', 'load');
                        jQuery('[data-tk-feedback-action="submit:1"]').attr('disabled', 'disabled');

                        const sendForm = jQuery.ajax({
                            type: 'post',
                            url: buddyformsGlobal.admin_url,
                            data: {
                                'action'                      : 'buddyforms_passive_feedback_ajax',
                                'nonce'                       : buddyformsGlobal.ajaxnonce,
                                'passive_feedback_text'       : feedbackText,
                                'passive_feedback_screenshot' : feedbackScreen,
                                'passive_feedback_url'        : window.location.href
                            }
                        });

                        sendForm.always(function () {
                            jQuery('[data-tk-feedback-action="submit:1"]').removeAttr('disabled');
                        });

                        sendForm.fail(function () {
                            jQuery('#tk-feedback-alert').attr('data-state', 'server');
                        });

                        sendForm.done(function () {
                            jQuery('#tk-feedback-text').val('');
                            jQuery('#tk-feedback-text').focus();
                            jQuery('#tk-feedback-alert').attr('data-state', 'ok');

                            setTimeout(function () {
                                jQuery('.tk-feedback-backend-dialog').attr('data-open', 0);
                            }, 5000);
                        });
                    } else {
                        jQuery('#tk-feedback-alert').attr('data-state', 'user');
                        jQuery('#tk-feedback-text').focus();
                    }
                    break;
                }
            }
        });

        jQuery(document).on('click', '.tk-feedback-frontend-button', function (e) {
            e.preventDefault();
            const thisScreenshot = takeScreenshot();
            jQuery('#tk-feedback-text').focus();

            thisScreenshot.then(function (screenshot) {
                jQuery('#tk-feedback-screenshot img').attr('src', screenshot.url);
            });
        });
    }

    return {
        init: function () {
            runEvent();
        }
    };
}

jQuery(document).ready(function () {
    tkFeedbackPassive().init();
});
