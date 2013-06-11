<?php
/*
 Plugin Name: BuddyForms
 Plugin URI: http://buddyforms.com
 Description: Form Magic and Collaborative Publishing for WordPress. With Frontend Editing and Drag-and-Drop Form Builder.   
 Version: 0.1 Beta
 Author: ThemeKraft
 Author URI: http://themekraft.com
 Licence: GPLv3
 Network: true
 */

define('buddyforms', '1.0 rc1');
global $buddyforms;
	
/**
 * Loads BuddyForms files only if BuddyPress is present
 *
 * @package BuddyForms
 * @since 0.1-beta
 */
function buddyforms_init() {
	require (dirname(__FILE__) . '/BuddyForms.php');
	$buddyforms_new = new BuddyForms();
}

add_action('init', 'buddyforms_init', 0);