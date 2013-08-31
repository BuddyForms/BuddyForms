<?php
/*
 Plugin Name: BuddyForms
 Plugin URI:  http://buddyforms.com
 Description: Form Magic and Collaborative Publishing for WordPress. With Frontend Editing and Drag-and-Drop Form Builder.   
 Version: 1.0 beta 3
 Author: Sven Lehnert
 Author URI: http://themekraft.com/members/svenl77/
 Licence: GPLv3
 Network: false
 */

define('buddyforms', '1.0 beta 3');
global $buddyforms;
	
/**
 * Loads BuddyForms files only if BuddyPress is present
 *
 * @package BuddyForms
 * @since 0.1-beta
 */
function buddyforms_init() {
	require (dirname(__FILE__) . '/buddyforms.php');
	$buddyforms_new = new BuddyForms();
}

add_action('init', 'buddyforms_init', 0);