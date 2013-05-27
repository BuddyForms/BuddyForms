<?php
/*
 Plugin Name: BuddyForms
 Plugin URI: http://buddyforms.com
 Description:   
 Version: 0.1 rc1
 Author: Sven Lehnert
 Author URI: http://themekraft.com
 Licence: GPLv3
 Network: true
 */

define('buddyforms', '1.0 rc1');
global $buddyforms;
	
/**
 * Loads buddyforms files only if BuddyPress is present
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function buddyforms_init() {
	require (dirname(__FILE__) . '/BuddyForms.php');
	$buddyforms_new = new BuddyForms();
}

add_action('init', 'buddyforms_init', 0);