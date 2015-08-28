=== BuddyForms  ===

Contributors: Sven Lehnert
Tags: collaborative, publishing, buddypress, groups, custom post types, taxonomy, frontend, posting, editing, forms, form builder
Requires at least: WordPress 3.x, BuddyPress 2.x
Tested up to: WordPress 4.3, BuddyPress 2.3.2.1
Stable tag: 1.4.1

=== Documentation & Support === 

You can find all help buttons in your BuddyForms Settings Panel in your WP Dashboard! 

// Documentation 
http://docs.buddyforms.com

// Support Tickets 
You can also create support tickets there. 
Login with your account from https://buddyforms.com/checkout/support-tickets/

// Changelog

== 1.4.1 ==
fixed small merging issues.

== 1.4 ==
add a new filter buddyforms_wp_login_form to the form
add check if submitter is author
fix a bug with the form element title. in some cases with other js conflicts it was possible to delete the title tag.
Add new options to the form builder to select the action happened after form submission
rewrite the form.php and add ajax form submit functionality to the form
add nonce check to the ajax form
build a work around for the wp_editor shortcodes issue. Need to investigate deeper why the shortcodes are executed. For now I load the wp_editor content with jQuery
fixed smaller bugs reported by users
add help text
fixed a types conflict in the form builder
add post_parent support
add ajax for post delete
removed the old delete function
removed the old delete rewrite roles
only display posts created by the form
small css changes and clean up the css
changed the-loop.php
Add options to change the Form Element Title
rewrite the addons section and load all BuddyForms Extension with a buddyforms search from wordpress.org
changed the featured image function to work with ajax
add file and featured image ajax upload
add wp_handle_upload_prefilter to check for allowed file types
fixed a checkbox issue if attribute id was empty
start adding deep validation
add new option hidden to the taxonomy field
add an ajax form option to the form builder
start adding validation to the form
fixed a bug with the featured image upload
add required option to the content form element
fixed a bug in the taxonomy hidden form element
fixed a bug with the allowed post type in the media uploader
fixed the after submit options. It was not working correctly
add new option to the title to make it a hidden field
fixed an issue with the edit link options
changed the url to the new buddyforms.com site
rewrite the jQuery to make the button handling work
add stripslashes to the form elements
rename session
fixed delete featured image issue
add beautiful validation to the form ;)
smaler bug fixed
Super nice validation options for every form element max min and message ;)
Admin UI improvements
removed the old zendesk support and link to the new support system
add new option to the file element to select supported file types
adjust the form messages
clean up js
clean up code
finalise the new ui

== 1.3.2 ==

add missing translations. Thanks to Milena to point me on this !
Made more strings translatable.
Fixed some typos.
Started making admin.js translatable. Props @rugwarrior
Fixed typo revison -> revision. Props @rugwarrior
Revised PO/MO files for changed source files. Props @rugwarrior
Added German translations. Props @rugwarrior
fixes small issues in the error handling
small clean up of readme.txt Props @rugwarrior
cleanup the code
add new filter bf_form_before_render
check if the form is broken and if some fields are missing do not add it to the adminbar

== 1.3.1 ==

fixed a bug in the taxonomy default form element

== 1.3 ==

Add new check if the user has the needed rights before adding the form to the admin bar
Create new function bf_edit_post_link to support new capabilities in the front end
Switch from chosen to select2
Add new error message to logged off users
Clean up debugger notice
Optimised the link rewrite function
Fixed form submit not working on mobile
Add new filter for the shortcodes button
Add new shortcodes to TinyMCE
Rewrite the Shortcodes
Changed plugin uri
Add new filters to manipulate the edit form id
Add a jQuery to make different submit buttons possible
Add post_parent as parameter
Fixed a bug in the error handling
Small css changes
Clean up the code

== 1.2 ==

create new form elements for title and content
3 new form elements date, number and html
add wp editor options to the form builder form element
add german language files
fixed editing BuddyPress js issues
fixed the shortcode over content issue
update chosen js to the latest version
create a new file form-builder-elements.php
add media uploader js
change split to explode
load the js css only if a BuddyForms view is displayed
css fixes
restructure code
create an update script for the new version
make it possible to enter tags comma separated
spelling correction

== 1.1 ==

add language support
add featured image as form element
add file form element
add ajax to delete a file
fixed a pagination bug
only display the post type related taxonomies in the form element options
add translation text domain "buddyforms"
rebuild the add new form screen
remove unneeded form elements from add form screen
add mail notification settings
add mail notification system to BuddyForms
add date time form element for post status future
spelling session
ui design
Settings page Add Ons rewrite
add new settings page for roles and capabilities
clean up the code
fixed bugs
add new default option to taxonomy form element
add Italian language

== 1.0.5 ==

rename hook buddyforms_add_form_element_in_sidebar to buddyforms_add_form_element_to_sidebar
spelling correction

== 1.0.4 ==

remove unneeded html

== 1.0.3 ==

editing your pending/draft posts from the frontend
fixed some css issues

== 1.0.2 ==

remove old button for community forum
add some new filter

== 1.0.1 ==

catch if create a new post_tag is empty
metabox rework

== 1.0 ==
first release