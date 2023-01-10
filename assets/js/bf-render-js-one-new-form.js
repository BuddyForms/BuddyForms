		jQuery(document.body).on('submit', '#buddyforms_form_one-new-form', function (event) {
		    var formIsInitialized = jQuery(this).data('initialize');
		    var formSlug = 'one-new-form';
		    console.log(formSlug);
		    var hasError = (buddyformsGlobal && buddyformsGlobal[formSlug] && buddyformsGlobal[formSlug].hasOwnProperty('errors'));
		    console.log(hasError);
		    if(typeof formIsInitialized !== 'undefined' && !hasError){
		        return false;
		    }
            event.preventDefault();
            console.log('internal submit');
            if(BuddyFormsHooks){
                var formTargetStatus = 'publish';
                var formTargetStatusElement = jQuery(this).find("button[type=submit]:focus" );
                if(formTargetStatusElement){
                    formTargetStatus = formTargetStatusElement.attr('data-status');
                }
                BuddyFormsHooks.doAction('buddyforms:submit', [jQuery(this), event]);
                BuddyFormsHooks.doAction('buddyforms:form:render', ["one-new-form", ["bootstrap","jQuery","focus"], "buddyforms_ajax_process_edit_post", "post", formTargetStatus]);
            } else {
                alert('Error, contact the admin!');
            }
            return false;
        });