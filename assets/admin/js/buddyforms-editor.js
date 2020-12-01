/**
 * Hooks object
 *
 * This object needs to be declared early so that it can be used in code.
 * Preferably at a global scope.
 */
var BuddyFormsBuilderHooks = BuddyFormsBuilderHooks || {};

BuddyFormsBuilderHooks.actions = BuddyFormsBuilderHooks.actions || {};
BuddyFormsBuilderHooks.filters = BuddyFormsBuilderHooks.filters || {};

/**
 * Add a new Action callback to BuddyFormsBuilderHooks.actions
 *
 * @param tag The tag specified by do_action()
 * @param callback The callback function to call when do_action() is called
 * @param priority The order in which to call the callbacks. Default: 10 (like WordPress)
 */
BuddyFormsBuilderHooks.addAction = function (tag, callback, priority) {
    if (typeof priority === 'undefined') {
        priority = 10;
    }
    // If the tag doesn't exist, create it.
    BuddyFormsBuilderHooks.actions[tag] = BuddyFormsBuilderHooks.actions[tag] || [];
    BuddyFormsBuilderHooks.actions[tag].push({ priority: priority, callback: callback });
};

/**
 * Add a new Filter callback to BuddyFormsBuilderHooks.filters
 *
 * @param tag The tag specified by apply_filters()
 * @param callback The callback function to call when apply_filters() is called
 * @param priority Priority of filter to apply. Default: 10 (like WordPress)
 */
BuddyFormsBuilderHooks.addFilter = function (tag, callback, priority) {
    if (typeof priority === 'undefined') {
        priority = 10;
    }
    // If the tag doesn't exist, create it.
    BuddyFormsBuilderHooks.filters[tag] = BuddyFormsBuilderHooks.filters[tag] || [];
    BuddyFormsBuilderHooks.filters[tag].push({ priority: priority, callback: callback });
};

/**
 * Remove an Action callback from BuddyFormsBuilderHooks.actions
 *
 * Must be the exact same callback signature.
 * Warning: Anonymous functions can not be removed.
 * @param tag The tag specified by do_action()
 * @param callback The callback function to remove
 */
BuddyFormsBuilderHooks.removeAction = function (tag, callback) {
    BuddyFormsBuilderHooks.actions[tag] = BuddyFormsBuilderHooks.actions[tag] || [];
    BuddyFormsBuilderHooks.actions[tag].forEach(function (filter, i) {
        if (filter.callback === callback) {
            BuddyFormsBuilderHooks.actions[tag].splice(i, 1);
        }
    });
};

/**
 * Remove a Filter callback from BuddyFormsBuilderHooks.filters
 *
 * Must be the exact same callback signature.
 * Warning: Anonymous functions can not be removed.
 * @param tag The tag specified by apply_filters()
 * @param callback The callback function to remove
 */
BuddyFormsBuilderHooks.removeFilter = function (tag, callback) {
    BuddyFormsBuilderHooks.filters[tag] = BuddyFormsBuilderHooks.filters[tag] || [];
    BuddyFormsBuilderHooks.filters[tag].forEach(function (filter, i) {
        if (filter.callback === callback) {
            BuddyFormsBuilderHooks.filters[tag].splice(i, 1);
        }
    });
};

/**
 * Calls actions that are stored in BuddyFormsBuilderHooks.actions for a specific tag or nothing
 * if there are no actions to call.
 *
 * @param tag A registered tag in Hook.actions
 * @param options Optional JavaScript object to pass to the callbacks
 */
BuddyFormsBuilderHooks.doAction = function (tag, options) {
    var actions = [];
    if (typeof BuddyFormsBuilderHooks.actions[tag] !== 'undefined' && BuddyFormsBuilderHooks.actions[tag].length > 0) {
        BuddyFormsBuilderHooks.actions[tag].forEach(function (hook) {
            actions[hook.priority] = actions[hook.priority] || [];
            actions[hook.priority].push(hook.callback);
        });

        actions.forEach(function (BuddyFormsBuilderHooks) {
            BuddyFormsBuilderHooks.forEach(function (callback) {
                callback(options);
            });
        });
    }
};

/**
 * Calls filters that are stored in BuddyFormsBuilderHooks.filters for a specific tag or return
 * original value if no filters exist.
 *
 * @param tag A registered tag in Hook.filters
 * @param value The value
 * @param options Optional JavaScript object to pass to the callbacks
 * @options
 */
