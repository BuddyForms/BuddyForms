function userSatisfaction() {
    function ajaxEvent() {
      jQuery(document).on('click', '[data-satisfaction-form-action]', function (e) {
        e.preventDefault();
        let action = jQuery(this).attr('data-satisfaction-form-action');

        switch (action) {
          case 'ajax':
            let href = "/";
            let inputs = jQuery(this).attr('data-satisfaction-form-inputs').split(',');
            let data = {};
            if (href && inputs) {
              inputs.forEach(input => {
                let newInput = input.split(':');
                if (newInput.length >= 2) {
                  let jqNewInput = jQuery('[name="'+newInput[0]+'"]:'+newInput[1]);
                  if (jqNewInput.length >= 1) {
                    Object.assign(data, {[jqNewInput[0].name]: jqNewInput.val()});
                  }
                } else {
                  let jqNewInput = jQuery('[name="'+newInput[0]+'"]');
                  if (jqNewInput.length >= 1) {
                    Object.assign(data, {[jqNewInput[0].name]: jqNewInput.val()});
                  }
                }
              });

              if (Object.keys(data).length >= 1) {
                let ajaxForm = jQuery.get(href, data);
                ajaxForm.then((data, textStatus, jqXHR) => {
                  console.log(textStatus, jqXHR);
                  sectionNav();
                });
                ajaxForm.fail((data, textStatus, jqXHR) => {
                  console.log(textStatus, jqXHR);
                });
                break;
              } else {
                console.error('error');
              }
            }
          break;
        }
      });
    }

    jQuery(document).on('click', '[data-section-browser]', function (e) {
      e.preventDefault();
      sectionNav(jQuery(this).attr('data-section-browser'));
    });

    function sectionNav(action) {
      let thisWindow = jQuery('.bf-satisfaction');
      let thisSection = Number(thisWindow.attr('data-section'));

      switch (action) {
        case '-': {
          thisSection--;
          break;
        }
        case '1': {
          thisSection = 1;
          break;
        }
        default: {
          thisSection++;
          break;
        }
      }
      thisWindow.attr('data-section', thisSection);
      jQuery('.bf-satisfaction .bf-satisfaction-top-title').html(jQuery('section[data-section="'+thisSection+'"]').attr('data-section-title'));
    }

    return {
      nav: function (action) {
        sectionNav(action);
      },
      init: function () {
        ajaxEvent();
      }
    }
  }


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
                            'key': 'form_list_coupon_for_free',
                            'nonce': buddyformsGlobal.ajaxnonce,
                        }
                    });
                }
            },
            content: buddyformsMarketingHandler.content,
        });
    }
    //Popup for user satisfaction
    if (buddyformsMarketingHandler && buddyformsGlobal && buddyformsMarketingHandler.content) {
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
                            'action': 'buddyforms_marketing_hide_for_ever_close',
                            'popup_key': buddyformsMarketingHandler.key || '',
                            'nonce': buddyformsGlobal.ajaxnonce,
                        }
                    });
                }
            },
            content: buddyformsMarketingHandler.content,
        });
        jQuery('div#corner-popup').addClass('buddyforms-marketing-container buddyforms-marketing-bundle-container');
    }

    userSatisfaction().init();
});
