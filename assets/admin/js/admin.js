jQuery(document).ready(function (jQuery) {


    //
    // No more bootstrap
    //
    //jQuery(".tabs, .tabs-left").tabs();
    //jQuery("#sortable_buddyforms_elements li").accordion({ header: ".accordion-heading-options", navigation: true, content: ".accordion-body" });


    //
    // This is uncomment as I'm not sure if we should save the latest settings tab...
    //
    //jQuery('#buddyforms_formbuilder_settings a').click(function(e) {
    //    e.preventDefault();
    //    jQuery(this).tab('show');
    //});
    //
    //// store the currently selected tab in the hash value
    //jQuery("#buddyforms_formbuilder_settings ul.nav-tabs > li > a").on("shown.bs.tab", function(e) {
    //    var id = jQuery(e.target).attr("href").substr(1);
    //    window.location.hash = id;
    //});
    //
    //// on load of the page: switch to the currently selected tab
    //var hash = window.location.hash;
    //jQuery('#buddyforms_formbuilder_settings a[href="' + hash + '"]').tab('show');

    jQuery(document.body).on('change', '#bf_add_new_form_element_modal', function () {
        jQuery('#formbuilder-action-select-modal').dialog("close");
        jQuery('#bf_add_new_form_element').val(jQuery('#bf_add_new_form_element_modal').val());
        jQuery( "#formbuilder-add-element" ).trigger( "click" );
    });

    function bf_alert(alert_message){

        jQuery('<div></div>').dialog({
            modal: true,
            title: "Info",
            open: function() {
                jQuery(this).html(alert_message);
            },
            buttons: {
                Ok: function() {
                    jQuery( this ).dialog( "close" );
                }
            }
        });  //end confirm dialog

    }

    jQuery(document.body).on('click', '#formbuilder-add-element', function () {

        jQuery('.formbuilder-spinner').addClass('is-active');

        var action = jQuery(this);
        var post_id = bf_getUrlParameter('post');

        if (post_id == undefined)
            post_id = 0;

        var fieldtype = jQuery('#bf_add_new_form_element').val();

        if (fieldtype === 'none') {
            jQuery('#bf_add_new_form_element_modal').val('none')

            jQuery('#formbuilder-action-select-modal').dialog({
                title: "Please Select a Field Type",
                height: 240,
                modal: true,
            });
            jQuery('.formbuilder-spinner').removeClass('is-active');
            return false;
        }

        var unique    = jQuery('#bf_add_new_form_element').find(':selected').data('unique');
        var exist = jQuery("#sortable_buddyforms_elements .bf_" + fieldtype);

        if (unique === 'unique') {
            if (exist !== null && typeof exist === 'object' && exist.length > 0) {
                bf_alert('This element can only be added once into each form');
                jQuery('.formbuilder-spinner').removeClass('is-active');
                return false;
            }
        }

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                "action": "buddyforms_display_form_element",
                "fieldtype": fieldtype,
                "unique": unique,
                "post_id": post_id
            },
            success: function (data) {
                if (data == 'unique') {
                    bf_alert('This element can only be added once into each form');
                    return false;
                }

                jQuery('.buddyforms_template').remove();

                data = data.replace('accordion-body collapse', 'accordion-body in collapse');

                jQuery('#sortable_buddyforms_elements').append(data);
                jQuery('.formbuilder-spinner').removeClass('is-active');

                bf_update_list_item_number();

                jQuery('#buddyforms_form_elements').removeClass('closed');
                jQuery("html, body").animate({scrollTop: jQuery('#buddyforms_form_elements ul li:last').offset().top - 200}, 1000);

            },
            error: function () {
                jQuery('.formbuilder-spinner').removeClass('is-active');
                jQuery('<div></div>').dialog({
                    modal: true,
                    title: "Info",
                    open: function() {
                        var markup = 'Something went wrong ;-(sorry)';
                        jQuery(this).html(markup);
                    },
                    buttons: {
                        Ok: function() {
                            jQuery( this ).dialog( "close" );
                        }
                    }
                });
            }
        });
        return false;

    });

    jQuery(document.body).on('click', '.bf_form_template', function () {
        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            url: ajaxurl,
            data: {
                "action": "buddyforms_form_template",
                "template": jQuery(this).data("template"),
            },
            success: function (data) {

                console.log(data);

                jQuery.each(data, function (i, val) {
                    switch (i) {
                        case 'html':
                            jQuery('.buddyforms_forms_builder').replaceWith(val);
                            bf_update_list_item_number();
                            break;
                        case 'form_setup':
                            jQuery.each(val, function (i2, form_setup) {
                                jQuery('input[name="buddyforms_options[' + i2 + ']"]').val(form_setup).change();
                                jQuery('select[name="buddyforms_options[' + i2 + ']"]').val(form_setup).change();
                            });
                            break;
                        default:
                            bf_alert(val);
                    }

                });

            },
            error: function () {
                jQuery('<div></div>').dialog({
                    modal: true,
                    title: "Info",
                    open: function() {
                        var markup = 'Something went wrong ;-(sorry)';
                        jQuery(this).html(markup);
                    },
                    buttons: {
                        Ok: function() {
                            jQuery( this ).dialog( "close" );
                        }
                    }
                });
            }
        });
        return false;

    });

    jQuery(".bf-select2").select2({
        placeholder: "Select an option"
    });

    jQuery(document.body).on('change', '.bf_hidden_multi_checkbox', function () {

        var input = jQuery(this).find("input");
        var id = input.attr('id');

        if(input.val() === 'admin')
            return;

        if (jQuery(input).is(':checked')) {

            jQuery('.' + id).removeClass('hidden');
            jQuery('.' + id + ' td .checkbox label').removeClass('hidden');
            jQuery('.' + id + ' td .' + id).removeClass('hidden');

        } else {

            jQuery('.' + id).addClass('hidden');

        }

    });

    jQuery(document.body).on('change', '.bf_hidden_checkbox', function () {

        var input = jQuery(this).find("input");
        var ids = input.attr('bf_hidden_checkbox');
        var id = input.attr('id');

        if (!ids)
            return;

        if (jQuery(input).is(':checked')) {
            ids = ids.split(" ");

            ids.forEach(function (entry) {
                jQuery('#table_row_' + entry).removeClass('hidden');
                jQuery('#table_row_' + entry + ' td .checkbox label').removeClass('hidden');
                jQuery('#table_row_' + entry + ' td #' + entry).removeClass('hidden');
                jQuery('#' + entry).removeClass('hidden');
            });
        } else {
            ids = ids.split(" ");
            ids.forEach(function (entry) {
                jQuery('#table_row_' + entry).addClass('hidden');
            });
        }

    });

    jQuery('.bf_tax_select').live('change', function () {

        var id = jQuery(this).attr('id');
        var taxonomy = jQuery(this).val();
        var taxonomy_default = jQuery("#taxonomy_default_" + id);

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                "action": "buddyforms_update_taxonomy_default",
                "taxonomy": taxonomy,
            },
            success: function (data) {
                if (data != false) {
                    taxonomy_default.val(null).trigger("change");
                    taxonomy_default.select2({placeholder: "Select default term"}).trigger("change");

                    taxonomy_default.html(data);
                }

            },
            error: function () {
                jQuery('<div></div>').dialog({
                    modal: true,
                    title: "Info",
                    open: function() {
                        var markup = 'Something went wrong ;-(sorry)';
                        jQuery(this).html(markup);
                    },
                    buttons: {
                        Ok: function() {
                            jQuery( this ).dialog( "close" );
                        }
                    }
                });
            }
        });

    });


    jQuery('#publish').click(function () {

        var create_new_form_name = jQuery('[name="post_title"]').val();

        var error = false;
        if (create_new_form_name === '') {
            jQuery('[name="post_title"]').removeClass('bf-ok');
            jQuery('[name="post_title"]').addClass('bf-error');
            error = true;
        } else {
            jQuery('[name="post_title"]').removeClass('bf-error');
            jQuery('[name="post_title"]').addClass('bf-ok');
        }

        // traverse all the required elements looking for an empty one
        jQuery("#post input[required]").each(function () {

            // if the value is empty, that means that is invalid
            if (jQuery(this).val() == "") {

                // hide the currently open accordion and open the one with the required field
                jQuery(".accordion-body.collapse.in").removeClass("in");
                jQuery(this).closest(".accordion-body.collapse").addClass("in").css("height", "auto");
                jQuery('#buddyforms_form_setup').removeClass('closed');
                jQuery('#buddyforms_form_elements').removeClass('closed');

                jQuery("html, body").animate({scrollTop: jQuery(this).offset().top - 250}, 1000);

                // stop scrolling through the required elements
                return false;
            }
        });


        if (error === true) {
            return false;
        }

    });

    var bf_getUrlParameter = function bf_getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };

    jQuery('.bf_add_element_action').click(function () {

        var action = jQuery(this);
        var post_id = bf_getUrlParameter('post');

        if (post_id == undefined)
            post_id = 0;

        var fieldtype = jQuery(this).data("fieldtype");
        var unique = jQuery(this).data("unique");

        var exist = jQuery("#sortable_buddyforms_elements .bf_" + fieldtype);

        if (unique === 'unique') {
            if (exist !== null && typeof exist === 'object' && exist.length > 0) {
                bf_alert('This element can only be added once into each form');
                return false;
            }
        }

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                "action": "buddyforms_display_form_element",
                "fieldtype": fieldtype,
                "unique": unique,
                "post_id": post_id
            },
            success: function (data) {
                if (data == 'unique') {
                    bf_alert('This element can only be added once into each form');
                    return false;
                }

                jQuery('.buddyforms_template').remove();

                data = data.replace('accordion-body collapse', 'accordion-body in collapse');

                var myvar = action.attr('href');
                var arr = myvar.split('/');
                jQuery('#sortable_buddyforms_elements').append(data);

                bf_update_list_item_number();

                jQuery('#buddyforms_form_elements').removeClass('closed');
                jQuery("html, body").animate({scrollTop: jQuery('#buddyforms_form_elements ul li:last').offset().top - 200}, 1000);

            },
            error: function () {;
                jQuery('<div></div>').dialog({
                    modal: true,
                    title: "Info",
                    open: function() {
                        var markup = 'Something went wrong ;-(sorry)';
                        jQuery(this).html(markup);
                    },
                    buttons: {
                        Ok: function() {
                            jQuery( this ).dialog( "close" );
                        }
                    }
                });
            }
        });
        return false;
    });

    jQuery(document).on('click', '.bf_delete_field', function () {

        var del_id = jQuery(this).attr('id');

        if (confirm('Delete Permanently'))
            jQuery("#field_" + del_id).remove();

        return false;
    });
    jQuery(document).on('click', '.bf_delete_trigger', function () {

        var del_id = jQuery(this).attr('id');

        if (confirm('Delete Permanently')) {
            jQuery("#trigger" + del_id).remove();
            jQuery(".trigger" + del_id).remove();
        }


        return false;
    });

    jQuery(document).on('click', '.bf_add_input', function () {

        var action = jQuery(this);
        var args = action.attr('href').split("/");
        var numItems = jQuery('#table_row_' + args[0] + '_select_options ul li').size();

        numItems = numItems + 1;
        jQuery('#table_row_' + args[0] + '_select_options ul').append(
            '<li class="field_item field_item_' + args[0] + '_' + numItems + '">' +
            '<table class="wp-list-table widefat fixed posts"><tbody><tr><td>' +
            '<input class="field-sortable" type="text" name="buddyforms_options[form_fields][' + args[0] + '][options][' + numItems + '][label]">' +
            '</td><td>' +
            '<input class="field-sortable" type="text" name="buddyforms_options[form_fields][' + args[0] + '][options][' + numItems + '][value]">' +
            '</td><td class="manage-column column-author">' +
            '<a href="#" id="' + args[0] + '_' + numItems + '" class="bf_delete_input">Delete</a>' +
            '</td></tr></li></tbody></table>');
        return false;

    });

    jQuery(document).on('click', '.bf_delete_input', function () {
        var del_id = jQuery(this).attr('id');
        if (confirm('Delete Permanently'))
            jQuery(".field_item_" + del_id).remove();
        return false;
    });

    //jQuery(document).on('mousedown', '.bf_list_item', function () {
    //    jQuery(".element_field_sortable").sortable({
    //        update: function (event, ui) {
    //            var testst = jQuery(".element_field_sortable").sortable('toArray');
    //            for (var key in testst) {
    //                //	bf_alert(key); this needs to be rethinked ;-)
    //            }
    //        }
    //    });
    //});

    function bf_update_list_item_number() {
        jQuery(".buddyforms_forms_builder ul").each(function () {
            jQuery(this).children("li").each(function (t) {
                jQuery(this).find("td.field_order .circle").first().html(t + 1)
            })
        })
    }

    bf_update_list_item_number();

    jQuery(document).on('mousedown', '.bf_list_item', function () {
        itemList = jQuery(this).closest('.sortable').sortable({
            update: function (event, ui) {
                bf_update_list_item_number();
            }
        });
    });

    function bf_update_list_item_number_mail() {

        jQuery("#mailcontainer .bf_trigger_list_item").each(function (t) {
            jQuery(this).find("td.field_order .circle").first().html(t + 1)
        })
    }

    bf_update_list_item_number_mail();

    jQuery('#mail_notification_add_new').click(function (e) {

        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            url: ajaxurl,
            data: {"action": "buddyforms_new_mail_notification"},
            success: function (data) {

                console.log(data);

                jQuery('#no-trigger-mailcontainer').hide();
                jQuery('#mailcontainer').append(data['html']);

                tinymce.execCommand( 'mceRemoveEditor', false, 'bf_mail_body' + data['trigger_id'] );
                tinymce.execCommand( 'mceAddEditor', false, 'bf_mail_body' + data['trigger_id'] );

                bf_update_list_item_number_mail();

            }
        });
        return false;
    });

    jQuery('#post_status_mail_notification_add_new').click(function (e) {

        var error = false;
        var trigger = jQuery('.post_status_mail_notification_trigger').val();

        if(!trigger){
            trigger = 'Mail_Notification'
        }

        if (trigger == 'none') {
            bf_alert('You have to select a trigger first.');
            return false;
        }

        // traverse all the required elements looking for an empty one
        jQuery("#post-status-mail-container li.bf_trigger_list_item").each(function () {
            if (jQuery(this).attr('id') == 'trigger' + trigger) {
                bf_alert('Trigger already exists');
                error = true;
            }
        })

        if (error == true)
            return false;

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {"action": "buddyforms_new_post_status_mail_notification", "trigger": trigger},
            success: function (data) {

                if (data == 0) {
                    bf_alert('trigger already exists');
                    return false;
                }
                jQuery('#no-trigger-post-status-mail-container').hide();
                jQuery('#post-status-mail-container').append(data);

                tinymce.execCommand( 'mceRemoveEditor', false, 'bf_mail_body' );
                tinymce.execCommand( 'mceAddEditor', false, 'bf_mail_body' );

                bf_update_list_item_number_mail();

            }
        });
        return false;
    });

    jQuery(".bf_check_all").click(function (e) {

        if (jQuery(".bf_permissions input[type='checkbox']").prop("checked")) {
            jQuery('.bf_permissions :checkbox').prop('checked', false);
            jQuery(this).text(admin_text.check);
        } else {
            jQuery('.bf_permissions :checkbox').prop('checked', true);
            jQuery(this).text(admin_text.uncheck);
        }
        e.preventDefault();
    });

    jQuery('.buddyforms_forms_builder').on('blur', '.use_as_slug', function () {

        var field_name = jQuery(this).val();
        if (field_name === '')
            return;

        var field_id = jQuery(this).attr('data');
        if (field_id === '')
            return;

        var field_slug_val = jQuery('tr .slug' + field_id).val();


        if (field_slug_val === '') {
            jQuery('tr .slug' + field_id).val(slug(field_name, {lower: true}));
        }
        jQuery(this).unbind('blur');
    });

});

