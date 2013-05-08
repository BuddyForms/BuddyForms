<?php
/*
 Plugin Name: CPT4BP
 Plugin URI: http://themekraft.com
 Description:   
 Version: 0.1 beta
 Author: Sven Lehnert
 Author URI: http://themekraft.com
 Licence: GPLv3
 Network: true
 */

define('CPT4BP', '0.1');

/**
 * Loads CPT4BP files only if BuddyPress is present
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function cpt4bp_init() {
	global $wpdb;

	if (is_multisite() && BP_ROOT_BLOG != $wpdb->blogid)
		return;

	require (dirname(__FILE__) . '/CPT4BP.php');
	$cpt4bp = new CPT4BP();
}

add_action('bp_loaded', 'cpt4bp_init', 0);
