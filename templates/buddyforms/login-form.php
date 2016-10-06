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

if(is_user_logged_in())
	return;

$redirect_url = esc_url( $_SERVER['REQUEST_URI'] )
?>

<?php do_action( 'buddyforms_login_start' );


buddyforms_wp_login_form();?>

<!--<form method="post" class="wc-auth-login">-->
<!--	<p class="form-row form-row-wide">-->
<!--		<label for="username">--><?php //_e( 'Username or email address', 'buddyforms' ); ?><!-- <span class="required">*</span></label>-->
<!--		<input type="text" class="input-text" name="username" id="username" value="--><?php //echo ( ! empty( $_POST['username'] ) ) ? esc_attr( $_POST['username'] ) : ''; ?><!--" />-->
<!--	</p>-->
<!--	<p class="form-row form-row-wide">-->
<!--		<label for="password">--><?php //_e( 'Password', 'buddyforms' ); ?><!-- <span class="required">*</span></label>-->
<!--		<input class="input-text" type="password" name="password" id="password" />-->
<!--	</p>-->
<!--	<p class="wc-auth-actions">-->
<!--		--><?php //wp_nonce_field( 'buddyforms-login' ); ?>
<!--		<input type="submit" class="button button-large button-primary wc-auth-login-button" name="login" value="--><?php //esc_attr_e( 'Login', 'buddyforms' ); ?><!--" />-->
<!--		<input type="hidden" name="redirect" value="--><?php //echo esc_url( $redirect_url ); ?><!--" />-->
<!--	</p>-->
<!--</form>-->

<?php do_action( 'buddyforms_auth_page_footer' ); ?>
