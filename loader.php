<?php
/*
 Plugin Name: BuddyForms
 Plugin URI: http://themekraft.com
 Description:   
 Version: 0.1 beta
 Author: Sven Lehnert
 Author URI: http://themekraft.com
 Licence: GPLv3
 Network: true
 */

define('buddyforms', '0.1');

/**
 * Loads buddyforms files only if BuddyPress is present
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function buddyforms_init() {
	global $wpdb;

	if (is_multisite() && BP_ROOT_BLOG != $wpdb->blogid)
		return;

	require (dirname(__FILE__) . '/buddyforms.php');
	$buddyforms = new BuddyForms();
}

add_action('bp_loaded', 'buddyforms_init', 0);
