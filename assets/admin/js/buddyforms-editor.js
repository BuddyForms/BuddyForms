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
    BuddyFormsBuilderHooks.actions[tag].push({priority: priority, callback: callback});
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
    BuddyFormsBuilderHooks.filters[tag].push({priority: priority, callback: callback});
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

function BuddyFormsEditor(){
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

    return {
        init: function () {
            var existBuilder = jQuery('.buddyforms-metabox');
            if (existBuilder && existBuilder.length > 0) {
                jQuery(document).on('click', '.buddyforms-shortcodes-action', insertShortCodeEvent);
            }
        },
    };
}

var buddyFormsEditorInstance = BuddyFormsEditor();
jQuery(document).ready(function () {
    buddyFormsEditorInstance.init();
    console.log('init');
});
