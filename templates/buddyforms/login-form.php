<?php

/**
 * Auth form login
 *
 * This template can be overridden by copying it to yourtheme/buddyforms/form-login.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_user_logged_in() ) {
	return;
}
?>

<script>
    jQuery(document).ready(function () {
        jQuery(document).on("click", '.bf-show-login', function (evt) {
            jQuery('.bf-show-login-form').toggle();
        });
    });
</script>
<div class="buddyforms-info"><?php _e( 'Returning user?', 'buddyforms' ) ?> <a href="#"
                                                                               class="bf-show-login"><?php _e( 'Click here to login', 'buddyforms' ) ?></a>
</div>
<div class="bf-show-login-form" style="display:none"><?php buddyforms_wp_login_form(); ?></div>

