=== BuddyForms  ===

Contributors: svenl77
Tags: collaborative, publishing, buddypress, groups, custom post types, taxonomy, frontend, posting, editing, forms, form builder
Requires at least: WordPress 3.9, BuddyPress 2.x
Tested up to: WordPress 4.3.1
Stable tag: 1.4.2
Author: Sven Lehnert
Author URI: https://profiles.wordpress.org/svenl77
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WordPress Front End Editor And Form Builder For Your User Generated Content

== Description ==

BuddyForms enables Your users to share their own content in a way that You choose to.
Create versatile and creative content forms that Your users can use to share content with, and become a part of the content sharing community

== Frequently Asked Questions ==

You can find all help buttons in your BuddyForms Settings Panel in your WP Dashboard! 

<b>Search the Documentation</b>
http://docs.buddyforms.com

<b>Create a new Support Ticket</b>
Create new Support Tickets or check your existing once in your BuddyForms Account.
https://buddyforms.com/checkout/support-tickets/

or write us a email: support@buddyforms.com

== Upgrade Notice ==

If you updated from version 1.3 please test your Form Elements "Featured Image" and "File". They have changed rapidly.
If you encounter any issues just delete the form elements and add them to the form again. This should fix all issues.

== Changelog ==

1.4.2
<ul>
<li>removed overflow: auto; from the list css to avoid conflicts</li>
<li>add missing required option to form elements</li>
<li>fix the hidden title required issue</li>
<li>clean up the code</li>
</ul>

1.4.1
<ul>
<li>fixed small merging issues.</li>
</ul>

1.4
<ul>
	<li>add a new filter buddyforms_wp_login_form to the form</li>
	<li>add check if submitter is author</li>
	<li>fix a bug with the form element title. in some cases with other js conflicts it was possible to delete the title tag.</li>
	<li>Add new options to the form builder to select the action happened after form submission</li>
	<li>rewrite the form.php and add ajax form submit functionality to the form</li>
	<li>add nonce check to the ajax form</li>
	<li>build a work around for the wp_editor shortcodes issue. Need to investigate deeper why the shortcodes are executed. For now I load the wp_editor content with jQuery</li>
	<li>fixed smaller bugs reported by users</li>
	<li>add help text</li>
	<li>fixed a types conflict in the form builder</li>
	<li>add post_parent support</li>
	<li>add ajax for post delete</li>
	<li>removed the old delete function</li>
	<li>removed the old delete rewrite roles</li>
	<li>only display posts created by the form</li>
	<li>small css changes and clean up the css</li>
	<li>changed the-loop.php</li>
	<li>Add options to change the Form Element Title</li>
	<li>rewrite the addons section and load all BuddyForms Extension with a buddyforms search from wordpress.org</li>
	<li>changed the featured image function to work with ajax</li>
	<li>add file and featured image ajax upload</li>
	<li>add wp_handle_upload_prefilter to check for allowed file types</li>
	<li>fixed a checkbox issue if attribute id was empty</li>
	<li>start adding deep validation</li>
	<li>add new option hidden to the taxonomy field</li>
	<li>add an ajax form option to the form builder</li>
	<li>start adding validation to the form</li>
	<li>fixed a bug with the featured image upload</li>
	<li>add required option to the content form element</li>
	<li>fixed a bug in the taxonomy hidden form element</li>
	<li>fixed a bug with the allowed post type in the media uploader</li>
	<li>fixed the after submit options. It was not working correctly</li>
	<li>add new option to the title to make it a hidden field</li>
	<li>fixed an issue with the edit link options</li>
	<li>changed the url to the new buddyforms.com site</li>
	<li>rewrite the jQuery to make the button handling work</li>
	<li>add stripslashes to the form elements</li>
	<li>fixed delete featured image issue</li>
	<li>add beautiful validation to the form ;)</li>
	<li>Super nice validation options for every form element max min and message ;)</li>
	<li>Admin UI improvements</li>
	<li>removed the old zendesk support and link to the new support system</li>
	<li>add new option to the file element to select supported file types</li>
	<li>adjust the form messages</li>
	<li>clean up js</li>
	<li>clean up code</li>
	<li>finalise the new ui</li>
