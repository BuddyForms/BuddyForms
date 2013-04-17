<?php
/*
Plugin Name: CPT4BP
Plugin URI: http://themekraft.com
Description: not now
Version: 0.1 alpha
Author: Sven Lehnert
Author URI: http://themekraft.com
Licence: GPLv3
Network: true
*/

define( 'BP_CUSTOM_GROUP_TYPES_VERSION', '0.1' );

/**
 * Loads Custom Group Types files only if BuddyPress is present
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function bp_cpt4bp_init() {
	global $wpdb;
	
	if( is_multisite() && BP_ROOT_BLOG != $wpdb->blogid )
		return;

	require( dirname( __FILE__ ) .'/CPT4BP.php' );
	$bp_cpt4bp = new CPT4BP();    
}
add_action( 'bp_loaded', 'bp_cpt4bp_init', 0 );