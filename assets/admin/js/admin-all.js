jQuery(document).ready(function (jQuery) {
    //Remove submission default page notification
    jQuery(document.body).on('click', '#buddyforms_submission_default_page button.notice-dismiss', function () {
        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            url: buddyformsGlobal.admin_url,
            data: {"action": "buddyforms_close_submission_default_page_notification", "nonce": buddyformsGlobal.ajaxnonce},
        })
    });
    jQuery(document.body).on('click', '#btn-compile-custom', function() {
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: buddyformsGlobal.admin_url,
            data: {
                'action': 'buddyforms_custom_form_template',
                'nonce': buddyformsGlobal.ajaxnonce,
            },
            success: function(data) {
                console.log(data);
            },
            error: function(request, status, error) {
                console.log(data);
            },
        });
    });

    jQuery(".bf-welcome-accordion_tab").click(function(){

        jQuery(".bf-welcome-accordion_tab").not(this).each(function(){
            
            jQuery(this).parent().removeClass("active");
            jQuery(this).removeClass("active");

        });

        jQuery(this).parent().toggleClass("active");
        jQuery(this).toggleClass("active");
        jQuery(this).animate({ color:'#509699' }, 500);
    });

    var activeTab = jQuery('.bf-welcome-accordion.active');
    var videoHeight = activeTab.width();
    jQuery('#bf-welcome-video-youtube').css('height', videoHeight*0.5);

    var goPro = jQuery('a[href="edit.php?post_type=buddyforms&page=buddyforms_bundle_screen"]');
    goPro.css('color', '#fca300' );
    goPro.parent().insertAfter('#menu-posts-buddyforms > ul > li:last-child');

    jQuery('#purchase').on('click', function (e) {

        var handler = FS.Checkout.configure({
            plugin_id:  '391',
            plan_id:    '583',
            public_key: 'pk_d462eaeb50bc258e3d97c2c146eb6',
        });
        
        handler.open({
            name     : 'ThemeKraft Bundle',
            licenses : jQuery('#licenses-1').val(),
            purchaseCompleted  : function (response) {
            },
            success  : function (response) {
            }
        });
        e.preventDefault();
    });

    jQuery('#purchase-2').on('click', function (e) {

        var handler = FS.Checkout.configure({
            plugin_id:  '7487',
            plan_id:    '12239',
            public_key: 'pk_68d9aeacd7352d37de451d91e3081',
        });
        
        handler.open({
            name     : 'ThemeKraft Bundle',
            licenses : jQuery('#licenses-2').val(),
            purchaseCompleted  : function (response) {
            },
            success  : function (response) {
            }
        });
        e.preventDefault();
    });

    jQuery('#purchase-3').on('click', function (e) {

        var handler = FS.Checkout.configure({
            plugin_id:  '2046',
            plan_id:    '4316',
            public_key: 'pk_ee958df753d34648b465568a836aa',
        });
        
        handler.open({
            name     : 'ThemeKraft Bundle',
            licenses : jQuery('#licenses-3').val(),
            purchaseCompleted  : function (response) {
            },
            success  : function (response) {
            }
        });
        e.preventDefault();
    });

    jQuery("select#licenses-1").change(function () {
        var selectedCountry = jQuery(this).children("option:selected").val();
        if( selectedCountry == '1'){
            jQuery('.fs-bundle-price-1').text('39.99');
            jQuery('#savings-price').text('59.99');
        }
        if( selectedCountry == '5'){
            jQuery('.fs-bundle-price-1').text('69.99');
            jQuery('#savings-price').text('199.95');
        }
        if( selectedCountry == 'unlimited'){
            jQuery('.fs-bundle-price-1').text('79.99');
            jQuery('#savings-price').text('219.99');
        }
    });

    jQuery("select#licenses-2").change(function () {
        var selectedCountry = jQuery(this).children("option:selected").val();
        if( selectedCountry == '1'){
            jQuery('.fs-bundle-price-2').text('89.99');
            jQuery('#savings-price-2').text('342.84');
        }
        if( selectedCountry == '5'){
            jQuery('.fs-bundle-price-2').text('99.99');
            jQuery('#savings-price-2').text('525.84');
        }
        if( selectedCountry == 'unlimited'){
            jQuery('.fs-bundle-price-2').text('119.99');
            jQuery('#savings-price-2').text('688.84');
        }
    });

    jQuery("select#licenses-3").change(function () {
        var selectedCountry = jQuery(this).children("option:selected").val();
        if( selectedCountry == '1'){
            jQuery('.fs-bundle-price-3').text('99.99');
            jQuery('#savings-price-3').text('602.75');
        }
        if( selectedCountry == '5'){
            jQuery('.fs-bundle-price-3').text('119.99');
            jQuery('#savings-price-3').text('965.75');
        }
        if( selectedCountry == 'unlimited'){
            jQuery('.fs-bundle-price-3').text('129.99');
            jQuery('#savings-price-3').text('1168.76');
        }
    });

    jQuery( ".bundle-list-see-more" ).click(function() {
        jQuery(".list-bundle-ul").animate({
            height: "1200"
        });
        jQuery(".separator, .bundle-list-see-more").hide();
    });
});