// https://github.com/dodo/node-slug
(function (root) {
// lazy require symbols table
    var _symbols, removelist;

    function symbols(code) {
        if (_symbols) return _symbols[code];
        _symbols = require('unicode/category/So');
        removelist = ['sign', 'cross', 'of', 'symbol', 'staff', 'hand', 'black', 'white']
            .map(function (word) {
                return new RegExp(word, 'gi')
            });
        return _symbols[code];
    }

    function slug(string, opts) {
        string = string.toString();
        if ('string' === typeof opts)
            opts = {replacement: opts};
        opts = opts || {};
        opts.mode = opts.mode || slug.defaults.mode;
        var defaults = slug.defaults.modes[opts.mode];
        var keys = ['replacement', 'multicharmap', 'charmap', 'remove', 'lower'];
        for (var key, i = 0, l = keys.length; i < l; i++) {
            key = keys[i];
            opts[key] = (key in opts) ? opts[key] : defaults[key];
        }
        if ('undefined' === typeof opts.symbols)
            opts.symbols = defaults.symbols;

        var lengths = [];
        for (var key in opts.multicharmap) {
            if (!opts.multicharmap.hasOwnProperty(key))
                continue;

            var len = key.length;
            if (lengths.indexOf(len) === -1)
                lengths.push(len);
        }

        var code, unicode, result = "";
        for (var char, i = 0, l = string.length; i < l; i++) {
            char = string[i];
            if (!lengths.some(function (len) {
                    var str = string.substr(i, len);
                    if (opts.multicharmap[str]) {
                        i += len - 1;
                        char = opts.multicharmap[str];
                        return true;
                    } else return false;
                })) {
                if (opts.charmap[char]) {
                    char = opts.charmap[char];
                    code = char.charCodeAt(0);
                } else {
                    code = string.charCodeAt(i);
                }
                if (opts.symbols && (unicode = symbols(code))) {
                    char = unicode.name.toLowerCase();
                    for (var j = 0, rl = removelist.length; j < rl; j++) {
                        char = char.replace(removelist[j], '');
                    }
                    char = char.replace(/^\s+|\s+$/g, '');
                }
            }
            char = char.replace(/[^\w\s\-\.\_~]/g, ''); // allowed
            if (opts.remove) char = char.replace(opts.remove, ''); // add flavour
            result += char;
        }
        result = result.replace(/^\s+|\s+$/g, ''); // trim leading/trailing spaces
        result = result.replace(/[-\s]+/g, opts.replacement); // convert spaces
        result = result.replace(opts.replacement + "$", ''); // remove trailing separator
        if (opts.lower)
            result = result.toLowerCase();
        return result;
    };

    slug.defaults = {
        mode: 'pretty',
    };

    slug.multicharmap = slug.defaults.multicharmap = {
        '<3': 'love', '&&': 'and', '||': 'or', 'w/': 'with',
    };

// https://code.djangoproject.com/browser/django/trunk/django/contrib/admin/media/js/urlify.js
    slug.charmap = slug.defaults.charmap = {
        // latin
        'À': 'A', 'Á': 'A', 'Â': 'A', 'Ã': 'A', 'Ä': 'A', 'Å': 'A', 'Æ': 'AE',
        'Ç': 'C', 'È': 'E', 'É': 'E', 'Ê': 'E', 'Ë': 'E', 'Ì': 'I', 'Í': 'I',
        'Î': 'I', 'Ï': 'I', 'Ð': 'D', 'Ñ': 'N', 'Ò': 'O', 'Ó': 'O', 'Ô': 'O',
        'Õ': 'O', 'Ö': 'O', 'Ő': 'O', 'Ø': 'O', 'Ù': 'U', 'Ú': 'U', 'Û': 'U',
        'Ü': 'U', 'Ű': 'U', 'Ý': 'Y', 'Þ': 'TH', 'ß': 'ss', 'à': 'a', 'á': 'a',
        'â': 'a', 'ã': 'a', 'ä': 'a', 'å': 'a', 'æ': 'ae', 'ç': 'c', 'è': 'e',
        'é': 'e', 'ê': 'e', 'ë': 'e', 'ì': 'i', 'í': 'i', 'î': 'i', 'ï': 'i',
        'ð': 'd', 'ñ': 'n', 'ò': 'o', 'ó': 'o', 'ô': 'o', 'õ': 'o', 'ö': 'o',
        'ő': 'o', 'ø': 'o', 'ù': 'u', 'ú': 'u', 'û': 'u', 'ü': 'u', 'ű': 'u',
        'ý': 'y', 'þ': 'th', 'ÿ': 'y', 'ẞ': 'SS',
        // greek
        'α': 'a', 'β': 'b', 'γ': 'g', 'δ': 'd', 'ε': 'e', 'ζ': 'z', 'η': 'h', 'θ': '8',
        'ι': 'i', 'κ': 'k', 'λ': 'l', 'μ': 'm', 'ν': 'n', 'ξ': '3', 'ο': 'o', 'π': 'p',
        'ρ': 'r', 'σ': 's', 'τ': 't', 'υ': 'y', 'φ': 'f', 'χ': 'x', 'ψ': 'ps', 'ω': 'w',
        'ά': 'a', 'έ': 'e', 'ί': 'i', 'ό': 'o', 'ύ': 'y', 'ή': 'h', 'ώ': 'w', 'ς': 's',
        'ϊ': 'i', 'ΰ': 'y', 'ϋ': 'y', 'ΐ': 'i',
        'Α': 'A', 'Β': 'B', 'Γ': 'G', 'Δ': 'D', 'Ε': 'E', 'Ζ': 'Z', 'Η': 'H', 'Θ': '8',
        'Ι': 'I', 'Κ': 'K', 'Λ': 'L', 'Μ': 'M', 'Ν': 'N', 'Ξ': '3', 'Ο': 'O', 'Π': 'P',
        'Ρ': 'R', 'Σ': 'S', 'Τ': 'T', 'Υ': 'Y', 'Φ': 'F', 'Χ': 'X', 'Ψ': 'PS', 'Ω': 'W',
        'Ά': 'A', 'Έ': 'E', 'Ί': 'I', 'Ό': 'O', 'Ύ': 'Y', 'Ή': 'H', 'Ώ': 'W', 'Ϊ': 'I',
        'Ϋ': 'Y',
        // turkish
        'ş': 's', 'Ş': 'S', 'ı': 'i', 'İ': 'I',
        'ğ': 'g', 'Ğ': 'G',
        // russian
        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo', 'ж': 'zh',
        'з': 'z', 'и': 'i', 'й': 'j', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
        'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h', 'ц': 'c',
        'ч': 'ch', 'ш': 'sh', 'щ': 'sh', 'ъ': 'u', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu',
        'я': 'ya',
        'А': 'A', 'Б': 'B', 'В': 'V', 'Г': 'G', 'Д': 'D', 'Е': 'E', 'Ё': 'Yo', 'Ж': 'Zh',
        'З': 'Z', 'И': 'I', 'Й': 'J', 'К': 'K', 'Л': 'L', 'М': 'M', 'Н': 'N', 'О': 'O',
        'П': 'P', 'Р': 'R', 'С': 'S', 'Т': 'T', 'У': 'U', 'Ф': 'F', 'Х': 'H', 'Ц': 'C',
        'Ч': 'Ch', 'Ш': 'Sh', 'Щ': 'Sh', 'Ъ': 'U', 'Ы': 'Y', 'Ь': '', 'Э': 'E', 'Ю': 'Yu',
        'Я': 'Ya',
        // ukranian
        'Є': 'Ye', 'І': 'I', 'Ї': 'Yi', 'Ґ': 'G', 'є': 'ye', 'і': 'i', 'ї': 'yi', 'ґ': 'g',
        // czech
        'č': 'c', 'ď': 'd', 'ě': 'e', 'ň': 'n', 'ř': 'r', 'š': 's', 'ť': 't', 'ů': 'u',
        'ž': 'z', 'Č': 'C', 'Ď': 'D', 'Ě': 'E', 'Ň': 'N', 'Ř': 'R', 'Š': 'S', 'Ť': 'T',
        'Ů': 'U', 'Ž': 'Z',
        // polish
        'ą': 'a', 'ć': 'c', 'ę': 'e', 'ł': 'l', 'ń': 'n', 'ś': 's', 'ź': 'z',
        'ż': 'z', 'Ą': 'A', 'Ć': 'C', 'Ę': 'E', 'Ł': 'L', 'Ń': 'N', 'Ś': 'S',
        'Ź': 'Z', 'Ż': 'Z',
        // latvian
        'ā': 'a', 'ē': 'e', 'ģ': 'g', 'ī': 'i', 'ķ': 'k', 'ļ': 'l', 'ņ': 'n',
        'ū': 'u', 'Ā': 'A', 'Ē': 'E', 'Ģ': 'G', 'Ī': 'I',
        'Ķ': 'K', 'Ļ': 'L', 'Ņ': 'N', 'Ū': 'U',
        // lithuanian
        'ė': 'e', 'į': 'i', 'ų': 'u', 'Ė': 'E', 'Į': 'I', 'Ų': 'U',
        // romanian
        'ț': 't', 'Ț': 'T', 'ţ': 't', 'Ţ': 'T', 'ș': 's', 'Ș': 'S', 'ă': 'a', 'Ă': 'A',
        // vietnamese
        'Ạ': 'A', 'Ả': 'A', 'Ầ': 'A', 'Ấ': 'A', 'Ậ': 'A', 'Ẩ': 'A', 'Ẫ': 'A',
        'Ằ': 'A', 'Ắ': 'A', 'Ặ': 'A', 'Ẳ': 'A', 'Ẵ': 'A', 'Ẹ': 'E', 'Ẻ': 'E',
        'Ẽ': 'E', 'Ề': 'E', 'Ế': 'E', 'Ệ': 'E', 'Ể': 'E', 'Ễ': 'E', 'Ị': 'I',
        'Ỉ': 'I', 'Ĩ': 'I', 'Ọ': 'O', 'Ỏ': 'O', 'Ồ': 'O', 'Ố': 'O', 'Ộ': 'O',
        'Ổ': 'O', 'Ỗ': 'O', 'Ơ': 'O', 'Ờ': 'O', 'Ớ': 'O', 'Ợ': 'O', 'Ở': 'O',
        'Ỡ': 'O', 'Ụ': 'U', 'Ủ': 'U', 'Ũ': 'U', 'Ư': 'U', 'Ừ': 'U', 'Ứ': 'U',
        'Ự': 'U', 'Ử': 'U', 'Ữ': 'U', 'Ỳ': 'Y', 'Ỵ': 'Y', 'Ỷ': 'Y', 'Ỹ': 'Y',
        'Đ': 'D', 'ạ': 'a', 'ả': 'a', 'ầ': 'a', 'ấ': 'a', 'ậ': 'a', 'ẩ': 'a',
        'ẫ': 'a', 'ằ': 'a', 'ắ': 'a', 'ặ': 'a', 'ẳ': 'a', 'ẵ': 'a', 'ẹ': 'e',
        'ẻ': 'e', 'ẽ': 'e', 'ề': 'e', 'ế': 'e', 'ệ': 'e', 'ể': 'e', 'ễ': 'e',
        'ị': 'i', 'ỉ': 'i', 'ĩ': 'i', 'ọ': 'o', 'ỏ': 'o', 'ồ': 'o', 'ố': 'o',
        'ộ': 'o', 'ổ': 'o', 'ỗ': 'o', 'ơ': 'o', 'ờ': 'o', 'ớ': 'o', 'ợ': 'o',
        'ở': 'o', 'ỡ': 'o', 'ụ': 'u', 'ủ': 'u', 'ũ': 'u', 'ư': 'u', 'ừ': 'u',
        'ứ': 'u', 'ự': 'u', 'ử': 'u', 'ữ': 'u', 'ỳ': 'y', 'ỵ': 'y', 'ỷ': 'y',
        'ỹ': 'y', 'đ': 'd',
        // currency
        '€': 'euro', '₢': 'cruzeiro', '₣': 'french franc', '£': 'pound',
        '₤': 'lira', '₥': 'mill', '₦': 'naira', '₧': 'peseta', '₨': 'rupee',
        '₩': 'won', '₪': 'new shequel', '₫': 'dong', '₭': 'kip', '₮': 'tugrik',
        '₯': 'drachma', '₰': 'penny', '₱': 'peso', '₲': 'guarani', '₳': 'austral',
        '₴': 'hryvnia', '₵': 'cedi', '¢': 'cent', '¥': 'yen', '元': 'yuan',
        '円': 'yen', '﷼': 'rial', '₠': 'ecu', '¤': 'currency', '฿': 'baht',
        "$": 'dollar', '₹': 'indian rupee',
        // symbols
        '©': '(c)', 'œ': 'oe', 'Œ': 'OE', '∑': 'sum', '®': '(r)', '†': '+',
        '“': '"', '”': '"', '‘': "'", '’': "'", '∂': 'd', 'ƒ': 'f', '™': 'tm',
        '℠': 'sm', '…': '...', '˚': 'o', 'º': 'o', 'ª': 'a', '•': '*',
        '∆': 'delta', '∞': 'infinity', '♥': 'love', '&': 'and', '|': 'or',
        '<': 'less', '>': 'greater',
    };

    slug.defaults.modes = {
        rfc3986: {
            replacement: '-',
            symbols: true,
            remove: null,
            lower: true,
            charmap: slug.defaults.charmap,
            multicharmap: slug.defaults.multicharmap,
        },
        pretty: {
            replacement: '-',
            symbols: true,
            remove: /[.]/g,
            lower: false,
            charmap: slug.defaults.charmap,
            multicharmap: slug.defaults.multicharmap,
        },
    };

// Be compatible with different module systems

    if (typeof define !== 'undefined' && define.amd) { // AMD
        // dont load symbols table in the browser
        for (var key in slug.defaults.modes) {
            if (!slug.defaults.modes.hasOwnProperty(key))
                continue;

            slug.defaults.modes[key].symbols = false;
        }
        define([], function () {
            return slug
        });
    } else if (typeof module !== 'undefined' && module.exports) { // CommonJS
        symbols(); // preload symbols table
        module.exports = slug;
    } else { // Script tag
        // dont load symbols table in the browser
        for (var key in slug.defaults.modes) {
            if (!slug.defaults.modes.hasOwnProperty(key))
                continue;

            slug.defaults.modes[key].symbols = false;
        }
        root.slug = slug;
    }

}(this));