BuddyFormsBuilderHooks.applyFilters = function (tag, value, options) {
    var filters = [];
    if (typeof BuddyFormsBuilderHooks.filters[tag] !== 'undefined' && BuddyFormsBuilderHooks.filters[tag].length > 0) {
        BuddyFormsBuilderHooks.filters[tag].forEach(function (hook) {
            filters[hook.priority] = filters[hook.priority] || [];
            filters[hook.priority].push(hook.callback);
        });
        filters.forEach(function (BuddyFormsBuilderHook) {
            BuddyFormsBuilderHook.forEach(function (callback) {
                value = callback(value, options);
            });
        });
    }
    return value;
};

function BuddyFormsEditor() {
    jQuery.fn.extend({
        insertShortCode: function (short) {
            return this.each(function (i) {
                if (document.selection) {
                    //For browsers like Internet Explorer
                    this.focus();
                    var sel = document.selection.createRange();
                    sel.text = short;
                    this.focus();
                } else if (this.selectionStart || this.selectionStart == '0') {
                    if (tinymce) {
                        var currentEditor = tinymce.get(this.id);
                        if (currentEditor) {
                            currentEditor.execCommand('mceInsertContent', false, short);
                            return;
                        }
                    }
                    //For browsers like Firefox and Webkit based
                    var startPos = this.selectionStart;
                    var endPos = this.selectionEnd;
                    var scrollTop = this.scrollTop;
                    this.value = this.value.substring(0, startPos) + short + this.value.substring(endPos, this.value.length);
                    this.focus();
                    this.selectionStart = startPos + short.length;
                    this.selectionEnd = startPos + short.length;
                    this.scrollTop = scrollTop;
                } else {
                    if (this instanceof tinymce) {
                        tinymce.activeEditor.execCommand('mceInsertContent', false, short);
                    } else {
                        this.value += short;
                        this.focus();
                    }
                }
            });
        },
    });

    function insertShortCode(target, short) {
        if (!target || !short) {
            return false;
        }
        return target.each(function () {
            if (document.selection) {
                //For browsers like Internet Explorer
                target.focus();
                var sel = document.selection.createRange();
                sel.text = short;
                target.focus();
            } else if (target.selectionStart || target.selectionStart == '0') {
                //For browsers like Firefox and Webkit based
                var startPos = target.selectionStart;
                var endPos = target.selectionEnd;
                var scrollTop = target.scrollTop;
                target.value = target.value.substring(0, startPos) + short + target.value.substring(endPos, target.value.length);
                target.focus();
                target.selectionStart = startPos + short.length;
                target.selectionEnd = startPos + short.length;
                target.scrollTop = scrollTop;
            } else {
                target.value += short;
                target.focus();
            }
        });
    }

    function insertShortCodeEvent(e) {
        e.stopPropagation();
        var currentElement = jQuery(this);
        if (currentElement) {
            var target = currentElement.attr('data-target');
            var elemTarget = jQuery('[name="' + target + '"]');
            if (elemTarget && elemTarget.length > 0) {
                var short = currentElement.attr('data-short');
                if (short && short.length > 0) {
                    elemTarget.insertShortCode(short);
                    return false;
                }
            }
        }
    }

    function header() {
        const headerId = '#buddyforms_form_editor_header';

        function change() {
            function activeForm(id) {
                window.location = '/wp-admin/post.php?post='+id+'&action=edit';
            }

            function formType(id) {
                console.log('Changed to ➡️', id);
            }

            function frontend(id) {
                console.log('Changed to ➡️', id);
            }

            return {
                activeForm: function (id) {
                    activeForm(id);
                },
                formType: function (id) {
                    formType(id);
                },
                frontend: function (id) {
                    frontend(id);
                }
            };
        }

        function runEvent() {
            jQuery(document).on('change', headerId + ' select[data-header="select"][name]', function (e) {
                e.preventDefault();

                const selectName = jQuery(this)[0].name;
                const selectVal = jQuery(this).val();

                switch (selectName) {
                    case 'bf_forms_selector': {
                        change().activeForm(selectVal);
                        break;
                    }
                    case 'bf_forms_type': {
                        change().formType(selectVal);
                        break;
                    }
                    case 'bf_forms_frontend': {
                        change().frontend(selectVal);
                        break;
                    }
                }
            });
        }

        function constructor() {
            const formTitle = jQuery('#titlediv #title[name="post_title"]');
            const formId = jQuery('form input[name="post_ID"]').val();

            jQuery('#post-body').removeClass('columns-2');

            formTitle.remove();
            jQuery(headerId + ' .tk-editor-header-info input[name="post_title"]').val(formTitle.val());
            jQuery(headerId + ' .tk-editor-header-info select[name="bf_forms_selector"] option[value="this:form"]').html(formTitle.val());
            jQuery(headerId + ' .tk-editor-header-info select[name="bf_forms_selector"] option[value="this:form"]').val(formId);
        }

        return {
            init: function () {
                constructor();
                runEvent();
            }
        };
    }

    function tabs() {
        const selectorButtons = '[data-bf-editor-section-button]';
        const selectorSections = '[data-bf-editor-section]';
        const selectorGroup = '[data-bf-editor-section-group]';

        function runEvent() {
            jQuery(document).on('click', selectorButtons + selectorGroup, function (e) {
                e.preventDefault();
                const id = jQuery(this)[0].dataset.bfEditorSectionButton;
                const group = jQuery(this)[0].dataset.bfEditorSectionGroup;

                setTab(id, group);
            });
        }

        function setTab(id, group) {
            if (!id || !group || jQuery(selectorSections + '[data-bf-editor-section="' + id + '"]').length === 0) {
                return false;
            }

            jQuery(selectorSections + '[data-bf-editor-section-group="' + group + '"]').attr('hidden', 'hidden');
            jQuery(selectorButtons + '[data-bf-editor-section-group="' + group + '"]').removeClass('tk-is-active');

            jQuery(selectorSections + '[data-bf-editor-section-group="' + group + '"][data-bf-editor-section="' + id + '"]').removeAttr('hidden');
            jQuery(selectorButtons + '[data-bf-editor-section-group="' + group + '"][data-bf-editor-section-button="' + id + '"]').addClass('tk-is-active');
        }

        function constructor() {
            jQuery(jQuery(selectorButtons + '.tk-is-active')).map(function () {
                const id = jQuery(this)[0].dataset.bfEditorSectionButton;
                const group = jQuery(this)[0].dataset.bfEditorSectionGroup;

                setTab(id, group);
            });
        }

        return {
            init: function () {
                constructor();
                runEvent();
            }
        };
    }

    function modal() {
        const tkModal = function (id) {
            let res = '.tk-modal';

            if (id !== 0) {
                if (id) {
                    res += '[data-modal-id="' + id + '"]';
                } else {
                    res += '[data-modal-id]';
                }
            }

            return res;
        };
        const tkModalOn = 'tk-is-active';

        function generateId(init) {
            let res = '';
            let str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

            if (init) {
                res = init + ':';
            }

            for (var i = 0; i < 16; i++) {
                res += str.charAt(Math.floor(Math.random() * str.length));
            }

            return res;
        }

        function stateChange(id, act) {
            const thisModal = jQuery(tkModal(id));

            if (!thisModal.length) {
                return false;
            }

            switch (act) {
                case 0: {
                    thisModal.removeClass(tkModalOn);
                    break;
                }

                case 1: {
                    thisModal.addClass(tkModalOn);
                    break;
                }

                default: {
                    if (thisModal.is('.' + tkModalOn)) {
                        thisModal.removeClass(tkModalOn);
                    } else {
                        thisModal.addClass(tkModalOn);
                    }
                    break;
                }
            }

            return true;
        }

        function constructor() {
            jQuery(tkModal(0)).each(function () {
                const id = generateId('TK-MODAL');

                function setDataId(e) {
                    jQuery(e).attr('data-modal-id', id);

                    if (jQuery(e)[0].children.length > 0) {
                        childrenEach(jQuery(e)[0].children);
                    }
                }

                function childrenEach(e) {
                    jQuery(e).map(function () {
                        setDataId(jQuery(this));
                    });
                }

                setDataId(jQuery(this));
            });
        }


        function runEvent() {
            jQuery(document).on('click', tkModal(), function (e) {
                const modalId = jQuery(this)[0].dataset.modalId;

                if (
                    jQuery(e.target).is(tkModal() + ' .tk-modal-close') ||
                    jQuery(e.target).is(tkModal() + ' .tk-modal-background')
                ) {
                    stateChange(modalId, 0);
                }
            });
        }

        return {
            init: function () {
                constructor();
                runEvent();
            },
            state: function (id, act) {
                stateChange(id, act);
            }
        };
    }

    return {
        init: function () {
            var existBuilder = jQuery('.buddyforms-metabox');
            if (existBuilder && existBuilder.length > 0) {
                jQuery(document).on('click', '.buddyforms-shortcodes-action', insertShortCodeEvent);
            }
            header().init();
            tabs().init();
            modal().init();
        },
        modalStateChange: function (id, act) {
            modal().state(id, act);
        }
    };
}

var buddyFormsEditorInstance = BuddyFormsEditor();
jQuery(document).ready(function () {
    buddyFormsEditorInstance.init();
    console.log('init');
});
