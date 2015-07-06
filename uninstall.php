<?php

// Make sure that we are uninstalling
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Removes all data from the database
delete_option( 'bf_license_manager' );
delete_option('buddyforms_product_id');
delete_option( 'buddyforms_deactivate_checkbox' );
delete_option( 'buddyforms_activated' );
delete_option( 'bf_version' );