</ul>

1.3.2
<ul>
	<li>check if the form is broken and if some fields are missing do not add it to the adminbar</li>
	<li>add new filter bf_form_before_render</li>
	<li>cleanup the code</li>
	<li>small clean up of readme.txt Props @rugwarrior</li>
	<li>fixes small issues in the error handling</li>
	<li>Added German translations. Props @rugwarrior</li>
	<li>Revised PO/MO files for changed source files. Props @rugwarrior</li>
	<li>Fixed typo revison -&gt; revision. Props @rugwarrior</li>
	<li>Started making admin.js translatable. Props @rugwarrior</li>
	<li>Fixed some typos.</li>
	<li>Made more strings translatable.</li>
	<li>add missing translations. Thanks to Milena to point me on this !</li>
</ul>

1.3.1
<ul>
	<li>Fixed a bug in the taxonomy default form element</li>
</ul>

1.3
<ul>
	<li>Add new check if the user has the needed rights before adding the form to the admin bar</li>
	<li>Create new function bf_edit_post_link to support new capabilities in the front end</li>
	<li>Switch from chosen to select2</li>
	<li>Add new error message to logged off users</li>
	<li>Clean up debugger notice</li>
	<li>Optimised the link rewrite function</li>
	<li>Fixed form submit not working on mobile</li>
	<li>Add new filter for then shortcodes button</li>
	<li>Add new shortcodes to tynymce</li>
	<li>Rewrite the Shortcodes</li>
	<li>Changed plugin uri</li>
	<li>Add new filters to manipulate the edit form id</li>
	<li>Add a jQuery to make different submit buttons possible</li>
	<li>Add post_parent as parameter</li>
	<li>Fixed a bug in the error handling</li>
	<li>Small css changes</li>
	<li>Clean up the code</li>
</ul>

1.2
<ul>
	<li>create new form elements for title and content</li>
	<li>3 new form elements: date, number and html</li>
	<li>add wp editor options to the form builder in the content element</li>
	<li>fixed editing BuddyPress js issues</li>
	<li>fixed shortcode over content issues</li>
	<li>update chosen js to the latest version</li>
	<li>add media uploader js</li>
	<li>change split to explode</li>
	<li>load the js css only if a buddyforms view is displayed</li>
	<li>css fixes</li>
	<li>restructure code</li>
	<li>create an update script for the new version</li>
	<li>make it possible to enter tags comma separated</li>
	<li>spelling correction</li>
	<li>add german language files</li>
</ul>

1.1
<ul>
	<li>add language support</li>
	<li>add featured image as form element</li>
	<li>add file form element</li>
	<li>add ajax to delete a file</li>
	<li>fixxed a pagination bug</li>
	<li>only display the post type related taxonomies in the form element options</li>
	<li>add translation textdomain "buddyforms"</li>
	<li>rebuild the add new form screen</li>
	<li>remove unneeded form elements from add form screen</li>
	<li>add mail notification settings</li>
	<li>add mail notification system to buddy forms</li>
	<li>add date time form element for post status future</li>
	<li>spelling session</li>
	<li>ui design</li>
	<li>Settings page Add Ons rewrite</li>
	<li>add new settings page for roles and capabilities</li>
	<li>cleanup the code</li>
	<li>fixed bugs</li>
	<li>add new default option to taxonomy form element</li>
	<li>add Italien language</li>
</ul>

1.0.5
<ul>
	<li>rename hook buddyforms_add_form_element_in_sidebar to buddyforms_add_form_element_to_sidebar</li>
	<li>spelling correction</li>
</ul>

1.0.4
<ul>
	<li>remove unneeded html</li>
</ul>

1.0.3
<ul>
	<li>editing your pending/draft posts from the frontend.</li>
	<li>fixed some css issues</li>
</ul>

1.0.2
<ul>
	<li>remove old button for community forum</li>
	<li>add some new filter</li>
</ul>

1.0.1
<ul>
	<li>catch if create a new post_tag is empty</li>
	<li>metabox rework</li>
</ul>

1.0
<ul>
	<li>first release</li>
</ul>
