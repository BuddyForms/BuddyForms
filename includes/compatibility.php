<?php

/**
 * Add compatibility with Better Notifications for WordPress
 *
 * @url https://wordpress.org/plugins/bnfw
 * @url https://betternotificationsforwp.com/documentation/compatibility/support-plugins-front-end-forms/
 */

add_filter( 'bnfw_trigger_insert_post', '__return_true' );
